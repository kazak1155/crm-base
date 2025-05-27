<?php require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'; ?>
<!DOCTYPE html>
<html>
<head>
<?php
$Site->get_meta();
$Site->get_files();
$File_stream = new File_stream();
?>
<link href="style.css" rel="stylesheet" />
<script type="text/javascript">
$(function() {
	var fileTree = new $fileTree();
});
</script>
</head>
<body>
</body>
</html>
