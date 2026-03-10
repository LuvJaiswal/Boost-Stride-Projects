<?php
/**
 * Uploader - Secure File Upload Handler
 */

namespace Core;

class Uploader {
    private static $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private static $max_size = 5242880; // 5MB

    public static function upload($file, $target_dir) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'No file uploaded or upload error.'];
        }

        // Validate type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        if (!in_array($mime_type, self::$allowed_types)) {
            return ['error' => 'Invalid file type. Only JPG, PNG, WEBP and GIF are allowed.'];
        }

        // Validate size
        if ($file['size'] > self::$max_size) {
            return ['error' => 'File is too large. Max size is 5MB.'];
        }

        // Create directory if not exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . $extension;
        $target_path = $target_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return ['success' => true, 'filename' => $filename, 'path' => 'uploads/' . $filename];
        }

        return ['error' => 'Failed to move uploaded file.'];
    }
}
