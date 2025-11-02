<?php
require_once 'common/config2.php';
require_once 'common/functions.php';
require_once 'common/database2.php';

$arr = array();

$its=(isset($_GET['its']) && $_GET['its'] != '') ? $_GET['its'] : '';

$rs = dbQuery2("SELECT UCASE(CONCAT(IFNULL(ft.firstTitleName, ''), ' ', pm.name1,' ', IFNULL(ft2.firstTitleName, ''), ' ', IFNULL(pm.name2, ''), ' ', IFNULL(pm.name3, ''))) AS name, bs.address AS businessaddress, bs.phone1 AS businessphone, fim.address1 AS homeaddress1, fim.address2 AS homeaddress2, t.townName AS town, fim.phone1 AS homephone, pcd.personalMobileNo, pcd.permenentEmail, bloodGroupName AS bg, CONCAT(UCASE(SUBSTRING(`mohallaName`, 1, 1)),LOWER(SUBSTRING(`mohallaName`, 2))) AS mohallaName
		FROM personalmaster pm
		LEFT JOIN fisttitle ft ON pm.firstTitle_ID = ft.firstTitle_ID
		LEFT JOIN fisttitle ft2 ON pm.name2Title = ft2.firstTitle_ID
		LEFT JOIN bussinesssabeeldetails bsd ON pm.eJamaat_ID = bsd.eJamaat_ID
		LEFT JOIN bussinesssabeels bs ON bsd.bussinessSabeel_ID = bs.bussinessSabeel_ID
		LEFT JOIN familyidentificationmaster fim ON pm.personalSabeel_ID = fim.personalSabeel_ID
		LEFT JOIN personal_contactdetails pcd ON pm.eJamaat_ID = pcd.eJamaat_ID
		LEFT JOIN towns t ON fim.town_ID = t.town_ID
		LEFT JOIN bloodgroup b ON pm.bloodGroup_ID = b.bloodGroup_ID
        LEFT JOIN mohallas m ON m.mohalla_ID = t.mohalla_ID
		WHERE pm.eJamaat_ID = $its");

while($obj = mysql_fetch_object($rs)) {
	$arr[] = $obj;
}
echo '{"details":'.json_encode($arr).'}';

?>