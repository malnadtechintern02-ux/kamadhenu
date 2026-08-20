<?php
/**
 * SEO Helper Functions
 * Kamadenu Goushala
 */

/**
 * Generate SEO meta tags
 */
function renderSeoMeta(array $seo = []): string {
    $siteName = getSetting('site_name', 'Kamadenu Goushala');
    
    $title       = e($seo['title'] ?? $siteName);
    $description = e($seo['description'] ?? getSetting('site_description', ''));
    $keywords    = e($seo['keywords'] ?? 'goushala, gau seva, cow protection, indigenous breeds, donate, adopt a cow');
    $canonical   = e($seo['canonical'] ?? getCurrentUrl());
    $image       = e($seo['image'] ?? ASSETS_URL . '/images/og-default.jpg');
    $type        = e($seo['type'] ?? 'website');
    
    $fullTitle = isset($seo['title']) ? "{$title} | {$siteName}" : $siteName;
    
    $html = <<<HTML
    <title>{$fullTitle}</title>
    <meta name="description" content="{$description}">
    <meta name="keywords" content="{$keywords}">
    <link rel="canonical" href="{$canonical}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{$fullTitle}">
    <meta property="og:description" content="{$description}">
    <meta property="og:image" content="{$image}">
    <meta property="og:url" content="{$canonical}">
    <meta property="og:type" content="{$type}">
    <meta property="og:site_name" content="{$siteName}">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{$fullTitle}">
    <meta name="twitter:description" content="{$description}">
    <meta name="twitter:image" content="{$image}">
HTML;
    
    return $html;
}

/**
 * Get current full URL
 */
function getCurrentUrl(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');
}

/**
 * Generate Schema.org JSON-LD for organization
 */
function renderOrganizationSchema(): string {
    $siteName = getSetting('site_name', 'Kamadenu Goushala');
    $phone = getSetting('phone', '');
    $email = getSetting('email', '');
    $address = getSetting('address', '');
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'NGO',
        'name' => $siteName,
        'description' => getSetting('site_description', ''),
        'url' => SITE_URL,
        'telephone' => $phone,
        'email' => $email,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $address,
            'addressCountry' => 'IN'
        ],
        'sameAs' => array_filter([
            getSetting('facebook_url'),
            getSetting('instagram_url'),
            getSetting('youtube_url'),
            getSetting('twitter_url')
        ])
    ];
    
    $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    return '<script type="application/ld+json">' . $json . '</script>';
}

/**
 * Generate breadcrumb Schema.org
 */
function renderBreadcrumbSchema(array $items): string {
    $listItems = [];
    foreach ($items as $i => $item) {
        $listItems[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'] ?? ''
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $listItems
    ];
    
    $json = json_encode($schema, JSON_UNESCAPED_SLASHES);
    return '<script type="application/ld+json">' . $json . '</script>';
}
