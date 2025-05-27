<style>
#params-content input
{
	min-width:10px;
	max-width:30px;
	text-align: center;
}
#params-content p
{
	line-height: 300%;
	font-size: 16px;
}
#params-content div
{
	margin: 5px 0;
	padding: 0 10px;
	border: 2px dotted #346392;
}
</style>
<div class="rate-tpl-container">
	<fieldset class="slide-set">
		<legend>Параметры тарифа</legend>
		<form id="rate-params">
			<div id="params-content">
				<div>
					<p>
						За каждый дополнительный килограмм в
						<?php
						/*
						 <input name="overweight_volume" value="<?php echo $this->req_data['overweight_volume']['value'] ?>"/>
						*/
						?>
						1м<sup><small>3</small></sup> -
						<input name="overweight_price" value="<?php echo $this->req_data['overweight_price']['value'] ?>"/>
						Евро к ставке.
					</p>
				</div>
				<div>
					<p>
						Стоимость доставки груза менее
						<input name="min_volume" value="<?php echo $this->req_data['min_volume']['value'] ?>"/>
						м<sup><small>3</small></sup> (включительно),<br />
						составляет<input name="min_volume_price" value="<?php echo $this->req_data['min_volume_price']['value'] ?>"/>
						&euro; при весе до <input name="max_density" value="<?php echo $this->req_data['max_density']['value'] ?>"/> в
						<?php
						/*
						?><input name="overweight_volume" value="<?php echo $this->req_data['overweight_volume']['value'] ?>"/>
						*/
						?>
						1м<sup><small>3</small></sup>
					</p>
				</div>
				<div>
					<p>
						При весе груза более <input name="self_calc_overweight" value="<?php echo $this->req_data['self_calc_overweight']['value'] ?>"/> в 1 м<sup><small>3</small></sup> - идивидуальный просчет
					</p>
				</div>
			</div>
		</form>
	</fieldset>
</div>
