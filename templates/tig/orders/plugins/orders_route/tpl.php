<script type="text/javascript">
$(function()
{
	new jqGrid$({
		subgrid:true,
		pseudo_subgrid:true,
		main:false,
		name:'orders_route',
		table:'Заказы_Маршруты',
		id:'Код',
		orderName:'Дата',
		tableSort:'DESC',
		hideBottomNav:true,
		useLs:false,
		navGrid:true,
		filterToolbar:true,
		navGridOptions:{
			add:true
		},
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Статус","Дата планируемая","Дата фактическая","Мест","Вес","Объем","Примечание","Очередность"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},
			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Статус_Код",index:"Заказы_Статус_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Заказы_статус']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Заказы_статус'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Заказы_статус'},opts,this); }
				}
			},

			{
				name: "Дата_П",index:"Дата_П",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'ISO8601Long',newformat:'d.m.Y H:i:s'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Дата_К",index:"Дата_К",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'ISO8601Long',newformat:'d.m.Y H:i:s'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Мест",index:"Мест",width:50
			},

			{
				name: "Вес",index:"Вес",width:50
			},

			{
				name: "Объем",index:"Объем",width:50
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Очередность",index:"Очередность",width:50
			}
		],
		options:{
			shrinkToFit:true,
			cellEdit:true
		}
	});
})
</script>
