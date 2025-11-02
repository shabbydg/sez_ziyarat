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
		header('Location: ' . $server_link);
		exit;
	}

	// the user want to logout
	if (isset($_GET['logout'])) {
		doLogout();
	}
}

function getActiveUser()
{
	    /*if (!isset($_COOKIE['user'])) {
        $redirect_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        setcookie("redirect_key", $redirect_link, time()+10800, "/", "colomboshabab.com"); 

		header("Location: https://m.colomboshabab.com/its-verification");
		exit;
	} else {
        return $_COOKIE['user'];
    }*/
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
*/
function createThumbnail($srcFile, $destFile, $width, $quality = 75)
{
	$thumbnail = '';
	
	if (file_exists($srcFile)  && isset($destFile))
	{
		$size        = getimagesize($srcFile);
		$w           = number_format($width, 0, ',', '');
		$h           = number_format(($size[1] / $size[0]) * $width, 0, ',', '');
		
		$thumbnail =  copyImage($srcFile, $destFile, $w, $h, $quality);
	}
	
	// return the thumbnail file name on sucess or blank on fail
	return basename($thumbnail);
}

/*
	Copy an image to a destination file. The destination
	image size will be $w X $h pixels
*/
function copyImage($srcFile, $destFile, $w, $h, $quality = 75)
{
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
    /*if($num == 0){
        return null;
    } else return $num;*/
    return $num;
}

function dateblanker($datetocheck){
    if($datetocheck == '0000-00-00'){
        return null;
    } else return $datetocheck;
}

function printdateblanker($datetocheck){
    $datearr = explode("-", $datetocheck);
    if($datearr[0] == '0000') $datearr[0] = '';
    if($datearr[1] == '00') $datearr[1] = '';
    if($datearr[2] == '00') $datearr[2] = '';
    return $datearr;
}

function embassy_formatter($datetochange){
    $dateparts = explode("-",$datetochange);
    $finaldate = $dateparts[2]."/".$dateparts[1]."/".$dateparts[0];
    return $finaldate;
}