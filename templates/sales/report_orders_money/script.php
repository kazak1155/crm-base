<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'orders_report_money',
		table:'Заказы_Отчет_Финансы',
		title:'Отчет - Деньги',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		footer:[
			{col:'Стоимость_прайс',calc:'sum'},
			{col:'Скидка_Итог',calc:'sum'},
			{col:'Стоимость_скидка',calc:'sum'},
			{col:'Стоимость_клиент',calc:'sum'},
			{col:'Стоимость_доставки_клиент',calc:'sum'},
			{col:'Сумма_транспорт',calc:'sum'}
		],
		navGrid:true,
		cn:[
			"Код","Клиент","Фабрика","Статус","Номер заказа",
			"Стоимость по прайсу","Итоговая скидка","Стоимость со скидкой","Цена для клиента","Стоимость доставки","Сумма транспорт","Дата_согласование","Сумма_согласование"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Клиенты_Код",index:"Клиенты_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Клиенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Клиенты'})}
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Фабрики'})}
				}
			},

			{
				name: "Статус_Код",index:"Статус_Код",formatter:'select',width:160,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Заказы_Статус','order'=>1]) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_Статус'})}
				},
				cellattr:function(rowId,val,rawObject,cm,rdata)
				{
					if(parseInt(rawObject['Блокировка']) === 1)
						return 'class="not-editable-cell" style="color:#545454;background-color:#EBEBE4;"';
				}
			},

			{
				name: "Номер_заказа",index:"Номер_заказа",width:100,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Стоимость_прайс",index:"Стоимость_прайс",width:80,formatter:floatFormatter
			},

			{
				name: "Скидка_Итог",index:"Скидка_Итог",width:80,formatter:floatFormatter
			},

			{
				name: "Стоимость_скидка",index:"Стоимость_скидка",width:80,formatter:floatFormatter
			},

			{
				name: "Стоимость_клиент",index:"Стоимость_клиент",width:80,formatter:floatFormatter
			},

			{
				name: "Стоимость_доставки_клиент",index:"Стоимость_доставки_клиент",width:80,formatter:floatFormatter
			},

			{
				name: "Сумма_транспорт",index:"Сумма_транспорт",width:80,formatter:floatFormatterNulls
			},

			{
				name: "Дата_согласование",index:"Дата_согласование",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,
				formatter:'date',formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'}
			},

			{
				name: "Сумма_согласование",index:"Сумма_согласование",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,formatter:floatFormatterNulls
			},

		],
		options:{
			autowidth: true,
			shrinkToFit:true
		},
		events:{
			ondblClickRow:function(rowid)
			{
				var rowObject = $(this).getRowDataRaw(rowid);
				this.p.gridProto.plugin({
					name:'adm',
					data:rowObject,
					dialog_title:'Администирование заказа: "'+rowObject['Номер_заказа']+'"',
					dialog_width:650,
					dialog_height:420,
					dialog_refresh_grid:true
				});
			}
		}
	})
})
</script>
