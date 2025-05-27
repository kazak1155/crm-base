<?php error_reporting(E_ERROR);
require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/tload.php";
$sql = "SELECT * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_REQUEST['oid']."'";
$res = $Core->query = $sql;
$Core->con_database('tig50');
$res = $Core->PDO();
$oldTask = $res->fetch();
if($oldTask['Обращение'] > 0){
	$sql = "SELECT * FROM [tig50].[dbo].[Контрагенты_Работа] WHERE [Код] = '".(int)$oldTask['Обращение']."'";
	$res = $Core->query = $sql;
	$Core->con_database('tig50');
	$res = $Core->PDO();
	$oldCall = $res->fetch();
	
}
//print "<pre>"; print_r($oldTask); print "</pre>";
?>
<style>
	.key_s{height:34px;width:123px;background-image: url(/css/images/key_s_b.png)}
	.key_s:hover{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_s:active{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_r{height:34px;width:98px;background-image: url(/css/images/key_r_b.png)}
	.key_r:hover{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
	.key_r:active{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
</style>
<link href="/css/js/jquery-ui2.css" rel="stylesheet" type="text/css" />
<!--<link href="/css/grid/jqGridNew.css" rel="stylesheet" type="text/css" />
document.getElementById('rw').value = parentData;-->
<script type="text/javascript" src="/css/grid/jquery_flexbox.js"></script>
<script type="text/javascript" src="/css/grid/jquery-ui.min.js"></script>
<script src="/js/jqgrid/i18n/grid.locale-ru2.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/plugins/jqgrid/jquery.jqGrid.min.js"></script>
<div style="padding:0px;background-color:#4479BA;color:#ffffff;position:relative;">
	<div style="position:absolute;top:7px;right:5px;"><a href="javascript:void(0);" onClick="javascript:window.parent.$('#horizont21').hide();"><img src="/css/images/crus003.png"/></a></div>
	<fieldset style="height:<?php print ($_REQUEST['h'] - 25);?>px;">
	<legend>Завершение задачи</legend>
		<?php if($oldCall['Код'] > 0): ?>
			<p><input type="checkbox" name="contrcall" id="contrcall" value="1"/> Создать вызов о выполнении</p>
		<p><textarea name="endtaskcall" id="endtaskcall" style="height:50px;width:250px;"/></textarea> Текст сообщения для вызова</p>
		<?php endif; ?>
		<p><input type="checkbox" name="enablecheck" id="enablecheck" value="1"/> Поставить задание на контроль</p>
		<p>Через <input type="text" name="dayscheck" id="dayscheck" value="5" style="width:25px;"/> дней</p>
</fieldset>
	<table style="position:absolute;bottom:5px;right:5px;">
		<tr>
			<td id="key_s" class="key_s" onClick="javascript:runFinish();"></td>
			<td id="key_r" class="key_r" onClick="javascript:window.parent.$('#horizont21').hide();"></td>
		</tr>
	</table>
</div>
<script>
	function runFinish(){
		if($("#contrcall").is(":checked")){var contrcall = 1;}else{var contrcall = 0;}
		if($("#enablecheck").is(":checked")){var enablecheck = 1;}else{var enablecheck = 0;}
		$.ajax({url: "/core/srv_01.php",async: true,type: 'post',data: {m: "crm_task_finish", i: '<?=$_REQUEST['oid'];?>',c:enablecheck,d:$('#dayscheck').val(),s:$('#endtaskcall').val(),e:contrcall},success: function (data) {
			//data = jQuery.parseJSON(data);
			window.parent.$('#list').trigger('reloadGrid');
			window.parent.$('#horizont21').hide();
		}});
		
	}
	$("#dayscheck").keypress(function(event){
		event = event || window.event;
		if (event.charCode && event.charCode!=0 && event.charCode!=46 && (event.charCode < 48 || event.charCode > 57) )
			return false;
	});
</script>

