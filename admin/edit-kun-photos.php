<?php
//NEW SAFAR SCRIPT
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';
$kunregid = (isset($_GET['kunregid']) && $_GET['kunregid'] != '') ? $_GET['kunregid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars" class="text-brown">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="text-brown">KUN - <?php echo $safar_name; ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">Update Photos</li>
  </ol>
</nav>

<?php
$sql_edit = "SELECT * FROM zyt_kun_registrations WHERE kun_id = $kunregid";
$result_edit = dbQuery($sql_edit);
if(dbNumRows($result_edit) > 0){
    while($row_edit = dbFetchAssoc($result_edit)){
        extract($row_edit);
    ?>

<!-- Success/Error Messages -->
<?php if(isset($_GET['success']) && $_GET['success'] == '1'): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Success!</strong> Photos have been uploaded successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Error!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Applicant Information Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h6 class="card-title text-brown mb-3"><i class="fas fa-info-circle me-2"></i>Applicant Information</h6>
        <div class="row">
            <div class="col-md-3">
                <strong>Name:</strong> <?php echo $kun_fname . ' ' . $kun_mname . ' ' . $kun_lname; ?>
            </div>
            <div class="col-md-3">
                <strong>ITS ID:</strong> <?php echo $kun_its; ?>
            </div>
            <div class="col-md-3">
                <strong>Passport Number:</strong> <?php echo $kun_ppno; ?>
            </div>
            <div class="col-md-3">
                <strong>Date of Birth:</strong> <?php echo $kun_dob; ?>
            </div>
        </div>
    </div>
</div>

<!-- SECTION A: Passport Documents Upload -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-brown text-white">
        <h4 class="mb-0">
            <i class="fas fa-passport me-2"></i>Section A: Passport Documents Upload
        </h4>
        <small class="opacity-75">Upload passport information page and additional pages</small>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info">
            <strong><i class="fas fa-info-circle me-2"></i>Upload Instructions:</strong>
            <ul class="mb-0 mt-2">
                <li>First file uploaded will be the <strong>Passport Information Page</strong> (bio page with photo)</li>
                <li>Additional files will be tagged as <strong>Additional Passport Pages</strong></li>
                <li>OCR extraction will automatically run on the first file</li>
                <li>Quality validation is enabled (admin can override warnings)</li>
            </ul>
        </div>
        
        <form id="passport-documents-form" enctype="multipart/form-data">
            <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
            <input type="hidden" name="kunregid" value="<?php echo $kunregid; ?>" />
            <input type="hidden" name="admin_override" id="admin_override_docs" value="0" />
            
            <div class="upload-area p-4 border border-2 border-dashed rounded mb-3">
                <div class="text-center">
                    <i class="fas fa-file-upload fa-3x text-primary mb-3"></i>
                    <h6>Select Passport Document Files</h6>
                    <p class="text-muted mb-3">First file = Info Page, Subsequent = Additional Pages</p>
                    
                    <input type="file" id="passportDocuments" name="kunPhotos[]" multiple 
                           class="form-control form-control-lg" accept=".jpg,.jpeg,.png" required>
                    
                    <div class="form-text mt-2">
                        <strong>Requirements:</strong> JPG/PNG only | Max 5MB per file | Up to 10 files
                    </div>
                </div>
                
                <!-- File Preview -->
                <div id="docsPreview" class="mt-3" style="display: none;">
                    <h6 class="text-primary">Selected Files:</h6>
                    <div id="docsFileList" class="row g-2"></div>
                </div>
            </div>
            
            <!-- Admin Override Option -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="overrideCheckDocs" onchange="document.getElementById('admin_override_docs').value = this.checked ? '1' : '0';">
                <label class="form-check-label text-warning" for="overrideCheckDocs">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Admin Override:</strong> Bypass quality warnings (use with caution)
                </label>
            </div>
            
            <!-- OCR Result Display -->
            <div id="ocrResultContainer" style="display: none;" class="mb-3">
                <div class="alert alert-success">
                    <h6 class="alert-heading"><i class="fas fa-robot me-2"></i>OCR Extraction Results</h6>
                    <div id="ocrResultContent"></div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" onclick="populateFormFromOCR()">
                        <i class="fas fa-arrow-right me-1"></i>Apply to Registration Form
                    </button>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-block">
                <button type="submit" class="btn btn-primary btn-lg me-2" id="submitDocumentsBtn" disabled>
                    <i class="fas fa-upload me-2"></i>Upload Passport Documents
                </button>
                <button type="reset" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-undo me-2"></i>Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SECTION B: Passport Photo Upload -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0">
            <i class="fas fa-camera me-2"></i>Section B: Passport Photo Upload
        </h4>
        <small class="opacity-75">Upload passport-size photo (51mm x 51mm at 120 DPI)</small>
    </div>
    
    <div class="card-body">
        <div class="alert alert-warning">
            <strong><i class="fas fa-info-circle me-2"></i>Photo Requirements:</strong>
            <ul class="mb-0 mt-2">
                <li>Passport-size photo of the applicant</li>
                <li>Final size: 51mm x 51mm (240x240 pixels at 120 DPI)</li>
                <li>After upload, you'll be able to crop the photo interactively</li>
                <li>Only one passport photo per applicant</li>
            </ul>
        </div>
        
        <form id="passport-photo-form" enctype="multipart/form-data">
            <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
            <input type="hidden" name="kunregid" value="<?php echo $kunregid; ?>" />
            <input type="hidden" name="admin_override" id="admin_override_photo" value="0" />
            <input type="hidden" name="crop_x" id="crop_x" value="0" />
            <input type="hidden" name="crop_y" id="crop_y" value="0" />
            <input type="hidden" name="crop_width" id="crop_width" value="0" />
            <input type="hidden" name="crop_height" id="crop_height" value="0" />
            
            <div class="upload-area p-4 border border-2 border-dashed rounded mb-3">
                <div class="text-center">
                    <i class="fas fa-user-circle fa-3x text-success mb-3"></i>
                    <h6>Select Passport Photo</h6>
                    <p class="text-muted mb-3">One photo file only</p>
                    
                    <input type="file" id="passportPhoto" name="passport_photo" 
                           class="form-control form-control-lg" accept=".jpg,.jpeg,.png" required>
                    
                    <div class="form-text mt-2">
                        <strong>Requirements:</strong> JPG/PNG only | Max 5MB | Square or portrait orientation
                    </div>
                </div>
            </div>
            
            <!-- Admin Override Option -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="overrideCheckPhoto" onchange="document.getElementById('admin_override_photo').value = this.checked ? '1' : '0';">
                <label class="form-check-label text-warning" for="overrideCheckPhoto">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Admin Override:</strong> Bypass quality warnings (use with caution)
                </label>
            </div>
            
            <div class="d-grid gap-2 d-md-block">
                <button type="submit" class="btn btn-success btn-lg me-2" id="submitPhotoBtn" disabled>
                    <i class="fas fa-upload me-2"></i>Upload & Crop Passport Photo
                </button>
                <button type="reset" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-undo me-2"></i>Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-crop me-2"></i>Crop Passport Photo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Drag to reposition and resize the crop box to frame the passport photo. The final image will be 51mm x 51mm (240x240 pixels at 120 DPI).
                </div>
                <div class="text-center">
                    <img id="cropperImage" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmCropBtn">
                    <i class="fas fa-check me-2"></i>Confirm & Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Current Photos Section -->
<?php
$sql_existing_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kunregid ORDER BY photo_type, photo_upload_time DESC";
$result_existing_photos = dbQuery($sql_existing_photos);
if(dbNumRows($result_existing_photos) > 0){
?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">
            <i class="fas fa-images me-2"></i>Current Photos
        </h4>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php
            while($photo_row = dbFetchAssoc($result_existing_photos)){
                extract($photo_row);
                $photoTypeLabel = '';
                $photoTypeClass = 'secondary';
                switch($photo_type) {
                    case 'passport_info':
                        $photoTypeLabel = 'Passport Info Page';
                        $photoTypeClass = 'primary';
                        break;
                    case 'passport_page':
                        $photoTypeLabel = 'Additional Page';
                        $photoTypeClass = 'info';
                        break;
                    case 'passport_photo':
                        $photoTypeLabel = 'Passport Photo';
                        $photoTypeClass = 'success';
                        break;
                    default:
                        $photoTypeLabel = 'Document';
                        $photoTypeClass = 'secondary';
                }
                
                $thumbnailPath = $photo_filename_thumb ? 'images/uploads/' . $photo_filename_thumb : 'images/uploads/' . $photo_filename;
            ?>
            <div class="col-md-3 col-6">
                <div class="card photo-card h-100">
                    <div class="card-body text-center p-3">
                        <span class="badge bg-<?php echo $photoTypeClass; ?> mb-2"><?php echo $photoTypeLabel; ?></span>
                        <img src="<?php echo $server_link . $thumbnailPath; ?>" 
                             class="img-fluid mb-2" 
                             alt="<?php echo $photoTypeLabel; ?>"
                             style="max-height: 150px; object-fit: cover;">
                        <small class="d-block text-muted"><?php echo $photo_filename; ?></small>
                        <div class="mt-2">
                            <a href="<?php echo $server_link . 'images/uploads/' . $photo_filename; ?>" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?php echo $server_link; ?>delete_photo.php?pid=<?php echo $photo_id; ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete this photo?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
}
?>

<!-- Back to Dashboard -->
<div class="text-center mt-4 mb-4">
    <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-outline-brown">
        <i class="fas fa-arrow-left me-2"></i>Back to Safar
    </a>
</div>

<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<style>
.upload-area {
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
}

.upload-area:hover {
    background: linear-gradient(135deg, #faf9f7 0%, #f5f1eb 100%);
    border-color: var(--brown-primary) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
}

.photo-card {
    transition: all 0.3s ease;
    border: 1px solid #e8e2db;
}

.photo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
    border-color: var(--brown-primary);
}

.card.border-success {
    border-color: #198754 !important;
    background: linear-gradient(135deg, #f8fff9 0%, #f0fff1 100%);
}

button:disabled {
    cursor: not-allowed;
}
</style>

<!-- Cropper.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<!-- Enhanced JavaScript for Photo Upload -->
<script>
let cropper = null;
let ocrData = null;

document.addEventListener('DOMContentLoaded', function() {
    
    // SECTION A: Passport Documents Upload
    const docsInput = document.getElementById('passportDocuments');
    const docsSubmitBtn = document.getElementById('submitDocumentsBtn');
    const docsForm = document.getElementById('passport-documents-form');
    
    docsInput.addEventListener('change', function() {
        const files = this.files;
        if (files.length > 0) {
            docsSubmitBtn.disabled = false;
            showDocumentsPreview(files);
        } else {
            docsSubmitBtn.disabled = true;
        }
    });
    
    docsForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        docsSubmitBtn.disabled = true;
        docsSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
        
        try {
            const response = await fetch('<?php echo $server_link; ?>passport_upload_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show OCR data if available
                if (result.data && result.data.ocr_data) {
                    displayOCRResults(result.data.ocr_data);
                }
                
                showAlert('success', 'Passport documents uploaded successfully!' + 
                    (result.warnings.length > 0 ? ' (With warnings: ' + result.warnings.join(', ') + ')' : ''));
                
                // Reload page after 2 seconds
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('danger', 'Upload failed: ' + (result.error || result.message));
            }
        } catch (error) {
            showAlert('danger', 'Upload error: ' + error.message);
        } finally {
            docsSubmitBtn.disabled = false;
            docsSubmitBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Passport Documents';
        }
    });
    
    // SECTION B: Passport Photo Upload
    const photoInput = document.getElementById('passportPhoto');
    const photoSubmitBtn = document.getElementById('submitPhotoBtn');
    const photoForm = document.getElementById('passport-photo-form');
    
    photoInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            photoSubmitBtn.disabled = false;
        } else {
            photoSubmitBtn.disabled = true;
        }
    });
    
    photoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const file = photoInput.files[0];
        if (!file) return;
        
        // Show cropper modal
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('cropperImage').src = event.target.result;
            const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
            cropperModal.show();
            
            // Initialize cropper
            document.getElementById('cropperModal').addEventListener('shown.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                }
                
                const image = document.getElementById('cropperImage');
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    minCropBoxWidth: 240,
                    minCropBoxHeight: 240,
                    autoCropArea: 0.8,
                    responsive: true,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true
                });
            });
        };
        reader.readAsDataURL(file);
    });
    
    // Confirm crop button
    document.getElementById('confirmCropBtn').addEventListener('click', async function() {
        if (!cropper) return;
        
        const cropData = cropper.getData();
        document.getElementById('crop_x').value = Math.round(cropData.x);
        document.getElementById('crop_y').value = Math.round(cropData.y);
        document.getElementById('crop_width').value = Math.round(cropData.width);
        document.getElementById('crop_height').value = Math.round(cropData.height);
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
        
        // Upload photo
        const formData = new FormData(photoForm);
        photoSubmitBtn.disabled = true;
        photoSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
        
        try {
            // Note: You'll need to create upload-passport-photo.php handler
            const response = await fetch('<?php echo $server_link; ?>upload-passport-photo.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('success', 'Passport photo uploaded successfully!');
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('danger', 'Upload failed: ' + (result.error || result.message));
            }
        } catch (error) {
            showAlert('danger', 'Upload error: ' + error.message);
        } finally {
            photoSubmitBtn.disabled = false;
            photoSubmitBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload & Crop Passport Photo';
        }
    });
});

function showDocumentsPreview(files) {
    const preview = document.getElementById('docsPreview');
    const fileList = document.getElementById('docsFileList');
    fileList.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileType = i === 0 ? 'Passport Info Page' : 'Additional Page';
        const badgeClass = i === 0 ? 'primary' : 'info';
        
        const fileDiv = document.createElement('div');
        fileDiv.className = 'col-md-4 col-6';
        fileDiv.innerHTML = `
            <div class="card border-${badgeClass}">
                <div class="card-body p-2 text-center">
                    <span class="badge bg-${badgeClass} mb-2">${fileType}</span>
                    <i class="fas fa-file-image fa-2x text-${badgeClass} mb-2"></i>
                    <h6 class="card-title small">${file.name}</h6>
                    <p class="card-text small text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            </div>
        `;
        fileList.appendChild(fileDiv);
    }
    
    preview.style.display = 'block';
}

function displayOCRResults(data) {
    ocrData = data;
    const container = document.getElementById('ocrResultContainer');
    const content = document.getElementById('ocrResultContent');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6"><strong>Passport No:</strong> ${data.kun_ppno || 'N/A'}</div>
            <div class="col-md-6"><strong>Expiry Date:</strong> ${data.kun_ppexpiry || 'N/A'}</div>
            <div class="col-md-6"><strong>First Name:</strong> ${data.kun_fname || 'N/A'}</div>
            <div class="col-md-6"><strong>Last Name:</strong> ${data.kun_lname || 'N/A'}</div>
            <div class="col-md-6"><strong>Nationality:</strong> ${data.kun_nationality || 'N/A'}</div>
            <div class="col-md-6"><strong>Date of Birth:</strong> ${data.kun_dob || 'N/A'}</div>
            <div class="col-md-6"><strong>Gender:</strong> ${data.kun_gender || 'N/A'}</div>
        </div>
    `;
    
    container.style.display = 'block';
}

function populateFormFromOCR() {
    if (!ocrData) return;
    
    // This function would populate the registration form if it's on the same page
    // For now, just show a message
    alert('OCR data can be applied when editing the registration form. Data has been saved with the passport upload.');
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.breadcrumb').after(alertDiv);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => alertDiv.remove(), 5000);
}
</script>
    <?php
    }
}
?>
