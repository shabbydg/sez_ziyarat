<?php
require_once 'common/config.php';
require_once 'common/functions.php';

$kunregid = (isset($_GET['reg_id']) && $_GET['reg_id'] != '') ? $_GET['reg_id'] : '0'; //0 means it's a new registration

$status = $_POST['kun_status'];

if($kunregid != '0'){
    //update the status of the registration
    $sql_update = "UPDATE zyt_kun_registrations SET kun_status = '$status' WHERE kun_id = $kunregid";
    dbQuery($sql_update);
    
    echo 1;
} else {
    echo 0; //reg_id was not set
}

?>