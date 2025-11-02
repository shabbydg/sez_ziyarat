<?php
//NEW SAFAR SCRIPT

?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Safars</li>
  </ol>
</nav>
<div class="module">
    <div class="module-head">
        <h3>All Safars</h3>
    </div>
    <div class="module-body table">
        <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display"
            width="100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql_allsafars = "SELECT * FROM zyt_safar WHERE safar_status IN ('active', 'planning', 'paused') ORDER BY FIELD(safar_status, 'active', 'planning', 'paused'), safar_status";
                $result_allsafars = dbQuery($sql_allsafars);
                while($row_safar = dbFetchAssoc($result_allsafars)){
                    extract($row_safar);
                ?>
                <tr class="">
                    <td class="safar-title">
                        <?php if($safar_type == 'kun'){
                            if(in_array($safar_status , array("paused", "completed", "cancelled"))){
                                echo strtoupper($safar_type) . " - $safar_name";
                            } else {
                        ?>
                        <a href="<?php echo $server_link . "safars/kun-safar/$safar_id"; ?>"><?php echo strtoupper($safar_type) . " - $safar_name"; ?></a>
                        <?php
                            } 
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo ucwords($safar_status); ?>
                    </td>
                    <td>
                        <a href="<?php echo $server_link . "safars/kun-safar/$safar_id"; ?>" class="btn btn-success">Check Status</a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>