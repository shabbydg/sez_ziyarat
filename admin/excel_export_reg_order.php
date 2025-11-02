<?php
require_once 'common/config.php';
require_once 'common/functions.php';
date_default_timezone_set('Asia/Colombo');

$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

$sql_safar = "SELECT * FROM zyt_safar WHERE safar_id = $safarid";
$result_safar = dbQuery($sql_safar);
extract(dbFetchAssoc($result_safar));

//create the title row as an array to include in excel file
$title_row = array("#","ITS","Name","Phone","Email","Status","PP No","PP Name","PP Surname","Issue Date","Expiry Date","DOB","Gender","Place of Issue","Country of Issue","Country of Birth");

//get the details to form the filename
$file_creation_time = date('m-d-Y_hia');

$file_name = "$safar_name - " . $file_creation_time;

//get the sql
$sql_export = "SELECT kun_its, kun_itsname, kun_contact, kun_email, kun_status, kun_ppno, CONCAT(kun_fname, ' ', kun_mname) AS name, kun_lname, kun_ppissue, kun_ppexpiry, kun_dob, kun_gender, kun_placeofissue, kun_countryofissue, kun_birthcountry, kun_group FROM zyt_kun_registrations
                WHERE safar_id = $safarid
                ORDER BY kun_id";

$result_export = dbQuery($sql_export);

$data = array();

$row_count = 1;
while($row = dbFetchAssoc($result_export)){
    $row = array('row_count' => $row_count) + $row;
    $data[] = $row;
    $row_count++;
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

/** Include PhpSpreadsheet */
$autoload_paths = [
    '../vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php'
];

$autoload_loaded = false;
foreach($autoload_paths as $path) {
    if(file_exists($path)) {
        require_once $path;
        $autoload_loaded = true;
        break;
    }
}

if(!$autoload_loaded) {
    die('Error: Could not find vendor/autoload.php. Please ensure Composer dependencies are installed.');
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator("SEZ Colombo")
    ->setLastModifiedBy("SEZ Colombo")
    ->setTitle("$file_name")
    ->setCategory("Exported File");

// Add some data
$sheet = $spreadsheet->getActiveSheet();

$sheet->fromArray($title_row, NULL, 'A1');
$sheet->fromArray($data, NULL, 'A2');

//Create style arrays to apply to cells
$centerStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
];

$courierStyle = [
    'font'  => [
        'name'  => 'Courier New'
    ]
];

$sheet->getStyle("A1:O1")->getFont()->setBold(true);

// Rename worksheet (Excel has a 31 character limit for sheet names)
$sheet_title = substr($safar_name, 0, 31);
$sheet->setTitle($sheet_title);

$sheet->getDefaultRowDimension()->setRowHeight(-1);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.date('D, d M Y H:i:s')); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

?>
