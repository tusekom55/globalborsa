<?php
/**
 * Security and Authentication Handler
 */

class Security
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function generateCSRFToken()
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public function validateCSRFToken($token)
    {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    public function getCSRFField()
    {
        $token = $this->generateCSRFToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePassword($password)
    {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password);
    }

    public function checkLoginAttempts($email)
    {
        $sql = "SELECT COUNT(*) as attempts, MAX(attempted_at) as last_attempt 
                FROM login_attempts 
                WHERE email = :email AND attempted_at > DATE_SUB(NOW(), INTERVAL :lockout_time SECOND)";
        
        $result = $this->db->selectOne($sql, [
            'email' => $email,
            'lockout_time' => LOGIN_LOCKOUT_TIME
        ]);

        if ($result && $result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $timeLeft = LOGIN_LOCKOUT_TIME - (time() - strtotime($result['last_attempt']));
            if ($timeLeft > 0) {
                return [
                    'locked' => true,
                    'time_left' => $timeLeft
                ];
            }
        }

        return ['locked' => false];
    }

    public function recordLoginAttempt($email, $success = false)
    {
        $this->db->insert('login_attempts', [
            'email' => $email,
            'success' => $success ? 1 : 0,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'attempted_at' => date('Y-m-d H:i:s')
        ]);

        // Clean old attempts
        $this->db->delete('login_attempts', 
            'attempted_at < DATE_SUB(NOW(), INTERVAL :cleanup_time SECOND)',
            ['cleanup_time' => LOGIN_LOCKOUT_TIME * 2]
        );
    }

    public function login($userId)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['login_time'] = time();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Update last login
        $this->db->update('users', 
            ['last_login' => date('Y-m-d H:i:s')],
            'id = :id',
            ['id' => $userId]
        );
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        session_start();
    }

    public function isLoggedIn()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }

        // Check session timeout
        if (time() - $_SESSION['login_time'] > SESSION_LIFETIME) {
            $this->logout();
            return false;
        }

        return true;
    }

    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $user = $this->db->selectOne(
            'SELECT * FROM users WHERE id = :id',
            ['id' => $_SESSION['user_id']]
        );

        return $user;
    }

    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireAdmin()
    {
        $user = $this->getCurrentUser();
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Access denied');
        }
    }

    public function rateLimitCheck($key, $limit = null, $window = 60)
    {
        if ($limit === null) {
            $limit = API_RATE_LIMIT;
        }

        $cacheKey = 'rate_limit_' . $key;
        $current = $_SESSION[$cacheKey] ?? ['count' => 0, 'reset' => time() + $window];

        if (time() > $current['reset']) {
            $current = ['count' => 0, 'reset' => time() + $window];
        }

        $current['count']++;
        $_SESSION[$cacheKey] = $current;

        return [
            'allowed' => $current['count'] <= $limit,
            'remaining' => max(0, $limit - $current['count']),
            'reset' => $current['reset']
        ];
    }

    public function generateApiKey()
    {
        return bin2hex(random_bytes(32));
    }

    public function validateApiKey($apiKey)
    {
        $user = $this->db->selectOne(
            'SELECT * FROM users WHERE api_key = :api_key AND api_key IS NOT NULL',
            ['api_key' => $apiKey]
        );

        return $user;
    }
}
