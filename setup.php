<?php
/**
 * Turgis Trading Platform Setup Script
 * Run this file once to initialize the database
 */

// Include configuration
require_once 'config/config.php';
require_once 'core/Database.php';

// Set content type
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turgis Trading Platform - Kurulum</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
            color: #155724;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
            color: #721c24;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fff3cd;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Turgis Trading Platform Kurulum</h1>
        
        <?php
        $setupComplete = false;
        $errors = [];
        $warnings = [];
        
        // Check if setup should run
        if (isset($_GET['run']) && $_GET['run'] === 'setup') {
            echo '<div class="step">📋 Kurulum başlatılıyor...</div>';
            
            try {
                // Test database connection
                echo '<div class="step">🔌 Veritabanı bağlantısı test ediliyor...</div>';
                $db = new Database();
                echo '<div class="step success">✅ Veritabanı bağlantısı başarılı!</div>';
                
                // Create tables
                echo '<div class="step">🏗️ Veritabanı tabloları oluşturuluyor...</div>';
                $db->createTables();
                echo '<div class="step success">✅ Veritabanı tabloları başarıyla oluşturuldu!</div>';
                
                // Check if admin user exists
                $adminExists = $db->selectOne("SELECT id FROM users WHERE email = 'admin@turgis.com'");
                if ($adminExists) {
                    echo '<div class="step success">✅ Admin kullanıcısı mevcut!</div>';
                } else {
                    echo '<div class="step warning">⚠️ Admin kullanıcısı bulunamadı, manuel olarak oluşturulması gerekebilir.</div>';
                }
                
                // Check sample data
                $symbolCount = $db->selectOne("SELECT COUNT(*) as count FROM symbols")['count'];
                if ($symbolCount > 0) {
                    echo '<div class="step success">✅ Örnek semboller yüklendi! (' . $symbolCount . ' sembol)</div>';
                } else {
                    echo '<div class="step warning">⚠️ Örnek semboller yüklenemedi.</div>';
                }
                
                $setupComplete = true;
                
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
                echo '<div class="step error">❌ Hata: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        
        if (!isset($_GET['run'])) {
            // Show setup information
            ?>
            <div class="info-box">
                <h3>📋 Kurulum Öncesi Kontrol Listesi</h3>
                <ul>
                    <li>✅ PHP 7.4 veya üzeri</li>
                    <li>✅ MySQL 5.7 veya üzeri</li>
                    <li>✅ PDO MySQL extension</li>
                    <li>✅ Web sunucusu (Apache/Nginx)</li>
                </ul>
            </div>
            
            <div class="step">
                <h3>🔧 Veritabanı Ayarları</h3>
                <p>Kurulum öncesi <code>config/config.php</code> dosyasındaki veritabanı ayarlarını kontrol edin:</p>
                <pre>
DB_HOST: <?= DB_HOST ?>

DB_NAME: <?= DB_NAME ?>

DB_USER: <?= DB_USER ?>

DB_PASS: <?= str_repeat('*', strlen(DB_PASS)) ?>
</pre>
            </div>
            
            <div class="step">
                <h3>📁 Dizin İzinleri</h3>
                <p>Aşağıdaki dizinlerin yazılabilir olduğundan emin olun:</p>
                <ul>
                    <li><code>logs/</code> (log dosyaları için)</li>
                    <li><code>uploads/</code> (dosya yüklemeleri için)</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="?run=setup" class="btn">🚀 Kurulumu Başlat</a>
            </div>
            
            <?php
        } elseif ($setupComplete) {
            ?>
            <div class="step success">
                <h3>🎉 Kurulum Tamamlandı!</h3>
                <p>Turgis Trading Platform başarıyla kuruldu. Artık platformu kullanmaya başlayabilirsiniz.</p>
            </div>
            
            <div class="info-box">
                <h3>📋 Varsayılan Giriş Bilgileri</h3>
                <p><strong>Admin Hesabı:</strong></p>
                <ul>
                    <li>E-posta: <code>admin@turgis.com</code></li>
                    <li>Şifre: <code>password</code></li>
                </ul>
                <p><strong>⚠️ Güvenlik Uyarısı:</strong> Üretim ortamında admin şifresini mutlaka değiştirin!</p>
            </div>
            
            <div class="step">
                <h3>🔧 Sonraki Adımlar</h3>
                <ol>
                    <li>Admin paneline giriş yapın ve ayarları kontrol edin</li>
                    <li>Güvenlik ayarlarını yapılandırın</li>
                    <li>API entegrasyonlarını kurun</li>
                    <li>Bu setup.php dosyasını silin veya erişimi engelleyin</li>
                </ol>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="/" class="btn btn-success">🏠 Ana Sayfaya Git</a>
                <a href="/admin" class="btn">⚙️ Admin Panel</a>
            </div>
            
            <?php
        }
        
        if (!empty($errors)) {
            echo '<div class="step error"><h3>❌ Hatalar:</h3><ul>';
            foreach ($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
        }
        
        if (!empty($warnings)) {
            echo '<div class="step warning"><h3>⚠️ Uyarılar:</h3><ul>';
            foreach ($warnings as $warning) {
                echo '<li>' . htmlspecialchars($warning) . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d;">
            <p>Turgis Trading Platform v1.0</p>
            <p>Modüler Forex & Kripto Trading Platformu</p>
        </div>
    </div>
</body>
</html>
