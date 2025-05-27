<div class="sel_cont">
	<select class="select2me" data-selectops='{"allowClear": false,"width":"100%"}'>
		<?php echo $this->Core->get_lib_html(['tname'=>'Б_Рейсы','selected'=>$_REQUEST['haul'],'cache'=>false,'order_by'=>'DESC']);?>
	</select>
	<textarea id="journal"></textarea>
</div>
<div>
	<table class='gridclass' id='route'></table>
</div>
