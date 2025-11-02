<?php
require_once 'common/config.php';
require_once 'common/functions.php';
date_default_timezone_set('Asia/Colombo');

// require_once '../vendor/autoload.php';

// use Google\Cloud\Translate\TranslateClient;

// putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/google-credentials.json'); // Set credentials file

// function translateText($text, $targetLanguage = 'ar') {
//     $translate = new TranslateClient([
//         'keyFilePath' => __DIR__ . '/google-credentials.json' // Ensure correct path
//     ]);
//     $result = $translate->translate($text, ['target' => $targetLanguage]);
//     return $result['text'];
// }

$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

$sql_safar = "SELECT * FROM zyt_safar WHERE safar_id = $safarid";
$result_safar = dbQuery($sql_safar);
extract(dbFetchAssoc($result_safar));

//create the title row as an array to include in excel file
$title_row = array(
    "ITS No", "Passport Number", "Passport First Name", "Passport Middle Name", "Passport Last Name",
    "Profession", "Passport Type", "Place of Issue", "Passport Issues Date (dd-mm-yyyy)", "Nationality",
    "Passport Expiry Date (dd-mm-yyyy)", "Date of Birth (dd-mm-yyyy)", "Birth Place", "Accommodation",
    "Passport Arabic Name", "Father Name English", "Grand Father Name English", "First Name Arabic",
    "Middle Name Arabic", "Father Name Arabic", "Grandfather Name Arabic", "Surname Arabic",
    "Mother Name Arabic", "Mother's Father Name Arabic", "Mother Name English", "Mother's Father Name English", "Family"
);

//get the details to form the filename
$file_creation_time = date('m-d-Y_hia');
$file_name = "$safar_name - Submission " . $file_creation_time;

//get the sql
$sql_export = "SELECT 
    kun_its, kun_ppno, kun_fname, kun_mname, kun_lname, kun_profession, kun_passport_type, 
    kun_placeofissue, DATE_FORMAT(kun_ppissue,'%d-%m-%Y') AS kun_ppissue, kun_nationality,
    DATE_FORMAT(kun_ppexpiry,'%d-%m-%Y') AS kun_ppexpiry, DATE_FORMAT(kun_dob,'%d-%m-%Y') AS kun_dob, 
    kun_birthplace, kun_accommodation_required, kun_passport_arabic_name, kun_father_name, 
    kun_grandfather_name, kun_fname_arabic, kun_mname_arabic, kun_father_name_arabic, 
    kun_grandfather_name_arabic, kun_lname_arabic, kun_mother_name_arabic, kun_mother_father_name_arabic, 
    kun_mother_name, kun_mother_father_name, kun_group
FROM zyt_kun_registrations
WHERE safar_id = $safarid AND kun_status <> 'cancelled'
ORDER BY zyt_kun_priority";

$result_export = dbQuery($sql_export);

$data = array();

$row_count = 1;
while($row = dbFetchAssoc($result_export)){
    // Format text fields to proper case
    $row['kun_fname'] = ucwords(strtolower($row['kun_fname']));
    $row['kun_mname'] = ucwords(strtolower($row['kun_mname']));
    $row['kun_lname'] = ucwords(strtolower($row['kun_lname']));
    $row['kun_ppno'] = ucwords(strtolower($row['kun_ppno']));
    $row['kun_placeofissue'] = ucwords(strtolower($row['kun_placeofissue']));
    $row['kun_nationality'] = ucwords(strtolower($row['kun_nationality']));
    $row['kun_birthplace'] = ucwords(strtolower($row['kun_birthplace']));
    $row['kun_father_name'] = ucwords(strtolower($row['kun_father_name']));
    $row['kun_mother_name'] = ucwords(strtolower($row['kun_mother_name']));
    $row['kun_mother_father_name'] = ucwords(strtolower($row['kun_mother_father_name']));
    $row['kun_profession'] = ucwords(strtolower($row['kun_profession']));
    
    // Handle NULL values for better Excel display
    foreach($row as $key => $value) {
        if($value === null || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            $row[$key] = '';
        }
    }
    
    $data[] = $row;
    $row_count++;
}

// print_r($data);

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

$sheet->getStyle("A1:AA1")->getFont()->setBold(true);
$sheet->getStyle("I2:I300")->getNumberFormat()->setFormatCode('dd-mm-yyyy');
$sheet->getStyle("K2:K300")->getNumberFormat()->setFormatCode('dd-mm-yyyy');
$sheet->getStyle("L2:L300")->getNumberFormat()->setFormatCode('dd-mm-yyyy');

// Rename worksheet (Excel has a 31 character limit for sheet names)
$sheet_title = substr($safar_name, 0, 31);
$sheet->setTitle($sheet_title);

$sheet->getDefaultRowDimension()->setRowHeight(-1);

// Set active sheet index to the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Excel2007)
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

// Add column width adjustments for better readability
$sheet->getColumnDimension('A')->setWidth(15); // ITS No
$sheet->getColumnDimension('B')->setWidth(20); // Passport Number
$sheet->getColumnDimension('C')->setWidth(20); // Passport First Name
$sheet->getColumnDimension('D')->setWidth(20); // Passport Middle Name
$sheet->getColumnDimension('E')->setWidth(20); // Passport Last Name
$sheet->getColumnDimension('F')->setWidth(20); // Profession
$sheet->getColumnDimension('G')->setWidth(15); // Passport Type
$sheet->getColumnDimension('H')->setWidth(20); // Place of Issue
$sheet->getColumnDimension('I')->setWidth(20); // Passport Issues Date
$sheet->getColumnDimension('J')->setWidth(20); // Nationality
$sheet->getColumnDimension('K')->setWidth(20); // Passport Expiry Date
$sheet->getColumnDimension('L')->setWidth(20); // Date of Birth
$sheet->getColumnDimension('M')->setWidth(20); // Birth Place
$sheet->getColumnDimension('N')->setWidth(15); // Accommodation
$sheet->getColumnDimension('O')->setWidth(40); // Passport Arabic Name
$sheet->getColumnDimension('P')->setWidth(20); // Father Name English
$sheet->getColumnDimension('Q')->setWidth(20); // Grand Father Name English
$sheet->getColumnDimension('R')->setWidth(20); // First Name Arabic
$sheet->getColumnDimension('S')->setWidth(20); // Middle Name Arabic
$sheet->getColumnDimension('T')->setWidth(20); // Father Name Arabic
$sheet->getColumnDimension('U')->setWidth(20); // Grandfather Name Arabic
$sheet->getColumnDimension('V')->setWidth(20); // Surname Arabic
$sheet->getColumnDimension('W')->setWidth(20); // Mother Name Arabic
$sheet->getColumnDimension('X')->setWidth(20); // Mother's Father Name Arabic
$sheet->getColumnDimension('Y')->setWidth(20); // Mother Name English
$sheet->getColumnDimension('Z')->setWidth(20); // Mother's Father Name English
$sheet->getColumnDimension('AA')->setWidth(30); // Family

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

?>
