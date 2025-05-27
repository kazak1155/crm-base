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
		name:'orders_cross',
		table:'Заказы_Кросс_Курс',
		id:'Код',
		formpos:false,
		hideBottomNav:true,
		useLs:false,
		navGrid:true,
		navGridOptions:{add:true},
		beforeSubmitCell:true,
		delOpts:true,
		subgridpost:{
			mainid:rowid,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Валюта исходящая","Валюта входящая","Курс"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Валюта_first",index:"Валюта_first",width:100,formatter:'select',stype:'select',
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
				name: "Валюта_second",index:"Валюта_second",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $Core->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
				},
				editoptions:{
					disabled:true,
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
				}
			},

			{
				name: "Курс",index:"Курс",width:50,formatter:floatFormatter
			}
		],
		options:{
			shrinkToFit:true,
			cellEdit:true
		}
	});
})
</script>
</head>
<body>
</body>
</html>
