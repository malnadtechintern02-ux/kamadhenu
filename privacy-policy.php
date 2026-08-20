<?php
/**
 * Privacy Policy
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$seo = [
    'title' => 'Privacy Policy',
    'description' => 'Privacy policy of Kamadhenu Goushala regarding donor information, data security, and communication.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Privacy Policy</li>
            </ol>
        </nav>
        <h1>Privacy Policy</h1>
        <p>Our commitment to transparency and donor confidentiality.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 900px;">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
            <h3 class="mb-3">1. Information We Collect</h3>
            <p>Kamadhenu Goushala respects your privacy. When you donate, adopt a cow, or contact us through our website, we may collect personal details such as your name, email address, phone number, PAN (for 80G tax receipts), and postal address.</p>

            <h3 class="mb-3 mt-4">2. How We Use Your Information</h3>
            <p>The information collected is used solely for the following purposes:</p>
            <ul>
                <li>Generating and issuing official donation receipts and 80G certificates.</li>
                <li>Sending updates regarding adopted cows and Gau Seva programs.</li>
                <li>Responding to inquiries or prayer requests submitted via our contact form.</li>
                <li>Internal accounting and regulatory compliance.</li>
            </ul>

            <h3 class="mb-3 mt-4">3. Data Protection &amp; Confidentiality</h3>
            <p>We do NOT sell, rent, or trade your personal data to any third parties. All transactions and communication are maintained strictly confidential within the Kamadhenu Goushala management team.</p>

            <h3 class="mb-3 mt-4">4. Online Payments &amp; Security</h3>
            <p>Online payment transactions are processed through encrypted bank gateways and verified UPI protocols. We do not store credit/debit card numbers or bank account passwords on our servers.</p>

            <h3 class="mb-3 mt-4">5. Contact Us</h3>
            <p>If you have any questions regarding our privacy practices, please contact us at:</p>
            <p><strong>Email:</strong> <?= e(getSetting('email', '[EMAIL ADDRESS]')) ?><br>
            <strong>Phone:</strong> <?= e(getSetting('phone', '[PHONE NUMBER]')) ?><br>
            <strong>Address:</strong> <?= e(getSetting('address', '[GOUSHALA ADDRESS]')) ?></p>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
