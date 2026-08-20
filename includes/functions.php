<?php
/**
 * Core Utility Functions
 * Kamadenu Goushala
 */

/**
 * Escape output for HTML to prevent XSS
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate a URL-safe slug from a string
 */
function generateSlug(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Generate a unique reference number
 */
function generateReferenceNumber(string $prefix = 'KG'): string {
    return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

/**
 * Calculate age from date of birth
 */
function calculateAge(?string $dob): string {
    if (empty($dob)) return 'Unknown';
    
    try {
        $birth = new DateTime($dob);
        $now = new DateTime();
        $diff = $now->diff($birth);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        } else {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }
    } catch (Exception $e) {
        return 'Unknown';
    }
}

/**
 * Format currency in Indian Rupee format
 */
function formatCurrency(float $amount): string {
    if ($amount >= 100000) {
        return '₹' . number_format($amount / 100000, 1) . 'L';
    }
    return '₹' . number_format($amount, 0, '.', ',');
}

/**
 * Format date to readable string
 */
function formatDate(?string $date, string $format = 'd M Y'): string {
    if (empty($date)) return '';
    try {
        return (new DateTime($date))->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Format time to 12-hour format
 */
function formatTime(?string $time): string {
    if (empty($time)) return '';
    try {
        return (new DateTime($time))->format('g:i A');
    } catch (Exception $e) {
        return $time;
    }
}

/**
 * Truncate text to a specific length
 */
function truncateText(string $text, int $length = 150, string $suffix = '...'): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get status badge CSS class
 */
function getStatusBadgeClass(string $status): string {
    $map = [
        'Available'          => 'bg-success',
        'Adopted'            => 'bg-primary',
        'Permanent Resident' => 'bg-info',
        'Medical Care'       => 'bg-warning text-dark',
        'Rescued'            => 'bg-secondary',
        'Upcoming'           => 'bg-primary',
        'Ongoing'            => 'bg-success',
        'Completed'          => 'bg-secondary',
        'Cancelled'          => 'bg-danger',
        'Draft'              => 'bg-secondary',
        'Published'          => 'bg-success',
        'Archived'           => 'bg-dark',
        'Pending'            => 'bg-warning text-dark',
        'Success'            => 'bg-success',
        'Failed'             => 'bg-danger',
        'Refunded'           => 'bg-info',
        'In Stock'           => 'bg-success',
        'Out of Stock'       => 'bg-danger',
        'Pre-Order'          => 'bg-warning text-dark',
    ];
    
    return $map[$status] ?? 'bg-secondary';
}

/**
 * Get the upload URL for a given file path with file existence check
 */
function getUploadUrl(?string $path, string $placeholder = ''): string {
    if (empty($path)) {
        return !empty($placeholder) ? $placeholder : ASSETS_URL . '/images/hero/hero-bg.jpg';
    }
    
    // Already a full URL
    if (str_starts_with($path, 'http')) {
        return $path;
    }
    
    $cleanPath = ltrim($path, '/');
    if (file_exists(UPLOADS_PATH . '/' . $cleanPath)) {
        return UPLOADS_URL . '/' . $cleanPath;
    }
    
    return !empty($placeholder) ? $placeholder : UPLOADS_URL . '/cows/gir-cow-1.jpg';
}

/**
 * Get placeholder image SVG data URI
 */
function getPlaceholderImage(string $text = '', int $width = 400, int $height = 300): string {
    $text = e($text ?: 'Goushala');
    return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}'%3E%3Crect fill='%23E8F5E9' width='{$width}' height='{$height}'/%3E%3Ctext fill='%232E7D32' font-family='Inter,sans-serif' font-weight='bold' font-size='18' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3E{$text}%3C/text%3E%3C/svg%3E";
}

/**
 * Get cow photo URL with fallback to generated cow images
 */
function getCowPhotoUrl(?string $photo, string $name = 'Cow'): string {
    if (!empty($photo) && file_exists(UPLOADS_PATH . '/cows/' . $photo)) {
        return UPLOADS_URL . '/cows/' . $photo;
    }
    if (file_exists(UPLOADS_PATH . '/cows/gir-cow-1.jpg')) {
        return UPLOADS_URL . '/cows/gir-cow-1.jpg';
    }
    return getPlaceholderImage($name, 400, 400);
}

/**
 * Execute a database query and return all results
 */
function dbFetchAll(string $sql, array $params = []): array {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('DB Query Error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Execute a database query and return one result
 */
function dbFetchOne(string $sql, array $params = []): ?array {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    } catch (PDOException $e) {
        error_log('DB Query Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Execute a database query and return the count
 */
function dbCount(string $table, string $where = '1=1', array $params = []): int {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM `{$table}` WHERE {$where}");
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)($result['count'] ?? 0);
    } catch (PDOException $e) {
        error_log('DB Count Error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Insert a record and return the ID
 */
function dbInsert(string $table, array $data): int {
    try {
        $db = getDB();
        $columns = implode(', ', array_map(fn($col) => "`{$col}`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $stmt = $db->prepare("INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        
        return (int)$db->lastInsertId();
    } catch (PDOException $e) {
        error_log('DB Insert Error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Update records in a table
 */
function dbUpdate(string $table, array $data, string $where, array $whereParams = []): bool {
    try {
        $db = getDB();
        $setParts = array_map(fn($col) => "`{$col}` = ?", array_keys($data));
        $setString = implode(', ', $setParts);
        
        $stmt = $db->prepare("UPDATE `{$table}` SET {$setString} WHERE {$where}");
        $stmt->execute(array_merge(array_values($data), $whereParams));
        
        return true;
    } catch (PDOException $e) {
        error_log('DB Update Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete records from a table
 */
function dbDelete(string $table, string $where, array $params = []): bool {
    try {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM `{$table}` WHERE {$where}");
        $stmt->execute($params);
        return true;
    } catch (PDOException $e) {
        error_log('DB Delete Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get current page number from request
 */
function getCurrentPage(): int {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    return max(1, $page);
}

/**
 * Build pagination data
 */
function buildPagination(int $totalItems, int $perPage, int $currentPage): array {
    $totalPages = max(1, (int)ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_items'  => $totalItems,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
        'has_prev'     => $currentPage > 1,
        'has_next'     => $currentPage < $totalPages,
    ];
}

/**
 * Redirect to a URL
 */
function redirect(string $url): void {
    header("Location: {$url}");
    exit;
}

/**
 * Check if request is POST
 */
function isPost(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Get request parameter with sanitization
 */
function getParam(string $key, string $default = '', string $method = 'GET'): string {
    $source = $method === 'POST' ? $_POST : $_GET;
    $value = $source[$key] ?? $default;
    return trim((string)$value);
}

/**
 * Get an integer parameter
 */
function getIntParam(string $key, int|string $default = 0, string $method = 'GET'): int {
    if (is_string($default) && in_array(strtoupper($default), ['GET', 'POST', 'REQUEST'], true)) {
        $method = strtoupper($default);
        $default = 0;
    }
    return (int)getParam($key, (string)$default, $method);
}

/**
 * Sanitize a string for safe output
 */
function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if the current URL matches a given path
 */
function isCurrentPage(string $path): bool {
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    return str_contains($currentPath, $path);
}

/**
 * Generate WhatsApp link
 */
function getWhatsAppLink(string $message = ''): string {
    $phone = getSetting('whatsapp', getSetting('phone', ''));
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (empty($phone)) return '#';
    return 'https://wa.me/' . $phone . ($message ? '?text=' . urlencode($message) : '');
}
