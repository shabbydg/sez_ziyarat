<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';

setcookie("redirect_key", '', time()-9999, "/", $cookie_host);
setcookie("user", '', time()-9999, "/", $cookie_host);
setcookie("user_shabab", '', time()-9999, "/", $cookie_host);

header("Location:" . $server_link . "?logout");

?>
