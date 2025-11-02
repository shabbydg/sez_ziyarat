<?php
/*
	Contain the common functions 
	required in shop and admin pages
*/
//require_once 'config.php';
//require_once 'database.php';

function queryString()
{
	$qString = array();
	
	foreach($_GET as $key => $value) {
		if (trim($value) != '') {
			$qString[] = $key. '=' . trim($value);
		} else {
			$qString[] = $key;
		}
	}
	
	$qString = implode('&', $qString);
	
	return $qString;
}

/*
	Put an error message on session 
*/
function setError($errorMessage)
{
	if (!isset($_SESSION['plaincart_error'])) {
		$_SESSION['plaincart_error'] = array();
	}
	
	$_SESSION['plaincart_error'][] = $errorMessage;

}

/*
	print the error message
*/
function displayError()
{
	if (isset($_SESSION['plaincart_error']) && count($_SESSION['plaincart_error'])) {
		$numError = count($_SESSION['plaincart_error']);
		
		echo '<table id="errorMessage" width="550" align="center" cellpadding="20" cellspacing="0"><tr><td>';
		for ($i = 0; $i < $numError; $i++) {
			echo '&#8226; ' . $_SESSION['plaincart_error'][$i] . "<br>\r\n";
		}
		echo '</td></tr></table>';
		
		// remove all error messages from session
		$_SESSION['plaincart_error'] = array();
	}
}


function getPagingQuery($sql, $itemPerPage = 10)
{
	if (isset($_GET['page']) && (int)$_GET['page'] > 0) {
		$page = (int)$_GET['page'];
	} else {
		$page = 1;
	}
	
	// start fetching from this row number
	$offset = ($page - 1) * $itemPerPage;
	
	return $sql . " LIMIT $offset, $itemPerPage";
}


function getPagingLink($sql, $itemPerPage = 10, $strGet = '')
{
	$result        = dbQuery($sql);
	$pagingLink    = '';
	$totalResults  = dbNumRows($result);
	$totalPages    = ceil($totalResults / $itemPerPage);
	
	// how many link pages to show
	$numLinks      = 10;

	// create the paging links only if we have more than one page of results
	if ($totalPages > 1) {

		$pagingLink = '<ul class="pagination">';

		
		$self = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ;
		

		if (isset($_GET['page']) && (int)$_GET['page'] > 0) {
			$pageNumber = (int)$_GET['page'];
		} else {
			$pageNumber = 1;
		}
		
		for($i=1; $i<=$totalPages; $i++){
			if($pageNumber == $i)
				$pagingLink .= "<li class='active'><a href='#'>$i</a></li>";
			else
				$pagingLink .= "<li><a href='$self?$strGet&page=$i'>$i</a></li>";
		}
		
		$pagingLink .= '</ul>';
	
	}
	
	return $pagingLink;
}

function getPrevNext($sql, $itemPerPage = 10, $strGet = '')
{
	$result        = dbQuery($sql);
	$prevNext    = '';
	$pagingLink    = '';
	$totalResults  = dbNumRows($result);
	$totalPages    = ceil($totalResults / $itemPerPage);
	
	// how many link pages to show
	$numLinks      = 10;

		
	// create the paging links only if we have more than one page of results
	if ($totalPages > 1) {
	
		$prevNext = '<ul class="direction-nav pagination-direction float-right">';
	
		$self = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ;
		

		if (isset($_GET['page']) && (int)$_GET['page'] > 0) {
			$pageNumber = (int)$_GET['page'];
		} else {
			$pageNumber = 1;
		}
		
		// print 'previous' link only if we're not
		// on page one
		if ($pageNumber > 1) {
			$page = $pageNumber - 1;
			if ($page > 1) {
				$prev = " <li><a href='$self?$strGet&page=$page' class='btn btn-prev'><span class='icon-arrow-left10'></span></a></li>";
			} else {
				$prev = " <li><a href='$self?$strGet' class='btn btn-prev'><span class='icon-arrow-left10'></span></a></li> ";
			}	
		} else {
			$prev  = "<li><a href='#' class='btn btn-prev disabled'><span class='icon-arrow-left10'></span></a></li>"; // we're on page one, disable the link
		}
	
		// print 'next' link only if we're not
		// on the last page
		if ($pageNumber < $totalPages) {
			$page = $pageNumber + 1;
			$next = " <li><a href='$self?$strGet&page=$page' class='btn btn-next'><span class='icon-arrow-right9'></span></a></li> ";
		} else {
			$next = " <li><a href='#' class='btn btn-next disabled'><span class='icon-arrow-right9'></span></a></li> "; // we're on the last page, disable the link
		}

		// comile the links
		$prevNext = $prevNext . $prev . $next;
		
		$prevNext .= '</ul>';
	}
	
	return $prevNext;
}
?>