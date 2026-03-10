<?php
require_once 'admin/core/Config.php';
require_once 'admin/core/Database.php';

use Core\Database;

try {
    $db = Database::getInstance();
    $stmt = $db->query("DESCRIBE services");
    $structure = $stmt->fetchAll();
    echo json_encode($structure, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
