<?php
/**
 * Admin Photo Upload Handler with Quality Validation
 * 
 * Enhanced version with:
 * - Quality validation using Google Vision API
 * - Photo type support (passport_info, passport_page, passport_photo, document)
 * - Admin override capability for quality warnings
 * - Thumbnail generation
 * - Comprehensive error handling and cleanup
 */

require_once 'common/config.php';
require_once 'common/functions.php';
require_once 'common/cleanup_functions.php';

$adminthisFile = str_replace('\\', '/', __FILE__);
$adminsrvRoot  = str_replace('admin/upload-kun-photos.php', '', $adminthisFile);

// Set response type for JSON when needed
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$safarid = (isset($_POST['safarid']) && $_POST['safarid'] != '') ? intval($_POST['safarid']) : 0;
$kunregid = (isset($_POST['kunregid']) && $_POST['kunregid'] != '') ? intval($_POST['kunregid']) : 0;
$photoType = (isset($_POST['photo_type']) && $_POST['photo_type'] != '') ? $_POST['photo_type'] : 'document';
$adminOverride = isset($_POST['admin_override']) && $_POST['admin_override'] == '1';

$user_id = getActiveUser();

// Validate photo type
$validPhotoTypes = ['passport_info', 'passport_page', 'passport_photo', 'document'];
if (!in_array($photoType, $validPhotoTypes)) {
    $photoType = 'document';
}

// Response structure for AJAX requests
$response = [
    'success' => true,
    'message' => '',
    'files' => [],
    'warnings' => [],
    'errors' => []
];

// Process file uploads
$file_count = count($_FILES['kunPhotos']['name']);

if($file_count > 0){
    for($i=0; $i<$file_count; $i++) {
        if(trim($_FILES['kunPhotos']['tmp_name'][$i]) == '') continue;
        
        $fileResult = [
            'index' => $i,
            'original_name' => $_FILES['kunPhotos']['name'][$i],
            'uploaded' => false,
            'filename' => null,
            'thumbnail' => null,
            'quality_check' => null,
            'warnings' => [],
            'errors' => []
        ];
        
        try {
            // Prepare file array
            $file = [
                'name' => $_FILES['kunPhotos']['name'][$i],
                'type' => $_FILES['kunPhotos']['type'][$i],
                'tmp_name' => $_FILES['kunPhotos']['tmp_name'][$i],
                'error' => $_FILES['kunPhotos']['error'][$i],
                'size' => $_FILES['kunPhotos']['size'][$i]
            ];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Upload error code: ' . $file['error']);
            }
            
            // Upload file
            $uploadResult = uploadFileWithValidation($file, $adminsrvRoot . 'images/uploads/', $photoType, $adminOverride);
            
            if (!$uploadResult['success']) {
                // Check if it's a warning that can be overridden
                if (isset($uploadResult['can_override']) && $uploadResult['can_override'] && !$adminOverride) {
                    $fileResult['warnings'] = $uploadResult['warnings'];
                    $fileResult['quality_check'] = $uploadResult['quality_check'];
                    throw new Exception('Quality check failed - admin override required');
                }
                throw new Exception($uploadResult['error']);
            }
            
            $fileResult['filename'] = $uploadResult['filename'];
            $fileResult['thumbnail'] = $uploadResult['thumbnail'];
            $fileResult['quality_check'] = $uploadResult['quality_check'];
            
            if (!empty($uploadResult['warnings'])) {
                $fileResult['warnings'] = $uploadResult['warnings'];
                $response['warnings'] = array_merge($response['warnings'], $uploadResult['warnings']);
            }
            
            // Insert into database
            $escapedFilename = mysqli_real_escape_string($dbConn, $uploadResult['filename']);
            $escapedThumbnail = mysqli_real_escape_string($dbConn, $uploadResult['thumbnail']);
            $escapedPhotoType = mysqli_real_escape_string($dbConn, $photoType);
            
            $sql_image = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_filename_thumb, photo_upload_time, photo_user_id, photo_type) 
                         VALUES ($kunregid, '$escapedFilename', '$escapedThumbnail', NOW(), $user_id, '$escapedPhotoType')";
            
            if (!dbQuery($sql_image)) {
                throw new Exception('Database insertion failed');
            }
            
            $fileResult['uploaded'] = true;
            $fileResult['photo_id'] = dbInsertId();
            
        } catch (Exception $e) {
            $fileResult['errors'][] = $e->getMessage();
            $response['errors'][] = "File {$file['name']}: " . $e->getMessage();
            
            // Cleanup on failure
            if (isset($uploadResult['filename'])) {
                cleanupFailedUpload($adminsrvRoot . 'images/uploads/' . $uploadResult['filename']);
            }
        }
        
        $response['files'][] = $fileResult;
    }
}

// Determine overall success
$uploadedCount = count(array_filter($response['files'], function($f) { return $f['uploaded']; }));
if ($uploadedCount === 0) {
    $response['success'] = false;
    $response['message'] = 'No files were uploaded successfully';
} else if (count($response['errors']) > 0) {
    $response['success'] = false;
    $response['message'] = "$uploadedCount file(s) uploaded, but some files failed";
} else {
    $response['message'] = "$uploadedCount file(s) uploaded successfully";
}

// Return JSON for AJAX requests
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Redirect for traditional form submissions
if ($response['success'] || $uploadedCount > 0) {
    header("Location: " . $server_link . "safars/kun-safar/$safarid?photo_upload=success");
} else {
    header("Location: " . $server_link . "safars/kun-safar/$safarid?photo_upload=error");
}
exit;

/**
 * Upload file with quality validation
 */
function uploadFileWithValidation($file, $uploadDir, $photoType = 'document', $adminOverride = false) {
    $result = [
        'success' => false,
        'filename' => null,
        'thumbnail' => null,
        'quality_check' => null,
        'warnings' => [],
        'error' => null,
        'can_override' => false
    ];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $result['error'] = 'Invalid file type. Only JPG, PNG, and GIF images are allowed';
        return $result;
    }
    
    // Check file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds 5MB limit';
        return $result;
    }
    
    // Validate image
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    // Generate filename using MD5 hash
    $ext = substr(strrchr($file['name'], "."), 1);
    $md5Hash = md5_file($file['tmp_name']);
    $filename = $md5Hash . '.' . strtolower($ext);
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $targetPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $result['error'] = 'Failed to move uploaded file';
        return $result;
    }
    
    $result['filename'] = $filename;
    
    // Run quality validation (for passport documents and photos)
    if (in_array($photoType, ['passport_info', 'passport_page', 'passport_photo'])) {
        $qualityResult = validatePassportImageQuality($targetPath);
        $result['quality_check'] = $qualityResult;
        
        if (isset($qualityResult['error']) || (isset($qualityResult['issues']) && !empty($qualityResult['issues']))) {
            $issues = $qualityResult['issues'] ?? [$qualityResult['error']];
            
            if ($adminOverride) {
                // Admin override: treat as warnings
                $result['warnings'] = $issues;
                error_log("Admin override used for quality issues on file: $filename - " . implode('; ', $issues));
            } else {
                // Quality check failed and no override
                $result['error'] = 'Image quality check failed: ' . implode('; ', $issues);
                $result['warnings'] = $issues;
                $result['can_override'] = true; // Admin can override
                
                // Delete the uploaded file
                unlink($targetPath);
                return $result;
            }
        }
    }
    
    // Generate thumbnail
    $thumbnailResult = generatePhotoThumbnail($targetPath, $uploadDir);
    if ($thumbnailResult['success']) {
        $result['thumbnail'] = $thumbnailResult['filename'];
    } else {
        $result['warnings'][] = 'Thumbnail generation failed: ' . $thumbnailResult['error'];
    }
    
    $result['success'] = true;
    return $result;
}

/**
 * Generate 100x100 thumbnail for uploaded photo
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
    
    // Get source image info
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    // Generate thumbnail filename
    $pathInfo = pathinfo($srcFile);
    $thumbnailFilename = $pathInfo['filename'] . '_thumb.jpg';
    $thumbnailPath = $uploadDir . $thumbnailFilename;
    
    // Load source image
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        $result['error'] = 'Failed to load source image';
        return $result;
    }
    
    $srcWidth = imagesx($src);
    $srcHeight = imagesy($src);
    
    // Calculate dimensions maintaining aspect ratio with center crop
    $cropSize = min($srcWidth, $srcHeight);
    $cropX = ($srcWidth - $cropSize) / 2;
    $cropY = ($srcHeight - $cropSize) / 2;
    
    // Create square thumbnail
    $thumb = imagecreatetruecolor($size, $size);
    
    // Preserve transparency for PNG
    if ($imageInfo[2] == IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }
    
    // Copy and resize
    imagecopyresampled($thumb, $src, 0, 0, $cropX, $cropY, $size, $size, $cropSize, $cropSize);
    
    // Save as JPEG with high quality
    if (imagejpeg($thumb, $thumbnailPath, 85)) {
        $result['success'] = true;
        $result['filename'] = $thumbnailFilename;
    } else {
        $result['error'] = 'Failed to save thumbnail';
    }
    
    // Cleanup
    imagedestroy($src);
    imagedestroy($thumb);
    
    return $result;
}

/**
 * Cleanup failed upload
 */
function cleanupFailedUpload($filePath) {
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Also delete thumbnail if it exists
    $pathInfo = pathinfo($filePath);
    $thumbPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.jpg';
    if (file_exists($thumbPath)) {
        unlink($thumbPath);
    }
}
?>
