<?php
error_reporting(E_ERROR);

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$Site->init_tmpl_connection();
$Cache = new WinCache();
$isflush = 1; //Сбрасывать весь кеш при записи
//$uzwer = '86';
if($uzwer > 1){
	$Core->query = "SELECT * FROM [tig50].[dbo].[Пользователи] WHERE [Код] = '".(int)$uzwer."'";
}else{
	$uzwerFull = $Cache->get('user');
	if( $uzwerFull == '' ||  $uzwerFull == 'false'){
	}else{
	}
	$SQl = "SELECT * FROM [tig50].[dbo].[Пользователи] WHERE [Login] = '".$_SERVER['PHP_AUTH_USER']."'";
	//print $SQl;
	$Core->query = $SQl;
}
if( $uzwerFull == '' ||  $uzwerFull == 'false') {
	$Core->con_database('tig50');
	$stm = $Core->PDO();
	$uzwerFull = $stm->fetch();
	//print "||||||||||||<pre>"; print_r($_SERVER); print "</pre>";
}
if($uzwer < 1) {
	$uzwer = $uzwerFull['Код'];
}
unset($uzwerGroups);
if($uzwerFull['Division'] == ''){$uzwerFull['Division'] = '';}
$uzwerGroups[] = $uzwerFull['Division'];
$uzwerGroups[] = $uzwerFull['Division2'];

if($_REQUEST['m'] == 'save_total'){
	if($_REQUEST['add'] == 1) {
		fputs($des, $r);
		fclose($des);
		$ex = new savenewline($_POST, $uzwer,$Cache,$isflush);
	}elseif($_REQUEST['del'] == 1){
		$ex = new deletotal($_POST);
	}else{
		$ex = new savetotal($_POST,$Cache,$isflush);
	}
}
if($_REQUEST['m'] == 'load_total') {
	$out = new totalLoad($uzwer,$uzwerGroups,$Cache);
}
if($_REQUEST['m'] == 'load_user') {
	$out = new userLoad($_REQUEST['my'],$uzwer,$Cache);
}
if($_REQUEST['m'] == 'load_group') {
	$out = new groupLoad($_REQUEST['my'],$uzwer,$uzwerGroups,$Cache);
}

if($_REQUEST['m'] == 'load_contractor') {
	$out = new contractorLoad($Cache,$isflush);
}
if($_REQUEST['m'] == 'auttocomp_sel_req_type') {
	$out = new auttocomp_sel_req_type($Cache);
}
if($_REQUEST['m'] == 'autocomp_sel_req_test') {
	$out = new auttocomp_sel_req_test();
}
if($_REQUEST['m'] == 'crm_get_delegate') {
	$out = new crm_get_delegate($uzwer,$uzwerGroups,$Cache,$isflush);
}
if($_REQUEST['m'] == 'crm_templates') {
	$out = new crm_templates($uzwer,$uzwerGroups,$Cache);
}
if($_REQUEST['m'] == 'crm_template') {
	$out = new crm_template($_REQUEST['t'],$uzwer,$uzwerGroups,$Cache);
}
if($_REQUEST['m'] == 'crm_templates_add') {
	if($_REQUEST['add'] == 1) {
		$out = new crm_templates_add($_REQUEST,$uzwer, $uzwerGroups,$Cache,$isflush);
	}elseif($_REQUEST['del'] == 1){
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $r); fclose($des);
		$out = new crm_templates_del($_REQUEST,$uzwer, $uzwerGroups,$Cache,$isflush);
	}else{
		$out = new crm_templates_edit($_REQUEST,$uzwer, $uzwerGroups,$Cache,$isflush);
	}
}
if($_REQUEST['m'] == 'crm_comments'){
	$out = new crm_comments($uzwer, $uzwerGroups, $Cache);
}
if($_REQUEST['m'] == 'crm_finaldata'){
	$out = new crm_finaldata($uzwer, $uzwerGroups, $Cache);
}
if($_REQUEST['m'] == 'crm_delegatedata'){
	$out = new crm_delegatedata($uzwer, $uzwerGroups,$Cache);
}
if($_REQUEST['m'] == 'crm_toarchive'){
	$out = new crm_toarchive($uzwer, $uzwerGroups,$Cache,$isflush);
}
if($_REQUEST['m'] == 'crm_selnamelist'){
	$out = new crm_selnamelist($uzwer, $uzwerGroups,$Cache);
}
if($_REQUEST['m'] == 'crm_selnamesave'){
	$out = new crm_selnamesave($uzwer, $uzwerGroups,$Cache,$isflush);
}
if($_REQUEST['m'] == 'crm_selnamedelete'){
	$out = new crm_selnamedelete($uzwer, $uzwerGroups,$Cache,$isflush);
}
if($_REQUEST['m'] == 'crm_tablesync') {
	$out = new tableSelectSync($_REQUEST['cl'],$Cache,$isflush);
}
if($_REQUEST['m'] == 'crm_usercheck') {
	$out = new userCheck($_REQUEST['cl'],$uzwer,$Cache);
}
if($_REQUEST['m'] == 'crm_test_user') {
	$out = new crm_test_user($uzwer);
}
class crm_test_user{
	function __construct ($uzwer) {
		$Core = new Core;
		$Core->con_database('tig50');
		$Core->query = "SELECT TOP 100000 t.*,p.[Division] FROM [tig50].[dbo].[Шаблоны_Заданий] AS t LEFT JOIN [tig50].[dbo].[Пользователи] AS p ON (p.[Код] = t.[Пользователю]) WHERE t.[Код] = '".(int)$_REQUEST['r']."'";
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		$row = $stm->fetch();
		if($row['Division'] == $row['Группе']){
		}else{
			$Core->query = "UPDATE [tig50].[dbo].[Шаблоны_Заданий] SET [Пользователю] = '' WHERE [Код] = '".(int)$_REQUEST['r']."'";
			$Core->con_database('tig50');
			$Core->PDO(array("exec" => true));
		}
	}
}
class tableSelectSync{ //Создает таблицу имен в селектах
	function __construct ($cl=0,$Cache,$isflush) {
		if($isflush == 1){$Cache->flush();}
		$taskArr[] = array('base'=>'tig50','bpref'=>'dbo','table'=>'Сотрудники_Задачи','field'=>'Тип_Обращения','class'=>'1');
		$taskArr[] = array('base'=>'tig50','bpref'=>'dbo','table'=>'Сотрудники_Задачи','field'=>'Тема','class'=>'2');
		if($cl > 0){
			foreach($taskArr as $k => $v){
				if($v['class'] == $cl){
					$this->fieldProcess($v['base'],$v['bpref'],$v['table'],$v['field'],$v['class']);
				}
			}
		}else{
			foreach($taskArr as $k => $v){
				$this->fieldProcess($v['base'],$v['bpref'],$v['table'],$v['field'],$v['class']);
			}
		}
	}
	function fieldProcess($base,$bpref,$table,$field,$class){
		$Core = new Core;
		unset($existArray);
		//$Core->query = "SELECT DISTINCT [".$field."] FROM [".$base."].[".$bpref."].[".$table."] ";
		$sql = "SELECT DISTINCT [name] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class]='".(int)$class."'";
		$Core->query = $sql;
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		while($row = $stm->fetch()) {
			$existArray[] = $row['name'];
		}
		$sql1 = "SELECT [".$field."] AS [fld] FROM [".$base."].[".$bpref."].[".$table."] UNION SELECT [".$field."] AS [fld] FROM [".$base."].[".$bpref."].[Шаблоны_Заданий]";
		$Core->query = $sql1;
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		while($roww = $stm->fetch()){
			if(trim($roww['fld']) != ''){
				if(in_array($roww['fld'],$existArray)){
				}else{
					$sql3 = "INSERT INTO [tig50].[dbo].[Имена_В_Селектах] ([name],[class])VALUES('".addslashes($roww['fld'])."','".(int)$class."')";
					$Core->con_database('tig50');
					$Core->query = $sql3;
					$Core->PDO(array("exec" => true));
				}
			}
		}
		//Шаблоны_Заданий
	}
}
class crm_selnamedelete{
	function __construct ($uzwer, $uzwerGroups,$Cache,$isflush) {
		unset($out);
		if($isflush == 1){$Cache->flush();}
		//$r = print_r($isflush,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $r); fclose($des);
		$Core = new Core;
		$Core->con_database('tig50');
		$sql = "DELETE FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] ='" . (int)$_REQUEST['class'] . "' AND  [id] = '" .(int)$_REQUEST['i']. "'";
		$Core->query = $sql;
		$Core->PDO(array("exec" => true));
	}
}
class crm_selnamesave{
	function __construct ($uzwer, $uzwerGroups,$Cache,$isflush) {
		unset($out);
		if($isflush == 1){$Cache->flush();}
		$Core = new Core;
		if(trim($_REQUEST['p']) != '') {
			if (trim($_REQUEST['i']) == '') {
				$Core->query = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] ='" . (int)$_REQUEST['class'] . "' AND  [name] = '" . addslashes(trim($_REQUEST['p'])) . "'";
				$Core->con_database('tig50');
				$stm = $Core->PDO();
				$roww = $stm->fetch();
				if ($roww['cnt'] == 0) {
					$sql = "INSERT INTO [tig50].[dbo].[Имена_В_Селектах] ([class],[name])VALUES('" . (int)$_REQUEST['class'] . "','" . addslashes(trim($_REQUEST['p'])) . "')";
				}
			} else {
				$sql = "UPDATE [tig50].[dbo].[Имена_В_Селектах] SET [class] = '" . (int)$_REQUEST['class'] . "', [name] ='" . addslashes(trim($_REQUEST['p'])) . "' WHERE [id] = '" . (int)$_REQUEST['i'] . "'";
			}
			//$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txtdddd2__.txt", "w");fputs($des, $sql); fclose($des);
			$Core->con_database('tig50');
			$Core->query = $sql;
			$Core->PDO(array("exec" => true));
		}
	}
}
class crm_selnamelist{ //Возвращает лист имен в селектах по классу
	function __construct ($uzwer, $uzwerGroups, $Cache) {
		unset($out);
		$keyy = 'selnamelist'.$_REQUEST['class'];
		//print $keyy;
		$out = $Cache->get($keyy);
		if($out != 'false' && $out != ''){
		}else{
			//$out[] = '<option></option>';
			$Core = new Core;
			$CdATE = date(Y) . '-' . date(m) . '-' . date(d);
			$sql = "SELECT DISTINCT TOP 200 [id],[name] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] ='" . (int)$_REQUEST['class'] . "' ORDER BY [name]";
			$Core->query = $sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while ($roww = $stm->fetch())
			{
				$out[] = array($roww['name'], $roww['id']);
			}
			$Cache->add($keyy, $out);
		}
		//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txtdddd2__.txt", "w");fputs($des, $sql.'  '.$r); fclose($des);
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
	}
}
class crm_toarchive{
	function __construct ($uzwer, $uzwerGroups,$Cache,$isflush) {
		if($isflush == 1){$Cache->flush();}
		$Core = new Core;
		$CdATE = date(Y).'-'.date(m).'-'.date(d);
		$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		$row = $stm->fetch();
		if($row['Завершена'] == 1){
			$setEnd = 0;
			$sqlK = "SELECT TOP 100000 count(*) AS [cnt] FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE ([Кому_Поставили_Пользователь_Код] = '".(int)$_SESSION['usr']['user_id']."' OR [Кому_Поставили_Группа] = '".$uzwerGroups[0]."') AND [Завершена] = '1'";
		}else{
			$setEnd = 1;
			$sqlK = "SELECT TOP 100000 count(*) AS [cnt] FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE ([Кому_Поставили_Пользователь_Код] = '".(int)$_SESSION['usr']['user_id']."' OR [Кому_Поставили_Группа] = '".$uzwerGroups[0]."') AND [Завершена] <> '1' OR [Завершена] IS NULL";
		}
		if($row['Дата_Окончания_Факт'] == '' && $row['Завершена'] != 1){
			$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Завершена] = '".$setEnd."', [Дата_Окончания_Факт] = convert(date, '".$CdATE."', 102)  WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
		}else{
			$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Завершена] = '".$setEnd."' WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
		}
		//$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Завершена] = '".$setEnd."' WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
		$Core->con_database('tig50');
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
		$Core->query = $sqlK;
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		$row = $stm->fetch();
		$r = print_r($row,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $sqlK.''.$r); fclose($des);
		print $row['cnt'];
	}
}
class crm_delegatedata{
	function __construct ($uzwer,$uzwerGroups,$Cache) {
		unset($Out,$Group,$cnt);
		$cnt = 0;
		$Core = new Core;
		$keyy = 'crewTask'.$_REQUEST['rowID'];
		$row = $Cache->get($keyy);
		if(trim($row) != '' && $row != 'false'){
		}else{
			$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$row = $stm->fetch();
			$Cache->add($keyy,$row);
		}
		$Group = $row['Кому_Поставили_Группа'];
		$keyy = 'crewTaskDiv'.addslashes($row['Кому_Поставили_Группа'].$row['Кому_Поставили_Пользователь_Код']);
		$Out = $Cache->get($keyy);
		if(trim($Out) != '' && $Out != 'false'){
		
		}else{
			$Out = '<option></option>';
			$Sql = "SELECT DISTINCT TOP 100000 * FROM [tig50].[dbo].[Пользователи] WHERE [Division] = '".addslashes($row['Кому_Поставили_Группа'])."' ";
			$Core->query = $Sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while($roww = $stm->fetch()){
				if($roww['Код'] == $row['Кому_Поставили_Пользователь_Код']){$sel = 'selected';}else{$sel = '';}
				$Out .= '<option value="'.$roww['Код'].'" '.$sel.'>'.$roww['Full_name'].'</option>';
				$cnt++;
			}
			if($cnt < 1){
				$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Пользователи]";
				$Core->con_database('tig50');
				$stm = $Core->PDO();
				while($roww = $stm->fetch()){
					if($roww['Код'] == $row['Кому_Поставили_Пользователь_Код']){$sel = 'selected';}else{$sel = '';}
					$Out .= '<option value="'.$roww['Код'].'" '.$sel.'>'.$roww['Full_name'].'</option>';
					$cnt++;
				}
			}
			$Out .= '|||'.$Group;
			$Cache->add($keyy,$Out);
		}
		//$Out .= '<option></option>';
		print $Out;
	}
}

class crm_finaldata{
	function __construct ($uzwer,$uzwerGroups,$Cache) {
		$keyy = 'finaldata'. $_REQUEST['rowID'];
		$roww = $Cache->get($keyy);
		if($roww != '' && $roww != 'false'){
		
		}else{
			$Core = new Core;
			$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$roww = $stm->fetch();
			$Cache->add($keyy,$roww);
		}
		$out = explode(' ',$roww['Дата_Окончания_Факт']);
		print $out[0];
	}
}

class crm_comments{
	function __construct ($uzwer,$uzwerGroups,$Cache) {
		//if($_REQUEST['k'] == '1')
		unset($Out,$out);
		$Out = '';
		$out = '';
		$winHeight = '640';
		if($_REQUEST['k'] == '1'){
			$out .= '<p>Новый комментарий</p>';
			$out .= '<textarea style="width:100%;height:80px;background-color:#99bbee;" name="commtext">';
			$out .= '</textarea>';
			$out .= '<p></p>';
			$winHeight = '500';
		}
		
		
		$keyy = 'crm_comments'.$_REQUEST['rowID'];
		$Out = $Cache->get($keyy);
		if($Out != '' && $Out != 'false'){
			$Out = $out.' '.$Out;
		}else{
			$Out = $out;
			$Core = new Core;
			$Core->query = "SELECT TOP 100000 Z.*, U1.[Full_name] AS [Имя_Пользователя] FROM [tig50].[dbo].[Задачи_Комментарии] AS Z
		LEFT JOIN [tig50].[dbo].[Пользователи] AS U1 ON U1.[Код] = Z.[Пользователь]
		WHERE Z.[Задача] = '".(int)$_REQUEST['rowID']."' ORDER BY [Код] DESC";
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$Out .= '<div style="height:'.$winHeight.'px;overflow: auto;">';
			while($roww = $stm->fetch()){
				$Out .= '<div style="width:100%;">';
				$Out .= '<div style="font-size:10px;color:#000000;font-weight: 900;">';
				if(trim($roww['Имя_Пользователя']) == ''){
					$Out .= $roww['Пользователь'];
				}else{
					$Out .= $roww['Имя_Пользователя'];
				}
				$datArr = explode(' ',$roww['Дата']);
				$Out .= ' <span style="float:right;">'.$datArr[0].'</span>';
				$Out .= '</div>';
				$Out .= '<blockquote>';
				$Out .= nl2br($roww['Комментарий']);
				$Out .= '</blockquote>';
				$Out .= '</div>';
			}
			$Out .= '<input type="hidden" name="rowID" value="'.(int)$_REQUEST['rowID'].'"/>';
			$Out .= '</div>';
			$Cache->add($keyy,$Out);
		}
		print $Out;
	}
}
class crm_template{
	function dataOver($dat){
		$data = explode('-',$dat);
		$out = $data[2].'-'.$data[1].'-'.$data[0];
		return $out;
	}
	function __construct ($id,$uzwer,$uzwerGroups,$Cache) {
		error_reporting(E_ERROR);
		//testLines();
		$keyy = 'crm_template_1_'.$id;
		$roww = $Cache->get($keyy);
		unset($roww);
		if($roww != '' && $roww != 'false'){
		}else{
			$Core = new Core;
			$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Шаблоны_Заданий] WHERE [Код] = '".(int)$id."'";
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$roww = $stm->fetch();
			$usrs = stripslashes($roww['Пользователи']);
			unset($roww['Код'],$roww['Название'],$roww['Пользователи']);
			$Cache->add($keyy,$roww);
			unset($_SESSION['seluser']);
			$usersArr = json_decode($usrs,1);
			foreach($usersArr as $k => $v){
					$_SESSION['seluser'][$v] = 1;
			}
		}
		//$r3 = print_r($roww,1);$r2 = print_r($usersArr,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $r2.' '.$r3); fclose($des);
		print json_encode($roww,JSON_UNESCAPED_UNICODE);
	}
	function testLines(){
	}
}
class crm_templates_add{
	function __construct ($data,$uzwer,$uzwerGroups,$Cache,$isflush) {
		if($isflush == 1){$Cache->flush();}
		$trsl['Контрагент'] = 'Контрагент_Код';
		$CdATE = date(Y).'-'.date(m).'-'.date(d);
		$Core = new Core;
		if(trim($data['Тема']) != ''){
			$sql1 = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] = '2' AND [name] = '".addslashes(trim($data['Тема']))."'";
			//$r = print_r($data,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__2.txt", "w");fputs($des,$sql1.' '.$r); fclose($des);
			$Core->query = $sql1;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$rowC = $stm->fetch();
			if($rowC['cnt'] == 0){
				$sql2 = "INSERT INTO [tig50].[dbo].[Имена_В_Селектах] ([class], [name]) VALUES ('2','".addslashes(trim($data['Тема']))."')";
				$Core->query = $sql2;
				$Core->PDO(array("exec"=>true));
			}
		}
		if(trim($data['Тип_Обращения']) != ''){
			$sql1 = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] = '1' AND [name] = '".addslashes(trim($data['Тип_Обращения']))."'";
			//$r = print_r($data,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__1.txt", "w");fputs($des,$sql1.' '.$r); fclose($des);
			$Core->query = $sql1;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$rowC = $stm->fetch();
			if($rowC['cnt'] == 0){
				$sql2 = "INSERT INTO [tig50].[dbo].[Имена_В_Селектах] ([class], [name]) VALUES ('1','".addslashes(trim($data['Тип_Обращения']))."')";
				$Core->query = $sql2;
				$Core->PDO(array("exec"=>true));
			}
		}
		$sql = "INSERT INTO [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		foreach($data as $k => $v){
			if($k != 'oper' && $k != 'id' && $k != 'base' && $k != 'tprefix' && $k != 'table' && $k != 'm' && $k != 'add' && $k != 'Кто_Поставил_Код' && $k != 'Дата_Постановки'){
				if($trsl[$k]){$k = $trsl[$k];}
				$Vars[] = " [".$k."] ";
				if( $k == 'Дата_Контрольная' || $k == 'Дата_Окончания_Факт') {
					if (trim($v) != '') {
						$Values[] = " convert(date, '" . $v . "', 102) ";
					} else {
						$Values[] = " convert(date, '" . $CdATE . "', 102) ";
					}
				}else{
					$Values[] =  " '".addslashes($v)."' ";
				}
			}
		}
		//$Vars[] = " [Код] "; $Values[] = " '' ";
		$sql .= '('.implode(',',$Vars).') VALUES ';
		$sql .= '('.implode(',',	$Values).')';
		$Core->con_database($_REQUEST['base']);
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
	}
}
class crm_templates_del {
	function __construct ($data,$uzwer,$uzwerGroups,$Cache,$isflush) {
		if ($isflush == 1) {
			$Cache->flush();
		}
		$Core = new Core;
		$sql = "DELETE FROM [tig50].[dbo].[Шаблоны_Заданий] WHERE [Код] = '".(int)$_REQUEST['id']."'";
		$Core->con_database($_REQUEST['base']);
		$Core->query = $sql;
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $r); fclose($des);
		$Core->PDO(array("exec"=>true));
	}
}
class crm_templates_edit {
	function __construct ($data,$uzwer,$uzwerGroups,$Cache,$isflush) {
		if($isflush == 1){$Cache->flush();}
		$trsl['Контрагент'] = 'Контрагент_Код';
		$CdATE = date(Y) . '-' . date(m) . '-' . date(d);
		$CdATE = date(Y) . '-' . date(m) . '-' . date(d);
		$ZdATE = '0000-00-00 00:00:00';
		$Core = new Core;
		unset($tmpArr);
		if ($data['id'] < 1) {
			return 0;
		}
		if(trim($data['Тема']) != ''){
			$sql1 = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] = '2' AND [name] = '".addslashes(trim($data['Тема']))."'";
			//$r = print_r($data,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__2.txt", "w");fputs($des,$sql1.' '.$r); fclose($des);
			$Core->query = $sql1;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$rowC = $stm->fetch();
			if($rowC['cnt'] == 0){
				$sql2 = "INSERT INTO [tig50].[dbo].[Имена_В_Селектах] ([class], [name]) VALUES ('2','".addslashes(trim($data['Тема']))."')";
				$Core->query = $sql2;
				$Core->PDO(array("exec"=>true));
			}
		}
		if(trim($data['Тип_Обращения']) != ''){
			$sql1 = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Имена_В_Селектах] WHERE [class] = '1' AND [name] = '".addslashes(trim($data['Тип_Обращения']))."'";
			//$r = print_r($data,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__1.txt", "w");fputs($des,$sql1.' '.$r); fclose($des);
			$Core->query = $sql1;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$rowC = $stm->fetch();
			if($rowC['cnt'] == 0){
				$sql2 = "INSERT INTO [tig50].[dbo].[Имена_В_Селектах] ([class], [name]) VALUES ('1','".addslashes(trim($data['Тип_Обращения']))."')";
				$Core->query = $sql2;
				$Core->PDO(array("exec"=>true));
			}
		}
		
		$sql = "UPDATE [" . $_REQUEST['base'] . "].[" . $_REQUEST['tprefix'] . "].[" . $_REQUEST['table'] . "] SET ";
		foreach ($data as $k => $v) {
			if ($k != 'oper' && $k != 'id' && $k != 'base' && $k != 'tprefix' && $k != 'table' && $k != 'm' && $k != 'add') {
				if ($trsl[$k]) {
					$k = $trsl[$k];
				}
				if ($k == 'Дата_Контрольная' || $k == 'Дата_Окончания_Факт') {
					$tmpArr[] = " " . $k . " = convert(date, '" . $v . "', 102) ";
				} else {
					$tmpArr[] = " " . $k . " = '" . addslashes($v) . "' ";
				}
			}
		}
		$sql .= implode(',', $tmpArr);
		$sql .= " WHERE [Код] = " . $data['id'];
		$Core->con_database($_REQUEST['base']);
		$Core->query = $sql;
		$Core->PDO(array("exec" => true));
	}
}
class crm_templates{
	function __construct ($uzwer,$uzwerGroups,$Cache) {
		$page = $_REQUEST['page'];
		$limit = $_REQUEST['rows'];
		if($limit < 1){$limit = 30;}
		$keyy = 'crm_templates_'.$page.$limit;
		$Core = new Core;
		unset($sTmp);
		$start  = $page * $limit;
		$Count = 0; $count = 0;
		//$Core->query = "SELECT TOP 100000 *,  Z.[Код] AS [Код], Z.[Код] AS [id] FROM [tig50].[dbo].[Шаблоны_Заданий] AS Z";
		$Core->query = "SELECT TOP 100000 *, U1.[Full_name] AS [Пользователю], Z.[Код] AS [Код], Z.[Код] AS [id] FROM [tig50].[dbo].[Шаблоны_Заданий] AS Z
	LEFT JOIN [tig50].[dbo].[Пользователи] AS U1 ON (U1.[Код] = Z.[Пользователю]) ";
		
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		while ($roww = $stm->fetch()) {
			$Count++; $count++;
			if($Count >= ($start-$limit)) {
				$row = $roww;
				$row['Пользователям'] = 'Подключить пользователей';
				$sTmp[] = $row;
			}
		}
		if( $count > 0 && $limit > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		$out['page'] = $page;
		$out['total'] = $total_pages;
		$out['records'] = $count;
		$out['rows'] = $sTmp;
		
		//$_SESSION['seluser'][$row['Код']]
		$OUT = array_reverse($out, true);
		print json_encode($OUT,JSON_UNESCAPED_UNICODE);
	}
}
class crm_get_delegate{
	function __construct ($uzwer,$uzwerGroups,$Cache,$isflush) {
		unset($Users,$out,$Out);
		//if($isflush == 1){$Cache->flush();}
		$Core = new Core;
		$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Пользователи]";
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		while ($roww = $stm->fetch()) {
			$row = $roww;
			$Users[$row['Код']] = $row;
		}
		$Core->query = "SELECT TOP 100000 Z.[Код] AS id, Z.*, Z.[Кто_Поставил_Код] AS [Кто_Поставил_Код_Номер], Z.[Кому_Поставили_Пользователь_Код] AS [Кому_Поставили_Пользователь_Код_Номер], K.[Название_RU] AS [Контрагент], U1.[FullName] AS [Кто_Поставил_Код] , U2.[FullName] AS [Кому_Поставили_Пользователь_Код]
FROM [tigg50].[dbo].[Сотрудники_Задачи] AS Z
LEFT JOIN [tig50].[dbo].[Контрагенты] AS K ON (K.[Код] = Z.[Контрагент_Код])
LEFT JOIN [tig50].[dbo].[Пользователи] AS U1 ON (U1.[Код] = Z.[Кто_Поставил_Код])
LEFT JOIN [tig50].[dbo].[Пользователи] AS U2 ON (U2.[Код] = Z.[Кому_Поставили_Пользователь_Код])
WHERE  [Код] = '".(int)$_REQUEST['rowid']."'";
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		$row = $this->correctData($roww);
	}
	private function correctData($dtRow){
		unset($out);
		foreach($dtRow as $k => $v){
			if(mb_substr($k,0,4) == 'Дата'){
				$vArr = explode(' ',$v);
				$out[$k] = $vArr[0];
			}else{
				$out[$k] = $v;
			}
		}
		$out .= '<select name="owner">';
		foreach($Users as $k->$v){
			$selected = ''; if($row['Кто_Поставил_Код]'] == $v['Код']){$selected = 'selected';}
			$out .= '<option value="'.$v['Код'].'" $selected>';
			$out .= $v['Full_name'];
			$out .= '</option>';
		}
		$out .= '</select>';
		$out .= '';
		$out .= '';
		$out .= '';
		print $out;
	}
	
}
class auttocomp_sel_req_type{
	function __construct ($Cache) {
		unset($out,$Out,$Count);
		$keyy = 'auttocomp_sel_req_type'.mb_strtoupper(addslashes(trim($_POST['w'])));
		$Out = $Cache->get($keyy);
		if($Out != '' && $Out != 'false'){
		
		}else{
			$out = ''; $Out = '';
			$Count = 0;
			$Core = new Core;
			if(trim($_POST['w']) != '') {
				if(trim($_POST['w']) == '' || trim($_POST['w']) == '&&&') {
					$sql = "SELECT DISTINCT TOP 200  [Тип_Обращения]  FROM [tig50].[dbo].[отрудники_Задачи] ORDER BY [Тип_Обращения]";
				}else{
					$sql = "SELECT DISTINCT TOP 200  [Тип_Обращения]  FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE UPPER([Тип_Обращения]) LIKE '%" . mb_strtoupper(addslashes(trim($_POST['w']))) . "%' ORDER BY [Тип_Обращения]";
				}
				$Core->query = $sql;
				$Core->con_database('tig50');
				$stm = $Core->PDO();
				while ($row = $stm->fetch()) {
					if (trim($row['Тип_Обращения'])) {
						$out .= '<p style="padding:0;margin:0;cursor:pointer; class="noclose"><a class="noclose" style="text-decoration:none;color:#000000;padding:0;margin:0;cursor:pointer;" href="javascript:void(0)" onClick="javascript:selRestore(\''.$row['Тип_Обращения'].'\',1)">' . $row['Тип_Обращения'] . '</a></p>';
						$Count++;
					}
				}
			}
			if($Count > 0){
				$Out .= '<div style="padding:10px 20px 20px 20px;background-color: #e0e0e0;max-height: 400px;position:static;overflow: auto;" class="noclose">';
				$Out .= $out;
				$Out .='</div>';
			}else{
			}
			$Cache->add($keyy,$Out);
		}
		print $Out;
		$Count = 0;
	}
}
class deletotal{
	function __construct ($data,$uzwer,$isflush,$Cache) {
		if($isflush == 1){$Cache->flush();}
		$Core = new Core;
		$sql = "DELETE FROM [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."]  WHERE [Код] = ".$data['id'];
		$Core->con_database($_REQUEST['base']);
		$Core->query = $sql;
		//$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $r); fclose($des);
		$Core->PDO(array("exec"=>true));
	}
}
class contractorLoad{
	function __construct($isflush,$Cache) {
		$Core = new Core;
		//if($isflush == 1){$Cache->flush();}
		unset($sTmp);
		$Core->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[Контрагенты] ORDER BY [Название_RU]";
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		$out = '<select>';
		while ($row = $stm->fetch()) {
			$out .= '<option value='.$row['Код'].'>'.$row['Название_RU'].'</option>';
		}
		$out .= '</select>';
		print $out;
	}
}
class groupLoad{
	function __construct($selUser,$uzwer,$group='',$Cache) {
		$keyy = 'groupLoad';
		$out = $Cache->get($keyy);
		if($out != '' && $out != 'false'){
		}else{
			$Core = new Core;
			unset($sTmp);
			$Core->query = "SELECT DISTINCT [Division] FROM [tig50].[dbo].[Пользователи] ORDER BY [Division]";
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$out = '<select>';
			//if($selUser != 1){$out .= '<option></option>';}
			while ($row = $stm->fetch()) {
				$sSel = '';
				if($selUser == 1 && $row['Division'] == $group){$sSel = ' selected ';}
				$out .= '<option value="'.$row['Division'].'" '.$sSel.'>'.$row['Division'].'</option>';
			}
			$out .= '</select>';
			$Cache->add($keyy,$out);
		}
		print $out;
	}
}
class userCheck{
	function __construct($selUser,$uzwer,$Cache) {
		$Core = new Core;
		if(isset($_REQUEST['login']) && trim($_REQUEST['login']) != ''){
			$keyy = 'userCheck'.addslashes($_REQUEST['login']);
			$sql = "SELECT TOP 10 * FROM [tig50].[dbo].[Пользователи] WHERE [Login] = '".addslashes($_REQUEST['login'])."'";
		}elseif(isset($_REQUEST['id']) && trim($_REQUEST['id']) != '') {
			$keyy = 'userCheck'.addslashes($_REQUEST['id']);
			$sql = "SELECT TOP 10 * FROM [tig50].[dbo].[Пользователи] WHERE [Код] = '".addslashes($_REQUEST['id'])."'";
		}
		$row = $Cache->get($keyy);
		if($row != '' && $row != 'false'){
		
		}else{
			$Core->query = $sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$row = $stm->fetch();
			$Cache->add($keyy,$row);
		}
		print json_encode($row,JSON_UNESCAPED_UNICODE);
		//print $row;
	}
}
class userLoad{
	function __construct($selUser,$uzwer,$Cache) {
		$Core = new Core;
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2-1.txt", "w");fputs($des, $r); fclose($des);
		unset($sTmp,$OUt);
		if($_REQUEST['modus'] == 2){
			$keyy = 'crm_template'.$_REQUEST['r'].$_REQUEST['grp'];
			$rowZ = $Cache->get($keyy);
			if($rowZ != '' && $rowZ != 'false'){
			
			}else{
				if($_REQUEST['r'] == ''){
					$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Шаблоны_Заданий] WHERE [Код] = '".(int)$_REQUEST['r']."'";
				}else{
					$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Шаблоны_Заданий] WHERE [Код] = '".(int)$_REQUEST['r']."'";
				}
				$Core->query = $Sql;
				$Core->con_database('tig50');
				$stm = $Core->PDO();
				$rowZ = $stm->fetch();
			}
			if(trim($rowZ['Группе']) != ''){
				$_REQUEST['grp'] = $rowZ['Группе'];
			}
		}
		$keyy = 'crewTask'.addslashes($_REQUEST['grp']).$_REQUEST['r'].$_REQUEST['modus'];
		$outArray = $Cache->get($keyy);
		if($outArray != '' && $outArray != 'false'){
		
		}else{
			if(isset($_REQUEST['grp']) && trim($_REQUEST['grp']) != ''){
				$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Пользователи] WHERE [Division] = '".addslashes($_REQUEST['grp'])."' ORDER BY [FullName]";
			}else{
				$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Пользователи] ORDER BY [FullName]";
			}
			//$r = print_r($Sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2-2.txt", "w");fputs($des, $r); fclose($des);
			$Core->query = $Sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while ($row = $stm->fetch()) {
				$outArray[] = $row;
			}
			$Cache->add($keyy,$outArray);
		}
		if($_REQUEST['modus'] != '1')$out = '<select>';
		if($_REQUEST['modus'] == 1){
			if($selUser != 1){
				$out[] = '<option></option>';
			}
		}else{
			//if($selUser != 1){$out .= '<option></option>';}
		}
		foreach($outArray as $K => $row) {
			$sSel = '';
			if($_REQUEST['modus'] == 1){
				if(($selUser == 1 && $rowZ['Пользователю'] == $row['Код'] && $row['Код'] != '')){
					$sSel = ' selected ';
				}
				$out[] = '<option value='.$row['Код'].' '.$sSel.'>'.$row['FullName'].'</option>';
			}else{
				if(($selUser == 1 && $row['Код'] == $uzwer && $row['Код'] != '')){
					$sSel = ' selected ';
				}
				$OUt[] = $row['Код'].':'.$row['FullName'];
				//$OUt[] = '<option value='.$row['Код'].' '.$sSel.'>'.$row['FullName'].'</option>';
				$out .= '<option value='.$row['Код'].' '.$sSel.'>'.$row['FullName'].'</option>';
			}
		}
		//$r = print_r($OUt,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2-.txt", "w");fputs($des, $r); fclose($des);
		
		if($_REQUEST['modus'] != '1')$out .= '</select>';
		if($_REQUEST['modus'] == 1) {
			print json_encode($out, JSON_UNESCAPED_UNICODE);
		}elseif($_REQUEST['modus'] == 2){
			$ouT = implode(';',$OUt);
			//$r = print_r($ouT,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $r); fclose($des);
			print $ouT;
			//print json_encode($OUt, JSON_UNESCAPED_UNICODE);
		}else{
			print $out;
		}
	}
}


class userLoad2{
	function __construct($selUser,$uzwer,$Cache) {
		$Core = new Core;
		unset($sTmp,$OUt);
		if($_REQUEST['modus'] == 2){
			$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Шаблоны_Заданий] WHERE [Код] = '".(int)$_REQUEST['r']."'";
			$Core->query = $Sql;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			$rowZ = $stm->fetch();
			if(trim($rowZ['Группе']) != ''){
				$_REQUEST['grp'] = $rowZ['Группе'];
			}
		}
		if(isset($_REQUEST['grp']) && trim($_REQUEST['grp']) != ''){
			$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Пользователи] WHERE [Division] = '".addslashes($_REQUEST['grp'])."' ORDER BY [FullName]";
		}else{
			$Sql = "SELECT TOP 100000 * FROM [tig50].[dbo].[Пользователи] ORDER BY [FullName]";
		}
		$Core->query = $Sql;
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		if($_REQUEST['modus'] != '1')$out = '<select>';
		if($_REQUEST['modus'] == 1){
			if($selUser != 1){$out[] = '<option></option>';}
		}else{
			//if($selUser != 1){$out .= '<option></option>';}
		}
		while ($row = $stm->fetch()) {
			$sSel = '';
			if($_REQUEST['modus'] == 1){
				if(($selUser == 1 && $rowZ['Пользователю'] == $row['Код'] && $row['Код'] != '')){
					$sSel = ' selected ';
				}
				$out[] = '<option value='.$row['Код'].' '.$sSel.'>'.$row['FullName'].'</option>';
			}else{
				if(($selUser == 1 && $row['Код'] == $uzwer && $row['Код'] != '')){
					$sSel = ' selected ';
				}
				$OUt[] = $row['Код'].':'.$row['FullName'];
				//$OUt[] = '<option value='.$row['Код'].' '.$sSel.'>'.$row['FullName'].'</option>';
				$out .= '<option value='.$row['Код'].' '.$sSel.'>'.$row['FullName'].'</option>';
			}
		}
		if($_REQUEST['modus'] != '1')$out .= '</select>';
		if($_REQUEST['modus'] == 1) {
			print json_encode($out, JSON_UNESCAPED_UNICODE);
		}elseif($_REQUEST['modus'] == 2){
			$ouT = implode(';',$OUt);
			print $ouT;
			//print json_encode($OUt, JSON_UNESCAPED_UNICODE);
		}else{
			print $out;
		}
	}
}

class totalLoad{
	function dataOver($dat){
		$datA = explode(' ',$dat);
		$data = explode('-',$datA[0]);
		$out = $data[2].'-'.$data[1].'-'.$data[0];
		return $out;
	}
	function __construct($uzwer,$uzwerGroups,$Cache){
		$Core = new Core;
		unset($sTmp,$Filtres);
		$count = 0;
		$Count = 0;
		$page = $_REQUEST['page'];
		if($_SESSION['crm']['maintable_page'] > 0){
			$page = $_SESSION['crm']['maintable_page'];
			$_SESSION['crm']['maintable_page'] = '';
			unset($_SESSION['crm']['maintable_page']);
		}
		$limit = $_REQUEST['rows'];
		if($limit < 1){$limit = 10;}
		if($_REQUEST['sord'] == ''){$_REQUEST['sord'] = 'ASC';}
		if($_REQUEST['sidx'] == ''){$_REQUEST['sidx'] = 'Код';}
		$start  = $page * $limit;
		$sidx = $_REQUEST['sidx'];
		$sord = $_REQUEST['sord'];
		if(!$sidx){ $sidx =1;}
		
		//$endModif = " (Z.[Завершена] <> 1 OR Z.[Завершена] IS NULL) AND "; 	if($_REQUEST['s'] == 1){$endModif = " Z.[Завершена] = 1 AND ";}
		$sQl = "SELECT [Флаг] FROM [srv].[dbo].[црм_Пользователь_Флаги] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND [Таблица] = '0' AND [Ключ] = 'arch00'";
		$Core->query = $sQl;
		$Core->con_database('tig50');
		$sTm = $Core->PDO();
		$rowQ = $sTm->fetch();
		if($rowQ['Флаг'] == 1){
			$endModif = " Z.[Завершена] = 1 AND ";
		}else{
			$endModif = " (Z.[Завершена] <> 1 OR Z.[Завершена] IS NULL) AND ";
		}
		if($_REQUEST['_fearch'] != 'false'){
			if($_REQUEST['filters'] != ''){
				$filter = json_decode($_REQUEST['filters']);
				$Filtres = $this->rulesMaker($filter);
				if(trim($Filtres) != ''){$endModif = $endModif.' ('.$Filtres.') AND ';}
			}
		}
		$keyy = 'totalLoad'.$_SESSION['usr']['user_id'].addslashes($_REQUEST['sidx']).$uzwerGroups[0].$uzwer.$_REQUEST['s'].$_REQUEST['sord'].$_REQUEST['filters'].'__'.$rowQ['Флаг'];
		$outArray = $Cache->get($keyy);
		if(mb_substr($_REQUEST['sidx'],0,4) == 'Дата'){
			// cast(10000000+123 as varchar)
			$_REQUEST['sidx'] = " cast(FORMAT([".$_REQUEST['sidx']."], 'dd-MM-yyyy', 'ru-RU' ) as date) ";
		}else{
			$_REQUEST['sidx'] = "[".$_REQUEST['sidx']."]";
		}
		
		if($outArray != '' && $outArray != 'false') {	}else{
			$selGroup = '';
			if($uzwerGroups[0] != ''){$selGroup = " OR Z.[Кому_Поставили_Группа] = '".$uzwerGroups[0]."' ";}
			$SQl = "SELECT TOP 100000 Z.[Код] AS id, Z.*, Z.[Кто_Поставил_Код] AS [Кто_Поставил_Код_Номер], Z.[Кому_Поставили_Пользователь_Код] AS [Кому_Поставили_Пользователь_Код_Номер],
			 K.[Название_RU] AS [Контрагент], U1.[FullName] AS [Кто_Поставил_Код] , U2.[FullName] AS [Кому_Поставили_Пользователь_Код],
			 (SELECT TOP 1 [Комментарий] FROM [tig50].[dbo].[Задачи_Комментарии] AS ZK WHERE ZK.[Задача] = Z.[Код] ORDER BY ZK.[Код] DESC) AS [Комментарий_Исполнителя]
			FROM [tig50].[dbo].[Сотрудники_Задачи] AS Z
			LEFT JOIN [tig50].[dbo].[Контрагенты] AS K ON (K.[Код] = Z.[Контрагент_Код])
			LEFT JOIN [tig50].[dbo].[Пользователи] AS U1 ON (U1.[Код] = Z.[Кто_Поставил_Код])
			LEFT JOIN [tig50].[dbo].[Пользователи] AS U2 ON (U2.[Код] = Z.[Кому_Поставили_Пользователь_Код])
			WHERE ".$endModif." (Z.[Кто_Поставил_Код] = ".(int)$uzwer." OR Z.[Кому_Поставили_Пользователь_Код] = ".(int)$uzwer." ".$selGroup.")
			ORDER BY  ".$_REQUEST['sidx']." ";
			if(trim($_REQUEST['sidx']) != trim($_REQUEST['sord']) && trim($_REQUEST['sidx']) != ''){
				$SQl .= " ".$_REQUEST['sord']." ";
			}
			//$r = print_r($_REQUEST,1);$r1 = print_r($_SERVER,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2ggg.txt", "w");fputs($des, $r1."\n".$r."\n".$endModif."\n".$SQl); fclose($des);
			$Core->query = $SQl;
			$Core->con_database('tig50');
			$stm = $Core->PDO();
			while ($roww = $stm->fetch()) {
				$roww['Дата_Постановки'] = $this->dataOver($roww['Дата_Постановки']);
				$roww['Дата_Контрольная'] = $this->dataOver($roww['Дата_Контрольная']);
				$roww['Дата_Окончания_Факт'] = $this->dataOver($roww['Дата_Окончания_Факт']);
				$outArray[] = $roww;
			}
		}
		foreach($outArray as $K => $roww){
			$count++;
			$Count++;
			if($Count >= ($start-$limit)){
				unset($row);
				$row = $this->correctData($roww);
				if(trim($row['Кто_Поставил_Код'])==''){$row['Кто_Поставил_Код'] = $row['Кто_Поставил_Код_Номер'];}
				if(trim($row['Кому_Поставили_Пользователь_Код'])=='0'){$row['Кому_Поставили_Пользователь_Код'] = '';}
				if($row['Кому_Поставили_Пользователь_Код'] == '' || $row['Кому_Поставили_Пользователь_Код'] == '0'){}else{$row['Кому_Поставили_Группа'] = '';}
				$row['Завершена'] = 'Нет';
				if($row['Завершена'] == 'null' || $row['Завершена'] == ''){$row['Завершена'] = 'Да';}
				$sTmp[] = $row;
			}
		}
		if( $count > 0 && $limit > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		$out['page'] = $page;
		$out['total'] = $total_pages;
		$out['records'] = $count;
		$out['rows'] = $sTmp;
		$out['nowdate'] = date(Y).'-'.date(m).'-'.date(d);
		//$r = print_r($out,1);$r2 = json_encode($out);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__.txt", "w");fputs($des, $r."\n".$r2); fclose($des);
		//if(count($sTmp) < 1){
		//	echo '{"page":"1","total":1,"records":1,"rows":[{"id":"243","\u041a\u043e\u0434":"243","\u041a\u043e\u043d\u0442\u0440\u0430\u0433\u0435\u043d\u0442_\u041a\u043e\u0434":"6807","\u0420\u0435\u0439\u0441_\u041a\u043e\u0434":null,"\u0424\u0430\u0431\u0440\u0438\u043a\u0438_\u041a\u043e\u0434":null,"\u0422\u0438\u043f_\u041a\u043e\u043d\u0442\u0430\u043a\u0442\u0430":"\u0412\u0445\u043e\u0434\u044f\u0449\u0438\u0439","\u0422\u0438\u043f_\u041e\u0431\u0440\u0430\u0449\u0435\u043d\u0438\u044f":"\u0442\u0435\u0441\u0442\u043e\u0432\u043e\u0435","\u0422\u0435\u043c\u0430":"\u0442\u0435\u0441\u0442\u043e\u0432\u0430\u044f","\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435_\u0417\u0430\u0434\u0430\u0447\u0438":"\u0442\u0435\u0441\u0442\u043e\u0432\u043e\u0435","\u041a\u0442\u043e_\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u043b_\u041a\u043e\u0434":"\u0418\u0432\u0430\u043d \u0411\u0440\u044b\u043a\u043e\u0432","\u041a\u043e\u043c\u0443_\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u043b\u0438_\u0413\u0440\u0443\u043f\u043f\u0430":"Admin","\u041a\u043e\u043c\u0443_\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u043b\u0438_\u041f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044c_\u041a\u043e\u0434":null,"\u0414\u0430\u0442\u0430_\u041f\u043e\u0441\u0442\u0430\u043d\u043e\u0432\u043a\u0438":"2018-07-30","\u0414\u0430\u0442\u0430_\u041a\u043e\u043d\u0442\u0440\u043e\u043b\u044c\u043d\u0430\u044f":"2018-07-30","\u0414\u0430\u0442\u0430_\u041e\u043a\u043e\u043d\u0447\u0430\u043d\u0438\u044f_\u0424\u0430\u043a\u0442":"2018-08-03","\u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439_\u0418\u0441\u043f\u043e\u043b\u043d\u0438\u0442\u0435\u043b\u044f":null,"\u0417\u0430\u0432\u0435\u0440\u0448\u0435\u043d\u0430":"\u041d\u0435\u0442","\u041a\u0442\u043e_\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u043b_\u041a\u043e\u0434_\u041d\u043e\u043c\u0435\u0440":"54","\u041a\u043e\u043c\u0443_\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u043b\u0438_\u041f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044c_\u041a\u043e\u0434_\u041d\u043e\u043c\u0435\u0440":null,"\u041a\u043e\u043d\u0442\u0440\u0430\u0433\u0435\u043d\u0442":"5test"}],"nowdate":"2018-08-03"}';
		//}else{
		//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__ss.txt", "w");fputs($des, $r); fclose($des);
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
		//}
		
	}
	private function correctData($dtRow){
		unset($out);
		foreach($dtRow as $k => $v){
			if(mb_substr($k,0,4) == 'Дата'){
				$vArr = explode(' ',$v);
				$out[$k] = $vArr[0];
			}else{
				$out[$k] = $v;
			}
		}
		return $out;
	}
	private function makePref($field){
		$out = '';
		$field = str_replace('[','',trim($field));
		$field = str_replace(']','',trim($field));
		
		if($field == 'Контрагент'){$out = 'K.[Название_RU]';}
		elseif($field == 'Кто_Поставил_Код'){$out = 'U1.[FullName]';}
		elseif($field == 'Кому_Поставили_Пользователь_Код'){$out = 'U2.[FullName]';}
		elseif($field == 'id'){$out = 'Z.[Код]';}
		elseif(mb_substr(trim($field),0,4) == 'Дата'){$out = " CONCAT(substring(cast(100+DATEPART(day, Z.[".$field."]) as varchar),2,2) ,'-',substring(cast(100+DATEPART(month, Z.[".$field."]) as varchar),2,2),'-',DATENAME(year, Z.[".$field."])) ";}
		else{$out = 'Z.['.$field.']';}
		return $out;
	}
	private function rulesMaker($filter){
		unset($ouT);
		foreach($filter->rules as $k => $v){
			//$tmp = ' ['.$v->field.'] ';
			//$r = print_r($v->field,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2__ll.txt", "w");fputs($des, $r); fclose($des);
			$tmp = $this->makePref($v->field);
			if($v->op == 'ne'){
				$tmp .= ' <> \''.$v->data.'\' ';
			}elseif($v->op == 'bw'){
				$tmp .= ' LIKE \''.$v->data.'%\' ';
			}elseif($v->op == 'bn'){
				$tmp .= ' NOT LIKE \''.$v->data.'%\' ';
			}elseif($v->op == 'ew'){
				$tmp .= ' LIKE \'%'.$v->data.'\' ';
			}elseif($v->op == 'en'){
				$tmp .= ' NOT LIKE \'%'.$v->data.'\' ';
			}elseif($v->op == 'eq'){
				$tmp .= ' LIKE \''.$v->data.'%\' ';
			}elseif($v->op == 'cn'){
				$tmp .= ' LIKE \'%'.$v->data.'%\' ';
			}elseif($v->op == 'nc'){
				$tmp .= ' NOT LIKE \'%'.$v->data.'%\' ';
			}elseif($v->op == 'nu'){
				$tmp .= ' = \'\' ';
			}elseif($v->op == 'nn'){
				$tmp .= ' <> \'\' ';
			}elseif($v->op == 'in'){
				$tmp .= ' NOT LIKE \'%'.$v->data.'%\' ';
			}elseif($v->op == 'ni'){
				$tmp .= ' LIKE \'%'.$v->data.'%\' ';
			}
			$ouT[] = $tmp;
		}
		@$Out = implode(' '.$filter->groupOp.' ',$ouT);
		return $Out;
	}
}
class savenewline{
	function __construct ($data,$uzwer,$Cache,$isflush) {
		if($isflush == 1){$Cache->flush();}
		$trsl['Контрагент'] = 'Контрагент_Код';
		$CdATE = date(Y).'-'.date(m).'-'.date(d);
		$ZdATE = '0000-00-00 00:00:00';
		$Core = new Core;
		unset($tmpArr,$Vars, $Values);
		//if($data['id'] < 1){return 0;}
		$sql = "INSERT INTO [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		foreach($data as $k => $v){
			if($k != 'oper' && $k != 'id' && $k != 'base' && $k != 'tprefix' && $k != 'table' && $k != 'm' && $k != 'add' && $k != 'Кто_Поставил_Код' && $k != 'Дата_Постановки'){
				if($trsl[$k]){$k = $trsl[$k];}
				$Vars[] = " [".$k."] ";
				if( $k == 'Дата_Контрольная' || $k == 'Дата_Окончания_Факт'){
					if(trim($v) != ''){
						$Values[] = " convert(date, '".$v."', 102) ";
					}else{
						$Values[] = " convert(date, '".$CdATE."', 102) ";
					}
				}else{
					$Values[] =  " '".addslashes($v)."' ";
				}
			}
		}
		$Vars[] = " [Кто_Поставил_Код] "; $Values[] = " '" . addslashes($uzwer) . "' ";
		$Vars[] = " [Дата_Постановки] ";  $Values[] = " convert(date, '".$CdATE."', 102) ";
		$sql .= '('.implode(',',$Vars).') VALUES ';
		$sql .= '('.implode(',',	$Values).')';
		//$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2t.txt", "w");fputs($des, $r); fclose($des);
		$Core->con_database($_REQUEST['base']);
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
	}
}

class savetotal{
	function __construct ($data,$Cache,$isflush) {
		if($isflush == 1){$Cache->flush();}
		$trsl['Контрагент'] = 'Контрагент_Код';
		$CdATE = date(Y).'-'.date(m).'-'.date(d);
		$CdATE = date(Y).'-'.date(m).'-'.date(d);
		$ZdATE = '0000-00-00 00:00:00';
		$Core = new Core;
		unset($tmpArr);
		if($data['id'] < 1){return 0;}
		$sql = "UPDATE [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] SET ";
		foreach($data as $k => $v){
			if($k != 'oper' && $k != 'id' && $k != 'base' && $k != 'tprefix' && $k != 'table' && $k != 'm' && $k != 'add'){
				if($trsl[$k]){$k = $trsl[$k];}
				if($k == 'Дата_Контрольная' || $k == 'Дата_Окончания_Факт'){
					$tmpArr[] = " ".$k." = convert(date, '".$v."', 102) ";
				}else{
					$tmpArr[] = " ".$k." = '".addslashes($v)."' ";
				}
			}
		}
		$sql .= implode(',',$tmpArr);
		$sql .= " WHERE [Код] = ".$data['id'];
		//$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $r); fclose($des);
		$Core->con_database($_REQUEST['base']);
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
		/*
		if(($_REQUEST['s'] != 1 && $data['Завершена'] == 1) || ($_REQUEST['s'] == 1 && $data['Завершена'] != 1)){
			$out = "<script>";
			$out .= "location.reload();";
			$out .= "</script>";
			print $out;
		}
		*/
	}
}
?>