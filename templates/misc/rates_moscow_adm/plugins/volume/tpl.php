<table id="volume-table">
	<?php foreach($this->req_data['volume_prices'] as $key => $value) { ?>
	<tr>
		<td style="diplay:none"><input type="hidden" name="Код" value="<?php echo $value['Код'] ?>"/></td>
		<td style="diplay:none"><input type="hidden" name="Тариф_Код" value="<?php echo $this->req_data['Тариф_Код'] ?>"/></td>
		<td colspan="3">
			<div class='float_label_wrapper' style='width:calc(100% - 15px)'>
				<input type="text" name="Цена" class="float_input" required value="<?php echo $value['Цена'] ?>"/>
				<label class="float_label">Цена</label>
			</div>
		</td>
		<td>
			<div class='float_label_wrapper' style='width:calc(100% - 15px)'>
				<input type="text" name="Плотность_мин" class="float_input" required value="<?php echo $value['Плотность_мин'] ?>"/>
				<label class="float_label">Плотность min</label>
			</div>
		</td>
		<td>
			<div class='float_label_wrapper' style='width:calc(100% - 15px)'>
				<input type="text" name="Плотность_макс" class="float_input" required value="<?php echo $value['Плотность_макс'] ?>"/>
				<label class="float_label">Плотность max</label>
			</div>
		</td>
	</tr>
	<?php } ?>
</table>
