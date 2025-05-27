<?php
	$r =  (array)json_decode($_REQUEST['r']);
	$wid = $_REQUEST['w'] / 3;
	require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
	$tar2Arr = array('Код', 'Тариф_Код');
	$sql = "SELECT * FROM [tig50].[dbo].[Тарифы2]  WHERE [Код] = '".$r['Тариф_Код']."'";
	$Core->query = $sql;
	$Core->con_database('tig50');
	$stm = $Core->PDO();
	$tarif = $stm->fetch();
	$template = $tarif['Tpl_name'];
	if($tarif['Родитель'] > 0){
		$max = 10;
		$coun = 0;
		$parent = $tarif['Родитель'];
		while($coun < $max){
			$sqlA = "SELECT * FROM [tig50].[dbo].[Тарифы2]  WHERE [Код] = '".$parent."'";
			$Core->query = $sqlA;
			$Core->con_database('tig50');
			$stmA = $Core->PDO();
			$rowA = $stmA->fetch();
			if($rowA['Родитель'] > 0){
				$parent = $rowA['Родитель'];
			}else{
				$template = $rowA['Tpl_name'];
				break;
			}
			$coun++;
		}
	}
	//print $template.'||||||||||||||';
?>
	<script src="/js/jquery/jquery-1.11.2.js" type="text/javascript"></script>
	<script>
	</script>
<div style="position:relative;">
	<div style="position:absolute;top:4;right:4;">
		<a href="javascript:void(0);" onClick="javascript:parent.window.document.getElementById('modal_winn_prp01').style.display='none';"><img src="/css/images/crus003.png"></a>
	</div>
</div>
<!-- top:100px;left:100px -->
<table style="height:100%;width:100%;background-color: #ffffff;">
	<tr><td style="width: <?=$wid;?>px;" valign="top">
			<fieldset class="slide-set" style="height:750px;">
			<legend>Параметры тарифа по объему</legend>
			<div style="overflow:auto;overflow-y: scroll;width: 100%;height:700px;">
			<table style="width: 100%;">
				<tr>
						<?php
			$sql0 = "SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME='Тарифы2_Объем'";
			$Core->query = $sql0;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while($row = $stm->fetch()){
				if(!in_array($row['COLUMN_NAME'],$tar2Arr)){
					print "<th align='left'>".$row['COLUMN_NAME']."</th>";
				}
			}
			?>
				</tr>
			<?php
			$sql = "SELECT * FROM [tig50].[dbo].[Тарифы2_Объем] WHERE [Тариф_Код] = '".$r['Тариф_Код']."'";
			$Core->query = $sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while($row = $stm->fetch()){
				print "<tr>";
				//print "<pre>"; print_r($row); print "</pre>";
				foreach($row as $k => $v){
					if(!in_array($k,$tar2Arr) && trim($v) != ''){
						print "<td align='left'>";
						print $v;
						print "</td>";
					}
				}
				print "</tr>";
			}
			?>
			</table>
				</div>
				</fieldset>
	</td>
	<td style="width: <?=$wid;?>px;" valign="top">
		<fieldset class="slide-set" style="height:750px;">
			<legend>Параметры тарифа по весу</legend>
			<div style="overflow:auto;overflow-y: scroll;width: 100%;height:700px;">
		<table style="width: 100%;">
			<tr>
				<th>Название</th>
				<th>Цена</th>
			</tr>
			<tr>
			<?php
			$sql = "SELECT m.[Название], g.[Цена] FROM [tig50].[dbo].[Тарифы2_Вес] AS g LEFT JOIN [tig50].[dbo].[Виды_груза] AS m ON m.[Категории_Код] = g.[Категория_Груза] WHERE g.[Тариф_Код] = '".$r['Тариф_Код']."'";
			$Core->query = $sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while($row = $stm->fetch()){
				print "<tr>";
				print "<td align='left'>".$row['Название']."</td>";
				print "<td align='right'>".$row['Цена']."</td>";
				print "</tr>";
			}
			?>
			</tr>
		</table>
		</div>
		</fieldset>
	</td>
	<td style="width: <?=$wid;?>px;" valign="top">
		<?php
		unset($tarParams);
		$sql = "SELECT * FROM [tig50].[dbo].[Тарифы2_Параметры] WHERE [Тариф_Код] = '".$r['Тариф_Код']."'";
		$Core->query = $sql;
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		while($row = $stm->fetch()){
			$tarParams[$row['Параметр_Название']] = array('value'=>$row['Параметр_Значение'], 'descr'=>$row['Примечание']);
		}
		$Out = new paramWin($_SERVER['DOCUMENT_ROOT'].'/templates/services/service_rates/plugins/'.$template.'/tpl.php', $tarParams);
		?>
	</td>
	</tr>
</table>
<?
class paramWin{
	function __construct($path,$req_data) {
		$this->req_data = $req_data;
		require_once $path;
	}
}
?>