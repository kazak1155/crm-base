<div class="rates-content">
	<div class="rates-tree-container">
		<select class="select2me" id="service" data-selectops='{"allowClear": false}' data-placeholder="Услуги" style="width:100%">
			<?php echo $this->Core->get_lib_html(['tname'=>'Услуги_Тарифицированные']);?>
		</select>
		<button disabled class="button-flat-blue rates-button-navigation" id="new-rate">Создать тариф</button>
		<button class="button-flat-blue rates-button-navigation" id="goods-cats">Категории грузов</button>
		<button class="button-flat-blue rates-button-navigation" id="ita-carrier-prm">Параметры перевозчиков</button>
		<button class="button-flat-blue rates-button-navigation" id="ita-prices">Тарифы перевозчиков</button>
		<table class='gridclass' id='rates-tree'></table>
	</div>
	<div class="rate-data"></div>
</div>
