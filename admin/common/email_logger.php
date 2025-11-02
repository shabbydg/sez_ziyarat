<?php

date_default_timezone_set('Asia/Colombo');

$log_date = date('l jS \of F Y h:i:s A');

$file = 'logs/email_log.txt';
$log = "Created debit note for ziyarat module with receipt: $receipt_no | $log_date: " . $e->errorMessage();
file_put_contents($file, $log, FILE_APPEND | LOCK_EX);

?>