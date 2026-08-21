<?php
/**
 * HTML Header / Head Section
 * Kamadenu Goushala
 * 
 * Variables to set before including:
 *   $seo   - Array with SEO data (title, description, etc.)
 *   $extraCss - Additional CSS files (optional)
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
    require_once BASE_PATH . '/config/config.php';
}

require_once BASE_PATH . '/includes/seo.php';

$seo = $seo ?? [];
$extraCss = $extraCss ?? [];
$siteName = getSetting('site_name', 'Kamadenu Goushala');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?= renderSeoMeta($seo) ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= ASSETS_URL ?>/images/logo-icon.png">
    <link rel="apple-touch-icon" href="<?= ASSETS_URL ?>/images/logo-icon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    
    <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?= e($css) ?>">
    <?php endforeach; ?>
    
    <!-- Organization Schema -->
    <?= renderOrganizationSchema() ?>
</head>
<body>
