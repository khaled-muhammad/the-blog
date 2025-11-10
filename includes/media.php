<?php
function uploadMedia($file, $uploadedBy = null, $altText = null) {
    if (!isset($GLOBALS['conn'])) {
        return false;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("File upload error: " . $file['error']);
        return false;
    }
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/webm'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        error_log("Invalid file type: " . $mimeType);
        return false;
    }
    
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $filename;
    $fileUrl = '/uploads/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        error_log("Failed to move uploaded file");
        return false;
    }
    
    $width = null;
    $height = null;
    if (strpos($mimeType, 'image/') === 0) {
        $imageInfo = getimagesize($filePath);
        if ($imageInfo) {
            $width = $imageInfo[0];
            $height = $imageInfo[1];
        }
    }
    
    try {
        $stmt = $GLOBALS['conn']->prepare("
            INSERT INTO media (
                filename, original_filename, file_path, file_url, mime_type, 
                file_size, width, height, alt_text, uploaded_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $filename,
            $file['name'],
            $filePath,
            $fileUrl,
            $mimeType,
            $file['size'],
            $width,
            $height,
            $altText,
            $uploadedBy
        ]);
        
        return $GLOBALS['conn']->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error saving media: " . $e->getMessage());
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        return false;
    }
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function calculateReadingTime($content) {
    $wordCount = str_word_count(strip_tags($content));
    $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute
    return max(1, $readingTime); // Minimum 1 minute
}

