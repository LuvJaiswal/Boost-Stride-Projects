<?php
require_once 'admin/core/Config.php';
require_once 'admin/core/Database.php';
use Core\Database;
try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, title, description, content FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($services, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
