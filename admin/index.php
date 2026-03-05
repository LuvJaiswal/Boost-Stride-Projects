<?php
/**
 * Admin Panel Entry Point
 */

require_once 'core/Config.php';
require_once 'core/Database.php';
require_once 'core/JWT.php';
require_once 'core/Auth.php';
require_once 'core/Middleware.php';

use Core\Auth;
use Core\Database;
use Core\Middleware;

// Security Gatekeeper
Middleware::protectWeb();

$db = Database::getInstance();
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Include UI
include 'views/layouts/header.php';

// Route to content
switch($page) {
    case 'hero':
        include 'views/pages/hero.php';
        break;
    case 'services':
        include 'views/pages/services.php';
        break;
    case 'testimonials':
        include 'views/pages/testimonials.php';
        break;
    case 'settings':
        include 'views/pages/settings.php';
        break;
    case 'seo':
        include 'views/pages/seo.php';
        break;
    case 'footer':
        include 'views/pages/footer.php';
        break;
    case 'menu':
        include 'views/pages/menu.php';
        break;
    default:
        include 'views/pages/dashboard.php';
        break;
}

include 'views/layouts/footer.php';
