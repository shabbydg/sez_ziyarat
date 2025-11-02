<?php
//NEW SAFAR SCRIPT
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';
$kunregid = (isset($_GET['kunregid']) && $_GET['kunregid'] != '') ? $_GET['kunregid'] : '0';

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
<!-- Success/Error Messages -->
<?php if(isset($_GET['success']) && $_GET['success'] == '1'): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Success!</strong> Registration has been updated successfully.
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
                <i class="fas fa-user-edit me-2"></i>Edit KUN Registration
            </h4>
            <small class="opacity-75">Safar: <?php echo $safar_name; ?></small>
        </div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="progress" style="height: 4px; border-radius: 0;">
        <div class="progress-bar bg-brown-light" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemax="100"></div>
    </div>
    
    <div class="card-body">
        <form action="<?php echo $server_link; ?>update-kun-registration.php" method="post" id="kun-registration" class="needs-validation" novalidate>
            <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
            <input type="hidden" name="kunregid" value="<?php echo $kunregid; ?>" />
            
            <!-- Section 1: Basic Information -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-user me-2"></i>Basic Information
                </h5>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="its" class="form-label required">ITS Number</label>
                    <input type="text" class="form-control" id="its" name="its" value="<?php echo $kun_its; ?>" required>
                    <div class="form-text">Eight digit number</div>
                    <div class="invalid-feedback">Please provide a valid ITS number.</div>
                </div>
                <div class="col-md-4">
                    <label for="dob" class="form-label required">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $kun_dob; ?>" required>
                    <div class="invalid-feedback">Please provide date of birth.</div>
                </div>
                <div class="col-md-4">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" class="form-control" id="age" name="age" value="<?php echo $kun_age; ?>" readonly>
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
                    <input type="text" class="form-control" id="fname" name="fname" value="<?php echo $kun_fname; ?>" required>
                    <div class="invalid-feedback">Please provide first name.</div>
                </div>
                <div class="col-md-4">
                    <label for="mname" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="mname" name="mname" value="<?php echo $kun_mname; ?>">
                </div>
                <div class="col-md-4">
                    <label for="lname" class="form-label required">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" value="<?php echo $kun_lname; ?>" required>
                    <div class="invalid-feedback">Please provide last name.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <label for="passport_arabic_name" class="form-label">Passport Arabic Name</label>
                    <input type="text" class="form-control" id="passport_arabic_name" name="passport_arabic_name" value="<?php echo $kun_passport_arabic_name; ?>" readonly>
                    <div class="form-text">Auto-translated from English names above</div>
                </div>
            </div>

            <!-- Section 3: Contact Information -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-address-book me-2"></i>Contact Information
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="address" class="form-label required">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required><?php echo $kun_address; ?></textarea>
                    <div class="invalid-feedback">Please provide address.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="contact" class="form-label required">Mobile Number</label>
                    <input type="tel" class="form-control" id="contact" name="contact" value="<?php echo $kun_contact; ?>" required>
                    <div class="invalid-feedback">Please provide mobile number.</div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $kun_email; ?>" required>
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
                    <input type="text" class="form-control" id="ppno" name="ppno" value="<?php echo $kun_ppno; ?>" required>
                    <div class="invalid-feedback">Please provide passport number.</div>
                </div>
                <div class="col-md-4">
                    <label for="passport_type" class="form-label required">Passport Type</label>
                    <select class="form-select" id="passport_type" name="passport_type" required>
                        <option value="">Select Type</option>
                        <option value="Normal" <?php echo ($kun_passport_type == 'Normal') ? 'selected' : ''; ?>>Normal</option>
                        <option value="Diplomatic" <?php echo ($kun_passport_type == 'Diplomatic') ? 'selected' : ''; ?>>Diplomatic</option>
                        <option value="Temporary" <?php echo ($kun_passport_type == 'Temporary') ? 'selected' : ''; ?>>Temporary</option>
                    </select>
                    <div class="invalid-feedback">Please select passport type.</div>
                </div>
                <div class="col-md-4">
                    <label for="nationality" class="form-label required">Nationality</label>
                    <select class="form-select" id="nationality" name="nationality" required>
                        <?php echo generateNationalityOptions($kun_nationality); ?>
                    </select>
                    <div class="invalid-feedback">Please select nationality.</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ppissue" class="form-label required">Issue Date</label>
                    <input type="date" class="form-control" id="ppissue" name="ppissue" value="<?php echo dateblanker($kun_ppissue); ?>" required>
                    <div class="invalid-feedback">Please provide issue date.</div>
                </div>
                <div class="col-md-6">
                    <label for="ppexpiry" class="form-label required">Expiry Date</label>
                    <input type="date" class="form-control" id="ppexpiry" name="ppexpiry" value="<?php echo dateblanker($kun_ppexpiry); ?>" required>
                    <div class="invalid-feedback">Please provide expiry date.</div>
                    <div id="passportExpiryError" class="text-danger mt-1" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>Passport must be valid for at least 6 months.
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="placeofissue" class="form-label required">Place of Issue</label>
                    <input type="text" class="form-control" id="placeofissue" name="placeofissue" value="<?php echo $kun_placeofissue; ?>" required>
                    <div class="invalid-feedback">Please provide place of issue.</div>
                </div>
                <div class="col-md-6">
                    <label for="countryofissue" class="form-label required">Country of Issue</label>
                    <input type="text" class="form-control" id="countryofissue" name="countryofissue" value="<?php echo $kun_countryofissue; ?>" required>
                    <div class="invalid-feedback">Please provide country of issue.</div>
                </div>
            </div>
            
            <!-- Section 5: Personal Details -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-info-circle me-2"></i>Personal Details
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label required">Gender</label>
                    <div class="form-check-container">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender_male" value="M" <?php if($kun_gender == 'M') echo "checked"; ?> required>
                            <label class="form-check-label" for="gender_male">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender_female" value="F" <?php if($kun_gender == 'F') echo "checked"; ?> required>
                            <label class="form-check-label" for="gender_female">Female</label>
                        </div>
                    </div>
                    <div class="invalid-feedback">Please select gender.</div>
                </div>
                <div class="col-md-4">
                    <label for="birthplace" class="form-label required">Birth Place</label>
                    <input type="text" class="form-control" id="birthplace" name="birthplace" value="<?php echo $kun_birthplace; ?>" required>
                    <div class="invalid-feedback">Please provide birth place.</div>
                </div>
                <div class="col-md-4">
                    <label for="birthcountry" class="form-label required">Birth Country</label>
                    <input type="text" class="form-control" id="birthcountry" name="birthcountry" value="<?php echo $kun_birthcountry; ?>" required>
                    <div class="invalid-feedback">Please provide birth country.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <label for="orignationality" class="form-label required">Original Nationality</label>
                    <input type="text" class="form-control" id="orignationality" name="orignationality" value="<?php echo $kun_orignationality; ?>" required>
                    <div class="invalid-feedback">Please provide original nationality.</div>
                </div>
            </div>
            
            <!-- Section 6: Family Names -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-users me-2"></i>Family Names
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="father_name" class="form-label required">Father Name</label>
                    <input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo $kun_father_name; ?>" required>
                    <div class="invalid-feedback">Please provide father's name.</div>
                </div>
                <div class="col-md-6">
                    <label for="father_name_arabic" class="form-label">Father Name (Arabic)</label>
                    <input type="text" class="form-control" id="father_name_arabic" name="father_name_arabic" value="<?php echo $kun_father_name_arabic; ?>" readonly>
                    <div class="form-text">Auto-translated from English name</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="grandfather_name" class="form-label">Grandfather Name</label>
                    <input type="text" class="form-control" id="grandfather_name" name="grandfather_name" value="<?php echo $kun_grandfather_name; ?>">
                </div>
                <div class="col-md-6">
                    <label for="grandfather_name_arabic" class="form-label">Grandfather Name (Arabic)</label>
                    <input type="text" class="form-control" id="grandfather_name_arabic" name="grandfather_name_arabic" value="<?php echo $kun_grandfather_name_arabic; ?>" readonly>
                    <div class="form-text">Auto-translated from English name</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="mother_name" class="form-label">Mother Name</label>
                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?php echo $kun_mother_name; ?>">
                </div>
                <div class="col-md-6">
                    <label for="mother_name_arabic" class="form-label">Mother Name (Arabic)</label>
                    <input type="text" class="form-control" id="mother_name_arabic" name="mother_name_arabic" value="<?php echo $kun_mother_name_arabic; ?>" readonly>
                    <div class="form-text">Auto-translated from English name</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="mother_father_name" class="form-label">Mother's Father Name</label>
                    <input type="text" class="form-control" id="mother_father_name" name="mother_father_name" value="<?php echo $kun_mother_father_name; ?>">
                </div>
                <div class="col-md-6">
                    <label for="mother_father_name_arabic" class="form-label">Mother's Father Name (Arabic)</label>
                    <input type="text" class="form-control" id="mother_father_name_arabic" name="mother_father_name_arabic" value="<?php echo $kun_mother_father_name_arabic; ?>" readonly>
                    <div class="form-text">Auto-translated from English name</div>
                </div>
            </div>
            
            <!-- Section 7: Professional Details -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-briefcase me-2"></i>Professional Details
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="certs" class="form-label required">Educational Level</label>
                    <select class="form-select" id="certs" name="certs" required>
                        <option value="">Select Educational Level</option>
                        <option value="STUDENT" <?php if($kun_certs == 'STUDENT') echo "selected"; ?>>Student</option>
                        <option value="O LEVELS" <?php if($kun_certs == 'O LEVELS') echo "selected"; ?>>O Levels</option>
                        <option value="A LEVELS" <?php if($kun_certs == 'A LEVELS') echo "selected"; ?>>A Levels</option>
                        <option value="BACHELORS DEGREE" <?php if($kun_certs == 'BACHELORS DEGREE') echo "selected"; ?>>Bachelors Degree</option>
                        <option value="MASTERS DEGREE" <?php if($kun_certs == 'MASTERS DEGREE') echo "selected"; ?>>Masters Degree</option>
                        <option value="PHD" <?php if($kun_certs == 'PHD') echo "selected"; ?>>PhD</option>
                    </select>
                    <div class="invalid-feedback">Please select educational level.</div>
                </div>
                <div class="col-md-6">
                    <label for="professional" class="form-label required">Professional</label>
                    <select class="form-select" id="professional" name="professional" required>
                        <option value="">Select Professional</option>
                        <option value="Engineer" <?php if($kun_professional == 'Engineer') echo "selected"; ?>>Engineer</option>
                        <option value="Physician" <?php if($kun_professional == 'Physician') echo "selected"; ?>>Physician</option>
                        <option value="Others" <?php if($kun_professional == 'Others') echo "selected"; ?>>Others</option>
                    </select>
                    <div class="invalid-feedback">Please select professional category.</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dateissuecerts" class="form-label">Date of Issuing Certificates</label>
                    <input type="date" class="form-control" id="dateissuecerts" name="dateissuecerts" value="<?php echo dateblanker($kun_dateissuecerts); ?>">
                </div>
                <div class="col-md-6">
                    <label for="issuecerts" class="form-label required">Place of Issuing Certificates</label>
                    <input type="text" class="form-control" id="issuecerts" name="issuecerts" value="<?php echo $kun_issuecerts; ?>" required>
                    <div class="invalid-feedback">Please provide place of issuing certificates.</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="profession" class="form-label required">Professional Title</label>
                    <input type="text" class="form-control" id="profession" name="profession" value="<?php echo $kun_profession; ?>" required>
                    <div class="invalid-feedback">Please provide professional title.</div>
                </div>
                <div class="col-md-6">
                    <label for="curroccupation" class="form-label required">Current Occupation</label>
                    <input type="text" class="form-control" id="curroccupation" name="curroccupation" value="<?php echo $kun_curroccupation; ?>" required>
                    <div class="invalid-feedback">Please provide current occupation.</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <label for="langspoken" class="form-label required">Languages Spoken</label>
                    <input type="text" class="form-control" id="langspoken" name="langspoken" value="<?php echo $kun_langspoken; ?>" required>
                    <div class="form-text">Separate multiple languages with commas</div>
                    <div class="invalid-feedback">Please provide languages spoken.</div>
                </div>
            </div>
            <!-- Section 8: Spouse Details -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-heart me-2"></i>Spouse Details <small class="text-muted">(Optional)</small>
                </h5>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="spname" class="form-label">Spouse Name</label>
                    <input type="text" class="form-control" id="spname" name="spname" value="<?php echo $kun_spname; ?>">
                </div>
                <div class="col-md-6">
                    <label for="spdob" class="form-label">Spouse Date of Birth</label>
                    <input type="date" class="form-control" id="spdob" name="spdob" value="<?php echo dateblanker($kun_spdob); ?>">
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="sppob" class="form-label">Spouse Place of Birth</label>
                    <input type="text" class="form-control" id="sppob" name="sppob" value="<?php echo $kun_sppob; ?>">
                </div>
                <div class="col-md-4">
                    <label for="spoccupation" class="form-label">Spouse Occupation</label>
                    <input type="text" class="form-control" id="spoccupation" name="spoccupation" value="<?php echo $kun_spoccupation; ?>">
                </div>
                <div class="col-md-4">
                    <label for="spnationality" class="form-label">Spouse Nationality</label>
                    <input type="text" class="form-control" id="spnationality" name="spnationality" value="<?php echo $kun_spnationality; ?>">
                </div>
            </div>
            
            <!-- Section 9: Iraq/Karbala Details -->
            <div class="section-header">
                <h5 class="text-brown mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>Iraq/Karbala Details
                </h5>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="numpreventry" class="form-label required">Number of Previous Entries</label>
                    <input type="number" class="form-control" id="numpreventry" name="numpreventry" value="<?php echo zeroblanker($kun_numpreventry); ?>" min="0" required>
                    <div class="invalid-feedback">Please provide number of previous entries.</div>
                </div>
                <div class="col-md-4">
                    <label for="datelentry" class="form-label">Date of Last Entry</label>
                    <input type="date" class="form-control" id="datelentry" name="datelentry" value="<?php echo dateblanker($kun_datelentry); ?>">
                </div>
                <div class="col-md-4">
                    <label for="accomppersons" class="form-label required">Accompanying Persons</label>
                    <input type="number" class="form-control" id="accomppersons" name="accomppersons" value="<?php echo zeroblanker($kun_accomppersons); ?>" min="0" required>
                    <div class="invalid-feedback">Please provide number of accompanying persons.</div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="child1name">Children's Details</label>
                <div class="controls">
                    <span class="help-inline">1.</span>
                    <input type="text" id="child1name" name="child1name" placeholder="Name" class="span4" value="<?php echo $kun_child1name; ?>">
                    <input type="text" id="child1dob" name="child1dob" placeholder="Date of birth" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_child1dob); ?>">
                    <input type="text" id="child1occ" name="child1occ" placeholder="Occupation" class="span4" value="<?php echo $kun_child1occ; ?>">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">2.</span>
                    <input type="text" id="child2name" name="child2name" placeholder="Name" class="span4" value="<?php echo $kun_child2name; ?>">
                    <input type="text" id="child2dob" name="child2dob" placeholder="Date of birth" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_child2dob); ?>">
                    <input type="text" id="child2occ" name="child2occ" placeholder="Occupation" class="span4" value="<?php echo $kun_child2occ; ?>">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">3.</span>
                    <input type="text" id="child3name" name="child3name" placeholder="Name" class="span4" value="<?php echo $kun_child3name; ?>">
                    <input type="text" id="child3dob" name="child3dob" placeholder="Date of birth" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_child3dob); ?>">
                    <input type="text" id="child3occ" name="child3occ" placeholder="Occupation" class="span4" value="<?php echo $kun_child3occ; ?>">
                    <div class="multi-input-space"></div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="visit1country">Last 3 Countries Visited</label>
                <div class="controls">
                    <span class="help-inline">1.</span>
                    <input type="text" id="visit1country" name="visit1country" placeholder="Name" class="span4" value="<?php echo $kun_visit1country; ?>">
                    <input type="text" id="visit1reason" name="visit1reason" placeholder="Reason" class="span4" value="<?php echo $kun_visit1reason; ?>">
                    <input type="text" id="visit1date" name="visit1date" placeholder="Date of travel" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_visit1date); ?>">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">2.</span>
                    <input type="text" id="visit2country" name="visit2country" placeholder="Name" class="span4" value="<?php echo $kun_visit2country; ?>">
                    <input type="text" id="visit2reason" name="visit2reason" placeholder="Reason" class="span4" value="<?php echo $kun_visit2reason; ?>">
                    <input type="text" id="visit2date" name="visit2date" placeholder="Date of travel" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_visit2date); ?>">
                    <div class="multi-input-space"></div>

                    <span class="help-inline">3.</span>
                    <input type="text" id="visit3country" name="visit3country" placeholder="Name" class="span4" value="<?php echo $kun_visit3country; ?>">
                    <input type="text" id="visit3reason" name="visit3reason" placeholder="Reason" class="span4" value="<?php echo $kun_visit3reason; ?>">
                    <input type="text" id="visit3date" name="visit3date" placeholder="Date of travel" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_visit3date); ?>">
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
                            <i class="fas fa-save me-2"></i>Update Registration
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
    <?php
    }
}
?>

<!-- Enhanced JavaScript for Admin Registration Form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the form
    initializeForm();
    
    // Check for previous application if ITS exists
    const itsField = document.getElementById('its');
    if (itsField && itsField.value) {
        checkPreviousApplication();
    }
});

// Initialize form functionality
function initializeForm() {
    // Age calculation
    document.getElementById('dob').addEventListener('change', calculateAge);
    
    // Passport Arabic name update
    document.getElementById('fname').addEventListener('input', updatePassportArabicName);
    document.getElementById('mname').addEventListener('input', updatePassportArabicName);
    document.getElementById('lname').addEventListener('input', updatePassportArabicName);
    
    // Family name translations
    document.getElementById('father_name').addEventListener('input', () => translateField('father_name', 'father_name_arabic'));
    document.getElementById('grandfather_name').addEventListener('input', () => translateField('grandfather_name', 'grandfather_name_arabic'));
    document.getElementById('mother_name').addEventListener('input', () => translateField('mother_name', 'mother_name_arabic'));
    document.getElementById('mother_father_name').addEventListener('input', () => translateField('mother_father_name', 'mother_father_name_arabic'));
    
    // Passport expiry validation
    document.getElementById('ppexpiry').addEventListener('change', validatePassportExpiry);
    document.getElementById('ppexpiry').addEventListener('blur', validatePassportExpiry);
    
    // Form validation
    document.getElementById('kun-registration').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
}

// Calculate age from date of birth
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
    }
}

// Update passport Arabic name from first, middle, last names
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
}

// Translate individual field
function translateField(sourceFieldId, targetFieldId) {
    const sourceField = document.getElementById(sourceFieldId);
    const targetField = document.getElementById(targetFieldId);
    
    if (sourceField && targetField && sourceField.value.trim()) {
        translateToArabic(sourceField.value.trim(), targetFieldId);
    }
}

// Google Translate API call
function translateToArabic(text, targetFieldId) {
    if (!text) return;
    
    const targetField = document.getElementById(targetFieldId);
    if (!targetField) return;
    
    // Show loading state
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
            targetField.style.fontStyle = 'normal';
        } else {
            targetField.value = '';
            targetField.style.fontStyle = 'normal';
            console.error('Translation failed:', data.error);
        }
    })
    .catch(error => {
        targetField.value = '';
        targetField.style.fontStyle = 'normal';
        console.error('Translation error:', error);
    });
}

// Check for previous application
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
            
            if (confirm('Previous application found. Would you like to populate the form with previous data?')) {
                populateFormWithPreviousData(data.data);
                translateAllArabicFields();
            }
        }
    })
    .catch(error => {
        console.error('Error checking previous application:', error);
    });
}

// Populate form with previous data
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
        'placeofissue': data.kun_placeofissue,
        'countryofissue': data.kun_countryofissue,
        'nationality': data.kun_nationality,
        'passport_type': data.kun_passport_type,
        'dob': data.kun_dob,
        'birthplace': data.kun_birthplace,
        'birthcountry': data.kun_birthcountry,
        'orignationality': data.kun_orignationality,
        'father_name': data.kun_father_name,
        'grandfather_name': data.kun_grandfather_name,
        'mother_name': data.kun_mother_name,
        'mother_father_name': data.kun_mother_father_name
    };
    
    // Populate form fields
    Object.keys(fieldMappings).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && fieldMappings[fieldId]) {
            field.value = fieldMappings[fieldId];
        }
    });
    
    // Handle radio buttons for gender
    if (data.kun_gender) {
        const genderRadio = document.querySelector(`input[name="gender"][value="${data.kun_gender}"]`);
        if (genderRadio) genderRadio.checked = true;
    }
    
    // Calculate age
    calculateAge();
}

// Translate all Arabic fields
function translateAllArabicFields() {
    updatePassportArabicName();
    
    setTimeout(() => {
        translateField('father_name', 'father_name_arabic');
        translateField('grandfather_name', 'grandfather_name_arabic');
        translateField('mother_name', 'mother_name_arabic');
        translateField('mother_father_name', 'mother_father_name_arabic');
    }, 1000);
}

// Validate passport expiry (6 months rule)
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
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Registration';
    }
}
</script>

<!-- Enhanced CSS for Admin Form -->
<style>
/* Required field indicator */
.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Section headers */
.section-header {
    border-bottom: 2px solid var(--brown-light);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
}

.section-header h5 {
    margin-bottom: 0;
    font-weight: 600;
}

/* Form controls focus state */
.form-control:focus,
.form-select:focus {
    border-color: var(--brown-primary);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
}

/* Submit button disabled state */
#submitBtn:disabled {
    background: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

/* Card styling improvements */
.card {
    border: 1px solid var(--brown-light);
    border-radius: 12px;
}

.card-header.bg-brown {
    background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
}

/* Progress bar styling */
.progress-bar.bg-brown-light {
    background: linear-gradient(90deg, var(--brown-light), var(--brown-accent)) !important;
}

/* Button outline brown */
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

/* Mobile responsiveness */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .section-header {
        margin-bottom: 1rem;
    }
    
    .section-header h5 {
        font-size: 1.1rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
}

/* Form validation styling */
.was-validated .form-control:valid {
    border-color: #198754;
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
}

.was-validated .form-select:valid {
    border-color: #198754;
}

.was-validated .form-select:invalid {
    border-color: #dc3545;
}

/* Loading state for translation */
input[readonly] {
    background-color: #f8f9fa !important;
    opacity: 0.8;
}
</style>
