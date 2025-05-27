<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'carrier_params',
		table:'Тарифы2_Курьеры_Параметры_Значения',
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
			"Код","Курьер","Параметр","Значение","Примечание"
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
				name: "Параметры_Код",index:"Параметры_Код",width:200,sortable:false,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Тарифы2_Курьеры_Параметры']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Тарифы2_Курьеры_Параметры'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Тарифы2_Курьеры_Параметры'},opts,this); }
				}
			},

			{
				name: "Значение",index:"Объем_мин",width:50,formatter:floatFormatter
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			shrinkToFit:true,
			cellEdit:true
		}
	});
})
</script>
