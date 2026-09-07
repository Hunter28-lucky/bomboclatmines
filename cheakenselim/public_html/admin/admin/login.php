<?php
require_once 'config.php';

// If already logged in as admin, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role, status FROM admin_users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if ($password === $user['password']) {
                    session_regenerate_id(true); // Prevent session fixation
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'];
                    
                    // Update last login
                    $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Log activity
                    logAdminActivity('login', 'Admin logged in successfully');
                    
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (Exception $e) {
            $error = 'Login failed. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Chicken Road Game</title>
    <style>
        :root {
            --main-color: #00ECBE;
            --main-gradient-start: #7AFEC3;
            --main-gradient-end: #02AFB6;
            --main_gradient-color: linear-gradient(180deg, var(--main-gradient-start), var(--main-gradient-end));
            --main_gradient-color2: linear-gradient(90deg, var(--main-gradient-start), var(--main-gradient-end));
            --bg_HomeModule_Stroke: #224BA2;
            --bg_HomeModule_Padding: linear-gradient(180deg, #001C54 0%, #000C33 100%);
            --norm_red-color: #D23838;
            --norm_green-color: #17B15E;
            --norm_secondary-color: #DD9138;
            --norm_Purple-color: #9B48DB;
            --norm_bule-color: #5088D3;
            --button_dis_color: #3D4863;
            --Secondary_red_color: #FD565C;
            --Secondary_green_color: #40AD72;
            --Secondary_Color1: #FFEBEC;
            --Secondary_Color2: #DAFFEB;
            --Secondary_moto_Color9: #239DA1;
            --Secondary_moto_Color8: #163B86;
            --text_color_L1: #E3EFFF;
            --text_color_L2: #92A8E3;
            --text_color_L3: #6F80A4;
            --text_color_L4: #05012B;
            --bg_color_L1: #05012B;
            --bg_color_L2: #011341;
            --bg_color_L3: #001C54;
            --Dividing-line_color: #022C68;
            --sheet_nva_color: #2C5ECA;
            --sheet_detail_bg_color: #000C33;
            --icon1: rgba(12, 195, 159, .6);
            --icon2: rgba(12, 195, 159, .25);
            --tab1: #21D9CC;
            --tab2: #BED921;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        body {
            background: var(--bg_HomeModule_Padding);
            min-height: 100vh;
            color: var(--text_color_L1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: var(--bg_color_L2);
            border-radius: 20px;
            padding: 40px;
            border: 2px solid var(--Dividing-line_color);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            max-width: 400px;
            width: 100%;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .chicken-icon {
            width: 60px;
            height: 60px;
            background: var(--main_gradient-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 15px;
            box-shadow: 0 8px 25px rgba(0, 236, 190, 0.4);
        }
        
        .app-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--text_color_L1);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 5px;
        }
        
        .app-subtitle {
            font-size: 0.9rem;
            color: var(--text_color_L2);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text_color_L1);
            margin-bottom: 8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .input-field {
            width: 100%;
            padding: 15px 18px;
            border-radius: 12px;
            background: var(--bg_color_L3);
            border: 2px solid var(--Dividing-line_color);
            color: var(--text_color_L1);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .input-field::placeholder {
            color: var(--text_color_L3);
        }
        
        .input-field:focus {
            outline: none;
            border-color: var(--main-color);
            box-shadow: 0 0 0 4px rgba(0, 236, 190, 0.3);
            background: var(--bg_color_L2);
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        
        .btn-primary {
            background: var(--main_gradient-color);
            color: var(--text_color_L4);
            box-shadow: 0 8px 25px rgba(0, 236, 190, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 236, 190, 0.6);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .message.error {
            background: rgba(210, 56, 56, 0.2);
            border: 2px solid rgba(210, 56, 56, 0.3);
            color: var(--Secondary_red_color);
        }
        
        .admin-info {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: var(--bg_color_L3);
            border-radius: 10px;
            border: 1px solid var(--Dividing-line_color);
        }
        
        .admin-info h4 {
            color: var(--main-color);
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .admin-info p {
            color: var(--text_color_L2);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }
            
            .app-title {
                font-size: 1.5rem;
            }
            
            .input-field {
                padding: 12px 15px;
                font-size: 0.95rem;
            }
            
            .btn {
                padding: 14px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="chicken-icon">🐔</div>
            <div class="app-title">CHICKEN ROAD</div>
            <div class="app-subtitle">Admin Panel</div>
        </div>
        
        <form method="POST">
            <?php if ($error): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" type="text" required 
                       class="input-field" 
                       placeholder="Enter your username"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" required 
                       class="input-field" 
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn btn-primary">
                Sign In to Admin Panel
            </button>
        </form>
        
        <div class="admin-info">
            <h4>🔐 Admin Access</h4>
            <p><strong>Super Admin:</strong> superadmin / admin123</p>
            <p><strong>Admin:</strong> admin / admin123</p>
            <p style="font-size: 0.75rem; color: var(--text_color_L3); margin-top: 10px;">
                ⚠️ Keep these credentials secure
            </p>
        </div>
    </div>
</body>
</html> 