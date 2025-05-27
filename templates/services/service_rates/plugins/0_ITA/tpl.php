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
						Доставка в пределах Италии <input name="price" value="<?php echo $this->req_data['price']['value'] ?>"/>€.
						<br />
						За пределами Италии <input name="out_destination_price" value="<?php echo $this->req_data['out_destination_price']['value'] ?>"/>€.
					</p>
				</div>
			</div>
		</form>
	</fieldset>
</div>
