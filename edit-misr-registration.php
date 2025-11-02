<?php
//NEW SAFAR SCRIPT
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';
$kunregid = (isset($_GET['kunregid']) && $_GET['kunregid'] != '') ? $_GET['kunregid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>">KUN - <?php echo $safar_name; ?></a></li>
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
<div class="module">
    <div class="module-head">
        <h3>Edit Registration</h3>
    </div>
    <div class="module-body">
        <form action="<?php echo $server_link; ?>update-kun-registration.php" method="post" id="kun-registration" novalidate>
            <?php
            if(isset($_GET['e'])){
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error!</strong> Something went wrong. Please check the date format (yyyy-mm-dd) and try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            }
            ?>
            <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
            <input type="hidden" name="kunregid" value="<?php echo $kunregid; ?>" />
            <div class="control-group">
                <label class="control-label" for="its">ITS Number</label>
                <div class="controls">
                    <input type="text" id="its" name="its" placeholder="" class="span3" value="<?php echo $kun_its; ?>" required>
                    <span class="help-inline">Eight digit number <a href="#" data-toggle="tooltip" data-placement="top" title="Hooray!">?</a></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="fname">Name (As per PP)</label>
                <div class="controls">
                    <input type="text" id="fname" name="fname" placeholder="First Name" class="span3" required value="<?php echo $kun_fname; ?>">
                    <input type="text" id="mname" name="mname" placeholder="Middle Name" class="span3" value="<?php echo $kun_mname; ?>">
                    <input type="text" id="lname" name="lname" placeholder="Last Name" class="span3" required value="<?php echo $kun_lname; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="fname">Contact Information</label>
                <div class="controls">
                    <input type="text" id="address" name="address" placeholder="Address" class="span8" value="<?php echo $kun_address; ?>">
                    <div class="multi-input-space"></div>
                    <input type="text" id="contact" name="contact" placeholder="Mobile Number" class="span3" required value="<?php echo $kun_contact; ?>">
                    <input type="email" id="email" name="email" placeholder="Email Address" class="span3" required value="<?php echo $kun_email; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ppno">Passport Details</label>
                <div class="controls">
                    <input type="text" id="ppno" name="ppno" placeholder="PP No" class="span3" required value="<?php echo $kun_ppno; ?>">
                    <div class="multi-input-space"></div>
                    <input type="text" id="ppissue" name="ppissue" placeholder="Issue Date" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" required value="<?php echo $kun_ppissue; ?>">
                    <input type="text" id="ppexpiry" name="ppexpiry" placeholder="Expiry Date" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" required value="<?php echo $kun_ppexpiry; ?>">
                    <input type="text" id="placeofissue" name="placeofissue" placeholder="Place of Issue" class="span3" required value="<?php echo $kun_placeofissue; ?>">
                    <input type="text" id="countryofissue" name="countryofissue" placeholder="Country of Issue" class="span3" required value="<?php echo $kun_countryofissue; ?>">
                    <div class="multi-input-space"></div>
                    <input type="text" id="nationality" name="nationality" placeholder="Current Nationality" class="span3" required value="<?php echo $kun_nationality; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dob">Birth Details</label>
                <div class="controls">
                    <input type="text" id="dob" name="dob" placeholder="Date of Birth" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" required value="<?php echo $kun_dob; ?>">
                    <div class="multi-input-space"></div>
                    <span class="help-inline">Gender</span>
                    <label class="radio inline">
                        <input type="radio" name="gender" id="gender" value="M" required <?php if($kun_gender == 'M') echo "checked"; ?>>
                        Male
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="gender" id="gender" value="F" required <?php if($kun_gender == 'F') echo "checked"; ?>>
                        Female
                    </label>
                    <div class="multi-input-space"></div>
                    <input type="text" id="birthplace" name="birthplace" placeholder="Birth Place" class="span3" required value="<?php echo $kun_birthplace; ?>">
                    <input type="text" id="birthcountry" name="birthcountry" placeholder="Birth Country" class="span3" required value="<?php echo $kun_birthcountry; ?>">
                    <input type="text" id="orignationality" name="orignationality" placeholder="Original Nationality" class="span3" required value="<?php echo $kun_orignationality; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="certs">Professional Details</label>
                <div class="controls">
                    <select id="certs" name="certs" data-placeholder="Select Educational Level.." class="span4" required>
                        <option value="">Select Educational Level..</option>
                        <option value="STUDENT" <?php if($kun_certs == 'STUDENT') echo "selected"; ?>>Student</option>
                        <option value="O LEVELS" <?php if($kun_certs == 'O LEVELS') echo "selected"; ?>>O Levels</option>
                        <option value="A LEVELS" <?php if($kun_certs == 'A LEVELS') echo "selected"; ?>>A Levels</option>
                        <option value="BACHELORS DEGREE" <?php if($kun_certs == 'BACHELORS DEGREE') echo "selected"; ?>>Bachelors Degree</option>
                        <option value="MASTERS DEGREE" <?php if($kun_certs == 'MASTERS DEGREE') echo "selected"; ?>>Masters Degree</option>
                        <option value="PHD" <?php if($kun_certs == 'PHD') echo "selected"; ?>>PhD</option>
                    </select>
                    <div class="multi-input-space"></div>
                    <input type="text" id="dateissuecerts" name="dateissuecerts" placeholder="Date of Issuing Certificates" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_dateissuecerts); ?>">
                    <span class="help-inline">Date does not need to be accurate. Mandatory to fill if you have completed your education.</span>
                    <div class="multi-input-space"></div>
                    <input type="text" id="issuecerts" name="issuecerts" placeholder="Place of Issuing Certificates" class="span5" required value="<?php echo $kun_issuecerts; ?>">
                    <span class="help-inline">Eg: St Peter's College. Please do not enter simply the town or city. (If you are still studying, please enter the current school name)</span>
                    <div class="multi-input-space"></div>
                    <input type="text" id="profession" name="profession" placeholder="Professional Title" class="span4" required value="<?php echo $kun_profession; ?>">
                    <input type="text" id="curroccupation" name="curroccupation" placeholder="Current Occupation" class="span4" required value="<?php echo $kun_curroccupation; ?>">
                    <span class="help-inline">Please enter STUDENT if applicant is still schooling.</span>
                    <div class="multi-input-space"></div>
                    <input type="text" id="langspoken" name="langspoken" placeholder="Languages Spoken" class="span6" required value="<?php echo $kun_langspoken; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="spname">Spouse Details</label>
                <div class="controls">
                    <input type="text" id="spname" name="spname" placeholder="Spouse Name" class="span5" value="<?php echo $kun_spname; ?>">
                    <div class="multi-input-space"></div>
                    <input type="text" id="spdob" name="spdob" placeholder="Spouse Date of Birth" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_spdob); ?>">
                    <input type="text" id="sppob" name="sppob" placeholder="Spouse Place of Birth" class="span3" value="<?php echo $kun_sppob; ?>">
                    <div class="multi-input-space"></div>
                    <input type="text" id="spoccupation" name="spoccupation" placeholder="Spouse Occupation" class="span3" value="<?php echo $kun_spoccupation; ?>">
                    <input type="text" id="spnationality" name="spnationality" placeholder="Spouse Nationality" class="span3" value="<?php echo $kun_spnationality; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="numpreventry">Iraq/Karbala Details</label>
                <div class="controls">
                    <input type="number" id="numpreventry" name="numpreventry" placeholder="Number of Previous Entries" class="span4" required value="<?php echo zeroblanker($kun_numpreventry); ?>">
                    <input type="text" id="datelentry" name="datelentry" placeholder="Date of Last Entry" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" value="<?php echo dateblanker($kun_datelentry); ?>">
                    <div class="multi-input-space"></div>
                    <input type="number" id="accomppersons" name="accomppersons" placeholder="Accompanying Persons" class="span4" required value="<?php echo zeroblanker($kun_accomppersons); ?>">
                    <span class="help-inline">The number of people travelling with you in your family.</span>
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
            <div class="control-group">
                <label class="control-label">Disclaimer</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" name="declaration" id="declaration" value="1" required>
                        I hereby declare that the details furnished above are true and correct to the best of my knowledge and I will undertake to inform Shababul Eidiz Zahabi, Colombo of any changes therein, immediately. I also authorize Shababul Eidiz Zahabi, Colombo to proceed with my application visa process and will not hold them responsible for any complications due to misleading or misrepresented information provided on this form.
                    </label>
                </div>
            </div>


            <div class="text-center mt-4">
                <button type="submit" class="btn btn-brown btn-lg px-5 me-3">
                    <i class="fas fa-check"></i> Submit Application
                </button>
                <button type="reset" class="btn btn-outline-secondary btn-lg px-4 me-3">
                    <i class="fas fa-undo"></i> Reset Form
                </button>
                <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-outline-danger btn-lg px-4">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap 2 to 5 Compatibility CSS -->
<style>
.control-group { margin-bottom: 1rem; }
.control-label { font-weight: 600; color: var(--brown-dark, #654321); margin-bottom: 0.5rem; display: block; }
.controls { /* Remove controls wrapper styling */ }
.span3 { width: 100%; max-width: 300px; margin-bottom: 0.5rem; }
.span4 { width: 100%; max-width: 400px; margin-bottom: 0.5rem; }
.span5 { width: 100%; max-width: 500px; margin-bottom: 0.5rem; }
.span6 { width: 100%; max-width: 600px; margin-bottom: 0.5rem; }
.span8 { width: 100%; margin-bottom: 0.5rem; }
@media (min-width: 768px) {
    .span3 { width: 23%; display: inline-block; margin-right: 2%; }
    .span4 { width: 31%; display: inline-block; margin-right: 2%; }
    .span5 { width: 39%; display: inline-block; margin-right: 2%; }
    .span6 { width: 48%; display: inline-block; margin-right: 2%; }
    .span8 { width: 100%; }
}
.multi-input-space { margin: 0.5rem 0; clear: both; }
.form-control, input[type="text"], input[type="email"], input[type="number"], select, textarea {
    border: 2px solid #e8e2db; border-radius: 6px; padding: 0.75rem 1rem; transition: all 0.3s ease;
}
.form-control:focus, input:focus, select:focus, textarea:focus {
    border-color: var(--brown-primary, #8B4513); box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15); outline: none;
}
:root { --brown-primary: #8B4513; --brown-secondary: #A0522D; --brown-light: #D2B48C; --brown-dark: #654321; --brown-accent: #DEB887; }
.btn-brown { background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary)); border-color: var(--brown-primary); color: white; font-weight: 600; }
.btn-brown:hover { background: linear-gradient(135deg, var(--brown-dark), var(--brown-primary)); border-color: var(--brown-dark); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3); }
.module { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(139, 69, 19, 0.1); overflow: hidden; border: 1px solid #e8e2db; }
.module-head { background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%); color: white; padding: 1.25rem 1.75rem; margin: 0; }
.module-head h3 { margin: 0; font-weight: 600; font-size: 1.25rem; }
.module-body { padding: 1.5rem; }
@media (max-width: 768px) {
    .span3, .span4, .span5, .span6, .span8 { width: 100%; display: block; margin-right: 0; margin-bottom: 1rem; }
    .module-head { padding: 1rem 1.25rem; } .module-body { padding: 1rem; }
}
</style>

<div class="text-center mt-4">
    <a href="<?php echo $server_link; ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
    <?php
    }
}
?>
