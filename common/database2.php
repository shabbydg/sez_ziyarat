<?php
//require_once 'config.php';

$dbHost2 = dbHost2;
$dbUser2 = dbUser2;
$dbPass2 = dbPass2;
$dbName2 = dbName2;

$dbConn2 = mysqli_connect ($dbHost2, $dbUser2, $dbPass2, $dbName2) or die ('MySQL connect failed. ' . mysqli_error($dbConn2));
mysqli_select_db($dbConn2, $dbName2) or die('Cannot select database. ' . mysqli_error($dbConn2));

function dbQuery2($sql)
{
	global $dbConn2;
    $result = mysqli_query($dbConn2, $sql) or die(mysqli_error($dbConn2));

	return $result;
}

function dbAffectedRows2()
{
	global $dbConn2;
	return mysqli_affected_rows($dbConn2);
}

function dbFetchArray2($result, $resultType = MYSQLI_NUM) {
	return mysqli_fetch_array($result, $resultType);
}

function dbFetchAssoc2($result)
{
	return mysqli_fetch_assoc($result);
}

function dbFetchRow2($result)
{
	return mysqli_fetch_row($result);
}

function dbFreeResult2($result)
{
	return mysqli_free_result($result);
}

function dbNumRows2($result)
{
	return mysqli_num_rows($result);
}

function dbSelect2($dbName2)
{
    global $dbConn2;
	return mysqli_select_db($dbConn2, $dbName2);
}

function dbInsertId2()
{
	global $dbConn2;
	return mysqli_insert_id($dbConn2);
}
?>