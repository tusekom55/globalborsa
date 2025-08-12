<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-globe"></i>
                <span>Global Borsa</span>
            </div>
            <h1>Giriş Yap</h1>
            <p>Hesabınıza giriş yapın ve trading'e başlayın</p>
        </div>

        <form method="POST" action="/login" class="login-form">
            <?= $this->csrf() ?>
            
            <div class="form-group">
                <label for="email">E-posta Adresi</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="ornek@email.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Şifre</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Şifrenizi girin"
                    required
                >
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1">
                    <span>Beni hatırla</span>
                </label>
                <a href="#" class="forgot-link">Şifremi unuttum</a>
            </div>

            <button type="submit" class="btn-login">
                Giriş Yap
            </button>
        </form>

        <div class="demo-section">
            <p>Demo hesap bilgileri:</p>
            <div class="demo-info">
                <span><strong>E-posta:</strong> admin@globalborsa.com</span>
                <span><strong>Şifre:</strong> password</span>
            </div>
            <button type="button" class="btn-demo" onclick="fillDemo()">
                Demo Bilgilerini Doldur
            </button>
        </div>

        <div class="login-footer">
            <p>Hesabınız yok mu? <a href="/register">Kayıt Ol</a></p>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.login-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 24px;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 20px;
}

.login-header h1 {
    font-size: 28px;
    color: #1e293b;
    margin-bottom: 10px;
}

.login-header p {
    color: #64748b;
    font-size: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #64748b;
}

.forgot-link {
    color: #2563eb;
    text-decoration: none;
    font-size: 14px;
}

.forgot-link:hover {
    text-decoration: underline;
}

.btn-login {
    width: 100%;
    background: #2563eb;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-login:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
}

.demo-section {
    margin: 25px 0;
    padding: 20px;
    background: #f8fafc;
    border-radius: 10px;
    text-align: center;
}

.demo-section p {
    margin-bottom: 10px;
    color: #64748b;
    font-size: 14px;
}

.demo-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 15px;
    font-size: 13px;
}

.btn-demo {
    background: #10b981;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-demo:hover {
    background: #059669;
}

.login-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #e5e7eb;
}

.login-footer a {
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .login-card {
        padding: 30px 20px;
    }
    
    .login-header h1 {
        font-size: 24px;
    }
}
</style>

<script>
function fillDemo() {
    document.getElementById('email').value = 'admin@globalborsa.com';
    document.getElementById('password').value = 'password';
}

// Prevent page refresh on form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Let the form submit normally, don't prevent default
            console.log('Form submitted');
        });
    }
});
</script>
