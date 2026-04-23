<?php
require_once 'includes.php';

$totalListings = count_rows('listings');
$totalCategories = count_rows('categories');
$totalMessages = count_rows('contact_messages');
$totalPackages = count_rows('packages');

$listings = $pdo->query("SELECT l.*, c.name AS category_name FROM listings l LEFT JOIN categories c ON c.id = l.category_id ORDER BY l.id DESC LIMIT 10")->fetchAll();
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | LiveKerala</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-panel">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <h2>LiveKerala</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="packages.php">Packages</a>
        <a href="categories.php">Categories</a>
        <a href="listings.php">Listings</a>
        <a href="messages.php">Messages</a>
        <a href="logout.php">Logout</a>
    </aside>
    <section class="admin-content">
        <div class="admin-topbar">
            <h1>Dashboard</h1>
            <p>Welcome, <?= e(admin_name()) ?></p>
        </div>

        <?php if ($success = get_flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

        <div class="admin-stats">
            <div class="stat-card"><h3><?= $totalListings ?></h3><p>Total Listings</p></div>
            <div class="stat-card"><h3><?= $totalCategories ?></h3><p>Total Categories</p></div>
            <div class="stat-card"><h3><?= $totalPackages ?></h3><p>Total Packages</p></div>
            <div class="stat-card"><h3><?= $totalMessages ?></h3><p>Contact Messages</p></div>
        </div>

        <div class="table-card">
            <div class="table-head"><h3>Recent Listings</h3><a class="btn btn-sm" href="listings.php">Manage Listings</a></div>
            <table class="admin-table">
                <thead><tr><th>Title</th><th>Category</th><th>Location</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($listings as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?></td>
                        <td><?= e($item['category_name']) ?></td>
                        <td><?= e($item['location']) ?></td>
                        <td><?= e(listing_status_badge($item['status'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card" style="margin-top:24px">
            <div class="table-head"><h3>Recent Messages</h3><a class="btn btn-sm" href="messages.php">View Messages</a></div>
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Email</th><th>Subject</th></tr></thead>
                <tbody>
                <?php foreach ($messages as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['email']) ?></td>
                        <td><?= e($item['subject']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>