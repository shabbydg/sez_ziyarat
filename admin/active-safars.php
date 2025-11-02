<?php
//NEW SAFAR SCRIPT

?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Active Safars</li>
  </ol>
</nav>
<div class="module">
    <div class="module-head">
        <h3>Ongoing Safars</h3>
    </div>
    <div class="module-body table">
        <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display"
            width="100%">
            <thead>
                <tr>
                    <th>
                        Title
                    </th>
                    <th>
                        Registrations
                    </th>
                    <th>
                        Approved
                    </th>
                    <th>
                        Cancelled
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql_allsafars = "SELECT * FROM zyt_safar 
                                WHERE safar_status IN ('active','planning')
                                ORDER BY FIELD(safar_status, 'active', 'planning', 'paused', 'completed', 'cancelled'), safar_status";
                $result_allsafars = dbQuery($sql_allsafars);
                while($row_safar = dbFetchAssoc($result_allsafars)){
                    extract($row_safar);
                ?>
                <tr class="">
                    <td class="safar-title">
                        <?php if($safar_type == 'kun'){
                        ?>
                        <a href="<?php echo $server_link . "safars/kun-safar/$safar_id"; ?>"><?php echo strtoupper($safar_type) . " - $safar_name"; ?></a>
                        <?php
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if($safar_type == 'kun'){
                            $sql_registered = "SELECT COUNT(kun_id) AS registered FROM zyt_kun_registrations WHERE safar_id = $safar_id";
                            $result_registered = dbQuery($sql_registered);
                            while($row_registered = dbFetchAssoc($result_registered)){
                                extract($row_registered);
                                if($registered == '') $registered = 0;
                            }
                            echo $registered;
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if($safar_type == 'kun'){
                            $sql_approved = "SELECT COUNT(kun_id) AS approved FROM zyt_kun_registrations WHERE safar_id = $safar_id AND kun_status IN ('approved','raza')";
                            $result_approved = dbQuery($sql_approved);
                            while($row_approved = dbFetchAssoc($result_approved)){
                                extract($row_approved);
                                if($approved == '') $approved = 0;
                            }
                            echo $approved;
                        }
                        ?>
                    </td>
                    <td class="center">
                        <?php
                        if($safar_type == 'kun'){
                            $sql_cancelled = "SELECT COUNT(kun_id) AS cancelled FROM zyt_kun_registrations WHERE safar_id = $safar_id AND kun_status LIKE 'cancelled'";
                            $result_cancelled = dbQuery($sql_cancelled);
                            while($row_cancelled = dbFetchAssoc($result_cancelled)){
                                extract($row_cancelled);
                                if($cancelled == '') $cancelled = 0;
                            }
                            echo $cancelled;
                        }
                        ?>
                    </td>
                    <td class="center">
                        <?php echo ucfirst($safar_status); ?>
                    </td>
                    <td><a href="<?php echo $server_link . "safars/edit-safar/$safar_id"; ?>"><i class="icon-pencil"></i></a></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>
                        Title
                    </th>
                    <th>
                        Registrations
                    </th>
                    <th>
                        Approved
                    </th>
                    <th>
                        Cancelled
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>