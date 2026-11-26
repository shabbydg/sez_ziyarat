<?php

require_once 'common/config.php';
require_once 'common/config2.php';
require_once 'common/database.php';

$safar_id = $_POST['safar_id'];
$its = $_POST['its'];
$name = $_POST['name'];
$email = $_POST['email'];
$contact = $_POST['contact'];

$sql = "INSERT INTO zyt_kun_registrations (safar_id, kun_its, kun_itsname, kun_email, kun_contact, kun_status, kun_lastupdate, kun_lastupdatememid) VALUES ('$safar_id', '$its', '$name', '$email', '$contact', 'registered', NOW(), '$its')";
dbQuery($sql);

// Stub row in anjuman.foreign_census — defaults cover remaining NOT NULL columns.
// ON DUPLICATE KEY UPDATE so a retry after a partial prior failure does not fatal.
$sql_add_to_foreign_census = "INSERT INTO foreign_census
    (ITS_ID, HOF_ID, name, email, mobile, father_its, mother_its, spouse_its)
    VALUES ('$its', '$its', '$name', '$email', '$contact', 0, 0, 0)
    ON DUPLICATE KEY UPDATE
        HOF_ID = VALUES(HOF_ID),
        name = VALUES(name),
        email = VALUES(email),
        mobile = VALUES(mobile)";
dbQuery2($sql_add_to_foreign_census);

setcookie("user_shabab", "passed", time()+10800, "/", "colomboshabab.com");  /* expire in 1 hour */
setcookie("user", $its, time()+10800, "/", "colomboshabab.com");  /* expire in 1 hour */
header("Location: https://ziyarat.colomboshabab.com/safars/kun-safar/$safar_id");

?>
