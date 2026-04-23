<?php
require_once __DIR__ . '/../config/db.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: " . $path);
    exit;
}

function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key) {
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }
    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $message;
}

function is_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_logged_in()) {
        redirect('../admin/login.php');
    }
}

function admin_name() {
    return $_SESSION['admin_name'] ?? 'Admin';
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function send_contact_email($name, $email, $phone, $subject, $message) {
    $to = CONTACT_RECEIVER_EMAIL;
    $mailSubject = "LiveKerala Contact: " . ($subject ?: 'New Message');
    $body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\nMessage:\n{$message}";
    $headers = "From: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    return @mail($to, $mailSubject, $body, $headers);
}

function listing_status_badge($status) {
    return (int)$status === 1 ? 'Published' : 'Pending';
}

function count_rows($table) {
    global $pdo;
    return (int)$pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}
?>