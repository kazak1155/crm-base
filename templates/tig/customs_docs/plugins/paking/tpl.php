<script>
$(function() {
	new $ajaxForm({
		name:'orderinfo',
		floatLabel:true,
		view_only:true
	});
	new $ajaxForm({
		eventHandlers:['keyup','change'],
		name:'orderpaking',
		floatLabel:true,
		id:'Код',
		table:'Заказы'
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
				<input type="text" name="Подклиент" class="float_input" required value="<?php echo $this->req_data['Подклиенты_Код'] ?>"/>
				<label class="float_label">Подклиент</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="Фабрика" class="float_input" required value="<?php echo $this->req_data['Фабрики_Код'] ?>"/>
				<label class="float_label">Фабрика</label>
			</div>
		</fieldset>
	</form>
	<form class="gridToForm" id="orderpaking">
		<fieldset>
			<legend>Пакинг заказа</legend>
			<input type="hidden" name="Код" class="float_input" value="<?php echo $this->req_rowid ?>"/>
			<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
				<select class="select2me" data-placeholder="Импортер" name="Импортеры_Код" style="width:100%">
					<?php echo $this->Core->get_lib_html(['tname'=>'Импортеры','selected'=>$this->req_data['Импортеры_Код']]);?>
				</select>
			</div>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Пакинг_Мест" class="float_input" value="<?php echo $this->req_data['Пакинг_Мест'] ?>"/>
				<label class="float_label">Пакинг мест</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Пакинг_Вес" class="float_input" value="<?php echo $this->req_data['Пакинг_Вес'] ?>"/>
				<label class="float_label">Пакинг вес</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<textarea name="Примечание_пакинг" class="float_input" ><?php echo $this->req_data['Примечание_пакинг'] ?></textarea>
				<label class="float_label">Пакинг примечание</label>
			</div>
		</fieldset>
	</form>
</div>
