<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Get the currently logged-in user array with balance, or null if guest
 */
function getCurrentUser(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT u.id, u.email, u.full_name, u.avatar_url, u.is_admin, u.is_banned, u.ban_reason,
                   COALESCE(u.is_promoter, 0) as is_promoter, u.created_at,
                   COALESCE(b.balance, 0.00) as balance, COALESCE(b.topups, 0) as topups
            FROM users u
            LEFT JOIN users_balance b ON u.id = b.user_id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            // Check if user is banned
            if (!empty($user['is_banned'])) {
                // Destroy session if banned
                $_SESSION = [];
                return null;
            }

            // Check if email matches admin email
            if (strtolower($user['email']) === strtolower(ADMIN_EMAIL)) {
                $user['is_admin'] = 1;
            }
            $user['balance'] = (float)$user['balance'];
            $user['topups'] = (int)$user['topups'];
            $user['is_promoter'] = (int)($user['is_promoter'] ?? 0);
            return $user;
        }
    } catch (Exception $e) {
        error_log("Error in getCurrentUser: " . $e->getMessage());
    }

    return null;
}

/**
 * Check if the currently logged-in user is an administrator
 */
function isAdmin(): bool {
    $user = getCurrentUser();
    if (!$user) return false;
    return ($user['is_admin'] == 1 || strtolower($user['email']) === strtolower(ADMIN_EMAIL));
}

/**
 * Require user login for pages
 */
function requireAuth() {
    if (!getCurrentUser()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require admin role for pages
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Attempt user login
 */
function loginUser(string $email, string $password): array {
    $email = strtolower(trim($email));
    if (empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'Please enter both email and password'];
    }

    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE LOWER(email) = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        // Check if account is banned
        if (!empty($user['is_banned'])) {
            $reason = !empty($user['ban_reason']) ? $user['ban_reason'] : 'Account suspended by administrator.';
            return ['success' => false, 'error' => 'Your account is banned: ' . $reason];
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        return ['success' => true, 'user' => getCurrentUser()];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Attempt user registration with starting balance
 */
function registerUser(string $email, string $password, string $fullName = ''): array {
    $email = strtolower(trim($email));
    $fullName = trim($fullName);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Please enter a valid email address'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Password must be at least 6 characters long'];
    }

    if (empty($fullName)) {
        $parts = explode('@', $email);
        $fullName = ucfirst($parts[0]);
    }

    try {
        $db = getDB();

        // Check if email already registered
        $checkStmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = :email LIMIT 1");
        $checkStmt->execute([':email' => $email]);
        if ($checkStmt->fetch()) {
            return ['success' => false, 'error' => 'An account with this email already exists'];
        }

        $userId = generateUUID();
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $isAdmin = (strtolower($email) === strtolower(ADMIN_EMAIL)) ? 1 : 0;
        $startingBonus = (float)getSystemSetting('welcome_bonus', INITIAL_USER_BALANCE);

        // Transaction for user and balance creation
        $db->beginTransaction();

        $userStmt = $db->prepare("
            INSERT INTO users (id, email, password_hash, full_name, avatar_url, is_admin, is_banned, ban_reason, created_at)
            VALUES (:id, :email, :pass, :name, '', :admin, 0, '', datetime('now'))
        ");
        
        try {
            $userStmt->execute([
                ':id' => $userId,
                ':email' => $email,
                ':pass' => $passwordHash,
                ':name' => $fullName,
                ':admin' => $isAdmin
            ]);
        } catch (Exception $fallback) {
            $userStmt = $db->prepare("
                INSERT INTO users (id, email, password_hash, full_name, avatar_url, is_admin, is_banned, ban_reason, created_at)
                VALUES (:id, :email, :pass, :name, '', :admin, 0, '', NOW())
            ");
            $userStmt->execute([
                ':id' => $userId,
                ':email' => $email,
                ':pass' => $passwordHash,
                ':name' => $fullName,
                ':admin' => $isAdmin
            ]);
        }

        // Initialize balance with dynamic welcome bonus
        $balanceStmt = $db->prepare("
            INSERT INTO users_balance (user_id, balance, topups)
            VALUES (:user_id, :balance, 0)
        ");
        $balanceStmt->execute([
            ':user_id' => $userId,
            ':balance' => $startingBonus
        ]);

        $db->commit();

        // Set session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;

        return ['success' => true, 'user' => getCurrentUser()];
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return ['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()];
    }
}

/**
 * Logout user
 */
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Update user's name and/or avatar
 */
function updateUserProfile(string $userId, ?string $fullName, ?string $avatarUrl = null): array {
    try {
        $db = getDB();
        $fields = [];
        $params = [':user_id' => $userId];

        if ($fullName !== null) {
            $fields[] = "full_name = :full_name";
            $params[':full_name'] = trim($fullName);
        }
        if ($avatarUrl !== null) {
            $fields[] = "avatar_url = :avatar_url";
            $params[':avatar_url'] = trim($avatarUrl);
        }

        if (empty($fields)) {
            return ['success' => true];
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return ['success' => true, 'user' => getCurrentUser()];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
