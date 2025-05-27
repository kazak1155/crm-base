<?php
//session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
//print "<pre>"; print_r($_SESSION['usr']['user_id']); print "</pre>";
$Cache = new WinCache();
require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/saveform.php';
?>
<link href="/css/js/jquery-ui2.css" rel="stylesheet" type="text/css" />
<link href="/css/js/chosen2.css" rel="stylesheet" type="text/css" />
<link href="/css/grid/ui.jqgrid2.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/css/grid/jquery_flexbox.js"></script>
<script type="text/javascript" src="/css/grid/jquery-ui.min.js"></script>
<script src="/js/jqgrid/i18n/grid.locale-ru2.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/plugins/jqgrid/jquery.jqGrid.min.js"></script>
<script src="/js/jquery/extend/moment.js" type="text/javascript"></script>
<script src="/js/jquery/extend/moment-weekday-calc.js" type="text/javascript"></script>
<?php
$projectName = 'crm';
$jqGridIdent = '00'; //Идентификатор текущей таблицы
//require_once $_SERVER['DOCUMENT_ROOT']."/templates/bill/tablerows.php";
//print "||||||||||||||||||||||<pre>"; print_r($viewsArr); print "</pre>";
require_once $_SERVER['DOCUMENT_ROOT']."/templates/crm/main.php";
require_once $_SERVER['DOCUMENT_ROOT']."/templates/crm/grid.php";
require_once $_SERVER['DOCUMENT_ROOT']."/templates/crm/context.php";
require_once $_SERVER['DOCUMENT_ROOT']."/templates/crm/grid_add.php";
?>

