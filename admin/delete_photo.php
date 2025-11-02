<?php
require_once 'common/config.php';
require_once 'common/functions.php';

$pid = (isset($_GET['pid']) && $_GET['pid'] != '') ? $_GET['pid'] : '0';

if($pid == 0) exit();

//get the filename to delete
$sql_photo = "SELECT photo_filename FROM zyt_kun_photos WHERE photos_id = $pid";
$result_photo = dbQuery($sql_photo);
extract(dbFetchAssoc($result_photo));

$adminthisFile = str_replace('\\', '/', __FILE__);
$adminRoot  = str_replace('admin/delete_photo.php', '', $adminthisFile);

if(file_exists($adminRoot.'images/uploads/'. $photo_filename)){
    unlink($adminRoot.'images/uploads/'. $photo_filename);
    //delete from the database
    $sql_del = "DELETE FROM zyt_kun_photos WHERE photos_id = $pid";
    dbQuery($sql_del);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>