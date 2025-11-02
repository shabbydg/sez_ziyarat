<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';

require '../PHPMailer/PHPMailerAutoload.php';

$user_id = getActiveUser();

$regid = (isset($_POST['dbregid']) && $_POST['dbregid'] != '') ? $_POST['dbregid'] : '0';
$safarid = (isset($_POST['dbsafarid']) && $_POST['dbsafarid'] != '') ? $_POST['dbsafarid'] : '0';
$dbname = (isset($_POST['dbname']) && $_POST['dbname'] != '') ? $_POST['dbname'] : '';
$dbpurpose = (isset($_POST['dbpurpose']) && $_POST['dbpurpose'] != '') ? $_POST['dbpurpose'] : '';
$dbnotes = (isset($_POST['dbnotes']) && $_POST['dbnotes'] != '') ? $_POST['dbnotes'] : '';
$dbemail = (isset($_POST['dbemail']) && $_POST['dbemail'] != '') ? $_POST['dbemail'] : '';
$dbamount = (isset($_POST['dbamount']) && $_POST['dbamount'] != '') ? $_POST['dbamount'] : '';

//get the max number of the subscription receipt number
$sql_max = "SELECT MAX(receipt_no) AS receipt_no FROM zyt_debtor_control";
$result_max = dbQuery($sql_max);
extract(dbFetchAssoc($result_max));

if($receipt_no == 0) $receipt_no = 552;
$receipt_no = $receipt_no + 1;
$receipt_code = md5($receipt_no);

$sql_update_payment = "INSERT INTO zyt_debtor_control (db_date, db_regid, safar_id, db_name, db_purpose, db_notes, db_amount, db_email, receipt_no, receipt_code, receipt_date, user_id)
                        VALUES(NOW(), $regid, $safarid, '$dbname', '$dbpurpose', '$dbnotes', '$dbamount', '$dbemail', $receipt_no, '$receipt_code', NOW(), $user_id)";
dbQuery($sql_update_payment);

$mail = new PHPMailer(true);

//$mail->SMTPDebug = 3;                               // Enable verbose debug output
try{
    
    $dbamount = number_format($dbamount, 2, ".", ",");

    $mail->From = 'info@colomboshabab.com';
    $mail->FromName = 'Ziyarat Team - SEZ Colombo';
    $mail->addAddress($dbemail, $dbname);     // Add a recipient

    $mail->isHTML(true); // Set email format to HTML

    $mail->Subject = '[SEZ, Colombo] Payment Receipt';
    $mail->Body    = "<style>body{font-family: 'Roboto',Arial,Helvetica,sans-serif;}</style><img src='https://m.colomboshabab.com/images/shabab_logo.png' width='100' /><p>Salaams $dbname,</p><p>Thank you for your payment for $dbpurpose.</p><p><strong>Your Receipt Number:</strong>&nbsp;&nbsp;$receipt_no<br /><strong>Amount:</strong>&nbsp;&nbsp;$dbamount</p><p>To view your receipt, please <a href='https://ziyarat.colomboshabab.com/print_receipt.php?rc=$receipt_code' target='_blank'>click here</a>.</p><p>Wassalaam,<br /><strong>Shababul Eidiz Zahabi, Colombo</strong>";
    $mail->AltBody = "<style>body{font-family: 'Roboto',Arial,Helvetica,sans-serif;}</style><img src='https://m.colomboshabab.com/images/shabab_logo.png' width='100' /><p>Salaams $dbname,</p><p>Thank you for your payment for $dbpurpose.</p><p><strong>Your Receipt Number:</strong>&nbsp;&nbsp;$receipt_no<br /><strong>Amount:</strong>&nbsp;&nbsp;$dbamount</p><p>To view your receipt, please <a href='https://ziyarat.colomboshabab.com/print_receipt.php?rc=$receipt_code' target='_blank'>click here</a>.</p><p>Wassalaam,<br /><strong>Shababul Eidiz Zahabi, Colombo</strong>";

    $mail->send();

}catch (phpmailerException $e) {
    require 'common/email_logger.php';
} catch (Exception $e) {
    require 'common/email_logger.php';
}

header('Location: ' . $_SERVER["HTTP_REFERER"] );

?>