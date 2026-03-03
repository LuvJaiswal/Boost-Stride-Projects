<?php
/**
 * Auth Middleware - Central Security Gatekeeper
 */

namespace Core;

class Middleware {
    
    /**
     * Protects Web Routes (Redirects to Login)
     */
    public static function protectWeb() {
        if (!Auth::check()) {
            header("Location: login.php");
            exit();
        }
    }

    /**
     * Protects API Routes (Returns 401 JSON)
     */
    public static function protectAPI() {
        if (!Auth::check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                "status" => "error", 
                "message" => "Unauthorized access. Valid token required."
            ]);
            exit();
        }
    }

    /**
     * Cross-Origin Resource Sharing (CORS) Security
     */
    public static function setCORS() {
        header('Access-Control-Allow-Origin: *'); // Change to specific domain in production
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit();
        }
    }
}
