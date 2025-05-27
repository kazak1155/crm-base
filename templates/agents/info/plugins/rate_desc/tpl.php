<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>

<script type="text/javascript">
	$(function() {
		new jqGrid$({
			main:false,
			subgrid: true,
			pseudo_subgrid: true,
			name:'rate_desc',
			table:'Тарифы2_Объем',
			id:'Код',
			ordername:'Код',
			tableSort:'ASC',
			hideBottomNav:true,
			useLs:false,
			navGrid:false,
			subgridpost:{
				mainid:'',
				mainidName:'Тариф_Код'
			},
			cn:[
				"Код","Тариф_Код","Цена","Плотность_мин","Плотность_макс"
			],
			cm:[
				{
					name: "Код",index:"Код",width:50,hidden:false,editable:false,editrules:{edithidden:false},hidedlg:true
				},

				{
					name: "Тариф_Код",index:"Тариф_Код",width:200,formatter:floatFormatter
				},

				{
					name: "Цена",index:"Цена",width:50,formatter:floatFormatter
				},

				{
					name: "Плотность_мин",index:"Плотность_мин",width:50,formatter:floatFormatter
				},
				{
					name: "Плотность_макс",index:"Плотность_макс",width:50,formatter:floatFormatter
				}
			],
			options:
				{
					shrinkToFit:true
				}
		})
	})
</script>


