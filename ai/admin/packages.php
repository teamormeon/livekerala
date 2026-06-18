<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $duration_days = (int)($_POST['duration_days'] ?? 30);
    $description = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO packages (name, slug, price, duration_days, description, features, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$name, slugify($name), $price, $duration_days, $description, $features, $sort_order]);
        set_flash('success', 'Package created successfully.');
    } else {
        set_flash('error', 'Package name is required.');
    }
    redirect('packages.php');
}

if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE packages SET status = IF(status=1,0,1) WHERE id = ?");
    $stmt->execute([(int)$_GET['toggle']]);
    set_flash('success', 'Package status updated.');
    redirect('packages.php');
}

$packages = $pdo->query("SELECT * FROM packages ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages | LiveKerala Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-panel">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <h2>LiveKerala</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="packages.php" class="active">Packages</a>
        <a href="categories.php">Categories</a>
        <a href="listings.php">Listings</a>
        <a href="messages.php">Messages</a>
        <a href="logout.php">Logout</a>
    </aside>
    <section class="admin-content">
        <div class="admin-topbar"><h1>Packages</h1></div>

        <?php if ($success = get_flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error = get_flash('error')): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <div class="grid-2-admin">
            <div class="form-card" style="padding:20px">
                <h3>Create Package</h3>
                <form method="post">
                    <input type="text" name="name" placeholder="Package Name" required>
                    <div style="margin-top:12px"><input type="number" step="0.01" name="price" placeholder="Price" value="0"></div>
                    <div style="margin-top:12px"><input type="number" name="duration_days" placeholder="Duration (Days)" value="30"></div>
                    <div style="margin-top:12px"><input type="number" name="sort_order" placeholder="Sort Order" value="0"></div>
                    <div style="margin-top:12px"><textarea name="description" placeholder="Short Description"></textarea></div>
                    <div style="margin-top:12px">
                        <label class="muted" style="display:block;margin-bottom:6px">Features (one per line)</label>
                        <textarea name="features" placeholder="Logo&#10;Address&#10;Email&#10;Phone"></textarea>
                    </div>
                    <div style="margin-top:12px"><button class="btn" type="submit">Save Package</button></div>
                </form>
            </div>

            <div class="table-card">
                <div class="table-head"><h3>All Packages</h3></div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Price</th><th>Duration</th><th>Status</th><th>Features</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($packages as $item): ?>
                        <tr>
                            <td><?= e($item['name']) ?></td>
                            <td>₹<?= number_format((float)$item['price'], 0) ?></td>
                            <td><?= (int)$item['duration_days'] ?> days</td>
                            <td><?= (int)$item['status'] === 1 ? 'Active' : 'Disabled' ?></td>
                            <td style="white-space:pre-line"><?= e($item['features']) ?></td>
                            <td class="actions">
                                <a href="package-edit.php?id=<?= (int)$item['id'] ?>">Edit</a>
                                <a href="?toggle=<?= (int)$item['id'] ?>">Toggle Status</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
</body>
</html>