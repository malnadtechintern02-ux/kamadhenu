<?php
/**
 * Flash Message System
 * Kamadenu Goushala
 */

/**
 * Set a flash message
 * 
 * @param string $type success|error|warning|info
 * @param string $message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash_messages'][] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Get and clear all flash messages
 */
function getFlashMessages(): array {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Check if there are any flash messages
 */
function hasFlashMessages(): bool {
    return !empty($_SESSION['flash_messages']);
}

/**
 * Render flash messages as Bootstrap alerts
 */
function renderFlashMessages(): string {
    $messages = getFlashMessages();
    if (empty($messages)) return '';
    
    $html = '<div class="flash-messages-container">';
    
    foreach ($messages as $msg) {
        $typeMap = [
            'success' => 'alert-success',
            'error'   => 'alert-danger',
            'warning' => 'alert-warning',
            'info'    => 'alert-info',
        ];
        
        $iconMap = [
            'success' => 'bi-check-circle-fill',
            'error'   => 'bi-exclamation-triangle-fill',
            'warning' => 'bi-exclamation-circle-fill',
            'info'    => 'bi-info-circle-fill',
        ];
        
        $alertClass = $typeMap[$msg['type']] ?? 'alert-info';
        $iconClass = $iconMap[$msg['type']] ?? 'bi-info-circle-fill';
        
        $html .= sprintf(
            '<div class="alert %s alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi %s me-2"></i>
                <div>%s</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>',
            $alertClass,
            $iconClass,
            e($msg['message'])
        );
    }
    
    $html .= '</div>';
    return $html;
}
