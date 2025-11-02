<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';

$receipt = (isset($_GET['receipt']) && $_GET['receipt'] != '') ? $_GET['receipt'] : '0';
$rc = (isset($_GET['rc']) && $_GET['rc'] != '') ? $_GET['rc'] : '0';
if($receipt == '0' && $rc == '0') {echo $rc;}
else{
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <style>
                .page{
                    width: 8.3in; height: 5.63in;
                    margin: 2px auto 2px auto;
                    font-size: 15px;
                    padding: 10px;
                    padding-bottom:0;
                    overflow:visible;
                }
                .page-break{display: block; page-break-before: always; }

                label{font-weight:bold; display:inline-block; width:150px;}
                .description{display:inline-block;}

                h4{margin:0;padding:0;}

                table, td, th{
                    padding:3px;
                    background:#fff;
                    border: 1px solid #000;
                    vertical-align: middle;
                    height: 20px;
                }

                .item_title{
                    font-weight:bold;
                    height:15px;
                }

                table { page-break-inside:auto; background: #fff; border-collapse:collapse;}
                tr    { page-break-inside:avoid; page-break-after:auto }
                th  {background: #eee; height:30px;}
                thead { display:table-header-group }
                tfoot { display:table-footer-group }

                #footer{
                    position: absolute;
                    bottom: 0;
                    width: 8.3in;
                    margin-top: 30px;
                    text-align: center;
                }

                .lastbox{
                    margin-right:0;

                }
                .other{
                    margin-top:30px; width:100%; margin-bottom: 10px;
                }
                .other td{
                    padding:10px;
                    height: 20px;
                }

            </style>
        </head>
        <body>
            <div class="page">
                <img src="https://m.colomboshabab.com/images/shabab_logo.png" width="120px" style="float:left; margin-right: 20px;" />
                <div style="margin-top: 30px; line-height: 15px;">
                    <h1>Shababul Eidiz Zahabi</h1>
                    <h3>No. 41, Glen Aber Place, Colombo 4, Sri Lanka</h3>
                    <h4><em>colomboshabab.com</em></h4>
                </div>
                <div style="clear:both; margin-bottom: 40px"></div>
                <?php

    if($receipt != '0'){
        $sql_fetch_receipt = "SELECT * FROM zyt_debtor_control dc
                        WHERE receipt_no = $receipt";
    } elseif($rc != '0') {
        $sql_fetch_receipt = "SELECT * FROM zyt_debtor_control dc
                        WHERE receipt_code = '$rc'";
    }


    $result_fetch_receipt = dbQuery($sql_fetch_receipt);

    if(dbNumRows($result_fetch_receipt) > 0){
        extract(dbFetchAssoc($result_fetch_receipt));
        $date = strtotime($receipt_date);
        $print_date = date("d/m/Y", $date);
        ?>
                <div>
                    <div style="float:right"><h4>Receipt No: <span style="font-size: 20px; text-decoration:underline;">Z-<?php echo $receipt_no; ?></span></h4></div>
                    <div><strong>Date:</strong> <?php echo $print_date; ?></div>
                </div>
                <div style="clear:both"></div>
                <table class="other">
                    <tr>
                        <th><strong><em>Received from:</em></strong></th>
                        <td><?php echo "$db_name"; ?></td>
                    </tr>
                    <tr>
                        <th><strong><em>On account of:</em></strong></th>
                        <td><?php echo "$db_purpose"; ?></td>
                    </tr>
                    <tr>
                        <th><strong><em>Sum Of:</em></strong></th>
                        <td><?php echo "Rs. $db_amount"; ?></td>
                    </tr>
                </table>
        <?php
    }
        ?>
                <div style="text-align:center"><p>This is a computer generated invoice and does not require a signature.</p></div>
            </div>
            <?php if(isset($_GET['p'])){
                ?>
                <script>window.print();</script>
                <?php
            }
            ?>
        </body>
    </html>
<?php
}
?>