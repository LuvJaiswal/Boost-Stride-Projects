<?php
/**
 * Boost Stride Admin Configuration
 */

define('APP_NAME', 'Boost Stride Admin');
define('VERSION', '1.0.0');

// MySQL Database Settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'boost_stride_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your password here
define('DB_CHARSET', 'utf8mb4');

// Auth Settings
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // Default: 'password'
define('JWT_SECRET', 'boost_stride_super_secret_key_123'); // Change this in production
define('JWT_EXPIRY', 3600); // 1 Hour

// Rate Limiting
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 Minutes

// Paths
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('BASE_URL', '/admin/');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
