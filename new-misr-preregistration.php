<?php
//NEW SAFAR SCRIPT
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

//get all the safar details
$sql_safar_dets = "SELECT safar_name FROM zyt_safar WHERE safar_id = $safarid";
extract(dbFetchAssoc(dbQuery($sql_safar_dets)));
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars">All Safars</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>">KUN - <?php echo $safar_name; ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">New Registration</li>
  </ol>
</nav>
<div class="module">
    <div class="module-head">
        <h3>New Registration</h3>
    </div>
    <div class="module-body">
        <div class="container">
            <div class="row">
                <div class="span2">
                    <div class="form-group col-md-12">
                        <img src="<?php echo $server_link; ?>images/no_image.jpg" class="img-responsive" id="profileImg" />
                    </div>
                </div>
                <div class="span9">
                    <form class="form-horizontal row-fluid" action="<?php echo $server_link; ?>update-kun-preregistration.php" method="post" id="kun-registration">
                        <?php
                        if(isset($_GET['e'])){
                        ?>
                        <div class="alert alert-error">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>Oh snap!</strong> Something went wrong. You may have entered an ITS number incorrectly or:
                            <ul></ul>
                        </div>
                        <?php
                        }
                        ?>
                        <input type="hidden" name="safarid" id="safarid" value="<?php echo $safarid; ?>" />
                        <div class="control-group">
                            <label class="control-label" for="its">ITS Number</label>
                            <div class="controls">
                                <input type="text" id="preits" name="its" placeholder="" class="span3" required>
                                <span class="help-inline" id="itsno_prereg_help">Eight digit number</span>
                                <span class="help-inline"><img src="<?php echo $server_link; ?>images/loading.gif" style="width: 30px;" class="invisi" /></span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="fname">Name</label>
                            <div class="controls">
                                <input type="text" id="itsname" name="itsname" placeholder="Name" class="span6" required>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="fname">Contact Information</label>
                            <div class="controls">
                                <input type="text" id="contact" name="contact" placeholder="Mobile Number" class="span3" required>
                                <input type="email" id="email" name="email" placeholder="Email Address" class="span5" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="reset" class="btn btn-warning">Reset</button>
                                <a href="<?php echo $server_link; ?>safars/kun-safar/<?php echo $safarid; ?>" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="align-right">
    <a href="<?php echo $server_link; ?>" class="btn btn-info">Back to Dashboard</a>
</div>