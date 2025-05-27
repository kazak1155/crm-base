<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/php/main/core.php';
$rowid = $_REQUEST['rowid'];
$data = json_decode($_REQUEST['row_data'],true);
?>
<!DOCTYPE html>
<html>
<head>
<?php
$Core = new Core;
$Core->con_database('sales');
$Core->get_meta();
$Core->get_files();
?>
<script type="text/javascript">
$(function() {
	var rowid = <?php echo $rowid ?>;
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'orders_details',
		table:'Заказы_Состав',
		id:'Код',
		formpos:false,
		hideBottomNav:true,
		useLs:false,
		footer:[
			{col:'Объем',calc:'sum'},
			{col:'Кол_во',calc:'sum'},
			{col:'Вес',calc:'sum'}
		],
		subgridpost:{
			mainid:rowid,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Вид груза","Валюта","Описание","Артикул","Себ.Фабрики","Объем","Кол-во","Вес"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Вид_Груза_Код",index:"Вид_Груза_Код",width:150,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $Core->get_lib(['tname'=>'Б_Виды_Груза']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Виды_Груза'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Виды_Груза',getNull:false},opts,this); }
				}
			},

			{
				name: "Валюта_Код",index:"Валюта_Код",width:150,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $Core->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
				}
			},

			{
				name: "Описание",index:"Описание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Артикул",index:"Артикул",width:100
			},

			{
				name: "Стоимость_фабрика",index:"Стоимость_фабрика",width:100,formatter:floatFormatter
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter
			},

			{
				name: "Кол_во",index:"Кол_во",width:50,formatter:'integer'
			},
			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			}
		],
		options:{
			shrinkToFit:true
		}
	});
})
</script>
</head>
<body>
</body>
</html>
