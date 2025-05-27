<?php
$step = isset($_GET['step']) ? $_GET['step'] : 1;
$rowid = $_GET['rowid'];
$data = json_decode($_REQUEST['row_data'],true);
?>
<div id="form_wrapper">
	<?php if($step == 1 ):?>
	<script>
		$(function() {
			console.log(eval('1 + 5 * (4 + 3)'))
			new $ajaxForm({
				eventHandlers:['keyup','change'],
				name:'form-contains',
				id:'Код',
				table:'Заказы_Состав',
				tableForm:true,
				floatLabel:true,
				add:true
			});
			new $ajaxForm({
				eventHandlers:['keyup','change'],
				name:'form-params',
				id:'Код',
				table:'Заказы_Параметры',
				floatLabel:true,
				add:true
			});
			$('#total_calculate').click(function(e){
				var super_total = 0;
				var formula;
				var payment_type = $('select[name="Способ_Оплаты_Код"]').val(),payment_type_text = $("select[name='Способ_Оплаты_Код'] option:selected" ).text();
				var over = isNaN(parseFloat($('input[name="Наценка"]').val())) ? 0 : parseFloat($('input[name="Наценка"]').val());
				var per_volume = isNaN(parseFloat($('input[name="Стоимость_куба"]').val())) ? 0 : parseFloat($('input[name="Стоимость_куба"]').val());
				if(!payment_type)
					return $.alert('Установите способ оплаты');
				if(over == 0)
					return $.alert('Установите процент наценки');
				if(per_volume == 0)
					return $.alert('Установите стоимость за куб');
				var total_price = total_price_log = total_volume = total_weight = total_quantity = 0;
				var empty_fabric = empty_volume = empty_weight = empty_quantity = false;
				var $formula = $('.formula_div');
				$.each($('input[name="Стоимость_фабрика"],input[name="Кол_во"],input[name="Вес"],input[name="Объем"]'),function(i,el){
					switch (this.name) {
						case 'Стоимость_фабрика':
							if(!el.value)
							{
								empty_fabric = true;
								return;
							}
							total_price += parseFloat(this.value);
							break;
						case 'Кол_во':
							if(!el.value)
							{
								empty_quantity = true;
								return;
							}
							total_quantity += parseInt(this.value);
							break;
						case 'Вес':
							if(!el.value)
							{
								empty_weight = true;
								return;
							}
							total_weight += parseFloat(this.value);
							break;
						case 'Объем':
							if(!el.value)
							{
								empty_volume = true;
								return;
							}
							total_volume += parseFloat(this.value);
							var test_w = $(this).closest('tr').find('input[name="Вес"]').val();
							if(parseFloat(test_w) / parseFloat(this.value) > 180)
								total_price_log += parseFloat(test_w) / 180 * 350;
							else
								total_price_log += isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value) * per_volume * parseFloat(1 + over / 100);
							break;
						default:break;
					}
				});
				if(empty_fabric)
					return $.alert('Установите нетто фабрики во все грузы.');
				if(empty_quantity)
					return $.alert('Установите количество во все грузы.');
				if(empty_volume)
				{
					if(empty_weight)
						return $.alert('Установите объем ИЛИ вес во все грузы');
				}
				if($formula.children().length > 0)
				{
					formula = '';
					$.each($formula.find('span'),function(i,el){
						var piece = el.innerHTML;
						if((/[a-zA-Z]/).test(piece))
						{
							switch(piece)
							{
								case 'Sf':
									formula.push(total_price * parseFloat(1 + over / 100)).toFixed(4);
								break;
								case 'Sw':
									formula.push(<?php echo $this->server_prm['price_swift']?>);
								break;
								case 'Tp':
									if(payment_type_text == 'Б/н')
										formula.push(<?php echo $this->server_prm['payment_cashless']?>);
									else if(payment_type_text == 'Наличные')
										formula.push(<?php echo $this->server_prm['payment_cash']?>);
									formula[i] = (total_price * parseFloat(parseInt(formula[i]) / 100 )).toFixed(4);
								break;
								case 'Sl':
									formula.push(total_price_log.toFixed(4));
								break;
								default:break;
							}
						}
						else if((/\%/).test(piece))
						{
							console.log(piece)
						}
						else
							formula.push(piece)
					});
					for(var i = 0;i< formula.length; i++)
					{
						if((/[\+\-\/\*]/).test(formula[i]))
						{
							switch(formula[i])
							{
								case '+':
									if(super_total > 0)
										super_total += parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) + parseFloat(formula[i + 1]);
								break;
								case '-':
									if(super_total > 0)
										super_total -= parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) - parseFloat(formula[i + 1]);
								break;
								case '/':
									if(super_total > 0)
										super_total = super_total / parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) / parseFloat(formula[i + 1]);
								break;
								case '*':
									if(super_total > 0)
										super_total = super_total * parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) * parseFloat(formula[i + 1]);
								break;
								default:break;
							}
						}
					}
				}
				else
				{
					formula = $("select[name='Формулы_расчета'] option:selected" ).text();
					if(!formula)
						return $.alert('Формула расчета не установлена');
					formula = formula.split(' ');
					for(var i = 0;i< formula.length; i++)
					{
						if((/[a-zA-Z]/).test(formula[i]))
						{
							switch(formula[i])
							{
								case 'Sf':
									//formula[i] = (total_price * parseFloat(1 + over / 100)).toFixed(4);
									formula[i] = total_price;
								break;
								case 'Sw':
									formula[i] = <?php echo $this->server_prm['price_swift']?>;
								break;
								case 'Tp':
									if(payment_type_text == 'Б/н')
										formula[i] = <?php echo $this->server_prm['payment_cashless']?>;
									else if(payment_type_text == 'Наличные')
										formula[i] = <?php echo $this->server_prm['payment_cash']?>;
									//formula[i] = (total_price * parseFloat(parseInt(formula[i]) / 100 )).toFixed(4);
									formula[i] = ((total_price + parseInt(<?php echo $this->server_prm['price_swift']?>)) * parseFloat(parseInt(formula[i]) / 100 )).toFixed(4);
								break;
								case 'Sl':
									formula[i] = total_price_log.toFixed(4);
								break;
								default:break;
							}
						}
					}
					formula = formula.join(' ');
					console.log(eval(formula))
					/*
					for(var i = 0;i< formula.length; i++)
					{
						if((/[\+\-\/\*]/).test(formula[i]))
						{
							switch(formula[i])
							{
								case '+':
									if(super_total > 0)
										super_total += parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) + parseFloat(formula[i + 1]);
								break;
								case '-':
									if(super_total > 0)
										super_total -= parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) - parseFloat(formula[i + 1]);
								break;
								case '/':
									if(super_total > 0)
										super_total = super_total / parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) / parseFloat(formula[i + 1]);
								break;
								case '*':
									if(super_total > 0)
										super_total = super_total * parseFloat(formula[i + 1]);
									else
										super_total += parseFloat(formula[i - 1]) * parseFloat(formula[i + 1]);
								break;
								default:break;
							}
						}
					}
					*/
				}

				super_total = super_total.toFixed(2);
				$('input[name="s_Ит_all"]').val(super_total);
				$.each($('input[name="Итого_real"]'),function(i,el)
				{
					var price_proportion;
					price_proportion = parseFloat($(this).closest('tr').find('input[name="Стоимость_фабрика"]').val()) / parseFloat(total_price);
					this.value = (super_total * price_proportion).toFixed(2);
				});
			});
			$.contextMenu({
				selector : '#function_params',
				animation : {
					duration : 25,
					show : 'fadeIn',
					hide : 'fadeOut'
				},
				zIndex:5,
				reposition : false,
				items:{
					Sf : {
						name : "Вставить Sf",
						icon : "paste"
					},
					Sw : {
						name : "Вставить Sw",
						icon : "paste"
					},
					Tp : {
						name : "Вставить Tp",
						icon : "paste"
					},
					Sl : {
						name : "Вставить Sl",
						icon : "paste"
					},
				},
				callback:function(key, options) {
					var formula_div = document.getElementsByClassName('formula_div');
					var last_formula_div = document.getElementById('lastaction');
					var operation_wrapper = document.createElement('div');
					var operation_holder = document.createElement('span');
					if(last_formula_div !== null && $(last_formula_div).find('span').text() == key)
						return;
					if(last_formula_div !== null && (last_formula_div.className !== 'formula_operator' && last_formula_div.className !== 'formula_bracket'))
						return;
					if(last_formula_div !== null)
						last_formula_div.removeAttribute('id');
					operation_wrapper.id = 'lastaction';
					operation_holder.innerHTML = key;
					operation_wrapper.className = 'formula_special';
					operation_wrapper.appendChild(operation_holder);
					formula_div[0].appendChild(operation_wrapper);
				}
			});
			$('#function_params').keydown(function(event)
			{
				event.preventDefault();
				var formula_div = document.getElementsByClassName('formula_div');
				var last_formula_div = document.getElementById('lastaction');
				var operation_wrapper = document.createElement('div');
				var operation_holder = document.createElement('span');
				var operation_holder_classname,v = event.key;
				if(event.shiftKey == true)
				{
					if (event.keyCode == 56 || event.keyCode == 189 || event.keyCode == 187 || event.keyCode == 191)
					{
						if(event.keyCode == 189)
							v = '-';
						if(event.keyCode == 190 && (/[a-zA-Z]/).test(event.key) == true)
							v = '.';
						operation_holder_classname = 'formula_operator';
					}
					else if(event.keyCode == 53)
					{
						operation_holder_classname = 'formula_unit';
					}
					else if(event.keyCode == 57 || event.keyCode == 48)
					{
						operation_holder_classname = 'formula_bracket';
					}
					else
						return
				}
				else
				{
					if ((event.keyCode > 31 && event.keyCode <= 57) || (event.keyCode == 188 || event.keyCode == 190))
					{
						if(event.keyCode == 188)
							v = '.'
						operation_holder_classname = 'formula_unit';
					}
					else if(event.keyCode == 111 || event.keyCode == 106 || event.keyCode == 109 || event.keyCode == 107)
					{
						operation_holder_classname = 'formula_operator';
					}
					else if(event.keyCode == 8)
					{
						if(last_formula_div !== null)
						{
							$(last_formula_div).prev().attr('id','lastaction');
							$(last_formula_div).remove();
							return;
						}
						else
							return;
					}
					else
						return;
				}
				if(last_formula_div !== null)
				{
					last_formula_div.removeAttribute('id');
					operation_wrapper.id = 'lastaction';
					if(last_formula_div.className == operation_holder_classname)
					{
						last_formula_div.id = 'lastaction'
						var v_old = $(last_formula_div).find('span').text();
						if((v_old.indexOf('.') > -1 && v == '.'))
							return;
						if(v_old.indexOf('%') > -1 && v == '%')
							return;
						if(operation_holder_classname == 'formula_operator' && v_old == v)
							return;
						$(last_formula_div).find('span').text(v_old + v);
					}
					else
					{
						operation_wrapper.id = 'lastaction';
						operation_holder.innerHTML = v;
						operation_wrapper.className = operation_holder_classname;
						operation_wrapper.appendChild(operation_holder);
						formula_div[0].appendChild(operation_wrapper);
					}
				}
				else
				{
					if(v == '.' || v == '%')
						return;
					operation_wrapper.id = 'lastaction';
					operation_holder.innerHTML = v;
					operation_wrapper.className = operation_holder_classname;
					operation_wrapper.appendChild(operation_holder);
					formula_div[0].appendChild(operation_wrapper);
				}
			})
		});
	</script>
	<form id="form-contains">
		<table>
			<?php
			$order_list = $this->catchPDO("SELECT * FROM Заказы_Состав WHERE Заказы_Код = $rowid")->fetchAll(PDO::FETCH_ASSOC);
			$order_list = $this->iconvKeys($order_list, 'cp1251', 'utf-8');
			foreach ($order_list as $rownum => $row)
			{
				?>
				<tr>
					<td><input type="hidden" name="Код" value="<?php echo $row['Код'] ?>"/><input type="submit" style="display:none"/></td>
					<td>
						<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
							<select class="select2me" data-placeholder="Вид груза" name="Вид_Груза_Код" style="width:100%" required="required" disabled="disabled">
								<?php echo $this->get_lib_html(['tname'=>'Б_Виды_Груза','selected'=>$row['Вид_Груза_Код']]);?>
							</select>
						</div>
					</td>
					<td>
						<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
							<select class="select2me" data-placeholder="Валюта" name="Валюта_Код" style="width:100%;" required="required">
								<?php echo $this->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$row['Валюта_Код']]);?>
							</select>
						</div>
					</td>
					<td width="400px">
						<div class='float_label_wrapper' style="width:calc(100% - 15px);">
							<textarea name="Описание" id="Описание" class="float_input"><?php echo $row['Описание']?></textarea>
							<label for="Описание" class="float_label">Описание</label>
						</div>
					</td>
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Артикул" class="float_input" disabled="disabled" value="<?php echo $row['Артикул']?>"/>
							<label class="float_label">Артикул</label>
						</div>
					</td>
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Стоимость_фабрика" class="float_input numeric-only" value="<?php echo $row['Стоимость_фабрика']?>" required/>
							<label class="float_label">Ц.фабр</label>
						</div>
					</td>
					<!--
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Ит_фабр" class="float_input" value="<?php echo $f_summ ?>" disabled="disabled"/>
							<label class="float_label">Ит.фабр</label>
						</div>
					</td>
					-->
					<td width="80px">
						<div class='float_label_wrapper'>
							<input type="text" name="Объем" class="float_input numeric-only" value="<?php echo $row['Объем']?>" required/>
							<label class="float_label">Объем</label>
						</div>
					</td>
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Кол_во" class="float_input integer-only" value="<?php echo $row['Кол_во']?>" required/>
							<label class="float_label">Кол-во</label>
						</div>
					</td>
					<!--
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Ит_лог" class="float_input" disabled="disabled"/>
							<label class="float_label">Ит.лог</label>
						</div>
					</td>
					-->
					<td width="70px">
						<div class='float_label_wrapper'>
							<input type="text" name="Вес" class="float_input numeric-only"/>
							<label class="float_label">Вес</label>
						</div>
					</td>
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Итого_real" class="float_input" disabled="disabled"/>
							<label class="float_label">Итого</label>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td></td>
				<td></td><td></td><td></td><td></td>
				<td></td><td></td><td></td><td></td>
				<td width="100px">
					<div class='float_label_wrapper' style="width:calc(100% - 10px)">
						<input type="text" name="s_Ит_all" class="float_input" disabled="disabled"/>
						<label class="float_label">∑ итого</label>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<form id="form-calc">
		<fieldset>
			<legend>Параметры и условия ввода</legend>
			<ul>
				<li><strong>Формула должна придерживатся арифметических правил!</strong></li>
				<li>+ - / * - доступные арифметические действия</li>
				<li>Sf - Сумма нетто фабрики</li>
				<li>Sw - Swift</li>
				<li>Tp - Процент с оплаты</li>
				<li>Sl - Cумма логистика</li>
			</ul>
		</fieldset>
		<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<select class="select2me" data-placeholder="Формулы расчета" name="Формулы_расчета" style="width:100%">
				<?php echo $this->get_lib_html(['tname'=>'З_Б_Формулы_Расчета']);?>
			</select>
		</div>
		<div class='float_label_wrapper formula_wrapper' style="width:calc(100% - 15px);">
			<div class="formula_div"></div>
			<textarea name="function_params" id="function_params" class="float_input formula_textarea" style="resize:none" ></textarea>
			<label for="function_params" class="float_label">Другая формула расчета</label>
		</div>
	</form>
	<form id="form-params">
		<input type="hidden" name="Код" value="<?php echo $rowid ?>"/>
		<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Способ оплаты" name="Способ_Оплаты_Код" style="width:100%" required="required">
				<?php echo $this->get_lib_html(['tname'=>'З_Б_Способы_Оплаты','selected'=>$data['Способ_Оплаты_Код']]);?>
			</select>
		</div>
		<div style="display:inline-block;width:calc(50% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Валюта логистика" name="Валюта_логистика" style="width:100%" required>
				<?php echo $this->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$data['Валюта_логистика']]);?>
			</select>
		</div>
		<div style="display:inline-block;width:calc(50% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Валюта договора" name="Валюта_договора" style="width:100%" required>
				<?php echo $this->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$data['Валюта_договора']]);?>
			</select>
		</div>
		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Стоимость_куба" class="float_input integer-only" value="<?php echo $data['Стоимость_куба'] ?>" required/>
			<label class="float_label">y/e за куб</label>
		</div>
		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Наценка" class="float_input integer-only" required value="<?php echo preg_replace('~\x{00a0}~siu','',$data['Наценка']) ?>"/>
			<label class="float_label">Наценка %</label>
		</div>
	</form>
	<hr class="ni-divider" />
	<button id="total_calculate" class="niceButton_blue">Расчитать</button>
	<?php endif; ?>
	<?php if($step == 2):?>
		<script>
		var rowid = <?php echo $rowid ?>;
		$(function() {
			new jqGrid$({
				defaultPage:false,
				subgrid:true,
				pseudo_subrgid:true,
				delOpts:true,
				formpos:false,
				main:false,
				name:'order_cross',
				table:'Заказы_Кросс_Курс',
				id:'Код',
				hideBottomNav:true,
				beforeSubmitCell:true,
				useLs:false,
				navGrid:true,
				navGridOptions:{add:true,clearFilters:false},
				subgridpost:{
					mainid:rowid,
					mainidName:'Заказы_Код'
				},
				cn:[
					"Код","Заказы_Код","Валюта исходящая","Валюта входящая","Курс"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Валюта_first",index:"Валюта_first",width:100,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
						}
					},

					{
						name: "Валюта_second",index:"Валюта_second",width:100,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
						},
						editoptions:{
							disabled:true,
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
						}
					},

					{
						name: "Курс",index:"Курс",width:50,formatter:floatFormatter
					}
				],
				options:{
					shrinkToFit:true,
					cellEdit:true,
					height:200,
					caption:'Кросс курсы заказа'
				}
			});
		})
		</script>
		<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<table class='gridclass' id='order_cross'></table>
		</div>
	<?php endif; ?>
	<?php if($step == 3):?>
		<script>
		var rowid = <?php echo $rowid ?>;
		$(function() {
			new jqGrid$({
				defaultPage:false,
				subgrid:true,
				pseudo_subrgid:true,
				main:false,
				name:'order_services',
				beforeSubmitCell:true,
				table:'Заказы_Услуги',
				id:'Код',
				delOpts:true,
				hideBottomNav:true,
				formpos:false,
				useLs:false,
				navGrid:true,
				navGridOptions:{add:true,clearFilters:false},
				subgridpost:{
					mainid:rowid,
					mainidName:'Заказы_Код'
				},
				cn:[
					"Код","Заказы_Код","Услуга","Стоимость","Примечание"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Услуга_Код",index:"Услуга_Код",width:100,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->get_lib(['tname'=>'Б_Виды_Услуг']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Виды_Услуг'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Виды_Услуг',getNull:false},opts,this); }
						}
					},

					{
						name: "Значение",index:"Значение",width:100,formatter:floatFormatter,
					},

					{
						name: "Примечание",index:"Примечание",width:250,align:"left",
						cellattr:textAreaCellAttr,
						edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
					}
				],
				options:{
					shrinkToFit:true,
					cellEdit:true,
					height:200,
					caption:'Дополнительные услуги заказа'
				}
			})
		})
		</script>
		<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<table class='gridclass' id='order_services'></table>
		</div>
	<?php endif; ?>
	<hr class="ni-divider" />
	<!--
	<div class='float_label_wrapper navigation_wrapper' style="">
		<?php if($step > 1):?>
		<div style="text-align:left;">
			<i id="prev" style="cursor:pointer;font-size:50px;color:#4479BA" class="fa fa-arrow-circle-left"></i>
		</div>
		<?php endif; ?>
		<div style="text-align:right;">
			<i id="next" style="cursor:pointer;font-size:50px;color:<?php if($step != 3) {?>#4479BA<?php } else {?>green<?php } ?>" class="fa <?php if($step != 3) {?>fa-arrow-circle-right <?php } else {?> fa-check-circle <?php } ?>"></i>
		</div>
	</div>
	-->
</div>
