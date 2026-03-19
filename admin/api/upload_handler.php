<?php
/**
 * AJAX Image Upload Handler for Summernote
 */
require_once __DIR__ . '/../core/Config.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Uploader.php';
require_once __DIR__ . '/../Core/Auth.php';

use Core\Uploader;
use Core\Auth;

// Simple authentication check
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $result = Uploader::upload($_FILES['image'], UPLOAD_DIR);
    
    if (isset($result['success'])) {
        // Return the path relative to the admin directory so it works in the editor preview
        // Uploader returns 'uploads/filename.jpg'
        echo $result['path'];
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo $result['error'];
    }
    exit;
}

header('HTTP/1.1 400 Bad Request');
echo 'Invalid request';
