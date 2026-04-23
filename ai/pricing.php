<?php
$pageTitle = "Pricing";
$metaDescription = "LiveKerala pricing plans";
include 'includes/header.php';

$packages = $pdo->query("SELECT * FROM packages WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<section class="page-hero">
  <div class="container">
    <div class="card">
      <h1>Pricing</h1>
      <p class="lead">Choose the right package for your business listing.</p>
    </div>
  </div>
</section>

<section class="section" style="padding-top:24px">
  <div class="container grid-3">
    <?php foreach ($packages as $package): ?>
      <div class="pricing-card">
        <h3><?= e($package['name']) ?></h3>
        <div class="price">₹<?= number_format((float)$package['price'], 0) ?> <small>/ plan</small></div>
        <p class="muted"><strong>Validity:</strong> <?= (int)$package['duration_days'] ?> days</p>
        <p class="muted"><?= e($package['description']) ?></p>
        <?php
            $features = array_filter(array_map('trim', explode("\n", (string)$package['features'])));
        ?>
        <ul>
          <?php foreach ($features as $feature): ?>
            <li><?= e($feature) ?></li>
          <?php endforeach; ?>
        </ul>
        <div style="margin-top:20px">
          <a class="btn btn-sm" href="add-listing.php">Choose <?= e($package['name']) ?></a>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (!$packages): ?>
      <p class="muted">No packages available yet.</p>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>