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
<style>
    /* Allow dropdown to extend beyond module boundaries */
    .module {
        overflow: visible !important;
    }
    .module-head {
        position: relative;
        z-index: 1000;
    }
</style>
<div class="module">
    <div class="module-head d-flex justify-content-between align-items-center" style="padding:20px 15px">
        <h3 class="mb-0">Organize Registrations</h3>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle" type="button" id="optionsMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="optionsMenuButton">
                    <a class="dropdown-item" href="<?php echo $server_link;?>excel_export_reg_order.php?safarid=<?php echo $safarid; ?>">Download - Order of Registration</a>
                    <a class="dropdown-item" href="<?php echo $server_link;?>excel_export_status_order.php?safarid=<?php echo $safarid; ?>">Download - Checking Purpose</a>
                    <a class="dropdown-item" href="<?php echo $server_link;?>excel_export_submission.php?safarid=<?php echo $safarid; ?>">Download - Submission File</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo $server_link;?>safars/kun-safar/<?php echo $safarid; ?>/grouping">Grouping</a>
                    <a class="dropdown-item" href="https://zadmin.colomboshabab.com/download_photos_zip.php?safarid=<?php echo $safarid; ?>">Download Photos</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo $server_link;?>get_grouplist.php?safarid=<?php echo $safarid; ?>">Print Embassy Submission Group Letter</a>
                    <a class="dropdown-item" href="#">Mass Update Status</a>
                </div>
            </div>
            <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-success">Dashboard</a>
        </div>
    </div>
    <div class="module-body">
        <div class="row">
            <div class="col-12">
                <div id="safarid" style="display: none"><?php echo $safarid; ?></div>
                <style>
                    #sortable {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    #sortable li {
                        background: #fff;
                        border: 1px solid #e0e0e0;
                        border-radius: 6px;
                        margin-bottom: 10px;
                        padding: 12px 15px;
                        cursor: move;
                        transition: all 0.2s ease;
                        display: flex;
                        align-items: center;
                        gap: 15px;
                    }
                    #sortable li:hover {
                        background: #f8f9fa;
                        border-color: var(--brown-primary);
                        box-shadow: 0 2px 8px rgba(139, 69, 19, 0.1);
                    }
                    #sortable li img {
                        width: 50px;
                        height: 50px;
                        object-fit: cover;
                        border-radius: 4px;
                        border: 2px solid #e0e0e0;
                        flex-shrink: 0;
                    }
                    #sortable li .user-info {
                        flex: 1;
                        min-width: 0;
                        display: flex;
                        flex-direction: column;
                        gap: 4px;
                    }
                    #sortable li .user-its {
                        font-size: 0.75rem;
                        color: #6c757d;
                        font-weight: 600;
                    }
                    #sortable li .user-name {
                        font-size: 0.95rem;
                        font-weight: 600;
                        color: #333;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    #sortable li .dropdowns {
                        display: flex;
                        gap: 10px;
                        flex-shrink: 0;
                    }
                    #sortable li select {
                        padding: 6px 10px;
                        border: 1px solid #ced4da;
                        border-radius: 4px;
                        font-size: 0.875rem;
                        min-width: 140px;
                        background-color: #fff;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    }
                    #sortable li select:hover {
                        border-color: var(--brown-primary);
                    }
                    #sortable li select:focus {
                        outline: none;
                        border-color: var(--brown-primary);
                        box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
                    }
                    @media (max-width: 768px) {
                        #sortable li {
                            flex-wrap: wrap;
                        }
                        #sortable li .dropdowns {
                            width: 100%;
                            margin-top: 10px;
                        }
                        #sortable li select {
                            flex: 1;
                            min-width: 0;
                        }
                    }
                </style>
                <ul id="sortable">
                    <?php
                    $sql_registrations = "SELECT * FROM zyt_kun_registrations WHERE safar_id = $safarid AND kun_status <> 'cancelled' ORDER BY zyt_kun_priority";
                    $result_registrations = dbQuery($sql_registrations);
                    while($row_reg = dbFetchAssoc($result_registrations)){
                        extract($row_reg);
                        ?>
                        <li id="item-<?php echo $kun_id; ?>">
                            <img src="<?php require 'support_files/get_applicant_image.php'; ?>" alt="Profile Photo" />
                            <div class="user-info">
                                <div class="user-its"><?php echo $kun_its; ?></div>
                                <div class="user-name"><?php if($kun_itsname != "") echo ucwords(strtolower($kun_itsname)); else echo ucwords(strtolower("$kun_fname $kun_mname $kun_lname")); ?></div>
                            </div>
                            <div class="dropdowns">
                                <select name="manifest" class="group-manifest" id="manifest-kun-<?php echo $kun_id;?>" data-kunid="<?php echo $kun_id; ?>">
                                    <option value="0" <?php if($zyt_kun_manifest == 0) echo "selected"; ?>>Manifest Group</option>
                                    <option value="1" <?php if($zyt_kun_manifest == 1) echo "selected"; ?>>Group 1</option>
                                    <option value="2" <?php if($zyt_kun_manifest == 2) echo "selected"; ?>>Group 2</option>
                                    <option value="3" <?php if($zyt_kun_manifest == 3) echo "selected"; ?>>Group 3</option>
                                    <option value="4" <?php if($zyt_kun_manifest == 4) echo "selected"; ?>>Group 4</option>
                                    <option value="5" <?php if($zyt_kun_manifest == 5) echo "selected"; ?>>Group 5</option>
                                    <option value="6" <?php if($zyt_kun_manifest == 6) echo "selected"; ?>>Group 6</option>
                                    <option value="7" <?php if($zyt_kun_manifest == 7) echo "selected"; ?>>Group 7</option>
                                    <option value="8" <?php if($zyt_kun_manifest == 8) echo "selected"; ?>>Group 8</option>
                                    <option value="9" <?php if($zyt_kun_manifest == 9) echo "selected"; ?>>Group 9</option>
                                    <option value="10" <?php if($zyt_kun_manifest == 10) echo "selected"; ?>>Group 10</option>
                                    <option value="11" <?php if($zyt_kun_manifest == 11) echo "selected"; ?>>Group 11</option>
                                    <option value="12" <?php if($zyt_kun_manifest == 12) echo "selected"; ?>>Group 12</option>
                                    <option value="13" <?php if($zyt_kun_manifest == 13) echo "selected"; ?>>Group 13</option>
                                    <option value="14" <?php if($zyt_kun_manifest == 14) echo "selected"; ?>>Group 14</option>
                                    <option value="15" <?php if($zyt_kun_manifest == 15) echo "selected"; ?>>Group 15</option>
                                </select>
                                <select name="mowafeka" class="group-mowafeka" id="mowafeka-kun-<?php echo $kun_id;?>" data-kunid="<?php echo $kun_id; ?>">
                                    <option value="0" <?php if($kun_mowafeka == 0) echo "selected"; ?>>Mowafeka Group</option>
                                    <option value="1" <?php if($kun_mowafeka == 1) echo "selected"; ?>>Mowafeka 1</option>
                                    <option value="2" <?php if($kun_mowafeka == 2) echo "selected"; ?>>Mowafeka 2</option>
                                    <option value="3" <?php if($kun_mowafeka == 3) echo "selected"; ?>>Mowafeka 3</option>
                                    <option value="4" <?php if($kun_mowafeka == 4) echo "selected"; ?>>Mowafeka 4</option>
                                    <option value="5" <?php if($kun_mowafeka == 5) echo "selected"; ?>>Mowafeka 5</option>
                                </select>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>