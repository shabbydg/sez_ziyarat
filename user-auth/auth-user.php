<?php

$its = (isset($_POST['itsid']) && $_POST['itsid'] != '') ? $_POST['itsid'] : '';
$authkey = (isset($_POST['authkey']) && $_POST['authkey'] != '') ? $_POST['authkey'] : '';

$verifier = file_get_contents('auth-string.txt');
//echo $verifier;

if ($its != '' && $authkey == $verifier)
{ 
    setcookie("user_shabab", "passed", time()+10800, "/", "colomboshabab.com"); 
    setcookie("user", $its, time()+10800, "/", "colomboshabab.com");
    header("Location: https://ziyarat.colomboshabab.com/");
}
else {
   header("Location: " . $_SERVER['HTTP_REFERER']. "&e");
}

?>