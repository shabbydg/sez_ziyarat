<?php
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name, safar_acceptnewreg, safar_status FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars" class="text-brown">All Safars</a></li>
    <li class="breadcrumb-item active" aria-current="page">KUN - <?php echo $safar_name; ?></li>
  </ol>
</nav>
<div class="module">
    <div class="module-head d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="mb-0 me-3"><i class="fas fa-users me-2"></i>KUN Registrations</h3>
        
        <div class="d-flex gap-2 flex-wrap">
            <?php if($safar_status == 'planning'){ ?>
                <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>/new-pre-registration" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i>New Pre-Registration
                </a>
            <?php } else { ?>
                <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>/new-registration" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i>New Registration
                </a>
            <?php } ?>
            
            <div class="dropdown">
                <button class="btn btn-brown dropdown-toggle" type="button" id="adminActionsButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog me-1"></i>Admin Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminActionsButton">
                    <li><h6 class="dropdown-header"><i class="fas fa-download me-1"></i>Downloads</h6></li>
                    <li><a class="dropdown-item" href="<?php echo $server_link;?>excel_export_reg_order.php?safarid=<?php echo $safarid; ?>">
                        <i class="fas fa-file-excel me-2"></i>Order of Registration
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo $server_link;?>excel_export_status_order.php?safarid=<?php echo $safarid; ?>">
                        <i class="fas fa-file-excel me-2"></i>Checking Purpose
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo $server_link;?>excel_export_submission.php?safarid=<?php echo $safarid; ?>">
                        <i class="fas fa-file-excel me-2"></i>Submission File
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header"><i class="fas fa-tools me-1"></i>Management</h6></li>
                    <li><a class="dropdown-item" href="<?php echo $server_link;?>safars/kun-safar/<?php echo $safarid; ?>/grouping">
                        <i class="fas fa-layer-group me-2"></i>Grouping
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo $server_link;?>get_grouplist.php?safarid=<?php echo $safarid; ?>">
                        <i class="fas fa-print me-2"></i>Print Group Letter
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-edit me-2"></i>Mass Update Status
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="module-body">
        <!-- Desktop Table View -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%" id="kunapplicants" style="min-width: 1200px;">
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
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_registrations = "SELECT * FROM zyt_kun_registrations WHERE safar_id = $safarid";
                $result_registrations = dbQuery($sql_registrations);
                while($row_reg = dbFetchAssoc($result_registrations)){
                    extract($row_reg);
                    if($kun_status == 'cancelled'){
                        ?>
                    <tr class="alert alert-error">
                        <td class="safar-title" align="center">
                            <img src="<?php require 'support_files/get_applicant_image.php'; ?>" width="75" /><br />
                            <?php echo $kun_its; ?>
                        </td>
                        <td>
                            <?php echo $kun_ppno; ?>
                        </td>
                        <td>
                            <?php echo $kun_itsname; ?>
                            <br />
                            <?php
                            //get the list of photos
                            $sql_photos = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
                            $result_photos = dbQuery($sql_photos);
                            while($row_photos = dbFetchAssoc($result_photos)){
                                extract($row_photos);
                                ?>
                                <div style="display:inline-block; margin-right: 5px;">
                                    <?php 
                                    $file_extension = strtolower(pathinfo($photo_filename, PATHINFO_EXTENSION));
                                    if ($file_extension === 'pdf') {
                                        // For PDFs, use a custom modal approach
                                        echo '<a href="#" onclick="openPdfModal(\'https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '\'); return false;" style="cursor: pointer;">';
                                        echo '<img src="https://ziyarat.colomboshabab.com/images/pdfs.png" width="50px" alt="PDF Document">';
                                        echo '</a>';
                                    } else {
                                        // For images, use Ekko Lightbox
                                        echo '<a href="https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '" data-toggle="lightbox" data-gallery="photo-gallery">';
                                        echo '<img src="https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '" width="50px">';
                                        echo '</a>';
                                    }
                                    ?>
                                    <br />
                                    <a href="<?php echo $server_link; ?>delete_photo.php?pid=<?php echo $photos_id; ?>" style="font-size:9px">DEL</a>
                                </div>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo ucfirst($kun_status); ?>
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
                            ?>
                        </td>
                        <td>
                            <span class="text-warning action-addis">
                                <form action="<?php echo $server_link;?>kun-status-update.php?reg_id=<?php echo $kun_id; ?>" method="post">
                                    <select name="kun_status" class="kun-status">
                                        <option value="registered" <?php if($kun_status == 'registered') echo "selected"; ?>>Registered</option>
                                        <option value="raza" <?php if($kun_status == 'raza') echo "selected"; ?>>Raza</option>
                                        <option value="approved" <?php if($kun_status == 'approved') echo "selected"; ?>>Approved</option>
                                        <option value="submitted for visa" <?php if($kun_status == 'submitted for visa') echo "selected"; ?>>Submitted for Visa</option>
                                        <option value="visa passed" <?php if($kun_status == 'visa passed') echo "selected"; ?>>Visa Passed</option>
                                        <option value="visa & ticket issued" <?php if($kun_status == 'visa & ticket issued') echo "selected"; ?>>Visa &amp; Ticket Issued</option>
                                        <option value="cancelled" <?php if($kun_status == 'cancelled') echo "selected"; ?>>Cancelled</option>
                                    </select>
                                </form>
                                <hr />
                                Count: <?php echo $kun_printcount; ?><br />
                                <?php if($kun_printcount != 0){ ?>
                                    Last Print: <br /><?php echo date_format(date_create($kun_lastprintdate),"d/m/Y H:iA"); ?>
                                <?php } ?>
                                <div>
                                    <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning">Update Passport Photos</a>
                                </div>
                            </span>
                        </td>
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
                                if($kun_fname != "") echo "$kun_fname $kun_mname $kun_lname";
                                else echo $kun_itsname;
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
                                <div style="display:inline-block; margin-right: 5px;">
                                    <?php 
                                    $file_extension = strtolower(pathinfo($photo_filename, PATHINFO_EXTENSION));
                                    if ($file_extension === 'pdf') {
                                        // For PDFs, use a custom modal approach
                                        echo '<a href="#" onclick="openPdfModal(\'https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '\'); return false;" style="cursor: pointer;">';
                                        echo '<img src="https://ziyarat.colomboshabab.com/images/pdfs.png" width="50px" alt="PDF Document">';
                                        echo '</a>';
                                    } else {
                                        // For images, use Ekko Lightbox
                                        echo '<a href="https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '" data-toggle="lightbox" data-gallery="photo-gallery">';
                                        echo '<img src="https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '" width="50px">';
                                        echo '</a>';
                                    }
                                    ?>
                                    <br />
                                    <a href="<?php echo $server_link; ?>delete_photo.php?pid=<?php echo $photos_id; ?>" style="font-size:9px">DEL</a>
                                </div>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo ucfirst($kun_status); ?>
                            <?php if($kun_declaration == 1) echo "<br /><span class='text-success'>Ready for processing</span>"; ?>
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
                            ?><br />
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#AddPayment-<?php echo $kun_id;?>">
                              Add Payment
                            </button>
                            <!-- Modal -->
                            <div id="AddPayment-<?php echo $kun_id;?>" class="modal fade" role="dialog">
                              <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Add Payment</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form class="form-horizontal row-fluid" action="<?php echo $server_link; ?>add-payment.php" method="post">
                                        <input type="hidden" name="dbregid" value="<?php echo $kun_id; ?>" />
                                        <input type="hidden" name="dbsafarid" value="<?php echo $safarid; ?>" />
                                        <input type="hidden" name="dbname" value="<?php if($kun_itsname != "") echo $kun_itsname; else echo "$kun_fname $kun_mname $kun_lname"; ?>" />
                                        <div class="control-group">
                                            <label class="control-label" for="dbpurpose">Purpose</label>
                                            <div class="controls">
                                                <input type="text" name="dbpurpose" placeholder="" class="span12" required value="Payment for <?php echo $safar_name; ?>">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="dbemail">Email</label>
                                            <div class="controls">
                                                <input type="email" name="dbemail" placeholder="" class="span12" required value="<?php echo $kun_email; ?>">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="dbamount">Amount</label>
                                            <div class="controls">
                                                <input type="number" name="dbamount" placeholder="" class="span12" required>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="dbnotes">Notes</label>
                                            <div class="controls">
                                                <input type="text" name="dbnotes" placeholder="" class="span12">
                                            </div>
                                        </div>
                                          <hr />
                                        <button class="btn btn-success" type="submit">Submit</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </form>
                                  </div>
                                </div>

                              </div>
                            </div>
                        </td>
                        <td>
                            <form action="<?php echo $server_link;?>kun-status-update.php?reg_id=<?php echo $kun_id; ?>" method="post">
                                <select name="kun_status" class="kun-status">
                                    <option value="registered" <?php if($kun_status == 'registered') echo "selected"; ?>>Registered</option>
                                    <option value="raza" <?php if($kun_status == 'raza') echo "selected"; ?>>Raza</option>
                                    <option value="approved" <?php if($kun_status == 'approved') echo "selected"; ?>>Approved</option>
                                    <option value="submitted for visa" <?php if($kun_status == 'submitted for visa') echo "selected"; ?>>Submitted for Visa</option>
                                    <option value="visa passed" <?php if($kun_status == 'visa passed') echo "selected"; ?>>Visa Passed</option>
                                    <option value="visa & ticket issued" <?php if($kun_status == 'visa & ticket issued') echo "selected"; ?>>Visa &amp; Ticket Issued</option>
                                    <option value="cancelled" <?php if($kun_status == 'cancelled') echo "selected"; ?>>Cancelled</option>
                                </select>
                            </form>
                            <hr />
                            <a href="<?php echo $server_link; ?>print_kun_visa.php?kunid=<?php echo $kun_id; ?>">Print Visa</a><br />
                            <span class="text-warning action-addis">
                                Count: <?php echo $kun_printcount; ?><br />
                                <?php if($kun_printcount != 0){ ?>
                                    Last Print: <br /><?php echo date_format(date_create($kun_lastprintdate),"d/m/Y H:iA"); ?>
                                <?php } ?>
                            </span>
                            <div>
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning">Update Passport Photos</a>
                            </div>
                        </td>
                    </tr>
                    <?php
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
                    <th>
                        Action
                    </th>
                </tr>
            </tfoot>
        </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-lg-none">
            <div class="alert alert-info">
                <strong>Mobile View Active</strong> - This view is optimized for mobile devices.
            </div>
            <?php
            $sql_registrations_mobile = "SELECT * FROM zyt_kun_registrations WHERE safar_id = $safarid";
            $result_registrations_mobile = dbQuery($sql_registrations_mobile);
            while($row_reg_mobile = dbFetchAssoc($result_registrations_mobile)){
                extract($row_reg_mobile);
                ?>
                <div class="card mb-3 <?php echo ($kun_status == 'cancelled') ? 'border-danger' : ''; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <?php if($kun_status == 'cancelled'){ ?>
                                <span class="text-danger"><?php echo $kun_its; ?></span>
                            <?php } else { ?>
                                <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="text-decoration-none">
                                    <?php echo $kun_its; ?>
                                </a>
                            <?php } ?>
                        </h6>
                        <span class="badge <?php echo ($kun_status == 'cancelled') ? 'bg-danger' : 'bg-primary'; ?>">
                            <?php echo ucfirst($kun_status); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Profile Image and Basic Info -->
                        <div class="row mb-3">
                            <div class="col-4 text-center">
                                <?php if($kun_status == 'cancelled'){ ?>
                                    <img src="<?php require 'support_files/get_applicant_image.php'; ?>" width="75" class="img-fluid rounded" />
                                <?php } else { ?>
                                    <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>">
                                        <img src="<?php require 'support_files/get_applicant_image.php'; ?>" width="75" class="img-fluid rounded" />
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="col-8">
                                <h6 class="mb-1">
                                    <?php if($kun_status == 'cancelled'){ ?>
                                        <?php echo $kun_itsname; ?>
                                    <?php } else { ?>
                                        <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="text-decoration-none">
                                            <?php 
                                            if($kun_fname != "") echo "$kun_fname $kun_mname $kun_lname";
                                            else echo $kun_itsname;
                                            ?>
                                        </a>
                                    <?php } ?>
                                </h6>
                                <p class="mb-1">
                                    <strong>Passport:</strong> 
                                    <?php if($kun_status == 'cancelled'){ ?>
                                        <?php echo $kun_ppno; ?>
                                    <?php } else { ?>
                                        <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-registration/$kun_id"; ?>" class="text-decoration-none">
                                            <?php echo $kun_ppno; ?>
                                        </a>
                                    <?php } ?>
                                </p>
                                <?php if($kun_declaration == 1) { ?>
                                    <span class="badge bg-success">Ready for processing</span>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Photos Section -->
                        <div class="mb-3">
                            <h6 class="mb-2">Documents & Photos:</h6>
                            <div class="d-flex flex-wrap">
                                <?php
                                $sql_photos_mobile = "SELECT * FROM zyt_kun_photos WHERE kun_id = $kun_id";
                                $result_photos_mobile = dbQuery($sql_photos_mobile);
                                while($row_photos_mobile = dbFetchAssoc($result_photos_mobile)){
                                    extract($row_photos_mobile);
                                    ?>
                                    <div class="me-2 mb-2">
                                        <?php 
                                        $file_extension = strtolower(pathinfo($photo_filename, PATHINFO_EXTENSION));
                                        if ($file_extension === 'pdf') {
                                            echo '<a href="#" onclick="openPdfModal(\'https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '\'); return false;" style="cursor: pointer;">';
                                            echo '<img src="https://ziyarat.colomboshabab.com/images/pdfs.png" width="40px" alt="PDF Document" class="img-thumbnail">';
                                            echo '</a>';
                                        } else {
                                            echo '<a href="https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '" data-toggle="lightbox" data-gallery="photo-gallery-mobile">';
                                            echo '<img src="https://ziyarat.colomboshabab.com/images/uploads/' . $photo_filename . '" width="40px" class="img-thumbnail">';
                                            echo '</a>';
                                        }
                                        ?>
                                        <div class="text-center">
                                            <a href="<?php echo $server_link; ?>delete_photo.php?pid=<?php echo $photos_id; ?>" class="btn btn-sm btn-outline-danger" style="font-size:10px">DEL</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mb-3">
                            <h6 class="mb-2">Payment Information:</h6>
                            <?php
                            $receipt_total_mobile = 0;
                            $sql_payments_mobile = "SELECT receipt_no, db_amount FROM zyt_debtor_control WHERE db_regid = $kun_id AND safar_id = $safarid";
                            $result_payments_mobile = dbQuery($sql_payments_mobile);
                            if(dbNumRows($result_payments_mobile)>0){
                                ?>
                                <div class="mb-2">
                                    <strong>Receipts:</strong>
                                    <?php
                                    while($row_payments_mobile = dbFetchAssoc($result_payments_mobile)){
                                        extract($row_payments_mobile);
                                        echo "<span class='badge bg-warning text-dark me-1'>$receipt_no</span>";
                                        $receipt_total_mobile = $receipt_total_mobile + $db_amount;
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            if($receipt_total_mobile > 0){
                                echo "<div class='mb-2'><strong>Total:</strong> " . number_format($receipt_total_mobile, 2, ".", ",") . "</div>";
                            }
                            ?>
                            <?php if($kun_status != 'cancelled'){ ?>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#AddPayment-<?php echo $kun_id;?>">
                                    <i class="fas fa-plus"></i> Add Payment
                                </button>
                            <?php } ?>
                        </div>

                        <!-- Status Update -->
                        <div class="mb-3">
                            <h6 class="mb-2">Status Update:</h6>
                            <form action="<?php echo $server_link;?>kun-status-update.php?reg_id=<?php echo $kun_id; ?>" method="post">
                                <select name="kun_status" class="form-select kun-status">
                                    <option value="registered" <?php if($kun_status == 'registered') echo "selected"; ?>>Registered</option>
                                    <option value="raza" <?php if($kun_status == 'raza') echo "selected"; ?>>Raza</option>
                                    <option value="approved" <?php if($kun_status == 'approved') echo "selected"; ?>>Approved</option>
                                    <option value="submitted for visa" <?php if($kun_status == 'submitted for visa') echo "selected"; ?>>Submitted for Visa</option>
                                    <option value="visa passed" <?php if($kun_status == 'visa passed') echo "selected"; ?>>Visa Passed</option>
                                    <option value="visa & ticket issued" <?php if($kun_status == 'visa & ticket issued') echo "selected"; ?>>Visa &amp; Ticket Issued</option>
                                    <option value="cancelled" <?php if($kun_status == 'cancelled') echo "selected"; ?>>Cancelled</option>
                                </select>
                            </form>
                        </div>

                        <!-- Print Information -->
                        <div class="mb-3">
                            <h6 class="mb-2">Print Information:</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Count: <?php echo $kun_printcount; ?></small>
                                </div>
                                <div class="col-6">
                                    <?php if($kun_printcount != 0){ ?>
                                        <small class="text-muted">Last Print:<br /><?php echo date_format(date_create($kun_lastprintdate),"d/m/Y H:iA"); ?></small>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if($kun_status != 'cancelled'){ ?>
                                <div class="mt-2">
                                    <a href="<?php echo $server_link; ?>print_kun_visa.php?kunid=<?php echo $kun_id; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print"></i> Print Visa
                                    </a>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <a href="<?php echo $server_link . "safars/kun-safar/$safarid/edit-photos/$kun_id"; ?>" class="btn btn-warning">
                                <i class="fas fa-camera"></i> Update Passport Photos
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payment Modal for Mobile -->
                <?php if($kun_status != 'cancelled'){ ?>
                <div id="AddPayment-<?php echo $kun_id;?>" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Payment</h4>
                            </div>
                            <div class="modal-body">
                                <form class="form-horizontal row-fluid" action="<?php echo $server_link; ?>add-payment.php" method="post">
                                    <input type="hidden" name="dbregid" value="<?php echo $kun_id; ?>" />
                                    <input type="hidden" name="dbsafarid" value="<?php echo $safarid; ?>" />
                                    <input type="hidden" name="dbname" value="<?php if($kun_itsname != "") echo $kun_itsname; else echo "$kun_fname $kun_mname $kun_lname"; ?>" />
                                    <div class="control-group">
                                        <label class="control-label" for="dbpurpose">Purpose</label>
                                        <div class="controls">
                                            <input type="text" name="dbpurpose" placeholder="" class="span12" required value="Payment for <?php echo $safar_name; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="dbemail">Email</label>
                                        <div class="controls">
                                            <input type="email" name="dbemail" placeholder="" class="span12" required value="<?php echo $kun_email; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="dbamount">Amount</label>
                                        <div class="controls">
                                            <input type="number" name="dbamount" placeholder="" class="span12" required>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="dbnotes">Notes</label>
                                        <div class="controls">
                                            <input type="text" name="dbnotes" placeholder="" class="span12">
                                        </div>
                                    </div>
                                    <hr />
                                    <button class="btn btn-success" type="submit">Submit</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php
            }
            ?>
        </div>

<!-- PDF Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">PDF Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <iframe id="pdfViewer" src="" width="100%" height="600px" style="border: none;"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a id="pdfDownload" href="" target="_blank" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
