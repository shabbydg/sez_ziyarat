<?php
/**
 * Review Auto-Created Registration
 * 
 * Displays auto-created registration for user review and confirmation
 * User can either confirm or edit the registration
 */

$kun_id = isset($_GET['kun_id']) ? intval($_GET['kun_id']) : 0;
$safar_id = isset($_GET['safar']) ? intval($_GET['safar']) : 0;

if ($kun_id <= 0) {
    header("Location: " . $server_link);
    exit;
}

// Get registration data
$sql_reg = "SELECT * FROM zyt_kun_registrations WHERE kun_id = $kun_id";
$result_reg = dbQuery($sql_reg);

if (!$result_reg || dbNumRows($result_reg) === 0) {
    header("Location: " . $server_link);
    exit;
}

$registration = dbFetchAssoc($result_reg);
extract($registration);

// Get safar details
$sql_safar = "SELECT safar_name, safar_eta FROM zyt_safar WHERE safar_id = $safar_id";
$safar_result = dbQuery($sql_safar);
$safar = dbFetchAssoc($safar_result);

// Get copied photos
$sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id ORDER BY photo_type";
$result_photos = dbQuery($sql_photos);
$photos = [];
while ($photo = dbFetchAssoc($result_photos)) {
    $photos[] = $photo;
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-light">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars" class="text-brown">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safar_id; ?>" class="text-brown">KUN - <?php echo $safar['safar_name']; ?></a></li>
    <li class="breadcrumb-item active">Review Registration</li>
  </ol>
</nav>

<!-- Success Alert -->
<div class="alert alert-success alert-lg shadow-sm">
    <div class="d-flex align-items-start">
        <i class="fas fa-check-circle fa-3x text-success me-3"></i>
        <div class="flex-grow-1">
            <h4 class="alert-heading"><i class="fas fa-magic"></i> Registration Created Automatically!</h4>
            <p class="mb-2">Great news! We found your previous registration with a valid passport (expires <strong><?php echo formatDateForDisplay($kun_ppexpiry); ?></strong> - <?php 
                require_once 'common/date_helpers.php';
                $validity = getPassportValidityStatus($kun_ppexpiry, $safar['safar_eta']);
                echo $validity['months_remaining'];
            ?> months remaining).</p>
            
            <p class="mb-2"><strong>What we did for you:</strong></p>
            <ul class="mb-2">
                <li><i class="fas fa-check text-success"></i> Copied all registration data from your previous application</li>
                <li><i class="fas fa-check text-success"></i> Linked your passport documents (<?php echo count($photos); ?> photos)</li>
                <li><i class="fas fa-check text-success"></i> Pre-filled all 25 required fields</li>
            </ul>
            
            <p class="mb-0"><strong class="text-danger"><i class="fas fa-exclamation-circle"></i> Action Required:</strong> Please review all information below carefully and confirm, or make changes if needed.</p>
        </div>
    </div>
</div>

<!-- Registration Review Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-brown text-white">
        <h4 class="mb-0"><i class="fas fa-eye"></i> Review Your Registration</h4>
    </div>
    <div class="card-body">
        
        <!-- Section 1: Basic Information -->
        <div class="review-section mb-4">
            <h5 class="text-brown border-bottom pb-2 mb-3">
                <i class="fas fa-user"></i> Basic Information
            </h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="review-field">
                        <label>ITS Number</label>
                        <div class="field-value"><?php echo $kun_its; ?></div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="review-field">
                        <label>Address</label>
                        <div class="field-value"><?php echo $kun_address; ?></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="review-field">
                        <label>Mobile Number</label>
                        <div class="field-value"><?php echo $kun_contact; ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="review-field">
                        <label>Email Address</label>
                        <div class="field-value"><?php echo $kun_email; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Passport Details -->
        <div class="review-section mb-4">
            <h5 class="text-brown border-bottom pb-2 mb-3">
                <i class="fas fa-passport"></i> Passport & Identity Details
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="review-field">
                        <label>Passport Number</label>
                        <div class="field-value"><?php echo $kun_ppno; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-field">
                        <label>Nationality</label>
                        <div class="field-value"><?php echo $kun_nationality; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-field">
                        <label>Passport Type</label>
                        <div class="field-value"><?php echo $kun_passport_type; ?></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="review-field">
                        <label>First Name</label>
                        <div class="field-value"><?php echo $kun_fname; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-field">
                        <label>Middle Name</label>
                        <div class="field-value"><?php echo $kun_mname ?: '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-field">
                        <label>Last Name</label>
                        <div class="field-value"><?php echo $kun_lname; ?></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="review-field">
                        <label>Passport Arabic Name</label>
                        <div class="field-value arabic-text"><?php echo $kun_passport_arabic_name; ?></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="review-field">
                        <label>Issue Date</label>
                        <div class="field-value"><?php echo formatDateForDisplay($kun_ppissue); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="review-field">
                        <label>Expiry Date</label>
                        <div class="field-value">
                            <?php echo formatDateForDisplay($kun_ppexpiry); ?>
                            <span class="badge bg-success ms-2"><?php echo $validity['months_remaining']; ?> months remaining</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="review-field">
                        <label>Place of Issue</label>
                        <div class="field-value"><?php echo $kun_placeofissue; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Personal Details -->
        <div class="review-section mb-4">
            <h5 class="text-brown border-bottom pb-2 mb-3">
                <i class="fas fa-id-card"></i> Personal Details
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="review-field">
                        <label>Date of Birth</label>
                        <div class="field-value"><?php echo formatDateForDisplay($kun_dob); ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="review-field">
                        <label>Age</label>
                        <div class="field-value"><?php echo $kun_age; ?> years</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="review-field">
                        <label>Gender</label>
                        <div class="field-value"><?php echo $kun_gender === 'M' ? 'Male' : 'Female'; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="review-field">
                        <label>Birth Place</label>
                        <div class="field-value"><?php echo $kun_birthplace; ?></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="review-field">
                        <label>Profession</label>
                        <div class="field-value"><?php echo $kun_profession; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Family Names -->
        <div class="review-section mb-4">
            <h5 class="text-brown border-bottom pb-2 mb-3">
                <i class="fas fa-users"></i> Family Names
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">English Names</h6>
                    <div class="review-field mb-2">
                        <label>Father Name</label>
                        <div class="field-value"><?php echo $kun_father_name; ?></div>
                    </div>
                    <div class="review-field mb-2">
                        <label>Grandfather Name</label>
                        <div class="field-value"><?php echo $kun_grandfather_name ?: '-'; ?></div>
                    </div>
                    <div class="review-field mb-2">
                        <label>Mother Name</label>
                        <div class="field-value"><?php echo $kun_mother_name ?: '-'; ?></div>
                    </div>
                    <div class="review-field">
                        <label>Mother's Father Name</label>
                        <div class="field-value"><?php echo $kun_mother_father_name ?: '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success mb-3">Arabic Names</h6>
                    <div class="review-field mb-2">
                        <label>Father Name (Arabic)</label>
                        <div class="field-value arabic-text"><?php echo $kun_father_name_arabic; ?></div>
                    </div>
                    <div class="review-field mb-2">
                        <label>Grandfather Name (Arabic)</label>
                        <div class="field-value arabic-text"><?php echo $kun_grandfather_name_arabic ?: '-'; ?></div>
                    </div>
                    <div class="review-field mb-2">
                        <label>Mother Name (Arabic)</label>
                        <div class="field-value arabic-text"><?php echo $kun_mother_name_arabic ?: '-'; ?></div>
                    </div>
                    <div class="review-field">
                        <label>Mother's Father Name (Arabic)</label>
                        <div class="field-value arabic-text"><?php echo $kun_mother_father_name_arabic ?: '-'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Copied Documents -->
        <?php if (count($photos) > 0): ?>
        <div class="review-section mb-4">
            <h5 class="text-brown border-bottom pb-2 mb-3">
                <i class="fas fa-images"></i> Passport Documents (<?php echo count($photos); ?> files)
            </h5>
            <div class="row">
                <?php foreach ($photos as $photo): 
                    $photoTypeLabel = '';
                    $badgeClass = 'secondary';
                    switch($photo['photo_type']) {
                        case 'passport_info':
                            $photoTypeLabel = 'Passport Info Page';
                            $badgeClass = 'primary';
                            break;
                        case 'passport_page':
                            $photoTypeLabel = 'Additional Page';
                            $badgeClass = 'info';
                            break;
                        case 'passport_photo':
                            $photoTypeLabel = 'Passport Photo';
                            $badgeClass = 'success';
                            break;
                        default:
                            $photoTypeLabel = 'Document';
                    }
                    
                    $thumbnailPath = $photo['photo_filename_thumb'] 
                        ? 'images/uploads/' . $photo['photo_filename_thumb'] 
                        : 'images/uploads/' . $photo['photo_filename'];
                ?>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center p-3">
                            <span class="badge bg-<?php echo $badgeClass; ?> mb-2"><?php echo $photoTypeLabel; ?></span>
                            <img src="<?php echo $server_link . $thumbnailPath; ?>" 
                                 class="img-fluid mb-2" 
                                 alt="<?php echo $photoTypeLabel; ?>"
                                 style="max-height: 150px; object-fit: cover;">
                            <small class="d-block text-muted"><?php echo $photo['photo_filename']; ?></small>
                            <a href="<?php echo $server_link . 'images/uploads/' . $photo['photo_filename']; ?>" 
                               class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                <i class="fas fa-eye"></i> View Full Size
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="text-center mt-5">
            <button type="button" class="btn btn-success btn-lg me-2" onclick="confirmRegistration()">
                <i class="fas fa-check-double"></i> Everything Looks Good - Confirm Registration
            </button>
            <a href="<?php echo $server_link; ?>edit-kun-registration.php?kun_id=<?php echo $kun_id; ?>&safar=<?php echo $safar_id; ?>" 
               class="btn btn-warning btn-lg me-2">
                <i class="fas fa-edit"></i> I Need to Make Changes
            </a>
            <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safar_id; ?>" 
               class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </div>
</div>

<!-- Back to Dashboard -->
<div class="text-center mt-4">
    <a href="<?php echo $server_link; ?>" class="btn btn-outline-brown">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<script>
function confirmRegistration() {
    if (!confirm('Are you sure all information is correct? This will finalize your registration.')) {
        return;
    }
    
    // Update registration status to confirmed
    fetch('<?php echo $server_link; ?>api/confirm-registration.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            kun_id: <?php echo $kun_id; ?>,
            safar_id: <?php echo $safar_id; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success and redirect
            alert('Registration confirmed successfully!');
            window.location.href = '<?php echo $server_link; ?>safars/kun-safar/<?php echo $safar_id; ?>?registration=confirmed';
        } else {
            alert('Error confirming registration: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>

<style>
.alert-lg {
    padding: 1.5rem;
    border-radius: 10px;
    border: none;
}

.review-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.review-field {
    margin-bottom: 1rem;
}

.review-field label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: block;
}

.review-field .field-value {
    font-size: 1.1rem;
    color: #212529;
    padding: 0.5rem;
    background: white;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.arabic-text {
    direction: rtl;
    text-align: right;
    font-family: 'Amiri', 'Scheherazade', 'Tahoma', serif;
}

.badge {
    font-size: 0.85rem;
    padding: 0.35rem 0.65rem;
}
</style>

