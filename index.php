<?php
require_once 'common/config.php';
require "common/config2.php";
require_once 'common/functions.php';

$sabeel = (isset($_COOKIE['user']) && $_COOKIE['user'] != '') ? $_COOKIE['user'] : '30362306';
$redirect_key = (isset($_COOKIE['redirect_key']) && $_COOKIE['redirect_key'] != '') ? $_COOKIE['redirect_key'] : '';
$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : 'home';
$user_id = getActiveUser();

//check if the user_id is a valid shabab member
if($user_id == ''){
    //let's set $_COOKIE['redirect_key'] here to redirect to the correct page after login
    $redirect_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    setcookie("redirect_key", $redirect_link, time()+10800, "/", $cookie_host);  /* expire in 1 hour */
    
    header("Location:".$verification_link."its-verification");
} else {
    //if a redirect is necessary, then let's go there
    if($redirect_key <> ''){
        //1st kill the cookie to prevent continuous redirects
        setcookie("redirect_key", '', time()-9999, "/", $cookie_host);  /* killed by putting the date in the past */
        header("Location: " . $redirect_key);
    } 
}

switch ($view) {
    case 'home' :
        $content     = 'home.php';
        $pageTitle     = 'SEZ Membership Portal - Dashboard';
        break;

    case 'dashboard' :
        $content     = 'home.php';
        $pageTitle     = 'SEZ Membership Portal - Dashboard';
        break;

    case 'safars' :
        $content     = 'safars.php';
        $pageTitle     = 'ZMS - All Safars';
        break;

    case 'active-safars' :
        $content     = 'active-safars.php';
        $pageTitle     = 'ZMS - Active Safars';
        break;
        
    case 'kun-safar' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'kun-safar.php';
        $pageTitle     = 'ZMS - KUN Safar Details';
        break;
        
    case 'new-kun-registration' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'new-kun-registration.php';
        $pageTitle     = 'ZMS - KUN Safar Details';
        break;
        
    case 'new-kun-preregistration' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'new-kun-preregistration.php';
        $pageTitle     = 'ZMS - KUN Safar Details';
        break;  
        
    case 'edit-kun-registration' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'edit-kun-registration.php';
        $pageTitle     = 'ZMS - KUN Safar Details';
        break;
        
    case 'edit-kun-photos' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'edit-kun-photos.php';
        $pageTitle     = 'ZMS - KUN Safar Photos';
        break;



        

    case 'misr-safar' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'misr-safar.php';
        $pageTitle     = 'ZMS - MISR Safar Details';
        break;
        
    case 'new-misr-registration' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'new-misr-registration.php';
        $pageTitle     = 'ZMS - MISR Safar Details';
        break;
        
    case 'new-misr-preregistration' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'new-misr-preregistration.php';
        $pageTitle     = 'ZMS - MISR Safar Details';
        break;  
        
    case 'edit-misr-registration' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'edit-misr-registration.php';
        $pageTitle     = 'ZMS - MISR Safar Details';
        break;
        
    case 'edit-misr-photos' :
        $safarid = (isset($_GET['safarid']) && $_GET['safarid'] != '') ? $_GET['safarid'] : '0'; 
        if($safarid == 0) header("Location:" . $server_link . "safars");//0 means we need to redirect back to the safars list
        
        $content     = 'edit-misr-photos.php';
        $pageTitle     = 'ZMS - MISR Safar Photos';
        break;
    





    case 'new-safar' :
        $content     = 'new-safar.php';
        $pageTitle     = 'ZMS - Create New Safar';
        break;

    case 'pre-registration' :
        $content     = 'pre-registration.php';
        $pageTitle     = 'ZMS - Pre Registration';
        break;
        
    case 'oops' :
        $content     = 'oops.php';
        $pageTitle     = 'You will be redirected';
        break;
        
    default :
        $content     = 'home.php';
        $pageTitle     = 'SEZ Membership Portal -  Dashboard';
}

require_once 'template.php';
?>
