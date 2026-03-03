<?php
/**
 * One-time Database Setup script for cPanel
 * Run this once at your-site.com/api/init_db.php
 */

require_once '../admin/core/Config.php';
require_once '../admin/core/Database.php';

use Core\Database;

header('Content-Type: text/plain');

try {
    echo "--- Boost Stride Database Initialization ---\n";
    
    $db = new Database();
    $result = $db->setup();

    if ($result === true) {
        echo "[SUCCESS] Tables created/verified successfully.\n";
        echo "[INFO] Admin Username: " . ADMIN_USER . "\n";
        echo "[INFO] Default Password: password\n";
        echo "[ACTION] Please DELETE this file after completion for security.";
    } else {
        echo "[ERROR] Setup failed: " . $result;
    }

} catch (Exception $e) {
    echo "[CRITICAL] Initialization failed: " . $e->getMessage();
}
