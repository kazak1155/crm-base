<script type="text/javascript">
$(function() {
	var form = new $ajaxForm({
		name:'orderinfo',
		floatLabel:true,
		view_only:true
	});
	var form_admin = new $ajaxForm({
		eventHandlers:['keyup','change'],
		name:'orderadmin',
		floatLabel:true,
		id:'Код',
		table:'Заказы_админ',
		add:true
	});
})
</script>
<div id="form_wrapper">
	<form class="gridToForm" id="orderinfo">
		<fieldset>
			<legend>Данные заказа</legend>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="Клиент" class="float_input" required value="<?php echo $this->req_data['Клиенты_Код'] ?>"/>
				<label class="float_label">Клиент</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="Фабрика" class="float_input" required value="<?php echo $this->req_data['Фабрики_Код'] ?>"/>
				<label class="float_label">Фабрика</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Статус_код'] ?>"/>
				<label class="float_label">Статус</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Рейс'] ?>"/>
				<label class="float_label">Номер рейса</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);max-width:100%">
				<input type="text" name="Статус" class="float_input" required value="<?php echo $this->req_data['Номер_заказа'] ?>"/>
				<label class="float_label">Номер заказа</label>
			</div>

		</fieldset>
	</form>
	<form class="gridToForm" id="orderadmin">
		<fieldset>
			<legend>Администирование заказа</legend>
			<input type="hidden" name="Код" class="float_input" value="<?php echo $this->req_rowid ?>"/>
			<div class='float_label_wrapper'>
				<input type="text" name="Дата_согласование" class="float_input dp" required value="<?php echo $this->req_data['Дата_согласование'] ?>"/>
				<label class="float_label">Дата согласования</label>
			</div>
			<div class='float_label_wrapper'>
				<input type="text" name="Сумма_согласование" class="float_input" defaultValue='0.00' required value="<?php echo $this->req_data['Сумма_согласование'] ?>"/>
				<label class="float_label">Сумма согласованная</label>
			</div>
			<div class='float_label_wrapper'>
				<input type="text" name="Сумма_транспорт" class="float_input" defaultValue='0.00' required value="<?php echo $this->req_data['Сумма_транспорт'] ?>"/>
				<label class="float_label">Сумма транспорт</label>
			</div>
			<hr class="tabs-divider" />
			<div class='float_label_wrapper'>
				<label style="border:1px solid #d4d4d4;box-shadow: inset 0px 2px 2px #ececec;padding:4px;color: #33A;">Блокировка
					<input style="margin-left:10px;vertical-align:sub" type="checkbox" name="Блокировка" <?php echo $this->req_data['Блокировка'] === '1' ? 'checked':''  ?> value="<?php echo $this->req_data['Блокировка'] ?>"/>
				</label>
			</div>
		</fieldset>
	</form>
</div>
