<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		name:'hauls_service',
		table:'V_Услуги_приход',
		id:'Код',
		delOpts:true,
		onCellSelect:true,
		beforeSubmitCell:true,
		inline_extra_param:{
			'Заказы_Код':<?php echo $this->req_rowid ?>,
			'Рейсы_Код':<?php echo $this->req_data['Рейсы_Код'] ?>,
			'Контрагенты_Код':<?php echo $this->req_data['Клиенты_Код'] ?>,
			'Фабрики_Код':<?php echo $this->req_data['Фабрики_Код'] ?>
		},
		hideBottomNav:true,
		navGrid:false,
		subgridpost:{
			mainidName:'Заказы_Код',
			mainid:<?php echo $this->req_rowid ?>
		},
		footer:[
			{col:'Сумма',calc:'sum'}
		],
		cn:[
			"Код","Услуга","Сумма","Валюта","Дата","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Назнач_Платеж_Код",index:"Назнач_Платеж_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Назнач_Платеж_П']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Назнач_Платеж_П'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{getNull:false,tname:'Выбор_Назнач_Платеж_П'},opts,this); }
				}
			},

			{
				name: "Сумма",index:"Сумма",width:50,formatter:floatFormatter
			},

			{
				name: "Валюты_Код",index:"Валюты_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Валюты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Валюты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{getNull:false,tname:'Выбор_Валюты'},opts,this); }
				}
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',inlinedefaultvalue:getDate(true),
				searchoptions:{
					sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp
				},
				formatoptions: {
					srcformat:'Y-m-d',newformat:'d.m.Y'
				},
				editoptions: {
					defaultValue:getDate(true),maxlengh: 10,dataInit: elemWd
				}
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
	})
})
</script>
