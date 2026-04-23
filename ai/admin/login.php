<?php
require_once '../includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        redirect('dashboard.php');
    }

    set_flash('error', 'Invalid login credentials.');
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | LiveKerala</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-login-wrap">
    <div class="form-card" style="padding:28px; max-width:420px; width:100%">
        <h2>Admin Login</h2>
        <?php if ($error = get_flash('error')): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <div style="margin-top:16px"><input type="email" name="email" placeholder="Admin Email" required></div>
            <div style="margin-top:16px"><input type="password" name="password" placeholder="Password" required></div>
            <div style="margin-top:16px"><button class="btn" type="submit" style="width:100%">Login</button></div>
        </form>
        <p class="muted" style="margin-top:14px">Default email: admin@livekerala.com</p>
        <p class="muted">Default password: password</p>
    </div>
</div>
</body>
</html>