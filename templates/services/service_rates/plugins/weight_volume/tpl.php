	<div class="rate-data-form">
		<fieldset class="slide-set">
			<legend>Ставки тарифа</legend>
			<form id="rate-weight-volume">
				<table id="weight-volume-table">
					<?php foreach($this->req_data['weight_volume_prices'] as $key => $value) { ?>
					<tr>
						<td style="display:none">
							<input type="hidden" name="Код" value="<?php echo $value['Код'] ?>"/>
						</td>
						<td style="display:none">
							<input type="hidden" name="Тариф_Код" value="<?php echo $this->req_data['Тариф_Код'] ?>"/>
						</td>
						<td>
							<div style="display:inline-block;width:200px;margin-left:5px;margin-top:18px">
								<select class="select2me" name="Категория_Груза" data-placeholder="Категория груза" style="width:200px" required>
									<?php echo $this->Core->get_lib_html(['tname'=>'Категории_груза','selected'=>$value['Категория_Груза']]);?>
								</select>
							</div>
						</td>
						<td>
							<div class='float_label_wrapper' style='width:calc(100px)' >
								<input type="text" name="Вес" class="float_input" value="<?php echo $value['Вес'] ?>"/>
							</div>
						</td>
						<td>
							<div class='float_label_wrapper' style='width:calc(100px)' >
								<input type="text" name="Объем" class="float_input" value="<?php echo $value['Объем'] ?>"/>
							</div>
						</td>						
					</tr>
					<?php } ?>
				</table>
			</form>
		</fieldset>
	</div>
</div>
