<?php
require_once 'includes.php';
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | LiveKerala Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-panel">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <h2>LiveKerala</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="packages.php">Packages</a>
        <a href="categories.php">Categories</a>
        <a href="listings.php">Listings</a>
        <a href="messages.php" class="active">Messages</a>
        <a href="logout.php">Logout</a>
    </aside>
    <section class="admin-content">
        <div class="admin-topbar"><h1>Contact Messages</h1></div>
        <div class="table-card">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Message</th></tr></thead>
                <tbody>
                <?php foreach ($messages as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['email']) ?></td>
                        <td><?= e($item['phone']) ?></td>
                        <td><?= e($item['subject']) ?></td>
                        <td><?= e($item['message']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>