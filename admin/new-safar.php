<?php
//NEW SAFAR SCRIPT

?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>safars">Safars</a></li>
    <li class="breadcrumb-item active" aria-current="page">New Safar</li>
  </ol>
</nav>
<div class="module">
    <div class="module-head">
        <h3>Create New Safar</h3>
    </div>
    <div class="module-body">
        <form class="form-horizontal row-fluid" action="update-safar.php" method="post">
            <?php
            if(isset($_GET['e'])){
            ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>Oh snap!</strong> Something went wrong. It seems the date format used was incorrect. pls try again with format yyyy-mm-dd
            </div>
            <?php
            }
            ?>
            <input type="hidden" name="safarid" value="0" />
            <div class="control-group">
                <label class="control-label" for="name">Title</label>
                <div class="controls">
                    <input type="text" id="name" name="name" placeholder="" class="span8" required>
                    <!--<span class="help-inline">Minimum 5 Characters</span>-->
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="type">Type</label>
                <div class="controls">
                    <select id="type" name="type" data-placeholder="Select type.." class="span8" required>
                        <option value="">Select type..</option>
                        <option value="kun">KUN</option>
                        <option value="ba">Baitul Muqaddas/Amman</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description">Description</label>
                <div class="controls">
                    <textarea class="span8" rows="3" id="description" name="description"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="eta">ETA</label>
                <div class="controls">
                    <input type="text" id="eta" name="eta" placeholder="" class="span8 datepicker" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                    <span class="help-inline">Choose from Popup</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="capacity">Capacity</label>
                <div class="controls">
                    <input type="number" id="capacity" name="capacity" placeholder="Leave blank for unlimited capacity..." class="span8">
                    <span class="help-inline">Max number of pax. Leave blank for unlimited capacity.</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="status">Status</label>
                <div class="controls">
                    <select id="status" name="status" data-placeholder="Select status.." class="span8" required>
                        <option value="">Select status..</option>
                        <option value="planning">Planning</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="paused">Paused</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Accept New Registrations</label>
                <div class="controls">
                    <label class="radio inline">
                        <input type="radio" name="safar_acceptnewreg" id="safar_acceptnewreg" value="1" checked="checked">
                        Yes
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="safar_acceptnewreg" id="safar_acceptnewreg" value="0">
                        No
                    </label> 
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="noted">Notes</label>
                <div class="controls">
                    <textarea class="span8" rows="3" id="notes" name="notes"></textarea>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="reset" class="btn btn-warning">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="span12 align-right">
    <a href="<?php echo $server_link; ?>" class="btn btn-info">Back to Dashboard</a>
</div>