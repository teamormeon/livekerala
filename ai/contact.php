<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        set_flash('error', 'Name, email and message are required.');
        redirect('contact.php');
    }

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $phone, $subject, $message]);

    send_contact_email($name, $email, $phone, $subject, $message);
    set_flash('success', 'Thank you. Your message has been sent successfully.');
    redirect('contact.php');
}

$pageTitle = "Contact";
$metaDescription = "Contact LiveKerala";
include 'includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <div class="card">
      <h1>Contact Us</h1>
      <p class="lead">Have questions or need help with your listing? Reach out to us.</p>
    </div>
  </div>
</section>
<section class="section" style="padding-top:24px">
  <div class="container contact-grid">
    <div class="contact-card">
      <h3>Get in touch</h3>
      <p class="muted">Email: <?= e(CONTACT_RECEIVER_EMAIL) ?></p>
      <p class="muted">Location: Kerala, India</p>
      <div class="notice">This form saves messages to MySQL and also attempts to send an email using PHP mail().</div>
    </div>
    <div class="form-card" style="padding:22px">
      <form method="post">
        <div class="form-row">
          <input type="text" name="name" placeholder="Your Name" required>
          <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-row" style="margin-top:16px">
          <input type="text" name="phone" placeholder="Phone Number">
          <input type="text" name="subject" placeholder="Subject">
        </div>
        <div style="margin-top:16px">
          <textarea name="message" placeholder="Write your message" required></textarea>
        </div>
        <div style="margin-top:16px">
          <button class="btn" type="submit">Send Message</button>
        </div>
      </form>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>