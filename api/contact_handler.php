<?php
/**
 * Lead Capture API - Boost Stride Framework
 */

header('Content-Type: application/json');
require_once '../admin/core/Config.php';
require_once '../admin/core/Database.php';

use Core\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get JSON or Form Data
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$subject = trim($input['subject'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Ensure leads table exists (Safety catch for new installations)
    $db->exec("CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255),
        message TEXT,
        status ENUM('new', 'read', 'responded') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $db->prepare("INSERT INTO leads (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$name, $email, $subject, $message]);

    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Your message has been received! Our team will contact you soon.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'System error: Could not save lead.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
