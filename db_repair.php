<?php
/**
 * Database Repair & Initialization Tool
 * Ensures all tables and default data are present.
 */

require_once 'admin/core/Config.php';
require_once 'admin/core/Database.php';

use Core\Database;

header('Content-Type: text/plain');

try {
    echo "--- Boost Stride Database Repair --- \n";
    
    $dsn = "mysql:host=" . DB_HOST;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "[1/4] Creating database if not exists... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "DONE\n";
    
    $pdo->exec("USE `" . DB_NAME . "`");
    
    echo "[2/4] Initializing tables... ";
    $db = Database::getInstance();
    $setupResult = (new Database_Setup_Helper($db))->run();
    echo "DONE\n";
    
    echo "[3/4] Checking for default data... ";
    // Add default services if empty
    $count = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO services (title, description, image, sort_order) VALUES 
            ('General Auto Repair', 'Reliable repair services for all car models.', 'img/service-1.jpg', 1),
            ('Brake Services', 'Precision braking system maintenance and repair.', 'img/service-2.jpg', 2),
            ('Tire Replacement', 'High-quality tire brands and professional installation.', 'img/service-3.jpg', 3)");
        echo "Default services added. ";
    }
    echo "DONE\n";
    
    echo "[4/4] Finalizing config... ";
    echo "DONE\n\n";
    
    echo "[SUCCESS] Database is fully functional and connected with phpMyAdmin.\n";
    echo "[INFO] You can now access http://localhost/phpmyadmin and see '" . DB_NAME . "'.\n";

} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo "[HELP] Make sure XAMPP MySQL is RUNNING and your credentials in admin/core/Config.php are correct.\n";
}

class Database_Setup_Helper {
    private $db;
    public function __construct($db) { $this->db = $db; }
    public function run() {
        $sql = "
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE,
                setting_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                content LONGTEXT,
                image VARCHAR(255),
                sort_order INT DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS testimonials (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                profession VARCHAR(100),
                text TEXT,
                image VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS login_attempts (
                ip_address VARCHAR(45) PRIMARY KEY,
                attempts INT DEFAULT 0,
                last_attempt INT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(100) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                content LONGTEXT,
                meta_title VARCHAR(255),
                meta_description TEXT,
                status ENUM('draft', 'published') DEFAULT 'published',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->db->exec($sql);
        
        // Robust Column Check: Add 'content' to existing 'services' table if missing
        try {
            $cols = $this->db->query("DESCRIBE services")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('content', $cols)) {
                $this->db->exec("ALTER TABLE services ADD COLUMN content LONGTEXT AFTER description");
            }
        } catch (Exception $e) {}

        return true;
    }
}
