<div id="gridcontainer">
	<div class="agents-wrapper">
		<table class='gridclass' id='agents'></table>
	</div>
	<div class="statistics-wrapper">
		<div style="display:inline-block;width:calc(100% - 9px);margin-left:5px;margin-top:15px;margin-bottom:15px;">
			<select class="select2me stat-type" data-selectops='{"allowClear": false}' data-placeholder="Выберите тип статистики" style="width:100%">
				<?php
					echo $this->Core->get_lib_html([
						'tname'=>'Статистика',
						'srv'=>true,
						'cache'=>false,
						'fields'=>['Конструктор','Название'],
						'filters'=>[['field'=>'Конструктор','op'=>'cn','data'=>'client']]
					])
				?>
			</select>
		</div>
		<hr class="ni-divider" />
		<div class="statistics-content"></div>
	</div>
</div>
