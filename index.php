<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/core/classes/onload.php';
new onload($Core);
?>
<!DOCTYPE html>
<html>
<head>
<title>Грузоперевозки</title>
<?php $Site->get_meta(); ?>
<link href="/css/index-page.css" rel="stylesheet" type="text/css" />
<?php $Site->get_files(); ?>
</head>
<body>

<?php

if($User->user_name === 'new'){
	$Site->render_tmpl(TEMPLATES_CORE_DIR.'new_user_menu.php');
}else{
	$Site->render_tmpl(TEMPLATES_CORE_DIR.'main_menu.php');
	// /templates/crm/modalwin.php
	if(strpos($_REQUEST['reference'],'templates/bill') || strpos($_REQUEST['reference'],'templates/calls')  || strpos($_REQUEST['reference'],'templates/crm/modalwin')){
		$rPath = $_SERVER['DOCUMENT_ROOT'].$_REQUEST['reference']; //Врезка блока
		require_once $rPath;
	}else{
		if (v($Site->reference_url)) {
			$Site->get_reference_html();
		}
		if ((!$_REQUEST['reference']) && ($User->user_group_name != 'external_users')) {
			//require_once $_SERVER['DOCUMENT_ROOT'] . "/templates/crm/script.php";
		}
	}
}
?>
</body>
</html>
