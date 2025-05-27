<?php
require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/tload.php";
?>
	<fieldset style="background-color: #98baee;height:<?php print ($_REQUEST['h'] - 20);?>px;" id="plataform">
	<legend class="ttitle">Подключение пользователей</legend>
	<div style="position:relative;" id="closeCrus">
		<div style="position:absolute;top:-9px;right:-9px;z-index:301"><a href="javascript:void(0);" onClick="javascript:window.parent.$('#horizont20').hide();"><img src="/css/images/crus003.png"/></a></div>
	</div>

<?php
$tRows = 5;
$cou = 0;
$out = '';
$out .= '<table><tr>';
$sql = "SELECT * FROM [srv].[dbo].[Пользователи] ORDER BY [Full_name]";
$res = $Core->query = $sql;
$Core->con_database('tig50');
$res = $Core->PDO();
while($row = $res->fetch()){
		$out .= '<td>';
		$out .= '<input';
		if($_SESSION['seluser'][$row['Код']] != ''){$out .= ' checked ';}
		$out .= ' type="checkbox" value="'.$row['Код'].'" class="testbox" name="'.$row['Код'].'"/> '.$row['Full_name'];
		$out .= '</td>';
		$cou ++;
	if($cou < $tRows){
	}else{
		$out .= '</tr><tr>';
		$cou = 0;
	}
	
}
$out .= '<tr></table>';
print '<div style="width:100%;height:100%;overflow-y: auto;overflow-x: hidden;">';
print $out;
print '</div>';
//print "<pre>"; print_r($row); print "</pre>";
?>
</fieldset>
<div style="position:relative;">
	<table id="key_table" style="position:absolute;bottom:2px;right:4px;">
		<tr>
			<td id="key_s" onClick="javascript:return addUsers();"></td>
		</tr>
	</table>
</div>
<script>
	function addUsers(){
		var caArr = [];
		$('input:checkbox:checked').each(function(){
			caArr.push($(this).val());
		});
		var selfields = JSON.stringify(caArr);
		//make_add_users
		$.ajax({url: "/core/srv_01.php",async: false,type: 'get',data: {m: "make_add_users", v:selfields},success: function (data) {
			pdoerrors();
			window.top.$('#urefr').val(data);
				//.trigger("chosen:updated");
			window.parent.$('#horizont20').hide();
			//console.log(data);
			//setTimeout(function() { window.top.runoutuserParent(); }, 10);
			
		}});
		//$.get("/core/srv_01.php", { m: "make_add_users", v:JSON.stringify(caArr)}, function(data){	});
	}
</script>