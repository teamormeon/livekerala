</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h3>LiveKerala</h3>
            <p>Kerala's local business directory to discover trusted services, destinations, and businesses.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="<?= base_url('index.php') ?>">Home</a></li>
                <li><a href="<?= base_url('categories.php') ?>">Categories</a></li>
                <li><a href="<?= base_url('about.php') ?>">About</a></li>
                <li><a href="<?= base_url('pricing.php') ?>">Pricing</a></li>
                <li><a href="<?= base_url('why-livekerala.php') ?>">Why Live Kerala</a></li>
                <li><a href="<?= base_url('contact.php') ?>">Contact</a></li>
                <li><a href="<?= base_url('add-listing.php') ?>">Add Listing</a></li>
            </ul>
        </div>
        <div>
            <h4>Contact</h4>
            <p>Email: <?= e(CONTACT_RECEIVER_EMAIL) ?></p>
            <p>Location: Kerala, India</p>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>© <?= date('Y') ?> LiveKerala. All rights reserved.</p>
    </div>
</footer>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
