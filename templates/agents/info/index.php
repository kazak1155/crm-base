<div id="gridcontainer">
	<div class="agents-wrapper" >
		<table class='gridclass' id='agents'></table>
	</div>
	<div id="wrapper" class="wrapper">
		<div class="form-wrapper">
			<fieldset class="slide-set slide-set-hidden">
				<legend>Общие данные клиента</legend>
				<form class="gridToForm" id="agentinfo">
					<input type="hidden" name="Код" class="float_input"/>
					<div class='float_label_wrapper' style='width:calc(33% - 15px)'>
						<input type="text" name="Название_RU" class="float_input" required/>
						<label class="float_label">Название Ru</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(33% - 15px)'>
						<input type="text" name="Название_EN" class="float_input" required/>
						<label class="float_label">Название En</label>
					</div>
					<div style="display:inline-block;width:calc(33% - 9px);margin-left:5px;">
						<select class="select2me" data-placeholder="ПМ" name="Персональный_Менеджер_Код" style="width:100%">
							<?php echo $this->Core->get_lib_html(['tname'=>'Выбор_ПМ']);?>
						</select>
					</div>
					<div style="display:inline-block;width:calc(60% - 9px);margin-left:5px;">
						<select class="select2me" data-placeholder="Страна" name="Страны_Код" style="width:100%">
							<?php echo $this->Core->get_lib_html(['tname'=>'Б_Страны']);?>
						</select>
					</div>
					<div class='float_label_wrapper' style='width:calc(20% - 15px)'>
						<input type="text" name="Торговая_марка" class="float_input">
						<label class="float_label">Торговая марка</label>
					</div>
					<div class='float_label_wrapper' style="width:calc(20% - 15px)">
						<input style="text-align:right"  type="text" name="Сайт_Код" class="float_input" required readonly="true"/>
						<label style="margin-right: 30px;" class="float_label">Код TLC-ONLINE</label>
					</div>
					<div class='float_label_wrapper' style="width:calc(20% - 15px)">
						<input style="text-align:right" type="checkbox" name="Архив" class="float_input"/>
						<label style="margin-right: 30px;" class="float_label">Не использовать в статистике</label>
					</div>

				</form>
			</fieldset>
		</div>
		<hr class="blank-divider" />
		<div class="form-wrapper">
			<fieldset class="slide-set">
				<legend>Основные данные клиента</legend>
				<form class="gridToForm" id="agentcontacts">
					<input type="hidden" name="Код" subname="Контрагенты_Код" class="float_input"/>
					<input type="hidden" name="Контрагенты_Код" class="float_input"/>
					<div class='float_label_wrapper' style='width:calc(20% - 15px)'>
						<input type="text" name="Индекс" class="float_input"/>
						<label class="float_label">Индекс</label>
					</div>
					<div style="display:inline-block;width:calc(40% - 9px);margin-left:5px;">
						<select class="select2me region_province" name="Регион" data-placeholder="Регион" disabled="disabled" style="width:100%">
							<?php echo $this->Core->get_lib_html(['tname'=>'Италия_Индексы_Регионы','fields'=>["Индекс_мин + '|' + Индекс_макс",'Название']]);?>
						</select>
					</div>
					<div style="display:inline-block;width:calc(40% - 9px);margin-left:5px;">
						<select class="select2me region_province" name="Провинция" data-placeholder="Провинция" disabled="disabled" style="width:100%">
							<?php echo $this->Core->get_lib_html(['tname'=>'Италия_Индексы_Провинции','fields'=>["Индекс_мин + '|' + Индекс_макс",'Название']]);?>
						</select>
					</div>
					<div class='float_label_wrapper' style='width:calc(100% - 15px)'>
						<input type="text" name="Адрес" class="float_input"/>
						<label class="float_label">Адрес</label>
					</div>
					<div style="display:inline-block;width:calc(50% - 9px);margin-left:5px;margin-top:15px;">
						<select class="select2me" name="Город_Код" data-placeholder="Город" style="width:100%">
							<?php echo $this->Core->get_lib_html(['tname'=>'Выбор_Города']);?>
						</select>
					</div>
					<div class='float_label_wrapper' style='width:calc(50% - 15px)'>
						<input type="text" name="Часы_работы" class="float_input"/>
						<label class="float_label">Часы работы</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(25% - 15px)'>
						<input type="text" name="Контакт" class="float_input"/>
						<label class="float_label">Контакт</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(25% - 15px)'>
						<input type="text" name="Телефон" class="float_input"/>
						<label class="float_label">Телефон</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(25% - 15px)'>
						<input type="text" name="Сайт" class="float_input"/>
						<label class="float_label">Сайт</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(25% - 15px)'>
						<input placeholder="Пользователь" type="text" name="Пользователь" class="float_input" disabled="disabled"/>
						<label class="float_label">Пользователь</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(34% - 15px)'>
						<input type="text" name="Email" class="float_input"/>
						<label class="float_label">Email</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(33% - 15px)'>
						<input type="email" name="Email_Счета" multiple class="float_input"/>
						<label class="float_label">Email счета</label>
					</div>
					<div class='float_label_wrapper' style='width:calc(33% - 15px)'>
						<input name="Email_Имя" class="float_input"/>
						<label class="float_label">Email Имя</label>
					</div>
					<div class='float_label_wrapper' style="width:calc(100% - 15px);">
						<textarea name="Примечание" id="Примечание" class="float_input"></textarea>
						<label for="Примечание" class="float_label">Примечание</label>
					</div>
				</form>
			</fieldset>
		</div>
		<hr class="blank-divider" />
		<div>
			<fieldset class="slide-set slide-set-hidden slide-set-single">
				<legend>Телефоны клиента</legend>
				<table class='gridclass' id='agents_phones'></table>
			</fieldset>
		</div>
		<hr class="blank-divider" />
		<div>
			<fieldset class="slide-set slide-set-hidden slide-set-single">
				<legend>Тарифы клиента</legend>
				<table class='gridclass' id='agents_rates'></table>
			</fieldset>
		</div>
		<!--		
		<hr class="blank-divider" />

		<div>
			<fieldset class="slide-set slide-set-hidden slide-set-single">
				<legend>Юридические данные клиента</legend>
				<table class='gridclass' id='agents_acc'></table>
			</fieldset>
		</div>
		<hr class="blank-divider" />
		<div>
			<fieldset class="slide-set slide-set-hidden slide-set-single">
				<legend>Работа с клиентом</legend>
				<table class='gridclass' id='agents_comments'></table>
			</fieldset>
		</div>
		<hr class="blank-divider" />
		<div>
			<fieldset class="slide-set slide-set-hidden slide-set-single">
				<legend>Неоплаченные счета</legend>
				<table class='gridclass' id='agents_bills'></table>
			</fieldset>
		</div>
		-->
	</div>
</div>
