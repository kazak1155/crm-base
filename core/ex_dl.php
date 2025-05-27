<?php

// source: Laravel Framework
// https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/Str.php
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

$haul = htmlspecialchars($_GET["haul"]);
//echo htmlspecialchars($_GET["haul"]);
//define('QUERY_SPACE',' ');

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
//$Site->init_tmpl_connection();

$Core->query = "SELECT TOP 1 Номер FROM tig50.dbo.Рейсы WHERE Код = ?";
$Core->query_params = array($haul);
$rows = $Core->PDO();

while($hauls = $rows->fetch()){
    $haul_num = $hauls['Номер'];
}
$i = 0; 
$Core->query = "SELECT files.name,files.file_stream FROM srv.dbo.files files".QUERY_SPACE;
$Core->query .= "INNER JOIN srv.dbo.files files_folder ON files_folder.name = 'ft_ex_docs' AND files.parent_path_locator = files_folder.path_locator".QUERY_SPACE;
$Core->query .= "INNER JOIN srv.dbo.files files_rowid ON files_folder.parent_path_locator = files_rowid.path_locator".QUERY_SPACE;
$Core->query .= "INNER JOIN tig50_view.dbo.Форма_заказы З ON files_rowid.name = cast(З.Код as nvarchar(max))".QUERY_SPACE;
$Core->query .= "WHERE files.is_directory = 0 AND З.Удалено = 0 AND З.Рейсы_код = ? and".QUERY_SPACE;
$Core->query .= "((left(files.name,3) in ('ex_','t1_')) OR (left(files.name,4) = 'inv_'))";
$Core->query_params = array($haul);
$rows = $Core->PDO(array('global_db'=>true));

$filename = $haul_num.'.zip';
$zip = new ZipArchive();
$zip->open(UPLOAD_DIR.$filename, ZipArchive::CREATE|ZipArchive::OVERWRITE);

while($file = $rows->fetch()){
	//$a = (str_starts_with($file['name'],'ex_'))||(str_starts_with($file['name'],'t1_'))||(str_starts_with($file['name'],'inv_'));
	//if ($a === true) 
    	$zip->addFromString($file['name'], $file['file_stream']);
        $i++;
}
//$zip->addEmptyDir('t');
//ob_end_flush();
$zip->close();


 

/*
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($file));
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));

if ($fd = fopen($file, 'rb')) {
	while (!feof($fd)) {
		print fread($fd, 1024);
	}
	fclose($fd);
}
//exit();
*/

header('Content-type: application/zip');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile(UPLOAD_DIR.$filename);
unlink(UPLOAD_DIR.$filename);

?>
