<?php
$rowid = $_GET['rowid'];
$o_data = $this->catchPDO("SELECT * FROM [Счет]($rowid)")->fetchAll(PDO::FETCH_ASSOC);
$o_data = $this->iconvKeys($o_data, 'cp1251', 'utf-8');
$o_services = $this->catchPDO("SELECT * FROM Заказы_Услуги INNER JOIN Б_Виды_Услуг ON Заказы_Услуги.Услуга_Код = Б_Виды_Услуг.Код WHERE Заказы_Код = $rowid")->fetchAll(PDO::FETCH_ASSOC);
$o_services = $this->iconvKeys($o_services, 'cp1251', 'utf-8');
?>
<form id="o-data">
	<fieldset>
	<legend>
		Состав заказа
	</legend>
	<table>
		<?php
		foreach ($o_data as $rownum => $row)
		{
			?>
			<tr>
				<td><input type="hidden" name="Код" value="<?php echo $row['Код'] ?>"/></td>
				<td width="250px">
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
				<td width="250px">
					<div class='float_label_wrapper' style="width:calc(100% - 10px)">
						<input type="text" name="Стоимость_фабрика" class="float_input" disabled="disabled" value="<?php echo $row['Стоимость_фабрика']?>"/>
						<label class="float_label">Ц.фабрика</label>
					</div>
				</td>
				<td width="250px">
					<div class='float_label_wrapper' style="width:calc(100% - 10px)">
						<input type="text" name="Стоимость_логистика" class="float_input" disabled="disabled" value="<?php echo $row['Стоимость_логистика']?>"/>
						<label class="float_label">Ц.логистика</label>
					</div>
				</td>
				<td width="250px">
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
	<legend>
		Услуги заказа
	</legend>
	<table>
		<?php
		foreach ($o_services as $rownum => $row)
		{
			?>
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
<?php } ?>
	</table>
	</fieldset>
</form>
<?php endif; ?>
<button class="niceButton_blue">Сохранить и выставить счет</button>
