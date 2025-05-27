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
$o_data = $Core->catchPDO("SELECT * FROM [Счет]($rowid)")->fetchAll(PDO::FETCH_ASSOC);
$o_data = $Core->iconvKeys($o_data, 'cp1251', 'utf-8');
$o_services = $Core->catchPDO("SELECT * FROM Заказы_Услуги INNER JOIN Б_Виды_Услуг ON Заказы_Услуги.Услуга_Код = Б_Виды_Услуг.Код WHERE Заказы_Код = $rowid")->fetchAll(PDO::FETCH_ASSOC);
$o_services = $Core->iconvKeys($o_services, 'cp1251', 'utf-8');
?>
<style>
.niceButton_blue
{
	width:100%;
	margin: 5px 0px;
	padding:5px;
}
.float_label_wrapper
{
	min-width: 50px;
}
</style>
<script type="text/javascript">
$(function() {
	var rowid = <?php echo $rowid ?>;
	$('.niceButton_blue').click(function(e){
		var o_data = {},summ_real = summ_fact = 0;
		$.each($('#o-data').find('input[type!="hidden"]:enabled'),function(){
			var id = $(this).closest('tr').find('input[type="hidden"]').val(),
			real = parseFloat($(this).closest('tr').find('input[name="Стоимость_фабрика"]').val()) + parseFloat($(this).closest('tr').find('input[name="Стоимость_логистика"]').val());
			real =  real.toFixed(2);
			summ_real += parseFloat(real);
			summ_fact += parseFloat(this.value);
			o_data[id] = {'Стоимость':this.value,'Себестоимость': real};
		});
		$.asyncloop({
			length:Object.keys(o_data).length,
			loop_action:function(loop,i)
			{
				var row = Object.keys(o_data)[i];
				$.ajaxShort({
					data:{
						action:'edit',
						query:'DELETE FROM Заказы_Счета WHERE Заказы_Код = '+ rowid + ' AND Заказы_Состав_Код = '+ row
					},
					success:function(){
						$.ajaxShort({
							data:{
								action:'add',
								query:"INSERT INTO Заказы_Счета (Заказы_Код,Заказы_Состав_Код,Стоимость,Стоимость_предв) VALUES ("+ rowid +","+ row +","+ o_data[row]['Стоимость'] +","+ o_data[row]['Себестоимость'] +")"
							},
							success:function(){
								loop();
							}
						});
					}
				});
			},
			callback:function()
			{
				$.ajaxShort({
					data:{
						action:'edit',
						query:'DELETE FROM Заказы_Фин_Движение WHERE Заказы_Код = '+ rowid + ' AND Движение_Тип_Код IN (3,4)'
					},
					success:function(){
						$.ajaxShort({
							data:{
								action:'add',
								query:"INSERT INTO Заказы_Фин_Движение (Заказы_Код,Движение_Тип_Код,Плательщик,Получатель,Движение_Категория_Код,Валюта_Код,Значение,Подтверждено) VALUES ("+ rowid +",3,35,35,4,'EUR',"+ summ_real +",1)"
							},
							success:function(){
								$.ajaxShort({
									data:{
										action:'add',
										query:"INSERT INTO Заказы_Фин_Движение (Заказы_Код,Движение_Тип_Код,Плательщик,Получатель,Движение_Категория_Код,Валюта_Код,Значение,Подтверждено) VALUES ("+ rowid +",4,35,35,4,'EUR',"+ summ_fact +",1)"
									},
									success:function()
									{
										window.parent.$('.confirm-html').attr('button-close',true);
										window.parent.$('.confirm-html').dialog('close','helpme');
									}
								});
							}
						});
					}
				});
			}
		});
	})
});
</script>
</head>
<body>
<div id="form_wrapper">
	<form id="o-data">
		<fieldset>
		<legend>Состав заказа</legend>
			<table style="width:100%">
				<?php
				foreach ($o_data as $rownum => $row)
				{
					?>
					<tr>
						<td width="0px" style="display:none"><input type="hidden" name="Код" value="<?php echo $row['Код'] ?>"/></td>
						<td width="100px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Артикул" class="float_input" disabled="disabled" value="<?php echo $row['Артикул']?>"/>
								<label class="float_label">Артикул</label>
							</div>
						</td>
						<td width="250px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Описание" class="float_input" disabled="disabled" value="<?php echo $row['Описание']?>"/>
								<label class="float_label">Описание</label>
							</div>
						</td>
						<td width="50px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Кол_во" class="float_input" disabled="disabled" value="<?php echo $row['Кол_во']?>"/>
								<label class="float_label">Кол-во</label>
							</div>
						</td>
						<td width="100px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Стоимость_фабрика" class="float_input" disabled="disabled" value="<?php echo $row['Стоимость_фабрика']?>"/>
								<label class="float_label">Ц.фабрика</label>
							</div>
						</td>
						<td width="100px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Стоимость_логистика" class="float_input" disabled="disabled" value="<?php echo $row['Стоимость_логистика']?>"/>
								<label class="float_label">Ц.логистика</label>
							</div>
						</td>
						<td width="100px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Итоговая" class="float_input" value="<?php echo $row['Итоговая']?>" required/>
								<label class="float_label">Итоговая</label>
							</div>
						</td>
					</tr>
		<?php } ?>
			</table>
		</fieldset>
	</form>
	<?php if(count($o_services) > 0 ):?>
	<form>
		<fieldset>
		<legend>Услуги заказа</legend>
			<table>
				<?php
				foreach ($o_services as $rownum => $row)
				{
					?>
					<tr>
						<td width="250px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Название" class="float_input" disabled="disabled" value="<?php echo $row['Название']?>"/>
								<label class="float_label">Вид услуги</label>
							</div>
						</td>
						<td width="250px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Значение" class="float_input" disabled="disabled" value="<?php echo $row['Значение']?>"/>
								<label class="float_label">Стоимость/руб.</label>
							</div>
						</td>
						<td width="400px">
							<div class='float_label_wrapper' style="width:calc(100% - 10px)">
								<input type="text" name="Примечание" class="float_input" disabled="disabled" value="<?php echo $row['Примечание']?>"/>
								<label class="float_label">Примечание</label>
							</div>
						</td>
					</tr>
		<?php } ?>
			</table>
		</fieldset>
	</form>
	<?php endif; ?>
	<div style="position: fixed;bottom:0%;width:100%">
		<button class="niceButton_blue">Сохранить и выставить счет</button>
	</div>
</div>
</body>
</html>
