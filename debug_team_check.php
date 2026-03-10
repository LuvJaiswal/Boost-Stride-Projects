<?php
// Absolute path based test script
$root = __DIR__;
require_once $root . '/admin/core/Config.php';
require_once $root . '/admin/core/Database.php';

use Core\Database;

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT * FROM team");
    $team = $stmt->fetchAll();
    echo json_encode([
        "status" => "success",
        "count" => count($team),
        "data" => $team
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
