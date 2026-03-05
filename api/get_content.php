<?php
/**
 * Public REST API for Frontend - Boost Stride
 */

require_once '../admin/core/Config.php';
require_once '../admin/core/Database.php';
require_once '../admin/core/Middleware.php';

use Core\Database;
use Core\Middleware;

// Initialize Security Headers (CORS)
Middleware::setCORS();
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    /**
     * Data Retrieval Strategy:
     * We pull all site state in one fast MySQL query where possible.
     */

    // 1. Fetch Global Settings
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
    $settingsRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 2. Fetch Services
    $stmt = $db->query("SELECT * FROM services ORDER BY sort_order ASC");
    $services = $stmt->fetchAll();

    // 3. Fetch Testimonials
    $stmt = $db->query("SELECT * FROM testimonials");
    $testimonials = $stmt->fetchAll();

    // Response Assembly - Syncing with Admin Panel Keys
    $response = [
        "status" => "success",
        "timestamp" => time(),
        "data" => [
            "hero" => json_decode($settingsRaw['hero_data'] ?? '{"title": "Welcome to Boost Stride", "subtitle": "Professional Auto Shop"}'),
            "contact" => json_decode($settingsRaw['contact_info'] ?? '{"phone": "+012 345 6789", "email": "info@example.com"}'),
            "seo" => json_decode($settingsRaw['seo_data'] ?? '{"title": "Boost Stride"}'),
            "footer" => json_decode($settingsRaw['footer_data'] ?? '{}'),
            "menu" => json_decode($settingsRaw['menu_data'] ?? '{}'),
            "services" => $services,
            "testimonials" => $testimonials
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Critical data retrieval error. Please check system logs."
    ]);
}
