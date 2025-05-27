<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'outcome',
		table:'Деньги_расход',
		title:'Расход',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		goToBlank:true,
		onCellSelect:true,
		beforeSubmitCell:true,
		delOpts:true,
		addFormOptions:{
			beforeSubmit:function(postdata, formid)
			{
				postdata.tname = 'Деньги_расход';
				return[true];
			}
		},
		footer:[
			{col:'Расход',calc:'sum'}
		],
		navGridOptions:{add:true},
		navGrid:true,
		cn:[
			"Код","Контрагент","Расход","Валюта","Номер платежа","Дата расхода","Номер заказа","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Контрагент_Код",index:"Контрагент_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Контрагенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Контрагенты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Контрагенты'},opts,this); }
				}
			},

			{
				name: "Расход",index:"Расход",width:50,formatter:floatFormatter
			},

			{
				name: "Валюта_Код",index:"Валюта_Код",formatter:'select',width:100,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Валюты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Валюты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Валюты'},opts,this); }
				}
			},

			{
				name: "Номер_платежа",index:"Номер_платежа",width:200,formatter:floatFormatter
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Номер_заказа']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Номер_заказа'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Номер_заказа'},opts,this); }
				}
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			autowidth: true,
			cellEdit:true,
			shrinkToFit:true
		}
	})
})
</script>
