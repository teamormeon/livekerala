<?php
$pageTitle = "Categories";
$metaDescription = "Explore top categories on LiveKerala";
include 'includes/header.php';

$where = ["l.status = 1", "(l.expiry_date IS NULL OR l.expiry_date >= CURDATE())"];
$params = [];

if (!empty($_GET['category_id'])) {
    $where[] = "l.category_id = ?";
    $params[] = (int)$_GET['category_id'];
}
if (!empty($_GET['q'])) {
    $where[] = "(l.title LIKE ? OR l.short_description LIKE ?)";
    $params[] = '%' . $_GET['q'] . '%';
    $params[] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['location'])) {
    $where[] = "l.location LIKE ?";
    $params[] = '%' . $_GET['location'] . '%';
}

$sql = "SELECT l.*, c.name AS category_name, p.name AS package_name FROM listings l LEFT JOIN categories c ON c.id = l.category_id LEFT JOIN packages p ON p.id = l.package_id WHERE " . implode(' AND ', $where) . " ORDER BY l.is_premium DESC, l.is_featured DESC, l.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC, name ASC")->fetchAll();
?>
<section class="page-hero">
  <div class="container">
    <div class="card">
      <h1>Categories & Listings</h1>
      <p class="lead">Explore Kerala businesses and services across high-intent categories.</p>
    </div>
  </div>
</section>
<section class="section" style="padding-top:24px">
  <div class="container">
    <form class="card" style="padding:18px; margin-bottom:24px" method="get">
      <div class="search-grid">
        <input type="text" name="q" placeholder="Search keyword" value="<?= e($_GET['q'] ?? '') ?>">
        <select name="category_id">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?= (int)$category['id'] ?>" <?= (!empty($_GET['category_id']) && (int)$_GET['category_id'] === (int)$category['id']) ? 'selected' : '' ?>><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="location" placeholder="Location" value="<?= e($_GET['location'] ?? '') ?>">
        <button class="btn" type="submit">Filter</button>
      </div>
    </form>

    <div class="grid-3">
      <?php foreach ($listings as $item): ?>
      <div class="listing-card">
        <?php if ($item['is_premium']): ?><span class="badge badge-premium">Premium</span><?php elseif ($item['is_featured']): ?><span class="badge badge-featured">Featured</span><?php endif; ?>
        <h3><?= e($item['title']) ?></h3>
        <p class="listing-meta"><?= e($item['location']) ?> • <?= e($item['category_name']) ?> • <?= e($item['package_name']) ?></p>
        <p class="muted"><?= e($item['short_description']) ?></p>
        <p class="muted"><strong>Phone:</strong> <?= e($item['phone']) ?></p>
        <p class="muted"><strong>Valid till:</strong> <?= e($item['expiry_date']) ?></p>
      </div>
      <?php endforeach; ?>
      <?php if (!$listings): ?><p class="muted">No listings found.</p><?php endif; ?>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>