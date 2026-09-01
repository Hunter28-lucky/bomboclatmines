<?php
error_reporting(0);
@ini_set('display_errors', '0');

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Please log in to access live support'], 401);
}

$action = cleanInput($_GET['action'] ?? $_POST['action'] ?? 'send');
$json = getJsonInput();
$db = getDB();

switch ($action) {
    case 'history':
        try {
            $stmt = $db->prepare("
                SELECT id, sender, message, is_escalated, created_at 
                FROM support_messages 
                WHERE user_id = :uid 
                ORDER BY created_at ASC 
                LIMIT 50
            ");
            $stmt->execute([':uid' => $user['id']]);
            $messages = $stmt->fetchAll();

            jsonResponse([
                'success' => true,
                'messages' => $messages,
                'user' => [
                    'name' => $user['full_name'] ?: 'Player',
                    'email' => $user['email'],
                    'balance' => (float)$user['balance']
                ]
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'escalate':
        try {
            $msg = cleanInput($json['message'] ?? $_POST['message'] ?? 'User requested human executive assistance.');
            $stmt = $db->prepare("
                INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                VALUES (:uid, 'user', :msg, 1, datetime('now'))
            ");
            try {
                $stmt->execute([':uid' => $user['id'], ':msg' => $msg]);
            } catch (Exception $e) {
                $stmt = $db->prepare("
                    INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                    VALUES (:uid, 'user', :msg, 1, NOW())
                ");
                $stmt->execute([':uid' => $user['id'], ':msg' => $msg]);
            }

            jsonResponse([
                'success' => true,
                'message' => 'Your request has been escalated to a human support executive.'
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'send':
    default:
        $userMessage = trim($json['message'] ?? $_POST['message'] ?? '');
        if (empty($userMessage)) {
            jsonResponse(['success' => false, 'error' => 'Message cannot be empty'], 400);
        }

        try {
            // 1. Fetch user's financial & gameplay context
            $balStmt = $db->prepare("SELECT balance FROM users_balance WHERE user_id = :uid");
            $balStmt->execute([':uid' => $user['id']]);
            $currentBalance = (float)($balStmt->fetchColumn() ?: 0.0);

            // Recent deposits
            $depStmt = $db->prepare("SELECT amount, utr_number, status, created_at FROM payments WHERE user_id = :uid ORDER BY created_at DESC LIMIT 3");
            $depStmt->execute([':uid' => $user['id']]);
            $recentDeposits = $depStmt->fetchAll();

            // Recent withdrawals
            $withStmt = $db->prepare("SELECT amount, upi_id, status, created_at FROM withdrawals WHERE user_id = :uid ORDER BY created_at DESC LIMIT 3");
            $withStmt->execute([':uid' => $user['id']]);
            $recentWithdrawals = $withStmt->fetchAll();

            // Fetch last 6 chat messages for memory context
            $histStmt = $db->prepare("SELECT sender, message FROM support_messages WHERE user_id = :uid ORDER BY created_at DESC LIMIT 6");
            $histStmt->execute([':uid' => $user['id']]);
            $pastRows = array_reverse($histStmt->fetchAll());

            // 2. Check for escalation intent
            $lowerMsg = strtolower($userMessage);
            $isEscalatedIntent = (
                strpos($lowerMsg, 'human') !== false ||
                strpos($lowerMsg, 'agent') !== false ||
                strpos($lowerMsg, 'executive') !== false ||
                strpos($lowerMsg, 'call me') !== false ||
                strpos($lowerMsg, 'manager') !== false ||
                strpos($lowerMsg, 'cheat') !== false ||
                strpos($lowerMsg, 'scam') !== false ||
                strpos($lowerMsg, 'fraud') !== false ||
                strpos($lowerMsg, 'police') !== false ||
                strpos($lowerMsg, 'complaint') !== false
            );

            // 3. Save User Message into DB
            $insUserStmt = $db->prepare("
                INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                VALUES (:uid, 'user', :msg, :esc, datetime('now'))
            ");
            try {
                $insUserStmt->execute([':uid' => $user['id'], ':msg' => $userMessage, ':esc' => $isEscalatedIntent ? 1 : 0]);
            } catch (Exception $fallback) {
                $insUserStmt = $db->prepare("
                    INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                    VALUES (:uid, 'user', :msg, :esc, NOW())
                ");
                $insUserStmt->execute([':uid' => $user['id'], ':msg' => $userMessage, ':esc' => $isEscalatedIntent ? 1 : 0]);
            }

            // 4. Build Context-Rich Prompt for OpenRouter AI
            $userName = $user['full_name'] ?: 'Valued Player';
            $userEmail = $user['email'];

            $depositSummary = 'None';
            if (!empty($recentDeposits)) {
                $depositSummary = implode(', ', array_map(function($d) {
                    return "₹{$d['amount']} (UTR: {$d['utr_number']}, Status: {$d['status']})";
                }, $recentDeposits));
            }

            $withdrawSummary = 'None';
            if (!empty($recentWithdrawals)) {
                $withdrawSummary = implode(', ', array_map(function($w) {
                    return "₹{$w['amount']} (UPI: {$w['upi_id']}, Status: {$w['status']})";
                }, $recentWithdrawals));
            }

            $systemPrompt = "You are the official 24/7 VIP Live Support Executive for 'Bombaclat Mines'.
PLAYER DETAILS:
- Name: {$userName}
- Email: {$userEmail}
- Current Wallet Balance: ₹" . number_format($currentBalance, 2) . "
- Recent Deposits: {$depositSummary}
- Recent Withdrawals: {$withdrawSummary}

RULES & INSTRUCTIONS:
1. LANGUAGE MATCHING:
   - If the player talks in English, reply in natural, polite English.
   - If the player talks in Hinglish (Hindi in Roman alphabets like 'bhai deposit kab aayega', 'withdrawal milega kya', 'kya ye real hai', 'mera paisa atka hai'), reply in warm, friendly, natural Hinglish.
2. CONCISENESS & TONE:
   - Keep replies short, direct, polite, and reassuring (maximum 2 to 4 sentences).
   - Understand user psychology: NEVER argue or get defensive. Always give comforting, clear facts.
3. CORE KNOWLEDGE BASE:
   - Deposits: Verified and credited to wallet within 5-15 minutes after submitting the correct UTR number and payment receipt.
   - Withdrawals: 24/7 instant UPI payouts processed within 5-30 minutes. Minimum withdrawal is ₹500.
   - Authenticity & Fairness: 100% genuine and provably fair platform with verified random number generation and 97%+ RTP payout.
   - Human Agent Escalation: If the player asks for a human agent, executive, or has an unresolved dispute, say: 'Aapki request humne senior executive ko assign kar di hai. Wo jald hi aapse connect karenge.' / 'Your request has been escalated to a senior human executive who will assist you shortly.'
4. STRICT LIMITS:
   - Do not output markdown asterisks or bullet points excessively. Keep it like a real human chat agent.";

            // Format OpenRouter Messages
            $apiMessages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            foreach ($pastRows as $pr) {
                $apiMessages[] = [
                    'role' => ($pr['sender'] === 'user') ? 'user' : 'assistant',
                    'content' => $pr['message']
                ];
            }
            $apiMessages[] = ['role' => 'user', 'content' => $userMessage];

            // 5. Call OpenRouter with fallback models
            $aiReply = callOpenRouterAI($apiMessages, $userMessage, $userName, $currentBalance, $recentDeposits, $recentWithdrawals);

            // 6. Save AI Response to DB
            $insBotStmt = $db->prepare("
                INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                VALUES (:uid, 'bot', :msg, :esc, datetime('now'))
            ");
            try {
                $insBotStmt->execute([':uid' => $user['id'], ':msg' => $aiReply, ':esc' => $isEscalatedIntent ? 1 : 0]);
            } catch (Exception $fallback) {
                $insBotStmt = $db->prepare("
                    INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                    VALUES (:uid, 'bot', :msg, :esc, NOW())
                ");
                $insBotStmt->execute([':uid' => $user['id'], ':msg' => $aiReply, ':esc' => $isEscalatedIntent ? 1 : 0]);
            }

            jsonResponse([
                'success' => true,
                'reply' => $aiReply,
                'is_escalated' => $isEscalatedIntent,
                'timestamp' => date('H:i')
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => 'Support error: ' . $e->getMessage()], 500);
        }
        break;
}

function cleanAiOutput(string $raw): string {
    // Strip <think>...</think>
    $text = preg_replace('/<think>.*?<\/think>/is', '', $raw);
    // Strip "Here's a thinking process:... \n\n"
    if (preg_match('/(?:Here\'s a thinking process|Thinking Process|Draft Response).*?\n\n(.*)$/is', $text, $m)) {
        $text = $m[1];
    }
    // Strip any leading quotes or raw markdown bullets if it was meant to be plain chat
    $text = trim($text);
    return $text;
}

/**
 * Call OpenRouter API with multi-model fallback and local smart fallback
 */
function callOpenRouterAI(array $messages, string $userMessage, string $userName, float $balance, array $recentDeposits, array $recentWithdrawals): string {
    $apiKey = OPENROUTER_API_KEY;
    if (empty($apiKey)) {
        return getSmartFallbackReply($userMessage, $userName, $balance, $recentDeposits, $recentWithdrawals);
    }

    $models = [
        'nvidia/nemotron-3.5-lightning:free',
        'google/gemma-4-26b-a4b-it:free',
        'liquid/lfm-2.5-2.6b:free',
        'meta-llama/llama-3.3-70b-instruct:free',
        'openai/gpt-4o-mini',
        'google/gemini-2.5-flash'
    ];

    foreach ($models as $model) {
        try {
            $payload = [
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => 200,
                'temperature' => 0.6
            ];

            $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
                'HTTP-Referer: http://localhost:8000',
                'X-Title: Bombaclat Mines Live Support'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode === 200 && $response) {
                $resData = json_decode($response, true);
                if (!empty($resData['choices'][0]['message']['content'])) {
                    $clean = cleanAiOutput($resData['choices'][0]['message']['content']);
                    if (strlen($clean) > 2) {
                        return $clean;
                    }
                }
            }
        } catch (Exception $e) {
            continue;
        }
    }

    // If API failed or rate-limited, fallback to instant high-quality conversational response
    return getSmartFallbackReply($userMessage, $userName, $balance, $recentDeposits, $recentWithdrawals);
}

/**
 * Intelligent localized fallback if external API is unreachable
 */
function getSmartFallbackReply(string $msg, string $name, float $balance, array $deposits, array $withdrawals): string {
    $lower = strtolower($msg);
    $isHinglish = preg_match('/\b(bhai|kya|hai|kab|aayega|milega|kitna|kaise|paise|rupaye|mera|meri|karo|nahi|hu|ho|ko)\b/i', $msg);

    // Deposit query
    if (strpos($lower, 'deposit') !== false || strpos($lower, 'add money') !== false || strpos($lower, 'utr') !== false) {
        $lastDep = $deposits[0] ?? null;
        if ($lastDep && $lastDep['status'] === 'pending') {
            if ($isHinglish) {
                return "Bhai {$name}, aapka ₹{$lastDep['amount']} ka deposit (UTR: {$lastDep['utr_number']}) abhi verification mein hai. Ye 5-10 minute ke andar aapke wallet mein add ho jayega!";
            }
            return "Hello {$name}, your deposit of ₹{$lastDep['amount']} (UTR: {$lastDep['utr_number']}) is currently under verification and will be credited within 5-10 minutes.";
        }
        if ($isHinglish) {
            return "Bhai, QR code par payment karke UTR number aur screenshot submit kar dijiye. Deposit 5-15 minute mein approve ho jata hai.";
        }
        return "Deposits are processed within 5-15 minutes after you submit the correct UTR transaction number and receipt.";
    }

    // Withdrawal query
    if (strpos($lower, 'withdraw') !== false || strpos($lower, 'nikal') !== false || strpos($lower, 'payout') !== false) {
        $lastWith = $withdrawals[0] ?? null;
        if ($lastWith && $lastWith['status'] === 'pending') {
            if ($isHinglish) {
                return "Bhai, aapka ₹{$lastWith['amount']} ka withdrawal request process ho raha hai. UPI se instant transfer 5-30 minute mein aapke bank mein aa jayega.";
            }
            return "Hello {$name}, your withdrawal of ₹{$lastWith['amount']} is being processed and will be credited to your UPI within 5-30 minutes.";
        }
        if ($isHinglish) {
            return "Withdrawal 24/7 instant UPI se milta hai (Minimum ₹500). Request submit karne ke 5-30 minute ke andar paise aapke account mein transfer ho jaate hain.";
        }
        return "Withdrawals are processed 24/7 via instant UPI within 5-30 minutes. Minimum withdrawal is ₹500.";
    }

    // Legitimacy / Scam check
    if (strpos($lower, 'real') !== false || strpos($lower, 'fake') !== false || strpos($lower, 'safe') !== false || strpos($lower, 'trust') !== false || strpos($lower, 'genuine') !== false) {
        if ($isHinglish) {
            return "Bombaclat Mines 100% genuine aur provably fair game hai bhai. Har win instant withdraw hoti hai aur sabhi transactions verified hote hain!";
        }
        return "Bombaclat Mines is 100% genuine, secure, and provably fair with a 97%+ RTP payout system and instant 24/7 UPI withdrawals.";
    }

    // Balance query
    if (strpos($lower, 'balance') !== false || strpos($lower, 'paisa') !== false) {
        $formatted = number_format($balance, 2);
        if ($isHinglish) {
            return "Aapka current wallet balance ₹{$formatted} hai. Aap jab chahein mining shuru kar sakte hain ya ₹500 hone par withdraw kar sakte hain.";
        }
        return "Your current wallet balance is ₹{$formatted}. You can start mining anytime or withdraw once it reaches ₹500.";
    }

    // Human escalation
    if (strpos($lower, 'human') !== false || strpos($lower, 'agent') !== false || strpos($lower, 'talk') !== false || strpos($lower, 'call') !== false) {
        if ($isHinglish) {
            return "Ji bhai, maine aapki query senior executive ko forward kar di hai. Wo kuch hi samay mein aapse contact karenge.";
        }
        return "I have forwarded your inquiry to our senior human executive team. They will assist you within a few minutes.";
    }

    // Default friendly response
    if ($isHinglish) {
        return "Namaste {$name} ji! Main Bombaclat Live Support hoon. Aapko deposit, withdrawal ya game ke baare mein koi bhi jaankari chahiye toh batayein, main madad karunga.";
    }
    return "Hello {$name}! I am your 24/7 Bombaclat Live Support assistant. How can I help you today with deposits, withdrawals, or game rules?";
}
