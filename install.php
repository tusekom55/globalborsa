<?php
/**
 * Simple Installation Script for Turgis Trading Platform
 */

// Basic error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Turgis Trading - Installation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .success { border-left-color: #28a745; background: #d4edda; color: #155724; }
        .error { border-left-color: #dc3545; background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🚀 Turgis Trading Platform - Installation</h1>
    
    <?php
    if (isset($_GET['run'])) {
        echo '<div class="step">Starting installation...</div>';
        
        try {
            // Database connection test
            echo '<div class="step">Testing database connection...</div>';
            
            $host = 'localhost';
            $dbname = 'u225998063_newe';
            $username = 'u225998063_newe';
            $password = '123456Tubb';
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            echo '<div class="step success">✅ Database connection successful!</div>';
            
            // Create tables
            echo '<div class="step">Creating database tables...</div>';
            
            // Users table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('user', 'admin') DEFAULT 'user',
                    status ENUM('active', 'inactive') DEFAULT 'active',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Symbols table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS symbols (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    symbol VARCHAR(20) UNIQUE NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    type ENUM('forex', 'crypto', 'stock') NOT NULL,
                    status ENUM('active', 'inactive') DEFAULT 'active',
                    is_featured BOOLEAN DEFAULT FALSE,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Prices table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS prices (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    symbol_id INT NOT NULL,
                    price DECIMAL(20, 8) NOT NULL,
                    change_percent_24h DECIMAL(10, 4) DEFAULT 0,
                    volume_24h DECIMAL(20, 8) DEFAULT 0,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (symbol_id) REFERENCES symbols(id)
                )
            ");
            
            echo '<div class="step success">✅ Tables created successfully!</div>';
            
            // Insert admin user
            $adminExists = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@turgis.com'")->fetchColumn();
            
            if (!$adminExists) {
                $pdo->exec("
                    INSERT INTO users (username, email, password, role) 
                    VALUES ('admin', 'admin@turgis.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin')
                ");
                echo '<div class="step success">✅ Admin user created!</div>';
            } else {
                echo '<div class="step success">✅ Admin user already exists!</div>';
            }
            
            // Insert sample symbols
            $symbolExists = $pdo->query("SELECT COUNT(*) FROM symbols")->fetchColumn();
            
            if ($symbolExists == 0) {
                $symbols = [
                    ['BTCUSDT', 'Bitcoin / Tether', 'crypto', 1],
                    ['ETHUSDT', 'Ethereum / Tether', 'crypto', 1],
                    ['EURUSD', 'Euro / US Dollar', 'forex', 1],
                    ['GBPUSD', 'British Pound / US Dollar', 'forex', 1]
                ];
                
                $stmt = $pdo->prepare("INSERT INTO symbols (symbol, name, type, is_featured) VALUES (?, ?, ?, ?)");
                foreach ($symbols as $symbol) {
                    $stmt->execute($symbol);
                }
                
                // Insert sample prices
                $pdo->exec("
                    INSERT INTO prices (symbol_id, price, change_percent_24h, volume_24h) VALUES
                    (1, 45000.00, 2.5, 1250.50),
                    (2, 3200.00, -1.2, 8500.25),
                    (3, 1.0850, 0.3, 125000.00),
                    (4, 1.2650, -0.5, 98000.00)
                ");
                
                echo '<div class="step success">✅ Sample data inserted!</div>';
            } else {
                echo '<div class="step success">✅ Sample data already exists!</div>';
            }
            
            echo '<div class="step success">🎉 Installation completed successfully!</div>';
            echo '<div class="step">
                <h3>Login Information:</h3>
                <p>Email: admin@turgis.com</p>
                <p>Password: password</p>
                <p><a href="/" class="btn">Go to Homepage</a></p>
            </div>';
            
        } catch (Exception $e) {
            echo '<div class="step error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        ?>
        <div class="step">
            <h3>Ready to install Turgis Trading Platform</h3>
            <p>This will create the necessary database tables and sample data.</p>
            <p><a href="?run=1" class="btn">Start Installation</a></p>
        </div>
        <?php
    }
    ?>
</body>
</html>
