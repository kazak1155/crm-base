	<div class="rate-data-form">
		<fieldset class="slide-set">
			<legend>Ставки тарифа за вес</legend>
			<form id="rate-weight">
				<table id="weight-table">
					<?php foreach($this->req_data['weight_prices'] as $key => $value) { ?>
					<tr>
						<td style="display:none">
							<input type="hidden" name="Код" value="<?php echo $value['Код'] ?>"/>
						</td>
						<td style="display:none">
							<input type="hidden" name="Тариф_Код" value="<?php echo $this->req_data['Тариф_Код'] ?>"/>
						</td>
						<td>
							<div style="display:inline-block;width:350px;margin-left:5px;margin-top:18px">
								<select class="select2me" name="Категория_Груза" data-placeholder="Категория груза" style="width:370px" required>
									<?php echo $this->Core->get_lib_html(['tname'=>'Категории_груза','selected'=>$value['Категория_Груза']]);?>
								</select>
							</div>
						</td>
						<td>
							<div class='float_label_wrapper' style='width:calc(100% - 15px)' >
								<input type="text" name="Цена" class="float_input" required value="<?php echo $value['Цена'] ?>"/>
								<label class="float_label">Цена за килограмм</label>
							</div>
						</td>
					</tr>
					<?php } ?>
				</table>
			</form>
		</fieldset>
	</div>
</div>
