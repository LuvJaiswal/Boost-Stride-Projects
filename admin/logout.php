<?php
/**
 * Logout Handler
 */

require_once 'core/Config.php';
require_once 'core/Auth.php';

use Core\Auth;

Auth::logout();
header("Location: login.php");
exit();
