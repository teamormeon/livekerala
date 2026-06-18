<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $owner_name = trim($_POST['owner_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $package_id = (int)($_POST['package_id'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');

    if (!$title || !$email || !$phone || !$category_id || !$package_id || !$location || !$short_description) {
        set_flash('error', 'Please fill all required fields.');
        redirect('add-listing.php');
    }

    $slug = slugify($title);
    $check = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE slug = ?");
    $check->execute([$slug]);
    if ((int)$check->fetchColumn() > 0) {
        $slug .= '-' . time();
    }

    $pkgStmt = $pdo->prepare("SELECT * FROM packages WHERE id = ? AND status = 1 LIMIT 1");
    $pkgStmt->execute([$package_id]);
    $package = $pkgStmt->fetch();

    if (!$package) {
        set_flash('error', 'Selected package is invalid.');
        redirect('add-listing.php');
    }

    $duration = (int)($package['duration_days'] ?? 30);
    $expiry_date = date('Y-m-d', strtotime("+{$duration} days"));

    $stmt = $pdo->prepare("INSERT INTO listings (category_id, package_id, title, slug, owner_name, email, phone, location, short_description, expiry_date, is_featured, is_premium, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, NOW())");
    $stmt->execute([$category_id, $package_id, $title, $slug, $owner_name, $email, $phone, $location, $short_description, $expiry_date]);

    set_flash('success', 'Listing submitted successfully. It is awaiting admin approval.');
    redirect('add-listing.php');
}

$pageTitle = "Add Listing";
$metaDescription = "Add your business listing to LiveKerala";
include 'includes/header.php';

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC, name ASC")->fetchAll();
$packages = $pdo->query("SELECT * FROM packages WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<section class="page-hero">
  <div class="container">
    <div class="card">
      <h1>Add Listing</h1>
      <p class="lead">Submit your business details. Choose a package with listing validity.</p>
    </div>
  </div>
</section>
<section class="section" style="padding-top:24px">
  <div class="container">
    <div class="form-card" style="padding:24px">
      <form method="post">
        <div class="form-row">
          <input type="text" name="title" placeholder="Business Name" required>
          <input type="text" name="owner_name" placeholder="Owner Name">
        </div>
        <div class="form-row" style="margin-top:16px">
          <input type="email" name="email" placeholder="Business Email" required>
          <input type="text" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="form-row" style="margin-top:16px">
          <select name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>"><?= e($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <select name="package_id" required>
            <option value="">Select Package</option>
            <?php foreach ($packages as $pkg): ?>
                <option value="<?= (int)$pkg['id'] ?>"><?= e($pkg['name']) ?> (<?= (int)$pkg['duration_days'] ?> days)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row" style="margin-top:16px">
          <input type="text" name="location" placeholder="Location" required>
          <input type="text" value="Listing validity will be applied from package" readonly>
        </div>
        <div style="margin-top:16px">
          <textarea name="short_description" placeholder="Business Description" required></textarea>
        </div>
        <div style="margin-top:16px">
          <button class="btn" type="submit">Submit Listing Request</button>
        </div>
      </form>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>