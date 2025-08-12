<!DOCTYPE html>
<html lang="<?= $current_language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <meta name="description" content="<?= $description ?? 'Professional Forex and Crypto Trading Platform' ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/shared/assets/css/main.css">
    <link rel="stylesheet" href="/shared/assets/css/themes/<?= $current_theme ?>.css">
    
    <!-- Chart.js for trading charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS for specific pages -->
    <?php if (isset($custom_css)): ?>
        <?php foreach ($custom_css as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="theme-<?= $current_theme ?>" data-theme="<?= $current_theme ?>">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="/" class="brand-link">
                    <i class="fas fa-globe"></i>
                    Global Borsa
                </a>
            </div>
            
            <div class="navbar-menu">
                <div class="navbar-nav">
                    <a href="/" class="nav-link">Ana Sayfa</a>
                    <a href="/markets" class="nav-link">Piyasalar</a>
                    <a href="/trading" class="nav-link">İşlem</a>
                    <a href="/blog" class="nav-link">Blog</a>
                </div>
                
                <div class="navbar-actions">
                    <!-- Theme Toggle -->
                    <button class="btn btn-icon theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon" id="theme-icon"></i>
                    </button>
                    
                    <!-- Language Toggle -->
                    <div class="dropdown">
                        <button class="btn btn-icon dropdown-toggle">
                            <i class="fas fa-globe"></i>
                            <span><?= strtoupper($current_language) ?></span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" onclick="setLanguage('tr')" class="dropdown-item">
                                <i class="flag-icon flag-icon-tr"></i> Türkçe
                            </a>
                            <a href="#" onclick="setLanguage('en')" class="dropdown-item">
                                <i class="flag-icon flag-icon-us"></i> English
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($user): ?>
                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle">
                                <i class="fas fa-user"></i>
                                <span><?= htmlspecialchars($user['username']) ?></span>
                            </button>
                            <div class="dropdown-menu">
                                <a href="/profile" class="dropdown-item">
                                    <i class="fas fa-user-cog"></i> Profil
                                </a>
                                <a href="/balance" class="dropdown-item">
                                    <i class="fas fa-wallet"></i> Bakiye
                                </a>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="/admin" class="dropdown-item">
                                        <i class="fas fa-cog"></i> Admin Panel
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="/logout" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Çıkış
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <a href="/login" class="btn btn-outline">Giriş</a>
                        <a href="/register" class="btn btn-primary">Kayıt Ol</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php 
    $flash = $_SESSION['flash'] ?? [];
    if (!empty($flash)):
    ?>
        <div class="flash-messages">
            <?php foreach ($flash as $type => $message): ?>
                <div class="alert alert-<?= $type ?> alert-dismissible">
                    <span><?= htmlspecialchars($message) ?></span>
                    <button class="alert-close" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4><?= SITE_NAME ?></h4>
                    <p>Profesyonel forex ve kripto para trading platformu</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-telegram"></i></a>
                        <a href="#"><i class="fab fa-discord"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h5>Platform</h5>
                    <ul>
                        <li><a href="/markets">Piyasalar</a></li>
                        <li><a href="/trading">İşlem</a></li>
                        <li><a href="/blog">Blog</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h5>Destek</h5>
                    <ul>
                        <li><a href="/help">Yardım</a></li>
                        <li><a href="/contact">İletişim</a></li>
                        <li><a href="/api-docs">API Dokümantasyonu</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h5>Yasal</h5>
                    <ul>
                        <li><a href="/terms">Kullanım Şartları</a></li>
                        <li><a href="/privacy">Gizlilik Politikası</a></li>
                        <li><a href="/risk-disclosure">Risk Bildirimi</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tüm hakları saklıdır.</p>
                <p class="disclaimer">
                    <strong>Risk Uyarısı:</strong> Forex ve kripto para ticareti yüksek risk içerir.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/shared/assets/js/main.js"></script>
    
    <!-- Custom JavaScript for specific pages -->
    <?php if (isset($custom_js)): ?>
        <?php foreach ($custom_js as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- CSRF Token for AJAX requests -->
    <script>
        window.csrfToken = '<?= $csrf_token ?>';
        window.currentLanguage = '<?= $current_language ?>';
        window.currentTheme = '<?= $current_theme ?>';
    </script>
</body>
</html>
