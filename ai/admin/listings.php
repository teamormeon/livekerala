<?php
require_once 'includes.php';

if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare("UPDATE listings SET status = 1 WHERE id = ?");
    $stmt->execute([(int)$_GET['approve']]);
    set_flash('success', 'Listing approved.');
    redirect('listings.php');
}

if (isset($_GET['toggle_featured'])) {
    $stmt = $pdo->prepare("UPDATE listings SET is_featured = IF(is_featured=1,0,1) WHERE id = ?");
    $stmt->execute([(int)$_GET['toggle_featured']]);
    set_flash('success', 'Featured status updated.');
    redirect('listings.php');
}

if (isset($_GET['toggle_premium'])) {
    $stmt = $pdo->prepare("UPDATE listings SET is_premium = IF(is_premium=1,0,1) WHERE id = ?");
    $stmt->execute([(int)$_GET['toggle_premium']]);
    set_flash('success', 'Premium status updated.');
    redirect('listings.php');
}

if (isset($_GET['disable'])) {
    $stmt = $pdo->prepare("UPDATE listings SET status = 0 WHERE id = ?");
    $stmt->execute([(int)$_GET['disable']]);
    set_flash('success', 'Listing disabled.');
    redirect('listings.php');
}

$listings = $pdo->query("SELECT l.*, c.name AS category_name, p.name AS package_name FROM listings l LEFT JOIN categories c ON c.id = l.category_id LEFT JOIN packages p ON p.id = l.package_id ORDER BY l.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings | LiveKerala Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-panel">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <h2>LiveKerala</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="packages.php">Packages</a>
        <a href="categories.php">Categories</a>
        <a href="listings.php" class="active">Listings</a>
        <a href="messages.php">Messages</a>
        <a href="logout.php">Logout</a>
    </aside>
    <section class="admin-content">
        <div class="admin-topbar"><h1>Listings</h1></div>
        <?php if ($success = get_flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr><th>Title</th><th>Category</th><th>Package</th><th>Location</th><th>Status</th><th>Expiry</th><th>Featured</th><th>Premium</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($listings as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?></td>
                        <td><?= e($item['category_name']) ?></td>
                        <td><?= e($item['package_name']) ?></td>
                        <td><?= e($item['location']) ?></td>
                        <td><?= e(listing_status_badge($item['status'])) ?></td>
                        <td><?= e($item['expiry_date']) ?></td>
                        <td><?= $item['is_featured'] ? 'Yes' : 'No' ?></td>
                        <td><?= $item['is_premium'] ? 'Yes' : 'No' ?></td>
                        <td class="actions">
                            <?php if (!(int)$item['status']): ?><a href="?approve=<?= (int)$item['id'] ?>">Approve</a><?php endif; ?>
                            <?php if ((int)$item['status']): ?><a href="?disable=<?= (int)$item['id'] ?>">Disable</a><?php endif; ?>
                            <a href="?toggle_featured=<?= (int)$item['id'] ?>">Toggle Featured</a>
                            <a href="?toggle_premium=<?= (int)$item['id'] ?>">Toggle Premium</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>