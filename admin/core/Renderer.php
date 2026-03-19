<?php
/**
 * Renderer - Content Rendering Utilities
 */

namespace Core;

class Renderer {
    /**
     * Fixes paths in HTML content (e.g., from Summernote) 
     * to work correctly on the frontend.
     */
    public static function content($html) {
        if (empty($html)) return '';
        
        // Fix image paths
        // Replaces src="uploads/" with src="admin/uploads/"
        $html = str_replace('src="uploads/', 'src="admin/uploads/', $html);
        
        // Fix link paths to internal pages if needed
        // (Optional: can add more mapping here)
        
        return $html;
    }
}
