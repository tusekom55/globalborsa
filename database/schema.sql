-- Turgis Trading Platform Database Schema

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    api_key VARCHAR(64) UNIQUE,
    language VARCHAR(5) DEFAULT 'tr',
    theme VARCHAR(10) DEFAULT 'dark',
    timezone VARCHAR(50) DEFAULT 'Europe/Istanbul',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Login attempts table for security
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_time (email, attempted_at)
);

-- Trading symbols table
CREATE TABLE IF NOT EXISTS symbols (
    id INT AUTO_INCREMENT PRIMARY KEY,
    symbol VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    base_currency VARCHAR(10) NOT NULL,
    quote_currency VARCHAR(10) NOT NULL,
    type ENUM('forex', 'crypto', 'stock', 'commodity') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    min_trade_amount DECIMAL(20, 8) DEFAULT 0.00000001,
    max_trade_amount DECIMAL(20, 8),
    price_precision INT DEFAULT 8,
    quantity_precision INT DEFAULT 8,
    trading_fee DECIMAL(5, 4) DEFAULT 0.001,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Current prices table
CREATE TABLE IF NOT EXISTS prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    symbol_id INT NOT NULL,
    price DECIMAL(20, 8) NOT NULL,
    bid DECIMAL(20, 8),
    ask DECIMAL(20, 8),
    volume_24h DECIMAL(20, 8) DEFAULT 0,
    change_24h DECIMAL(10, 4) DEFAULT 0,
    change_percent_24h DECIMAL(10, 4) DEFAULT 0,
    high_24h DECIMAL(20, 8),
    low_24h DECIMAL(20, 8),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (symbol_id) REFERENCES symbols(id) ON DELETE CASCADE,
    UNIQUE KEY unique_symbol (symbol_id)
);

-- Price history for charts
CREATE TABLE IF NOT EXISTS price_history (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    symbol_id INT NOT NULL,
    timeframe ENUM('1m', '5m', '15m', '30m', '1h', '4h', '1d', '1w') NOT NULL,
    open_price DECIMAL(20, 8) NOT NULL,
    high_price DECIMAL(20, 8) NOT NULL,
    low_price DECIMAL(20, 8) NOT NULL,
    close_price DECIMAL(20, 8) NOT NULL,
    volume DECIMAL(20, 8) DEFAULT 0,
    timestamp DATETIME NOT NULL,
    FOREIGN KEY (symbol_id) REFERENCES symbols(id) ON DELETE CASCADE,
    UNIQUE KEY unique_candle (symbol_id, timeframe, timestamp),
    INDEX idx_symbol_timeframe_time (symbol_id, timeframe, timestamp)
);

-- User balances
CREATE TABLE IF NOT EXISTS balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    currency VARCHAR(10) NOT NULL,
    balance DECIMAL(20, 8) DEFAULT 0,
    locked_balance DECIMAL(20, 8) DEFAULT 0,
    demo_balance DECIMAL(20, 8) DEFAULT 10000,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_currency (user_id, currency)
);

-- Trading orders
CREATE TABLE IF NOT EXISTS orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symbol_id INT NOT NULL,
    type ENUM('market', 'limit', 'stop', 'stop_limit') NOT NULL,
    side ENUM('buy', 'sell') NOT NULL,
    quantity DECIMAL(20, 8) NOT NULL,
    price DECIMAL(20, 8),
    stop_price DECIMAL(20, 8),
    filled_quantity DECIMAL(20, 8) DEFAULT 0,
    remaining_quantity DECIMAL(20, 8),
    status ENUM('pending', 'partial', 'filled', 'cancelled', 'rejected') DEFAULT 'pending',
    is_demo BOOLEAN DEFAULT TRUE,
    fee DECIMAL(20, 8) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (symbol_id) REFERENCES symbols(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_symbol_status (symbol_id, status)
);

-- Trade executions
CREATE TABLE IF NOT EXISTS trades (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    symbol_id INT NOT NULL,
    side ENUM('buy', 'sell') NOT NULL,
    quantity DECIMAL(20, 8) NOT NULL,
    price DECIMAL(20, 8) NOT NULL,
    fee DECIMAL(20, 8) DEFAULT 0,
    is_demo BOOLEAN DEFAULT TRUE,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (symbol_id) REFERENCES symbols(id) ON DELETE CASCADE,
    INDEX idx_user_time (user_id, executed_at),
    INDEX idx_symbol_time (symbol_id, executed_at)
);

-- User favorites
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symbol_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (symbol_id) REFERENCES symbols(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_symbol (user_id, symbol_id)
);

-- Blog posts
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id INT NOT NULL,
    category ENUM('news', 'analysis', 'tutorial', 'announcement') DEFAULT 'news',
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status_published (status, published_at),
    INDEX idx_category_published (category, published_at)
);

-- Site settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Exchange API configurations
CREATE TABLE IF NOT EXISTS exchange_apis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    api_key VARCHAR(255),
    api_secret VARCHAR(255),
    api_url VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    rate_limit INT DEFAULT 100,
    supported_symbols TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT IGNORE INTO users (username, email, password, role, status, email_verified) 
VALUES ('admin', 'admin@turgis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', TRUE);

-- Insert sample trading symbols
INSERT IGNORE INTO symbols (symbol, name, base_currency, quote_currency, type, is_featured) VALUES
('BTCUSDT', 'Bitcoin / Tether', 'BTC', 'USDT', 'crypto', TRUE),
('ETHUSDT', 'Ethereum / Tether', 'ETH', 'USDT', 'crypto', TRUE),
('EURUSD', 'Euro / US Dollar', 'EUR', 'USD', 'forex', TRUE),
('GBPUSD', 'British Pound / US Dollar', 'GBP', 'USD', 'forex', TRUE),
('XAUUSD', 'Gold / US Dollar', 'XAU', 'USD', 'commodity', TRUE),
('ADAUSDT', 'Cardano / Tether', 'ADA', 'USDT', 'crypto', FALSE),
('DOTUSDT', 'Polkadot / Tether', 'DOT', 'USDT', 'crypto', FALSE),
('USDTRY', 'US Dollar / Turkish Lira', 'USD', 'TRY', 'forex', TRUE);

-- Insert sample prices
INSERT IGNORE INTO prices (symbol_id, price, bid, ask, volume_24h, change_24h, change_percent_24h, high_24h, low_24h) VALUES
(1, 45000.00, 44995.00, 45005.00, 1250.50, 1200.00, 2.74, 45500.00, 43800.00),
(2, 3200.00, 3198.50, 3201.50, 8500.25, -85.50, -2.60, 3350.00, 3180.00),
(3, 1.0850, 1.0848, 1.0852, 125000.00, 0.0025, 0.23, 1.0875, 1.0820),
(4, 1.2650, 1.2648, 1.2652, 98000.00, -0.0035, -0.28, 1.2695, 1.2630),
(5, 1985.50, 1985.20, 1985.80, 2500.00, 15.30, 0.78, 1995.00, 1970.00),
(6, 0.4850, 0.4848, 0.4852, 15000.00, 0.0125, 2.64, 0.4920, 0.4720),
(7, 7.25, 7.24, 7.26, 5500.00, -0.18, -2.42, 7.55, 7.20),
(8, 34.25, 34.23, 34.27, 85000.00, 0.45, 1.33, 34.50, 33.80);

-- Insert default settings
INSERT IGNORE INTO settings (key_name, value, type, description) VALUES
('site_name', 'Turgis Trading', 'string', 'Site name'),
('site_description', 'Professional Forex and Crypto Trading Platform', 'string', 'Site description'),
('maintenance_mode', 'false', 'boolean', 'Maintenance mode status'),
('registration_enabled', 'true', 'boolean', 'User registration enabled'),
('demo_mode_default', 'true', 'boolean', 'Default demo mode for new users'),
('default_demo_balance', '10000', 'number', 'Default demo balance for new users'),
('trading_fee', '0.001', 'number', 'Default trading fee percentage'),
('max_open_orders', '50', 'number', 'Maximum open orders per user'),
('price_update_interval', '1000', 'number', 'Price update interval in milliseconds');
