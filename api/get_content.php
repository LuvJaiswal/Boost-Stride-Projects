<?php
/**
 * Public REST API for Frontend - Boost Stride
 */

// Use absolute path for robustness
$baseDir = dirname(__DIR__);
require_once $baseDir . '/admin/core/Config.php';
require_once $baseDir . '/admin/core/Database.php';
require_once $baseDir . '/admin/core/Middleware.php';

use Core\Database;
use Core\Middleware;

// Initialize Security Headers (CORS)
Middleware::setCORS();
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    if (!$db) {
        throw new Exception("Database connection failed. Please check your DB settings in Config.php.");
    }
    
    // Verify table existence
    $checkTable = $db->query("SHOW TABLES LIKE 'services'")->rowCount();
    if ($checkTable == 0) {
        throw new Exception("The 'services' table does not exist. Please run the database setup.");
    }

    // 1. Fetch Global Settings
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
    $settingsRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 2. Fetch Services
    $stmt = $db->query("SELECT * FROM services ORDER BY sort_order ASC");
    $services = $stmt->fetchAll();

    // 3. Fetch Testimonials
    $stmt = $db->query("SELECT * FROM testimonials");
    $testimonials = $stmt->fetchAll();

    // 4. Fetch Team
    $stmt = $db->query("SELECT * FROM team ORDER BY sort_order ASC, id DESC");
    $team = $stmt->fetchAll();

    // Response Assembly - Syncing with Admin Panel Keys
    $response = [
        "status" => "success",
        "timestamp" => time(),
        "data" => [
            "hero" => json_decode($settingsRaw['hero_data'] ?? '{"title": "Welcome to Boost Stride", "subtitle": "Professional Auto Shop"}'),
            "contact" => json_decode($settingsRaw['contact_info'] ?? '{"phone": "+012 345 6789", "email": "info@example.com"}'),
            "seo" => json_decode($settingsRaw['seo_data'] ?? '{"title": "Boost Stride"}'),
            "footer" => json_decode($settingsRaw['footer_data'] ?? '{}'),
            "menu" => json_decode($settingsRaw['menu_data'] ?? json_encode([
                "brand_name" => "Boost Stride",
                "cta_text" => "Get A Quote",
                "cta_url" => "quote.php",
                "main_menu" => [
                    ["label" => "Home", "url" => "index.php", "type" => "link"],
                    ["label" => "About", "url" => "about.php", "type" => "link"],
                    ["label" => "Service", "url" => "service.php", "type" => "link"],
                    ["label" => "Pages", "url" => "#", "type" => "dropdown", "children" => [
                        ["label" => "Feature", "url" => "feature.php"],
                        ["label" => "Free Quote", "url" => "quote.php"],
                        ["label" => "Our Team", "url" => "team.php"],
                        ["label" => "Testimonial", "url" => "testimonial.php"]
                    ]],
                    ["label" => "Contact", "url" => "contact.php", "type" => "link"]
                ]
            ])),
            "about" => json_decode($settingsRaw['about_data'] ?? '{}'),
            "features" => json_decode($settingsRaw['features_data'] ?? '[]'),
            "team" => $team,
            "services" => $services,
            "testimonials" => $testimonials
        ]
    ];

    echo json_encode($response, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);

} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Critical data retrieval error. Please check system logs."
    ]);
}
