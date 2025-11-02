<?php
//NEW SAFAR SCRIPT
require_once 'nationality_options.php';
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';
$kunregid = (isset($_GET['kunregid']) && $_GET['kunregid'] != '') ? $_GET['kunregid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>

<!-- Modern breadcrumb navigation -->
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-light">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown"><i class="icon-home"></i> Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars" class="text-brown">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="text-brown">KUN - <?php echo $safar_name; ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">Update Registration</li>
  </ol>
</nav>

<?php
$sql_edit = "SELECT * FROM zyt_kun_registrations WHERE kun_id = $kunregid";
$result_edit = dbQuery($sql_edit);
if(dbNumRows($result_edit) > 0){
    while($row_edit = dbFetchAssoc($result_edit)){
        extract($row_edit);
    ?>

<!-- Modern Form Container -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-brown text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1"><i class="icon-edit"></i> Edit Karbala Registration</h3>
                            <small class="opacity-75">Update applicant information</small>
                        </div>
                        <div class="text-right">
                            <small class="d-block">Safar: <?php echo $safar_name; ?></small>
                            <small class="d-block">ID: <?php echo $kunregid; ?></small>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-brown-light" role="progressbar" style="width: 0%" id="form-progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Error Alert -->
                    <?php if(isset($_GET['e'])){ ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error!</strong> Something went wrong. Please check the date format (yyyy-mm-dd) and try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php } ?>

                    <form action="<?php echo $server_link; ?>update-kun-registration.php" method="post" id="kun-registration" novalidate>
                        <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
                        <input type="hidden" name="kunregid" value="<?php echo $kunregid; ?>" />

                        <!-- Section 0: Passport Upload -->
                        <div class="form-section mb-4" id="section-passport-upload">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-upload text-primary"></i> Passport Upload & OCR
                                </h4>
                                <p class="text-muted small mb-3">Upload passport pages for automatic data extraction</p>
                            </div>

                            <!-- Upload Interface -->
                            <div class="upload-container mb-4">
                                <div class="upload-area" id="passport-upload-area">
                                    <div class="upload-content text-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-brown mb-3"></i>
                                        <h5 class="text-brown mb-2">Upload Passport Pages</h5>
                                        <p class="text-muted mb-3">
                                            Drag & drop passport images here or click to browse<br>
                                            <small class="text-info">
                                                <i class="fas fa-info-circle"></i> Upload information page first for best results
                                            </small>
                                        </p>
                                        <input type="file" id="passport-files" name="passport_files[]" multiple 
                                               accept="image/jpeg,image/png,image/jpg" class="d-none">
                                        <button type="button" class="btn btn-outline-brown" onclick="document.getElementById('passport-files').click()">
                                            <i class="fas fa-folder-open"></i> Choose Files
                                        </button>
                                    </div>
                                </div>

                                <!-- File Preview Area -->
                                <div id="file-preview-container" class="mt-3" style="display: none;">
                                    <h6 class="text-brown mb-3">
                                        <i class="fas fa-images"></i> Uploaded Files
                                    </h6>
                                    <div id="file-preview-list" class="row"></div>
                                </div>

                                <!-- Upload Progress -->
                                <div id="upload-progress" class="mt-3" style="display: none;">
                                    <div class="progress mb-2">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-brown-light" 
                                             role="progressbar" style="width: 0%" id="upload-progress-bar"></div>
                                    </div>
                                    <small class="text-muted" id="upload-status">Preparing upload...</small>
                                </div>
                            </div>

                            <!-- OCR Data Preview -->
                            <div id="ocr-preview-container" class="mt-4" style="display: none;">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-brown text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye"></i> Extracted Passport Data
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-lightbulb"></i>
                                            <strong>Review & Accept:</strong> Please review the extracted data below and accept or reject each field.
                                        </div>

                                        <div id="ocr-data-table" class="table-responsive">
                                            <!-- OCR data will be populated here -->
                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-success btn-sm me-2" id="accept-all-ocr">
                                                <i class="fas fa-check-circle"></i> Accept All
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm me-2" id="reject-all-ocr">
                                                <i class="fas fa-times-circle"></i> Reject All
                                            </button>
                                            <button type="button" class="btn btn-outline-brown btn-sm" id="clear-ocr-data">
                                                <i class="fas fa-trash"></i> Clear Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quality Validation Feedback -->
                            <div id="quality-feedback" class="mt-3" style="display: none;">
                                <!-- Quality validation results will be shown here -->
                            </div>

                            <!-- Data Comparison Interface -->
                            <div id="data-comparison-container" class="mt-4" style="display: none;">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-balance-scale"></i> Data Comparison & Selection
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Compare & Choose:</strong> Review data from previous application, OCR extraction, and current form. Select the best data source for each field.
                                        </div>

                                        <!-- Comparison Summary -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body text-center py-2">
                                                        <h6 class="mb-1">Previous Data</h6>
                                                        <small>From last application</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-warning text-dark">
                                                    <div class="card-body text-center py-2">
                                                        <h6 class="mb-1">OCR Data</h6>
                                                        <small>From passport scan</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body text-center py-2">
                                                        <h6 class="mb-1">Current Form</h6>
                                                        <small>Already entered</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bulk Actions -->
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success btn-sm me-2" id="accept-all-previous">
                                                <i class="fas fa-check-circle"></i> Accept All Previous
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm me-2" id="accept-all-comparison">
                                                <i class="fas fa-check-circle"></i> Accept All OCR
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm me-2" id="keep-all-current">
                                                <i class="fas fa-check-circle"></i> Keep All Current
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-comparison-data">
                                                <i class="fas fa-trash"></i> Clear All
                                            </button>
                                        </div>

                                        <!-- Comparison Table -->
                                        <div id="comparison-data-table" class="table-responsive">
                                            <!-- Comparison data will be populated here -->
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-primary btn-lg me-2" id="apply-selected-data">
                                                <i class="fas fa-check-double"></i> Apply Selected Data
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-lg" id="cancel-comparison">
                                                <i class="fas fa-times"></i> Cancel Comparison
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 0.5: Passport Photo Upload -->
                        <div class="form-section mb-4" id="section-passport-photo">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-camera text-warning"></i> Passport Photo Upload
                                </h4>
                                <p class="text-muted small mb-3">Upload a square passport photo (50mm x 50mm) for document generation</p>
                            </div>

                            <!-- Upload Interface -->
                            <div class="upload-container mb-4">
                                <div class="upload-area" id="passport-photo-upload-area">
                                    <div class="upload-content text-center">
                                        <i class="fas fa-camera fa-3x text-brown mb-3"></i>
                                        <h5 class="text-brown mb-2">Upload Passport Photo</h5>
                                        <p class="text-muted mb-3">
                                            Upload a clear photo for cropping to 50mm x 50mm<br>
                                            <small class="text-info">
                                                <i class="fas fa-info-circle"></i> Supported formats: JPG, PNG, GIF (Max 5MB)
                                            </small>
                                        </p>
                                        <input type="file" id="passport-photo-file" name="passport_photo" 
                                               accept="image/jpeg,image/png,image/jpg,image/gif" class="d-none">
                                        <button type="button" class="btn btn-outline-brown" onclick="document.getElementById('passport-photo-file').click()">
                                            <i class="fas fa-folder-open"></i> Choose Photo
                                        </button>
                                    </div>
                                </div>

                                <!-- Cropping Interface -->
                                <div id="passport-photo-cropper" class="mt-4" style="display: none;">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-crop"></i> Crop Passport Photo
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="cropping-container">
                                                        <canvas id="passport-photo-canvas" class="border rounded" style="max-width: 100%; height: auto;"></canvas>
                                                        <div class="crop-overlay" id="crop-overlay">
                                                            <div class="crop-box" id="crop-box">
                                                                <div class="crop-handle crop-handle-nw"></div>
                                                                <div class="crop-handle crop-handle-ne"></div>
                                                                <div class="crop-handle crop-handle-sw"></div>
                                                                <div class="crop-handle crop-handle-se"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="cropping-controls">
                                                        <h6 class="text-brown mb-3">Crop Controls</h6>
                                                        
                                                        <!-- Zoom Controls -->
                                                        <div class="mb-3">
                                                            <label class="form-label small">Zoom</label>
                                                            <div class="d-flex align-items-center">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="zoom-out">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="range" class="form-range mx-2" id="zoom-slider" min="0.5" max="3" step="0.1" value="1">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="zoom-in">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Rotate Controls -->
                                                        <div class="mb-3">
                                                            <label class="form-label small">Rotate</label>
                                                            <div class="d-flex gap-2">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="rotate-left">
                                                                    <i class="fas fa-undo"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="rotate-right">
                                                                    <i class="fas fa-redo"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Preview -->
                                                        <div class="mb-3">
                                                            <label class="form-label small">Preview</label>
                                                            <div class="preview-container border rounded p-2 text-center">
                                                                <canvas id="passport-photo-preview" width="150" height="150" class="border rounded"></canvas>
                                                                <div class="mt-2">
                                                                    <small class="text-muted">1000x1000 pixels</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- File Info -->
                                                        <div class="mb-3">
                                                            <label class="form-label small">File Information</label>
                                                            <div class="file-info">
                                                                <div class="d-flex justify-content-between">
                                                                    <small>Size:</small>
                                                                    <small id="file-size-info">-</small>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <small>Dimensions:</small>
                                                                    <small id="file-dimensions-info">-</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Action Buttons -->
                                                        <div class="d-grid gap-2">
                                                            <button type="button" class="btn btn-primary" id="crop-and-upload">
                                                                <i class="fas fa-check"></i> Crop & Upload
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary" id="cancel-crop">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Progress -->
                                <div id="passport-photo-progress" class="mt-3" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar bg-brown" role="progressbar" style="width: 0%" id="passport-photo-progress-bar"></div>
                                    </div>
                                    <small class="text-muted mt-1">Processing passport photo...</small>
                                </div>

                                <!-- Success/Error Messages -->
                                <div id="passport-photo-messages" class="mt-3"></div>
                            </div>
                        </div>

                        <!-- Section 1: Basic Information -->
                        <div class="form-section mb-4" id="section-basic">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-user text-primary"></i> Basic Information
                                </h4>
                                <p class="text-muted small mb-3">Essential contact and identification details</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="its" class="font-weight-bold">ITS Number <span class="text-danger">*</span></label>
                                        <input type="text" id="its" name="its" class="form-control form-control-lg" 
                                               value="<?php echo $kun_its; ?>" required 
                                               placeholder="Enter 8-digit ITS number">
                                        <small class="form-text text-muted">8-digit identification number</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="address" class="font-weight-bold">Address</label>
                                        <textarea id="address" name="address" class="form-control" rows="3" 
                                                  placeholder="Enter complete address"><?php echo $kun_address; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="contact" class="font-weight-bold">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="tel" id="contact" name="contact" class="form-control" 
                                               value="<?php echo $kun_contact; ?>" required 
                                               placeholder="+94 77 123 4567">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" class="form-control" 
                                               value="<?php echo $kun_email; ?>" required 
                                               placeholder="example@email.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Passport & Identity -->
                        <div class="form-section mb-4" id="section-passport">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-passport text-success"></i> Passport & Identity Details
                                </h4>
                                <p class="text-muted small mb-3">Passport information and travel document details</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="ppno" class="font-weight-bold">Passport Number <span class="text-danger">*</span></label>
                                        <input type="text" id="ppno" name="ppno" class="form-control" 
                                               value="<?php echo $kun_ppno; ?>" required 
                                               placeholder="N1234567" style="text-transform: uppercase;">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="nationality" class="font-weight-bold">Nationality <span class="text-danger">*</span></label>
                                        <select id="nationality" name="nationality" class="form-control" required>
                                            <?php echo generateNationalityOptions($kun_nationality); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="passport_type" class="font-weight-bold">Passport Type <span class="text-danger">*</span></label>
                                        <select id="passport_type" name="passport_type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="Normal" <?php if($kun_passport_type == 'Normal') echo "selected"; ?>>Normal</option>
                                            <option value="Diplomatic" <?php if($kun_passport_type == 'Diplomatic') echo "selected"; ?>>Diplomatic</option>
                                            <option value="Temporary" <?php if($kun_passport_type == 'Temporary') echo "selected"; ?>>Temporary</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="fname" class="font-weight-bold">First Name (as per Passport) <span class="text-danger">*</span></label>
                                        <input type="text" id="fname" name="fname" class="form-control" 
                                               value="<?php echo $kun_fname; ?>" required 
                                               placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="mname" class="font-weight-bold">Middle Name</label>
                                        <input type="text" id="mname" name="mname" class="form-control" 
                                               value="<?php echo $kun_mname; ?>" 
                                               placeholder="Middle Name (optional)">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="lname" class="font-weight-bold">Last Name (as per Passport) <span class="text-danger">*</span></label>
                                        <input type="text" id="lname" name="lname" class="form-control" 
                                               value="<?php echo $kun_lname; ?>" required 
                                               placeholder="Last Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="ppissue" class="font-weight-bold">Passport Issue Date <span class="text-danger">*</span></label>
                                        <input type="date" id="ppissue" name="ppissue" class="form-control" 
                                               value="<?php echo $kun_ppissue; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="ppexpiry" class="font-weight-bold">Passport Expiry Date <span class="text-danger">*</span></label>
                                        <input type="date" id="ppexpiry" name="ppexpiry" class="form-control" 
                                               value="<?php echo $kun_ppexpiry; ?>" required>
                                        <small class="form-text text-info">
                                            <i class="fas fa-info-circle"></i> Must be valid for at least 6 months from today
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="placeofissue" class="font-weight-bold">Place of Issue <span class="text-danger">*</span></label>
                                        <input type="text" id="placeofissue" name="placeofissue" class="form-control" 
                                               value="<?php echo $kun_placeofissue; ?>" required 
                                               placeholder="City, Country">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="passport_arabic_name" class="font-weight-bold">Passport Arabic Name <span class="text-danger">*</span></label>
                                        <input type="text" id="passport_arabic_name" name="passport_arabic_name" 
                                               class="form-control arabic-text" dir="rtl" 
                                               value="<?php echo $kun_passport_arabic_name; ?>" required>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-language"></i> Auto-translated from English name
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Personal Details -->
                        <div class="form-section mb-4" id="section-personal">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-id-card text-info"></i> Personal Details
                                </h4>
                                <p class="text-muted small mb-3">Birth information and professional details</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="dob" class="font-weight-bold">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" id="dob" name="dob" class="form-control" 
                                               value="<?php echo $kun_dob; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="form-group">
                                        <label for="age" class="font-weight-bold">Age <span class="text-danger">*</span></label>
                                        <input type="number" id="age" name="age" class="form-control bg-light" 
                                               value="<?php echo $kun_age; ?>" readonly>
                                        <small class="form-text text-muted">Auto-calculated</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="gender" class="font-weight-bold">Gender <span class="text-danger">*</span></label>
                                        <select id="gender" name="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="M" <?php if($kun_gender == 'M') echo "selected"; ?>>Male</option>
                                            <option value="F" <?php if($kun_gender == 'F') echo "selected"; ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="birthplace" class="font-weight-bold">Birth Place <span class="text-danger">*</span></label>
                                        <input type="text" id="birthplace" name="birthplace" class="form-control" 
                                               value="<?php echo $kun_birthplace; ?>" required 
                                               placeholder="City, Country">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="profession" class="font-weight-bold">Profession <span class="text-danger">*</span></label>
                                        <select id="profession" name="profession" class="form-control" required>
                                            <option value="">Select Profession</option>
                                            <option value="Engineer" <?php if($kun_profession == 'Engineer') echo "selected"; ?>>Engineer</option>
                                            <option value="Physician" <?php if($kun_profession == 'Physician') echo "selected"; ?>>Physician</option>
                                            <option value="Others" <?php if($kun_profession == 'Others') echo "selected"; ?>>Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Family Names (Collapsible) -->
                        <div class="form-section mb-4" id="section-family">
                            <div class="section-header">
                                <h4 class="section-title" data-bs-toggle="collapse" data-bs-target="#family-names" 
                                   style="cursor: pointer;" aria-expanded="true">
                                    <i class="fas fa-users text-warning"></i> Family Names 
                                    <i class="fas fa-chevron-down float-end transition-all"></i>
                                </h4>
                                <p class="text-muted small mb-3">Family member names in English and Arabic</p>
                            </div>
                            
                            <div id="family-names" class="collapse show">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="card bg-light border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary"><i class="fas fa-globe-americas"></i> English Names</h6>
                                                <div class="form-group">
                                                    <label for="father_name" class="font-weight-bold">Father Name</label>
                                                    <input type="text" id="father_name" name="father_name" class="form-control" 
                                                           value="<?php echo $kun_father_name; ?>" 
                                                           placeholder="Father's full name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="grandfather_name" class="font-weight-bold">Grandfather Name</label>
                                                    <input type="text" id="grandfather_name" name="grandfather_name" class="form-control" 
                                                           value="<?php echo $kun_grandfather_name; ?>" 
                                                           placeholder="Grandfather's name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_name" class="font-weight-bold">Mother Name</label>
                                                    <input type="text" id="mother_name" name="mother_name" class="form-control" 
                                                           value="<?php echo $kun_mother_name; ?>" 
                                                           placeholder="Mother's full name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_father_name" class="font-weight-bold">Mother's Father Name</label>
                                                    <input type="text" id="mother_father_name" name="mother_father_name" class="form-control" 
                                                           value="<?php echo $kun_mother_father_name; ?>" 
                                                           placeholder="Maternal grandfather's name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="card bg-light border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="card-title text-success"><i class="fas fa-language"></i> Arabic Names</h6>
                                                <div class="form-group">
                                                    <label for="father_name_arabic" class="font-weight-bold">Father Name (Arabic)</label>
                                                    <input type="text" id="father_name_arabic" name="father_name_arabic" 
                                                           class="form-control arabic-text" dir="rtl" 
                                                           value="<?php echo $kun_father_name_arabic; ?>">
                                                    <small class="form-text text-muted">Auto-translated</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="grandfather_name_arabic" class="font-weight-bold">Grandfather Name (Arabic)</label>
                                                    <input type="text" id="grandfather_name_arabic" name="grandfather_name_arabic" 
                                                           class="form-control arabic-text" dir="rtl" 
                                                           value="<?php echo $kun_grandfather_name_arabic; ?>">
                                                    <small class="form-text text-muted">Auto-translated</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_name_arabic" class="font-weight-bold">Mother Name (Arabic)</label>
                                                    <input type="text" id="mother_name_arabic" name="mother_name_arabic" 
                                                           class="form-control arabic-text" dir="rtl" 
                                                           value="<?php echo $kun_mother_name_arabic; ?>">
                                                    <small class="form-text text-muted">Auto-translated</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_father_name_arabic" class="font-weight-bold">Mother's Father Name (Arabic)</label>
                                                    <input type="text" id="mother_father_name_arabic" name="mother_father_name_arabic" 
                                                           class="form-control arabic-text" dir="rtl" 
                                                           value="<?php echo $kun_mother_father_name_arabic; ?>">
                                                    <small class="form-text text-muted">Auto-translated</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden Arabic name fields for individual names -->
                                <div class="row mt-3" style="display: none;">
                                    <div class="col-md-4">
                                        <input type="text" id="fname_arabic" name="fname_arabic" value="<?php echo $kun_fname_arabic; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="mname_arabic" name="mname_arabic" value="<?php echo $kun_mname_arabic; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="lname_arabic" name="lname_arabic" value="<?php echo $kun_lname_arabic; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Declaration Section -->
                        <div class="form-section mb-4">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-check-circle text-danger"></i> Declaration
                                </h4>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="declaration" name="declaration" value="1" required>
                                    <label class="form-check-label" for="declaration">
                                        <strong>I hereby declare</strong> that the details furnished above are true and correct to the best of my knowledge and I will undertake to inform Shababul Eidiz Zahabi, Colombo of any changes therein, immediately. I also authorize Shababul Eidiz Zahabi, Colombo to proceed with my application visa process and will not hold them responsible for any complications due to misleading or misrepresented information provided on this form.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-section text-center">
                            <div class="btn-group-vertical btn-group-lg d-md-none mb-3">
                                <button type="submit" class="btn btn-brown btn-lg mb-2">
                                    <i class="icon-ok"></i> Submit Application
                                </button>
                                <button type="reset" class="btn btn-outline-secondary mb-2">
                                    <i class="icon-refresh"></i> Reset Form
                                </button>
                                <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-outline-danger">
                                    <i class="icon-remove"></i> Cancel
                                </a>
                            </div>
                            
                            <div class="d-none d-md-block">
                                <button type="submit" class="btn btn-brown btn-lg mr-3 px-5">
                                    <i class="icon-ok"></i> Submit Application
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg mr-3 px-4">
                                    <i class="icon-refresh"></i> Reset Form
                                </button>
                                <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-outline-danger btn-lg px-4">
                                    <i class="icon-remove"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back to Dashboard (Mobile-friendly) -->
<div class="text-center mt-4">
    <a href="<?php echo $server_link; ?>" class="btn btn-outline-secondary">
        <i class="icon-arrow-left"></i> Back to Dashboard
    </a>
</div>

<script>
// Passport Upload & OCR Functionality
let uploadedFiles = [];
let ocrData = null;
let passportUploadCompleted = false;

// Nationality mapping from ISO-3 codes to nationality labels
const nationalityMapping = {
    'AFG': 'Afghan', 'ALB': 'Albanian', 'DZA': 'Algerian', 'USA': 'American', 'ARG': 'Argentinian', 
    'AUS': 'Australian', 'AUT': 'Austrian', 'BHR': 'Bahraini', 'BGD': 'Bangladeshi', 'BRB': 'Barbadian', 
    'BWA': 'Batswana', 'BEL': 'Belgian', 'BOL': 'Bolivian', 'BRA': 'Brazilian', 'GBR': 'British', 
    'BGR': 'Bulgarian', 'KHM': 'Cambodian', 'CMR': 'Cameroonian', 'CAN': 'Canadian', 'CHL': 'Chilean', 
    'CHN': 'Chinese', 'COL': 'Colombian', 'COM': 'Comoros', 'CRI': 'Costa Rican', 'HRV': 'Croatian', 
    'CUB': 'Cuban', 'CYP': 'Cyprus', 'CZE': 'Czech', 'DNK': 'Danish', 'DEU': 'Deutsch', 
    'DOM': 'Dominican', 'NLD': 'Dutch', 'ECU': 'Ecuadorian', 'EGY': 'Egyptian', 'ARE': 'Emirati', 
    'EST': 'Estonian', 'ETH': 'Ethiopian', 'FJI': 'Fijian', 'FIN': 'Finnish', 'FRA': 'French', 
    'DEU': 'German', 'GHA': 'Ghanaian', 'GRC': 'Greek', 'GTM': 'Guatemalan', 'HTI': 'Haitian', 
    'HND': 'Honduran', 'HUN': 'Hungarian', 'ISL': 'Icelandic', 'IND': 'Indian', 'IDN': 'Indonesian', 
    'IRN': 'Iranian', 'IRL': 'Irish', 'ISR': 'Israeli', 'ITA': 'Italian', 'JAM': 'Jamaican', 
    'JPN': 'Japanese', 'JOR': 'Jordanian', 'KEN': 'Kenyan', 'KOR': 'Korean', 'KWT': 'Kuwaiti', 
    'LAO': 'Lao', 'LVA': 'Latvian', 'LBN': 'Lebanese', 'LBY': 'Libyan', 'LTU': 'Lithuanian', 
    'LUX': 'Luxembourg', 'MDG': 'Malagasy', 'MYS': 'Malaysian', 'MLI': 'Malian', 'MLT': 'Maltese', 
    'MUS': 'Mauritius', 'MEX': 'Mexican', 'MNG': 'Mongolian', 'MAR': 'Moroccan', 'MOZ': 'Mozambican', 
    'MMR': 'Myanmar', 'NAM': 'Namibian', 'NLD': 'Nederlandse', 'NPL': 'Nepalese', 'KNA': 'Nevis', 
    'NZL': 'New Zealand', 'NIC': 'Nicaraguan', 'NGA': 'Nigerian', 'NOR': 'Norwegian', 'OMN': 'Omani', 
    'PAK': 'Pakistani', 'PAN': 'Panamanian', 'PRY': 'Paraguayan', 'PER': 'Peruvian', 'PHL': 'Philippine', 
    'POL': 'Polish', 'PRT': 'Portuguese', 'ROU': 'Romanian', 'RUS': 'Russian', 'SLV': 'Salvadorian', 
    'SAU': 'Saudi', 'SCT': 'Scottish', 'SEN': 'Senegalese', 'SRB': 'Serbian', 'SGP': 'Singaporean', 
    'SVK': 'Slovak', 'ZAF': 'South African', 'KOR': 'South Korean', 'ESP': 'Spanish', 'LKA': 'Sri Lankan', 
    'KNA': 'St Kitts', 'SDN': 'Sudanese', 'SWE': 'Swedish', 'CHE': 'Swiss', 'SYR': 'Syrian', 
    'TWN': 'Taiwanese', 'TJK': 'Tajikistani', 'TZA': 'Tanzanian', 'THA': 'Thai', 'TON': 'Tongan', 
    'TUN': 'Tunisian', 'TUR': 'Turkish', 'UGA': 'Ugandan', 'UKR': 'Ukrainian', 'URY': 'Uruguayan', 
    'VEN': 'Venezuelan', 'VNM': 'Vietnamese', 'WLS': 'Welsh', 'YEM': 'Yemeni', 'ZMB': 'Zambian', 
    'ZWE': 'Zimbabwean'
};

// Initialize passport upload functionality
function initializePassportUpload() {
    const uploadArea = document.getElementById('passport-upload-area');
    const fileInput = document.getElementById('passport-files');
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        const files = Array.from(e.dataTransfer.files);
        handleFileSelection(files);
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleFileSelection(files);
    });
    
    // OCR action buttons
    document.getElementById('accept-all-ocr').addEventListener('click', acceptAllOCRData);
    document.getElementById('reject-all-ocr').addEventListener('click', rejectAllOCRData);
    document.getElementById('clear-ocr-data').addEventListener('click', clearOCRData);
}

// Handle file selection
function handleFileSelection(files) {
    const validFiles = files.filter(file => {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!validTypes.includes(file.type)) {
            showAlert('error', `Invalid file type: ${file.name}. Please upload JPG or PNG files only.`);
            return false;
        }
        
        if (file.size > maxSize) {
            showAlert('error', `File too large: ${file.name}. Maximum size is 10MB.`);
            return false;
        }
        
        return true;
    });
    
    if (validFiles.length > 0) {
        uploadedFiles = [...uploadedFiles, ...validFiles];
        displayFilePreviews();
        uploadFiles(validFiles);
    }
}

// Display file previews
function displayFilePreviews() {
    const container = document.getElementById('file-preview-container');
    const list = document.getElementById('file-preview-list');
    
    container.style.display = 'block';
    list.innerHTML = '';
    
    uploadedFiles.forEach((file, index) => {
        const preview = createFilePreview(file, index);
        list.appendChild(preview);
    });
}

// Create file preview card
function createFilePreview(file, index) {
    const col = document.createElement('div');
    col.className = 'col-md-4 col-sm-6 col-12 mb-3';
    
    col.innerHTML = `
        <div class="card file-preview-card" data-file-index="${index}" data-filename="${file.name}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0 text-truncate" style="max-width: 150px;" title="${file.name}">
                        ${file.name}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="file-info">
                    <small class="text-muted d-block">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    <div class="file-status mt-2">
                        <span class="badge bg-warning" id="status-${index}">Pending</span>
                    </div>
                </div>
                <div class="upload-progress mt-2" style="display: none;">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-brown-light" id="progress-${index}" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return col;
}

// Remove file from upload list
function removeFile(index) {
    uploadedFiles.splice(index, 1);
    displayFilePreviews();
    
    // If no files left, hide containers
    if (uploadedFiles.length === 0) {
        document.getElementById('file-preview-container').style.display = 'none';
        document.getElementById('ocr-preview-container').style.display = 'none';
        passportUploadCompleted = false;
        updateFormProgress();
    }
}

// Upload files to server
function uploadFiles(files) {
    const progressContainer = document.getElementById('upload-progress');
    const progressBar = document.getElementById('upload-progress-bar');
    const statusText = document.getElementById('upload-status');
    
    progressContainer.style.display = 'block';
    statusText.textContent = 'Uploading files...';
    
    const formData = new FormData();
    files.forEach(file => {
        formData.append('kunPhotos[]', file);
    });
    
    // Add registration ID for tracking
    formData.append('kunregid', document.querySelector('input[name="kunregid"]').value);
    
    fetch('<?php echo $server_link; ?>passport_upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusText.textContent = 'Files uploaded successfully! Processing OCR...';
            
            // Update file statuses using data.data.file_results with stable filename matching
            data.data.file_results.forEach((fileResult) => {
                const previewElement = document.querySelector(`[data-filename="${fileResult.original_name}"]`);
                if (previewElement) {
                    const statusBadge = previewElement.querySelector('.file-status .badge');
                    if (fileResult.success) {
                        statusBadge.className = 'badge bg-success';
                        statusBadge.textContent = 'Uploaded';
                    } else {
                        statusBadge.className = 'badge bg-danger';
                        statusBadge.textContent = 'Failed';
                    }
                }
            });
            
            // Find the first passport_info file with successful OCR
            const passportInfoFile = data.data.file_results.find(fr => 
                fr.photo_type === 'passport_info' && fr.success && fr.ocr_data
            );
            
            if (passportInfoFile && passportInfoFile.ocr_data) {
                displayOCRData(passportInfoFile.ocr_data);
                statusText.textContent = 'OCR completed successfully!';
                
                // Check for previous application data after OCR is processed
                var its = document.getElementById('its').value;
                if (its && its.trim() !== '') {
                    // Previous application check is handled in displayOCRData()
                }
            } else {
                statusText.textContent = 'Upload completed - manual entry required';
            }
            
            // Display quality validation feedback
            displayQualityFeedback(data.data.file_results);
            
            passportUploadCompleted = true;
            updateFormProgress();
            
        } else {
            showAlert('error', data.message || 'Upload failed. Please try again.');
            statusText.textContent = 'Upload failed';
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showAlert('error', 'Upload failed. Please check your connection and try again.');
        statusText.textContent = 'Upload failed';
    })
    .finally(() => {
        progressContainer.style.display = 'none';
    });
}

// OCR processing is now handled directly by the upload handler
// This function is kept for backward compatibility but is no longer used

// Display OCR data for review
function displayOCRData(data) {
    const container = document.getElementById('ocr-preview-container');
    const table = document.getElementById('ocr-data-table');
    
    container.style.display = 'block';
    
    // Store OCR data globally for comparison
    window.currentOCRData = data;
    
    // Normalize OCR data to standard field names
    const normalized = {
        passport_number: data.kun_ppno || data.kun_passport_no,
        first_name: data.kun_fname,
        last_name: data.kun_lname,
        date_of_birth: data.kun_dob,
        nationality: nationalityMapping[data.kun_nationality] || data.kun_nationality,
        expiry_date: data.kun_ppexpiry || data.kun_passport_expiry,
        issue_date: data.kun_ppissue || data.kun_passport_issue,
        passport_type: data.kun_passport_type || data.passport_type,
        place_of_issue: data.kun_placeofissue || data.place_of_issue,
    };
    
    const fieldMappings = {
        'passport_number': { label: 'Passport Number', field: 'ppno', type: 'text' },
        'first_name': { label: 'First Name', field: 'fname', type: 'text' },
        'middle_name': { label: 'Middle Name', field: 'mname', type: 'text' },
        'last_name': { label: 'Last Name', field: 'lname', type: 'text' },
        'date_of_birth': { label: 'Date of Birth', field: 'dob', type: 'date' },
        'nationality': { label: 'Nationality', field: 'nationality', type: 'select' },
        'passport_type': { label: 'Passport Type', field: 'passport_type', type: 'select' },
        'issue_date': { label: 'Issue Date', field: 'ppissue', type: 'date' },
        'expiry_date': { label: 'Expiry Date', field: 'ppexpiry', type: 'date' },
        'place_of_issue': { label: 'Place of Issue', field: 'placeofissue', type: 'text' }
    };
    
    let tableHTML = `
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%;">Field</th>
                    <th style="width: 35%;">Extracted Value</th>
                    <th style="width: 25%;">Current Value</th>
                    <th style="width: 15%;">Action</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    Object.keys(fieldMappings).forEach(key => {
        if (normalized[key]) {
            const mapping = fieldMappings[key];
            const currentValue = document.getElementById(mapping.field)?.value || '';
            const confidence = data[`${key}_confidence`] || 0;
            
            tableHTML += `
                <tr data-field="${mapping.field}" data-ocr-value="${normalized[key]}">
                    <td><strong>${mapping.label}</strong></td>
                    <td>
                        <span class="ocr-value">${normalized[key]}</span>
                        <small class="d-block text-muted">Confidence: ${confidence}%</small>
                    </td>
                    <td>
                        <span class="current-value">${currentValue || 'Empty'}</span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" onclick="acceptOCRField('${mapping.field}', '${normalized[key].replace(/'/g, "\\'")}')" title="Accept">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="rejectOCRField('${mapping.field}')" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    });
    
    tableHTML += `
            </tbody>
        </table>
    `;
    
    table.innerHTML = tableHTML;
    
    // Check for previous application data after OCR is displayed
    var its = document.getElementById('its').value;
    if (its && its.trim() !== '') {
        checkPreviousApplication();
    }
}

// Accept individual OCR field
function acceptOCRField(fieldId, value) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.value = value;
        field.classList.add('border-success');
        
        // Remove border after animation
        setTimeout(() => {
            field.classList.remove('border-success');
        }, 2000);
        
        // Update progress
        updateFormProgress();
        
        // Auto-translate if it's a name field
        if (['fname', 'mname', 'lname'].includes(fieldId)) {
            updatePassportArabicName();
        }
    }
    
    // Mark row as accepted
    const row = document.querySelector(`tr[data-field="${fieldId}"]`);
    if (row) {
        row.classList.add('table-success');
        row.querySelector('.btn-group').innerHTML = '<span class="badge bg-success">Accepted</span>';
    }
    
    // If in comparison mode, update the comparison data
    if (window.comparisonData && window.comparisonData.ocr[fieldId]) {
        window.comparisonData.current[fieldId] = {
            value: value,
            source: 'current',
            label: getFieldLabel(fieldId)
        };
    }
}

// Reject individual OCR field
function rejectOCRField(fieldId) {
    const row = document.querySelector(`tr[data-field="${fieldId}"]`);
    if (row) {
        row.classList.add('table-danger');
        row.querySelector('.btn-group').innerHTML = '<span class="badge bg-danger">Rejected</span>';
    }
}

// Accept all OCR data
function acceptAllOCRData() {
    const rows = document.querySelectorAll('#ocr-data-table tbody tr');
    rows.forEach(row => {
        const fieldId = row.dataset.field;
        const ocrValue = row.dataset.ocrValue;
        if (fieldId && ocrValue) {
            acceptOCRField(fieldId, ocrValue);
        }
    });
}

// Reject all OCR data
function rejectAllOCRData() {
    const rows = document.querySelectorAll('#ocr-data-table tbody tr');
    rows.forEach(row => {
        const fieldId = row.dataset.field;
        if (fieldId) {
            rejectOCRField(fieldId);
        }
    });
}

// Clear OCR data
function clearOCRData() {
    document.getElementById('ocr-preview-container').style.display = 'none';
    ocrData = null;
}

// Display quality validation feedback
function displayQualityFeedback(fileResults) {
    const feedbackContainer = document.getElementById('quality-feedback');
    const feedbackEntries = [];
    
    fileResults.forEach(fileResult => {
        if (fileResult.quality_validation) {
            const quality = fileResult.quality_validation;
            const filename = fileResult.original_name;
            
            if (quality.error || (quality.issues && quality.issues.length > 0)) {
                const issues = quality.issues || [quality.error];
                feedbackEntries.push({
                    filename: filename,
                    status: 'Issues',
                    issues: issues
                });
            } else {
                feedbackEntries.push({
                    filename: filename,
                    status: 'OK',
                    issues: []
                });
            }
        }
    });
    
    if (feedbackEntries.length > 0) {
        let feedbackHTML = '<div class="alert alert-info"><h6><i class="fas fa-info-circle"></i> Image Quality Validation</h6><ul class="mb-0">';
        
        feedbackEntries.forEach(entry => {
            if (entry.status === 'OK') {
                feedbackHTML += `<li><strong>${entry.filename}</strong> — <span class="text-success">OK</span></li>`;
            } else {
                feedbackHTML += `<li><strong>${entry.filename}</strong> — <span class="text-warning">Issues:</span> ${entry.issues.join(', ')}</li>`;
            }
        });
        
        feedbackHTML += '</ul></div>';
        feedbackContainer.innerHTML = feedbackHTML;
        feedbackContainer.style.display = 'block';
    } else {
        feedbackContainer.style.display = 'none';
    }
}

// Show alert messages
function showAlert(type, message) {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-success';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 
                              type === 'warning' ? 'exclamation-circle' : 'check-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insert alert at the top of the passport upload section
    const section = document.getElementById('section-passport-upload');
    section.insertAdjacentHTML('afterbegin', alertHTML);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alerts = section.querySelectorAll('.alert');
        if (alerts.length > 0) {
            alerts[0].remove();
        }
    }, 5000);
}

// Enhanced form progress tracking
function updateFormProgress() {
    // Calculate passport upload section progress (20% of total)
    const passportProgress = passportUploadCompleted ? 20 : 0;
    
    // Calculate remaining form fields progress (80% of total)
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    const filledFields = Array.from(requiredFields).filter(field => {
        if (field.type === 'checkbox') {
            return field.checked;
        }
        return field.value.trim() !== '';
    });
    const formProgress = (filledFields.length / requiredFields.length) * 80;
    
    const progress = passportProgress + formProgress;
    
    const progressBar = document.getElementById('form-progress');
    progressBar.style.width = progress + '%';
    progressBar.setAttribute('aria-valuenow', Math.round(progress));
    
    // Update progress bar color based on completion
    progressBar.className = 'progress-bar transition-all';
    if (progress < 30) {
        progressBar.style.background = 'linear-gradient(90deg, #dc3545, #c82333)';
    } else if (progress < 70) {
        progressBar.style.background = 'linear-gradient(90deg, #ffc107, #e0a800)';
    } else {
        progressBar.classList.add('bg-brown-light');
        progressBar.style.background = '';
    }
}

// Calculate age from date of birth
function calculateAge() {
    var dob = document.getElementById('dob').value;
    if (dob) {
        var today = new Date();
        var birthDate = new Date(dob);
        var age = today.getFullYear() - birthDate.getFullYear();
        var monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        document.getElementById('age').value = age;
        updateFormProgress();
    }
}

// Enhanced translation function for individual fields
function translateToArabic(englishText, targetFieldId) {
    if (!englishText || englishText.trim() === '') return;
    
    fetch('https://ziyarat.colomboshabab.com/translate_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            text: englishText,
            source: 'en',
            target: 'ar'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.translatedText) {
            document.getElementById(targetFieldId).value = data.translatedText;
        }
    })
    .catch(error => {
        console.log('Translation service error:', error);
    });
}

// New function to translate combined passport name
function updatePassportArabicName() {
    var fname = document.getElementById('fname').value || '';
    var mname = document.getElementById('mname').value || '';
    var lname = document.getElementById('lname').value || '';
    
    // Combine names with spaces, removing extra spaces
    var fullName = [fname, mname, lname].filter(name => name.trim() !== '').join(' ');
    
    if (fullName.trim() !== '') {
        fetch('https://ziyarat.colomboshabab.com/translate_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: fullName,
                source: 'en',
                target: 'ar'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.translatedText) {
                document.getElementById('passport_arabic_name').value = data.translatedText;
            }
        })
        .catch(error => {
            console.log('Translation service error:', error);
        });
    }

    //fname_arabic
    if (fname) {
        fetch('https://ziyarat.colomboshabab.com/translate_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
    
                body: JSON.stringify({
                    text: fname,
                    source: 'en',
                    target: 'ar'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.translatedText) {
                    document.getElementById('fname_arabic').value = data.translatedText;
                }
            })
            .catch(error => {
                console.log('Translation service error:', error);
            });
    }

    //mname_arabic
    if (mname) {
        fetch('https://ziyarat.colomboshabab.com/translate_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: mname,
                source: 'en',
                target: 'ar'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.translatedText) {
                document.getElementById('mname_arabic').value = data.translatedText;
            }
        })
        .catch(error => {
            console.log('Translation service error:', error);
        });
    }

    //lname_arabic
    if (lname) {
        fetch('https://ziyarat.colomboshabab.com/translate_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: lname,
                source: 'en',
                target: 'ar'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.translatedText) {
                document.getElementById('lname_arabic').value = data.translatedText;
            }
        })
        .catch(error => {
            console.log('Translation service error:', error);
        });
    }

}

// Enhanced auto-populate from previous application logic
const PASSPORT_MIN_MONTHS = 6;

function checkPreviousApplication() {
    var its = document.getElementById('its').value;
    var ppno = document.getElementById('ppno').value;
    var ppexpiry = document.getElementById('ppexpiry').value;
    
    if ((!ppno || !ppexpiry) && its) {
        fetch('check_previous_application.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({its: its})
        })
        .then(response => response.json())
        .then(data => {
            if (data.found && data.passport_valid) {
                // Check if OCR data is available for comparison
                var ocrContainer = document.getElementById('ocr-preview-container');
                var hasOCRData = ocrContainer && ocrContainer.style.display !== 'none';
                
                if (hasOCRData && window.currentOCRData) {
                    // Show comprehensive comparison interface
                    displayDataComparison(data, window.currentOCRData);
                } else {
                    // Show simple confirmation dialog for previous data only
                    if (confirm('Previous application found with valid passport. Would you like to populate the form with previous data?')) {
                        populateFormWithPreviousData(data.application);
                    }
                }
            } else if (data.found && !data.passport_valid) {
                alert(`Previous application found, but passport expires within ${PASSPORT_MIN_MONTHS} months. Please enter passport details manually.`);
            }
        })
        .catch(error => {
            console.log('Error checking previous application:', error);
        });
    }
}

// Enhanced populate function with auto-translation
function populateFormWithPreviousData(data) {
    // Populate form fields with previous application data
    Object.keys(data).forEach(function(key) {
        var element = document.getElementById(key.replace('kun_', ''));
        if (element && data[key]) {
            if (element.type === 'checkbox') {
                element.checked = data[key] == '1';
            } else {
                element.value = data[key];
            }
        }
    });
    
    // Recalculate age if DOB is populated
    if (data.kun_dob) {
        calculateAge();
    }
    
    // Auto-translate all Arabic fields after population
    setTimeout(function() {
        translateAllArabicFields();
        updateFormProgress();
    }, 500); // Small delay to ensure fields are populated
}

// ===== DATA COMPARISON FUNCTIONS =====

// Display comprehensive data comparison interface
function displayDataComparison(previousData, ocrData) {
    const container = document.getElementById('data-comparison-container');
    const table = document.getElementById('comparison-data-table');
    
    // Hide OCR preview and show comparison interface
    document.getElementById('ocr-preview-container').style.display = 'none';
    container.style.display = 'block';
    
    // Store data globally for comparison functions
    window.comparisonData = {
        previous: previousData.comparison_data || {},
        ocr: normalizeOCRDataForComparison(ocrData),
        current: getCurrentFormData()
    };
    
    // Build comparison table
    let tableHTML = `
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th style="width: 20%;">Field</th>
                    <th style="width: 25%;">Previous Data</th>
                    <th style="width: 25%;">OCR Data</th>
                    <th style="width: 20%;">Current Form</th>
                    <th style="width: 10%;">Select</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    // Get all unique fields from all data sources
    const allFields = new Set([
        ...Object.keys(window.comparisonData.previous),
        ...Object.keys(window.comparisonData.ocr),
        ...Object.keys(window.comparisonData.current)
    ]);
    
    allFields.forEach(fieldId => {
        const previous = window.comparisonData.previous[fieldId] || {};
        const ocr = window.comparisonData.ocr[fieldId] || {};
        const current = window.comparisonData.current[fieldId] || {};
        
        // Skip empty fields unless they're required
        if (!previous.value && !ocr.value && !current.value && !previous.required) {
            return;
        }
        
        // Skip fields that don't exist in the form
        if (!document.getElementById(fieldId)) {
            return;
        }
        
        const fieldLabel = previous.label || ocr.label || getFieldLabel(fieldId);
        const hasConflict = hasDataConflict(previous.value, ocr.value, current.value);
        
        tableHTML += `
            <tr data-field="${fieldId}" class="${hasConflict ? 'table-warning' : ''}">
                <td>
                    <strong>${fieldLabel}</strong>
                    ${previous.required ? '<span class="text-danger">*</span>' : ''}
                    ${hasConflict ? '<br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Conflict detected</small>' : ''}
                </td>
                <td>
                    <div class="previous-data">
                        ${previous.value ? `<span class="text-success">${previous.value}</span>` : '<span class="text-muted">Empty</span>'}
                        ${previous.confidence ? `<br><small class="text-muted">Confidence: ${previous.confidence}%</small>` : ''}
                    </div>
                </td>
                <td>
                    <div class="ocr-data">
                        ${ocr.value ? `<span class="text-warning">${ocr.value}</span>` : '<span class="text-muted">Empty</span>'}
                        ${ocr.confidence ? `<br><small class="text-muted">Confidence: ${ocr.confidence}%</small>` : ''}
                    </div>
                </td>
                <td>
                    <div class="current-data">
                        ${current.value ? `<span class="text-info">${current.value}</span>` : '<span class="text-muted">Empty</span>'}
                    </div>
                </td>
                <td>
                    <div class="btn-group-vertical btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="selectDataSource('${fieldId}', 'previous')" title="Use Previous Data">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="selectDataSource('${fieldId}', 'ocr')" title="Use OCR Data">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectDataSource('${fieldId}', 'current')" title="Keep Current">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableHTML += `
            </tbody>
        </table>
    `;
    
    table.innerHTML = tableHTML;
    
    // Add event listeners for bulk actions
    addComparisonEventListeners();
}

// Normalize OCR data for comparison
function normalizeOCRDataForComparison(ocrData) {
    const normalized = {};
    
    // Map OCR fields to standard field names
    const fieldMappings = {
        'ppno': ocrData.kun_ppno || ocrData.kun_passport_no,
        'fname': ocrData.kun_fname,
        'mname': ocrData.kun_mname,
        'lname': ocrData.kun_lname,
        'dob': ocrData.kun_dob,
        'nationality': nationalityMapping[ocrData.kun_nationality] || ocrData.kun_nationality,
        'ppexpiry': ocrData.kun_ppexpiry || ocrData.kun_passport_expiry,
        'ppissue': ocrData.kun_ppissue || ocrData.kun_passport_issue,
        'passport_type': ocrData.kun_passport_type || ocrData.passport_type,
        'placeofissue': ocrData.kun_placeofissue || ocrData.place_of_issue,
        'gender': ocrData.kun_gender,
        'address': ocrData.kun_address,
        'contact': ocrData.kun_contact,
        'email': ocrData.kun_email
    };
    
    Object.keys(fieldMappings).forEach(fieldId => {
        const value = fieldMappings[fieldId];
        if (value) {
            normalized[fieldId] = {
                value: value,
                source: 'ocr',
                confidence: ocrData[`${fieldId}_confidence`] || 0,
                label: getFieldLabel(fieldId)
            };
        }
    });
    
    return normalized;
}

// Get current form data
function getCurrentFormData() {
    const currentData = {};
    const formFields = [
        'ppno', 'fname', 'mname', 'lname', 'dob', 'nationality', 'gender',
        'address', 'contact', 'email', 'ppexpiry', 'ppissue', 'passport_type',
        'placeofissue', 'emergency_contact', 'emergency_name', 'medical_conditions',
        'medications', 'allergies', 'dietary_requirements', 'special_needs',
        'previous_ziyarat', 'occupation', 'employer', 'marital_status',
        'spouse_name', 'children_count', 'passport_arabic_name', 'father_name',
        'mother_name', 'place_of_birth', 'religion', 'education_level',
        'language_spoken', 'travel_history', 'visa_history', 'criminal_record',
        'deportation_history', 'financial_support', 'bank_statement',
        'employment_letter', 'family_ties', 'purpose_of_visit',
        'accommodation_details', 'travel_insurance', 'insurance_provider',
        'insurance_policy_number', 'emergency_contact_relationship',
        'emergency_contact_address', 'additional_info', 'notes'
    ];
    
    formFields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            const value = element.type === 'checkbox' ? (element.checked ? '1' : '0') : element.value;
            if (value && value.trim() !== '') {
                currentData[fieldId] = {
                    value: value,
                    source: 'current',
                    label: getFieldLabel(fieldId)
                };
            }
        }
    });
    
    return currentData;
}

// Check if there's a data conflict between sources
function hasDataConflict(previousValue, ocrValue, currentValue) {
    const values = [previousValue, ocrValue, currentValue].filter(v => v && v.trim() !== '');
    const uniqueValues = [...new Set(values)];
    return uniqueValues.length > 1;
}

// Get field label for display
function getFieldLabel(fieldId) {
    const labels = {
        'ppno': 'Passport Number',
        'fname': 'First Name',
        'mname': 'Middle Name',
        'lname': 'Last Name',
        'dob': 'Date of Birth',
        'nationality': 'Nationality',
        'gender': 'Gender',
        'address': 'Address',
        'contact': 'Contact Number',
        'email': 'Email Address',
        'ppexpiry': 'Passport Expiry',
        'ppissue': 'Passport Issue Date',
        'passport_type': 'Passport Type',
        'placeofissue': 'Place of Issue',
        'emergency_contact': 'Emergency Contact',
        'emergency_name': 'Emergency Contact Name',
        'medical_conditions': 'Medical Conditions',
        'medications': 'Current Medications',
        'allergies': 'Allergies',
        'dietary_requirements': 'Dietary Requirements',
        'special_needs': 'Special Needs',
        'previous_ziyarat': 'Previous Ziyarat Experience',
        'occupation': 'Occupation',
        'employer': 'Employer',
        'marital_status': 'Marital Status',
        'spouse_name': 'Spouse Name',
        'children_count': 'Number of Children',
        'passport_arabic_name': 'Passport Arabic Name',
        'father_name': 'Father Name',
        'mother_name': 'Mother Name',
        'place_of_birth': 'Place of Birth',
        'religion': 'Religion',
        'education_level': 'Education Level',
        'language_spoken': 'Languages Spoken',
        'travel_history': 'Travel History',
        'visa_history': 'Visa History',
        'criminal_record': 'Criminal Record',
        'deportation_history': 'Deportation History',
        'financial_support': 'Financial Support',
        'bank_statement': 'Bank Statement Available',
        'employment_letter': 'Employment Letter Available',
        'family_ties': 'Family Ties',
        'purpose_of_visit': 'Purpose of Visit',
        'accommodation_details': 'Accommodation Details',
        'travel_insurance': 'Travel Insurance',
        'insurance_provider': 'Insurance Provider',
        'insurance_policy_number': 'Insurance Policy Number',
        'emergency_contact_relationship': 'Emergency Contact Relationship',
        'emergency_contact_address': 'Emergency Contact Address',
        'additional_info': 'Additional Information',
        'notes': 'Notes'
    };
    
    return labels[fieldId] || fieldId.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

// Select data source for a specific field
function selectDataSource(fieldId, source) {
    const row = document.querySelector(`tr[data-field="${fieldId}"]`);
    if (!row) return;
    
    // Remove existing selection indicators
    row.querySelectorAll('.btn').forEach(btn => {
        btn.classList.remove('btn-success', 'btn-warning', 'btn-info');
        btn.classList.add('btn-outline-success', 'btn-outline-warning', 'btn-outline-info');
    });
    
    // Highlight selected source
    const buttons = row.querySelectorAll('.btn-group-vertical .btn');
    const sourceIndex = ['previous', 'ocr', 'current'].indexOf(source);
    if (sourceIndex >= 0 && buttons[sourceIndex]) {
        const selectedBtn = buttons[sourceIndex];
        selectedBtn.classList.remove('btn-outline-success', 'btn-outline-warning', 'btn-outline-info');
        selectedBtn.classList.add(source === 'previous' ? 'btn-success' : source === 'ocr' ? 'btn-warning' : 'btn-info');
    }
    
    // Store selection
    if (!window.selectedDataSources) {
        window.selectedDataSources = {};
    }
    window.selectedDataSources[fieldId] = source;
}

// Apply selected data to form
function applySelectedData() {
    if (!window.selectedDataSources || !window.comparisonData) {
        alert('No data selections made. Please select data sources for the fields you want to update.');
        return;
    }
    
    let appliedCount = 0;
    
    Object.keys(window.selectedDataSources).forEach(fieldId => {
        const source = window.selectedDataSources[fieldId];
        const data = window.comparisonData[source][fieldId];
        
        if (data && data.value) {
            const element = document.getElementById(fieldId);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = data.value === '1';
                } else {
                    element.value = data.value;
                }
                
                // Visual feedback
                element.classList.add('border-success');
                setTimeout(() => {
                    element.classList.remove('border-success');
                }, 2000);
                
                appliedCount++;
            }
        }
    });
    
    // Hide comparison interface and show OCR preview again
    document.getElementById('data-comparison-container').style.display = 'none';
    document.getElementById('ocr-preview-container').style.display = 'block';
    
    // Update form progress
    updateFormProgress();
    
    // Auto-translate Arabic fields
    setTimeout(() => {
        translateAllArabicFields();
    }, 500);
    
    alert(`Applied ${appliedCount} field(s) to the form.`);
}

// Cancel comparison and return to OCR preview
function cancelComparison() {
    document.getElementById('data-comparison-container').style.display = 'none';
    document.getElementById('ocr-preview-container').style.display = 'block';
    
    // Clear selections
    window.selectedDataSources = {};
}

// Add event listeners for comparison interface
function addComparisonEventListeners() {
    // Bulk action buttons
    document.getElementById('accept-all-previous').onclick = () => {
        selectAllDataSources('previous');
    };
    
    document.getElementById('accept-all-comparison').onclick = () => {
        selectAllDataSources('ocr');
    };
    
    document.getElementById('keep-all-current').onclick = () => {
        selectAllDataSources('current');
    };
    
    document.getElementById('clear-comparison-data').onclick = () => {
        clearAllSelections();
    };
    
    // Action buttons
    document.getElementById('apply-selected-data').onclick = applySelectedData;
    document.getElementById('cancel-comparison').onclick = cancelComparison;
}

// Select all fields from a specific data source
function selectAllDataSources(source) {
    const rows = document.querySelectorAll('#comparison-data-table tr[data-field]');
    rows.forEach(row => {
        const fieldId = row.getAttribute('data-field');
        selectDataSource(fieldId, source);
    });
}

// Clear all selections
function clearAllSelections() {
    const rows = document.querySelectorAll('#comparison-data-table tr[data-field]');
    rows.forEach(row => {
        const buttons = row.querySelectorAll('.btn-group-vertical .btn');
        buttons.forEach(btn => {
            btn.classList.remove('btn-success', 'btn-warning', 'btn-info');
            btn.classList.add('btn-outline-success', 'btn-outline-warning', 'btn-outline-info');
        });
    });
    
    window.selectedDataSources = {};
}

// New function to translate all Arabic fields
function translateAllArabicFields() {
    // Translate passport names (combined)
    updatePassportArabicName();
    
    // Translate individual name fields
    var englishFields = [
        {english: 'father_name', arabic: 'father_name_arabic'},
        {english: 'grandfather_name', arabic: 'grandfather_name_arabic'},
        {english: 'mother_name', arabic: 'mother_name_arabic'},
        {english: 'mother_father_name', arabic: 'mother_father_name_arabic'},
        {english: 'fname', arabic: 'fname_arabic'},
        {english: 'mname', arabic: 'mname_arabic'},
        {english: 'lname', arabic: 'lname_arabic'}
    ];
    
    englishFields.forEach(function(field) {
        var englishValue = document.getElementById(field.english).value;
        if (englishValue && englishValue.trim() !== '') {
            translateToArabic(englishValue, field.arabic);
        }
    });
}

// Passport expiry validation function
function validatePassportExpiry() {
    var expiryDate = document.getElementById('ppexpiry').value;
    var submitButton = document.querySelector('button[type="submit"]');
    var errorContainer = document.getElementById('passport-expiry-error');
    
    // Remove existing error message if any
    if (errorContainer) {
        errorContainer.remove();
    }
    
    if (expiryDate) {
        var expiry = new Date(expiryDate);
        var today = new Date();
        var sixMonthsFromNow = new Date();
        sixMonthsFromNow.setMonth(today.getMonth() + 6);
        
        if (expiry <= sixMonthsFromNow) {
            // Passport expires within 6 months - show error
            var errorDiv = document.createElement('div');
            errorDiv.id = 'passport-expiry-error';
            errorDiv.className = 'alert alert-danger mt-2';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Invalid Passport Expiry!</strong> Passport must be valid for at least 6 months from today. Current expiry: ' + expiryDate;
            
            // Insert error message after the expiry field
            var expiryField = document.getElementById('ppexpiry');
            expiryField.parentNode.appendChild(errorDiv);
            
            // Disable submit button
            submitButton.disabled = true;
            submitButton.classList.add('disabled');
            submitButton.title = 'Please enter a valid passport expiry date';
            
        } else {
            // Passport is valid - enable submit button
            submitButton.disabled = false;
            submitButton.classList.remove('disabled');
            submitButton.title = '';
        }
    } else {
        // No expiry date entered - enable submit button (required validation will handle this)
        submitButton.disabled = false;
        submitButton.classList.remove('disabled');
        submitButton.title = '';
    }
}

// Enhanced event listeners with progress tracking
document.addEventListener('DOMContentLoaded', function() {
    // Initialize passport upload functionality
    initializePassportUpload();
    
    // Add progress tracking to all form inputs
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', updateFormProgress);
        input.addEventListener('change', updateFormProgress);
    });
    
    // Check for previous application when ITS is entered
    document.getElementById('its').addEventListener('blur', checkPreviousApplication);
    
    // Calculate age when DOB changes
    document.getElementById('dob').addEventListener('change', calculateAge);
    
    // Calculate age on page load if DOB exists
    calculateAge();
    
    // Auto-check previous application if ITS already exists on page load
    var existingITS = document.getElementById('its').value;
    if (existingITS && existingITS.trim() !== '') {
        checkPreviousApplication();
    }
    
    // Add event listeners for passport name changes
    document.getElementById('fname').addEventListener('input', updatePassportArabicName);
    document.getElementById('mname').addEventListener('input', updatePassportArabicName);
    document.getElementById('lname').addEventListener('input', updatePassportArabicName);
    
    // Add translation listeners for family names
    document.getElementById('father_name').addEventListener('blur', function() {
        translateToArabic(this.value, 'father_name_arabic');
    });
    document.getElementById('grandfather_name').addEventListener('blur', function() {
        translateToArabic(this.value, 'grandfather_name_arabic');
    });
    document.getElementById('mother_name').addEventListener('blur', function() {
        translateToArabic(this.value, 'mother_name_arabic');
    });
    document.getElementById('mother_father_name').addEventListener('blur', function() {
        translateToArabic(this.value, 'mother_father_name_arabic');
    });
    
    // Add passport expiry validation
    document.getElementById('ppexpiry').addEventListener('change', validatePassportExpiry);
    document.getElementById('ppexpiry').addEventListener('blur', validatePassportExpiry);
    
    // Validate on page load if expiry already exists
    validatePassportExpiry();
    
    // Initial progress calculation
    updateFormProgress();
    
    // Prevent form submission if validations fail
    document.getElementById('kun-registration').addEventListener('submit', function(e) {
        var submitButton = document.querySelector('button[type="submit"]');
        if (submitButton.disabled) {
            e.preventDefault();
            alert('Please fix the passport expiry date before submitting.');
            return false;
        }
        
        // Check if passport upload is completed
        if (!passportUploadCompleted) {
            e.preventDefault();
            alert('Please upload your passport information page before submitting.');
            return false;
        }
    });
    
    // Collapsible section animations
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(element) {
        element.addEventListener('click', function() {
            var icon = this.querySelector('.fa-chevron-down');
            if (icon) {
                icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    });
});

// Passport Photo Upload & Cropping Functionality
let passportPhotoData = null;
let passportPhotoCanvas = null;
let passportPhotoCtx = null;
let passportPhotoPreviewCtx = null;
let passportPhotoImage = null;
let passportPhotoZoom = 1;
let passportPhotoRotation = 0;
let passportPhotoCropBox = null;
let passportPhotoCropData = null;
let passportPhotoUploadCompleted = false;

// Initialize passport photo functionality
document.addEventListener('DOMContentLoaded', function() {
    const passportPhotoFile = document.getElementById('passport-photo-file');
    const passportPhotoCropper = document.getElementById('passport-photo-cropper');
    const passportPhotoCanvas = document.getElementById('passport-photo-canvas');
    const passportPhotoPreview = document.getElementById('passport-photo-preview');
    
    if (passportPhotoFile) {
        passportPhotoFile.addEventListener('change', handlePassportPhotoUpload);
    }
    
    // Initialize cropping controls
    initPassportPhotoCropper();
});

function handlePassportPhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file
    if (!validatePassportPhotoRequirements(file)) {
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        passportPhotoData = e.target.result;
        initPassportPhotoCropper();
        showPassportPhotoCropper();
        updateFileInfo(file);
    };
    reader.readAsDataURL(file);
}

function validatePassportPhotoRequirements(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    
    if (!allowedTypes.includes(file.type)) {
        showPassportPhotoMessage('Please select a valid image file (JPG, PNG, GIF).', 'danger');
        return false;
    }
    
    if (file.size > maxSize) {
        showPassportPhotoMessage('File size must be less than 5MB.', 'danger');
        return false;
    }
    
    return true;
}

function initPassportPhotoCropper() {
    passportPhotoCanvas = document.getElementById('passport-photo-canvas');
    passportPhotoCtx = passportPhotoCanvas.getContext('2d');
    passportPhotoPreviewCtx = document.getElementById('passport-photo-preview').getContext('2d');
    passportPhotoCropBox = document.getElementById('crop-box');
    
    if (!passportPhotoData) return;
    
    passportPhotoImage = new Image();
    passportPhotoImage.onload = function() {
        drawPassportPhoto();
        initCropBox();
        setupCroppingControls();
    };
    passportPhotoImage.src = passportPhotoData;
}

function drawPassportPhoto() {
    if (!passportPhotoImage || !passportPhotoCanvas) return;
    
    const canvas = passportPhotoCanvas;
    const ctx = passportPhotoCtx;
    
    // Calculate display dimensions
    const maxWidth = 600;
    const maxHeight = 400;
    
    let displayWidth = passportPhotoImage.width;
    let displayHeight = passportPhotoImage.height;
    
    // Scale down if too large
    if (displayWidth > maxWidth || displayHeight > maxHeight) {
        const scale = Math.min(maxWidth / displayWidth, maxHeight / displayHeight);
        displayWidth *= scale;
        displayHeight *= scale;
    }
    
    // Apply zoom
    displayWidth *= passportPhotoZoom;
    displayHeight *= passportPhotoZoom;
    
    canvas.width = displayWidth;
    canvas.height = displayHeight;
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Apply rotation
    ctx.save();
    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate((passportPhotoRotation * Math.PI) / 180);
    ctx.translate(-canvas.width / 2, -canvas.height / 2);
    
    // Draw image
    ctx.drawImage(passportPhotoImage, 0, 0, displayWidth, displayHeight);
    
    ctx.restore();
    
    updatePreview();
}

function initCropBox() {
    if (!passportPhotoCanvas || !passportPhotoCropBox) return;
    
    const canvas = passportPhotoCanvas;
    const cropBox = passportPhotoCropBox;
    const overlay = document.getElementById('crop-overlay');
    
    // Set overlay dimensions
    overlay.style.width = canvas.width + 'px';
    overlay.style.height = canvas.height + 'px';
    
    // Initialize crop box to center with square aspect ratio
    const size = Math.min(canvas.width, canvas.height) * 0.6;
    const x = (canvas.width - size) / 2;
    const y = (canvas.height - size) / 2;
    
    cropBox.style.left = x + 'px';
    cropBox.style.top = y + 'px';
    cropBox.style.width = size + 'px';
    cropBox.style.height = size + 'px';
    
    passportPhotoCropData = { x, y, width: size, height: size };
}

function setupCroppingControls() {
    // Zoom controls
    const zoomSlider = document.getElementById('zoom-slider');
    const zoomIn = document.getElementById('zoom-in');
    const zoomOut = document.getElementById('zoom-out');
    
    if (zoomSlider) {
        zoomSlider.addEventListener('input', function() {
            passportPhotoZoom = parseFloat(this.value);
            drawPassportPhoto();
            updateCropBoxPosition();
        });
    }
    
    if (zoomIn) {
        zoomIn.addEventListener('click', function() {
            passportPhotoZoom = Math.min(3, passportPhotoZoom + 0.1);
            zoomSlider.value = passportPhotoZoom;
            drawPassportPhoto();
            updateCropBoxPosition();
        });
    }
    
    if (zoomOut) {
        zoomOut.addEventListener('click', function() {
            passportPhotoZoom = Math.max(0.5, passportPhotoZoom - 0.1);
            zoomSlider.value = passportPhotoZoom;
            drawPassportPhoto();
            updateCropBoxPosition();
        });
    }
    
    // Rotation controls
    const rotateLeft = document.getElementById('rotate-left');
    const rotateRight = document.getElementById('rotate-right');
    
    if (rotateLeft) {
        rotateLeft.addEventListener('click', function() {
            passportPhotoRotation -= 90;
            drawPassportPhoto();
            updateCropBoxPosition();
            updatePreview();
        });
    }
    
    if (rotateRight) {
        rotateRight.addEventListener('click', function() {
            passportPhotoRotation += 90;
            drawPassportPhoto();
            updateCropBoxPosition();
            updatePreview();
        });
    }
    
    // Crop and upload button
    const cropAndUpload = document.getElementById('crop-and-upload');
    if (cropAndUpload) {
        cropAndUpload.addEventListener('click', cropAndUploadPassportPhoto);
    }
    
    // Cancel crop button
    const cancelCrop = document.getElementById('cancel-crop');
    if (cancelCrop) {
        cancelCrop.addEventListener('click', cancelPassportPhotoCrop);
    }
    
    // Make crop box draggable and resizable
    makeCropBoxInteractive();
}

function makeCropBoxInteractive() {
    const cropBox = passportPhotoCropBox;
    if (!cropBox) return;
    
    let isDragging = false;
    let isResizing = false;
    let startX, startY, startWidth, startHeight, startLeft, startTop;
    
    // Mouse events for dragging
    cropBox.addEventListener('mousedown', function(e) {
        if (e.target.classList.contains('crop-handle')) {
            isResizing = true;
            const handle = e.target;
            const rect = cropBox.getBoundingClientRect();
            startX = e.clientX;
            startY = e.clientY;
            startWidth = rect.width;
            startHeight = rect.height;
            startLeft = rect.left;
            startTop = rect.top;
        } else {
            isDragging = true;
            const rect = cropBox.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
        }
        e.preventDefault();
    });
    
    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            const canvas = passportPhotoCanvas;
            const rect = canvas.getBoundingClientRect();
            const newX = e.clientX - rect.left - startX;
            const newY = e.clientY - rect.top - startY;
            
            // Constrain to canvas bounds
            const maxX = canvas.width - cropBox.offsetWidth;
            const maxY = canvas.height - cropBox.offsetHeight;
            
            cropBox.style.left = Math.max(0, Math.min(maxX, newX)) + 'px';
            cropBox.style.top = Math.max(0, Math.min(maxY, newY)) + 'px';
            
            updateCropData();
        } else if (isResizing) {
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            const newSize = Math.max(50, Math.min(startWidth + deltaX, startHeight + deltaY));
            
            cropBox.style.width = newSize + 'px';
            cropBox.style.height = newSize + 'px';
            
            updateCropData();
        }
    });
    
    document.addEventListener('mouseup', function() {
        isDragging = false;
        isResizing = false;
    });
}

function updateCropBoxPosition() {
    if (!passportPhotoCropBox || !passportPhotoCanvas) return;
    
    const canvas = passportPhotoCanvas;
    const cropBox = passportPhotoCropBox;
    const overlay = document.getElementById('crop-overlay');
    
    // Update overlay dimensions
    overlay.style.width = canvas.width + 'px';
    overlay.style.height = canvas.height + 'px';
    
    // Constrain crop box to canvas
    const maxX = canvas.width - cropBox.offsetWidth;
    const maxY = canvas.height - cropBox.offsetHeight;
    
    const currentX = Math.max(0, Math.min(maxX, parseInt(cropBox.style.left) || 0));
    const currentY = Math.max(0, Math.min(maxY, parseInt(cropBox.style.top) || 0));
    
    cropBox.style.left = currentX + 'px';
    cropBox.style.top = currentY + 'px';
    
    updateCropData();
}

function updateCropData() {
    if (!passportPhotoCropBox || !passportPhotoCanvas) return;
    
    const cropBox = passportPhotoCropBox;
    const canvas = passportPhotoCanvas;
    
    passportPhotoCropData = {
        x: parseInt(cropBox.style.left) || 0,
        y: parseInt(cropBox.style.top) || 0,
        width: cropBox.offsetWidth,
        height: cropBox.offsetHeight
    };
    
    updatePreview();
}

function updatePreview() {
    if (!passportPhotoImage || !passportPhotoCropData || !passportPhotoPreviewCtx) return;
    
    const previewCanvas = document.getElementById('passport-photo-preview');
    const ctx = passportPhotoPreviewCtx;
    
    // Clear preview
    ctx.clearRect(0, 0, previewCanvas.width, previewCanvas.height);
    
    // Calculate source coordinates
    const sourceX = passportPhotoCropData.x / passportPhotoZoom;
    const sourceY = passportPhotoCropData.y / passportPhotoZoom;
    const sourceWidth = passportPhotoCropData.width / passportPhotoZoom;
    const sourceHeight = passportPhotoCropData.height / passportPhotoZoom;
    
    // Apply rotation
    ctx.save();
    ctx.translate(previewCanvas.width / 2, previewCanvas.height / 2);
    ctx.rotate((passportPhotoRotation * Math.PI) / 180);
    ctx.translate(-previewCanvas.width / 2, -previewCanvas.height / 2);
    
    // Draw cropped image to preview
    ctx.drawImage(
        passportPhotoImage,
        sourceX, sourceY, sourceWidth, sourceHeight,
        0, 0, previewCanvas.width, previewCanvas.height
    );
    
    ctx.restore();
}

function cropAndUploadPassportPhoto() {
    if (!passportPhotoImage || !passportPhotoCropData) {
        showPassportPhotoMessage('Please select and crop a photo first.', 'warning');
        return;
    }
    
    // Show progress
    showPassportPhotoProgress();
    
    // Create cropped image
    const croppedCanvas = document.createElement('canvas');
    const croppedCtx = croppedCanvas.getContext('2d');
    
    // Set canvas size to target dimension (1000x1000 pixels)
    const targetSize = 1000;
    croppedCanvas.width = targetSize;
    croppedCanvas.height = targetSize;
    
    // Calculate source coordinates
    const sourceX = passportPhotoCropData.x / passportPhotoZoom;
    const sourceY = passportPhotoCropData.y / passportPhotoZoom;
    const sourceWidth = passportPhotoCropData.width / passportPhotoZoom;
    const sourceHeight = passportPhotoCropData.height / passportPhotoZoom;
    
    // Apply rotation
    croppedCtx.save();
    croppedCtx.translate(targetSize / 2, targetSize / 2);
    croppedCtx.rotate((passportPhotoRotation * Math.PI) / 180);
    croppedCtx.translate(-targetSize / 2, -targetSize / 2);
    
    // Draw cropped and resized image
    croppedCtx.drawImage(
        passportPhotoImage,
        sourceX, sourceY, sourceWidth, sourceHeight,
        0, 0, targetSize, targetSize
    );
    
    croppedCtx.restore();
    
    // Convert to blob and upload
    croppedCanvas.toBlob(function(blob) {
        uploadCroppedPassportPhoto(blob);
    }, 'image/jpeg', 0.9);
}

function uploadCroppedPassportPhoto(blob) {
    const formData = new FormData();
    formData.append('passport_photo', blob, 'passport_photo.jpg');
    formData.append('kunregid', document.querySelector('input[name="kunregid"]').value);
    formData.append('safarid', document.querySelector('input[name="safarid"]').value);
    
    fetch('upload-kun-photos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hidePassportPhotoProgress();
        
        if (data.success) {
            passportPhotoUploadCompleted = true;
            showPassportPhotoMessage('Passport photo uploaded successfully!', 'success');
            updateFormProgress();
            
            // Hide cropper and show success state
            hidePassportPhotoCropper();
            showPassportPhotoSuccess();
        } else {
            showPassportPhotoMessage(data.message || 'Upload failed. Please try again.', 'danger');
        }
    })
    .catch(error => {
        hidePassportPhotoProgress();
        showPassportPhotoMessage('Upload failed. Please try again.', 'danger');
        console.error('Upload error:', error);
    });
}

function showPassportPhotoCropper() {
    const cropper = document.getElementById('passport-photo-cropper');
    const uploadArea = document.getElementById('passport-photo-upload-area');
    
    if (cropper) cropper.style.display = 'block';
    if (uploadArea) uploadArea.style.display = 'none';
}

function hidePassportPhotoCropper() {
    const cropper = document.getElementById('passport-photo-cropper');
    const uploadArea = document.getElementById('passport-photo-upload-area');
    
    if (cropper) cropper.style.display = 'none';
    if (uploadArea) uploadArea.style.display = 'block';
}

function showPassportPhotoSuccess() {
    const uploadArea = document.getElementById('passport-photo-upload-area');
    if (uploadArea) {
        uploadArea.innerHTML = `
            <div class="upload-content text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success mb-2">Passport Photo Uploaded</h5>
                <p class="text-muted mb-3">Your passport photo has been successfully uploaded and processed.</p>
                <button type="button" class="btn btn-outline-success" onclick="resetPassportPhotoUpload()">
                    <i class="fas fa-upload"></i> Upload Different Photo
                </button>
            </div>
        `;
    }
}

function resetPassportPhotoUpload() {
    passportPhotoData = null;
    passportPhotoUploadCompleted = false;
    passportPhotoZoom = 1;
    passportPhotoRotation = 0;
    
    const uploadArea = document.getElementById('passport-photo-upload-area');
    const fileInput = document.getElementById('passport-photo-file');
    
    if (uploadArea) {
        uploadArea.innerHTML = `
            <div class="upload-content text-center">
                <i class="fas fa-camera fa-3x text-brown mb-3"></i>
                <h5 class="text-brown mb-2">Upload Passport Photo</h5>
                <p class="text-muted mb-3">
                    Upload a clear photo for cropping to 50mm x 50mm<br>
                    <small class="text-info">
                        <i class="fas fa-info-circle"></i> Supported formats: JPG, PNG, GIF (Max 5MB)
                    </small>
                </p>
                <input type="file" id="passport-photo-file" name="passport_photo" 
                       accept="image/jpeg,image/png,image/jpg,image/gif" class="d-none">
                <button type="button" class="btn btn-outline-brown" onclick="document.getElementById('passport-photo-file').click()">
                    <i class="fas fa-folder-open"></i> Choose Photo
                </button>
            </div>
        `;
        
        // Re-attach event listener
        const newFileInput = document.getElementById('passport-photo-file');
        if (newFileInput) {
            newFileInput.addEventListener('change', handlePassportPhotoUpload);
        }
    }
    
    if (fileInput) fileInput.value = '';
    
    hidePassportPhotoCropper();
    updateFormProgress();
}

function cancelPassportPhotoCrop() {
    hidePassportPhotoCropper();
    resetPassportPhotoUpload();
}

function showPassportPhotoProgress() {
    const progress = document.getElementById('passport-photo-progress');
    if (progress) progress.style.display = 'block';
}

function hidePassportPhotoProgress() {
    const progress = document.getElementById('passport-photo-progress');
    if (progress) progress.style.display = 'none';
}

function showPassportPhotoMessage(message, type) {
    const messagesContainer = document.getElementById('passport-photo-messages');
    if (messagesContainer) {
        messagesContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }
}

function updateFileInfo(file) {
    const sizeInfo = document.getElementById('file-size-info');
    const dimensionsInfo = document.getElementById('file-dimensions-info');
    
    if (sizeInfo) {
        sizeInfo.textContent = formatFileSize(file.size);
    }
    
    if (dimensionsInfo && passportPhotoImage) {
        dimensionsInfo.textContent = `${passportPhotoImage.width} x ${passportPhotoImage.height}`;
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Update form progress to include passport photo
function updateFormProgress() {
    // Calculate passport upload section progress (15% of total)
    const passportProgress = passportUploadCompleted ? 15 : 0;
    
    // Calculate passport photo progress (10% of total)
    const passportPhotoProgress = passportPhotoUploadCompleted ? 10 : 0;
    
    // Calculate remaining form fields progress (75% of total)
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    const filledFields = Array.from(requiredFields).filter(field => {
        if (field.type === 'checkbox') {
            return field.checked;
        }
        return field.value.trim() !== '';
    });
    const formProgress = (filledFields.length / requiredFields.length) * 75;
    
    const progress = passportProgress + passportPhotoProgress + formProgress;
    
    const progressBar = document.getElementById('form-progress');
    if (progressBar) {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
    }
}
</script>

<style>
/* Brown theme variables */
:root {
    --brown-primary: #8B4513;
    --brown-secondary: #A0522D;
    --brown-light: #D2B48C;
    --brown-dark: #654321;
    --brown-accent: #DEB887;
}

/* Brown theme classes */
.bg-brown {
    background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
}

.bg-brown-light {
    background: linear-gradient(90deg, var(--brown-light), var(--brown-accent)) !important;
}

.btn-brown {
    background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary));
    border-color: var(--brown-primary);
    color: white;
    font-weight: 600;
}

.btn-brown:hover {
    background: linear-gradient(135deg, var(--brown-dark), var(--brown-primary));
    border-color: var(--brown-dark);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
}

.text-brown {
    color: var(--brown-primary) !important;
}

.text-brown:hover {
    color: var(--brown-dark) !important;
    text-decoration: none;
}

/* Bootstrap 5 Navbar brown theme */
.navbar.bg-brown {
    background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
}

.navbar-brand {
    font-weight: 600;
    font-size: 1.1rem;
}

.dropdown-menu {
    border: 1px solid var(--brown-light);
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
}

.dropdown-item:hover {
    background-color: var(--brown-light);
    color: var(--brown-dark);
}

/* Modern responsive form styling */
.form-section {
    background: linear-gradient(135deg, #faf9f7 0%, #ffffff 100%);
    border-radius: 10px;
    border: 1px solid #e8e2db;
    padding: 1.75rem;
    box-shadow: 0 2px 8px rgba(139, 69, 19, 0.08);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.form-section:hover {
    box-shadow: 0 4px 16px rgba(139, 69, 19, 0.12);
    transform: translateY(-2px);
}

.section-header {
    border-bottom: 2px solid var(--brown-light);
    padding-bottom: 0.75rem;
    margin-bottom: 1.25rem;
}

.section-title {
    color: var(--brown-dark);
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.section-title i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
    color: var(--brown-primary);
}

/* Arabic text styling */
.arabic-text {
    direction: rtl;
    text-align: right;
    font-family: 'Amiri', 'Scheherazade', 'Tahoma', serif;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Enhanced progress bar */
.progress {
    height: 6px;
    background: linear-gradient(90deg, #e8e2db, #f5f1eb);
    border-radius: 8px;
    box-shadow: inset 0 1px 2px rgba(139, 69, 19, 0.1);
}

.progress-bar {
    border-radius: 8px;
    transition: width 0.6s ease, background-color 0.3s ease;
}

.progress-bar.bg-brown-light {
    background: linear-gradient(90deg, var(--brown-light), var(--brown-accent)) !important;
}

/* Enhanced form controls */
.form-control {
    border-radius: 6px;
    border: 2px solid #e8e2db;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #ffffff;
}

.form-control:focus {
    border-color: var(--brown-primary);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
    background: #ffffff;
    outline: none;
}

.form-control:hover:not(:focus) {
    border-color: var(--brown-light);
}

.form-control-lg {
    padding: 1rem 1.25rem;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Card enhancements */
.card {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e8e2db;
}

.card-header.bg-brown {
    border: none;
    padding: 1.25rem 1.75rem;
}

.card-body {
    background: #fefefe;
    padding: 1.5rem;
}

/* Button enhancements */
.btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    border-width: 2px;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Validation styling */
.is-invalid {
    border-color: #dc3545;
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    font-weight: 500;
}

/* Alert styling */
.alert {
    border-radius: 10px;
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 500;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .form-section {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
    
    .card-header.bg-brown {
        padding: 1rem 1.25rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg {
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
    }
    
    .progress {
        height: 5px;
    }
    
    .form-control {
        font-size: 16px; /* Prevents zoom on iOS */
        padding: 0.625rem 0.875rem;
    }
    
    .section-header {
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .section-title {
        font-size: 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* Collapsible animations */
.collapse.show {
    animation: slideDown 0.4s ease;
}

.collapse:not(.show) {
    animation: slideUp 0.4s ease;
}

@keyframes slideDown {
    from { 
        opacity: 0; 
        transform: translateY(-15px);
        max-height: 0;
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
        max-height: 1000px;
    }
}

@keyframes slideUp {
    from { 
        opacity: 1; 
        transform: translateY(0);
        max-height: 1000px;
    }
    to { 
        opacity: 0; 
        transform: translateY(-15px);
        max-height: 0;
    }
}

/* Transition utilities */
.transition-all {
    transition: all 0.3s ease;
}

/* Form check styling */
.form-check-label {
    font-size: 0.95rem;
    line-height: 1.6;
}

.form-check-input:checked {
    background-color: var(--brown-primary);
    border-color: var(--brown-primary);
}

/* Breadcrumb styling */
.breadcrumb {
    background: linear-gradient(135deg, #faf9f7 0%, #f5f1eb 100%);
    border-radius: 8px;
    padding: 0.875rem 1.125rem;
    box-shadow: 0 2px 6px rgba(139, 69, 19, 0.08);
    border: 1px solid #e8e2db;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
}

/* Loading states */
.form-control:disabled {
    background-color: #f8f9fa;
    opacity: 0.8;
}

/* Focus management for accessibility */
.form-control:focus,
.btn:focus {
    outline: none;
    box-shadow: 0 0 0 0.25rem rgba(139, 69, 19, 0.25);
}

/* Bootstrap 5 specific enhancements */
.btn-close:focus {
    box-shadow: 0 0 0 0.25rem rgba(139, 69, 19, 0.25);
}

.navbar-brand:hover {
    color: var(--brown-light) !important;
}

/* Ensure proper spacing with Bootstrap 5 */
.main-content {
    padding-top: 80px;
}

/* Bootstrap 5 alert improvements */
.alert {
    border: none;
    border-radius: 8px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Passport Upload Styles */
.upload-area {
    border: 3px dashed var(--brown-light);
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
    background: linear-gradient(135deg, #faf9f7 0%, #ffffff 100%);
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: var(--brown-primary);
    background: linear-gradient(135deg, #f5f1eb 0%, #faf9f7 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(139, 69, 19, 0.15);
}

.upload-area.drag-over {
    border-color: var(--brown-primary);
    background: linear-gradient(135deg, #f0e6d2 0%, #f5f1eb 100%);
    transform: scale(1.02);
}

.upload-content {
    pointer-events: none;
}

.upload-content i {
    opacity: 0.8;
    transition: all 0.3s ease;
}

.upload-area:hover .upload-content i {
    opacity: 1;
    transform: scale(1.1);
}

.file-preview-card {
    border: 1px solid #e8e2db;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: #ffffff;
}

.file-preview-card:hover {
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
    transform: translateY(-2px);
}

.file-preview-card .card-title {
    color: var(--brown-dark);
    font-size: 0.9rem;
    font-weight: 600;
}

.file-info small {
    color: #6c757d;
    font-size: 0.8rem;
}

.file-status .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.upload-progress .progress {
    background-color: #f8f9fa;
    border-radius: 4px;
}

.upload-progress .progress-bar {
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* OCR Preview Styles */
#ocr-preview-container .card {
    border: 1px solid var(--brown-light);
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(139, 69, 19, 0.1);
}

#ocr-preview-container .card-header {
    border-radius: 12px 12px 0 0;
    border: none;
    padding: 1rem 1.25rem;
}

#ocr-preview-container .card-body {
    padding: 1.25rem;
}

.ocr-data-table {
    border-radius: 8px;
    overflow: hidden;
}

.ocr-data-table .table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.ocr-data-table .table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    padding: 0.75rem;
    font-weight: 600;
    color: var(--brown-dark);
    font-size: 0.9rem;
}

.ocr-data-table .table tbody td {
    padding: 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

.ocr-data-table .table tbody tr:hover {
    background-color: #f8f9fa;
}

.ocr-data-table .table tbody tr.table-success {
    background-color: #d4edda;
}

.ocr-data-table .table tbody tr.table-danger {
    background-color: #f8d7da;
}

.ocr-value {
    font-weight: 600;
    color: var(--brown-primary);
}

.current-value {
    color: #6c757d;
    font-style: italic;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

/* Quality Feedback Styles */
#quality-feedback .alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.25rem;
}

#quality-feedback .alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border-left: 4px solid #ffc107;
}

#quality-feedback .alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

#quality-feedback .alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Button outline brown */
.btn-outline-brown {
    color: var(--brown-primary);
    border-color: var(--brown-primary);
    background-color: transparent;
}

.btn-outline-brown:hover {
    color: white;
    background-color: var(--brown-primary);
    border-color: var(--brown-primary);
}

.btn-outline-brown:focus {
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
}

/* Border success animation */
.border-success {
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    transition: all 0.3s ease;
}

/* Mobile optimizations for passport upload */
@media (max-width: 768px) {
    .upload-area {
        padding: 2rem 1rem;
    }
    
    .upload-area h5 {
        font-size: 1.1rem;
    }
    
    .upload-area p {
        font-size: 0.9rem;
    }
    
    .file-preview-card .card-body {
        padding: 1rem;
    }
    
    .ocr-data-table .table {
        font-size: 0.85rem;
    }
    
    .ocr-data-table .table thead th,
    .ocr-data-table .table tbody td {
        padding: 0.5rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .upload-area {
        padding: 1.5rem 0.75rem;
    }
    
    .upload-content i {
        font-size: 2rem !important;
    }
    
    .upload-area h5 {
        font-size: 1rem;
    }
    
    .upload-area p {
        font-size: 0.8rem;
    }
    
    .file-preview-card .card-title {
        font-size: 0.8rem;
        max-width: 120px !important;
    }
    
    .ocr-data-table .table {
        font-size: 0.8rem;
    }
    
    .ocr-data-table .table thead th,
    .ocr-data-table .table tbody td {
        padding: 0.4rem;
    }
    
    #ocr-preview-container .card-body {
        padding: 1rem;
    }
}

/* Print styles */
@media print {
    .btn, .breadcrumb, .card-header {
        display: none;
    }
    
    .form-section {
        box-shadow: none;
        border: 1px solid #000;
        break-inside: avoid;
    }
    
    .upload-area, #file-preview-container, #ocr-preview-container {
        display: none !important;
    }
}

/* Passport Photo Cropping Styles */
.cropping-container {
    position: relative;
    display: inline-block;
    max-width: 100%;
}

#passport-photo-canvas {
    display: block;
    max-width: 100%;
    height: auto;
    cursor: crosshair;
}

.crop-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.crop-box {
    position: absolute;
    border: 2px solid #8B4513;
    background: rgba(139, 69, 19, 0.1);
    pointer-events: all;
    cursor: move;
}

.crop-handle {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #8B4513;
    border: 2px solid white;
    border-radius: 50%;
    cursor: pointer;
}

.crop-handle-nw {
    top: -6px;
    left: -6px;
    cursor: nw-resize;
}

.crop-handle-ne {
    top: -6px;
    right: -6px;
    cursor: ne-resize;
}

.crop-handle-sw {
    bottom: -6px;
    left: -6px;
    cursor: sw-resize;
}

.crop-handle-se {
    bottom: -6px;
    right: -6px;
    cursor: se-resize;
}

.cropping-controls {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.preview-container {
    background: white;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.file-info {
    background: white;
    padding: 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

#passport-photo-progress {
    margin-top: 1rem;
}

#passport-photo-messages .alert {
    margin-bottom: 0;
}

/* Responsive adjustments for cropping */
@media (max-width: 768px) {
    .cropping-container {
        margin-bottom: 1rem;
    }
    
    .cropping-controls {
        padding: 0.75rem;
    }
    
    .preview-container {
        min-height: 150px;
    }
    
    #passport-photo-preview {
        width: 120px !important;
        height: 120px !important;
    }
}
</style>

    <?php
    }
}
?>
