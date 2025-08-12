<?php
/**
 * Turgis - Modular Forex/Crypto Trading Platform
 * Entry Point
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('ROOT_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('MODULES_PATH', ROOT_PATH . '/modules');
define('SHARED_PATH', ROOT_PATH . '/shared');
define('LANGUAGES_PATH', ROOT_PATH . '/languages');

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        CORE_PATH . '/' . $class . '.php',
        MODULES_PATH . '/' . strtolower($class) . '/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Start session
session_start();

// Load configuration
require_once CONFIG_PATH . '/config.php';

// Initialize core components
$database = new Database();
$security = new Security();
$router = new Router();

// Load routes
require_once CONFIG_PATH . '/routes.php';

// Handle request
$router->handleRequest();
