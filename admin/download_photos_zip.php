<?php
require_once 'common/config.php';
require_once 'common/functions.php';
date_default_timezone_set('Asia/Colombo');

$safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0';

$sql_safar = "SELECT * FROM zyt_safar WHERE safar_id = $safarid";
$result_safar = dbQuery($sql_safar);
extract(dbFetchAssoc($result_safar));

//create a directory using safarid
$adminthisFile = str_replace('\\', '/', __FILE__);
$adminsrvRoot  = str_replace('admin/download_photos_zip.php', '', $adminthisFile);

$dir = $adminsrvRoot . 'images/uploads/safar-'.$safarid . '/';

// Remove existing directory if it exists
if (is_dir($dir)) {
    // Remove all files and subdirectories recursively
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    
    // Remove the directory itself
    rmdir($dir);
}

// Create the directory
if (!mkdir($dir, 0777, true)) {
    die('Failed to create directories...');
} else {
    //let's copy all the files into this and rename them according to the order
    //get the list of applications for this safar
    $sql_apps = "SELECT kun_id, zyt_kun_priority FROM zyt_kun_registrations WHERE safar_id = $safarid AND kun_status NOT LIKE 'cancelled'";
    $result_apps = dbQuery($sql_apps);

    while($row_app = dbFetchAssoc($result_apps)){
        extract($row_app);
        $imgcounter = 0;
        //let's get the files
        $sql_files = "SELECT photo_filename FROM zyt_kun_photos WHERE kun_id = $kun_id";
        $result_files = dbQuery($sql_files);
        while($row_file = dbFetchAssoc($result_files)){
            extract($row_file);
            //copy the file to the new folder
            $source = $adminsrvRoot . 'images/uploads/' . $photo_filename;

            //get the extension for file renaming
            $ext = substr(strrchr($photo_filename, "."), 1);

            $imgcounter_print = "";
            if($imgcounter != 0){
                $imgcounter_print = "-$imgcounter";
            }
            
            //we have to rename it to something
            $destination = $dir . $zyt_kun_priority . $imgcounter_print. "." . $ext;

            if( !copy($source, $destination) ) { 
                echo "File can't be copied! \n"; 
                exit();
            }
            $imgcounter++;
        }
    }
}

//let's now create the zip file and download it...
$zip = new ZipArchive();
$filename = $dir . "safar-$safarid.zip";

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

// Create zip
createZip($zip,$dir,$dir);

$zip->close();

$download_filename = "safar-$safarid.zip";

if (file_exists($filename)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="'.basename($download_filename).'"');
    header('Content-Length: ' . filesize($filename));

    flush();
    readfile($filename);
    // delete file
    //unlink($filename);

}


function createZip($zip,$dir,$basePath){
    if (is_dir($dir)){
  
      if ($dh = opendir($dir)){
         while (($file = readdir($dh)) !== false){
   
           // If file
           if (is_file($dir.$file)) {
              if($file != '' && $file != '.' && $file != '..'){
                 // Calculate relative path for zip entry
                 $relativePath = str_replace($basePath, '', $dir.$file);
                 $zip->addFile($dir.$file, $relativePath);
              }
           }else{
              // If directory
              if(is_dir($dir.$file) ){
  
                if($file != '' && $file != '.' && $file != '..'){
  
                  // Calculate relative path for zip entry
                  $relativePath = str_replace($basePath, '', $dir.$file);
                  // Add empty directory
                  $zip->addEmptyDir($relativePath);
  
                  $folder = $dir.$file.'/';
   
                  // Read data of the folder
                  createZip($zip,$folder,$basePath);
                }
              }
   
           }
   
         }
         closedir($dh);
       }
    }
  }