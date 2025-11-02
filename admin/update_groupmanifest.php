<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';


$kunid = (isset($_GET['kunid']) && $_GET['kunid'] != '') ? $_GET['kunid'] : '0';
$manifestgroup = (isset($_GET['manifestgroup']) && $_GET['manifestgroup'] != '') ? $_GET['manifestgroup'] : '0';

if($kunid != '0' && $kunid != 'undefined' && $manifestgroup != '0'){
    $sql_update = "UPDATE zyt_kun_registrations SET zyt_kun_manifest = $manifestgroup WHERE kun_id = '$kunid'";
    dbQuery($sql_update);
}

echo "done";