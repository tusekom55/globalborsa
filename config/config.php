<?php
/**
 * Main Configuration File
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u225998063_newe');
define('DB_USER', 'u225998063_newe');
define('DB_PASS', '123456Tubb');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'Global Borsa');
define('SITE_URL', 'https://silver-eland-900684.hostingersite.com');
define('SITE_EMAIL', 'admin@globalborsa.com');

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Default Settings
define('DEFAULT_LANGUAGE', 'tr');
define('DEFAULT_THEME', 'dark');
define('DEFAULT_TIMEZONE', 'Europe/Istanbul');

// API Configuration
define('DEMO_MODE', true);
define('API_RATE_LIMIT', 100); // requests per minute

// File Upload
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Cache Settings
define('CACHE_ENABLED', false);
define('CACHE_LIFETIME', 300); // 5 minutes

// Development Settings
define('DEBUG_MODE', true);
define('LOG_ERRORS', true);
define('LOG_PATH', ROOT_PATH . '/logs');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);
