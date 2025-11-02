<?php
require_once 'common/config.php';
require_once 'common/functions.php';

$kunid = (isset($_GET['kunid']) && $_GET['kunid'] != '') ? $_GET['kunid'] : '-1';

$date = date('Y-m-d H:i:s');

if($kunid == -1){
    echo -1;
} else {
    $sql_update = "UPDATE zyt_kun_registrations SET kun_lastprintdate = '$date', kun_printcount = kun_printcount + 1 WHERE kun_id = $kunid";
    dbQuery($sql_update);
    echo 1;
}

?>