<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'orders_composition_summary',
		table:'Заказы_Состав_Сводная',
		id:'Заказы_Код',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		navGrid:false,
		footer:[
			{col:'Объем',calc:'sum'},
			{col:'Вес',calc:'sum'}
		],
		permFilter:{
			groupOp:"AND",rules:[
				{field:"Заказы_Код",op:"eq",data:<?php echo $this->req_rowid ?>}
			]
		},
		cn:[
			"Заказы_Код","Категория","Объем","Вес"
		],
		cm:[
			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Категории_Код",index:"Категории_Код",width:200,formatter:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Категории_груза']) ?>
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Категории_груза'},opts,this); }
				}
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			},
		],
		options:
		{
			shrinkToFit:true
		},
		events:{
			rowattr:function(rowData,currentObj,rowid)
			{
				var volume = parseInt(rowData['Объем']);
				var weight = parseInt(rowData['Вес']);
				if(isNaN(volume) && isNaN(weight))
					return {
						'style' : 'background:#FF3232;color:#FFF'
					};
			}
		}
	})
})
</script>
