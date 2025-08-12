<?php
/**
 * Routes Configuration
 */

// Landing Module Routes
$router->addRoute('GET', '/', 'LandingController@index');
$router->addRoute('GET', '/home', 'LandingController@index');

// Markets Module Routes
$router->addRoute('GET', '/markets', 'MarketsController@index');
$router->addRoute('GET', '/markets/search', 'MarketsController@search');
$router->addRoute('GET', '/symbol/{symbol}', 'MarketsController@symbol');
$router->addRoute('POST', '/api/favorites', 'MarketsController@toggleFavorite');

// Trading Module Routes
$router->addRoute('GET', '/trading', 'TradingController@index');
$router->addRoute('GET', '/trading/{symbol}', 'TradingController@symbol');
$router->addRoute('POST', '/api/orders', 'TradingController@placeOrder');
$router->addRoute('GET', '/api/orders', 'TradingController@getOrders');
$router->addRoute('DELETE', '/api/orders/{id}', 'TradingController@cancelOrder');

// Accounts Module Routes
$router->addRoute('GET', '/login', 'AccountsController@loginForm');
$router->addRoute('POST', '/login', 'AccountsController@login');
$router->addRoute('GET', '/register', 'AccountsController@registerForm');
$router->addRoute('POST', '/register', 'AccountsController@register');
$router->addRoute('GET', '/logout', 'AccountsController@logout');
$router->addRoute('GET', '/profile', 'AccountsController@profile');
$router->addRoute('POST', '/profile', 'AccountsController@updateProfile');
$router->addRoute('GET', '/balance', 'AccountsController@balance');

// Blog Module Routes
$router->addRoute('GET', '/blog', 'BlogController@index');
$router->addRoute('GET', '/blog/{slug}', 'BlogController@post');
$router->addRoute('GET', '/news', 'BlogController@news');

// Admin Module Routes
$router->addRoute('GET', '/admin', 'AdminController@dashboard');
$router->addRoute('GET', '/admin/users', 'AdminController@users');
$router->addRoute('GET', '/admin/symbols', 'AdminController@symbols');
$router->addRoute('GET', '/admin/settings', 'AdminController@settings');
$router->addRoute('POST', '/admin/settings', 'AdminController@updateSettings');

// API Routes
$router->addRoute('GET', '/api/prices', 'ApiController@prices');
$router->addRoute('GET', '/api/chart/{symbol}', 'ApiController@chartData');
$router->addRoute('GET', '/api/orderbook/{symbol}', 'ApiController@orderBook');
$router->addRoute('GET', '/api/trades/{symbol}', 'ApiController@recentTrades');

// Language and Theme Routes
$router->addRoute('POST', '/api/language', 'SettingsController@setLanguage');
$router->addRoute('POST', '/api/theme', 'SettingsController@setTheme');

// Error Routes
$router->addRoute('GET', '/404', 'ErrorController@notFound');
$router->addRoute('GET', '/500', 'ErrorController@serverError');
