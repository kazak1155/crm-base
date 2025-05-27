<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		delOpts:true,
		main:false,
		name:'orders_prim',
		table:'Заказы_Примечание',
		id:'Код',
		orderName:'Дата',
		tableSort:'DESC',
		hideBottomNav:true,
		useLs:false,
		navGrid:false,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Дата","Примечание","Тип"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Дата",index:"Дата",width:100,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Флаг",index:"Флаг",width:100,stype:'select',
				formatter:function(cellvalue, options, rowObject)
				{
					var value;
					if(cellvalue == 0)
						value = 'Примечание';
					else if(cellvalue == 1)
						value = 'Примечание-Д'
					return value;
				},
				searchoptions:{
					value:':;0:Примечание;1:Примечание-Д',dataInit:dataSelect2
				}
			}
		],
		options:{
			shrinkToFit:true
		}
	})
})
</script>
