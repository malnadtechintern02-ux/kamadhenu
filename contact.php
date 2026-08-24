<?php
/**
 * Contact Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/validation.php';

$seo = [
    'title' => 'Contact Us',
    'description' => 'Get in touch with Kamadhenu Goushala. Visit us, call, email, or send a message. We are located in Kavadi, Virajpet Taluk, Kodagu, Karnataka.',
];

// Handle form submission
$errors = [];
$formData = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

if (isPost()) {
    requireCsrfToken();
    
    $formData = [
        'name'    => getParam('name', '', 'POST'),
        'email'   => getParam('email', '', 'POST'),
        'phone'   => getParam('phone', '', 'POST'),
        'subject' => getParam('subject', '', 'POST'),
        'message' => getParam('message', '', 'POST'),
    ];
    
    $validator = new Validator($formData);
    $validator->required('name', 'Name')
              ->required('email', 'Email')
              ->email('email')
              ->required('subject', 'Subject')
              ->required('message', 'Message')
              ->minLength('message', 10, 'Message')
              ->maxLength('message', 2000, 'Message')
              ->phone('phone');
    
    if ($validator->passes()) {
        $inserted = dbInsert('contact_messages', [
            'name'    => $formData['name'],
            'email'   => $formData['email'],
            'phone'   => $formData['phone'],
            'subject' => $formData['subject'],
            'message' => $formData['message'],
        ]);
        
        if ($inserted) {
            setFlash('success', 'Thank you for your message! We will get back to you soon.');
            redirect(SITE_URL . '/contact.php');
        } else {
            setFlash('error', 'Something went wrong. Please try again later.');
        }
    } else {
        $errors = $validator->getErrors();
    }
}

$phone = getSetting('phone');
if (empty($phone) || $phone === '[PHONE NUMBER]') {
    $phone = '6763887630';
}
$email = getSetting('email');
if (empty($email) || $email === '[EMAIL ADDRESS]') {
    $email = 'sharath234@gmail.com';
}
$address = getSetting('address', '[GOUSHALA ADDRESS]');
$whatsapp = getSetting('whatsapp', '');
$mapsUrl = getSetting('google_maps_url', '');

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Reach out for any questions about Gau Seva.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-5 animate-on-scroll fade-left">
                <div class="card border-0 rounded-4 p-4 p-lg-5 mb-4 shadow-sm" style="background-color: var(--clr-brown); color: var(--clr-gold);">
                    <h3 class="mb-4 text-gold font-heading">Contact Us</h3>
                    
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-4 fs-5">
                        <li class="d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill me-3 fs-4"></i>
                            <span class="text-white opacity-75"><?= e($address) ?></span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-telephone-fill me-3 fs-4"></i>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="text-white opacity-75 text-decoration-none hover-gold"><?= e($phone) ?></a>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-envelope-fill me-3 fs-4"></i>
                            <a href="mailto:<?= e($email) ?>" class="text-white opacity-75 text-decoration-none hover-gold"><?= e($email) ?></a>
                        </li>
                        <?php if ($whatsapp): 
                            $whatsappDefaultMsg = getSetting('whatsapp_default_msg', '🙏 Namaste, I would like to support Gau Seva.');
                        ?>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-whatsapp me-3 fs-4"></i>
                            <a href="<?= e(getWhatsAppLink($whatsappDefaultMsg)) ?>" target="_blank" class="text-white opacity-75 text-decoration-none hover-gold">WhatsApp Chat</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Map -->
                <?php if ($mapsUrl): ?>
                <div class="mt-4 rounded-4 overflow-hidden shadow-sm">
                    <iframe src="<?= e($mapsUrl) ?>" width="100%" height="250" style="border:0;" allowfullscreen loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade" title="Goushala Location"></iframe>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7 animate-on-scroll fade-right">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h3 class="mb-3">Send a Message</h3>
                    <p class="text-muted mb-4">Fill out the form below and we'll respond as soon as possible.</p>
                    
                    <form method="POST" action="" class="needs-validation" novalidate id="contactForm">
                        <?= csrfField() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= e($formData['name']) ?>" required>
                                <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                       id="email" name="email" value="<?= e($formData['email']) ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                       id="phone" name="phone" value="<?= e($formData['phone']) ?>">
                                <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" 
                                       id="subject" name="subject" value="<?= e($formData['subject']) ?>" required>
                                <?php if (isset($errors['subject'])): ?>
                                <div class="invalid-feedback"><?= e($errors['subject']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" 
                                          id="message" name="message" rows="5" required><?= e($formData['message']) ?></textarea>
                                <?php if (isset($errors['message'])): ?>
                                <div class="invalid-feedback"><?= e($errors['message']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom btn-lg">
                                    <i class="bi bi-send me-1"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
