<?php
session_start();

define('APP_NAME', 'LiveKerala');
define('BASE_URL', '');

define('DB_HOST', 'localhost');
define('DB_NAME', 'livekerala_db');
define('DB_USER', 'abhilash');
define('DB_PASS', 'dH23NV1V4XlL');

define('ADMIN_EMAIL', 'info@livekerala.com');
define('CONTACT_RECEIVER_EMAIL', 'info@livekerala.com');

date_default_timezone_set('Asia/Kolkata');

function base_url($path = '') {
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $base ? $base . '/' . $path : $path;
}
?>