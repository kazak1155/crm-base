<?php require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'; ?>
<!DOCTYPE html>
<html>
<head>
<?php
$Site->get_meta();
$Site->get_plugin_css();
$Site->get_files();
?>
</head>
<body>
<?php $Site->render_plugins_tmpl(); ?>
</body>
</html>
<?php
