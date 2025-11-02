<?php
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name, safar_acceptnewreg, safar_status FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<!-- Modern breadcrumb navigation -->
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-light">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown"><i class="icon-home"></i> Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown">Active Safars</a></li>
    <li class="breadcrumb-item active" aria-current="page">KUN - <?php echo $safar_name; ?></li>
  </ol>
</nav>
<?php
if($safar_acceptnewreg == 1 && $safar_status == 'planning'){
    ?>
<div class="module">
    <div class="module-head">
        <h3><i class="fas fa-info-circle"></i> Please note</h3>
    </div>
    <div class="module-body">
        <p>The following list contains all members of your family under your Sabeel. If you are interested in Pre-registering for <?php echo $safar_name; ?>, kindly click on "Registration". Please note that raza will be granted on the following basis:</p>
        <ol>
            <li>Raza is dependant on the Quota provided from Sigatul Najaf and allocation from ITS.</li>
            <li>Your Anjuman Safai Clearance.</li>
        </ol>
    </div>
</div>
<?php
}
?>

<?php 
//let's put the logic here 
$family_list = [];
//get the sabeel
$sql_sabeel = "SELECT HOF_ID AS user_sabeel FROM census
            UNION ALL
            SELECT HOF_ID AS user_sabeel FROM foreign_census
            WHERE ITS_ID = $user_id AND sabeel = '30362306'";
//$sql_sabeel = "SELECT personalSabeel_ID AS user_sabeel FROM personalmaster WHERE eJamaat_ID = $user_id";
$result_sabeel = dbQuery2($sql_sabeel);
if(dbNumRows($result_sabeel) <= 0){
    ?>
<div class="module">
    <div class="module-head">
        <h3><i class="fas fa-exclamation-triangle"></i> Registration Issue</h3>
    </div>
    <div class="module-body">
    Your ITS does not seem to match any Sabeel in the system. This could be because your ITS is not registered with the Colombo Jamaat OR your information is missing for some reason. Kindly contact <a href="tel:0714515253">0714515253</a> to check further.
    </div>
</div>
<?php
} else {
extract(dbFetchAssoc($result_sabeel));
$sabeel = $user_sabeel;

$sql_names = "SELECT ITS_ID AS family_its
        FROM census
        WHERE HOF_ID = '$sabeel'";
$result_names = dbQuery2($sql_names);
while($row_name = dbFetchAssoc($result_names)){
    extract($row_name);
    array_push($family_list, $family_its);
}

$family_string = implode(",", $family_list);

?>
<div class="module">
    <div class="module-head">
        <h3><i class="fas fa-users"></i> Registrations</h3>
    </div>
    <div class="module-body">
        <!-- Desktop Table View -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped display"
                    width="100%" id="kunapplicants">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Profile</th>
                            <th style="width: 100px;">ITS</th>
                            <th style="width: 120px;">Passport</th>
                            <th style="width: 200px;">Name</th>
                            <th style="width: 150px;">Status</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
            <tbody>
            <?php
            if($safar_acceptnewreg == 1 && $safar_status == ('planning' || 'active')) {
            //we need to check all the family members
            $sql_directory = "SELECT ITS_ID AS eJamaat_ID, sabeel AS personalSabeel_ID, name, mobile AS personalMobileNo, email AS personalEmail from census WHERE HOF_ID = $sabeel";
				
				
				// SELECT p.eJamaat_ID, p.personalSabeel_ID, UCASE(CONCAT(IFNULL(first.firstTitleName, ''), ' ', name1,' ', IFNULL(second.firstTitleName, ''), ' ', IFNULL(name2, ''))) AS name, personalMobileNo, personalEmail
//                 FROM personalmaster p
//                 LEFT JOIN fisttitle first ON first.firstTitle_ID = p.firstTitle_ID
//                 LEFT JOIN fisttitle second ON second.firstTitle_ID = p.name2Title
//                 LEFT JOIN personal_contactdetails pc ON pc.eJamaat_ID = p.eJamaat_ID
//                 LEFT JOIN familyidentificationmaster f ON f.personalSabeel_ID = p.personalSabeel_ID
//                 WHERE p.personalSabeel_ID LIKE '$sabeel'";
            $result_directory = dbQuery2($sql_directory);
            if(dbNumRows($result_directory)>0){
                while($mumin = dbFetchAssoc($result_directory)){
                    extract($mumin);
                    //check if already registered
                    $sql_registered = "SELECT * FROM zyt_kun_registrations WHERE kun_its = $eJamaat_ID AND safar_id = $safarid";
                    $result_registered = dbQuery($sql_registered);
                    if(dbNumRows($result_registered) > 0){
                        extract(dbFetchAssoc($result_registered));
                        if($kun_status == 'cancelled'){
                        ?>
                        <tr class="">
                            <td class="safar-title">
                                <?php echo $kun_its; ?>
                            </td>
                            <td>
                                <?php echo $kun_ppno; ?>
                            </td>
                            <td>
                                <?php 
                                if($kun_fname != "") echo "$kun_fname $kun_mname $kun_lname";
                                else echo $kun_itsname;
								?>
								<br />
								<?php
								//get the list of photos
								$sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
								$result_photos = dbQuery($sql_photos);
								while($row_photos = dbFetchAssoc($result_photos)){
									extract($row_photos);
									?>
									<img src="<?php echo $server_link . "images/uploads/" . $photo_filename; ?>" width="50px">&nbsp;
									<?php
								}
                                ?>
                            </td>
                            <td>
                                <?php echo ucfirst($kun_status); ?><br />
                                <?php
                                $receipt_total = 0;
                                //check if there are any payments made
                                $sql_payments = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                                $result_payments = dbQuery($sql_payments);
                                if(dbNumRows($result_payments)>0){
                                    ?>
                                    <span class="text-warning">Payments</span>:&nbsp;
                                    <?php
                                    while($row_payments = dbFetchAssoc($result_payments)){
                                        extract($row_payments);
                                        echo "<span class='text-warning'>$receipt_no</span> | ";
                                        $receipt_total = $receipt_total + $db_amount;
                                    }
                                }
                                if($receipt_total > 0){
                                    echo "<br /><span class='text-warning'>Total Payments</span>: " . number_format($receipt_total, 2, ".", ",");
                                }
                                ?>
                            </td>
                            <td class="cell-mob-off"></td>
                        </tr>
                    <?php
                        } else {
                        ?>
                        <tr class="">
                            <!-- Profile Column -->
                            <td class="profile-cell">
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="profile-link">
                                    <img src="<?php require 'support_files/get_applicant_image.php'; ?>" alt="Profile" />
                                </a>
                            </td>
                            <!-- ITS Column -->
                            <td class="its-cell">
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="text-brown">
                                    <strong><?php echo $kun_its; ?></strong>
                                </a>
                            </td>
                            <!-- Passport Column -->
                            <td class="passport-cell">
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="text-brown">
                                    <?php echo $kun_ppno; ?>
                                </a>
                            </td>
                            <!-- Name Column -->
                            <td class="name-cell">
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="text-brown">
                                    <?php 
                                    if($kun_fname != "") echo ucwords(strtolower("$kun_fname $kun_mname $kun_lname"));
                                    else echo ucwords(strtolower($kun_itsname));
                                    ?>
                                </a>
                                <?php
                                // Show photos if available
                                $sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
                                $result_photos = dbQuery($sql_photos);
                                if(dbNumRows($result_photos) > 0){
                                    echo "<br><small class='text-muted'>Photos: ";
                                    $photo_count = 0;
                                    while($row_photos = dbFetchAssoc($result_photos)){
                                        $photo_count++;
                                    }
                                    echo "$photo_count uploaded</small>";
                                }
                                ?>
                            </td>
                            <!-- Status Column -->
                            <td class="status-cell">
                                <div class="status-info">
                                    <span class="badge bg-<?php echo $kun_status == 'active' ? 'success' : ($kun_status == 'pending' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst($kun_status); ?>
                                    </span>
                                    <?php if($kun_declaration == 1) { ?>
                                        <br><span class="badge bg-success mt-1">Ready</span>
                                    <?php } ?>
                                    
                                    <?php
                                    $receipt_total = 0;
                                    $sql_payments = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                                    $result_payments = dbQuery($sql_payments);
                                    if(dbNumRows($result_payments)>0){
                                        echo "<div class='payment-summary mt-2'>";
                                        echo "<strong class='text-warning'>Payments:</strong><br>";
                                        while($row_payments = dbFetchAssoc($result_payments)){
                                            extract($row_payments);
                                            echo "<span class='badge bg-warning text-dark me-1'>$receipt_no</span>";
                                            $receipt_total = $receipt_total + $db_amount;
                                        }
                                        if($receipt_total > 0){
                                            echo "<br><strong>Total: " . number_format($receipt_total, 2, ".", ",") . "</strong>";
                                        }
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </td>
                            <!-- Actions Column -->
                            <td class="actions-cell">
                                <?php if($safar_status == 'active'){ ?>
                                    <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="btn btn-success btn-sm mb-1 d-block">
                                        <i class="fas fa-edit"></i> Update Visa
                                    </a>
                                    <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning btn-sm d-block">
                                        <i class="fas fa-camera"></i> Update Photos
                                    </a>
                                <?php } else { ?>
                                    <span class="text-muted small">No actions available</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        }
                    } else {
                    //let's show the information with a registration button
                    ?>
                    <tr> <?php $kun_its = $eJamaat_ID; ?>
                        <td>
                            <a href="#" class="preregistrarion" data-bs-toggle="modal" data-bs-target="#preregister_<?php echo $kun_its; ?>">
                                <img src="<?php require 'support_files/get_applicant_image.php'; ?>" width="75" /><br />
                                <?php echo $kun_its; ?>
                            </a>
                        </td>
                        <td></td>
                        <td><?php echo ucwords(strtolower($name)); ?></td>
                        <td>
                            Unregistered
                            <br /><a href="#" class="btn btn-success preregistrarion mob-on" data-bs-toggle="modal" data-bs-target="#preregister_<?php echo $kun_its; ?>">Register</a>
                        </td>
                        <td class="cell-mob-off"><a href="#" class="btn btn-success preregistrarion" data-bs-toggle="modal" data-bs-target="#preregister_<?php echo $kun_its; ?>">Register</a></td>
                        <div class="modal fade" id="preregister_<?php echo $kun_its; ?>" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">

                              <!-- Modal Header -->
                              <div class="modal-header">
                                    <h4 class="modal-title">
                                        Pre Register <?php echo ucwords(strtolower($name)); ?>
                                    </h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                                <form action="<?php echo $server_link; ?>update-kun-preregistration.php" method="post">
                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <input type="hidden" name="safarid" id="safarid" value="<?php echo $safarid; ?>" />
                                        <input type="hidden" name="its" id="its" value="<?php echo $eJamaat_ID; ?>" />
                                        <input type="hidden" name="itsname" id="itsname" value="<?php echo ucwords(strtolower($name)); ?>" />
                                        <div class="mb-3">
                                            <label class="form-label fw-bold" for="contact">Contact Information</label>
                                            <input type="text" id="contact" name="contact" placeholder="Mobile Number" class="form-control mb-2" required value="<?php echo $personalMobileNo; ?>">
                                            <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" required value="<?php echo $personalEmail; ?>">
                                        </div>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                          </div>
                        </div>
                    </tr>
                    <?php
                        
                    }
                }
            }
        } elseif($safar_acceptnewreg == 0 && $safar_status == ('planning' || 'active')) {
            //we need to check all the family members
            $sql_directory = "SELECT ITS_ID AS eJamaat_ID, sabeel AS personalSabeel_ID, name, mobile AS personalMobileNo, email AS personalEmail from census WHERE HOF_ID = $sabeel";
            $result_directory = dbQuery2($sql_directory);
            if(dbNumRows($result_directory)>0){
                while($mumin = dbFetchAssoc($result_directory)){
                    extract($mumin);
                    //check if already registered
                    $sql_registered = "SELECT * FROM zyt_kun_registrations WHERE kun_its = $eJamaat_ID AND safar_id = $safarid";
                    $result_registered = dbQuery($sql_registered);
                    if(dbNumRows($result_registered) > 0){
                        extract(dbFetchAssoc($result_registered));
                        if($kun_status == 'cancelled'){
                        ?>
                        <tr class="">
                            <td class="safar-title">
                                <?php echo $kun_its; ?>
                            </td>
                            <td>
                                <?php echo $kun_ppno; ?>
                            </td>
                            <td>
                                <?php 
                                if($kun_fname != "") echo "$kun_fname $kun_mname $kun_lname";
                                else echo $kun_itsname;
                                ?>
                            </td>
                            <td>
                                <?php echo ucfirst($kun_status); ?><br />
                                <?php
                                $receipt_total = 0;
                                //check if there are any payments made
                                $sql_payments = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                                $result_payments = dbQuery($sql_payments);
                                if(dbNumRows($result_payments)>0){
                                    ?>
                                    <span class="text-warning">Payments</span>:&nbsp;
                                    <?php
                                    while($row_payments = dbFetchAssoc($result_payments)){
                                        extract($row_payments);
                                        echo "<span class='text-warning'>$receipt_no</span> | ";
                                        $receipt_total = $receipt_total + $db_amount;
                                    }
                                }
                                if($receipt_total > 0){
                                    echo "<br /><span class='text-warning'>Total Payments</span>: " . number_format($receipt_total, 2, ".", ",");
                                }
                                ?>
                            </td>
                            <td class="cell-mob-off"></td>
                        </tr>
                    <?php
                        } else {
                        ?>
                        <tr class="">
                            <td class="safar-title" align="center">
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>">
                                    <img src="<?php require 'support_files/get_applicant_image.php'; ?>" width="75" /><br />
                                    <?php echo $kun_its; ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>"><?php echo $kun_ppno; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>">
                                <?php 
                                if($kun_fname != "") echo ucwords(strtolower("$kun_fname $kun_mname $kun_lname"));
                                else echo ucwords(strtolower($kun_itsname));
                                ?>
                                </a>
                                <br />
								<?php
								//get the list of photos
								$sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
								$result_photos = dbQuery($sql_photos);
								while($row_photos = dbFetchAssoc($result_photos)){
									extract($row_photos);
									?>
									<img src="<?php echo $server_link . "images/uploads/" . $photo_filename; ?>" width="50px">&nbsp;
									<?php
								}
                                ?>
                            </td>
                            <td>
                                <?php echo ucfirst($kun_status); ?><br />
                                <?php if($kun_declaration == 1) echo "<br /><span class='text-success'>Ready for processing</span><br />"; ?>
                                <hr />
                                <!-- Payment information -->
                                <?php
                                $receipt_total = 0;
                                //check if there are any payments made
                                $sql_payments = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                                $result_payments = dbQuery($sql_payments);
                                if(dbNumRows($result_payments)>0){
                                    ?>
                                    <span class="text-warning">Payments</span>:&nbsp;
                                    <?php
                                    while($row_payments = dbFetchAssoc($result_payments)){
                                        extract($row_payments);
                                        echo "<span class='text-warning'>$receipt_no</span> | ";
                                        $receipt_total = $receipt_total + $db_amount;
                                    }
                                }
                                if($receipt_total > 0){
                                    echo "<br /><span class='text-warning'>Total Payments</span>: " . number_format($receipt_total, 2, ".", ",");
                                }
                                if($safar_status == 'active'){
                                    ?>
                                    <br /><a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="btn btn-success mob-on">Update Visa Form</a><br /><br />
								    <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning mob-on">Update Passport Photos</a>
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="cell-mob-off">
                                <?php
                                if($safar_status == 'active'){
                                    ?>
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="btn btn-success">Update Visa Form</a><br /><br />
								<a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning">Update Passport Photos</a>
                                <?php
                                }
                                ?>
                                <!--<a href="<?php echo $server_link; ?>print_kun_visa.php?kunid=<?php echo $kun_id; ?>">Print Visa</a><br />
                                <span class="text-warning action-addis">
                                    <?php if($kun_printcount != 0){ ?>
                                        Last Print: <br /><?php echo date_format(date_create($kun_lastprintdate),"d/m/Y H:iA"); ?>
                                    <?php } ?>
                                </span>-->
                            </td>
                        </tr>
                        <?php
                        }
                    }
                }
            }
        }
        ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Profile</th>
                    <th>ITS</th>
                    <th>Passport</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </tfoot>
        </table>
            </div> <!-- End table-responsive -->
        </div> <!-- End desktop view -->

        <!-- Mobile Card View -->
        <div class="d-lg-none">
            <div class="registration-cards">
                <?php
                // Re-execute the same logic for mobile cards
                if($safar_acceptnewreg == 1 && $safar_status == ('planning' || 'active')) {
                    $sql_directory = "SELECT ITS_ID AS eJamaat_ID, sabeel AS personalSabeel_ID, name, mobile AS personalMobileNo, email AS personalEmail from census WHERE HOF_ID = $sabeel";
                    $result_directory = dbQuery2($sql_directory);
                    if(dbNumRows($result_directory)>0){
                        while($mumin = dbFetchAssoc($result_directory)){
                            extract($mumin);
                            $sql_registered = "SELECT * FROM zyt_kun_registrations WHERE kun_its = $eJamaat_ID AND safar_id = $safarid";
                            $result_registered = dbQuery($sql_registered);
                            if(dbNumRows($result_registered) > 0){
                                extract(dbFetchAssoc($result_registered));
                                if($kun_status != 'cancelled'){
                ?>
                <!-- Registered User Card -->
                <div class="registration-card registered-card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <div class="profile-section">
                            <img src="<?php require 'support_files/get_applicant_image.php'; ?>" class="profile-img" alt="Profile">
                        </div>
                        <div class="basic-info ms-3 flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?php 
                                if($kun_fname != "") echo ucwords(strtolower("$kun_fname $kun_mname $kun_lname"));
                                else echo ucwords(strtolower($kun_itsname));
                            ?></h6>
                            <div class="text-muted small">
                                <span class="d-block">ITS: <strong><?php echo $kun_its; ?></strong></span>
                                <span class="d-block">PP: <strong><?php echo $kun_ppno; ?></strong></span>
                            </div>
                        </div>
                        <div class="status-badge">
                            <span class="badge bg-<?php echo $kun_status == 'active' ? 'success' : ($kun_status == 'pending' ? 'warning' : 'secondary'); ?>">
                                <?php echo ucfirst($kun_status); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php if($kun_declaration == 1) { ?>
                            <div class="col-12">
                                <div class="alert alert-success py-2 mb-2">
                                    <i class="fas fa-check-circle"></i> Ready for processing
                                </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Payment Information -->
                            <?php
                            $receipt_total = 0;
                            $sql_payments = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                            $result_payments = dbQuery($sql_payments);
                            if(dbNumRows($result_payments)>0){
                            ?>
                            <div class="col-12">
                                <div class="payment-info bg-light p-2 rounded">
                                    <strong class="text-warning">Payments:</strong>
                                    <div class="small">
                                        <?php
                                        while($row_payments = dbFetchAssoc($result_payments)){
                                            extract($row_payments);
                                            echo "<span class='badge bg-warning text-dark me-1'>$receipt_no</span>";
                                            $receipt_total = $receipt_total + $db_amount;
                                        }
                                        if($receipt_total > 0){
                                            echo "<br><strong>Total: " . number_format($receipt_total, 2, ".", ",") . "</strong>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Photos -->
                            <?php
                            $sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
                            $result_photos = dbQuery($sql_photos);
                            if(dbNumRows($result_photos) > 0){
                            ?>
                            <div class="col-12">
                                <div class="photos-section">
                                    <strong class="small text-muted">Photos:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <?php
                                        while($row_photos = dbFetchAssoc($result_photos)){
                                            extract($row_photos);
                                        ?>
                                        <img src="<?php echo $server_link . "images/uploads/" . $photo_filename; ?>" class="photo-thumb" alt="Photo">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons mt-3">
                            <?php if($safar_status == 'active'){ ?>
                            <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="btn btn-success btn-sm me-2 mb-1">
                                <i class="fas fa-edit"></i> Update Visa Form
                            </a>
                            <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning btn-sm mb-1">
                                <i class="fas fa-camera"></i> Update Photos
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
                                }
                            } else {
                                // Unregistered user card
                                $kun_its = $eJamaat_ID;
                ?>
                <!-- Unregistered User Card -->
                <div class="registration-card unregistered-card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <div class="profile-section">
                            <img src="<?php require 'support_files/get_applicant_image.php'; ?>" class="profile-img" alt="Profile">
                        </div>
                        <div class="basic-info ms-3 flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?php echo ucwords(strtolower($name)); ?></h6>
                            <div class="text-muted small">
                                <span class="d-block">ITS: <strong><?php echo $kun_its; ?></strong></span>
                                <span class="d-block text-warning">Not Registered</span>
                            </div>
                        </div>
                        <div class="status-badge">
                            <span class="badge bg-danger">Unregistered</span>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#mobile_preregister_<?php echo $kun_its; ?>">
                            <i class="fas fa-user-plus"></i> Register Now
                        </button>
                    </div>
                </div>
                
                <!-- Modal for unregistered user (Mobile) -->
                <div class="modal fade" id="mobile_preregister_<?php echo $kun_its; ?>" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Pre Register <?php echo ucwords(strtolower($name)); ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?php echo $server_link; ?>update-kun-preregistration.php" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="safarid" id="safarid" value="<?php echo $safarid; ?>" />
                                    <input type="hidden" name="its" id="its" value="<?php echo $eJamaat_ID; ?>" />
                                    <input type="hidden" name="itsname" id="itsname" value="<?php echo ucwords(strtolower($name)); ?>" />
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" for="contact">Contact Information</label>
                                        <input type="text" id="contact" name="contact" placeholder="Mobile Number" class="form-control mb-2" required value="<?php echo $personalMobileNo; ?>">
                                        <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" required value="<?php echo $personalEmail; ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                            }
                        }
                    }
                } elseif($safar_acceptnewreg == 0 && $safar_status == ('planning' || 'active')) {
                    // Similar logic for when new registration is not accepted
                    $sql_directory = "SELECT ITS_ID AS eJamaat_ID, sabeel AS personalSabeel_ID, name, mobile AS personalMobileNo, email AS personalEmail from census WHERE HOF_ID = $sabeel";
                    $result_directory = dbQuery2($sql_directory);
                    if(dbNumRows($result_directory)>0){
                        while($mumin = dbFetchAssoc($result_directory)){
                            extract($mumin);
                            $sql_registered = "SELECT * FROM zyt_kun_registrations WHERE kun_its = $eJamaat_ID AND safar_id = $safarid";
                            $result_registered = dbQuery($sql_registered);
                            if(dbNumRows($result_registered) > 0){
                                extract(dbFetchAssoc($result_registered));
                                if($kun_status != 'cancelled'){
                ?>
                <!-- Registered User Card (No new registration) -->
                <div class="registration-card registered-card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <div class="profile-section">
                            <img src="<?php require 'support_files/get_applicant_image.php'; ?>" class="profile-img" alt="Profile">
                        </div>
                        <div class="basic-info ms-3 flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?php 
                                if($kun_fname != "") echo ucwords(strtolower("$kun_fname $kun_mname $kun_lname"));
                                else echo ucwords(strtolower($kun_itsname));
                            ?></h6>
                            <div class="text-muted small">
                                <span class="d-block">ITS: <strong><?php echo $kun_its; ?></strong></span>
                                <span class="d-block">PP: <strong><?php echo $kun_ppno; ?></strong></span>
                            </div>
                        </div>
                        <div class="status-badge">
                            <span class="badge bg-<?php echo $kun_status == 'active' ? 'success' : ($kun_status == 'pending' ? 'warning' : 'secondary'); ?>">
                                <?php echo ucfirst($kun_status); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php if($kun_declaration == 1) { ?>
                            <div class="col-12">
                                <div class="alert alert-success py-2 mb-2">
                                    <i class="fas fa-check-circle"></i> Ready for processing
                                </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Payment Information -->
                            <?php
                            $receipt_total = 0;
                            $sql_payments = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                            $result_payments = dbQuery($sql_payments);
                            if(dbNumRows($result_payments)>0){
                            ?>
                            <div class="col-12">
                                <div class="payment-info bg-light p-2 rounded">
                                    <strong class="text-warning">Payments:</strong>
                                    <div class="small">
                                        <?php
                                        while($row_payments = dbFetchAssoc($result_payments)){
                                            extract($row_payments);
                                            echo "<span class='badge bg-warning text-dark me-1'>$receipt_no</span>";
                                            $receipt_total = $receipt_total + $db_amount;
                                        }
                                        if($receipt_total > 0){
                                            echo "<br><strong>Total: " . number_format($receipt_total, 2, ".", ",") . "</strong>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Photos -->
                            <?php
                            $sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
                            $result_photos = dbQuery($sql_photos);
                            if(dbNumRows($result_photos) > 0){
                            ?>
                            <div class="col-12">
                                <div class="photos-section">
                                    <strong class="small text-muted">Photos:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <?php
                                        while($row_photos = dbFetchAssoc($result_photos)){
                                            extract($row_photos);
                                        ?>
                                        <img src="<?php echo $server_link . "images/uploads/" . $photo_filename; ?>" class="photo-thumb" alt="Photo">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons mt-3">
                            <?php if($safar_status == 'active'){ ?>
                            <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="btn btn-success btn-sm me-2 mb-1">
                                <i class="fas fa-edit"></i> Update Visa Form
                            </a>
                            <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning btn-sm mb-1">
                                <i class="fas fa-camera"></i> Update Photos
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
                                }
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Bootstrap 5 Styling for kun-safar.php -->
<style>
/* Brown theme variables */
:root {
    --brown-primary: #8B4513;
    --brown-secondary: #A0522D;
    --brown-light: #D2B48C;
    --brown-dark: #654321;
    --brown-accent: #DEB887;
}

/* Modern breadcrumb styling */
.breadcrumb {
    background: linear-gradient(135deg, #faf9f7 0%, #f5f1eb 100%);
    border-radius: 8px;
    padding: 0.875rem 1.125rem;
    box-shadow: 0 2px 6px rgba(139, 69, 19, 0.08);
    border: 1px solid #e8e2db;
    margin-bottom: 1.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
}

.text-brown {
    color: var(--brown-primary) !important;
    text-decoration: none;
}

.text-brown:hover {
    color: var(--brown-dark) !important;
    text-decoration: none;
}

/* Module styling with proper brown theme */
.module {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(139, 69, 19, 0.1);
    overflow: hidden;
    border: 1px solid #e8e2db;
    margin-bottom: 1.5rem;
}

.module-head {
    background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
    color: white !important;
    padding: 1.25rem 1.75rem;
    margin: 0;
    border: none;
}

.module-head h3 {
    margin: 0;
    font-weight: 600;
    font-size: 1.25rem;
    color: white !important;
}

.module-head h3 i {
    margin-right: 0.5rem;
    opacity: 0.9;
}

.module-body {
    padding: 1.5rem;
    background: #ffffff;
}

/* Table responsive wrapper */
.table-responsive {
    border-radius: 8px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 2px 8px rgba(139, 69, 19, 0.08);
}

/* Enhanced table styling */
.table {
    margin-bottom: 0;
    font-size: 0.85rem;
    min-width: 1200px; /* Wider table for better content display */
    white-space: nowrap; /* Prevent text wrapping in cells */
}

.table th {
    background: linear-gradient(135deg, var(--brown-light) 0%, var(--brown-accent) 100%);
    color: var(--brown-dark);
    font-weight: 700;
    border: none;
    padding: 1rem 0.75rem;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    vertical-align: middle;
    padding: 0.875rem 0.75rem;
    border-color: #e8e2db;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #faf9f7;
}

.table-striped tbody tr:hover {
    background-color: #f5f1eb;
    transform: scale(1.005);
    transition: all 0.2s ease;
}

/* Profile images in table */
.table img {
    border-radius: 8px;
    border: 2px solid var(--brown-light);
    transition: all 0.3s ease;
}

.table img:hover {
    border-color: var(--brown-primary);
    transform: scale(1.1);
}

/* Links styling */
.table a {
    color: var(--brown-primary);
    text-decoration: none;
    font-weight: 500;
}

.table a:hover {
    color: var(--brown-dark);
    text-decoration: underline;
}

/* Button enhancements */
.btn-success {
    background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary));
    border-color: var(--brown-primary);
    color: white !important;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, var(--brown-dark), var(--brown-primary));
    border-color: var(--brown-dark);
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    border-color: #ffc107;
    color: #212529;
    font-weight: 600;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e0a800, #d39e00);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

/* Form compatibility for modals */
.control-group { 
    margin-bottom: 1rem; 
}

.control-label { 
    font-weight: 600; 
    color: var(--brown-dark); 
    margin-bottom: 0.5rem; 
    display: block; 
}

.controls { 
    /* Remove controls wrapper styling */ 
}

.span3 { 
    width: 100%; 
    max-width: 300px; 
    margin-bottom: 0.5rem; 
}

.span5 { 
    width: 100%; 
    max-width: 500px; 
    margin-bottom: 0.5rem; 
}

@media (min-width: 768px) {
    .span3 { 
        width: 31%; 
        display: inline-block; 
        margin-right: 2%; 
    }
    .span5 { 
        width: 48%; 
        display: inline-block; 
        margin-right: 2%; 
    }
}

/* Form elements styling */
.form-control, input[type="text"], input[type="email"], select, textarea {
    border: 2px solid #e8e2db;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.form-control:focus, input:focus, select:focus, textarea:focus {
    border-color: var(--brown-primary);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
    outline: none;
}

/* Status badges and text styling */
.text-warning {
    color: #e0a800 !important;
    font-weight: 600;
}

.text-success {
    color: #198754 !important;
    font-weight: 600;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .module-head {
        padding: 1rem 1.25rem;
    }
    
    .module-head h3 {
        font-size: 1.1rem;
    }
    
    .module-body {
        padding: 1rem;
    }
    
    .table {
        font-size: 0.8rem;
        min-width: 800px;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.375rem;
    }
    
    .breadcrumb {
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
    }
    
    .span3, .span5 {
        width: 100%;
        display: block;
        margin-right: 0;
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .table {
        min-width: 700px;
    }
    
    .module-head h3 {
        font-size: 1rem;
    }
}

/* Modal styling compatibility */
[data-toggle="modal"] { 
    cursor: pointer; 
}

[data-target] { 
    /* Bootstrap 2/4 to 5 modal target compatibility */ 
}

/* Modal enhancements */
.modal-header {
    background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%);
    color: white;
    border-radius: 8px 8px 0 0;
}

.modal-title {
    color: white;
    font-weight: 600;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 32px rgba(139, 69, 19, 0.2);
}

.modal-footer .btn {
    margin: 0 0.25rem;
}

/* Scroll indicators for table */
.table-responsive::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 20px;
    background: linear-gradient(90deg, transparent, rgba(139, 69, 19, 0.1));
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.table-responsive:hover::after {
    opacity: 1;
}

/* Loading states */
.table tbody tr.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Mobile card layout styling */
.registration-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(139, 69, 19, 0.1);
    border: 1px solid #e8e2db;
    overflow: hidden;
    transition: all 0.3s ease;
}

.registration-card:hover {
    box-shadow: 0 4px 16px rgba(139, 69, 19, 0.15);
    transform: translateY(-2px);
}

.registration-card .card-header {
    background: linear-gradient(135deg, #faf9f7 0%, #f5f1eb 100%);
    border-bottom: 1px solid #e8e2db;
    padding: 1rem;
}

.registration-card .card-body {
    padding: 1rem;
}

.profile-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid var(--brown-light);
    object-fit: cover;
}

.basic-info h6 {
    color: var(--brown-dark);
    margin-bottom: 0.25rem;
}

.status-badge .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.payment-info {
    border-left: 4px solid var(--brown-accent);
}

.photo-thumb {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    border: 2px solid var(--brown-light);
    object-fit: cover;
}

.action-buttons .btn {
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
}

.registered-card {
    border-left: 4px solid var(--brown-primary);
}

.unregistered-card {
    border-left: 4px solid #dc3545;
}

.unregistered-card .card-header {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
}

/* Desktop table cell styling for better content display */
.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
    border-color: #e8e2db;
    max-width: 200px;
    overflow: hidden;
}

.table td.profile-cell {
    text-align: center;
    width: 120px;
}

.table td.its-cell {
    width: 100px;
    text-align: center;
    font-weight: 600;
    color: var(--brown-dark);
}

.table td.passport-cell {
    width: 120px;
    text-align: center;
}

.table td.name-cell {
    width: 200px;
    font-weight: 600;
}

.table td.status-cell {
    width: 150px;
}

.table td.actions-cell {
    width: 180px;
    text-align: center;
}

.table .profile-link img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid var(--brown-light);
    margin-bottom: 0.25rem;
}

.table .status-info {
    font-size: 0.8rem;
    line-height: 1.3;
}

.table .payment-summary {
    font-size: 0.75rem;
    margin-top: 0.25rem;
    padding: 0.25rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.table .btn-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    margin: 0.125rem;
}

/* Enhanced table responsiveness */
@media (min-width: 992px) {
    .table {
        white-space: normal;
    }
    
    .table td {
        padding: 1rem 0.75rem;
    }
}

/* Print styles */
@media print {
    .breadcrumb,
    .btn,
    .modal,
    .d-lg-none {
        display: none;
    }
    
    .d-none.d-lg-block {
        display: block !important;
    }
    
    .module {
        box-shadow: none;
        border: 1px solid #000;
        break-inside: avoid;
    }
    
    .module-head {
        background: #f8f9fa !important;
        color: #000 !important;
    }
    
    .table {
        font-size: 10px;
    }
}
</style>

<script>
// Bootstrap 5 modal compatibility and debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, checking modals...');
    
    // Convert any remaining old Bootstrap syntax to Bootstrap 5
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"][data-target]');
    modalTriggers.forEach(function(trigger) {
        const target = trigger.getAttribute('data-target');
        trigger.setAttribute('data-bs-toggle', 'modal');
        trigger.setAttribute('data-bs-target', target);
        trigger.removeAttribute('data-toggle');
        trigger.removeAttribute('data-target');
        console.log('Converted modal trigger:', trigger);
    });
    
    // Convert data-dismiss to data-bs-dismiss for modal close buttons
    const dismissButtons = document.querySelectorAll('[data-dismiss="modal"]');
    dismissButtons.forEach(function(button) {
        button.setAttribute('data-bs-dismiss', 'modal');
        button.removeAttribute('data-dismiss');
        console.log('Converted dismiss button:', button);
    });
    
    // Check if Bootstrap 5 is loaded
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap 5 is loaded');
        
        // Test mobile modal functionality
        const mobileModalTriggers = document.querySelectorAll('.d-lg-none [data-bs-toggle="modal"]');
        console.log('Found mobile modal triggers:', mobileModalTriggers.length);
        
        mobileModalTriggers.forEach(function(trigger, index) {
            console.log(`Mobile trigger ${index}:`, trigger);
            const targetId = trigger.getAttribute('data-bs-target');
            const targetModal = document.querySelector(targetId);
            console.log(`Target modal ${index} (${targetId}):`, targetModal);
            
            if (!targetModal) {
                console.error(`Mobile modal ${targetId} not found!`);
            }
        });
        
        // Check for duplicate IDs
        const allModalIds = document.querySelectorAll('[id^="preregister_"]');
        console.log('All modal IDs found:', allModalIds.length);
        allModalIds.forEach(function(modal) {
            console.log('Modal ID:', modal.id);
        });
    } else {
        console.error('Bootstrap 5 not loaded!');
    }
    
    // Add click event listener for debugging
    document.addEventListener('click', function(e) {
        if (e.target.hasAttribute('data-bs-toggle') && e.target.getAttribute('data-bs-toggle') === 'modal') {
            console.log('Modal trigger clicked:', e.target);
            console.log('Target modal:', e.target.getAttribute('data-bs-target'));
        }
    });
});
</script>

<?php   
}
?>