<?php
require_once 'common/config.php';
require_once 'common/database.php';
require_once 'common/functions.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$its = $input['its'] ?? '';

if (empty($its)) {
    echo json_encode(['found' => false, 'error' => 'ITS number required']);
    exit;
}

// Sanitize the ITS input to prevent SQL injection
global $dbConn;
$its = mysqli_real_escape_string($dbConn, $its);

// Check for previous applications by ITS number
$sql_prev = "SELECT * FROM zyt_kun_registrations 
             WHERE kun_its = '$its' AND kun_ppno != '' AND kun_ppno IS NOT NULL 
             ORDER BY kun_lastupdate DESC LIMIT 1";

$result = dbQuery($sql_prev);

if (dbNumRows($result) > 0) {
    $previous_app = dbFetchAssoc($result);
    
    // Check if passport expires within 6 months
    $passport_valid = true;
    $months_diff = 0;
    
    if (!empty($previous_app['kun_ppexpiry']) && $previous_app['kun_ppexpiry'] !== '0000-00-00') {
        $expiry_date = new DateTime($previous_app['kun_ppexpiry']);
        $current_date = new DateTime();
        $interval = $current_date->diff($expiry_date);
        $months_diff = ($interval->y * 12) + $interval->m;
        
        if ($months_diff <= 6) {
            $passport_valid = false;
        }
    }
    
    // Generate comparison data for enhanced UI
    $comparison_data = getFieldMappings($previous_app);
    
    echo json_encode([
        'found' => true,
        'passport_valid' => $passport_valid,
        'application' => $previous_app,
        'comparison_data' => $comparison_data,
        'field_mappings' => getFieldMappings($previous_app, true),
        'months_until_expiry' => $months_diff
    ]);
} else {
    echo json_encode(['found' => false]);
}

/**
 * Generate field mappings for data comparison
 * @param array $data Previous application data
 * @param bool $mapping_only Return only field mappings without values
 * @return array Field mappings with values and metadata
 */
function getFieldMappings($data, $mapping_only = false) {
    $field_mappings = [
        'ppno' => ['label' => 'Passport Number', 'type' => 'text', 'required' => true],
        'ppexpiry' => ['label' => 'Passport Expiry', 'type' => 'date', 'required' => true],
        'fname' => ['label' => 'First Name', 'type' => 'text', 'required' => true],
        'mname' => ['label' => 'Middle Name', 'type' => 'text', 'required' => false],
        'lname' => ['label' => 'Last Name', 'type' => 'text', 'required' => true],
        'dob' => ['label' => 'Date of Birth', 'type' => 'date', 'required' => true],
        'nationality' => ['label' => 'Nationality', 'type' => 'select', 'required' => true],
        'gender' => ['label' => 'Gender', 'type' => 'select', 'required' => true],
        'address' => ['label' => 'Address', 'type' => 'textarea', 'required' => false],
        'contact' => ['label' => 'Contact Number', 'type' => 'tel', 'required' => true],
        'email' => ['label' => 'Email Address', 'type' => 'email', 'required' => true],
        'emergency_contact' => ['label' => 'Emergency Contact', 'type' => 'tel', 'required' => false],
        'emergency_name' => ['label' => 'Emergency Contact Name', 'type' => 'text', 'required' => false],
        'medical_conditions' => ['label' => 'Medical Conditions', 'type' => 'textarea', 'required' => false],
        'medications' => ['label' => 'Current Medications', 'type' => 'textarea', 'required' => false],
        'allergies' => ['label' => 'Allergies', 'type' => 'textarea', 'required' => false],
        'dietary_requirements' => ['label' => 'Dietary Requirements', 'type' => 'textarea', 'required' => false],
        'special_needs' => ['label' => 'Special Needs', 'type' => 'textarea', 'required' => false],
        'previous_ziyarat' => ['label' => 'Previous Ziyarat Experience', 'type' => 'textarea', 'required' => false],
        'occupation' => ['label' => 'Occupation', 'type' => 'text', 'required' => false],
        'employer' => ['label' => 'Employer', 'type' => 'text', 'required' => false],
        'marital_status' => ['label' => 'Marital Status', 'type' => 'select', 'required' => false],
        'spouse_name' => ['label' => 'Spouse Name', 'type' => 'text', 'required' => false],
        'children_count' => ['label' => 'Number of Children', 'type' => 'number', 'required' => false],
        'passport_arabic_name' => ['label' => 'Passport Arabic Name', 'type' => 'text', 'required' => false],
        'father_name' => ['label' => 'Father Name', 'type' => 'text', 'required' => false],
        'mother_name' => ['label' => 'Mother Name', 'type' => 'text', 'required' => false],
        'place_of_birth' => ['label' => 'Place of Birth', 'type' => 'text', 'required' => false],
        'religion' => ['label' => 'Religion', 'type' => 'text', 'required' => false],
        'education_level' => ['label' => 'Education Level', 'type' => 'select', 'required' => false],
        'language_spoken' => ['label' => 'Languages Spoken', 'type' => 'text', 'required' => false],
        'travel_history' => ['label' => 'Travel History', 'type' => 'textarea', 'required' => false],
        'visa_history' => ['label' => 'Visa History', 'type' => 'textarea', 'required' => false],
        'criminal_record' => ['label' => 'Criminal Record', 'type' => 'select', 'required' => false],
        'deportation_history' => ['label' => 'Deportation History', 'type' => 'select', 'required' => false],
        'financial_support' => ['label' => 'Financial Support', 'type' => 'select', 'required' => false],
        'bank_statement' => ['label' => 'Bank Statement Available', 'type' => 'select', 'required' => false],
        'employment_letter' => ['label' => 'Employment Letter Available', 'type' => 'select', 'required' => false],
        'family_ties' => ['label' => 'Family Ties', 'type' => 'textarea', 'required' => false],
        'purpose_of_visit' => ['label' => 'Purpose of Visit', 'type' => 'textarea', 'required' => false],
        'accommodation_details' => ['label' => 'Accommodation Details', 'type' => 'textarea', 'required' => false],
        'travel_insurance' => ['label' => 'Travel Insurance', 'type' => 'select', 'required' => false],
        'insurance_provider' => ['label' => 'Insurance Provider', 'type' => 'text', 'required' => false],
        'insurance_policy_number' => ['label' => 'Insurance Policy Number', 'type' => 'text', 'required' => false],
        'emergency_contact_relationship' => ['label' => 'Emergency Contact Relationship', 'type' => 'text', 'required' => false],
        'emergency_contact_address' => ['label' => 'Emergency Contact Address', 'type' => 'textarea', 'required' => false],
        'consent_data_processing' => ['label' => 'Data Processing Consent', 'type' => 'checkbox', 'required' => true],
        'consent_medical_info' => ['label' => 'Medical Information Consent', 'type' => 'checkbox', 'required' => false],
        'consent_emergency_contact' => ['label' => 'Emergency Contact Consent', 'type' => 'checkbox', 'required' => false],
        'consent_photo_video' => ['label' => 'Photo/Video Consent', 'type' => 'checkbox', 'required' => false],
        'consent_publicity' => ['label' => 'Publicity Consent', 'type' => 'checkbox', 'required' => false],
        'consent_terms_conditions' => ['label' => 'Terms & Conditions Consent', 'type' => 'checkbox', 'required' => true],
        'additional_info' => ['label' => 'Additional Information', 'type' => 'textarea', 'required' => false],
        'notes' => ['label' => 'Notes', 'type' => 'textarea', 'required' => false]
    ];
    
    if ($mapping_only) {
        return $field_mappings;
    }
    
    $comparison_data = [];
    foreach ($field_mappings as $field => $config) {
        $db_field = 'kun_' . $field;
        $value = isset($data[$db_field]) ? $data[$db_field] : '';
        
        // Normalize nationality values to match select option values
        if ($field === 'nationality' && $value) {
            $value = normalizeNationalityValue($value);
        }
        
        // Calculate confidence based on data completeness and validity
        $confidence = calculateDataConfidence($field, $value, $data);
        
        $comparison_data[$field] = [
            'value' => $value,
            'source' => 'previous',
            'confidence' => $confidence,
            'label' => $config['label'],
            'type' => $config['type'],
            'required' => $config['required'],
            'last_updated' => isset($data['kun_lastupdate']) ? $data['kun_lastupdate'] : null
        ];
    }
    
    return $comparison_data;
}

/**
 * Calculate confidence score for data quality
 * @param string $field Field name
 * @param mixed $value Field value
 * @param array $data Complete data array
 * @return int Confidence score (0-100)
 */
function calculateDataConfidence($field, $value, $data) {
    $confidence = 0;
    
    // Base confidence for having a value
    if (!empty($value) && $value !== '0000-00-00' && $value !== '0') {
        $confidence += 50;
    }
    
    // Additional confidence for critical fields
    $critical_fields = ['ppno', 'fname', 'lname', 'dob', 'nationality', 'gender'];
    if (in_array($field, $critical_fields)) {
        $confidence += 30;
    }
    
    // Additional confidence for recent data
    if (isset($data['kun_lastupdate']) && !empty($data['kun_lastupdate'])) {
        $last_update = new DateTime($data['kun_lastupdate']);
        $current_date = new DateTime();
        $days_diff = $current_date->diff($last_update)->days;
        
        if ($days_diff <= 30) {
            $confidence += 20;
        } elseif ($days_diff <= 90) {
            $confidence += 10;
        }
    }
    
    // Cap at 100
    return min($confidence, 100);
}
/**
 * Normalize nationality values to match select option values
 * Maps ISO-3 codes and common variations to standard nationality labels
 */
function normalizeNationalityValue($nationality) {
    // Nationality mapping from ISO-3 codes to nationality labels
    $nationalityMapping = [
        'AFG' => 'Afghan', 'ALB' => 'Albanian', 'DZA' => 'Algerian', 'USA' => 'American', 'ARG' => 'Argentinian', 
        'AUS' => 'Australian', 'AUT' => 'Austrian', 'BHR' => 'Bahraini', 'BGD' => 'Bangladeshi', 'BRB' => 'Barbadian', 
        'BWA' => 'Batswana', 'BEL' => 'Belgian', 'BOL' => 'Bolivian', 'BRA' => 'Brazilian', 'GBR' => 'British', 
        'BGR' => 'Bulgarian', 'KHM' => 'Cambodian', 'CMR' => 'Cameroonian', 'CAN' => 'Canadian', 'CHL' => 'Chilean', 
        'CHN' => 'Chinese', 'COL' => 'Colombian', 'COM' => 'Comoros', 'CRI' => 'Costa Rican', 'HRV' => 'Croatian', 
        'CUB' => 'Cuban', 'CYP' => 'Cyprus', 'CZE' => 'Czech', 'DNK' => 'Danish', 'DEU' => 'Deutsch', 
        'DOM' => 'Dominican', 'NLD' => 'Dutch', 'ECU' => 'Ecuadorian', 'EGY' => 'Egyptian', 'ARE' => 'Emirati', 
        'EST' => 'Estonian', 'ETH' => 'Ethiopian', 'FJI' => 'Fijian', 'FIN' => 'Finnish', 'FRA' => 'French', 
        'DEU' => 'German', 'GHA' => 'Ghanaian', 'GRC' => 'Greek', 'GTM' => 'Guatemalan', 'HTI' => 'Haitian', 
        'HND' => 'Honduran', 'HUN' => 'Hungarian', 'ISL' => 'Icelandic', 'IND' => 'Indian', 'IDN' => 'Indonesian', 
        'IRN' => 'Iranian', 'IRL' => 'Irish', 'ISR' => 'Israeli', 'ITA' => 'Italian', 'JAM' => 'Jamaican', 
        'JPN' => 'Japanese', 'JOR' => 'Jordanian', 'KEN' => 'Kenyan', 'KOR' => 'Korean', 'KWT' => 'Kuwaiti', 
        'LAO' => 'Lao', 'LVA' => 'Latvian', 'LBN' => 'Lebanese', 'LBY' => 'Libyan', 'LTU' => 'Lithuanian', 
        'LUX' => 'Luxembourg', 'MDG' => 'Malagasy', 'MYS' => 'Malaysian', 'MLI' => 'Malian', 'MLT' => 'Maltese', 
        'MUS' => 'Mauritius', 'MEX' => 'Mexican', 'MNG' => 'Mongolian', 'MAR' => 'Moroccan', 'MOZ' => 'Mozambican', 
        'MMR' => 'Myanmar', 'NAM' => 'Namibian', 'NLD' => 'Nederlandse', 'NPL' => 'Nepalese', 'KNA' => 'Nevis', 
        'NZL' => 'New Zealand', 'NIC' => 'Nicaraguan', 'NGA' => 'Nigerian', 'NOR' => 'Norwegian', 'OMN' => 'Omani', 
        'PAK' => 'Pakistani', 'PAN' => 'Panamanian', 'PRY' => 'Paraguayan', 'PER' => 'Peruvian', 'PHL' => 'Philippine', 
        'POL' => 'Polish', 'PRT' => 'Portuguese', 'ROU' => 'Romanian', 'RUS' => 'Russian', 'SLV' => 'Salvadorian', 
        'SAU' => 'Saudi', 'SCT' => 'Scottish', 'SEN' => 'Senegalese', 'SRB' => 'Serbian', 'SGP' => 'Singaporean', 
        'SVK' => 'Slovak', 'ZAF' => 'South African', 'KOR' => 'South Korean', 'ESP' => 'Spanish', 'LKA' => 'Sri Lankan', 
        'KNA' => 'St Kitts', 'SDN' => 'Sudanese', 'SWE' => 'Swedish', 'CHE' => 'Swiss', 'SYR' => 'Syrian', 
        'TWN' => 'Taiwanese', 'TJK' => 'Tajikistani', 'TZA' => 'Tanzanian', 'THA' => 'Thai', 'TON' => 'Tongan', 
        'TUN' => 'Tunisian', 'TUR' => 'Turkish', 'UGA' => 'Ugandan', 'UKR' => 'Ukrainian', 'URY' => 'Uruguayan', 
        'VEN' => 'Venezuelan', 'VNM' => 'Vietnamese', 'WLS' => 'Welsh', 'YEM' => 'Yemeni', 'ZMB' => 'Zambian', 
        'ZWE' => 'Zimbabwean'
    ];
    
    // Check if it's already a valid nationality label
    if (in_array($nationality, $nationalityMapping)) {
        return $nationality;
    }
    
    // Try to map from ISO code
    if (isset($nationalityMapping[$nationality])) {
        return $nationalityMapping[$nationality];
    }
    
    // Try case-insensitive matching
    foreach ($nationalityMapping as $code => $label) {
        if (strcasecmp($nationality, $code) === 0 || strcasecmp($nationality, $label) === 0) {
            return $label;
        }
    }
    
    // Return original value if no mapping found
    return $nationality;
}
