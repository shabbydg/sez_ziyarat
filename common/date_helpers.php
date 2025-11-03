<?php
/**
 * Date Helper Functions
 * 
 * Utility functions for date calculations, passport validity checks,
 * and date format conversions
 */

/**
 * Calculate months between two dates
 * 
 * @param string $date1 First date (YYYY-MM-DD)
 * @param string $date2 Second date (YYYY-MM-DD)
 * @return int Number of months between dates
 */
function monthsBetweenDates($date1, $date2) {
    try {
        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);
        $interval = $d1->diff($d2);
        
        // Calculate total months
        $months = ($interval->y * 12) + $interval->m;
        
        // Add extra month if day difference is positive
        if ($interval->d > 0) {
            $months++;
        }
        
        return abs($months);
    } catch (Exception $e) {
        error_log("Error calculating months between dates: " . $e->getMessage());
        return 0;
    }
}

/**
 * Check if passport is valid for minimum required months
 * 
 * @param string $expiryDate Passport expiry date (YYYY-MM-DD)
 * @param string $referenceDate Reference date to check against (default: travel date or today+6 months)
 * @param int $minMonths Minimum months required (default: 6)
 * @return bool True if passport is valid
 */
function isPassportValid($expiryDate, $referenceDate = null, $minMonths = 6) {
    if (empty($expiryDate)) {
        return false;
    }
    
    // Default to 6 months from today if no reference date
    if ($referenceDate === null) {
        $referenceDate = date('Y-m-d', strtotime('+6 months'));
    }
    
    try {
        $expiry = new DateTime($expiryDate);
        $reference = new DateTime($referenceDate);
        
        // Check if expiry is after reference date
        if ($expiry <= $reference) {
            return false;
        }
        
        // Calculate months remaining
        $monthsRemaining = monthsBetweenDates($expiryDate, $referenceDate);
        
        return $monthsRemaining > $minMonths;
        
    } catch (Exception $e) {
        error_log("Error validating passport: " . $e->getMessage());
        return false;
    }
}

/**
 * Get passport validity status with detailed information
 * 
 * @param string $expiryDate Passport expiry date
 * @param string $referenceDate Reference date (travel date or today)
 * @return array Validity status and details
 */
function getPassportValidityStatus($expiryDate, $referenceDate = null) {
    $status = [
        'valid' => false,
        'months_remaining' => 0,
        'days_remaining' => 0,
        'expiry_date' => $expiryDate,
        'reference_date' => $referenceDate,
        'message' => ''
    ];
    
    if (empty($expiryDate)) {
        $status['message'] = 'No expiry date provided';
        return $status;
    }
    
    if ($referenceDate === null) {
        $referenceDate = date('Y-m-d', strtotime('+6 months'));
    }
    
    $status['reference_date'] = $referenceDate;
    
    try {
        $expiry = new DateTime($expiryDate);
        $reference = new DateTime($referenceDate);
        $today = new DateTime();
        
        // Check if already expired
        if ($expiry <= $today) {
            $status['message'] = 'Passport has expired';
            return $status;
        }
        
        // Calculate months and days remaining
        $interval = $reference->diff($expiry);
        $status['months_remaining'] = ($interval->y * 12) + $interval->m;
        $status['days_remaining'] = $interval->days;
        
        // Check if valid for minimum 6 months
        if ($status['months_remaining'] > 6) {
            $status['valid'] = true;
            $status['message'] = "Passport valid for {$status['months_remaining']} months";
        } else {
            $status['message'] = "Passport expires within 6 months ({$status['months_remaining']} months remaining)";
        }
        
    } catch (Exception $e) {
        $status['message'] = 'Error parsing dates: ' . $e->getMessage();
    }
    
    return $status;
}

/**
 * Format date for display
 * 
 * @param string $date Date in YYYY-MM-DD format
 * @param string $format Output format (default: 'd M Y')
 * @return string Formatted date or empty string
 */
function formatDateForDisplay($date, $format = 'd M Y') {
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    
    try {
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Convert date from various formats to YYYY-MM-DD
 * 
 * @param string $dateString Date in any format
 * @return string Date in YYYY-MM-DD format or null
 */
function normalizeDateFormat($dateString) {
    if (empty($dateString)) {
        return null;
    }
    
    // Already in correct format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
        return $dateString;
    }
    
    // Try common formats
    $formats = [
        'd/m/Y', 'd-m-Y', 'd.m.Y',
        'm/d/Y', 'm-d-Y', 'm.d.Y',
        'Y/m/d', 'Y.m.d',
        'd M Y', 'd F Y'
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $dateString);
        if ($date) {
            return $date->format('Y-m-d');
        }
    }
    
    return null;
}

/**
 * Calculate age from date of birth
 * 
 * @param string $dob Date of birth (YYYY-MM-DD)
 * @param string $asOf Calculate age as of this date (default: today)
 * @return int Age in years
 */
function calculateAge($dob, $asOf = null) {
    if (empty($dob)) {
        return 0;
    }
    
    try {
        $birthDate = new DateTime($dob);
        $asOfDate = $asOf ? new DateTime($asOf) : new DateTime();
        
        $age = $birthDate->diff($asOfDate)->y;
        
        return $age;
    } catch (Exception $e) {
        error_log("Error calculating age: " . $e->getMessage());
        return 0;
    }
}

/**
 * Check if a date is in the past
 * 
 * @param string $date Date to check (YYYY-MM-DD)
 * @return bool True if date is in the past
 */
function isDateInPast($date) {
    if (empty($date)) {
        return false;
    }
    
    try {
        $checkDate = new DateTime($date);
        $today = new DateTime();
        
        return $checkDate < $today;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Check if a date is in the future
 * 
 * @param string $date Date to check (YYYY-MM-DD)
 * @return bool True if date is in the future
 */
function isDateInFuture($date) {
    if (empty($date)) {
        return false;
    }
    
    try {
        $checkDate = new DateTime($date);
        $today = new DateTime();
        
        return $checkDate > $today;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get date with offset
 * 
 * @param int $months Months to add (negative for past)
 * @param string $fromDate Base date (default: today)
 * @return string Date in YYYY-MM-DD format
 */
function getDateWithOffset($months, $fromDate = null) {
    try {
        $date = $fromDate ? new DateTime($fromDate) : new DateTime();
        $date->modify(($months >= 0 ? '+' : '') . $months . ' months');
        return $date->format('Y-m-d');
    } catch (Exception $e) {
        error_log("Error calculating date offset: " . $e->getMessage());
        return date('Y-m-d');
    }
}
?>

