<script type="text/javascript">
$(function()
{
	var form = new $ajaxForm({
		name:'haulinfo',
		floatLabel:true,
		view_only:true
	});
	var form_admin = new $ajaxForm({
		eventHandlers:['keyup','change'],
		name:'hauldata',
		floatLabel:true,
		id:'Код',
		table:'Рейсы',
		add:true
	});
});
</script>
<div id="form_wrapper">
	<form class="gridToForm" id="haulinfo">
		<fieldset>
			<legend>Основные данные рейса</legend>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="Клиент" class="float_input" required value="<?php echo $this->req_data['Номер'] ?>"/>
				<label class="float_label">Номер рейса</label>
			</div>

		</fieldset>
	</form>
	<form class="gridToForm" id="hauldata">
		<fieldset>
			<legend>Дополнительные данные рейса</legend>
			<input type="hidden" name="Код" class="float_input" value="<?php echo $this->req_rowid ?>"/>
			<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
				<select class="select2me" data-selectops='{"allowClear": false}' data-placeholder="Таможня" name="Таможни_Код" style="width:100%">
					<?php echo $this->Core->get_lib_html(['tname'=>'Таможни','selected'=>$this->req_data['Таможни_Код']]);?>
				</select>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="НомерМашины" class="float_input" required value="<?php echo $this->req_data['НомерМашины'] ?>"/>
				<label class="float_label">Номер машины</label>
			</div>
			<div style="display:inline-block;width:calc(33% - 9px);margin-top:15px;margin-left:5px;">
				<select class="select2me" data-selectops='{"allowClear": false}' data-placeholder="Объем" name="Объем" style="width:100%">
					<?php echo $this->Core->get_lib_html(['tname'=>'Рейсы','selected'=>$this->req_data['Объем'],'init_data'=>'0:0;93:93;120:120;82:82;20:20f;40:40f;60:HQ']);?>
				</select>
			</div>
			<div class='float_label_wrapper' style="width:calc(33% - 15px);">
				<input type="text" name="Брутто_ЭД" class="float_input" required value="<?php echo $this->req_data['Брутто_ЭД'] ?>"/>
				<label class="float_label">Брутто ЭД</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(34% - 15px);">
				<input type="text" name="Мест_ЭД" class="float_input" required value="<?php echo $this->req_data['Мест_ЭД'] ?>"/>
				<label class="float_label">Мест ЭД</label>
			</div>
			<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
				<select class="select2me" data-selectops='{"allowClear": false}' data-placeholder="Перевозчик" name="Перевозчики_Код" style="width:100%">
					<?php echo $this->Core->get_lib_html(['tname'=>'Перевозчики','selected'=>$this->req_data['Перевозчики_Код']]);?>
				</select>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<textarea name="Примечание" class="float_input" required><?php echo $this->req_data['Примечание'] ?></textarea>
				<label class="float_label">Примечание</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="ГТД" class="float_input" required value="<?php echo $this->req_data['ГТД'] ?>"/>
				<label class="float_label">ГТД</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Платеж" class="float_input" required value="<?php echo $this->req_data['Платеж'] ?>"/>
				<label class="float_label">Платеж</label>
			</div>
			<div style="display:inline-block;width:calc(50% - 9px);margin-top:15px;margin-left:5px;">
				<select class="select2me" data-selectops='{"allowClear": false}' data-placeholder="Валюта" name="Валюты_Код" style="width:100%">
					<?php echo $this->Core->get_lib_html(['tname'=>'Валюты','selected'=>$this->req_data['Валюты_Код']]);?>
				</select>
			</div>
		</fieldset>
	</form>
</div>
