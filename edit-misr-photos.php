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
<div class="module">
    <div class="module-head">
        <h3>Edit Registration</h3>
    </div>
    <div class="module-body">
        <form class="form-horizontal row-fluid" action="<?php echo $server_link; ?>upload-kun-photos.php" method="post" id="kun-photos" enctype="multipart/form-data">
            <?php
            if(isset($_GET['e'])){
            ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>Oh snap!</strong> Something went wrong. The files were not updated.
            </div>
            <?php
            }
            ?>
            <input type="hidden" name="safarid" value="<?php echo $safarid; ?>" />
            <input type="hidden" name="kunregid" value="<?php echo $kunregid; ?>" />
            <div class="mb-3">
                <label for="kunPhotos" class="form-label">Default file input example</label>
                <input class="form-control" type="file" id="kunPhotos" name="kunPhotos[]" multiple>
            </div>
            
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-danger">Cancel</button>
                </div>
            </div>
        </form>
        <div>
            
        </div>
    </div>
</div>
<div class="align-right">
    <a href="<?php echo $server_link; ?>" class="btn btn-info">Back to Dashboard</a>
</div>
    <?php
    }
}
?>
