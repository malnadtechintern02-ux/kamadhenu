<?php
/**
 * Dynamic XML Sitemap Generator
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

header("Content-Type: application/xml; charset=utf-8");

$cows = dbFetchAll("SELECT id, updated_at FROM cows ORDER BY updated_at DESC");
$breeds = dbFetchAll("SELECT slug FROM breeds WHERE is_active = 1");
$news = dbFetchAll("SELECT slug, published_date FROM news WHERE status = 'Published' ORDER BY published_date DESC");
$events = dbFetchAll("SELECT slug, event_date FROM events WHERE status != 'Cancelled' ORDER BY event_date DESC");
$products = dbFetchAll("SELECT slug FROM products WHERE is_active = 1");

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?= SITE_URL ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/about.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/cows.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/breeds.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/gau-seva.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/feed-a-cow.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/adopt-a-cow.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/donate.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.95</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/events.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/news.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/gallery.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/products.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/contact.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/privacy-policy.php</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/terms.php</loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>

    <!-- Individual Cows -->
    <?php foreach ($cows as $c): ?>
    <url>
        <loc><?= SITE_URL ?>/cow-details.php?id=<?= $c['id'] ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- Individual Breeds -->
    <?php foreach ($breeds as $b): ?>
    <url>
        <loc><?= SITE_URL ?>/breed-details.php?slug=<?= e($b['slug']) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- News Articles -->
    <?php foreach ($news as $n): ?>
    <url>
        <loc><?= SITE_URL ?>/news-details.php?slug=<?= e($n['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($n['published_date'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- Events -->
    <?php foreach ($events as $e): ?>
    <url>
        <loc><?= SITE_URL ?>/event-details.php?slug=<?= e($e['slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- Products -->
    <?php foreach ($products as $p): ?>
    <url>
        <loc><?= SITE_URL ?>/product-details.php?slug=<?= e($p['slug']) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>
