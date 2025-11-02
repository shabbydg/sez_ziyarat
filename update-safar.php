<?php
require_once 'common/config.php';
require_once 'common/functions.php';
require 'PHPMailer/PHPMailerAutoload.php';

$safarid = (isset($_POST['safarid']) && $_POST['safarid'] != '') ? $_POST['safarid'] : '0'; //0 means it's a new safar

$user_id = getActiveUser();

$type = $_POST['type'];
$name = $_POST['name'];
$description = $_POST['description'];
$eta = (isset($_POST['eta']) && $_POST['eta'] != '') ? $_POST['eta'] : '';
$capacity = (isset($_POST['capacity']) && $_POST['capacity'] != '') ? $_POST['capacity'] : '0';
$status = $_POST['status'];
$notes = $_POST['notes'];

//check if the date entered is valid
if($eta != '' && !validateDate($eta)){
    header("Location: " . $server_link . "new-safar?e");
}

if($safarid == 0){
    //INSERT
    $sql_insert = "INSERT INTO zyt_safar (safar_type, safar_name, safar_description, safar_eta, safar_capacity, safar_status, safar_notes, safar_createdby, safar_createtime, safar_lasteditedby, safar_lastupdate)
                    VALUES ('$type', '$name', '$description', '$eta', $capacity, '$status', '$notes', $user_id, NOW(), $user_id, NOW())";
    dbQuery($sql_insert);
} else {
    $sql_update = "UPDATE zyt_safar SET safar_type = '$type', safar_name = '$name', safar_description = '$description', safar_eta = $eta, safar_capacity = $capacity, safar_status = '$status', safar_notes = '$notes', safar_createdby $user_id, safar_createtime = NOW(), safar_lasteditedby = $user_id, safar_lastupdate = NOW()";
    dbQuery($sql_update);
}

header("Location: " . $server_link . "safars");
?>