<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/php/main/core.php';
$rowid = $_REQUEST['rowid'];
$data = json_decode($_REQUEST['row_data'],true);
?>
<!DOCTYPE html>
<html>
<head>
<?php
$Core = new Core;
$Core->con_database('sales');
$Core->get_meta();
$Core->get_files();
$params = $Core->catchPDO("SELECT * FROM Заказы_Параметры WHERE Код = $rowid")->fetch();
if(is_array($params))
	$params = $Core->iconvKeys($params, 'cp1251', 'utf-8');
?>
<script type="text/javascript">
$(function() {
	var rowid = <?php echo $rowid ?>;
	new $ajaxForm({
		eventHandlers:['keyup','change'],
		name:'orderparams',
		floatLabel:true,
		view_only:true
	});
})
</script>
<style>
.select2-results {
    margin: 4px 4px 4px 0;
    max-height: 100px;
    overflow-x: hidden;
    overflow-y: hidden;
    padding: 0 0 0 4px;
    position: relative;
}
.select2-results ul {
	max-height: 100px !important;
}
</style>
</head>
<body>
	<form class="gridToForm" id="orderparams">
		<input type="hidden" name="Код" class="float_input" value="<?php echo $rowid ?>"/>
		<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Способ оплаты" name="Способ_Оплаты_Код" style="width:100%" required="required">
				<?php echo $Core->get_lib_html(['tname'=>'З_Б_Способы_Оплаты','selected'=>$params['Способ_Оплаты_Код']]);?>
			</select>
		</div>
		<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Валюта логистика" name="Валюта_логистика" style="width:100%" required="required">
				<?php echo $Core->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$params['Валюта_логистика']]);?>
			</select>
		</div>
		<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Валюта договора" name="Валюта_договора" style="width:100%" required="required">
				<?php echo $Core->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$params['Валюта_договора']]);?>
			</select>
		</div>
		<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<input type="text" name="Стоимость_куба" class="float_input" value="<?php echo $params['Стоимость_куба'] ?>"/>
			<label class="float_label">У/e за куб</label>
		</div>

		<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<input type="text" name="Наценка" class="float_input" value="<?php echo $params['Наценка'] ?>"/>
			<label class="float_label">Наценка %</label>
		</div>
	</form>
</body>
</html>
