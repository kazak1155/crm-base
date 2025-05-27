<?php
error_reporting(0);
$currLayer = 'horizont2';
$startMode = 0;
if($_REQUEST['oid'] == 1){
	$currLayer = 'horizont14';
	$startMode = 1;
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/tload.php";
	$Cache = new WinCache();
	require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/saveform.php';
	?>
	<link href="/css/js/jquery-ui2.css" rel="stylesheet" type="text/css" />
	<link href="/css/js/chosen2.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/css/grid/jquery_flexbox.js"></script>
	<script type="text/javascript" src="/css/grid/jquery-ui.min.js"></script>
	<script src="/js/jqgrid/i18n/grid.locale-ru2.js" type="text/javascript"></script>
	<script type="text/javascript" src="/js/plugins/jqgrid/jquery.jqGrid.min.js"></script>
	<script src="/js/jquery/extend/moment.js" type="text/javascript"></script>
	<script src="/js/jquery/extend/moment-weekday-calc.js" type="text/javascript"></script>
	<style>
		* {
			font-family: Verdana;
		}
		.llong{ margin: 0; padding: 0; border: 0; width: 200px;}
		.query{display:none;}
		.ui-jqgrid .ui-pg-input {height:20px;}
		.ui-jqgridssssss .ui-jqgrid-pager {height:36px;}
		.ui-jqgrid, .ui-jqgrid-pager{font-size:12px;}
		.ui-icon, .ui-pg-div, #Архив {font-family: Verdana; font-size:10px;display: inline-block;white-space:nowrap;}
		.ui-paging-info, td #input_pager, .ui-pg-input, .ui-pg-selbox option, .ui-paging-pager tbody tr td, .ui-paging-pager tbody tr td select, .ui-jqgrid .ui-pg-selbox{font-size:14px;}
		input.w_100, select.w_100, textarea.w_100{
			width:100%;font-size:100%;
			box-sizing:content-box;
			-ms-box-sizing:content-box;
			-moz-box-sizing:content-box;
			-webkit-box-sizing:content-box;
			margin: 3px 0;
			padding: 0;
			border: 0;
		}
		td#pager-left, .ui-jqgrid .ui-pg-table td {vertical-align: top;}
		#gbox_list5, #gview_list5, .ui-jqgrid-bdivsssss{width:1250px;}
		#shader, #shader2, #shader3, #shader4, #shader5 {position: absolute;top:0;left:0;height: 100%;min-height: 100%;width: 100%;background: #000;filter:alpha(opacity=40, style=0);opacity: 0.4;z-index:300;}
		.chosen-container, .chosen-container-single, .chosen-container-active, .chosen-with-drop{margin:4px 0px;}
		.chosen-container {font-size:12px;}
		.chosen-single span{height:18px;}
	</style>
	<?php
}
$Core = new Core;
?>
<?php $currDate = date('Y') . '-' . date('m') . '-' . date('d'); ?>
<style>
	.ttitle{font-size:12px;}
</style>
<script>
	//alert('00000000000000000000000000');
	//alert('<?=$currDate;?>');
	$(document).ready(function () {
		$('#Дата').datepicker({dateFormat: "dd-mm-yy"});  // Привязать вызов календаря к полю с CSS идентификатором #calendar
		$('#Крайний_срок').datepicker({dateFormat: "dd-mm-yy"});  // Привязать вызов календаря к полю с CSS идентификатором #calenda
	});
	function getNewTemplate(tmpl) { //читываем из базы ыбранный шаблон
		$('#Пользователю').val('');
		$('#Группе').val('');
		if (tmpl == '') {
			$('#Тип_Обращения').val('');
			$('#Тип_Контакта').val('');
			$('#Тип_Обращения').val('');
			$('#Тема').val('');
			$('#Описание_задачи').val('');
			$('#Пользователю').val('');
			$('#Группе').val('');
			$('#Дата').val('');
		} else {
			$.post("/core/reqsave?m=crm_template", {m: "crm_template", t: tmpl},
				onAjaxSuccess
			);
		}
	}
	function dataOver(dt){
		var dat = String(dt);
		var dataa = dat.split('-');
		var out = dataa[2] + '-' + dataa[1] + '-' + dataa[0];
		return out;
	}
	function onAjaxSuccess(data) { // забрасываем шаблон в соответствующие поля
		//alert(data);
		var daArray = jQuery.parseJSON(data);
		var user;
		var group;
		for (key in daArray) {
			if (key == 'Тип_Контакта' || key == 'Тип_Обращения' || key == 'Тема') {
				$('#' + key).val(daArray[key]).trigger("chosen:updated");
			} else if (key == 'Дата') {
				var newDt = plusDni('<?=$currDate;?>', (daArray[key] * 1));
				var nDt = dataOver(newDt);
				$('#' + key).val(nDt);
			} else if (key == 'Пользователю') {
				user = daArray[key];
			} else if (key == 'Группе') {
				if(daArray[key] != '' && daArray[key] != 'NaN'){
					group = daArray[key];
				}
			} else {
				$('#' + key).val(daArray[key]);
			}
		}//После отработки Аякса правим селекторы
		$("#Группе option[value='" + group + "']").prop("selected", true);
		<?php
		if($_REQUEST['oid'] == 1){
		?>
		<?php
		}else{
		?>
			takeUsers('', group, 'Пользователю');
			$("#Пользователю option[value='" + user + "']").prop("selected", true);
		<?php
		}
		if($_REQUEST['oid'] == 1){
		?>
		FieldUpdate('Контрагент_Код','callCrm','contragent');
		<?php
		}
		?>

	}
	function plusDni(inDate, inDays) { //Вычисляем количество рабочих дней между датами
		var o_Date = getDate(inDate, 0); //Получаем объект первой даты
		var n_Date = getDate(inDate, inDays); //олучаем объект второй даты
		var vdays = moment().isoWeekdayCalc([o_Date['y'], o_Date['m'], o_Date['d']], [n_Date['y'], n_Date['m'], n_Date['d']], [6, 7]); //Вычисляем количество выходных между ними
		var nDays = Number(vdays);
		var NDays = Number(inDays);
		var D = new Date(inDate);
		D.setDate(D.getDate() + nDays + NDays); //Прибавляем к текущей дате сдвиг и количество выходных между датами
		var curr_date = D.getDate();
		curr_date = String(curr_date);
		var curr_month = D.getMonth() + 1;
		curr_month = String(curr_month);
		var curr_year = D.getFullYear();
		if (curr_month.length < 2) {
			curr_month = '0' + curr_month;
		}
		if (curr_date.length < 2) {
			curr_date = '0' + curr_date;
		}
		var formated_date = curr_year + "-" + curr_month + "-" + curr_date; //Форматируем окончательную дату
		return formated_date;
	}
	function getDate(inDate, sh = 0) { //Функция отдает объект даты по начальной дате и сдвигу в определенное количество дней
		var D = new Date(inDate);
		var shif = Number(sh);
		if (shif > 0) {
			D.setDate(D.getDate() + shif);
		}
		var formated_olddate = {};
		formated_olddate['d'] = D.getDate();
		formated_olddate['m'] = D.getMonth();
		formated_olddate['M'] = D.getMonth() + 1;
		formated_olddate['y'] = D.getFullYear();
		formated_olddate['all'] = formated_olddate['y'] + "-" + formated_olddate['M'] + "-" + formated_olddate['d'];
		return formated_olddate;
	}
	function takeUsers(sel,grp,out){ //Загружаем пользователей, соответствующих выбранной группе
		//alert(sel+"|||||"+grp);
		$.ajax({url:"/core/reqsave?m=load_user&my="+sel+"&grp="+grp+"&modus=1", async:false, type:'POST', data:{m: "load_user", my: sel, grp:grp,modus:1},success:function(data) {
			//alert(data);
			$('#' + out).empty();
			var dataArray = jQuery.parseJSON(data);
			$('#' + out).append(dataArray);
		}});
	}
	function ch_group() { //Выполняем функцию при смене группы или шаблона
		var gr = $("#Группе").val();
		takeUsers('',gr,'Пользователю'); //Загружаем пользователей, соответствующих выбранной группе
	}
	function ch_user(){ //Выполняем функцию при смене пользователя или шаблона
		var cu = $("#Пользователю").val();
		$.post("/core/reqsave?m=crm_usercheck", {m: "crm_usercheck", id: cu},function(data){ //Выбираем группу по пользователю
			var dataArray = jQuery.parseJSON(data);
			var div = dataArray['Division'];
			$("#Группе option[value='"+div+"']").attr("selected", true);
		});
	}
	$(document).ready(function(){ //Блок, выполняемый при старте страницы
		//alert('b1b1b1');
		takeUsers('','','Пользователю');
		//alert('b2b2b2b2');
		$("#Группе").change(function(){ //сли изменили селектор групп
			ch_group(); //Загружаем пользователей, соответствующих выбранной группе
		});
		$("#Пользователю").change(function(){ //Если изменили селектор пользователей
			//ch_user();
		});
		$("#task").change(function(){ //Если изменили селектор шаблонов
			//ch_user(); // Проверяем, соответствует ли пользователь группе, и если не соответствует, сбрасываем пользователя
			ch_group(); //Загружаем пользователей, соответствующих выбранной группе
		});
		//alert('b3b3b3b3');
	});
	var conragents = [];
</script>
<?php
unset($taskSelector, $conragents, $users, $groups, $taskArray);
$groups = $Cache->get('groups');
if(trim($groups) != ''){

}else{
	$sqlQ = "SELECT DISTINCT [Division] FROM [tig50].[dbo].[Пользователи]  ORDER BY [Division]";
	$Core->query = $sqlQ;
	$Core->con_database('tig50');
	$stm = $Core->PDO();
	while ($roww = $stm->fetch()) {
		if(trim($roww['Division']) != ''){
			$groups .= '<option value="' . $roww['Division'] . '">' . $roww['Division'] . '</option>';
		}
	}
	$Cache->add('groups', $groups);
}
$taskSelector = $Cache->get('taskSelector');
if(trim($taskSelector) != ''){

}else{
	$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Шаблоны_Заданий]";
	$Core->con_database('tig50');
	$stm = $Core->PDO();
	while ($roww = $stm->fetch())
	{
		$taskSelector .= '<option value="' . $roww['Код'] . '">' . $roww['Шаблон'] . '</option>';
		//foreach($roww as $k => $v){$taskArray[$roww['Код']][$k] = $v;}
	}
	$Cache->add('taskSelector', $taskSelector);
	//print "<script>alert('1212121212121212');</script>";
}
if($_REQUEST['oid'] == 1) {
	print '<div style="position:absolute;">';
	print '<div style="position:absolute;top:3px;left:0px;"><img src="/css/images/wertlin.png" border="0" id="wertlin" style="position:absolute;z-index: 3000000;"/></div>';
	print '</div>';
}

?>
<div style="padding:0px 5px 0px 5px;margin:0px 5px 0px 5px;background-color:#99bbee;position:relative;" id="crmadd">
	<?php
	if($_REQUEST['oid'] != 1){
	?>
	<div style="position:absolute;top:3px;right:4px;"><a href="javascript:void(0);" style="color:#ffffff;text-decoration:none;" onClick="javascript:<?php if ($startMode == 1) { print "parent.window."; } ?>document.getElementById('<?= $currLayer; ?>').style.display='none';"><img src="/css/images/crus004.png" border="0"/> </a></div>
	<fieldset>
		<legend class="ttitle">Новое задание</legend>
		<?php
		}else{
		}
		?>
		<form method="post" action="" id="createForm">
			<p class="ttitle" >
			<table><tr><td style="padding:0px 0 1px 3px;margin: 0;background: url(/css/images/platta04.png) no-repeat;position:relative;width:195px;height:30px;">
						<select name="tasks" id="tasks" onchange="javascript:getNewTemplate(this.value);" class="ttitle" style="width:186px;border:0px;border:none;">
							<option></option>
							<?= $taskSelector; ?>
						</select>
					</td><td>Выберите&#160;шаблон</td></tr></table>
			</p>
			<table class="wtable">
				<tr>
					<td style="padding:0px 0 7px 3px;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;" class="ui-widget noclose backfield">
						<? require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/cagentform.php'; ?>
					</td>
					<td></td>
					<td class="ttitle">Контрагент</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<tr>
					<td style="padding:0px 0 3px 3px;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;">
						<select name="Тип_Контакта" id="Тип_Контакта" class="w_100 ttitle" style="width:290px;margin-left: 4px;">
							<option></option>
							<option value="Входящий">Входящий</option>
							<option value="Исходящий">Исходящий</option>
						</select>
					</td>
					<td></td>
					<td class="ttitle">Тип Контакта</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<script>
					//alert('WWWWWWWWWWWWWWWWWWW');
					function shVid(laye){
						if(document.getElementById(laye).style.display == 'none'){
							$.post("/core/reqsave?m=crm_tablesync", {m: "crm_tablesync", cl:"1"},function(data){ //alert(data);
							});
							document.getElementById(laye).style.display = 'block';
						}else{
							document.getElementById(laye).style.display = 'none';
						}
					}
				</script>
				<tr>
					<td class="ui-widget noclose backfield" id="tocont"  style="padding:0px 0 7px 3px;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;">
						<?
						require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/typeform.php'
						?>
					</td>
					<td class="ui-widget noclose backfield" id="tocont2" style="position:relative;">
						<a style="float:right;" href="javascript:void(0);" onClick="javascript:shVid('chosesel1');"><img src="/css/images/plus_select3.png" style="padding: 0 0 0 5px;"/></a>
					</td>
					<td class="ttitle">Тип Обращения</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<script>
					//alert('WWWWWWWWWWWWWWWWWWW111');
					function shVid2(laye){
						if(document.getElementById(laye).style.display == 'none'){
							$.post("/core/reqsave?m=crm_tablesync", {m: "crm_tablesync", cl:"2"},function(data){ //alert(data);
							});
							document.getElementById(laye).style.display = 'block';
						}else{
							document.getElementById(laye).style.display = 'none';
						}
					}
				</script>
				<tr>
					<td class="ui-widget noclose backfield" id="theme" style="padding:0px 0 7px 3px;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;">
						<!--<input type="text" name="Тема" id="Тема" class="w_100"/>-->
						<? require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/testform.php' ?>
					</td>
					<td class="ui-widget noclose" id="theme2" style="position:relative;">
						<a style="float:right;" href="javascript:void(0);" onClick="javascript:shVid2('chosesel2');"><img src="/css/images/plus_select3.png" style="padding: 0 0 0 5px;"/></a>
					</td>
					<td class="ttitle">Тема</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<tr>
					<td  style="padding:0px 0 1px 0;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;">
						<input type="text" name="Описание_Задачи" id="Описание_задачи" class="w_100 ttitle" style="width:290px;margin-left: 4px;"/>
					</td>
					<td></td>
					<td class="ttitle">Описание задачи</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<tr>
					<?php
					if($_REQUEST['oid'] == 1) {
						print '<input type="hidden" name="Кому_Поставили_Пользователь_Код" id="Пользователю"/>';
						print '<td  style="padding:0px 0 0px 0;margin: 0;position:relative;width:300px;overflow:auto;">';
						print '<div style="width:100%;min-height: 20px;padding:0;margin:0;" id="out__user"></div>';
						//print '<div id="out__user"></div>';
						print "<script>";
						?>
						setInterval(function(){
						var ss = window.parent.$('#urefr').val();
							if(ss != ''){
								$('#out__user').html(ss);
							}
						},2000);
						<?php
						print "</script>";
						print '</td>';
					}else{
						?>
						<td  style="padding:0px 0 3px 0;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;">
							<select name="Кому_Поставили_Пользователь_Код" id="Пользователю" class="w_100 ttitle" style="width:290px;margin-left: 4px;">
							</select>
						</td>
						<?php
					}
					?>
					<td></td>
					<td class="ttitle">
						<?php
						if($_REQUEST['oid'] != 1) {
							print 'Пользователю';
						}else{
							print '<a href="javascript:void(0);" onClick="javascript:window.parent.usrProcX();" style="padding:0;margin:0;font-size: 11px;">Пользовтели</a>';
						}
						?>
					</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<tr>
					<td style="padding:0px 0 3px 0;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;">
						<select name="Кому_Поставили_Группа" id="Группе" class="w_100 ttitle"  style="width:290px;margin-left: 4px;">
							<option></option>
							<?= $groups; ?>
						</select>
					</td>
					<td></td>
					<td class="ttitle">Группе</td>
				</tr>
				<tr><td colspan="3" style="font-size: 5px;">&#160;</td></tr>
				<tr>
					<td style="padding:0px 0 3px 0;margin: 0;background: url(/css/images/platta06.png) no-repeat;position:relative;width:300px;">
						<input type="text" name="Дата" id="Дата" class="w_100 ttitle" style="width:290px;margin-left: 4px;"/>
					</td>
					<td></td>
					<td class="ttitle">Дата</td>
				</tr>
			</table>
			<br/><input type="hidden" id="usersess_count"/>
			<div id="errshow" style="color:#ff0000;text-align: center;display:none;">Пожалуйста, заполните все поля формы</div>
			<script>
				function runoutuser(){
					$.ajax({url: "/core/srv_01.php",async: false,type: 'get',data: {m: "getSessionUsers"},success: function (data) {
						document.getElementById('out__user').innerHTML = data;
					}});
				}
				window.runoutuserParent = function(){
						//$('#out__user').html('ddddd');
				}
				
				function forlLockSave(){
					var errArray = [];
					errArray = formCheck();
					if(errArray.length > 0){
						alert(errOut(errArray));
						return false;
					}
					formSubmit('');
					$('#horizont2').hide();
					$('#list').trigger("reloadGrid",{current:true});
				}
				function errOut(errArray){
					var ouText = "Вы не заполнили следующие обязательные поля:";
					for (key in errArray) {
						if(typeof(errArray[key]) == 'string')
							ouText += errArray[key] + "  ";
					}
					return ouText;
				}

				function formSubmit(newID){
					//alert('||||| '+newID);
					//$('#createForm').submit();
					$.ajax({url: "/core/srv_01.php",async: true,type: 'get',data: {
						m: "saveAddForm",
						Контрагент_Код: $('#Контрагент_Код').val(),
						Контрагент_Код: $('#Контрагент_Код').val(),
						Тип_Контакта: $('#Тип_Контакта').val(),
						Тип_Обращения: $('#Тип_Обращения').val(),
						Тема: $('#Тема').val(),
						Описание_Задачи: $('#Описание_задачи').val(),
						Кому_Поставили_Пользователь_Код: $('#Пользователю').val(),
						Кому_Поставили_Группа: $('#Группе').val(),
						Дата_Контрольная: $('#Дата').val(),
						Обращение: newID},success: function (data) {
						//data = jQuery.parseJSON(data);
					}});
				}
				function formCheck() {
					var outArray = [];
					$.ajax({url: "/core/srv_01.php",async: false,type: 'get',data: {m: "testSessionArray", a: 'seluser'},success: function (data) {
						$('#usersess_count').val(data);
					}});

					if($('#Дата').val() == ''){outArray.push('Дата');}
					if($('#Группе').val() == ''){outArray.push('Группе');}
					if($('#Описание_задачи').val() == ''){outArray.push('Описание_задачи');}
					if($('#Тип_Обращения').val() == ''){outArray.push('Тип_Обращения');}
					if($('#Тип_Контакта').val() == ''){outArray.push('Тип_Контакта');}
					if($('#Контрагент_Код').val() == ''){outArray.push('Контрагент');}
					if(($('#usersess_count').val() == '' || $('#usersess_count').val() == 0) && $('#Пользователю').val() == ''){outArray.push('Пользователь');}
					return outArray;
				}
				function formCheck2() {
					if ($('#Дата').val() != '' && ($('#Группе').val() != '' || $('#Пользователю').val() != '') && $('#Описание_задачи').val() != ''
						&& $('#Тема').val() != '' && $('#Тип_Обращения').val() != '' && $('#Тип_Контакта').val() != '' && $('#Контрагент_Код').val() != '') {
						$('#createForm').submit();
						<?php if($startMode == 1){print	"window.parent.";}?>$('#<?=$currLayer;?>').hide();
					} else {
						$('#errshow').show();
						<?php if($startMode == 1){print	"window.parent.";}?>$('#<?=$currLayer;?>').hide();
					}
				}
				function FieldUpdate(fArr){
					//alert('AAAAAAAAAAAAAAAAAAAAAAAAA');
					$.get("/core/srv_01.php", { m: "getSessionArray", a: fArr}, function(data){ //alert(data);
						var Data = jQuery.parseJSON(data);
						try{
							var data = Data['contragent'];
							if (data != 'NaN' && data != '' && data != 'undefined' && data != 'false' && typeof(data) != 'boolean') {
								$('#Контрагент_Код').val(data).trigger("chosen:updated");
							}
						}catch(e) {		}
						try{
							var data = Data['subtheme'];
							if (data != 'NaN' && data != '' && data != 'undefined' && data != 'false' && typeof(data) != 'boolean') {
								$('#tasks').val(data).trigger("chosen:updated");
								getNewTemplate(data);
							}
						}catch(e) {		}
					});
					//alert('BBBBBBBBBBBBBBBBBBBBBBBBBBB');
				}
				
				<?php
				//require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/addform_func.php';
				
				if($_REQUEST['oid'] == 1){
				?>
				FieldUpdate('callCrm');
				$( document ).ready(function() { //alert('XXXXXXXX');
					runoutuser();
					$('.chosen-container').width(302);
					$('.chosen-select').width(290).css('border',0);
					$('.ui-widget').css('padding-top','3px');
				});
				
				<?php
				}else{
				?>
				$( document ).ready(function() {
					$('.chosen-container').width(300);
					$('.chosen-single').width(293);
					$('#crmadd').width(550);



				});
				<?php
				}
				?>
				
			</script>
			<input type="hidden" name="sform" value="1"/>
		</form>
		
		<?php
		if($_REQUEST['oid'] != 1){
		?>
		<br/><br/><br/>
		<table style="float:right;">
			<tr>
				<td id="key_s" onClick="javascript:forlLockSave();"></td>
				<td id="key_r"
					onClick="javascript:document.getElementById('createForm').reset();<?php if($startMode == 1){print	"window.parent.";}?>$('#<?=$currLayer;?>').hide();$('#errshow').hide();"></td>
			</tr>
		</table>
	</fieldset>
<?php
}
?>
</div>
<input type="hidden" name="acmod" id="acmod" value="a"/>
