<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';


$kunid = (isset($_GET['kunid']) && $_GET['kunid'] != '') ? $_GET['kunid'] : '0';
$mowafeka = (isset($_GET['mowafeka']) && $_GET['mowafeka'] != '') ? $_GET['mowafeka'] : '0';

if($kunid != '0' && $kunid != 'undefined' && $mowafeka != '0'){
    $sql_update = "UPDATE zyt_kun_registrations SET kun_mowafeka = $mowafeka WHERE kun_id = '$kunid'";
    dbQuery($sql_update);
}

echo "done";