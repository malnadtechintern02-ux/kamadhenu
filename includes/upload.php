<?php
/**
 * File Upload Handler
 * Kamadenu Goushala
 */

/**
 * Direct file upload function matching admin forms expectations
 * 
 * @param array  $file        Sub-array from $_FILES (e.g. $_FILES['image'])
 * @param string $uploadDir   Subdirectory in uploads folder
 * @param array  $allowedTypes MIME types allowed
 * @param int    $maxSize     Max file size limit in bytes
 * @return array ['success' => bool, 'filename' => string|null, 'message' => string|null, 'error' => string|null]
 */
function uploadFile(array $file, string $uploadDir, array $allowedTypes = [], int $maxSize = 5242880): array {
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'filename' => null, 'message' => 'No file uploaded.', 'error' => 'No file uploaded.'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error (no temp directory).',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
        ];
        $msg = $errorMessages[$file['error']] ?? 'Unknown upload error.';
        return ['success' => false, 'filename' => null, 'message' => $msg, 'error' => $msg];
    }
    
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024, 1);
        $msg = "File size exceeds {$maxMB}MB limit.";
        return ['success' => false, 'filename' => null, 'message' => $msg, 'error' => $msg];
    }
    
    if (!empty($allowedTypes)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $msg = 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
            return ['success' => false, 'filename' => null, 'message' => $msg, 'error' => $msg];
        }
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $targetDir = UPLOADS_PATH . '/' . trim($uploadDir, '/');
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            $msg = 'Failed to create upload directory.';
            return ['success' => false, 'filename' => null, 'message' => $msg, 'error' => $msg];
        }
    }
    
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $msg = 'Failed to save uploaded file.';
        return ['success' => false, 'filename' => null, 'message' => $msg, 'error' => $msg];
    }
    
    return [
        'success'  => true,
        'filename' => $filename,
        'message'  => null,
        'error'    => null,
        'path'     => $uploadDir . '/' . $filename
    ];
}

/**
 * Handle a single file upload
 * 
 * @param string $fieldName  Form field name
 * @param string $uploadDir  Subdirectory within uploads/ (e.g. 'cows', 'events')
 * @param array  $options    Optional: max_size, allowed_types, allowed_extensions
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function handleFileUpload(string $fieldName, string $uploadDir, array $options = []): array {
    // Defaults
    $maxSize = $options['max_size'] ?? MAX_FILE_SIZE;
    $allowedTypes = $options['allowed_types'] ?? ALLOWED_IMAGE_TYPES;
    $allowedExtensions = $options['allowed_extensions'] ?? ALLOWED_IMAGE_EXTENSIONS;
    
    // Check if file was uploaded
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'filename' => null, 'error' => null]; // No file uploaded (not an error)
    }
    
    $file = $_FILES[$fieldName];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error (no temp directory).',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
        ];
        
        $error = $errorMessages[$file['error']] ?? 'Unknown upload error.';
        return ['success' => false, 'filename' => null, 'error' => $error];
    }
    
    // Validate file size
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024, 1);
        return ['success' => false, 'filename' => null, 'error' => "File size exceeds {$maxMB}MB limit."];
    }
    
    // Validate MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'filename' => null, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)];
    }
    
    // Validate extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'filename' => null, 'error' => 'Invalid file extension. Allowed: ' . implode(', ', $allowedExtensions)];
    }
    
    // Create upload directory if needed
    $targetDir = UPLOADS_PATH . '/' . trim($uploadDir, '/');
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            return ['success' => false, 'filename' => null, 'error' => 'Failed to create upload directory.'];
        }
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . '/' . $filename;
    
    // Move the file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'filename' => null, 'error' => 'Failed to save uploaded file.'];
    }
    
    return [
        'success'  => true, 
        'filename' => $filename, 
        'error'    => null,
        'path'     => $uploadDir . '/' . $filename
    ];
}

/**
 * Delete an uploaded file
 */
function deleteUploadedFile(string $relativePath): bool {
    $fullPath = UPLOADS_PATH . '/' . ltrim($relativePath, '/');
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    
    return false;
}

/**
 * Handle multiple file uploads
 */
function handleMultipleFileUploads(string $fieldName, string $uploadDir, array $options = []): array {
    $results = [];
    
    if (!isset($_FILES[$fieldName])) {
        return $results;
    }
    
    $files = $_FILES[$fieldName];
    $fileCount = is_array($files['name']) ? count($files['name']) : 0;
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        
        // Reconstruct single file array
        $_FILES['_temp_upload'] = [
            'name'     => $files['name'][$i],
            'type'     => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error'    => $files['error'][$i],
            'size'     => $files['size'][$i],
        ];
        
        $results[] = handleFileUpload('_temp_upload', $uploadDir, $options);
    }
    
    unset($_FILES['_temp_upload']);
    
    return $results;
}
