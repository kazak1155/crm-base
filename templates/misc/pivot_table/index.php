<?php include_once $_SERVER['DOCUMENT_ROOT'].'/php/main/core.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Сводная таблица</title>
<?php
$Core = new Core;
$Core->get_meta();
$Core->get_files();
?>
<link href="/css/plugins/pivot.css" rel="stylesheet" />
<script src="/js/plugins/jquery.pivot/pivot.js"></script>
<script src="/js/plugins/jquery.pivot/jquery_pivot.js"></script>
<script>
$(function(){
	var field_definitions = [
		{name:'Квадрат_Адрес',type:'string',filterable: true},
		{name:'Регионы_Код',type:'integer',filterable: true},
		{name:'Фабрики_Код',type:'integer',filterable: true},
		{name:'Объем_груза',type:'float',rowLabelable: false},
		{name:'Вес_груза',type:'float',rowLabelable: false},
		{name:'MATRA',type:'float',rowLabelable: false},
		{name:'Дата',type:'date',filterable:true}
	];
	var json = '[["Квадрат_Адрес","Регионы_Код","Фабрики_Код","Объем_груза","Вес_груза","MATRA","Дата"],'+
	'["1016 CORDIGNANO (TV) Strada Maestra d\' Italia 32",20,3,0.7800,134.0000,58.00,"2015-03-05 17:25:00.000"],'+
	'["1016 CORDIGNANO (TV) Strada Maestra d\' Italia 32",20,3,1,134.0000,58.00,"2015-03-05 17:25:00.000"],'+
	'["1016 CORDIGNANO (TV) Strada Maestra d\' Italia 32",20,3,2,134.0000,58.00,"2015-03-05 17:25:00.000"],'+
	'["1016 CORDIGNANO (TV) Strada Maestra d\' Italia 32",20,3,3,134.0000,58.00,"2015-03-05 17:25:00.000"],'+
	'["1016 CORDIGNANO (TV) Strada Maestra d\' Italia 32",20,3,4,134.0000,58.00,"2015-03-05 17:25:00.000"]]';
	
	
	$('#pivot').pivot_display('setup',{
		json:json,
		fields:field_definitions
	})
});
</script>
</head>
<body>
<div id="pivot"></div>
<div id="results"></div>
</body>
</html>