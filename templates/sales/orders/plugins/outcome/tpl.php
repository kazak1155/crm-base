<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'order_outcome',
		table:'Деньги_расход',
		id:'Код',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		delOpts:true,
		onCellSelect:true,
		beforeSubmitCell:true,
		navGrid:true,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Контрагент","Расход","Валюта","Номер платежа","Дата расхода","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
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
				name: "Расход",index:"Расход",width:100,formatter:floatFormatter
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
				name: "Дата",index:"Дата",width:100,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Примечание",index:"Примечание",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			autowidth: true,
			cellEdit:true,
			shrinkToFit:true
		},
	})
})
</script>
