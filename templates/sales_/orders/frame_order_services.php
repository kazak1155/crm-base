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
	var fin = new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'order_services',
		table:'Заказы_Услуги',
		id:'Код',
		hideBottomNav:true,
		footer:[
			{col:'Значение',calc:'sum'}
		],
		useLs:false,
		subgridpost:{
			mainid:rowid,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Услуга","Стоимость","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Услуга_Код",index:"Услуга_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $Core->get_lib(['tname'=>'Б_Виды_Услуг']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Виды_Услуг'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Виды_Услуг',getNull:false},opts,this); }
				}
			},

			{
				name: "Значение",index:"Значение",width:100,formatter:floatFormatter,
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			shrinkToFit:true
		}
	})
})
</script>
</head>
<body>
</body>
</html>
