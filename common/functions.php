<?php
function createPass()
{
	$array = array('1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	$pass = "";
	for($i=0; $i<5; $i++)
		$pass = $pass.$array[(rand()%35)];
	return $pass;
}

function checkUser()
{
	// if the session id is not set, redirect to login page
	if (!isset($_SESSION['sez_user_id'])) {
		header('Location: ' . $verification_link);
		exit;
	}

	// the user want to logout
	if (isset($_GET['logout'])) {
		doLogout();
	}
}

function getActiveUser()
{
	// if the session id is not set, redirect to login page
	// if (!isset($_COOKIE['user'])) {
    //     $redirect_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     setcookie("redirect_key", $redirect_link, time()+10800, "/", "colomboshabab.com");  /* expire in 1 hour */

	// 	header("Location: https://m.colomboshabab.com/its-verification");
	// 	exit;
	// } else {
    //     return $_COOKIE['user'];
    // }
	return "30362306";
}

/*
	
*/
function doLogin()
{
	// if we found an error save the error message in this variable
	$errorMessage = '';
	
	$userName = $_POST['txtUserName'];
	$password = $_POST['txtPassword'];
	
	// first, make sure the username & password are not empty
	if ($userName == '') {
		$errorMessage = 'You must enter your username';
	} else if ($password == '') {
		$errorMessage = 'You must enter the password';
	} else {
		// check the database and see if the username and password combo do match
		$sql = "SELECT user_id
		        FROM tbl_users 
				WHERE user_name = '$userName' AND user_pwd = '$password'";
		$result = dbQuery($sql);
	
		if (dbNumRows($result) == 1) {
			$row = dbFetchAssoc($result);
			$_SESSION['sez_user_id'] = $row['user_id'];
			
			// log the time when the user last login
			$sql = "UPDATE tbl_users 
					SET user_last_login = NOW()
					WHERE user_id = '{$row['user_id']}'";
			dbQuery($sql);


			if ($catId == 0){
				header('Location: index.php');
			}
			else {
				header('Location: products&catId='.$catId);
			}
		} else {
			$errorMessage = 'Wrong username or password';
		}		
			
	}
	
	return $errorMessage;
}

/*
	Logout a user
*/
function doLogout()
{
	if (isset($_SESSION['sez_user_id'])) {
		unset($_SESSION['sez_user_id']);
		session_unregister('sez_user_id');
	}
    if (isset($_SESSION['user_id'])) {
		unset($_SESSION['user_id']);
		session_unregister('user_id');
	}
	header('Location: ' . $server_link);
	exit;
}


/*
	Generate combo box options containing the categories we have.
	if $catId is set then that category is selected
*/
function buildCategoryOptions($catId)
{
	$sql = "SELECT cat_id, cat_name
			FROM tbl_category
			ORDER BY cat_id";
	$result = dbQuery($sql) or die('Cannot get Product. ' . mysql_error());
	
	$categories = array();
	while($row = dbFetchArray($result)) {
		list($id, $name) = $row;
	
		$categories[$id] = array('id' => $id, 'name' => $name);
	}	
	
	$list = '';
	foreach ($categories as $key => $value) {
		$id   = $value['id'];
		$name = $value['name'];
		
		$list .= "<option value=\"{$id}\"";
			if ($id == $catId) {
				$list.= " selected";
			}
		$list .= ">{$name}</option>\r\n";
	}
	return $list;
}

function buildMemOptions($memId)
{
	$sql = "SELECT its_id, name
            FROM tbl_members
            ORDER BY its_id";
	$result = dbQuery($sql) or die('Cannot get Product. ' . mysql_error());
	
	$members = array();
	while($row = dbFetchArray($result)) {
		list($id, $name) = $row;
	
		$members[$id] = array('id' => $id, 'name' => $name);
	}	
	
	$list = '';
	foreach ($members as $key => $value) {
		$id   = $value['id'];
		$name = $value['name'];
		
		$list .= "<option value=\"{$id}\"";
			if ($id == $memId) {
				$list.= " selected";
			}
		$list .= ">{$name}</option>\r\n";
	}
	return $list;
}

function buildMemOptionsWithArray($memArr)
{
    $sql = "SELECT its_id, name
            FROM tbl_members
            ORDER BY its_id";
    $result = dbQuery($sql) or die('Cannot get Banner. ' . mysql_error());

    $members = array();
    while($row = dbFetchArray($result)) {
        list($id, $name) = $row;

        $members[$id] = array('id' => $id, 'name' => $id . " - " . $name);
    }

    $list = '';
    foreach ($members as $key => $value) {
        $id   = $value['id'];
        $name = $value['name'];

        $list .= "<option value=\"{$id}\"";
        $mems = $memArr;//reset the array so that it can loop the whole thing again
        foreach ($mems as &$value) {
            if ($id == $value) {
                $list.= " selected";
            }
        }
        $list .= ">{$name}</option>\r\n";
    }
    return $list;
}

function buildMohallaOptions($mohallas)
{
    $sql = "SELECT mohallaName
            FROM mohallas
            ORDER BY mohallaName";
    $result = dbQuery($sql) or die('Cannot get Banner. ' . mysql_error());
    
    //get the list of mohallas if any
    $mohalla_list = explode(',',$mohallas);
    
    $list = "";
    
    while($row = dbFetchAssoc($result)) {
        extract($row);
        if (in_array($mohallaName, $mohalla_list))
        {
          $list .= "<option value=\"{$mohallaName}\" selected>{$mohallaName}</option>";
        }
        else
        {
          $list .= "<option value=\"{$mohallaName}\">{$mohallaName}</option>";
        }
    }
    return $list;
}

/*
	Create a thumbnail of $srcFile and save it to $destFile.
	The thumbnail will be $width pixels.
	If $square is true, creates a square cropped thumbnail using cropSquareAndResize()
*/
function createThumbnail($srcFile, $destFile, $width, $quality = 75, $square = false)
{
	$thumbnail = '';
	
	if (file_exists($srcFile)  && isset($destFile))
	{
		if ($square) {
			// Use square crop and resize for passport photos
			$thumbnail = cropSquareAndResize($srcFile, $destFile, $width, $quality);
		} else {
			// Use original behavior for regular thumbnails
			$size        = getimagesize($srcFile);
			$w           = number_format($width, 0, ',', '');
			$h           = number_format(($size[1] / $size[0]) * $width, 0, ',', '');
			
			$thumbnail =  copyImage($srcFile, $destFile, $w, $h, $quality);
		}
	}
	
	// return the thumbnail file name on sucess or blank on fail
	return basename($thumbnail);
}

/*
	Copy an image to a destination file. The destination
	image size will be $w X $h pixels
	If $square is true, creates a square cropped image using cropSquareAndResize()
*/
function copyImage($srcFile, $destFile, $w, $h, $quality = 75, $square = false)
{
    if ($square) {
        // Use square crop and resize for passport photos
        return cropSquareAndResize($srcFile, $destFile, $w, $quality);
    }
    
    $tmpSrc     = pathinfo(strtolower($srcFile));
    $tmpDest    = pathinfo(strtolower($destFile));
    $size       = getimagesize($srcFile);

    if ($tmpDest['extension'] == "gif" || $tmpDest['extension'] == "jpg")
    {
       $destFile  = substr_replace($destFile, 'jpg', -3);
       $dest      = imagecreatetruecolor($w, $h);
       imageantialias($dest, TRUE);
    } elseif ($tmpDest['extension'] == "png") {
       $dest = imagecreatetruecolor($w, $h);
       imageantialias($dest, TRUE);
    } else {
      return false;
    }

    switch($size[2])
    {
       case 1:       //GIF
           $src = imagecreatefromgif($srcFile);
           break;
       case 2:       //JPEG
           $src = imagecreatefromjpeg($srcFile);
           break;
       case 3:       //PNG
           $src = imagecreatefrompng($srcFile);
           break;
       default:
           return false;
           break;
    }

    imagecopyresampled($dest, $src, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);

    switch($size[2])
    {
       case 1:
       case 2:
           imagejpeg($dest,$destFile, $quality);
           break;
       case 3:
           imagepng($dest,$destFile);
    }
    return $destFile;

}

/*
	Create the paging links
*/
function getPagingNav($sql, $pageNum, $rowsPerPage, $queryString = '')
{
	$result  = mysql_query($sql) or die('Error, query failed. ' . mysql_error());
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
	
	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);
	
	$self = $_SERVER['PHP_SELF'];
	
	// creating 'previous' and 'next' link
	// plus 'first page' and 'last page' link
	
	// print 'previous' link only if we're not
	// on page one
	if ($pageNum > 1)
	{
		$page = $pageNum - 1;
		$prev = " <a href=\"$self?page=$page{$queryString}\">[Prev]</a> ";
	
		$first = " <a href=\"$self?page=1{$queryString}\">[First Page]</a> ";
	}
	else
	{
		$prev  = ' [Prev] ';       // we're on page one, don't enable 'previous' link
		$first = ' [First Page] '; // nor 'first page' link
	}
	
	// print 'next' link only if we're not
	// on the last page
	if ($pageNum < $maxPage)
	{
		$page = $pageNum + 1;
		$next = " <a href=\"$self?page=$page{$queryString}\">[Next]</a> ";
	
		$last = " <a href=\"$self?page=$maxPage{$queryString}{$queryString}\">[Last Page]</a> ";
	}
	else
	{
		$next = ' [Next] ';      // we're on the last page, don't enable 'next' link
		$last = ' [Last Page] '; // nor 'last page' link
	}
	
	// return the page navigation link
	return $first . $prev . " Showing page <strong>$pageNum</strong> of <strong>$maxPage</strong> pages " . $next . $last; 
}

/*
	Create breadcrumbs on product detail page by traversing the categories.
*/

function createBreadCrumbProduct($group)
{
	$sql_cat = "SELECT cat_id, pd_name FROM tbl_product WHERE pd_code LIKE '$group' LIMIT 0,1";
	$result_cat = dbQuery($sql_cat);
	extract(dbFetchAssoc($result_cat));
	
	//use createBreadCrumbCategories to get the trail of categories
	$breadcrumbs = createBreadCrumbCategories($cat_id, '');
	//combine the product page crumb to $breadcrumbs
	$breadcrumbs .= " $group - $pd_name";
	
	return $breadcrumbs;
}

/*
	Create breadcrumbs on category page by traversing the categories.
*/
function createBreadCrumbCategories($catId, $breadcrumbs)
{
	if($catId == '') $catId = 0;
	while($catId != 0){
		$sql_cat = "SELECT cat_id, LCASE(cat_name) AS cat_name, p_cat_id FROM tbl_category WHERE cat_id = $catId";
		$result_cat = dbQuery($sql_cat);
		if(dbNumRows($result_cat) > 0){
			extract(dbFetchAssoc($result_cat));
			$breadcrumbs = "<li><a href='products&catid=$cat_id'>$cat_name</a></li>" . $breadcrumbs;
			$catId = $p_cat_id;
		}
	}
	
	$breadcrumbs = "<li><a href='index.php'>Home</a></li>" . $breadcrumbs;
	return $breadcrumbs;
}

function addDashes($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

function genMohallaCombo($mohalla){
    //displays only the options required. Since Mohallas will not change so frequently, let's do this manually
    //1st let's make the mohalla in "Proper" format
    $mohalla = ucwords(strtolower($mohalla));
    
    $output = '<option value=""></option>';
    
    $output.= '<option value="Badri" ';
    if($mohalla == 'Badri') $output.= 'selected';
    $output.='>Badri</option>';
    
    $output.= '<option value="Burhani" ';
    if($mohalla == 'Burhani') $output.= 'selected';
    $output.='>Burhani</option>';
    
    $output.= '<option value="Ezzi" ';
    if($mohalla == 'Ezzi') $output.= 'selected';
    $output.='>Ezzi</option>';
    
    $output.= '<option value="Fakhri" ';
    if($mohalla == 'Fakhri') $output.= 'selected';
    $output.='>Fakhri</option>';
    
    $output.= '<option value="Husaini" ';
    if($mohalla == 'Husaini') $output.= 'selected';
    $output.='>Husaini</option>';
    
    $output.= '<option value="Husami" ';
    if($mohalla == 'Husami') $output.= 'selected';
    $output.='>Husami</option>';
    
    $output.= '<option value="Jamali" ';
    if($mohalla == 'Jamali') $output.= 'selected';
    $output.='>Jamali</option>';
    
    $output.= '<option value="Mohammedi" ';
    if($mohalla == 'Mohammedi') $output.= 'selected';
    $output.='>Mohammedi</option>';
    
    $output.= '<option value="Najmi" ';
    if($mohalla == 'Najmi') $output.= 'selected';
    $output.='>Najmi</option>';
    
    $output.= '<option value="Qutbi" ';
    if($mohalla == 'Qutbi') $output.= 'selected';
    $output.='>Qutbi</option>';
    
    $output.= '<option value="Saifi" ';
    if($mohalla == 'Saifi') $output.= 'selected';
    $output.='>Saifi</option>';
    
    $output.= '<option value="Shujaee" ';
    if($mohalla == 'Shujaee') $output.= 'selected';
    $output.='>Shujaee</option>';
    
    $output.= '<option value="Taheri" ';
    if($mohalla == 'Taheri') $output.= 'selected';
    $output.='>Taheri</option>';
    
    return $output;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

function zeroblanker($num){
    if($num == 0){
        return null;
    } else return $num;
    //return $num;
}

function dateblanker($datetocheck){
    if($datetocheck == '0000-00-00'){
        return "";
    } else return $datetocheck;
}

function printdateblanker($datetocheck){
    if($datetocheck != '0000-00-00'){
        $datetocheck == '0000-00-00';
    }
    $datearr = explode("-", $datetocheck);
    if($datearr[0] == '0000') $datearr[0] = '';
    if($datearr[1] == '00') $datearr[1] = '';
    if($datearr[2] == '00') $datearr[2] = '';
    return $datearr;
}

// Passport photo processing constants
define('MAX_PASSPORT_PHOTO_SIZE', 500 * 1024); // 500KB (portrait photo)
// Larger limit for passport page scans (e.g., 2MB)
if (!defined('MAX_PASSPORT_PAGE_SIZE')) {
    define('MAX_PASSPORT_PAGE_SIZE', 2 * 1024 * 1024);
}
define('MAX_PASSPORT_PHOTO_WIDTH', 1000);
define('PASSPORT_PHOTO_QUALITY', 85);
define('PASSPORT_PHOTO_TARGET_DIMENSION', 1000); // Target dimension for passport photos

/*
    Convert any image format (PNG, GIF, etc.) to JPG format before saving
    Building on the existing copyImage() function logic
*/
function convertToJpg($srcFile, $destFile) {
    if (!file_exists($srcFile)) {
        return false;
    }
    
    $size = getimagesize($srcFile);
    if (!$size) {
        return false;
    }
    
    // Create destination file with .jpg extension
    $destFile = pathinfo($destFile, PATHINFO_DIRNAME) . '/' . pathinfo($destFile, PATHINFO_FILENAME) . '.jpg';
    
    // Use existing copyImage function to convert
    return copyImage($srcFile, $destFile, $size[0], $size[1], PASSPORT_PHOTO_QUALITY);
}

/*
    Create square cropped images for passport photos (50mm x 50mm requirement)
    with maximum file size of 500KB
*/
function cropSquareAndResize($srcFile, $destFile, $maxWidth = 1000, $quality = 75) {
    if (!file_exists($srcFile)) {
        return false;
    }
    
    $size = getimagesize($srcFile);
    if (!$size) {
        return false;
    }
    
    $srcWidth = $size[0];
    $srcHeight = $size[1];
    
    // Calculate square crop dimensions
    $cropSize = min($srcWidth, $srcHeight);
    $cropX = ($srcWidth - $cropSize) / 2;
    $cropY = ($srcHeight - $cropSize) / 2;
    
    // Calculate final dimensions using target dimension
    $finalWidth = min(PASSPORT_PHOTO_TARGET_DIMENSION, $cropSize);
    $finalHeight = $finalWidth; // Square aspect ratio
    
    // Create source image based on type
    switch($size[2]) {
        case 1: // GIF
            $src = imagecreatefromgif($srcFile);
            break;
        case 2: // JPEG
            $src = imagecreatefromjpeg($srcFile);
            break;
        case 3: // PNG
            $src = imagecreatefrompng($srcFile);
            break;
        default:
            return false;
    }
    
    if (!$src) {
        return false;
    }
    
    // Force destination file to have .jpg extension
    $pathInfo = pathinfo($destFile);
    $destFile = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.jpg';
    
    // Create destination image
    $dest = imagecreatetruecolor($finalWidth, $finalHeight);
    imageantialias($dest, TRUE);
    
    // Crop and resize
    imagecopyresampled($dest, $src, 0, 0, $cropX, $cropY, $finalWidth, $finalHeight, $cropSize, $cropSize);
    
    // Iteratively reduce quality until file size is <= MAX_PASSPORT_PHOTO_SIZE
    $currentQuality = $quality;
    $minQuality = 40; // Lower bound for quality
    
    do {
        // Save as JPEG with current quality
        $result = imagejpeg($dest, $destFile, $currentQuality);
        
        if (!$result) {
            break;
        }
        
        // Check file size
        $fileSize = filesize($destFile);
        
        if ($fileSize <= MAX_PASSPORT_PHOTO_SIZE) {
            break; // File size is acceptable
        }
        
        // Reduce quality for next iteration
        $currentQuality -= 5;
        
    } while ($currentQuality >= $minQuality && $fileSize > MAX_PASSPORT_PHOTO_SIZE);
    
    // Clean up
    imagedestroy($src);
    imagedestroy($dest);
    
    return $result ? $destFile : false;
}

/*
    Set EXIF/DPI metadata for passport photos
    Attempts to preserve metadata using Imagick if available, otherwise documents constraints
*/
function setPassportPhotoMetadata($imagePath) {
    if (!file_exists($imagePath)) {
        return false;
    }
    
    // Try to use Imagick if available for better metadata handling
    if (extension_loaded('imagick')) {
        try {
            $imagick = new Imagick($imagePath);
            
            // Set DPI to 300 (standard for passport photos)
            $imagick->setImageResolution(300, 300);
            
            // Set EXIF data for passport photo requirements
            $imagick->setImageProperty('exif:ImageDescription', 'Passport Photo - 50mm x 50mm');
            $imagick->setImageProperty('exif:Software', 'Ziyarat Registration System');
            
            // Save with metadata
            $imagick->writeImage($imagePath);
            $imagick->destroy();
            
            return true;
        } catch (Exception $e) {
            error_log('Imagick metadata setting failed: ' . $e->getMessage());
            // Fall through to GD documentation
        }
    }
    
    // GD doesn't support EXIF metadata writing, document the constraint
    error_log('EXIF/DPI metadata preservation: GD library used - metadata cannot be preserved. ' .
              'For full metadata support, install Imagick extension. ' .
              'Current behavior: 50mm equivalence based on pixel dimensions (' . 
              PASSPORT_PHOTO_TARGET_DIMENSION . 'x' . PASSPORT_PHOTO_TARGET_DIMENSION . ' pixels)');
    
    return true; // Return true as the image is still valid, just without metadata
}

/*
    Helper function that calls the Google Vision API service to check image quality
    and return user-friendly error messages
*/
function validatePassportImageQuality($imagePath) {
    if (!file_exists($imagePath)) {
        return ['error' => 'Image file not found'];
    }
    
    // Include the OCR service directly to avoid HTTP calls
    require_once __DIR__ . '/../passport_ocr_service.php';
    
    // Call the validateImageQuality function directly
    return validateImageQuality($imagePath);
}

/*
    Passport Photo Processing Functions
    Specialized functions for handling 50mm x 50mm passport photos
*/

// Constants for passport photo processing
define('PASSPORT_PHOTO_MIN_DIMENSION', PASSPORT_PHOTO_TARGET_DIMENSION); // Use target dimension as minimum
define('PASSPORT_PHOTO_CROP_RATIO', 1); // Square aspect ratio
define('PASSPORT_PHOTO_TEMP_DIR', SRV_ROOT . 'images/temp_passport/');
define('PASSPORT_PHOTO_MAX_SIZE', 500 * 1024); // 500KB max file size

/*
    Main function to process uploaded passport photos with cropping, format conversion, and compression
*/
function processPassportPhoto($srcFile, $destFile) {
    if (!file_exists($srcFile)) {
        return ['success' => false, 'error' => 'Source file not found'];
    }
    
    // Create temp directory if it doesn't exist
    if (!file_exists(PASSPORT_PHOTO_TEMP_DIR)) {
        mkdir(PASSPORT_PHOTO_TEMP_DIR, 0755, true);
    }
    
    // Validate source image
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        return ['success' => false, 'error' => 'Invalid image file'];
    }
    
    // Load source image
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        return ['success' => false, 'error' => 'Could not load image'];
    }
    
    // Crop to square and resize to passport photo dimensions
    $croppedFile = cropSquareAndResizePassportPhoto($src, $srcFile);
    if (!$croppedFile) {
        imagedestroy($src);
        return ['success' => false, 'error' => 'Failed to crop and resize image'];
    }
    
    // Convert to JPG format
    $jpgFile = convertToJpgPassportPhoto($croppedFile);
    if (!$jpgFile) {
        imagedestroy($src);
        unlink($croppedFile);
        return ['success' => false, 'error' => 'Failed to convert to JPG'];
    }
    
    // Compress to meet file size requirements
    $finalFile = compressPassportPhoto($jpgFile, $destFile);
    if (!$finalFile) {
        imagedestroy($src);
        unlink($croppedFile);
        unlink($jpgFile);
        return ['success' => false, 'error' => 'Failed to compress image'];
    }
    
    // Clean up temporary files
    imagedestroy($src);
    unlink($croppedFile);
    unlink($jpgFile);
    
    // Validate final dimensions
    $finalDimensions = validatePassportPhotoDimensions($finalFile);
    if (!$finalDimensions['valid']) {
        unlink($finalFile);
        return ['success' => false, 'error' => $finalDimensions['error']];
    }
    
    // Set EXIF/DPI metadata
    setPassportPhotoMetadata($finalFile);
    
    return ['success' => true, 'file' => $finalFile, 'dimensions' => $finalDimensions];
}

/*
    Crop image to square aspect ratio and resize to passport photo dimensions
*/
function cropSquareAndResizePassportPhoto($src, $srcFile) {
    $srcWidth = imagesx($src);
    $srcHeight = imagesy($src);
    
    // Determine crop size (smaller dimension)
    $cropSize = min($srcWidth, $srcHeight);
    
    // Calculate crop position (center the crop)
    $cropX = ($srcWidth - $cropSize) / 2;
    $cropY = ($srcHeight - $cropSize) / 2;
    
    // Create cropped image
    $cropped = imagecreatetruecolor($cropSize, $cropSize);
    imagecopy($cropped, $src, 0, 0, $cropX, $cropY, $cropSize, $cropSize);
    
    // Resize to passport photo dimensions
    $resized = imagecreatetruecolor(PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION);
    imagecopyresampled($resized, $cropped, 0, 0, 0, 0, 
                      PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION, 
                      $cropSize, $cropSize);
    
    // Save to temporary file
    $tempFile = PASSPORT_PHOTO_TEMP_DIR . 'cropped_' . md5(time() . rand()) . '.png';
    imagepng($resized, $tempFile);
    
    // Clean up
    imagedestroy($cropped);
    imagedestroy($resized);
    
    return $tempFile;
}

/*
    Convert image to JPG format for passport photos
*/
function convertToJpgPassportPhoto($srcFile) {
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        return false;
    }
    
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        return false;
    }
    
    // Create JPG file
    $jpgFile = PASSPORT_PHOTO_TEMP_DIR . 'converted_' . md5(time() . rand()) . '.jpg';
    
    // Create white background for transparent images
    $jpg = imagecreatetruecolor(PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION);
    $white = imagecolorallocate($jpg, 255, 255, 255);
    imagefill($jpg, 0, 0, $white);
    
    // Copy source image to JPG
    imagecopy($jpg, $src, 0, 0, 0, 0, PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION);
    
    // Save as JPG with high quality
    $result = imagejpeg($jpg, $jpgFile, 95);
    
    // Clean up
    imagedestroy($src);
    imagedestroy($jpg);
    
    return $result ? $jpgFile : false;
}

/*
    Compress passport photo to meet file size requirements
*/
function compressPassportPhoto($srcFile, $destFile) {
    $currentQuality = 90;
    $minQuality = 60;
    
    do {
        // Load source image
        $src = imagecreatefromjpeg($srcFile);
        if (!$src) {
            return false;
        }
        
        // Create destination image
        $dest = imagecreatetruecolor(PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION);
        
        // Copy with current quality
        imagecopyresampled($dest, $src, 0, 0, 0, 0, 
                          PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION, 
                          PASSPORT_PHOTO_MIN_DIMENSION, PASSPORT_PHOTO_MIN_DIMENSION);
        
        // Save with current quality
        $result = imagejpeg($dest, $destFile, $currentQuality);
        
        if (!$result) {
            imagedestroy($src);
            imagedestroy($dest);
            return false;
        }
        
        // Check file size
        $fileSize = filesize($destFile);
        
        if ($fileSize <= PASSPORT_PHOTO_MAX_SIZE) {
            imagedestroy($src);
            imagedestroy($dest);
            break; // File size is acceptable
        }
        
        // Reduce quality for next iteration
        $currentQuality -= 5;
        
        // Clean up for next iteration
        imagedestroy($src);
        imagedestroy($dest);
        
    } while ($currentQuality >= $minQuality && $fileSize > PASSPORT_PHOTO_MAX_SIZE);
    
    return $result ? $destFile : false;
}

/*
    Validate that the final photo meets 50mm x 50mm requirements
*/
function validatePassportPhotoDimensions($imagePath) {
    if (!file_exists($imagePath)) {
        return ['valid' => false, 'error' => 'Image file not found'];
    }
    
    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) {
        return ['valid' => false, 'error' => 'Invalid image file'];
    }
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    // Check dimensions (should be square and meet minimum size)
    if ($width !== $height) {
        return ['valid' => false, 'error' => 'Image must be square'];
    }
    
    if ($width < PASSPORT_PHOTO_MIN_DIMENSION) {
        return ['valid' => false, 'error' => 'Image too small. Minimum size: ' . PASSPORT_PHOTO_MIN_DIMENSION . 'x' . PASSPORT_PHOTO_MIN_DIMENSION . ' pixels'];
    }
    
    // Check file size
    $fileSize = filesize($imagePath);
    if ($fileSize > PASSPORT_PHOTO_MAX_SIZE) {
        return ['valid' => false, 'error' => 'File too large. Maximum size: ' . formatBytes(PASSPORT_PHOTO_MAX_SIZE)];
    }
    
    return [
        'valid' => true, 
        'width' => $width, 
        'height' => $height, 
        'file_size' => $fileSize,
        'file_size_formatted' => formatBytes($fileSize)
    ];
}

/*
    Generate standardized filename for passport photos
*/
function generatePassportPhotoFilename($kunId) {
    return 'passport_photo_' . $kunId . '_' . md5(time() . rand()) . '.jpg';
}

/*
    Clean up temporary files created during cropping process
*/
function cleanupTempPassportFiles($tempDir = null) {
    if (!$tempDir) {
        $tempDir = PASSPORT_PHOTO_TEMP_DIR;
    }
    
    if (!is_dir($tempDir)) {
        return true;
    }
    
    $files = glob($tempDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    return true;
}

/*
    Prepare crop data for passport photo processing
*/
function prepareCropData($cropX, $cropY, $cropWidth, $cropHeight, $originalWidth, $originalHeight) {
    // Validate crop coordinates
    if ($cropX < 0 || $cropY < 0 || $cropWidth <= 0 || $cropHeight <= 0) {
        return false;
    }
    
    if ($cropX + $cropWidth > $originalWidth || $cropY + $cropHeight > $originalHeight) {
        return false;
    }
    
    // Ensure square aspect ratio
    $size = min($cropWidth, $cropHeight);
    
    return [
        'x' => $cropX,
        'y' => $cropY,
        'width' => $size,
        'height' => $size
    ];
}

/*
    Apply crop coordinates to source image
*/
function applyCropToImage($srcFile, $cropData, $destFile) {
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        return false;
    }
    
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        return false;
    }
    
    // Create cropped image
    $cropped = imagecreatetruecolor($cropData['width'], $cropData['height']);
    imagecopy($cropped, $src, 0, 0, $cropData['x'], $cropData['y'], 
              $cropData['width'], $cropData['height']);
    
    // Save cropped image
    $result = imagepng($cropped, $destFile);
    
    // Clean up
    imagedestroy($src);
    imagedestroy($cropped);
    
    return $result ? $destFile : false;
}

/*
    Validate crop area meets minimum size requirements
*/
function validateCropArea($cropData, $minSize = 100) {
    if (!$cropData || !isset($cropData['width']) || !isset($cropData['height'])) {
        return false;
    }
    
    return $cropData['width'] >= $minSize && $cropData['height'] >= $minSize;
}

/*
    Create user-specific directory structure for passport photos
*/
function createPassportPhotoDirectory($kunId) {
    $baseDir = SRV_ROOT . 'images/uploads/';
    $userDir = $baseDir . 'passport_photos_' . $kunId . '/';
    
    if (!is_dir($userDir)) {
        return mkdir($userDir, 0755, true) ? $userDir : false;
    }
    
    return $userDir;
}

/*
    Generate full path for passport photo storage
*/
function getPassportPhotoPath($kunId, $filename) {
    $baseDir = SRV_ROOT . 'images/uploads/';
    return $baseDir . $filename;
}

/*
    Create backup of original uploaded file
*/
function backupOriginalPassportPhoto($srcFile, $kunId) {
    $backupDir = SRV_ROOT . 'images/backups/passport_photos/';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $backupFile = $backupDir . 'original_' . $kunId . '_' . md5(time() . rand()) . '.jpg';
    
    return copy($srcFile, $backupFile) ? $backupFile : false;
}

/*
    Helper function to load image from file based on type
*/
function loadImageFromFile($filePath, $imageType) {
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($filePath);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($filePath);
        case IMAGETYPE_GIF:
            return imagecreatefromgif($filePath);
        case IMAGETYPE_WEBP:
            return imagecreatefromwebp($filePath);
        default:
            return false;
    }
}

/*
    Format bytes to human readable format
*/
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/*
    Shared helpers for passport page uploads (DRY across handlers)
*/
function passportPageUploadFile($file, $uploadDir) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $result['error'] = 'Invalid file upload';
        return $result;
    }

    $maxSize = defined('MAX_PASSPORT_PAGE_SIZE') ? MAX_PASSPORT_PAGE_SIZE : MAX_PASSPORT_PHOTO_SIZE;
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds maximum allowed size of ' . formatBytes($maxSize);
        return $result;
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $result['error'] = 'Invalid image file';
        return $result;
    }

    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $md5Hash = md5_file($file['tmp_name']);
    $filename = $md5Hash . '.' . strtolower($fileExtension);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetPath = $uploadDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Failed to move uploaded file';
    }

    return $result;
}

function processPassportPageImage($filePath, $filename) {
    $result = [
        'success' => false,
        'processed_filename' => null,
        'error' => null
    ];

    try {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $jpgDest = SRV_ROOT . 'images/uploads/' . $base . '.jpg';

        $converted = convertToJpg($filePath, $jpgDest);
        if (!$converted) {
            $result['error'] = 'Failed to convert image to JPG';
            return $result;
        }

        $finalPath = $jpgDest;

        if (realpath($filePath) !== realpath($finalPath) && file_exists($filePath)) {
            @unlink($filePath);
        }

        $result['success'] = true;
        $result['processed_filename'] = basename($finalPath);

    } catch (Exception $e) {
        $result['error'] = 'Image processing error: ' . $e->getMessage();
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    return $result;
}

function runPassportOcrCommon($filePath) {
    $result = [
        'success' => false,
        'data' => [],
        'error' => null
    ];

    try {
        require_once __DIR__ . '/../passport_ocr_service.php';
        $ocrResult = extractPassportData($filePath);

        if (is_array($ocrResult) && isset($ocrResult['success']) && $ocrResult['success']) {
            $result['success'] = true;
            $data = $ocrResult['data'] ?? [];
            $result['data'] = [
                'kun_ppno' => $data['kun_passport_no'] ?? '',
                'kun_ppexpiry' => $data['kun_passport_expiry'] ?? '',
                'kun_fname' => $data['kun_fname'] ?? '',
                'kun_lname' => $data['kun_lname'] ?? '',
                'kun_nationality' => $data['kun_nationality'] ?? '',
                'kun_dob' => $data['kun_dob'] ?? '',
                'kun_gender' => $data['kun_gender'] ?? ''
            ];
        } else {
            $result['error'] = $ocrResult['error'] ?? 'OCR extraction failed';
        }

    } catch (Exception $e) {
        $result['error'] = 'OCR processing error: ' . $e->getMessage();
    }

    return $result;
}

function insertKunPhotoRecord($kunId, $filename, $photoType, $ocrData = []) {
    global $dbConn;
    $result = [
        'success' => false,
        'photo_id' => null,
        'error' => null
    ];

    try {
        $escapedKunId = mysqli_real_escape_string($dbConn, $kunId);
        $escapedFilename = mysqli_real_escape_string($dbConn, $filename);
        $escapedPhotoType = mysqli_real_escape_string($dbConn, $photoType);
        $escapedUserId = mysqli_real_escape_string($dbConn, getActiveUser());

        $sql = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_type, photo_upload_time, photo_user_id) ".
               "VALUES ('$escapedKunId', '$escapedFilename', '$escapedPhotoType', NOW(), '$escapedUserId')";

        $queryResult = dbQuery($sql);

        if ($queryResult) {
            $result['success'] = true;
            $result['photo_id'] = dbInsertId();
            $result['ocr_data'] = $ocrData;
        } else {
            $result['error'] = 'Database insertion failed: ' . mysqli_error($dbConn);
        }

    } catch (Exception $e) {
        $result['error'] = 'Database error: ' . $e->getMessage();
    }

    return $result;
}

/*
    Unified passport photo validation function
    Used by both upload-kun-photos.php and update-kun-registration.php
*/
function validatePassportPhotoUpload($file) {
    // Check file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'File size must be less than 5MB'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['valid' => false, 'error' => 'Invalid file type. Please upload JPG, PNG, or GIF images only'];
    }
    
    // Check if it's a valid image
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        return ['valid' => false, 'error' => 'Invalid image file'];
    }
    
    // Check minimum dimensions
    $minDimension = PASSPORT_PHOTO_MIN_DIMENSION; // Use the constant from Comment 8
    if ($imageInfo[0] < $minDimension || $imageInfo[1] < $minDimension) {
        return ['valid' => false, 'error' => 'Image too small. Minimum size: ' . $minDimension . 'x' . $minDimension . ' pixels'];
    }
    
    return ['valid' => true];
}

/*
    Unified passport photo database storage function
    Used by both upload-kun-photos.php and update-kun-registration.php
*/
function storePassportPhoto($kunregid, $filename, $user_id) {
    try {
        // Check if passport photo already exists for this registration
        $checkSql = "SELECT photo_id FROM zyt_kun_photos WHERE kun_id = $kunregid AND photo_type = 'passport_photo'";
        $existingPhoto = dbFetchAssoc(dbQuery($checkSql));
        
        if ($existingPhoto) {
            // Update existing record
            $sql = "UPDATE zyt_kun_photos SET 
                    photo_filename = '$filename', 
                    photo_upload_time = NOW(), 
                    photo_user_id = $user_id 
                    WHERE kun_id = $kunregid AND photo_type = 'passport_photo'";
        } else {
            // Insert new record
            $sql = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_filename_thumb, photo_upload_time, photo_user_id, photo_type) 
                    VALUES ($kunregid, '$filename', '', NOW(), $user_id, 'passport_photo')";
        }
        
        $result = dbQuery($sql);
        
        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to store photo information in database'];
        }
        
    } catch (Exception $e) {
        error_log('Database error storing passport photo: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Database error occurred'];
    }
}