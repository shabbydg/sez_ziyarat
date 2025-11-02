<?php
require_once 'common/config.php';
require_once 'common/functions.php';

$itsid = (isset($_GET['itsid']) && $_GET['itsid'] != '') ? $_GET['itsid'] : '0';
$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

$sql_check_exists = "SELECT kun_its FROM zyt_kun_registrations WHERE kun_its = $itsid AND safar_id = $safarid";
$result = dbQuery($sql_check_exists);
if(dbNumRows($result)>0){
    echo "-1";
} else {
    echo "1";
}
?>