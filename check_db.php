<?php
require_once 'd:/xampp/htdocs/Hobart Auto Shop/admin/core/Config.php';
require_once 'd:/xampp/htdocs/Hobart Auto Shop/admin/core/Database.php';
use Core\Database;

try {
    $db = Database::getInstance();
    $result = $db->query("SHOW TABLES LIKE 'leads'")->fetch();
    if ($result) {
        echo "TABLE_EXISTS";
    } else {
        echo "TABLE_MISSING";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
