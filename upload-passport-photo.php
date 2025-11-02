<?php
/**
 * Passport Photo Upload Handler with Cropping
 * 
 * Handles passport photo uploads with:
 * - Interactive cropping support (receives crop coordinates from client)
 * - Server-side crop validation
 * - Resize to 51mm x 51mm at 120 DPI (240x240 pixels minimum)
 * - Quality validation
 * - Thumbnail generation
 * - Database storage as 'passport_photo' type
 */

require_once 'common/config.php';
require_once 'common/functions.php';
require_once 'common/cleanup_functions.php';

header('Content-Type: application/json');

// Get form data
$safarid = isset($_POST['safarid']) ? intval($_POST['safarid']) : 0;
$kunregid = isset($_POST['kunregid']) ? intval($_POST['kunregid']) : 0;
$adminOverride = isset($_POST['admin_override']) && $_POST['admin_override'] == '1';

// Get crop coordinates
$cropX = isset($_POST['crop_x']) ? intval($_POST['crop_x']) : 0;
$cropY = isset($_POST['crop_y']) ? intval($_POST['crop_y']) : 0;
$cropWidth = isset($_POST['crop_width']) ? intval($_POST['crop_width']) : 0;
$cropHeight = isset($_POST['crop_height']) ? intval($_POST['crop_height']) : 0;

$user_id = getActiveUser();

// Response structure
$response = [
    'success' => false,
    'message' => '',
    'filename' => null,
    'thumbnail' => null,
    'warnings' => [],
    'error' => null
];

try {
    // Validate registration ID
    if ($kunregid <= 0) {
        throw new Exception('Invalid registration ID');
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['passport_photo']) || $_FILES['passport_photo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }
    
    $uploadedFile = $_FILES['passport_photo'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG and PNG images are allowed');
    }
    
    // Check file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($uploadedFile['size'] > $maxSize) {
        throw new Exception('File size exceeds 5MB limit');
    }
    
    // Validate image
    $imageInfo = getimagesize($uploadedFile['tmp_name']);
    if (!$imageInfo) {
        throw new Exception('Invalid image file');
    }
    
    // Generate filename
    $md5Hash = md5_file($uploadedFile['tmp_name']);
    $filename = $md5Hash . '.jpg'; // Always save as JPG
    $uploadDir = SRV_ROOT . 'images/uploads/';
    $filePath = $uploadDir . $filename;
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Process the image with cropping
    $cropData = [
        'x' => $cropX,
        'y' => $cropY,
        'width' => $cropWidth,
        'height' => $cropHeight
    ];
    
    $processResult = processPassportPhotoWithCrop($uploadedFile['tmp_name'], $filePath, $cropData);
    
    if (!$processResult['success']) {
        throw new Exception($processResult['error']);
    }
    
    // Quality validation (if not admin override)
    if (!$adminOverride) {
        $qualityResult = validatePassportImageQuality($filePath);
        
        if (isset($qualityResult['error']) || (isset($qualityResult['issues']) && !empty($qualityResult['issues']))) {
            $issues = $qualityResult['issues'] ?? [$qualityResult['error']];
            
            // Delete uploaded file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            throw new Exception('Image quality check failed: ' . implode('; ', $issues));
        }
    }
    
    // Generate thumbnail
    $thumbnailResult = generatePhotoThumbnail($filePath, $uploadDir, 100);
    $thumbnail = $thumbnailResult['success'] ? $thumbnailResult['filename'] : '';
    
    if (!$thumbnailResult['success']) {
        $response['warnings'][] = 'Thumbnail generation failed: ' . $thumbnailResult['error'];
    }
    
    // Store in database
    $storeResult = storePassportPhoto($kunregid, $filename, $thumbnail, $user_id);
    
    if (!$storeResult['success']) {
        // Cleanup uploaded files
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        if ($thumbnail && file_exists($uploadDir . $thumbnail)) {
            unlink($uploadDir . $thumbnail);
        }
        throw new Exception($storeResult['error']);
    }
    
    $response['success'] = true;
    $response['message'] = 'Passport photo uploaded successfully';
    $response['filename'] = $filename;
    $response['thumbnail'] = $thumbnail;
    $response['dimensions'] = $processResult['dimensions'] ?? null;
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    $response['message'] = 'Upload failed: ' . $e->getMessage();
}

echo json_encode($response);

/**
 * Process passport photo with custom crop coordinates
 */
function processPassportPhotoWithCrop($srcFile, $destFile, $cropData) {
    $result = [
        'success' => false,
        'dimensions' => null,
        'error' => null
    ];
    
    if (!file_exists($srcFile)) {
        $result['error'] = 'Source file not found';
        return $result;
    }
    
    // Load source image
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        $result['error'] = 'Could not load source image';
        return $result;
    }
    
    $srcWidth = imagesx($src);
    $srcHeight = imagesy($src);
    
    // Validate crop coordinates
    if ($cropData['width'] <= 0 || $cropData['height'] <= 0) {
        // Use center crop if no valid coordinates provided
        $cropSize = min($srcWidth, $srcHeight);
        $cropData['x'] = ($srcWidth - $cropSize) / 2;
        $cropData['y'] = ($srcHeight - $cropSize) / 2;
        $cropData['width'] = $cropSize;
        $cropData['height'] = $cropSize;
    }
    
    // Ensure crop is within bounds
    $cropData['x'] = max(0, min($cropData['x'], $srcWidth - 1));
    $cropData['y'] = max(0, min($cropData['y'], $srcHeight - 1));
    $cropData['width'] = min($cropData['width'], $srcWidth - $cropData['x']);
    $cropData['height'] = min($cropData['height'], $srcHeight - $cropData['y']);
    
    // Target size: 51mm x 51mm at 120 DPI = approximately 240x240 pixels
    $targetSize = 240;
    
    // Create cropped and resized image
    $dest = imagecreatetruecolor($targetSize, $targetSize);
    
    // Preserve transparency for PNG
    if ($imageInfo[2] == IMAGETYPE_PNG) {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
    }
    
    // Copy and resize cropped area
    imagecopyresampled(
        $dest, $src,
        0, 0,
        $cropData['x'], $cropData['y'],
        $targetSize, $targetSize,
        $cropData['width'], $cropData['height']
    );
    
    // Save as high-quality JPEG
    if (imagejpeg($dest, $destFile, 95)) {
        // Set DPI metadata (120 DPI)
        // Note: PHP's GD doesn't set DPI directly, but we can document it
        $result['success'] = true;
        $result['dimensions'] = [
            'width' => $targetSize,
            'height' => $targetSize,
            'dpi' => 120,
            'file_size' => filesize($destFile),
            'file_size_formatted' => formatBytes(filesize($destFile))
        ];
    } else {
        $result['error'] = 'Failed to save processed image';
    }
    
    // Cleanup
    imagedestroy($src);
    imagedestroy($dest);
    
    return $result;
}

/**
 * Store passport photo in database (with thumbnail support)
 */
function storePassportPhoto($kunregid, $filename, $thumbnail, $user_id) {
    global $dbConn;
    
    try {
        // Check if passport photo already exists
        $checkSql = "SELECT photo_id FROM zyt_kun_photos WHERE kun_id = $kunregid AND photo_type = 'passport_photo'";
        $existingPhoto = dbFetchAssoc(dbQuery($checkSql));
        
        if ($existingPhoto) {
            // Update existing record
            $sql = "UPDATE zyt_kun_photos SET 
                    photo_filename = '$filename', 
                    photo_filename_thumb = '$thumbnail',
                    photo_upload_time = NOW(), 
                    photo_user_id = $user_id 
                    WHERE kun_id = $kunregid AND photo_type = 'passport_photo'";
        } else {
            // Insert new record
            $sql = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_filename_thumb, photo_upload_time, photo_user_id, photo_type) 
                    VALUES ($kunregid, '$filename', '$thumbnail', NOW(), $user_id, 'passport_photo')";
        }
        
        $result = dbQuery($sql);
        
        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to store photo information in database'];
        }
        
    } catch (Exception $e) {
        error_log('Database error storing passport photo: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Database error occurred'];
    }
}

/**
 * Generate thumbnail
 */
function generatePhotoThumbnail($srcFile, $uploadDir, $size = 100) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    if (!file_exists($srcFile)) {
        $result['error'] = 'Source file not found';
        return $result;
    }
    
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    $pathInfo = pathinfo($srcFile);
    $thumbnailFilename = $pathInfo['filename'] . '_thumb.jpg';
    $thumbnailPath = $uploadDir . $thumbnailFilename;
    
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        $result['error'] = 'Failed to load source image';
        return $result;
    }
    
    $srcWidth = imagesx($src);
    $srcHeight = imagesy($src);
    $cropSize = min($srcWidth, $srcHeight);
    $cropX = ($srcWidth - $cropSize) / 2;
    $cropY = ($srcHeight - $cropSize) / 2;
    
    $thumb = imagecreatetruecolor($size, $size);
    imagecopyresampled($thumb, $src, 0, 0, $cropX, $cropY, $size, $size, $cropSize, $cropSize);
    
    if (imagejpeg($thumb, $thumbnailPath, 85)) {
        $result['success'] = true;
        $result['filename'] = $thumbnailFilename;
    } else {
        $result['error'] = 'Failed to save thumbnail';
    }
    
    imagedestroy($src);
    imagedestroy($thumb);
    
    return $result;
}

/**
 * Format bytes to human-readable size
 */
function formatBytes($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

