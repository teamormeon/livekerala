<?php
require_once __DIR__ . '/functions.php';
$current = basename($_SERVER['PHP_SELF']);
function is_active($page, $current) {
    return $page === $current ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . APP_NAME : APP_NAME ?></title>
    <meta name="description" content="<?= isset($metaDescription) ? e($metaDescription) : 'LiveKerala Directory' ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a href="<?= base_url('index.php') ?>" class="logo">Live<span>Kerala</span></a>
        <nav class="site-nav">
            <a class="<?= is_active('index.php', $current) ?>" href="<?= base_url('index.php') ?>">Home</a>
            <a class="<?= is_active('categories.php', $current) ?>" href="<?= base_url('categories.php') ?>">Categories</a>
            <a class="<?= is_active('about.php', $current) ?>" href="<?= base_url('about.php') ?>">About</a>
            <a class="<?= is_active('pricing.php', $current) ?>" href="<?= base_url('pricing.php') ?>">Pricing</a>
            <a class="<?= is_active('why-livekerala.php', $current) ?>" href="<?= base_url('why-livekerala.php') ?>">Why Live Kerala</a>
            <a class="<?= is_active('contact.php', $current) ?>" href="<?= base_url('contact.php') ?>">Contact</a>
            <a class="btn btn-sm <?= is_active('add-listing.php', $current) ?>" href="<?= base_url('add-listing.php') ?>">Add Listing</a>
        </nav>
        <button class="menu-toggle" aria-label="Toggle Menu">☰</button>
    </div>
</header>
<main>
<?php if ($success = get_flash('success')): ?>
<div class="container" style="padding-top:18px"><div class="alert alert-success"><?= e($success) ?></div></div>
<?php endif; ?>
<?php if ($error = get_flash('error')): ?>
<div class="container" style="padding-top:18px"><div class="alert alert-error"><?= e($error) ?></div></div>
<?php endif; ?>
