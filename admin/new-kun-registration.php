<?php
//NEW SAFAR SCRIPT
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));

// Include nationality options
require_once 'nationality_options.php';
?>
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars" class="text-brown">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="text-brown">KUN - <?php echo $safar_name; ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">New Registration</li>
  </ol>
</nav>

<!-- Success/Error Messages -->
<?php if(isset($_GET['success']) && $_GET['success'] == '1'): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Success!</strong> Registration has been created successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['e'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Error!</strong> Something went wrong. Please check the date format (yyyy-mm-dd) and try again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Main Registration Card -->
<div class="card shadow-sm">
    <div class="card-header bg-brown text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-user-plus me-2"></i>New KUN Registration
            </h4>
            <small class="opacity-75">Safar: <?php echo $safar_name; ?></small>
        </div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="progress" style="height: 4px; border-radius: 0;">
        <div class="progress-bar bg-brown-light" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemax="100" id="formProgress"></div>
    </div>
    
    <div class="card-body">
        <form action="<?php echo $server_link; ?>update-kun-registration.php" method="post" id="kun-registration" class="needs-validation" novalidate>
            <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
            
            <!-- Section 1: Basic Information -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-user me-2"></i>Basic Information
                </h5>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="its" class="form-label required">ITS Number</label>
                    <input type="text" class="form-control" id="its" name="its" required>
                    <div class="form-text">Eight digit number</div>
                    <div class="invalid-feedback">Please provide a valid ITS number.</div>
                </div>
                <div class="col-md-4">
                    <label for="dob" class="form-label required">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                    <div class="invalid-feedback">Please provide date of birth.</div>
                </div>
                <div class="col-md-4">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" class="form-control" id="age" name="age" readonly>
                    <div class="form-text">Calculated automatically</div>
                </div>
            </div>

            <!-- Section 2: Passport & Identity -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-passport me-2"></i>Passport & Identity Details
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="fname" class="form-label required">Passport First Name</label>
                    <input type="text" class="form-control" id="fname" name="fname" required>
                    <div class="invalid-feedback">Please provide first name.</div>
                </div>
                <div class="col-md-4">
                    <label for="mname" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="mname" name="mname">
                </div>
                <div class="col-md-4">
                    <label for="lname" class="form-label required">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" required>
                    <div class="invalid-feedback">Please provide last name.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <label for="passport_arabic_name" class="form-label">Passport Arabic Name</label>
                    <input type="text" class="form-control" id="passport_arabic_name" name="passport_arabic_name" readonly>
                    <div class="form-text">Auto-translated from English names above</div>
                </div>
            </div>

            <!-- Section 3: Contact Information -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-address-book me-2"></i>Contact Information</h5>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="address" class="form-label required">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                    <div class="invalid-feedback">Please provide address.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="contact" class="form-label required">Mobile Number</label>
                    <input type="tel" class="form-control" id="contact" name="contact" required>
                    <div class="invalid-feedback">Please provide mobile number.</div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>
            </div>
            
            <!-- Section 4: Passport Details -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-id-card me-2"></i>Passport Details
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="ppno" class="form-label required">Passport Number</label>
                    <input type="text" class="form-control" id="ppno" name="ppno" required>
                    <div class="invalid-feedback">Please provide passport number.</div>
                </div>
                <div class="col-md-4">
                    <label for="passport_type" class="form-label required">Passport Type</label>
                    <select class="form-select" id="passport_type" name="passport_type" required>
                        <option value="">Select Type</option>
                        <option value="Normal">Normal</option>
                        <option value="Diplomatic">Diplomatic</option>
                        <option value="Temporary">Temporary</option>
                    </select>
                    <div class="invalid-feedback">Please select passport type.</div>
                </div>
                <div class="col-md-4">
                    <label for="nationality" class="form-label required">Nationality</label>
                    <select class="form-select" id="nationality" name="nationality" required>
                        <?php echo generateNationalityOptions(); ?>
                    </select>
                    <div class="invalid-feedback">Please select nationality.</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ppissue" class="form-label required">Issue Date</label>
                    <input type="date" class="form-control" id="ppissue" name="ppissue" required>
                    <div class="invalid-feedback">Please provide issue date.</div>
                </div>
                <div class="col-md-6">
                    <label for="ppexpiry" class="form-label required">Expiry Date</label>
                    <input type="date" class="form-control" id="ppexpiry" name="ppexpiry" required>
                    <div class="invalid-feedback">Please provide expiry date.</div>
                    <div id="passportExpiryError" class="text-danger mt-1" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>Passport must be valid for at least 6 months.
                    </div>
                </div>
            </div>
            
            <!-- Section 5: Family Names -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-users me-2"></i>Family Names
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="father_name" class="form-label required">Father Name</label>
                    <input type="text" class="form-control" id="father_name" name="father_name" required>
                    <div class="invalid-feedback">Please provide father's name.</div>
                </div>
                <div class="col-md-6">
                    <label for="father_name_arabic" class="form-label">Father Name (Arabic)</label>
                    <input type="text" class="form-control" id="father_name_arabic" name="father_name_arabic" readonly>
                    <div class="form-text">Auto-translated from English name</div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ppno">Passport Details</label>
                <div class="controls">
                    <input type="text" id="ppno" name="ppno" placeholder="PP No" class="span3" required>
                    <div class="multi-input-space"></div>
                    <input type="text" id="ppissue" name="ppissue" placeholder="Issue Date" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" >
                    <input type="text" id="ppexpiry" name="ppexpiry" placeholder="Expiry Date" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" >
                    <input type="text" id="placeofissue" name="placeofissue" placeholder="Place of Issue" class="span3" >
                    <input type="text" id="countryofissue" name="countryofissue" placeholder="Country of Issue" class="span3" >
                    <div class="multi-input-space"></div>
                    <input type="text" id="nationality" name="nationality" placeholder="Current Nationality" class="span3" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dob">Birth Details</label>
                <div class="controls">
                    <input type="text" id="dob" name="dob" placeholder="Date of Birth" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" >
                    <div class="multi-input-space"></div>
                    <span class="help-inline">Gender</span>
                    <label class="radio inline">
                        <input type="radio" name="gender" id="gender" value="M" >
                        Male
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="gender" id="gender" value="F" >
                        Female
                    </label>
                    <div class="multi-input-space"></div>
                    <input type="text" id="birthplace" name="birthplace" placeholder="Birth Place" class="span3" >
                    <input type="text" id="birthcountry" name="birthcountry" placeholder="Birth Country" class="span3" >
                    <input type="text" id="orignationality" name="orignationality" placeholder="Original Nationality" class="span3" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="certs">Professional Details</label>
                <div class="controls">
                    <select id="certs" name="certs" data-placeholder="Select Educational Level.." class="span4" >
                        <option value="">Select Educational Level..</option>
                        <option value="O Levels">O Levels</option>
                        <option value="A Levels">A Levels</option>
                        <option value="Bachelors Degree">Bachelors Degree</option>
                        <option value="Masters Degree">Masters Degree</option>
                        <option value="PhD">PhD</option>
                    </select>
                    <div class="multi-input-space"></div>
                    <input type="text" id="dateissuecerts" name="dateissuecerts" placeholder="Date of Issuing Certificates" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" >
                    <input type="text" id="issuecerts" name="issuecerts" placeholder="Place of Issuing Certificates" class="span5" >
                    <div class="multi-input-space"></div>
                    <input type="text" id="profession" name="profession" placeholder="Professional Title" class="span4" >
                    <input type="text" id="curroccupation" name="curroccupation" placeholder="Current Occupation" class="span4" >
                    <div class="multi-input-space"></div>
                    <input type="text" id="langspoken" name="langspoken" placeholder="Languages Spoken" class="span6" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="spname">Spouse Details</label>
                <div class="controls">
                    <input type="text" id="spname" name="spname" placeholder="Spouse Name" class="span5">
                    <div class="multi-input-space"></div>
                    <input type="text" id="spdob" name="spdob" placeholder="Spouse Date of Birth" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <input type="text" id="sppob" name="sppob" placeholder="Spouse Place of Birth" class="span3">
                    <div class="multi-input-space"></div>
                    <input type="text" id="spoccupation" name="spoccupation" placeholder="Spouse Occupation" class="span3">
                    <input type="text" id="spnationality" name="spnationality" placeholder="Spouse Nationality" class="span3">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="numpreventry">Iraq/Karbala Details</label>
                <div class="controls">
                    <input type="number" id="numpreventry" name="numpreventry" placeholder="Number of Previous Entries" class="span4" >
                    <input type="text" id="datelentry" name="datelentry" placeholder="Date of Last Entry" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <div class="multi-input-space"></div>
                    <input type="number" id="accomppersons" name="accomppersons" placeholder="Accompanying Persons" class="span4" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="child1name">Children's Details</label>
                <div class="controls">
                    <span class="help-inline">1.</span>
                    <input type="text" id="child1name" name="child1name" placeholder="Name" class="span4">
                    <input type="text" id="child1dob" name="child1dob" placeholder="Date of birth" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <input type="text" id="child1occ" name="child1occ" placeholder="Occupation" class="span4">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">2.</span>
                    <input type="text" id="child2name" name="child2name" placeholder="Name" class="span4">
                    <input type="text" id="child2dob" name="child2dob" placeholder="Date of birth" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <input type="text" id="child2occ" name="child2occ" placeholder="Occupation" class="span4">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">3.</span>
                    <input type="text" id="child3name" name="child3name" placeholder="Name" class="span4">
                    <input type="text" id="child3dob" name="child3dob" placeholder="Date of birth" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <input type="text" id="child3occ" name="child3occ" placeholder="Occupation" class="span4">
                    <div class="multi-input-space"></div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="visit1country">Last 3 Countries Visited</label>
                <div class="controls">
                    <span class="help-inline">1.</span>
                    <input type="text" id="visit1country" name="visit1country" placeholder="Name" class="span4">
                    <input type="text" id="visit1reason" name="visit1reason" placeholder="Reason" class="span4">
                    <input type="text" id="visit1date" name="visit1date" placeholder="Date of travel" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">2.</span>
                    <input type="text" id="visit2country" name="visit2country" placeholder="Name" class="span4">
                    <input type="text" id="visit2reason" name="visit2reason" placeholder="Reason" class="span4">
                    <input type="text" id="visit2date" name="visit2date" placeholder="Date of travel" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">3.</span>
                    <input type="text" id="visit3country" name="visit3country" placeholder="Name" class="span4">
                    <input type="text" id="visit3reason" name="visit3reason" placeholder="Reason" class="span4">
                    <input type="text" id="visit3date" name="visit3date" placeholder="Date of travel" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                </div>
            </div>


            <!-- Declaration Section -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-file-signature me-2"></i>Declaration
                </h5>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="declaration" id="declaration" value="1" required>
                        <label class="form-check-label" for="declaration">
                            <strong>I hereby declare</strong> that the details furnished above are true and correct to the best of my knowledge and I will undertake to inform Shababul Eidiz Zahabi, Colombo of any changes therein, immediately. I also authorize Shababul Eidiz Zahabi, Colombo to proceed with my application visa process and will not hold them responsible for any complications due to misleading or misrepresented information provided on this form.
                        </label>
                        <div class="invalid-feedback">You must accept the declaration to proceed.</div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-paper-plane me-2"></i>Form Actions
                </h5>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-brown btn-lg me-2" id="submitBtn">
                            <i class="fas fa-user-plus me-2"></i>Create Registration
                        </button>
                        <button type="reset" class="btn btn-outline-warning btn-lg me-2">
                            <i class="fas fa-undo me-2"></i>Reset Form
                        </button>
                        <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Back to Dashboard -->
<div class="text-center mt-4">
    <a href="<?php echo $server_link; ?>" class="btn btn-outline-brown">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
</div>

<!-- Enhanced JavaScript for Admin New Registration Form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeNewForm();
});

function initializeNewForm() {
    // Age calculation
    document.getElementById('dob').addEventListener('change', calculateAge);
    
    // ITS field for auto-population check
    document.getElementById('its').addEventListener('blur', function() {
        if (this.value.length === 8) {
            checkPreviousApplication();
        }
    });
    
    // Passport Arabic name update
    document.getElementById('fname').addEventListener('input', updatePassportArabicName);
    document.getElementById('mname').addEventListener('input', updatePassportArabicName);
    document.getElementById('lname').addEventListener('input', updatePassportArabicName);
    
    // Family name translations
    document.getElementById('father_name').addEventListener('input', () => translateField('father_name', 'father_name_arabic'));
    
    // Passport expiry validation
    document.getElementById('ppexpiry').addEventListener('change', validatePassportExpiry);
    document.getElementById('ppexpiry').addEventListener('blur', validatePassportExpiry);
    
    // Form progress tracking
    trackFormProgress();
    
    // Form validation
    document.getElementById('kun-registration').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
}

function calculateAge() {
    const dobField = document.getElementById('dob');
    const ageField = document.getElementById('age');
    
    if (dobField.value) {
        const dob = new Date(dobField.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        ageField.value = age;
        updateProgress();
    }
}

function updatePassportArabicName() {
    const fname = document.getElementById('fname').value.trim();
    const mname = document.getElementById('mname').value.trim();
    const lname = document.getElementById('lname').value.trim();
    
    let fullName = fname;
    if (mname) fullName += ' ' + mname;
    if (lname) fullName += ' ' + lname;
    
    if (fullName.trim()) {
        translateToArabic(fullName, 'passport_arabic_name');
    }
    updateProgress();
}

function translateField(sourceFieldId, targetFieldId) {
    const sourceField = document.getElementById(sourceFieldId);
    const targetField = document.getElementById(targetFieldId);
    
    if (sourceField && targetField && sourceField.value.trim()) {
        translateToArabic(sourceField.value.trim(), targetFieldId);
    }
}

function translateToArabic(text, targetFieldId) {
    if (!text) return;
    
    const targetField = document.getElementById(targetFieldId);
    if (!targetField) return;
    
    targetField.value = 'Translating...';
    targetField.style.fontStyle = 'italic';
    
    fetch('translate_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            text: text,
            source: 'en',
            target: 'ar'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.translatedText) {
            targetField.value = data.translatedText;
        } else {
            targetField.value = '';
        }
        targetField.style.fontStyle = 'normal';
    })
    .catch(error => {
        targetField.value = '';
        targetField.style.fontStyle = 'normal';
        console.error('Translation error:', error);
    });
}

function checkPreviousApplication() {
    const itsField = document.getElementById('its');
    if (!itsField || !itsField.value) return;
    
    fetch('check_previous_application.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            kun_its: itsField.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.hasData) {
            if (!data.passportValid) {
                alert('Previous application found, but ' + data.passportMessage);
                return;
            }
            
            if (confirm('Previous application found! Would you like to populate the form with previous data?')) {
                populateFormWithPreviousData(data.data);
                setTimeout(() => {
                    updatePassportArabicName();
                    translateField('father_name', 'father_name_arabic');
                }, 1000);
            }
        }
    })
    .catch(error => {
        console.error('Error checking previous application:', error);
    });
}

function populateFormWithPreviousData(data) {
    const fieldMappings = {
        'fname': data.kun_fname,
        'mname': data.kun_mname,
        'lname': data.kun_lname,
        'address': data.kun_address,
        'contact': data.kun_contact,
        'email': data.kun_email,
        'ppno': data.kun_ppno,
        'ppissue': data.kun_ppissue,
        'ppexpiry': data.kun_ppexpiry,
        'nationality': data.kun_nationality,
        'passport_type': data.kun_passport_type,
        'dob': data.kun_dob,
        'father_name': data.kun_father_name
    };
    
    Object.keys(fieldMappings).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && fieldMappings[fieldId]) {
            field.value = fieldMappings[fieldId];
        }
    });
    
    calculateAge();
    updateProgress();
}

function validatePassportExpiry() {
    const ppexpiryField = document.getElementById('ppexpiry');
    const errorDiv = document.getElementById('passportExpiryError');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!ppexpiryField.value) return;
    
    const expiryDate = new Date(ppexpiryField.value);
    const today = new Date();
    const sixMonthsFromNow = new Date();
    sixMonthsFromNow.setMonth(today.getMonth() + 6);
    
    if (expiryDate <= sixMonthsFromNow) {
        errorDiv.style.display = 'block';
        ppexpiryField.classList.add('is-invalid');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Cannot Submit - Invalid Passport';
    } else {
        errorDiv.style.display = 'none';
        ppexpiryField.classList.remove('is-invalid');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Create Registration';
    }
}

function trackFormProgress() {
    const requiredFields = document.querySelectorAll('[required]');
    const progressBar = document.getElementById('formProgress');
    
    const updateProgress = () => {
        let filledFields = 0;
        requiredFields.forEach(field => {
            if (field.type === 'checkbox') {
                if (field.checked) filledFields++;
            } else if (field.value.trim() !== '') {
                filledFields++;
            }
        });
        
        const progress = (filledFields / requiredFields.length) * 100;
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
    };
    
    requiredFields.forEach(field => {
        field.addEventListener('input', updateProgress);
        field.addEventListener('change', updateProgress);
    });
}

function updateProgress() {
    const requiredFields = document.querySelectorAll('[required]');
    const progressBar = document.getElementById('formProgress');
    
    let filledFields = 0;
    requiredFields.forEach(field => {
        if (field.type === 'checkbox') {
            if (field.checked) filledFields++;
        } else if (field.value.trim() !== '') {
            filledFields++;
        }
    });
    
    const progress = (filledFields / requiredFields.length) * 100;
    progressBar.style.width = progress + '%';
    progressBar.setAttribute('aria-valuenow', progress);
}
</script>

<!-- Enhanced CSS for Admin New Form (same as edit form) -->
<style>
.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

.section-header {
    border-bottom: 2px solid var(--brown-light);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
}

.section-header h5 {
    margin-bottom: 0;
    font-weight: 600;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--brown-primary);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
}

#submitBtn:disabled {
    background: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

.card {
    border: 1px solid var(--brown-light);
    border-radius: 12px;
}

.btn-outline-brown {
    color: var(--brown-primary);
    border-color: var(--brown-primary);
    background: transparent;
}

.btn-outline-brown:hover {
    color: white;
    background: var(--brown-primary);
    border-color: var(--brown-primary);
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    .section-header {
        margin-bottom: 1rem;
    }
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
}

input[readonly] {
    background-color: #f8f9fa !important;
    opacity: 0.8;
}
</style>