<?php
/**
 * Accounts Controller - User Authentication and Management
 */

require_once CORE_PATH . '/Controller.php';

class AccountsController extends Controller
{
    public function loginForm()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            header('Location: /trading');
            exit;
        }

        $this->view('login', [
            'title' => 'Giriş Yap',
            'custom_css' => ['/modules/accounts/assets/accounts.css']
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlashMessage('E-posta ve şifre gereklidir.', 'error');
            header('Location: /login');
            exit;
        }

        // Check user credentials
        $user = $this->db->selectOne("
            SELECT * FROM users WHERE email = ? AND status = 'active'
        ", [$email]);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            $this->setFlashMessage('Başarıyla giriş yaptınız!', 'success');
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: /trading');
            } else {
                header('Location: /trading');
            }
            exit;
        } else {
            $this->setFlashMessage('Geçersiz e-posta veya şifre.', 'error');
            header('Location: /login');
            exit;
        }
    }

    public function registerForm()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            header('Location: /trading');
            exit;
        }

        $this->view('register', [
            'title' => 'Kayıt Ol',
            'custom_css' => ['/modules/accounts/assets/accounts.css']
        ]);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $this->setFlashMessage('Tüm alanlar gereklidir.', 'error');
            header('Location: /register');
            exit;
        }

        if ($password !== $confirmPassword) {
            $this->setFlashMessage('Şifreler eşleşmiyor.', 'error');
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            $this->setFlashMessage('Şifre en az 6 karakter olmalıdır.', 'error');
            header('Location: /register');
            exit;
        }

        // Check if user already exists
        $existingUser = $this->db->selectOne("
            SELECT id FROM users WHERE email = ? OR username = ?
        ", [$email, $username]);

        if ($existingUser) {
            $this->setFlashMessage('Bu e-posta veya kullanıcı adı zaten kullanılıyor.', 'error');
            header('Location: /register');
            exit;
        }

        // Create new user
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $this->db->insert("
                INSERT INTO users (username, email, password, role, status, created_at)
                VALUES (?, ?, ?, 'user', 'active', NOW())
            ", [$username, $email, $hashedPassword]);

            $this->setFlashMessage('Hesabınız başarıyla oluşturuldu! Giriş yapabilirsiniz.', 'success');
            header('Location: /login');
            exit;
        } catch (Exception $e) {
            $this->setFlashMessage('Kayıt sırasında bir hata oluştu.', 'error');
            header('Location: /register');
            exit;
        }
    }

    public function logout()
    {
        session_destroy();
        $this->setFlashMessage('Başarıyla çıkış yaptınız.', 'success');
        header('Location: /');
        exit;
    }

    public function profile()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $user = $this->getCurrentUser();
        
        $this->view('profile', [
            'title' => 'Profil',
            'user' => $user,
            'custom_css' => ['/modules/accounts/assets/accounts.css']
        ]);
    }

    public function updateProfile()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($username) || empty($email)) {
            $this->setFlashMessage('Kullanıcı adı ve e-posta gereklidir.', 'error');
            header('Location: /profile');
            exit;
        }

        try {
            $this->db->update("
                UPDATE users SET username = ?, email = ? WHERE id = ?
            ", [$username, $email, $userId]);

            $_SESSION['user_email'] = $email;
            $this->setFlashMessage('Profil başarıyla güncellendi.', 'success');
        } catch (Exception $e) {
            $this->setFlashMessage('Profil güncellenirken bir hata oluştu.', 'error');
        }

        header('Location: /profile');
        exit;
    }

    public function balance()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        // For demo purposes, return mock balance data
        header('Content-Type: application/json');
        echo json_encode([
            'demo_balance' => 10000.00,
            'live_balance' => 0.00,
            'currency' => 'USD'
        ]);
        exit;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    private function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->db->selectOne("
            SELECT * FROM users WHERE id = ?
        ", [$_SESSION['user_id']]);
    }
}
