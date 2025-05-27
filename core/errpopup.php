<?php
require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/tload.php";
if(trim($_SESSION['dbo_error']) != ''){
	print $_SESSION['dbo_error'];
	$_SESSION['dbo_error'] = ''; unset($_SESSION['dbo_error']);
}
?>