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
						Таможенное оформление = Вес * 
						<input name="pay_per_m" value="<?php echo $this->req_data['pay_per_m']['value'] ?>"/>
						Евро.  (По умолчанию)
					</p>
				</div>
				<div>
					<p>
						Фрахт = Объем * 
						<input name="pay_per_v_fraht" value="<?php echo $this->req_data['pay_per_v_fraht']['value'] ?>"/>
						Евро. (По умолчанию)
					</p>
				</div>
				<div>
					<p>
						Коммерция = Объем * 
						<input name="pay_per_v_kom" value="<?php echo $this->req_data['pay_per_v_kom']['value'] ?>"/>
						Евро.
					</p>
				</div>				
				<div>
					<p>
						Оформление экспортной =
						<input name="EX1" value="<?php echo $this->req_data['EX1']['value'] ?>"/>
						Евро.
					</p>
				</div>	
				<div>
					<p>
						Складская обработка = Объем * 
						<input name="warehouse_handling" value="<?php echo $this->req_data['warehouse_handling']['value'] ?>"/>
						Евро.
					</p>
				</div>
				<div>
					<p>
						Минимальная стоимость заказа: <input name="min_price" value="<?php echo $this->req_data['min_price']['value'] ?>"/> Евро за 1м3 (без учета сбора на ин.склад)
					</p>
				</div>											
			</div>
		</form>
	</fieldset>
</div>
