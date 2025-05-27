<script type="text/javascript">
$(function() {
	new jqGrid$({
		defaultPage:false,
		subgrid:true,
		main:false,
		name:'order_payments',
		table:'Заказы_финансы',
		tableQuery:'Заказы_фин',
		id:'Код',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		delOpts:true,
		onCellSelect:true,
		beforeSubmitCell:true,
		singleBlank:true,
		navGrid:true,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Стоимость по прайсу","Итоговая скидка","Стоимость со скидкой","Цена для клиента","Стоимость доставки","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Стоимость_прайс",index:"Стоимость_прайс",width:100,formatter:floatFormatter
			},

			{
				name: "Скидка_Итог",index:"Скидка_Итог",width:100,editable:false,formatter:'percentage'
			},

			{
				name: "Стоимость_скидка",index:"Стоимость_скидка",width:100,editable:false,formatter:floatFormatter
			},

			{
				name: "Стоимость_клиент",index:"Стоимость_клиент",width:100,formatter:floatFormatter
			},

			{
				name: "Стоимость_доставки_клиент",index:"Стоимость_доставки_клиент",width:100,formatter:floatFormatter
			},

			{
				name: "Примечание",index:"Примечание",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},
		],
		options:{
			autowidth: true,
			cellEdit:true,
			shrinkToFit:true
		},
		subGridOps:[
			{
				subgrid:true,
				main:false,
				name:'payment_discount',
				table:'Заказы_фин_скидки',
				id:'Код',
				filterToolbar:false,
				goToBlank:false,
				onCellSelect:true,
				beforeSubmitCell:true,
				delOpts:true,
				navGrid:false,
				refreshMainOnChange:'order_payments',
				subgridpost:{
					mainidName:'Заказы_фин_Код'
				},
				hideBottomNav:true,
				cn:[
					'Код','Заказы_фин_Код','Скидки'
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Пользователи_Код",index:"Пользователи_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Скидка",index:"Скидка",width:250,formatter:'percentage'
					},
				],
				options:{
					shrinkToFit:true,
					autowidth:true,
					cellEdit:true,
					height:'100%'
				}
			}
		]
	})
	new $ajaxForm({
		name:'orderinfo',
		floatLabel:true,
		view_only:true
	})
})
</script>
<div id="form_wrapper">
	<form class="gridToForm" id="orderinfo">
		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Клиент" class="float_input" required value="<?php echo $this->req_data['Клиенты_Код'] ?>"/>
			<label class="float_label">Клиент</label>
		</div>
		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Фабрика" class="float_input" required value="<?php echo $this->req_data['Фабрики_Код'] ?>"/>
			<label class="float_label">Фабрика</label>
		</div>
		<div class='float_label_wrapper'>
			<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Статус_код'] ?>"/>
			<label class="float_label">Статус</label>
		</div>
		<div class='float_label_wrapper'>
			<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Рейс'] ?>"/>
			<label class="float_label">Номер рейса</label>
		</div>
		<div class='float_label_wrapper'>
			<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Дата_получания_заявки'] ?>"/>
			<label class="float_label">Получение заявки</label>
		</div>
		<div class='float_label_wrapper'>
			<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Дата_оплаты_клиентом'] ?>"/>
			<label class="float_label">Оплата клиентом</label>
		</div>
		<div class='float_label_wrapper'>
			<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Дата_оплаты_фабрика'] ?>"/>
			<label class="float_label">Оплата фабрикой</label>
		</div>
		<div class='float_label_wrapper' style="width:calc(100% - 15px);max-width:100%">
			<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Номер_заказа'] ?>"/>
			<label class="float_label">Номер заказа</label>
		</div>
	</form>
</div>
<div id="grid_wrapper">
	<div>
		<table class='gridclass' id='order_payments'></table>
	</div>
</div>
