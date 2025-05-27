<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
//ob_start('ob_gzhandler');
ob_start();
new Mssql_factory($User,$Core,$Site);
ob_end_flush();
exit();
?>
