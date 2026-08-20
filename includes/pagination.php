<?php
/**
 * Pagination Renderer
 * Kamadenu Goushala
 */

/**
 * Render Bootstrap pagination HTML
 * 
 * @param array $pagination  From buildPagination()
 * @param string $baseUrl    Base URL for page links (without page param)
 * @return string HTML
 */
function renderPagination(array $pagination, string $baseUrl = ''): string {
    if ($pagination['total_pages'] <= 1) return '';
    
    // Build base URL with existing query params
    if (empty($baseUrl)) {
        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
        $queryParams = $_GET;
        unset($queryParams['page']);
        if (!empty($queryParams)) {
            $baseUrl .= '?' . http_build_query($queryParams) . '&';
        } else {
            $baseUrl .= '?';
        }
    }
    
    $current = $pagination['current_page'];
    $total = $pagination['total_pages'];
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous
    if ($pagination['has_prev']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . 'page=' . ($current - 1)) . '" aria-label="Previous"><i class="bi bi-chevron-left"></i></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>';
    }
    
    // Page numbers
    $start = max(1, $current - 2);
    $end = min($total, $current + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . 'page=1') . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i === $current) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . 'page=' . $i) . '">' . $i . '</a></li>';
        }
    }
    
    if ($end < $total) {
        if ($end < $total - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . 'page=' . $total) . '">' . $total . '</a></li>';
    }
    
    // Next
    if ($pagination['has_next']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . 'page=' . ($current + 1)) . '" aria-label="Next"><i class="bi bi-chevron-right"></i></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}
