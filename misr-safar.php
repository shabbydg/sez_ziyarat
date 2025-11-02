<?php
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name, safar_acceptnewreg, safar_status FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars">All Safars</a></li>
    <li class="breadcrumb-item active" aria-current="page">KUN - <?php echo $safar_name; ?></li>
  </ol>
</nav>
<?php
if($safar_acceptnewreg == 1 && $safar_status == 'planning'){
    ?>
<div class="module">
    <div class  ="module-head">
        <h3>Please note</h3>
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
$sql_sabeel = "SELECT HOF_ID AS user_sabeel FROM census where ITS_ID = $user_id";
//$sql_sabeel = "SELECT personalSabeel_ID AS user_sabeel FROM personalmaster WHERE eJamaat_ID = $user_id";
$result_sabeel = dbQuery2($sql_sabeel);
if(dbNumRows($result_sabeel) <= 0){
    ?>
<div class="module">
    <div class  ="module-head">
        <h3>Registrations</h3>
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
    <div class  ="module-head">
        <h3>Registrations</h3>
    </div>
    <div class="module-body table">

        <table cellpadding="0" cellspacing="0" border="0" class=" table table-bordered table-striped display"
            width="100%" id="kunapplicants">
            <thead>
                <tr>
                    <th>
                        ITS
                    </th>
                    <th>
                        PP
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Reg Status
                    </th>
                    <th class="cell-mob-off">
                        Action
                    </th>
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
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="btn btn-success">Update Visa Form</a><br /><br /><br />

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
                    } else {
                    //let's show the information with a registration button
                    ?>
                    <tr> <?php $kun_its = $eJamaat_ID; ?>
                        <td>
                            <a href="#" class="preregistrarion" data-toggle="modal" data-target="#preregister_<?php echo $kun_its; ?>">
                                <img src="<?php require 'support_files/get_applicant_image.php'; ?>" width="75" /><br />
                                <?php echo $kun_its; ?>
                            </a>
                        </td>
                        <td></td>
                        <td><?php echo ucwords(strtolower($name)); ?></td>
                        <td>
                            Unregistered
                            <br /><a href="#" class="btn btn-success preregistrarion mob-on" data-toggle="modal" data-target="#preregister_<?php echo $kun_its; ?>">Register</a>
                        </td>
                        <td class="cell-mob-off"><a href="#" class="btn btn-success preregistrarion" data-toggle="modal" data-target="#preregister_<?php echo $kun_its; ?>">Register</a></td>
                        <div class="modal fade" id="preregister_<?php echo $kun_its; ?>" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">

                              <!-- Modal Header -->
                              <div class="modal-header">
                                    <h4 class="modal-title">
                                        Pre Register <?php echo ucwords(strtolower($name)); ?>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </h4>
                              </div>
                                <form action="<?php echo $server_link; ?>update-kun-preregistration.php" method="post">
                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <input type="hidden" name="safarid" id="safarid" value="<?php echo $safarid; ?>" />
                                        <input type="hidden" name="its" id="its" value="<?php echo $eJamaat_ID; ?>" />
                                        <input type="hidden" name="itsname" id="itsname" value="<?php echo ucwords(strtolower($name)); ?>" />
                                        <div class="control-group">
                                            <label class="control-label" for="contact">Contact Information</label>
                                            <div class="controls">
                                                <input type="text" id="contact" name="contact" placeholder="Mobile Number" class="span3" required value="<?php echo $personalMobileNo; ?>">
                                                <input type="email" id="email" name="email" placeholder="Email Address" class="span5" required value="<?php echo $personalEmail; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
                    <th>
                        ITS
                    </th>
                    <th>
                        PP
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Reg Status
                    </th>
                    <th class="cell-mob-off">
                        Action
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Bootstrap 2 to 5 Compatibility CSS for Modals and Forms -->
<style>
/* Modal compatibility for Bootstrap 5 */
[data-toggle="modal"] { cursor: pointer; }
[data-target] { /* Bootstrap 2/4 to 5 modal target compatibility */ }

/* Form compatibility */
.control-group { margin-bottom: 1rem; }
.control-label { font-weight: 600; color: var(--brown-dark, #654321); margin-bottom: 0.5rem; display: block; }
.controls { /* Remove controls wrapper styling */ }
.span3 { width: 100%; max-width: 300px; margin-bottom: 0.5rem; }
.span5 { width: 100%; max-width: 500px; margin-bottom: 0.5rem; }

@media (min-width: 768px) {
    .span3 { width: 31%; display: inline-block; margin-right: 2%; }
    .span5 { width: 48%; display: inline-block; margin-right: 2%; }
}

/* Brown theme for form elements */
.form-control, input[type="text"], input[type="email"], select, textarea {
    border: 2px solid #e8e2db; border-radius: 6px; padding: 0.75rem 1rem; transition: all 0.3s ease;
}

.form-control:focus, input:focus, select:focus, textarea:focus {
    border-color: var(--brown-primary, #8B4513); box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15); outline: none;
}

/* Brown theme variables */
:root { --brown-primary: #8B4513; --brown-secondary: #A0522D; --brown-light: #D2B48C; --brown-dark: #654321; --brown-accent: #DEB887; }

/* Brown theme buttons */
.btn-success { background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary)); border-color: var(--brown-primary); color: white; }
.btn-success:hover { background: linear-gradient(135deg, var(--brown-dark), var(--brown-primary)); border-color: var(--brown-dark); color: white; }

/* Table and general styling */
.module { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(139, 69, 19, 0.1); overflow: hidden; border: 1px solid #e8e2db; }
.module-head { background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%); color: white; padding: 1.25rem 1.75rem; margin: 0; }
.module-head h3 { margin: 0; font-weight: 600; font-size: 1.25rem; }
.module-body { padding: 1.5rem; }

@media (max-width: 768px) {
    .span3, .span5 { width: 100%; display: block; margin-right: 0; margin-bottom: 1rem; }
    .module-head { padding: 1rem 1.25rem; } .module-body { padding: 1rem; }
}
</style>

<script>
// Bootstrap 5 modal compatibility fix
document.addEventListener('DOMContentLoaded', function() {
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"][data-target]');
    modalTriggers.forEach(function(trigger) {
        const target = trigger.getAttribute('data-target');
        trigger.setAttribute('data-bs-toggle', 'modal');
        trigger.setAttribute('data-bs-target', target);
    });
    
    const dismissButtons = document.querySelectorAll('[data-dismiss="modal"]');
    dismissButtons.forEach(function(button) {
        button.setAttribute('data-bs-dismiss', 'modal');
    });
});
</script>

<?php   
}
?>