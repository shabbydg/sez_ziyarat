<?php
/**
 * Admin Passport Photo Upload Handler with Cropping
 * 
 * Same functionality as regular upload-passport-photo.php but with admin override support
 */

require_once 'common/config.php';
require_once 'common/functions.php';

// Include the main upload-passport-photo.php logic
// Adjust paths for admin directory
$adminthisFile = str_replace('\\', '/', __FILE__);
$adminsrvRoot  = str_replace('admin/upload-passport-photo.php', '', $adminthisFile);

// Override SRV_ROOT constant for admin context
if (!defined('SRV_ROOT_OVERRIDE')) {
    define('SRV_ROOT_OVERRIDE', $adminsrvRoot);
}

// Include main handler
require $adminsrvRoot . 'upload-passport-photo.php';
?>



