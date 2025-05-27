<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'carrier_rates',
		table:'Тарифы2_Курьеры_Тарифы',
		id:'Код',
		beforeSubmitCell:true,
		filterToolbar:true,
		useLs:false,
		navGrid:true,
		no_excel:true,
		no_sort:true,
		no_etc:true,
		navGridOptions:{add:true},
		cn:[
			"Код","Курьер","Регион","Провинция","V-min","V-max","Цена","Min-Цена"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Перевозчики_Код",index:"Перевозчики_Код",width:200,sortable:false,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Перевозчики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Перевозчики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Перевозчики'},opts,this); }
				}
			},

			{
				name: "Италия_Регионы_Код",index:"Италия_Регионы_Код",width:200,sortable:false,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Италия_Регионы']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Италия_Регионы'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Италия_Регионы'},opts,this); }
				}
			},

			{
				name: "Италия_Провинции_Код",index:"Италия_Провинции_Код",width:200,sortable:false,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Италия_Провинции']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Италия_Провинции'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Италия_Провинции'},opts,this); }
				}
			},

			{
				name: "Объем_мин",index:"Объем_мин",width:50,formatter:floatFormatter
			},

			{
				name: "Объем_макс",index:"Объем_макс",width:50,formatter:floatFormatter
			},

			{
				name: "Тариф",index:"Тариф",width:50,formatter:floatFormatter
			},

			{
				name: "Min_Tax",index:"Min_Tax",width:50,formatter:floatFormatter
			},
		],
		options:{
			shrinkToFit:true,
			cellEdit:true
		}
	});
})
</script>
