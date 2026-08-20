<?php
/**
 * Event Details Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$slug = getParam('slug');
if (!$slug) { redirect(SITE_URL . '/events.php'); }

$event = dbFetchOne("SELECT * FROM events WHERE slug = ?", [$slug]);
if (!$event) { http_response_code(404); include BASE_PATH . '/404.php'; exit; }

$seo = [
    'title' => $event['title'],
    'description' => truncateText($event['short_description'] ?? $event['description'] ?? '', 160),
    'type' => 'article'
];

$relatedEvents = dbFetchAll(
    "SELECT * FROM events WHERE id != ? AND status = 'Upcoming' ORDER BY event_date ASC LIMIT 3",
    [$event['id']]
);

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
$eventDate = new DateTime($event['event_date']);
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/events.php">Events</a></li>
                <li class="breadcrumb-item active"><?= e($event['title']) ?></li>
            </ol>
        </nav>
        <h1><?= e($event['title']) ?></h1>
        <p><i class="bi bi-calendar3 me-1"></i> <?= $eventDate->format('l, d F Y') ?>
           <?php if ($event['event_time']): ?> · <i class="bi bi-clock me-1"></i> <?= formatTime($event['event_time']) ?><?php endif; ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <!-- Event Image -->
                <div class="rounded-4 overflow-hidden shadow-sm mb-4">
                    <img src="<?= getUploadUrl($event['image'] ? 'events/' . $event['image'] : '', getPlaceholderImage($event['title'], 800, 400)) ?>" 
                         alt="<?= e($event['title']) ?>" class="w-100" style="max-height:450px; object-fit:cover;"
                         onerror="this.src='<?= getPlaceholderImage($event['title'], 800, 400) ?>'">
                </div>
                
                <!-- Description -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h3 class="mb-3">About This Event</h3>
                    <div class="content-body">
                        <?= !empty($event['description']) ? nl2br(e($event['description'])) : '<p>Details coming soon.</p>' ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h4 class="mb-3">Event Details</h4>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-calendar-event text-primary-custom fs-5 me-3 mt-1"></i>
                            <div><small class="text-muted d-block">Date</small><strong><?= $eventDate->format('d F Y') ?></strong></div>
                        </li>
                        <?php if ($event['event_time']): ?>
                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-clock text-primary-custom fs-5 me-3 mt-1"></i>
                            <div><small class="text-muted d-block">Time</small><strong><?= formatTime($event['event_time']) ?></strong></div>
                        </li>
                        <?php endif; ?>
                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-geo-alt text-primary-custom fs-5 me-3 mt-1"></i>
                            <div><small class="text-muted d-block">Location</small><strong><?= e($event['location'] ?? 'TBA') ?></strong></div>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-info-circle text-primary-custom fs-5 me-3 mt-1"></i>
                            <div><small class="text-muted d-block">Status</small><span class="badge <?= getStatusBadgeClass($event['status']) ?>"><?= e($event['status']) ?></span></div>
                        </li>
                    </ul>
                    
                    <?php if (!empty($event['registration_url'])): ?>
                    <a href="<?= e($event['registration_url']) ?>" target="_blank" class="btn btn-primary-custom w-100">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Register Now
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Donate -->
                <div class="card border-0 rounded-4 p-4 text-center" style="background: linear-gradient(135deg, var(--clr-primary-light), var(--clr-gold-light));">
                    <h5 class="mb-2">Support Our Events</h5>
                    <p class="small text-muted mb-3">Your donations help us organize community events and programs.</p>
                    <a href="<?= SITE_URL ?>/donate.php" class="btn btn-donate btn-sm"><i class="bi bi-heart-fill me-1"></i> Donate</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedEvents)): ?>
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">More Events</h2>
        <div class="row g-4">
            <?php foreach ($relatedEvents as $re): $rd = new DateTime($re['event_date']); ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="custom-card event-card">
                    <div class="card-img-wrapper">
                        <img src="<?= getUploadUrl($re['image'] ? 'events/' . $re['image'] : '', getPlaceholderImage($re['title'], 400, 250)) ?>" 
                             alt="<?= e($re['title']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($re['title'], 400, 250) ?>'">
                        <div class="event-date-badge"><span class="day"><?= $rd->format('d') ?></span><span class="month"><?= $rd->format('M') ?></span></div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= e($re['title']) ?></h3>
                        <p class="event-location"><i class="bi bi-geo-alt me-1"></i><?= e($re['location'] ?? '') ?></p>
                        <a href="<?= SITE_URL ?>/event-details.php?slug=<?= e($re['slug']) ?>" class="btn btn-outline-custom btn-sm mt-auto">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>
