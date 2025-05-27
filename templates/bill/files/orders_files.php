<?php
$file = $_SERVER['DOCUMENT_ROOT'].$_REQUEST['l'];
header('Content-Description: File Transfer');
//header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($_REQUEST['n']));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
header("Content-Type: image/jpeg");
if ($fd = fopen($file, 'rb')) {
	while (!feof($fd)) {
		print fread($fd, 1024);
	}
	fclose($fd);
}
//exit;
?>