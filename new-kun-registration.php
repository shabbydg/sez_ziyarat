<?php
//NEW SAFAR SCRIPT
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars" class="text-brown">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="text-brown">KUN - <?php echo $safar_name; ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">New Registration</li>
  </ol>
</nav>
<!-- Modern Form Container -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-brown text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1"><i class="fas fa-user-plus"></i> New Karbala Registration</h3>
                            <small class="opacity-75">Create new application</small>
                        </div>
                        <div class="text-right">
                            <small class="d-block">Safar: <?php echo $safar_name; ?></small>
                        </div>
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
                                    <div class="form-group mb-3">
                                        <label for="its" class="form-label font-weight-bold">ITS Number <span class="text-danger">*</span></label>
                                        <input type="text" id="its" name="its" class="form-control form-control-lg" required placeholder="Enter 8-digit ITS number">
                                        <small class="form-text text-muted">8-digit identification number</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-3">
                                        <label for="fname" class="form-label font-weight-bold">First Name (as per Passport) <span class="text-danger">*</span></label>
                                        <input type="text" id="fname" name="fname" class="form-control" required placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-3">
                                        <label for="mname" class="form-label font-weight-bold">Middle Name</label>
                                        <input type="text" id="mname" name="mname" class="form-control" placeholder="Middle Name (optional)">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-3">
                                        <label for="lname" class="form-label font-weight-bold">Last Name (as per Passport) <span class="text-danger">*</span></label>
                                        <input type="text" id="lname" name="lname" class="form-control" required placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label for="address" class="form-label font-weight-bold">Address</label>
                                        <textarea id="address" name="address" class="form-control" rows="3" placeholder="Enter complete address"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="contact" class="form-label font-weight-bold">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="tel" id="contact" name="contact" class="form-control" required placeholder="+94 77 123 4567">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" class="form-control" required placeholder="example@email.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Section 2: Passport Details -->
                        <div class="form-section mb-4" id="section-passport">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="fas fa-passport text-success"></i> Passport Details
                                </h4>
                                <p class="text-muted small mb-3">Passport information and travel document details</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ppno" class="form-label font-weight-bold">Passport Number <span class="text-danger">*</span></label>
                                        <input type="text" id="ppno" name="ppno" class="form-control" required placeholder="N1234567" style="text-transform: uppercase;">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-3">
                                        <label for="nationality" class="form-label font-weight-bold">Nationality <span class="text-danger">*</span></label>
                                        <select id="nationality" name="nationality" class="form-control" required>
                        <option value="">--Select Nationality --</option>
                        <option value='Afghan'>Afghan</option>
                        <option value='Albanian'>Albanian</option>
                        <option value='Algerian'>Algerian</option>
                        <option value='Argentinian'>Argentinian</option>
                        <option value='Australian'>Australian</option>
                        <option value='Austrian'>Austrian</option>
                        <option value='Bangladeshi'>Bangladeshi</option>
                        <option value='Belgian'>Belgian</option>
                        <option value='Bolivian'>Bolivian</option>
                        <option value='Batswana'>Batswana</option>
                        <option value='Brazilian'>Brazilian</option>
                        <option value='Bulgarian'>Bulgarian</option>
                        <option value='Cambodian'>Cambodian</option>
                        <option value='Cameroonian'>Cameroonian</option>
                        <option value='Canadian'>Canadian</option>
                        <option value='Chilean'>Chilean</option>
                        <option value='Chinese'>Chinese</option>
                        <option value='Colombian'>Colombian</option>
                        <option value='Costa Rican'>Costa Rican</option>
                        <option value='Croatian'>Croatian</option>
                        <option value='Cuban'>Cuban</option>
                        <option value='Czech'>Czech</option>
                        <option value='Danish'>Danish</option>
                        <option value='Dominican'>Dominican</option>
                        <option value='Ecuadorian'>Ecuadorian</option>
                        <option value='Egyptian'>Egyptian</option>
                        <option value='Salvadorian'>Salvadorian</option>
                        <option value='Estonian'>Estonian</option>
                        <option value='Ethiopian'>Ethiopian</option>
                        <option value='Fijian'>Fijian</option>
                        <option value='Finnish'>Finnish</option>
                        <option value='French'>French</option>
                        <option value='German'>German</option>
                        <option value='Ghanaian'>Ghanaian</option>
                        <option value='Greek'>Greek</option>
                        <option value='Guatemalan'>Guatemalan</option>
                        <option value='Haitian'>Haitian</option>
                        <option value='Honduran'>Honduran</option>
                        <option value='Hungarian'>Hungarian</option>
                        <option value='Icelandic'>Icelandic</option>
                        <option value='Indian'>Indian</option>
                        <option value='Indonesian'>Indonesian</option>
                        <option value='Iranian'>Iranian</option>
                        <option value='Iraqi'>Iraqi</option>
                        <option value='Irish'>Irish</option>
                        <option value='Israeli'>Israeli</option>
                        <option value='Italian'>Italian</option>
                        <option value='Jamaican'>Jamaican</option>
                        <option value='Japanese'>Japanese</option>
                        <option value='Jordanian'>Jordanian</option>
                        <option value='Kenyan'>Kenyan</option>
                        <option value='Kuwaiti'>Kuwaiti</option>
                        <option value='Lao'>Lao</option>
                        <option value='Latvian'>Latvian</option>
                        <option value='Lebanese'>Lebanese</option>
                        <option value='Libyan'>Libyan</option>
                        <option value='Lithuanian'>Lithuanian</option>
                        <option value='Malagasy'>Malagasy</option>
                        <option value='Malaysian'>Malaysian</option>
                        <option value='Malian'>Malian</option>
                        <option value='Maltese'>Maltese</option>
                        <option value='Mexican'>Mexican</option>
                        <option value='Mongolian'>Mongolian</option>
                        <option value='Moroccan'>Moroccan</option>
                        <option value='Mozambican'>Mozambican</option>
                        <option value='Namibian'>Namibian</option>
                        <option value='Nepalese'>Nepalese</option>
                        <option value='Dutch'>Dutch</option>
                        <option value='New Zealand'>New Zealand</option>
                        <option value='Nicaraguan'>Nicaraguan</option>
                        <option value='Nigerian'>Nigerian</option>
                        <option value='Norwegian'>Norwegian</option>
                        <option value='Pakistani'>Pakistani</option>
                        <option value='Panamanian'>Panamanian</option>
                        <option value='Paraguayan'>Paraguayan</option>
                        <option value='Peruvian'>Peruvian</option>
                        <option value='Philippine'>Philippine</option>
                        <option value='Polish'>Polish</option>
                        <option value='Portuguese'>Portuguese</option>
                        <option value='Romanian'>Romanian</option>
                        <option value='Russian'>Russian</option>
                        <option value='Saudi'>Saudi</option>
                        <option value='Scottish'>Scottish</option>
                        <option value='Senegalese'>Senegalese</option>
                        <option value='Serbian'>Serbian</option>
                        <option value='Singaporean'>Singaporean</option>
                        <option value='Slovak'>Slovak</option>
                        <option value='South African'>South African</option>
                        <option value='Korean'>Korean</option>
                        <option value='Spanish'>Spanish</option>
                        <option value='Sri Lankan'>Sri Lankan</option>
                        <option value='Sudanese'>Sudanese</option>
                        <option value='Swedish'>Swedish</option>
                        <option value='Swiss'>Swiss</option>
                        <option value='Syrian'>Syrian</option>
                        <option value='Taiwanese'>Taiwanese</option>
                        <option value='Tajikistani'>Tajikistani</option>
                        <option value='Thai'>Thai</option>
                        <option value='Tongan'>Tongan</option>
                        <option value='Tunisian'>Tunisian</option>
                        <option value='Turkish'>Turkish</option>
                        <option value='Ukrainian'>Ukrainian</option>
                        <option value='Emirati'>Emirati</option>
                        <option value='British'>British</option>
                        <option value='American'>American</option>
                        <option value='Uruguayan'>Uruguayan</option>
                        <option value='Venezuelan'>Venezuelan</option>
                        <option value='Vietnamese'>Vietnamese</option>
                        <option value='Welsh'>Welsh</option>
                        <option value='Zambian'>Zambian</option>
                        <option value='Zimbabwean'>Zimbabwean</option>
                    </select>
                    
                    <!-- <input type="text" id="nationality" name="nationality" placeholder="Current Nationality" class="span3" required> -->
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dob">Birth Details</label>
                <div class="controls">
                    <input type="text" id="dob" name="dob" placeholder="Date of Birth" class="span3 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                    <div class="multi-input-space"></div>
                    <span class="help-inline">Gender</span>
                    <label class="radio inline">
                        <input type="radio" name="gender" id="gender" value="M" required>
                        Male
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="gender" id="gender" value="F" required>
                        Female
                    </label>
                    <div class="multi-input-space"></div>
                    <input type="text" id="birthplace" name="birthplace" placeholder="Birth Place" class="span3" required>
                    <input type="text" id="birthcountry" name="birthcountry" placeholder="Birth Country" class="span3" required>
                    <input type="text" id="orignationality" name="orignationality" placeholder="Original Nationality" class="span3" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="certs">Professional Details</label>
                <div class="controls">
                    <select id="certs" name="certs" data-placeholder="Select Educational Level.." class="span4" required>
                        <option value="">Select Educational Level..</option>
                        <option value="O Levels">O Levels</option>
                        <option value="A Levels">A Levels</option>
                        <option value="Bachelors Degree">Bachelors Degree</option>
                        <option value="Masters Degree">Masters Degree</option>
                        <option value="PhD">PhD</option>
                    </select>
                    <div class="multi-input-space"></div>
                    <input type="text" id="dateissuecerts" name="dateissuecerts" placeholder="Date of Issuing Certificates" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                    <span class="help-inline">Date does not need to be accurate</span>
                    <div class="multi-input-space"></div>
                    <input type="text" id="issuecerts" name="issuecerts" placeholder="Place of Issuing Certificates" class="span5" required>
                    <span class="help-inline">Eg: St Peter's College. Please do not enter simply the town or city.</span>
                    <div class="multi-input-space"></div>
                    <input type="text" id="profession" name="profession" placeholder="Professional Title" class="span4" required>
                    <input type="text" id="curroccupation" name="curroccupation" placeholder="Current Occupation" class="span4" required>
                    <div class="multi-input-space"></div>
                    <input type="text" id="langspoken" name="langspoken" placeholder="Languages Spoken" class="span6" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="spname">Spouse Details</label>
                <div class="controls">
                    <input type="number" id="spname" name="spname" placeholder="Spouse Name" class="span5">
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
                    <input type="number" id="numpreventry" name="numpreventry" placeholder="Number of Previous Entries" class="span4" required>
                    <input type="text" id="datelentry" name="datelentry" placeholder="Date of Last Entry" class="span4 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <div class="multi-input-space"></div>
                    <input type="number" id="accomppersons" name="accomppersons" placeholder="Accompanying Persons" class="span4" required>
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


                        <!-- Form Actions -->
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
        </div>
    </div>
</div>

<style>
/* Bootstrap 2 to 5 Compatibility CSS for Quick Fix */
.control-group {
    margin-bottom: 1rem;
}

.control-label {
    font-weight: 600;
    color: var(--brown-dark, #654321);
    margin-bottom: 0.5rem;
    display: block;
}

.controls {
    /* Remove controls wrapper styling */
}

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

.multi-input-space {
    margin: 0.5rem 0;
    clear: both;
}

/* Apply brown theme to form controls */
.form-control, input[type="text"], input[type="email"], input[type="number"], select, textarea {
    border: 2px solid #e8e2db;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, input:focus, select:focus, textarea:focus {
    border-color: var(--brown-primary, #8B4513);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
    outline: none;
}

/* Brown theme variables for compatibility */
:root {
    --brown-primary: #8B4513;
    --brown-secondary: #A0522D;
    --brown-light: #D2B48C;
    --brown-dark: #654321;
    --brown-accent: #DEB887;
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

/* Fix alert styling */
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
}

/* Card styling for form container */
.module {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(139, 69, 19, 0.1);
    overflow: hidden;
    border: 1px solid #e8e2db;
}

.module-head {
    background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%);
    color: white;
    padding: 1.25rem 1.75rem;
    margin: 0;
}

.module-head h3 {
    margin: 0;
    font-weight: 600;
    font-size: 1.25rem;
}

.module-body {
    padding: 1.5rem;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .span3, .span4, .span5, .span6, .span8 {
        width: 100%;
        display: block;
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .module-head {
        padding: 1rem 1.25rem;
    }
    
    .module-body {
        padding: 1rem;
    }
}
</style>
<div class="align-right">
    <a href="<?php echo $server_link; ?>" class="btn btn-info">Back to Dashboard</a>
</div>