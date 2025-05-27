<!DOCTYPE html>
<html>
<head>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/php/main/core.php');
$rowid = $_REQUEST['rowid'];
$data = json_decode($_REQUEST['row_data'],true);
$Core = new Core;
$Core->con_database('sales');
$Core->get_meta();
$Core->get_files();
$step = isset($_GET['step']) ? $_GET['step'] : 1;
?>
<script type="text/javascript">
$(function() {
	var step = parseInt(<?php echo $step ?>);
	$('#next,#prev').click(function(e)
	{
		if(step == 3 && this.id != 'prev')
		{
			$.ajaxShort({
				data:{
					oper:'edit',
					tname:'Заказы_Параметры',
					tid:'Код',
					id:<?php echo $rowid ?>,
					'Сформирован':1
				},
				success:function(){
					window.parent.$('.confirm-html ').dialog('close');
				}
			});
		}
		if($('#form-contains').length > 0 && $('#form-params').length > 0 && this.id != 'prev')
		{
			if($('#form-contains')[0].checkValidity() == false)
			{
				$('#form-contains').find(':submit').click();
				return false;
			}
			if($('#form-params')[0].checkValidity() == false)
			{
				$('#form-params').find(':submit').click();
				return false;
			}
		}
		switch(this.id){
			case 'next':
				step++
			break;
			case 'prev':
				--step;
			break;
		}
		var s = document.location.search;
		if(s.indexOf('step') > 0)
			s = s.substr(0,s.indexOf('&step=')) + '&step='+ step;
		else
			s = s + '&step='+ step;
		document.location.search = s;
	});
})
</script>
<style>
.select2-container--default .select2-selection--single, .select2-selection__arrow
{
    height:25px !important;
    outline: none;
}
.select2-selection__rendered
{
    line-height:23px !important;
}
.float_label_wrapper
{
	min-width: 0px;
}
#form-contains
{
	overflow-y: scroll;
	height: 300px;
}
.ni-divider
{
  border: 0;
  height: 1px;
  background-image: -webkit-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
  background-image: -moz-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
  background-image: -ms-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
  background-image: -o-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
}
.navigation_wrapper
{
	position: fixed;
	bottom:0%;
	width:calc(100% - 10px);
	text-align:right;
}
.navigation_wrapper div{
	display: inline-block;
	width: calc(50% - 5px);
}
textarea
{
	resize: none;
	height:50px;
	margin-top: 18px;
}
.niceButton_blue
{
	width:100%;
	margin: 5px 0px;
	padding:5px;
}
</style>
</head>
<body>
<div id="form_wrapper">
	<?php if($step == 1 ):?>
	<script>
		$(function() {
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
			$('#get_defaults').click(function(e){
				var payment_type = $('select[name="Способ_Оплаты_Код"]').val();
				if(!payment_type)
					return $.alert('Установите способ оплаты');
				/*var price_per_volume = <?php echo $Core->server_prm['price_per_volume']?>;
				var price_swift = <?php echo $Core->server_prm['price_swift']?>;
				var paymen_cash = <?php echo $Core->server_prm['payment_cash']?>;
				var payment_cashless = <?php echo $Core->server_prm['payment_cashless']?>;*/
			});
			$('input[name="Стоимость_фабрика"]').keyup(function(e){
				var over = isNaN(parseFloat($('input[name="Наценка"]').val())) ? 0 : parseFloat($('input[name="Наценка"]').val());
				var cost = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
				cost = parseFloat(1 + over / 100) * cost;
				$(this).closest('tr').find('input[name="Ит_фабр"]').val(cost.toFixed(2));
			})
			$('input[name="Ит_фабр"]').change(function(e){
				var sum = 0;
				$.each($('input[name="Ит_фабр"]'),function(e){
					sum += isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
				});
				$('input[name="s_Ит_фабр"]').val(sum.toFixed(2));
			});
			$('input[name="Объем"]').keyup(function(e){
				var over = isNaN(parseFloat($('input[name="Наценка"]').val())) ? 0 : parseFloat($('input[name="Наценка"]').val());
				var volume = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
				var per_volume = isNaN(parseFloat($('input[name="Стоимость_куба"]').val())) ? 0 : parseFloat($('input[name="Стоимость_куба"]').val());
				var cost = volume * per_volume * parseFloat(1 + over / 100);
				$(this).closest('tr').find('input[name="Ит_лог"]').val(cost.toFixed(2));
			})
			$('input[name="Ит_лог"]').change(function(e){
				var sum = 0;
				$.each($('input[name="Ит_лог"]'),function(e){
					sum += isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
				});
				$('input[name="s_Ит_лог"]').val(sum.toFixed(2));
			});
			$('input[name="Наценка"]').keyup(function(e){
				var over = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
				$.each($('input[name="Стоимость_фабрика"],input[name="Объем"]'),function(i,n){
					var cost;
					switch (this.name) {
						case 'Стоимость_фабрика':
							cost = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
							$(this).closest('tr').find('input[name="Ит_фабр"]').val((cost * parseFloat(1 + over / 100)).toFixed(2));
							$('input[name="Ит_фабр"]').trigger('change');
							break;
						case 'Объем':
							var per_volume = isNaN(parseFloat($('input[name="Стоимость_куба"]').val())) ? 0 : parseFloat($('input[name="Стоимость_куба"]').val());
							var volume = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
							cost = volume * per_volume * parseFloat(1 + over / 100);
							$(this).closest('tr').find('input[name="Ит_лог"]').val(cost.toFixed(2));
							$('input[name="Ит_лог"]').trigger('change');
							break;
					}
				});
			});
			$('input[name="Стоимость_куба"]').keyup(function(e){
				var over = isNaN(parseFloat($('input[name="Наценка"]').val())) ? 0 : parseFloat($('input[name="Наценка"]').val());
				var per_volume = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
				$.each($('input[name="Объем"]'),function(i,n){
					var volume = isNaN(parseFloat(this.value)) ? 0 : parseFloat(this.value);
					var cost = volume * per_volume * parseFloat(1 + over / 100);
					$(this).closest('tr').find('input[name="Ит_лог"]').val(cost.toFixed(2));
					$('input[name="Ит_лог"]').trigger('change');
				});
			});
		});
	</script>
	<form id="form-contains">
		<table>
			<?php
			$order_list = $Core->catchPDO("SELECT * FROM Заказы_Состав WHERE Заказы_Код = $rowid")->fetchAll(PDO::FETCH_ASSOC);
			$order_list = $Core->iconvKeys($order_list, 'cp1251', 'utf-8');
			$f_summ_e = $l_summ_e = 0;
			foreach ($order_list as $rownum => $row)
			{
				$f_summ = number_format(floatval($row['Стоимость_фабрика']) * (1 + floatval($data['Наценка']) / 100),2,'.','');
				$f_summ_e += $f_summ;
				$l_summ = number_format(floatval($row['Объем']) * floatval($data['Стоимость_куба']) * (1 + floatval($data['Наценка']) / 100),2,'.','');
				$l_summ_e += $l_summ;
				?>
				<tr>
					<td><input type="hidden" name="Код" value="<?php echo $row['Код'] ?>"/><input type="submit" style="display:none"/></td>
					<td>
						<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
							<select class="select2me" data-placeholder="Вид груза" name="Вид_Груза_Код" style="width:100%" required="required" disabled="disabled">
								<?php echo $Core->get_lib_html(['tname'=>'Б_Виды_Груза','selected'=>$row['Вид_Груза_Код']]);?>
							</select>
						</div>
					</td>
					<td>
						<div style="display:inline-block;width:calc(100% - 9px);margin-top:15px;margin-left:5px;">
							<select class="select2me" data-placeholder="Валюта <i class='fa fa-asterisk'></i>" name="Валюта_Код" style="width:100%" required="required">
								<?php echo $Core->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$row['Валюта_Код']]);?>
							</select>
						</div>
					</td>
					<td width="300px">
						<div class='float_label_wrapper' style="width:calc(100% - 15px);">
							<textarea name="Описание" id="Описание" class="float_input"><?php echo $row['Описание']?></textarea>
							<label for="Примечание" class="float_label">Описание</label>
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
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Ит_фабр" class="float_input" value="<?php echo $f_summ ?>" disabled="disabled"/>
							<label class="float_label">Ит.фабр</label>
						</div>
					</td>
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
					<td width="100px">
						<div class='float_label_wrapper'>
							<input type="text" name="Ит_лог" class="float_input" disabled="disabled" value="<?php echo $l_summ?>"/>
							<label class="float_label">Ит.лог</label>
						</div>
					</td>
					<td width="70px">
						<div class='float_label_wrapper'>
							<input type="text" name="Вес" class="float_input numeric-only" value="<?php echo $row['Вес']?>"/>
							<label class="float_label">Вес</label>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td></td><td></td><td></td><td></td><td></td><td></td>
				<td width="100px">
					<div class='float_label_wrapper'>
						<input type="text" name="s_Ит_фабр" class="float_input" value="<?php echo $f_summ_e ?>" disabled="disabled"/>
						<label class="float_label">∑ Ит.фабр</label>
					</div>
				</td>
				<td></td><td></td>
				<td width="100px">
					<div class='float_label_wrapper'>
						<input type="text" name="s_Ит_лог" class="float_input" value="<?php echo $l_summ_e ?>" disabled="disabled"/>
						<label class="float_label">∑ Ит.лог</label>
					</div>
				</td>
			</tr>
			<tr>
				<td></td><td></td><td></td><td></td>
				<td></td><td></td>
				<td width="400px" colspan="4">
					<div class='float_label_wrapper' style="width:calc(100% - 10px)">
						<input type="text" name="s_Ит_all" class="float_input" value="<?php echo $l_summ_e ?>" disabled="disabled"/>
						<label class="float_label">∑ итого</label>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<form id="form-params">
		<input type="hidden" name="Код" value="<?php echo $rowid ?>"/>
		<div style="display:inline-block;width:calc(50% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Способ оплаты" name="Способ_Оплаты_Код" style="width:100%" required="required">
				<?php echo $Core->get_lib_html(['tname'=>'З_Б_Способы_Оплаты','selected'=>$data['Способ_Оплаты_Код']]);?>
			</select>
		</div>
		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Swift" class="float_input numeric-only" value="<?php echo $data['Swift'] ?>" required disabled/>
			<label class="float_label">Swift</label>
		</div>
		<div style="display:inline-block;width:calc(50% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Валюта логистика" name="Валюта_логистика" style="width:100%" required>
				<?php echo $Core->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$data['Валюта_логистика']]);?>
			</select>
		</div>
		<div style="display:inline-block;width:calc(50% - 9px);margin-top:15px;margin-left:5px;">
			<select class="select2me" data-placeholder="Валюта договора" name="Валюта_договора" style="width:100%" required>
				<?php echo $Core->get_lib_html(['tname'=>'srv_Фин_З_Б_Валюты','selected'=>$data['Валюта_договора']]);?>
			</select>
		</div>
		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Стоимость_куба" class="float_input integer-only" value="<?php echo $data['Стоимость_куба'] ?>" required disabled/>
			<label class="float_label">y/e за куб</label>
		</div>

		<div class='float_label_wrapper' style="width:calc(50% - 15px);">
			<input type="text" name="Наценка" class="float_input integer-only" required value="<?php echo preg_replace('~\x{00a0}~siu','',$data['Наценка']) ?>"/>
			<label class="float_label">Наценка %</label>
		</div>
	</form>
	<hr class="ni-divider" />
	<button id="get_defaults" class="niceButton_blue">get defaults</button>
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
							value:<?php echo $Core->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
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
							value:<?php echo $Core->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
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
							value:<?php echo $Core->get_lib(['tname'=>'Б_Виды_Услуг']) ?>
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
</div>
</body>
</html>
