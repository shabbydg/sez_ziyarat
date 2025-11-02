<?php
require_once 'common/config.php';
require_once 'common/functions.php';
require 'PHPMailer/PHPMailerAutoload.php';

$kunregid = (isset($_POST['kunregid']) && $_POST['kunregid'] != '') ? $_POST['kunregid'] : '0'; //0 means it's a new registration

$user_id = getActiveUser();

$safarid = $_POST['safarid'];
$its = $_POST['its'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$itsname = $_POST['itsname'];

if($kunregid == 0){
    //INSERT
    $sql_insert = "INSERT INTO `zyt_kun_registrations` (`safar_id`, `kun_its`, `kun_itsname`, `kun_status`, `kun_contact`, `kun_email`, `kun_declaration`, `kun_lastupdate`,`kun_lastupdatememid`) VALUES ('$safarid', '$its', '$itsname', 'registered', '$contact', '$email', 0, NOW(),'$its');";
    dbQuery($sql_insert);
} else {
    $sql_update = "UPDATE zyt_kun_registrations SET safar_id = '$safarid', kun_its = '$its', kun_itsname = '$itsname', `kun_contact` = '$contact', `kun_email` = '$email', `kun_declaration` = 0, `kun_lastupdate` = NOW(),`kun_lastupdatememid` = '$its' WHERE kun_id = $kunregid";
    dbQuery($sql_update);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
?>