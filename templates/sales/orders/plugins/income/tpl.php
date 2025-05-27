<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'order_income',
		table:'Заказы_Приход',
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
			"Код","Заказы_Код","Тип прихода","Приход","Валюта","Номер платежа","Дата прихода","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Тип_Код",index:"Тип_Код",formatter:'select',width:100,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Заказы_приход_тип']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_приход_тип'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Заказы_приход_тип'},opts,this); }
				}
			},

			{
				name: "Приход",index:"Приход",width:100,formatter:floatFormatter
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
				name: "Номер_платежа",index:"Номер_платежа",width:200
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Примечание",index:"Примечание",width:200,align:"left",
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
