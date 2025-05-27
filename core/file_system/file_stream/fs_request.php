<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
//ob_start('ob_gzhandler');
ob_start();
new File_stream_query();
ob_end_flush();
exit();
?>
