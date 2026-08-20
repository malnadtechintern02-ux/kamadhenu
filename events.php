<?php
/**
 * Events Listing Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/pagination.php';

$seo = [
    'title' => 'Events',
    'description' => 'Upcoming and past events at Kamadhenu Goushala. Join us for Gau Seva programs, health camps, and community gatherings.',
];

$tab = getParam('tab', 'upcoming');

// Upcoming events
$upcomingEvents = dbFetchAll(
    "SELECT * FROM events WHERE (status = 'Upcoming' OR status = 'Ongoing') AND event_date >= CURDATE() ORDER BY event_date ASC"
);

// Past events
$pastEvents = dbFetchAll(
    "SELECT * FROM events WHERE status = 'Completed' OR event_date < CURDATE() ORDER BY event_date DESC LIMIT 12"
);

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Events</li>
            </ol>
        </nav>
        <h1>Events & Programs</h1>
        <p>Join us in our sacred events dedicated to Gau Seva and community service.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Tabs -->
        <ul class="nav nav-pills justify-content-center mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $tab === 'upcoming' ? 'active' : '' ?> rounded-pill px-4" data-bs-toggle="pill" 
                        data-bs-target="#upcoming" type="button" role="tab">
                    <i class="bi bi-calendar-event me-1"></i> Upcoming (<?= count($upcomingEvents) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $tab === 'past' ? 'active' : '' ?> rounded-pill px-4" data-bs-toggle="pill" 
                        data-bs-target="#past" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i> Past Events (<?= count($pastEvents) ?>)
                </button>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Upcoming Events -->
            <div class="tab-pane fade <?= $tab === 'upcoming' ? 'show active' : '' ?>" id="upcoming" role="tabpanel">
                <?php if (empty($upcomingEvents)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">No Upcoming Events</h3>
                    <p class="text-muted">Stay tuned for future events and programs.</p>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($upcomingEvents as $event): 
                        $eventDate = new DateTime($event['event_date']);
                    ?>
                    <div class="col-lg-4 col-md-6 animate-on-scroll">
                        <div class="custom-card event-card">
                            <div class="card-img-wrapper">
                                <img src="<?= getUploadUrl($event['image'] ? 'events/' . $event['image'] : '', getPlaceholderImage($event['title'], 400, 250)) ?>" 
                                     alt="<?= e($event['title']) ?>" loading="lazy"
                                     onerror="this.src='<?= getPlaceholderImage($event['title'], 400, 250) ?>'">
                                <div class="event-date-badge">
                                    <span class="day"><?= $eventDate->format('d') ?></span>
                                    <span class="month"><?= $eventDate->format('M') ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-success mb-2"><?= e($event['status']) ?></span>
                                <h3 class="card-title"><?= e($event['title']) ?></h3>
                                <p class="event-location mb-2"><i class="bi bi-geo-alt me-1"></i><?= e($event['location'] ?? '') ?></p>
                                <?php if ($event['event_time']): ?>
                                <p class="small text-muted mb-2"><i class="bi bi-clock me-1"></i><?= formatTime($event['event_time']) ?></p>
                                <?php endif; ?>
                                <p class="card-text"><?= e(truncateText($event['short_description'] ?? $event['description'] ?? '', 100)) ?></p>
                                <a href="<?= SITE_URL ?>/event-details.php?slug=<?= e($event['slug']) ?>" class="btn btn-primary-custom btn-sm mt-auto">
                                    View Details <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Past Events -->
            <div class="tab-pane fade <?= $tab === 'past' ? 'show active' : '' ?>" id="past" role="tabpanel">
                <?php if (empty($pastEvents)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">No Past Events Yet</h3>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($pastEvents as $event):
                        $eventDate = new DateTime($event['event_date']);
                    ?>
                    <div class="col-lg-4 col-md-6 animate-on-scroll">
                        <div class="custom-card event-card" style="opacity:0.85;">
                            <div class="card-img-wrapper">
                                <img src="<?= getUploadUrl($event['image'] ? 'events/' . $event['image'] : '', getPlaceholderImage($event['title'], 400, 250)) ?>" 
                                     alt="<?= e($event['title']) ?>" loading="lazy"
                                     onerror="this.src='<?= getPlaceholderImage($event['title'], 400, 250) ?>'">
                                <div class="event-date-badge" style="background:var(--clr-text-muted);">
                                    <span class="day"><?= $eventDate->format('d') ?></span>
                                    <span class="month"><?= $eventDate->format('M') ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2">Completed</span>
                                <h3 class="card-title"><?= e($event['title']) ?></h3>
                                <p class="event-location"><i class="bi bi-geo-alt me-1"></i><?= e($event['location'] ?? '') ?></p>
                                <p class="card-text"><?= e(truncateText($event['short_description'] ?? '', 100)) ?></p>
                                <a href="<?= SITE_URL ?>/event-details.php?slug=<?= e($event['slug']) ?>" class="btn btn-outline-custom btn-sm mt-auto">Read More</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
