<?php
/**
 * Terms and Conditions
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$seo = [
    'title' => 'Terms & Conditions',
    'description' => 'Terms and conditions governing donations, adoptions, and use of Kamadhenu Goushala services.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Terms &amp; Conditions</li>
            </ol>
        </nav>
        <h1>Terms &amp; Conditions</h1>
        <p>Guidelines regarding donations, adoptions, and website usage.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 900px;">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
            <h3 class="mb-3">1. Seva Contributions &amp; Donations</h3>
            <p>All contributions made to Kamadhenu Goushala are voluntary religious and charitable offerings dedicated to the maintenance, nourishment, and medical care of indigenous cows in our sanctuary.</p>

            <h3 class="mb-3 mt-4">2. Cow Adoption Program</h3>
            <p>The "Adopt a Cow" program constitutes a symbolic sponsorship and monthly guardian support model. The adopted cow remains under the physical care and sanctuary of Kamadhenu Goushala. Guardians are warmly welcome to visit their adopted cow during visiting hours.</p>

            <h3 class="mb-3 mt-4">3. Refund &amp; Cancellation Policy</h3>
            <p>As donations and seva offerings are immediately allocated towards cow fodder and daily care operations, contributions are generally non-refundable. In the event of an accidental duplicate transaction, please notify us within 7 days with your transaction ID for assistance.</p>

            <h3 class="mb-3 mt-4">4. Intellectual Property</h3>
            <p>All photography, branding, logo assets, and articles on this website belong to Kamadhenu Goushala and may not be reproduced without prior written permission.</p>

            <h3 class="mb-3 mt-4">5. Sanctuary Visits</h3>
            <p>Devotees and visitors are welcome to visit our sanctuary at Kavadi, Virajpet Taluk, Kodagu. We request all visitors to maintain peaceful conduct and follow caretaker guidance around the cows.</p>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
