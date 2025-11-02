<?php
require_once 'common/config.php';
require_once 'common/functions.php';
require_once 'common/cleanup_functions.php';

$safarid = (isset($_POST['safarid']) && $_POST['safarid'] != '') ? $_POST['safarid'] : '0';
$kunregid = (isset($_POST['kunregid']) && $_POST['kunregid'] != '') ? $_POST['kunregid'] : '0';

$user_id = getActiveUser();

// Check if this is a passport photo upload
if (isset($_FILES['passport_photo']) && !empty($_FILES['passport_photo']['tmp_name'])) {
    header('Content-Type: application/json');
    $result = handlePassportPhotoUpload($kunregid, $user_id);
    echo json_encode($result);
    exit;
}

// Handle regular photo uploads (existing functionality)
$file_count = count($_FILES['kunPhotos']['name']);

if($file_count > 0){
    for($i=0; $i<$file_count; $i++) {
        if(trim($_FILES['kunPhotos']['tmp_name'][$i]) == '') continue;
        $files = uploadFile($i, SRV_ROOT . 'images/uploads/');

        $mainFile = $files['file'];

        // Get photo type from uploadFile function and escape it
        $photoType = $files['photo_type'];
        $escapedPhotoType = mysqli_real_escape_string($dbConn, $photoType);
        $escapedMainFile = mysqli_real_escape_string($dbConn, $mainFile);
        
        $sql_image = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_filename_thumb, photo_upload_time, photo_user_id, photo_type) VALUES ($kunregid, '$escapedMainFile', '', NOW(), $user_id, '$escapedPhotoType')";
        dbQuery($sql_image);
    }
}

// For regular uploads, redirect as before
header("Location: " . $server_link . "safars/kun-safar/$safarid");

function uploadImageFile($count, $uploadDir)
{
    //$image     = $_FILES['fleImage'];
    $imagePath = '';
    $thumbnailPath = '';
    $thumbnailMidPath =  '';

    // if a file is given
    if (trim($_FILES['kunPhotos']['tmp_name'][$count]) != '') {
        //if(trim($_FILES['kunPhotos']['tmp_name'][$count]) == '') continue;
        $ext = substr(strrchr($_FILES['kunPhotos']['name'][$count], "."), 1);

        // generate a random new file name to avoid name conflict
        $imagePath = md5(rand() * time()) . ".$ext";

        list($width, $height, $type, $attr) = getimagesize($_FILES['kunPhotos']['tmp_name'][$count]);

        // make sure the image width does not exceed the
        // maximum allowed width
        if (LIMIT_PRODUCT_WIDTH && $width > MAX_PRODUCT_IMAGE_WIDTH) {
            $result    = createThumbnail($_FILES['kunPhotos']['tmp_name'][$count], $uploadDir . $imagePath, MAX_PRODUCT_IMAGE_WIDTH);
            $imagePath = $result;
        } else {
            $result = move_uploaded_file($_FILES['kunPhotos']['tmp_name'][$count], $uploadDir . $imagePath);
        }

        if ($result) {
            // create thumbnail
            $thumbnailPath =  $imagePath . ".$ext";
            $thumbnailMidPath =  md5(rand() * time()) . ".$ext";
            $result = createThumbnail($uploadDir . $imagePath, $uploadDir . $thumbnailPath, MAX_PRODUCT_IMAGE_WIDTH);
            $resultMid = createThumbnail($uploadDir . $imagePath, $uploadDir . $thumbnailMidPath, MAX_PRODUCT_IMAGE_WIDTH); //width for mid image is always 220

            // create thumbnail failed, delete the image
            if (!$result) {
                unlink($uploadDir . $imagePath);
                $imagePath = $thumbnailPath = $thumnailMidPath = '';
            } else {
                $thumbnailPath = $result;
            }

            // create thumbnailmid failed, delete the image
            if (!$resultMid) {
                unlink($uploadDir . $imagePath);
                $imagePath = $thumbnailPath = $thumbnailMidPath = '';
            } else {
                $thumbnailMidPath = $resultMid;
            }
        } else {
            // the image cannot be upload / resized
            $imagePath = $thumbnailPath = $thumbnailMidPath = '';
        }

    }
    return array('image' => $imagePath, 'thumbnail' => $thumbnailPath, 'thumbnailMid' => $thumbnailMidPath);
}


function uploadFile($count, $uploadDir, $photoType = 'document')
{
    //$image     = $_FILES['fleImage'];
    $imagePath = '';
    $thumbnailPath = '';
    $thumbnailMidPath =  '';

    // Validate photo type against ENUM values
    $validPhotoTypes = ['passport_info', 'passport_page', 'passport_photo', 'document'];
    if (!in_array($photoType, $validPhotoTypes)) {
        $photoType = 'document'; // Default fallback
    }

    // if a file is given
    if (trim($_FILES['kunPhotos']['tmp_name'][$count]) != '') {
        $ext = substr(strrchr($_FILES['kunPhotos']['name'][$count], "."), 1);
        // generate a random new file name to avoid name conflict
        $filePath = md5(rand() * time()) . ".$ext";
    
        //$filePath = $_FILES['kunPhotos']['name'][$count];

        $result = move_uploaded_file($_FILES['kunPhotos']['tmp_name'][$count], $uploadDir . $filePath);

        if (!$result) {
            // the image cannot be upload / resized
            $filePath = '';
        }
    }
    return array('file' => $filePath, 'photo_type' => $photoType);
}

/*
    Passport Photo Upload Handler Functions
*/

/*
    Main function to handle passport photo uploads with cropping support
*/
function handlePassportPhotoUpload($kunregid, $user_id) {
    try {
        // Validate input parameters
        if (!$kunregid || !$user_id) {
            return ['success' => false, 'message' => 'Invalid registration ID or user ID'];
        }
        
        // Validate uploaded file
        if (!isset($_FILES['passport_photo']) || $_FILES['passport_photo']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error occurred'];
        }
        
        $uploadedFile = $_FILES['passport_photo'];
        
        // Validate file type and size
        $validation = validatePassportPhotoUpload($uploadedFile);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['error']];
        }
        
        // Generate filename for passport photo
        $filename = generatePassportPhotoFilename($kunregid);
        $destPath = SRV_ROOT . 'images/uploads/' . $filename;
        
        // Process the passport photo
        $processResult = processPassportPhoto($uploadedFile['tmp_name'], $destPath);
        if (!$processResult['success']) {
            return ['success' => false, 'message' => $processResult['error']];
        }
        
        // Validate image quality using Vision API
        $qualityResult = validatePassportImageQuality($destPath);
        if (isset($qualityResult['error'])) {
            // Delete the file if quality validation fails
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            return ['success' => false, 'message' => 'Image quality validation failed: ' . $qualityResult['error']];
        }
        
        // Store in database
        $storeResult = storePassportPhoto($kunregid, $filename, $user_id);
        if (!$storeResult['success']) {
            // Clean up uploaded file if database storage fails
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            return ['success' => false, 'message' => $storeResult['error']];
        }
        
        // Clean up temporary files
        cleanupTempPassportFiles();
        
        return [
            'success' => true, 
            'message' => 'Passport photo uploaded successfully',
            'filename' => $filename,
            'file_size' => $processResult['dimensions']['file_size_formatted'],
            'dimensions' => $processResult['dimensions']['width'] . 'x' . $processResult['dimensions']['height']
        ];
        
    } catch (Exception $e) {
        // Log error for debugging
        error_log('Passport photo upload error: ' . $e->getMessage());
        
        // Clean up any temporary files
        cleanupTempPassportFiles();
        
        return ['success' => false, 'message' => 'An error occurred during upload. Please try again.'];
    }
}


?>