<div class="navigation-wrapper">
	<div class="selects-wrapper">
		<select class="select2me" id="base-rate" data-selectops='{"allowClear": false}' data-placeholder="Базовый тариф" style="width:100%">
			<?php echo $this->Core->get_lib_html(['tname'=>'Тарифы2','cache'=>false,'filters'=>['groupOp'=>'AND',["field"=>"Родитель","op"=>"isNull"]]]);?>
		</select>
	</div>
	<div class="selects-wrapper">
		<select class="select2me" id="child-rate" data-selectops='{"allowClear": true}' data-placeholder="Дочерний тариф" style="width:100%"></select>
	</div>
	<div class="buttons-wrapper">
		<button class="button-flat-blue rates-button-navigation" id="button-new-rate">Создать базовый тариф</button>
		<button class="button-flat-blue rates-button-navigation" id="button-new-child-rate" disabled>Создать дочерний тариф</button>
		<button class="button-flat-blue rates-button-navigation" id="button-del-child-rate" disabled>Удалить дочерний тариф</button>
		<button class="button-flat-blue rates-button-navigation" id="button-open-category" >Открыть категории грузов</button>
	</div>
	<div class="rate-description-wrapper float_label_wrapper">
		<form id="rate-description-form" >
			<input type="hidden" name="Код"/>
			<textarea name="Описание" id="rate-description" class="float_input"></textarea>
			<label for="rate-description" class="float_label">Описание тарифа</label>
		</form>
	</div>
</div>
<div class="forms-wrapper">
	<div class="rate-data-dummy">
		<fieldset class="slide-set">
			<legend>...</legend>
			<form></form>
		</fieldset>
	</div>
	<div class="rate-cats-wrapper init-none">
		<fieldset class="slide-set">
			<legend>Ставки тарифа по категориям</legend>
			<form id="rate-cats"></form>
		</fieldset>
	</div>
	<div class="form-volume-wrapper init-none">
		<fieldset class="slide-set">
			<legend>Ставки тарифа за объем</legend>
			<form id="rate-volume"></form>
		</fieldset>
	</div>
	<div class="form-weight-wrapper">
		<fieldset class="slide-set">
			<legend>Ставки тарифа за вес</legend>
			<form id="rate-weight"></form>
		</fieldset>
	</div>
</div>
<div class="grids-wrapper">
	<div>
		<table class='gridclass' id='rate-params'></table>
	</div>
	<div style="margin-top:23px;">
		<table class='gridclass' id='rate-clients'></table>
	</div>
</div>
