<?php
/**
 * Passport Upload Handler
 * 
 * Comprehensive passport upload handler that processes multiple passport page uploads
 * with OCR extraction and quality validation. Integrates seamlessly with existing
 * codebase infrastructure including Google Cloud Vision API, file upload patterns,
 * and database operations.
 * 
 * References:
 * - translate_api.php (Google Cloud integration patterns)
 * - check_previous_application.php (JSON response patterns)
 * - upload-kun-photos.php (file upload workflow)
 * - passport_ocr_service.php (OCR extraction)
 * - common/functions.php (image processing functions)
 * - common/config.php (configuration constants)
 */

// Include required dependencies
require_once 'common/config.php';
require_once 'common/functions.php';
require_once 'common/database.php';
require_once 'common/cleanup_functions.php';
require_once 'passport_ocr_service.php';

// Set content type for JSON response
header('Content-Type: application/json');

// Constants for passport upload handling
define('PASSPORT_UPLOAD_DIR', SRV_ROOT . 'images/uploads/');

/**
 * Upload a single file following the pattern from upload-kun-photos.php
 * 
 * @param array $file The $_FILES array element for a single file
 * @param string $uploadDir The directory to upload to
 * @return array Result array with success status, filename, and error message
 */
function uploadFile($file, $uploadDir) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    // Validate file upload
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $result['error'] = 'Invalid file upload';
        return $result;
    }
    
    // Check file size: use page-scan limit for page uploads
    $maxSize = defined('MAX_PASSPORT_PAGE_SIZE') ? MAX_PASSPORT_PAGE_SIZE : MAX_PASSPORT_PHOTO_SIZE;
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds maximum allowed size of ' . formatBytes($maxSize);
        return $result;
    }
    
    // Validate file type using getimagesize
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    // Generate MD5 hash filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $md5Hash = md5_file($file['tmp_name']);
    $filename = $md5Hash . '.' . strtolower($fileExtension);
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    $targetPath = $uploadDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

/**
 * Run passport quality check using existing function
 * 
 * @param string $filePath Path to the image file
 * @return array Quality validation result
 */
function runPassportQualityCheck($filePath) {
    return \validatePassportImageQuality($filePath);
}

/**
 * Process image file with conversion and resizing
 * 
 * @param string $filePath Path to the original image file
 * @param string $filename Original filename
 * @return array Processing result
 */
function processImageFile($filePath, $filename) {
    $result = [
        'success' => false,
        'processed_filename' => null,
        'error' => null
    ];
    
    try {
        // Create derived filenames
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $jpgDest = PASSPORT_UPLOAD_DIR . $base . '.jpg';
        
        // Convert to JPG using convertToJpg from common/functions.php
        $converted = convertToJpg($filePath, $jpgDest);
        if (!$converted) {
            $result['error'] = 'Failed to convert image to JPG';
            return $result;
        }
        
        // For passport page scans, avoid square crop - use copyImage to maintain aspect ratio
        $finalPath = $jpgDest; // convertToJpg already writes to final destination
        
        $result['success'] = true;
        $result['processed_filename'] = basename($finalPath);
        
    } catch (Exception $e) {
        $result['error'] = 'Image processing error: ' . $e->getMessage();
        // Clean up uploaded file on failure
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    return $result;
}

/**
 * Run passport OCR using existing function
 * 
 * @param string $filePath Path to the passport image file
 * @return array OCR extraction result
 */
function runPassportOcr($filePath) {
    $result = [
        'success' => false,
        'data' => [],
        'error' => null
    ];
    
    try {
        // Use existing extractPassportData function from passport_ocr_service.php
        $ocrResult = \extractPassportData($filePath);
        
        if (is_array($ocrResult) && isset($ocrResult['success']) && $ocrResult['success']) {
            $result['success'] = true;
            
            // Map OCR results to database field names with correct field names
            $data = $ocrResult['data'] ?? [];
            $result['data'] = [
                'kun_ppno' => $data['kun_passport_no'] ?? '',
                'kun_ppexpiry' => $data['kun_passport_expiry'] ?? '',
                'kun_fname' => $data['kun_fname'] ?? '',
                'kun_lname' => $data['kun_lname'] ?? '',
                'kun_nationality' => $data['kun_nationality'] ?? '',
                'kun_dob' => $data['kun_dob'] ?? '',
                'kun_gender' => $data['kun_gender'] ?? ''
            ];
        } else {
            $result['error'] = $ocrResult['error'] ?? 'OCR extraction failed';
        }
        
    } catch (Exception $e) {
        $result['error'] = 'OCR processing error: ' . $e->getMessage();
    }
    
    return $result;
}

/**
 * Insert photo record into database
 * 
 * @param int $kunId The kun ID
 * @param string $filename The photo filename
 * @param string $photoType The photo type (passport_info, passport_page, etc.)
 * @param array $ocrData Optional OCR data for passport_info type
 * @return array Database insertion result
 */
function insertPhotoRecord($kunId, $filename, $photoType, $ocrData = []) {
    global $dbConn;
    $result = [
        'success' => false,
        'photo_id' => null,
        'error' => null
    ];
    
    try {
        // Escape input data
        $escapedKunId = mysqli_real_escape_string($dbConn, $kunId);
        $escapedFilename = mysqli_real_escape_string($dbConn, $filename);
        $escapedPhotoType = mysqli_real_escape_string($dbConn, $photoType);
        $escapedUserId = mysqli_real_escape_string($dbConn, getActiveUser());
        
        // Build SQL query
        $sql = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_type, photo_upload_time, photo_user_id) 
                VALUES ('$escapedKunId', '$escapedFilename', '$escapedPhotoType', NOW(), '$escapedUserId')";
        
        // Execute query
        $queryResult = dbQuery($sql);
        
        if ($queryResult) {
            $result['success'] = true;
            $result['photo_id'] = dbInsertId();
            
            // Include OCR data in response but don't automatically update kun record
            $result['ocr_data'] = $ocrData;
        } else {
            $result['error'] = 'Database insertion failed: ' . mysqli_error($dbConn);
        }
        
    } catch (Exception $e) {
        $result['error'] = 'Database error: ' . $e->getMessage();
    }
    
    return $result;
}


// Main processing logic
try {
    // Validate POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }
    
    // Check if files were uploaded
    if (!isset($_FILES['kunPhotos']) || !is_array($_FILES['kunPhotos']['name'])) {
        throw new Exception('No files uploaded');
    }
    
    // Get kun_id from POST data (accept kunregid with kun_id fallback)
    $kunId = 0;
    if (isset($_POST['kunregid'])) {
        $kunId = (int) $_POST['kunregid'];
    } elseif (isset($_POST['kun_id'])) {
        $kunId = (int) $_POST['kun_id'];
    }
    if ($kunId <= 0) {
        throw new Exception('Invalid registration id');
    }
    
    // Initialize response structure following check_previous_application.php pattern
    $response = [
        'success' => true,
        'message' => 'Passport upload processing completed',
        'data' => [
            'total_files' => count($_FILES['kunPhotos']['name']),
            'processed_files' => 0,
            'failed_files' => 0,
            'file_results' => []
        ],
        'errors' => []
    ];
    
    // Process each uploaded file
    $fileCount = count($_FILES['kunPhotos']['name']);
    $isFirstFile = true;
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileResult = [
            'file_index' => $i,
            'original_name' => $_FILES['kunPhotos']['name'][$i],
            'success' => false,
            'filename' => null,
            'photo_type' => null,
            'ocr_data' => null,
            'quality_validation' => null,
            'error' => null
        ];
        
        try {
            // Prepare file array for uploadFile function
            $file = [
                'name' => $_FILES['kunPhotos']['name'][$i],
                'type' => $_FILES['kunPhotos']['type'][$i],
                'tmp_name' => $_FILES['kunPhotos']['tmp_name'][$i],
                'error' => $_FILES['kunPhotos']['error'][$i],
                'size' => $_FILES['kunPhotos']['size'][$i]
            ];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload error: ' . $file['error']);
            }
            
            // Upload file
            $uploadResult = uploadFile($file, PASSPORT_UPLOAD_DIR);
            if (!$uploadResult['success']) {
                throw new Exception($uploadResult['error']);
            }
            
            $fileResult['filename'] = $uploadResult['filename'];
            
            // Process image file
            $processResult = processImageFile(PASSPORT_UPLOAD_DIR . $uploadResult['filename'], $uploadResult['filename']);
            if (!$processResult['success']) {
                throw new Exception($processResult['error']);
            }
            
            $fileResult['filename'] = $processResult['processed_filename'];
            
            // Validate image quality
            $qualityResult = runPassportQualityCheck(PASSPORT_UPLOAD_DIR . $processResult['processed_filename']);
            $fileResult['quality_validation'] = $qualityResult;
            
            // Quality gating - only proceed if quality passes
            if (isset($qualityResult['error']) || (isset($qualityResult['issues']) && !empty($qualityResult['issues']))) {
                throw new Exception('Image quality issues: ' . implode('; ', $qualityResult['issues'] ?? [$qualityResult['error']]));
            }
            
            // Determine photo type (first file is passport_info, others are passport_page)
            $photoType = $isFirstFile ? 'passport_info' : 'passport_page';
            $fileResult['photo_type'] = $photoType;
            
            // Extract OCR data for passport_info (first file)
            $ocrData = [];
            if ($isFirstFile) {
                $ocrResult = runPassportOcr(PASSPORT_UPLOAD_DIR . $processResult['processed_filename']);
                if ($ocrResult['success']) {
                    $fileResult['ocr_data'] = $ocrResult['data'];
                    $ocrData = $ocrResult['data'];
                } else {
                    $fileResult['ocr_data'] = ['error' => $ocrResult['error']];
                }
            }
            
            // Insert photo record into database
            $dbResult = insertPhotoRecord($kunId, $processResult['processed_filename'], $photoType, $ocrData);
            if (!$dbResult['success']) {
                throw new Exception($dbResult['error']);
            }
            
            $fileResult['success'] = true;
            $response['data']['processed_files']++;
            
            // Mark first file as processed
            $isFirstFile = false;
            
        } catch (Exception $e) {
            $fileResult['error'] = $e->getMessage();
            $response['data']['failed_files']++;
            $response['errors'][] = "File " . ($i + 1) . ": " . $e->getMessage();
            
            // Clean up uploaded file on failure
            if (isset($uploadResult['filename']) && file_exists(PASSPORT_UPLOAD_DIR . $uploadResult['filename'])) {
                unlink(PASSPORT_UPLOAD_DIR . $uploadResult['filename']);
            }
        }
        
        $response['data']['file_results'][] = $fileResult;
    }
    
    // Determine overall success
    if ($response['data']['failed_files'] > 0) {
        $response['success'] = false;
        $response['message'] = 'Some files failed to process';
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Passport upload processing failed',
        'error' => $e->getMessage(),
        'data' => null
    ];
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
?>
