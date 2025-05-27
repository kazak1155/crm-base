	<div class="sel_cont">
		<select id="hail_sel" class="select2me" data-selectops='{"allowClear": false,"width":"100%"}'>
			<?php echo $this->Core->get_lib_html(['tname'=>'Б_Рейсы','order_by'=>'DESC']);?>
		</select>
	</div>
	<div>
		<table class='gridclass' id='delivery'></table>
	</div>
