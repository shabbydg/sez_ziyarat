<?php
require_once 'common/config.php';
require_once 'common/functions.php';
require 'PHPMailer/PHPMailerAutoload.php';

$kunregid = (isset($_POST['kunregid']) && $_POST['kunregid'] != '') ? $_POST['kunregid'] : '0'; //0 means it's a new registration

$user_id = getActiveUser();

// Required fields that still exist in the new form
$safarid = $_POST['safarid'];
$its = $_POST['its'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$fname = $_POST['fname'];
$mname = $_POST['mname'] ?? '';
$lname = $_POST['lname'];
$ppno = $_POST['ppno'];
$nationality = $_POST['nationality'];
$ppissue = $_POST['ppissue'];
$ppexpiry = $_POST['ppexpiry'];
$placeofissue = $_POST['placeofissue'];
$dob = $_POST['dob'];
$birthplace = $_POST['birthplace'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$profession = $_POST['profession'];

// Legacy fields that were removed - set to empty/default values
$countryofissue = '';
$orignationality = '';
$birthcountry = '';
$curroccupation = '';
$langspoken = '';
$spname = '';
$spoccupation = '';
$spnationality = '';
$spdob = '0000-00-00';
$sppob = '';
$accomppersons = 0;
$certs = '';
$issuecerts = '';
$dateissuecerts = '0000-00-00';
$numpreventry = 0;
$datelentry = '0000-00-00';
$child1name = '';
$child1dob = '0000-00-00';
$child1occ = '';
$child2name = '';
$child2dob = '0000-00-00';
$child2occ = '';
$child3name = '';
$child3dob = '0000-00-00';
$child3occ = '';
$visit1country = '';
$visit1reason = '';
$visit1date = '0000-00-00';
$visit2country = '';
$visit2reason = '';
$visit2date = '0000-00-00';
$visit3country = '';
$visit3reason = '';
$visit3date = '0000-00-00';

// New fields from redesigned form
$age = $_POST['age'] ?? '';
$passport_arabic_name = $_POST['passport_arabic_name'] ?? '';
$accommodation_required = 'Yes'; // Always set to Yes (as per requirements)
$fname_arabic = $_POST['fname_arabic'] ?? '';
$mname_arabic = $_POST['mname_arabic'] ?? '';
$lname_arabic = $_POST['lname_arabic'] ?? '';
$father_name = $_POST['father_name'] ?? '';
$father_name_arabic = $_POST['father_name_arabic'] ?? '';
$grandfather_name = $_POST['grandfather_name'] ?? '';
$grandfather_name_arabic = $_POST['grandfather_name_arabic'] ?? '';
$mother_name = $_POST['mother_name'] ?? '';
$mother_name_arabic = $_POST['mother_name_arabic'] ?? '';
$mother_father_name = $_POST['mother_father_name'] ?? '';
$mother_father_name_arabic = $_POST['mother_father_name_arabic'] ?? '';
$passport_type = $_POST['passport_type'] ?? '';

if($kunregid == 0){
    //INSERT
    $sql_insert = "INSERT INTO `zyt_kun_registrations` (`safar_id`, `kun_its`, `kun_fname`, `kun_mname`, `kun_lname`, `kun_ppno`, `kun_nationality`, `kun_ppissue`, `kun_ppexpiry`, `kun_placeofissue`, `kun_countryofissue`, `kun_dob`, `kun_age`, `kun_birthplace`, `kun_gender`, `kun_orignationality`, `kun_birthcountry`, `kun_curroccupation`, `kun_address`, `kun_langspoken`, `kun_spname`, `kun_spoccupation`, `kun_spdob`, `kun_sppob`, `kun_spnationality`, `kun_accomppersons`, `kun_profession`, `kun_certs`, `kun_issuecerts`, `kun_dateissuecerts`, `kun_numpreventry`, `kun_datelentry`, `kun_child1name`, `kun_child1dob`, `kun_child1occ`, `kun_child2name`, `kun_child2dob`, `kun_child2occ`, `kun_child3name`, `kun_child3dob`, `kun_child3occ`, `kun_visit1country`, `kun_visit1reason`, `kun_visit1date`, `kun_visit2country`, `kun_visit2reason`, `kun_visit2date`, `kun_visit3country`, `kun_visit3reason`, `kun_visit3date`, `kun_status`, `kun_contact`, `kun_email`, `kun_declaration`, `kun_lastupdate`, `kun_lastupdatememid`, `kun_passport_arabic_name`, `kun_accommodation_required`, `kun_fname_arabic`, `kun_mname_arabic`, `kun_lname_arabic`, `kun_father_name`, `kun_father_name_arabic`, `kun_grandfather_name`, `kun_grandfather_name_arabic`, `kun_mother_name`, `kun_mother_name_arabic`, `kun_mother_father_name`, `kun_mother_father_name_arabic`, `kun_passport_type`) VALUES ('$safarid', '$its', '$fname', '$mname', '$lname', '$ppno', '$nationality', '$ppissue', '$ppexpiry', '$placeofissue', '$countryofissue', '$dob', '$age', '$birthplace', '$gender', '$orignationality', '$birthcountry', '$curroccupation', '$address', '$langspoken', '$spname', '$spoccupation', '$spdob', '$sppob', '$spnationality', '$accomppersons', '$profession', '$certs', '$issuecerts', '$dateissuecerts', '$numpreventry', '$datelentry', '$child1name', '$child1dob', '$child1occ', '$child2name', '$child2dob', '$child2occ', '$child3name', '$child3dob', '$child3occ', '$visit1country', '$visit1reason', '$visit1date', '$visit2country', '$visit2reason', '$visit2date', '$visit3country', '$visit3reason', '$visit3date', 'registered', '$contact', '$email', 1, NOW(), '$its', '$passport_arabic_name', '$accommodation_required', '$fname_arabic', '$mname_arabic', '$lname_arabic', '$father_name', '$father_name_arabic', '$grandfather_name', '$grandfather_name_arabic', '$mother_name', '$mother_name_arabic', '$mother_father_name', '$mother_father_name_arabic', '$passport_type');";
    dbQuery($sql_insert);
    
    // Get the last inserted ID for passport photo processing
    $kunregid = dbInsertId();
} else {
    $sql_update = "UPDATE zyt_kun_registrations SET 
        safar_id = '$safarid', 
        kun_its = '$its', 
        kun_fname = '$fname', 
        kun_mname = '$mname', 
        kun_lname = '$lname', 
        kun_ppno = '$ppno', 
        kun_nationality = '$nationality', 
        kun_ppissue = '$ppissue', 
        kun_ppexpiry = '$ppexpiry', 
        kun_placeofissue = '$placeofissue', 
        kun_countryofissue = '$countryofissue', 
        kun_dob = '$dob', 
        kun_age = '$age',
        kun_birthplace = '$birthplace', 
        kun_gender = '$gender', 
        kun_orignationality = '$orignationality', 
        kun_birthcountry = '$birthcountry', 
        kun_curroccupation = '$curroccupation', 
        kun_address = '$address', 
        kun_langspoken = '$langspoken', 
        kun_spname = '$spname', 
        kun_spoccupation = '$spoccupation', 
        kun_spdob = '$spdob', 
        kun_sppob = '$sppob', 
        kun_spnationality = '$spnationality', 
        kun_accomppersons = '$accomppersons', 
        kun_profession = '$profession', 
        kun_certs = '$certs', 
        kun_issuecerts = '$issuecerts', 
        kun_dateissuecerts = '$dateissuecerts', 
        kun_numpreventry = '$numpreventry', 
        kun_datelentry = '$datelentry', 
        kun_child1name = '$child1name', 
        kun_child1dob = '$child1dob', 
        kun_child1occ = '$child1occ', 
        kun_child2name = '$child2name', 
        kun_child2dob = '$child2dob', 
        kun_child2occ = '$child2occ', 
        kun_child3name = '$child3name', 
        kun_child3dob = '$child3dob', 
        kun_child3occ = '$child3occ', 
        kun_visit1country = '$visit1country', 
        kun_visit1reason = '$visit1reason', 
        kun_visit1date = '$visit1date', 
        kun_visit2country = '$visit2country', 
        kun_visit2reason = '$visit2reason', 
        kun_visit2date = '$visit2date', 
        kun_visit3country = '$visit3country', 
        kun_visit3reason = '$visit3reason', 
        kun_visit3date = '$visit3date', 
        kun_contact = '$contact', 
        kun_email = '$email', 
        kun_declaration = 1, 
        kun_lastupdate = NOW(),
        kun_lastupdatememid = '$its',
        kun_passport_arabic_name = '$passport_arabic_name',
        kun_accommodation_required = '$accommodation_required',
        kun_fname_arabic = '$fname_arabic',
        kun_mname_arabic = '$mname_arabic',
        kun_lname_arabic = '$lname_arabic',
        kun_father_name = '$father_name',
        kun_father_name_arabic = '$father_name_arabic',
        kun_grandfather_name = '$grandfather_name',
        kun_grandfather_name_arabic = '$grandfather_name_arabic',
        kun_mother_name = '$mother_name',
        kun_mother_name_arabic = '$mother_name_arabic',
        kun_mother_father_name = '$mother_father_name',
        kun_mother_father_name_arabic = '$mother_father_name_arabic',
        kun_passport_type = '$passport_type'
        WHERE kun_id = $kunregid";
    dbQuery($sql_update);
}

// Handle passport page uploads if present (with OCR processing)
if (isset($_FILES['kunPhotos']) && !empty($_FILES['kunPhotos']['tmp_name'][0])) {
    $passportPageResult = processPassportPageUploads($kunregid, $user_id);
    
    // Log the result for debugging
    if (!$passportPageResult['success']) {
        error_log('Passport page upload failed: ' . $passportPageResult['message']);
    }
}

// Handle passport photo upload if present (50mm x 50mm)
if (isset($_FILES['passport_photo']) && !empty($_FILES['passport_photo']['tmp_name'])) {
    $passportPhotoResult = processPassportPhotoUpload($kunregid, $user_id);
    
    // Log the result for debugging
    if (!$passportPhotoResult['success']) {
        error_log('Passport photo upload failed: ' . $passportPhotoResult['message']);
    }
}

header("Location: " . $server_link . "safars/kun-safar/$safarid");

/*
    Process passport photo upload during form submission
*/
function processPassportPhotoUpload($kunregid, $user_id) {
    try {
        // Validate input parameters
        if (!$kunregid || !$user_id) {
            return ['success' => false, 'message' => 'Invalid registration ID or user ID'];
        }
        
        // Validate uploaded file
        if (!isset($_FILES['passport_photo']) || $_FILES['passport_photo']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No passport photo file uploaded'];
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
        error_log('Passport photo upload error in update-kun-registration.php: ' . $e->getMessage());
        
        // Clean up any temporary files
        cleanupTempPassportFiles();
        
        return ['success' => false, 'message' => 'An error occurred during passport photo upload'];
    }
}

/*
    Process multiple passport page uploads with OCR extraction
    Integrates with existing passport_upload_handler.php functionality
*/
function processPassportPageUploads($kunregid, $user_id) {
    try {
        // Validate input parameters
        if (!$kunregid || !$user_id) {
            return ['success' => false, 'message' => 'Invalid registration ID or user ID'];
        }
        
        // Validate uploaded files
        if (!isset($_FILES['kunPhotos']) || !is_array($_FILES['kunPhotos']['name'])) {
            return ['success' => false, 'message' => 'No passport page files uploaded'];
        }
        
        $fileCount = count($_FILES['kunPhotos']['name']);
        if ($fileCount == 0) {
            return ['success' => false, 'message' => 'No files to process'];
        }
        
        // Initialize response structure
        $response = [
            'success' => true,
            'message' => 'Passport page upload processing completed',
            'data' => [
                'total_files' => $fileCount,
                'processed_files' => 0,
                'failed_files' => 0,
                'file_results' => [],
                'ocr_data' => null
            ],
            'errors' => []
        ];
        
        // Process each uploaded file
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
                
                // Upload file using shared helper
                require_once __DIR__ . '/common/functions.php';
                $uploadResult = passportPageUploadFile($file, SRV_ROOT . 'images/uploads/');
                if (!$uploadResult['success']) {
                    throw new Exception($uploadResult['error']);
                }
                
                $fileResult['filename'] = $uploadResult['filename'];
                
                // Process image file using shared helper
                $processResult = processPassportPageImage(SRV_ROOT . 'images/uploads/' . $uploadResult['filename'], $uploadResult['filename']);
                if (!$processResult['success']) {
                    throw new Exception($processResult['error']);
                }
                
                $fileResult['filename'] = $processResult['processed_filename'];
                
                // Validate image quality
                $qualityResult = validatePassportImageQuality(SRV_ROOT . 'images/uploads/' . $processResult['processed_filename']);
                $fileResult['quality_validation'] = $qualityResult;
                
                // Quality gating - only proceed if quality passes
                if (isset($qualityResult['error']) || (isset($qualityResult['issues']) && !empty($qualityResult['issues']))) {
                    throw new Exception('Image quality issues: ' . implode('; ', $qualityResult['issues'] ?? [$qualityResult['error']]));
                }
                
                // Determine photo type (always passport_page per requirement)
                $photoType = 'passport_page';
                $fileResult['photo_type'] = $photoType;
                
                // Extract OCR data for passport_info (first file)
                $ocrData = [];
                if ($isFirstFile) {
                    $ocrResult = runPassportOcrCommon(SRV_ROOT . 'images/uploads/' . $processResult['processed_filename']);
                    if ($ocrResult['success']) {
                        $fileResult['ocr_data'] = $ocrResult['data'];
                        $ocrData = $ocrResult['data'];
                        $response['data']['ocr_data'] = $ocrData;
                    } else {
                        $fileResult['ocr_data'] = ['error' => $ocrResult['error']];
                    }
                }
                
                // Insert photo record into database
                $dbResult = insertPassportPhotoRecord($kunregid, $processResult['processed_filename'], $photoType, $ocrData);
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
                if (isset($uploadResult['filename']) && file_exists(SRV_ROOT . 'images/uploads/' . $uploadResult['filename'])) {
                    unlink(SRV_ROOT . 'images/uploads/' . $uploadResult['filename']);
                }
            }
            
            $response['data']['file_results'][] = $fileResult;
        }
        
        // Determine overall success
        if ($response['data']['failed_files'] > 0) {
            $response['success'] = false;
            $response['message'] = 'Some passport page files failed to process';
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Log error for debugging
        error_log('Passport page upload error in update-kun-registration.php: ' . $e->getMessage());
        
        return [
            'success' => false, 
            'message' => 'An error occurred during passport page upload processing',
            'error' => $e->getMessage()
        ];
    }
}

/*
    Insert photo record into database with proper photo type
*/
function insertPassportPhotoRecord($kunId, $filename, $photoType, $ocrData = []) {
    require_once __DIR__ . '/common/functions.php';
    return insertKunPhotoRecord($kunId, $filename, $photoType, $ocrData);
}

?>