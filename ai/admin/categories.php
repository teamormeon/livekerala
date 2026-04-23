<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? '📁');
    $description = trim($_POST['description'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$name, slugify($name), $icon, $description, $sort_order]);
        set_flash('success', 'Category added successfully.');
    }
    redirect('categories.php');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | LiveKerala Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-panel">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <h2>LiveKerala</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="packages.php">Packages</a>
        <a href="categories.php" class="active">Categories</a>
        <a href="listings.php">Listings</a>
        <a href="messages.php">Messages</a>
        <a href="logout.php">Logout</a>
    </aside>
    <section class="admin-content">
        <div class="admin-topbar"><h1>Categories</h1></div>
        <?php if ($success = get_flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <div class="grid-2-admin">
            <div class="form-card" style="padding:20px">
                <h3>Add Category</h3>
                <form method="post">
                    <input type="text" name="name" placeholder="Category Name" required>
                    <div style="margin-top:12px"><input type="text" name="icon" placeholder="Emoji/Icon" value="📁"></div>
                    <div style="margin-top:12px"><textarea name="description" placeholder="Short Description"></textarea></div>
                    <div style="margin-top:12px"><input type="number" name="sort_order" placeholder="Sort Order" value="0"></div>
                    <div style="margin-top:12px"><button class="btn" type="submit">Save Category</button></div>
                </form>
            </div>
            <div class="table-card">
                <div class="table-head"><h3>All Categories</h3></div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Icon</th><th>Order</th></tr></thead>
                    <tbody>
                    <?php foreach ($categories as $item): ?>
                    <tr><td><?= e($item['name']) ?></td><td><?= e($item['icon']) ?></td><td><?= (int)$item['sort_order'] ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
</body>
</html>