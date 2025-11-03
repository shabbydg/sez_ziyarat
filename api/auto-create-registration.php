<?php
/**
 * Auto-Create Registration API
 * 
 * Automatically creates a new registration by copying all data from a previous registration
 * Also copies photo records (references same physical files)
 * Used for returning travelers with valid passports
 */

require_once '../common/config.php';
require_once '../common/functions.php';

header('Content-Type: application/json');

// Response structure
$response = [
    'success' => false,
    'new_kun_id' => null,
    'copied_photos' => 0,
    'source_kun_id' => null,
    'message' => ''
];

try {
    // Get input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $source_kun_id = isset($input['source_kun_id']) ? intval($input['source_kun_id']) : 0;
    $safar_id = isset($input['safar_id']) ? intval($input['safar_id']) : 0;
    $user_id = isset($input['user_id']) ? intval($input['user_id']) : getActiveUser();
    
    if ($source_kun_id <= 0) {
        throw new Exception('Invalid source registration ID');
    }
    
    if ($safar_id <= 0) {
        throw new Exception('Invalid safar ID');
    }
    
    // Step 1: Fetch source registration data
    $sql_source = "SELECT * FROM zyt_kun_registrations WHERE kun_id = $source_kun_id";
    $sourceResult = dbQuery($sql_source);
    
    if (!$sourceResult || dbNumRows($sourceResult) === 0) {
        throw new Exception('Source registration not found');
    }
    
    $sourceData = dbFetchAssoc($sourceResult);
    
    // Step 2: Create new registration with all fields copied
    // Using INSERT SELECT for efficiency
    $sql_insert = "INSERT INTO zyt_kun_registrations 
        (safar_id, kun_its, kun_fname, kun_lname, kun_mname, kun_ppno, 
         kun_ppexpiry, kun_ppissue, kun_placeofissue, kun_dob, kun_gender,
         kun_nationality, kun_birthplace, kun_address, kun_contact, kun_email,
         kun_passport_type, kun_profession, kun_father_name, kun_grandfather_name,
         kun_mother_name, kun_mother_father_name, kun_passport_arabic_name,
         kun_father_name_arabic, kun_grandfather_name_arabic, kun_mother_name_arabic,
         kun_mother_father_name_arabic, kun_fname_arabic, kun_mname_arabic, kun_lname_arabic,
         kun_age, kun_status, kun_created_date, kun_created_by)
    SELECT $safar_id, kun_its, kun_fname, kun_lname, kun_mname, kun_ppno,
           kun_ppexpiry, kun_ppissue, kun_placeofissue, kun_dob, kun_gender,
           kun_nationality, kun_birthplace, kun_address, kun_contact, kun_email,
           kun_passport_type, kun_profession, kun_father_name, kun_grandfather_name,
           kun_mother_name, kun_mother_father_name, kun_passport_arabic_name,
           kun_father_name_arabic, kun_grandfather_name_arabic, kun_mother_name_arabic,
           kun_mother_father_name_arabic, kun_fname_arabic, kun_mname_arabic, kun_lname_arabic,
           kun_age, 'pending', NOW(), $user_id
    FROM zyt_kun_registrations
    WHERE kun_id = $source_kun_id";
    
    $insertResult = dbQuery($sql_insert);
    
    if (!$insertResult) {
        throw new Exception('Failed to create new registration: ' . mysqli_error($dbConn));
    }
    
    // Get new kun_id
    $new_kun_id = dbInsertId();
    $response['new_kun_id'] = $new_kun_id;
    $response['source_kun_id'] = $source_kun_id;
    
    // Step 3: Copy photo records (reference same physical files, don't duplicate)
    $sql_photos = "INSERT INTO zyt_kun_photos 
        (kun_id, photo_filename, photo_filename_thumb, photo_type, 
         photo_upload_time, photo_user_id)
    SELECT $new_kun_id, photo_filename, photo_filename_thumb, photo_type, 
           NOW(), $user_id
    FROM zyt_kun_photos
    WHERE kun_id = $source_kun_id";
    
    $photosResult = dbQuery($sql_photos);
    
    if ($photosResult) {
        // Count how many photos were copied
        $sql_count = "SELECT COUNT(*) as count FROM zyt_kun_photos WHERE kun_id = $new_kun_id";
        $countResult = dbQuery($sql_count);
        $countData = dbFetchAssoc($countResult);
        $response['copied_photos'] = $countData ? intval($countData['count']) : 0;
    }
    
    // Step 4: Log the auto-creation for audit trail
    $log_message = "Auto-created registration KUN ID $new_kun_id from source $source_kun_id for ITS {$sourceData['kun_its']} - Safar ID $safar_id";
    error_log($log_message);
    
    $response['success'] = true;
    $response['message'] = "Registration created successfully from previous data. Copied {$response['copied_photos']} photos.";
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Auto-create registration error: " . $e->getMessage());
}

echo json_encode($response);
?>

