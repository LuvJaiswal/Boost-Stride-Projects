<?php
require_once 'admin/core/Config.php';
require_once 'admin/Core/Database.php';

use Core\Database;

$db = new \Core\Database();
$result = $db->setup();

if ($result === true) {
    echo "Database tables (including new 'pages' table) successfully created or updated!";
} else {
    echo "Error during setup: " . $result;
}
unlink(__FILE__); // Self-destruct after running
?>
