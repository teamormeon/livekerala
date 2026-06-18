<?php
require_once 'includes.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'Invalid package selected.');
    redirect('packages.php');
}

$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$package = $stmt->fetch();

if (!$package) {
    set_flash('error', 'Package not found.');
    redirect('packages.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $duration_days = (int)($_POST['duration_days'] ?? 30);
    $description = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;

    if ($name === '') {
        set_flash('error', 'Package name is required.');
        redirect('package-edit.php?id=' . $id);
    }

    $slug = slugify($name);

    $check = $pdo->prepare("SELECT COUNT(*) FROM packages WHERE slug = ? AND id != ?");
    $check->execute([$slug, $id]);
    if ((int)$check->fetchColumn() > 0) {
        $slug .= '-' . $id;
    }

    $update = $pdo->prepare("UPDATE packages SET name = ?, slug = ?, price = ?, duration_days = ?, description = ?, features = ?, sort_order = ?, status = ? WHERE id = ?");
    $update->execute([$name, $slug, $price, $duration_days, $description, $features, $sort_order, $status, $id]);

    set_flash('success', 'Package updated successfully.');
    redirect('packages.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package | LiveKerala Admin</title>
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
        <div class="admin-topbar">
            <h1>Edit Package</h1>
            <a class="btn btn-sm" href="packages.php">Back to Packages</a>
        </div>

        <?php if ($error = get_flash('error')): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <div class="form-card" style="padding:20px; max-width: 860px;">
            <form method="post">
                <div class="form-row">
                    <input type="text" name="name" placeholder="Package Name" value="<?= e($package['name']) ?>" required>
                    <input type="number" step="0.01" name="price" placeholder="Price" value="<?= e($package['price']) ?>">
                </div>

                <div class="form-row" style="margin-top:12px">
                    <input type="number" name="duration_days" placeholder="Duration (Days)" value="<?= (int)$package['duration_days'] ?>">
                    <input type="number" name="sort_order" placeholder="Sort Order" value="<?= (int)$package['sort_order'] ?>">
                </div>

                <div style="margin-top:12px">
                    <textarea name="description" placeholder="Short Description"><?= e($package['description']) ?></textarea>
                </div>

                <div style="margin-top:12px">
                    <label class="muted" style="display:block;margin-bottom:6px">Features (one per line)</label>
                    <textarea name="features" placeholder="Logo&#10;Address&#10;Email&#10;Phone"><?= e($package['features']) ?></textarea>
                </div>

                <div style="margin-top:12px">
                    <label style="display:flex;align-items:center;gap:10px">
                        <input type="checkbox" name="status" value="1" <?= (int)$package['status'] === 1 ? 'checked' : '' ?>>
                        Active Package
                    </label>
                </div>

                <div style="margin-top:16px">
                    <button class="btn" type="submit">Update Package</button>
                </div>
            </form>
        </div>
    </section>
</div>
</body>
</html>