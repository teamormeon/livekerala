<?php
$pageTitle = "Home";
$metaDescription = "Discover trusted businesses and services across Kerala";
include 'includes/header.php';

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC, name ASC LIMIT 8")->fetchAll();
$premiumListings = $pdo->query("SELECT l.*, c.name AS category_name FROM listings l LEFT JOIN categories c ON c.id = l.category_id WHERE l.status = 1 AND l.is_premium = 1 AND (l.expiry_date IS NULL OR l.expiry_date >= CURDATE()) ORDER BY l.id DESC LIMIT 3")->fetchAll();
$featuredListings = $pdo->query("SELECT l.*, c.name AS category_name FROM listings l LEFT JOIN categories c ON c.id = l.category_id WHERE l.status = 1 AND l.is_featured = 1 AND (l.expiry_date IS NULL OR l.expiry_date >= CURDATE()) ORDER BY l.id DESC LIMIT 3")->fetchAll();
?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow">Kerala's Local Directory</span>
      <h1>Discover trusted businesses, services and destinations across Kerala</h1>
      <p class="lead">LiveKerala helps people find businesses, resorts, healthcare providers, educational institutions, local services and opportunities across Kerala in one place.</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px">
        <a class="btn" href="categories.php">Explore Categories</a>
        <a class="btn btn-outline" href="add-listing.php">Add Your Listing</a>
      </div>
    </div>
    <div class="hero-card">
      <h3>Find the right business faster</h3>
      <p class="muted">Browse by category or location.</p>
      <form class="search-grid" action="categories.php" method="get">
        <input type="text" name="q" placeholder="What are you looking for?">
        <select name="category_id">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>"><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="location" placeholder="Location">
        <button class="btn" type="submit">Search</button>
      </form>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Explore Top Categories</h2>
        <p class="muted">Browse businesses and services by category and find what you need quickly.</p>
      </div>
      <a class="btn btn-outline" href="categories.php">View All Categories</a>
    </div>
    <div class="grid-4">
      <?php foreach ($categories as $category): ?>
      <div class="category-card card">
        <div class="category-icon"><?= e($category['icon']) ?></div>
        <h3><?= e($category['name']) ?></h3>
        <p class="muted"><?= e($category['description']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Premium Listings</h2>
        <p class="muted">Priority visibility for businesses that want stronger reach and better homepage exposure.</p>
      </div>
    </div>
    <div class="grid-3">
      <?php foreach ($premiumListings as $item): ?>
      <div class="listing-card">
        <span class="badge badge-premium">Premium</span>
        <h3><?= e($item['title']) ?></h3>
        <p class="listing-meta"><?= e($item['location']) ?> • <?= e($item['category_name']) ?></p>
        <p class="muted"><?= e($item['short_description']) ?></p>
      </div>
      <?php endforeach; ?>
      <?php if (!$premiumListings): ?><p class="muted">No premium listings yet.</p><?php endif; ?>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Featured Listings</h2>
        <p class="muted">Highlighted businesses and services selected for stronger platform visibility.</p>
      </div>
    </div>
    <div class="grid-3">
      <?php foreach ($featuredListings as $item): ?>
      <div class="listing-card">
        <span class="badge badge-featured">Featured</span>
        <h3><?= e($item['title']) ?></h3>
        <p class="listing-meta"><?= e($item['location']) ?> • <?= e($item['category_name']) ?></p>
        <p class="muted"><?= e($item['short_description']) ?></p>
      </div>
      <?php endforeach; ?>
      <?php if (!$featuredListings): ?><p class="muted">No featured listings yet.</p><?php endif; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-3">
      <div class="feature-box">
        <h3>Why businesses choose LiveKerala</h3>
        <ul class="check-list">
          <li>Stronger local visibility across Kerala</li>
          <li>Structured category-based discovery</li>
          <li>Premium and featured listing options</li>
          <li>Simple listing management</li>
        </ul>
      </div>
      <div class="feature-box">
        <h3>About LiveKerala</h3>
        <p class="muted">LiveKerala is a Kerala-focused digital directory built to connect people with trusted local businesses, destinations and services across the state.</p>
        <a class="btn btn-outline btn-sm" href="about.php">Learn More</a>
      </div>
      <div class="feature-box">
        <h3>Flexible listing plans</h3>
        <p class="muted">Choose Free, Featured or Premium plans based on the visibility and growth you want for your business.</p>
        <a class="btn btn-outline btn-sm" href="pricing.php">View Pricing</a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta">
      <h2>Own a business in Kerala? List it on LiveKerala today</h2>
      <p>Create your business listing and reach more customers across Kerala through a clear, searchable online presence.</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px">
        <a class="btn btn-light" href="add-listing.php">Add Listing Now</a>
        <a class="btn btn-outline" style="color:#fff;border-color:#fff" href="contact.php">Contact Us</a>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>