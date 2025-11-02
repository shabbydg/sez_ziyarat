<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';


$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

$i = 1;

foreach ($_POST['item'] as $value) {
    // Execute statement:
    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
    $sql_update = "UPDATE zyt_kun_registrations SET zyt_kun_priority = $i WHERE kun_id = $value AND safar_id = $safarid";
    dbQuery($sql_update);
    $i++;
}