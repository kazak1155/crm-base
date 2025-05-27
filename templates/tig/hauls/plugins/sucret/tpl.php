<?php
if($this->req_data['require'] != 'iddqd')
	die("<script type='text/javascript'>window.parent.$('.ui-dialog-content:visible').dialog('close');</script>");
?>
<script type="text/javascript">
$(function() {
	var idle = 0;
	var timer = setInterval(function()
	{
		idle++;
		var dialog = window.parent.$('.Допол').find('.ui-dialog-content');
		if(idle > 60)
			dialog.dialog('close');
	},1000);
	$(window).on('click keypress mousemove',function(e)
	{
		idle = 0;
	});
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'haul_outcome',
		table:'V_Услуги_Расход',
		id:'Код',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		navGrid:true,
		beforeSubmitCell:true,
		delOpts:true,
		navGridOptions:{add:true},
		onCellSelect:true,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Рейсы_Код'
		},
		cn:[
			"Код","Рейсы_Код","Контрагент","Фабрика","Услуга","Сумма","Валюта","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:1,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Рейсы_Код",index:"Рейсы_Код",width:1,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Контрагенты_Код",index:"Контрагенты_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Контрагенты_EN']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Контрагенты_EN'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Контрагенты_EN'},opts,this); }
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Фабрики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Фабрики'},opts,this); }
				}
			},

			{
				name: "Назнач_Платеж_Код",index:"Назнач_Платеж_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Рейсы_Услуги_Расход']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Рейсы_Услуги_Расход'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Рейсы_Услуги_Расход'},opts,this); }
				}
			},

			{
				name: "Сумма",index:"Сумма",width:100,formatter:floatFormatter
			},

			{
				name: "Валюты_Код",index:"Валюты_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Валюты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Валюты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Валюты'},opts,this); }
				}
			},

			{
				name: "Примечание",index:"Примечание",width:200,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			autowidth: true,
			shrinkToFit:true,
			cellEdit:true
		}
	})
})
</script>
