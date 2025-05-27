<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'rate-clients',
		table:'Контрагенты_Тарифы',
		id:'Код',
		hideBottomNav:true,
		useLs:false,
		navGrid:false,
		delOpts:true,
		beforeSubmitCell:true,
		onCellSelect:true,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Тариф_Код'
		},
		cn:[
			"Код","Тариф_Код","Контрагент"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Тариф_Код",index:"Тариф_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Клиент_Код",index:"Клиент_Код",width:200,stype:'select',formatter:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Клиенты_EN']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Клиенты_EN'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_Клиенты_EN'},opts,this); }
				},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if (rawObject['Весь_состав'] == 1)
						return "style='background-color:#4ca64c;font-weight:bold;color:#FFF'";
				}
			},
		],
		options:{
			shrinkToFit:true,
			cellEdit:true
		}
	});
})
</script>
