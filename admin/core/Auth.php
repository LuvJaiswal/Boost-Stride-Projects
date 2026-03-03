<?php
/**
 * Advanced Authentication & Security Handler - MySQL Version
 */

namespace Core;

require_once 'JWT.php';
require_once 'Database.php';

class Auth {
    
    /**
     * Verify credentials and return JWT if successful
     */
    public static function login($username, $password, $ip) {
        // 1. Rate Limiting Check
        if (self::isLockedOut($ip)) {
            return ["error" => "Too many attempts. Locked out for 15 minutes."];
        }

        // 2. Verify User against Config (or Table)
        if ($username === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
            self::resetAttempts($ip);

            $payload = [
                "user" => $username,
                "role" => "admin",
                "iat" => time(),
                "exp" => time() + JWT_EXPIRY
            ];

            $token = JWT::encode($payload, JWT_SECRET);
            
            // Secure Cookie Setup
            setcookie("auth_token", $token, [
                'expires' => time() + JWT_EXPIRY,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
                'secure' => isset($_SERVER['HTTPS'])
            ]);

            return ["success" => true, "token" => $token];
        }

        // 3. Increment Attempts
        self::recordFailure($ip);
        return ["error" => "Invalid credentials. Remaining attempts: " . (MAX_LOGIN_ATTEMPTS - self::getAttemptCount($ip))];
    }

    public static function check() {
        $token = $_COOKIE['auth_token'] ?? null;
        if (!$token) return false;

        $decoded = JWT::decode($token, JWT_SECRET);
        if (!$decoded || $decoded['role'] !== 'admin') {
            return false;
        }

        return true;
    }

    private static function isLockedOut($ip) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
        $row = $stmt->fetch();

        if ($row && $row['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            if (time() < ($row['last_attempt'] + LOCKOUT_TIME)) {
                return true;
            } else {
                self::resetAttempts($ip);
            }
        }
        return false;
    }

    private static function getAttemptCount($ip) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT attempts FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
        return $stmt->fetchColumn() ?: 0;
    }

    private static function recordFailure($ip) {
        $db = Database::getInstance();
        $sql = "INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                VALUES (?, 1, ?) 
                ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1, 
                last_attempt = VALUES(last_attempt)";
        $db->prepare($sql)->execute([$ip, time()]);
    }

    private static function resetAttempts($ip) {
        $db = Database::getInstance();
        $db->prepare("DELETE FROM login_attempts WHERE ip_address = ?")->execute([$ip]);
    }

    public static function logout() {
        setcookie("auth_token", "", time() - 3600, "/");
        if (session_status() == PHP_SESSION_NONE) session_start();
        session_destroy();
    }
}
