<?php

//error_reporting(E_ERROR|E_CORE_ERROR|E_COMPILE_ERROR|E_USER_ERROR|E_RECOVERABLE_ERROR);//(E_ALL);
//error_reporting(E_ALL);
error_reporting(E_ERROR);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/library01.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/library02.php';
//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2wssw.txt", "w");fputs($des, $r); fclose($des);
$Site->init_tmpl_connection();
$Cache['cache'] = new WinCache();
$isflush = 1; //Сбрасывать весь кеш при записи
$stop = 1; // Остановить кеширование
$prCachestop = 0; // Остановить выборочное кеширование
$iconvArr = new iconvarr();
$SQl = "SELECT * FROM [tig50].[dbo].[Пользователи] WHERE [Login] = '".$_SERVER['PHP_AUTH_USER']."'";
$Core->query = $SQl;
$Core->con_database('tig50');
$stm = $Core->PDO();
$uzwerFull = $stm->fetch();
//$Core->PDO(array("exec" => true));
$Cache['cacherules'] = array('isflush'=>$isflush,'stop'=>$stop, 'privatCache'=>$prCachestop);
$Cache['core'] = $Core;
$Cache['iconv'] = $iconvArr;
$Cache['user'] = $uzwerFull;
$Cache['dataconv'] = new dataConv();
$Cache['dateSQL'] = new date_sql('-');
$Cache['dateSQLinv'] = new date_sql_inv('-');
$Cache['sqlfunc'] = new sqlfunc($Core);

//print $_REQUEST['m'];
//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2Fay.txt", "a");fputs($des, $r); fclose($des);

if(isset($_REQUEST['m']) && $_REQUEST['m'] != ''){ //Запускаем класс, переданный аяксом в переменной m
	new $_REQUEST['m']($Cache);
}
/*
class get_last_status{ //Вычисляем последнее значение статуса у счета
	function __construct ($Cache) {
		require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/holiday.php";

		echo json_encode($out, JSON_UNESCAPED_UNICODE);
	}
}
m: "flag_set", o:fie, pr:"<?=$projectName;?>", gr:"<?=$jqGridIdent;?>",us: <?=$_SESSION['usr']['user_id'];?>, f: fielD}, success: function (data){
*/
class get_dbo_error{ //роверяем не выставил ли драйвер базы сессионную переменную с сообщением об ошибке
	function __construct($Cache) {
		$err = trim($_SESSION['dbo_error']); //Если выставил, извлекаем ее из сессии
		if($err){
			if($_REQUEST['leave_error'] != 1){ //Стираем сессионное сообщение об ошибке если не задано обратное
				$_SESSION['dbo_error'] = ''; unset($_SESSION['dbo_error']);
			}
			$out = $err;
			$Out = json_encode(array('dbo'=>$out), JSON_UNESCAPED_UNICODE);
		}else{
			$Out = 'none';
		}
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2zzs_call.txt", "a");fputs($des,'%%%'.$Out."|||\n"); fclose($des);
		print $Out;
	}
}
class get_cagent_call{
	function __construct($Cache) {
		$sql = "SELECT [Контрагент_Код] FROM [tig50].[dbo].[Контрагенты_Работа] WHERE [Код]='".(int)$_REQUEST['l']."'";
		$res = $Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$res = $Cache['core']->PDO();
		$out = $res->fetch();
	//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2yyy_call.txt", "w");fputs($des, $r.' ||| '.$out['Контрагент_Код']); fclose($des);
		
		print intval($out['Контрагент_Код']);
	}
}
class get_cagent_crm{
	function __construct($Cache) {
		$sql = "SELECT [Контрагент_Код] FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код]='".(int)$_REQUEST['l']."'";
		$res = $Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$res = $Cache['core']->PDO();
		$out = $res->fetch();
		$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2yyy_crm.txt", "w");fputs($des, $r.' ||| '.$out['Контрагент_Код']); fclose($des);
		print intval($out['Контрагент_Код']);
	}
}
class get_cagent_tab{
	function __construct($Cache) {
		$out = '';
		if(intval($_REQUEST['c']) > 0){
			require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/servers/srv01lib/cagent_tabs.php";
		}
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2way.txt", "w");fputs($des, $r.' '.$out); fclose($des);
		print $out;
	}
}
class crm_task_finish{
	function __construct($Cache) {
		$currDate = date('Y').'-'.date('m').'-'.date('d');
		require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/holiday.php";
		$datPlus = new holidaysCount($Cache['core']);
		$date = date("Y-m-d");
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2may.txt", "w");fputs($des, $r); fclose($des);
		$sql = "SELECT * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_REQUEST['i']."'";
		$res = $Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$res = $Cache['core']->PDO();
		$oldTask = $res->fetch();
		if($oldTask['Обращение'] > 0) {
			$sql = "SELECT * FROM [tig50].[dbo].[Контрагенты_Работа] WHERE [Код] = '".(int)$oldTask['Обращение']."'";
			$res = $Cache['core']->query = $sql;
			$Cache['core']->con_database('tig50');
			$res = $Cache['core']->PDO();
			$oldCall = $res->fetch();
		}
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2Fay.txt", "w");fputs($des, $r); fclose($des);
		if($_REQUEST['c'] == 1){
			$exArray = array('Код','Кто_Поставил_Код','Дата_Постановки','Дата_Контрольная');
			$sqlArr = $Cache['sqlfunc']->copyRequest($Cache,$oldTask,$exArray,'[tig50].[dbo].[Сотрудники_Задачи]');
			$sqlArr['Кто_Поставил_Код'] = " [Кто_Поставил_Код] = '". $_SESSION['usr']['user_id']."'";
			$sqlArr['Дата_Постановки'] = " [Дата_Постановки] = convert(date, '" .$date. "', 102) ";
			$sqlArr['Дата_Контрольная'] = " [Дата_Контрольная] = convert(date, '" .$datPlus->daysCount($date,(int)$_REQUEST['d']). "', 102) ";
			$sqArr = $Cache['sqlfunc']->convToArr($sqlArr);
			$sql1 = "INSERT INTO [tig50].[dbo].[Сотрудники_Задачи] (".implode(',',$sqArr[0]).")VALUES(".implode(',',$sqArr[1]).")";
			$Cache['core']->query = $sql1;
			$Cache['core']->con_database('tig50');
			$Cache['core']->PDO(array("exec"=>true));
			$sql2 = "SELECT TOP 1 * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE ".implode(' AND ',$sqlArr)." ORDER BY [Код] DESC";
			$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd4ty.txt", "w");fputs($des, $sql2); fclose($des);
			$Cache['core']->query = $sql2;
			$Cache['core']->con_database('tig50');
			$res = $Cache['core']->PDO();
			$row = $res->fetch();
			$newID = $row['Код'];
			$contMess = "Проверить результаты по задаче ".$oldTask['Код'];
			$sql3 = "INSERT INTO [tig50].[dbo].[Задачи_Комментарии] ([Комментарий],[Дата],[Задача],[Пользователь])VALUES('".$contMess."',convert(date, '" .$currDate. "', 102),'".$newID."','".$_SESSION['usr']['user_id']."')";
			$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd3ty.txt", "w");fputs($des, $sql3.' ||| '.$newID); fclose($des);
			$Cache['core']->query = $sql3;
			$Cache['core']->con_database('tig50');
			$Cache['core']->PDO(array("exec"=>true));
		}
		if($_REQUEST['e'] == 1){
			$exArray = array('Код','Дата','Текст','Закрыта','Пользователь_Код');
			$sqlArr = $Cache['sqlfunc']->copyRequest($Cache,$oldCall,$exArray,'[tig50].[dbo].[Контрагенты_Работа]');
			$endText = "Задача ".$oldTask['Код']." закрыта. \n".$_REQUEST['s'];
			$sqlArr['Текст'] = " [Текст] = '".$endText."' ";
			$sqlArr['Закрыта'] = " [Закрыта] = '0' ";
			$sqlArr['Пользователь_Код'] = " [Пользователь_Код] = '".$_SESSION['usr']['user_id']."' ";
			$sqlArr['Дата'] = " [Дата] = convert(date, '" .$currDate. "', 102) ";
			$sqArr = $Cache['sqlfunc']->convToArr($sqlArr);
			$sql1 = "INSERT INTO [tig50].[dbo].[Контрагенты_Работа] (".implode(',',$sqArr[0]).")VALUES(".implode(',',$sqArr[1]).")";
			$r = print_r($sqlArr,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2ty.txt", "w");fputs($des, $r.' ||| '.$sql1.' ||| '.$newID); fclose($des);
			$Cache['core']->query = $sql1;
			$Cache['core']->con_database('tig50');
			$Cache['core']->PDO(array("exec"=>true));
		}
		$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Завершена] = '1' WHERE [Код] = '".(int)$_REQUEST['i']."' ";
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$Cache['core']->PDO(array("exec"=>true));
	}
}

class make_add_users {
	function __construct($Cache) {
		unset($out, $_SESSION['seluser'],$out);
		$selUsers = json_decode($_REQUEST['v'],1);
		$sql = "SELECT * FROM [srv].[dbo].[Пользователи] WHERE [Код] IN (".implode(',',$selUsers).") ORDER BY [Full_name]";
		$res = $Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$res = $Cache['core']->PDO();
		while($row = $res->fetch()){
			$out[] = $row['Full_name'];
			$_SESSION['seluser'][$row['Код']] = $row['Full_name'];
		}
		print implode(', ',$out);
			//$r = print_r($_SESSION,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2day.txt", "w");fputs($des, $r); fclose($des);
	}
}

class get_prev_days{ //Вычисляем следующее значение статуса
	function __construct ($Cache) {
		require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/holiday.php";
		$nDat = new holidaysCount($Cache['core']);
		$newLin[0] = date('Y').'-'.date('m').'-'.date('d').' 00:00:00';
		$newLin[1] = $nDat->dcProcess(1);
		$newLin[2] = $nDat->dcProcess(2);
		$newLin[3] = $nDat->dcProcess(3);
		$newStamp[0] = date('d').'-'.date('m').'-'.date('Y');
		$newStamp[1] = $Cache['dateSQL']->dateFromSql($newLin[1]);
		$newStamp[2] = $Cache['dateSQL']->dateFromSql($newLin[2]);
		$newStamp[3] = $Cache['dateSQL']->dateFromSql($newLin[3]);
		$out = array('lin'=>$newLin,'stamp'=>$newStamp);
		//$r = print_r($newStamp,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2day.txt", "w");fputs($des, $r); fclose($des);
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
	}
}
class get_user_flags{
	function __construct($Cache) {
		if(intval($_REQUEST['tbl']) == 0){
			//$sql = "SELECT [Флаги] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = '".addslashes($_REQUEST['prj'])."' AND ([Таблица] = '0' OR [Таблица] = '00')";
		}
		$c = 0;
		$sql = "SELECT [Проект],[Флаги] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Флаги] IS NOT NULL ";
		$Cache['core']->con_database('srv');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		while($row = $stm->fetch()){
			$out = json_decode($row['Флаги'], 1);
			if(count($out)){
				foreach($out as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						$c++;
						$v2 = $this->logProject($v2);
						$v2['Проект'] = $_SESSION['config']['projects'][$row['Проект']];
						$v2['id'] = $_SESSION['config']['projects'][$row['Проект']];
						$tOut[] = $v2;
					}
				}
			}
		}
		//$tOut[''] =
		$Out['rows'] = $tOut;
		$Out['page'] = 1;
		$Out['total'] = 1;
		$Out['records'] = $c;
		
		//$r2 = print_r($Out,1);$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2oarr2.txt", "w");fputs($des, $sql.'  '.$r.'  '.$r2); fclose($des);
		print json_encode($Out);
	}
	private function logProject($arr){
		foreach($arr as $k => $v){
			if($v > 0){
				$arr[$k] = 'Да';
			}else{
				$arr[$k] = 'Нет';
			}
		}
		return $arr;
	}
}
class put_user_flags{
	function __construct($Cache) {
		unset($updArrau, $insArrayK, $insArrayV,$oArr, $nArr, $addArray);
		$fieldArray = explode('|',$_SESSION['config']['main']['rulfields']);
		if($_REQUEST['id'] == '_empty'){
			foreach($_SESSION['config']['projects'] as $k => $v) {
				if($_REQUEST['Проект'] == $k){
					unset($nArr);
					foreach($fieldArray as $K => $V){
						if(trim($V) != ''){
							$nArr[trim($V)] = intval($this->rAnsw($_REQUEST[trim($V)]));
						}
					}
					$oArr[$_SESSION['config']['projects'][$k]][$_SESSION['config']['tables'][$k]] = $nArr;
				}
			unset($nArr);
			}
			$prTabl = $_SESSION['config']['tables'][$_REQUEST['Проект']];
			$tabsection = $this->getTable(intval($prTabl));
			$sql = "SELECT count(*) AS [cnt] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Проект] = '".addslashes($_REQUEST['Проект'])."' AND ".$tabsection." AND [Пользователь] = '".$_SESSION['usr']['user_id']."' ";
			$Cache['core']->con_database('srv');
			$Cache['core']->query = $sql;
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			//$r2 = print_r($row,1);$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2oarrX.txt", "w");fputs($des, $sql.'  '.$r.'  '.$r2); fclose($des);
			if($row['cnt'] == 0){
				$sql = "INSERT INTO [srv].[dbo].[црм_Пользователь_Установки] ([Проект], [Таблица], [Пользователь]) VALUES ('".addslashes($_REQUEST['Проект'])."', '".$prTabl."','".$_SESSION['usr']['user_id']."')";
				$r2 = print_r($row,1);$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2oarr.txt", "w");fputs($des, $sql.'  '.$r.'  '.$r2); fclose($des);
				$Cache['core']->query = $sql;
				$Cache['core']->con_database('srv');
				$Cache['core']->PDO(array("exec"=>true));
			}
			$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Флаги] = '".json_encode($oArr)."' WHERE [Проект] = '".addslashes($_REQUEST['Проект'])."' AND ".$tabsection." AND [Пользователь] = '".$_SESSION['usr']['user_id']."' ";
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('srv');
			$Cache['core']->PDO(array("exec"=>true));
		}else{
			$tabArray = $this->prjNameRecode($_REQUEST['id']);
			$tabsection = $this->getTable(intval($tabArray['table']));
			$tabsectionA = $this->putTable($tabArray['table']);
			$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Проект] = '".$tabArray['ident']."' AND ".$tabsection." AND [Пользователь] = '".$_SESSION['usr']['user_id']."' ";
			$Cache['core']->con_database('srv');
			$Cache['core']->query = $sql;
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			$oldArray = json_decode($row['Флаги'],1);
			$oArr = $oldArray;
			foreach($oldArray as $k1=>$v1){
				foreach ($v1 as $k2 => $v2){
					foreach ($v2 as $k3 => $v3){
						if(isset($_REQUEST[$k3])){
							$oldArray[$k1][$k2][$k3] = intval($this->rAnsw($_REQUEST[$k3]));
						}
					}
				}
				//$addArray
			}
			$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Флаги] = '".json_encode($oldArray)."' WHERE [Проект] = '".$tabArray['ident']."' AND ".$tabsection." AND [Пользователь] = '".$_SESSION['usr']['user_id']."' ";
			//$r2 = print_r($oldArray,1);$r = print_r($oArr,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2oarr.txt", "w");fputs($des, $sql.'  '.$r.'  '.$r2); fclose($des);
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('srv');
			$Cache['core']->PDO(array("exec"=>true));
		}
	}
	private function prjNameRecode($prjNam){
		foreach($_SESSION['config']['projects'] as $k => $v){
			if(trim($v) == $prjNam){
				return array('ident'=>$k, 'table'=>$_SESSION['config']['tables'][$k],'name'=>$prjNam);
			}
		}
	}
	private function putTable($tbl){
		if(intval($tbl) == 0){
			$tabsection = " [Таблица] = '0' ";
		}else{
			$tabsection = "[Таблица] = '".addslashes($tbl)."'";
		}
		return $tabsection;
	}
	private function getTable($tbl){
		if(intval($tbl) == 0){
			$tabsection = " ([Таблица] = '0' OR [Таблица] = '00') ";
		}else{
			$tabsection = "[Таблица] = '".addslashes($tbl)."'";
		}
		return $tabsection;
	}
	private function rAnsw($val){
		if($val == 'Да' or $val == 'да'){
			return 1;
		}else{
			return 0;
		}
	}
}
class getCagentFull {
	function __construct($Cache) {
		unset($out,$returnFlag,$outX);
		$keyX = __CLASS__.'-a-';
		/*
		if($Cache['cacherules']['privatCache'] != 1){
			$outX = $Cache['cache']->get($keyX);
			if($outX != 'false' && $outX != ''){
				echo $outX;
				$returnFlag = 1;
			}
		}
		*/
		if($returnFlag != 1){
			$sql = "SELECT TOP 100000 [Код],[Название_RU],[Название_EN],[Страны_Код],[Email_Счета] FROM [tig50].[dbo].[Контрагенты] ORDER BY [Название_RU]";
			$Cache['core']->con_database('tig50');
			$Cache['core']->query = $sql;
			$stm = $Cache['core']->PDO();
			$out[] = '<option></option>';
			while($row = $stm->fetch()){
				$out[] = '<option value="'.$row['Код'].'">'.$row['Название_RU'].' '.$row['Название_EN'].'  ['.$row['Страны_Код'].'] '.$row['Email_Счета'].' '.$row['Код'].' </option>';
			}
			$out = implode(' ',$out);
			if($Cache['cacherules']['privatCache'] != 1){$Cache['cache']->add($keyX,$out);}
			print $out;
		}

	}
}
class getCagentSmall {
	function __construct($Cache) {
		unset($out);
		$sql = "SELECT TOP 100000 [Код],[Название_RU],[Название_EN],[Страны_Код],[Email_Счета] FROM [tig50].[dbo].[Контрагенты] ORDER BY [Название_RU]";
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		while($row = $stm->fetch()){
			$out[] = array(
				'Код'=> $row['Код'],
				'Название' => $row['Название_RU'].' - '.$row['Название_EN'].' - '.$row['Страны_Код'].' - '.$row['Email_Счета']
			);
		}
		$tmpArr = json_encode($out,JSON_UNESCAPED_UNICODE);
		//$tmpArr = implode('', $out);
		print $tmpArr;
	}
}
class getCagentData {
	function __construct($Cache) {
		$sql = "SELECT * FROM [tig50].[dbo].[Контрагенты_Работа] WHERE [Код] = '".(int)$_REQUEST['id']."'";
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		$cAgentId = $row['Контрагент_Код'];
		$cAgenTxt = $row['Текст'];
		
		$sql = "SELECT * FROM [tig50].[dbo].[Контрагенты] WHERE [Код] = '".(int)$cAgentId."'";
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		$tmpArr = json_encode(array($row,$cAgenTxt));
		print $tmpArr;
	}
}
class getSignalReload {
	function __construct($Cache) {
		$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND ([Таблица] = '00' OR [Таблица] = '0')";
		$Cache['core']->con_database('srv');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		if($row['Перезагрузка'] == 1){
			if($_REQUEST['mod'] == 1){
				$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Перезагрузка] = '0' WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND ([Таблица] = '00' OR [Таблица] = '0')";
				$Cache['core']->query = $sql;
				$Cache['core']->con_database('srv');
				$Cache['core']->PDO(array("exec"=>true));
			}
			print 1;
		}else{
			print 0;
		}
		
	}
}

class getSignal {
function __construct($Cache) {
	//$r = print_r($_SESSION,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessSSL.txt", "w");fputs($des, $r); fclose($des);
		$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND ([Таблица] = '00' OR [Таблица] = '0')";
		$Cache['core']->con_database('srv');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		$lastRead = (int)$row['Последняя_Задача'];
		$relOad = 0;
		if($row['Перезагрузка'] == 1){$relOad = 1;}
		if($row['Пользователь'] > 0) {
		}else{
			$sql = "SELECT count(*) AS [cnt] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND ([Таблица] = '00' OR [Таблица] = '0')";
			$Cache['core']->con_database('srv');
			$Cache['core']->query = $sql;
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			if($row['cnt'] < 1){
				$sql = "INSERT INTO [srv].[dbo].[црм_Пользователь_Установки] ([Пользователь],[Проект], [Таблица])VALUES('".$_SESSION['usr']['user_id']."', 'crm','00')";
				$Cache['core']->query = $sql;
				$Cache['core']->con_database('srv');
				$Cache['core']->PDO(array("exec"=>true));
				$relOad = 1;
			}
			
			
		}
		$sql = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Кому_Поставили_Пользователь_Код] = '".$_SESSION['usr']['user_id']."' AND [Код] > '".(int)$lastRead."'";
	//$r = print_r($roww,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessSS.txt", "w");fputs($des, $sql.'  '.$r); fclose($des);
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		$nMess = (int)$row['cnt'];
		if($nMess > 0){
			/*
			$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND ([Таблица] = '00' OR [Таблица] = '0')";
			$res = $Cache['core']->query = $sql;
			$Cache['core']->con_database('srv');
			$res = $Cache['core']->PDO();
			$row = $res->fetch();
			$uFlgs = json_decode($row['Флаги'],1);//Считываем флаги почтовых и звуковых настроек
			$r2 = print_r($uFlgs,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_sessD.txt", "w");fputs($des, $sql.' '.$r2); fclose($des);
			*/
			//UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Последняя_Задача] = 0, [Перезагрузка] = 0 WHERE [Пользователь] = '54' AND [Проект] = 'crm'
			$out = $nMess.'|1';
			$sql = "SELECT * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Кому_Поставили_Пользователь_Код] = '".$_SESSION['usr']['user_id']."' AND [Код] > '".(int)$lastRead."' ORDER BY [Код]";
			$Cache['core']->con_database('tig50');
			$Cache['core']->query = $sql;
			$stm = $Cache['core']->PDO();
			while($row = $stm->fetch()){
				$lastCode = $row['Код'];
			}
			$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Последняя_Задача] = '".$lastCode."', [Перезагрузка] = '1' WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND [Таблица] = '00'";
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('srv');
			$Cache['core']->PDO(array("exec"=>true));
			//$aaa = 1;
			if(preg_match("/\@/",$_SESSION['usr']['user_email'])){
				//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2WWWWWWW.txt", "a");fputs($des, $_REQUEST['n']."\n"); fclose($des);
				if($_SESSION['usr']['UserFlags']['crm']['Почта'] == 1 || !isset($_SESSION['usr']['UserFlags']['crm']['Почта'])){
					if($_SESSION['usr']['UserFlags']['crm']['Повтор_почты'] == 1 || !isset($_SESSION['usr']['UserFlags']['crm']['Повтор_почты'])) {
						$this->toMail($_SESSION['usr']['user_email']);
					}else{
						//if($_REQUEST['m'] == 0){
							$this->toMail($_SESSION['usr']['user_email']);
						//}
					}
				}
			}
			$relOad = 1;
			//print $nMess.'|1';
		}else{
			//print '0|'.$relOad;
		}
		print $relOad;
	}
	private function toMail($mailTo){
		$mail_from = 'noreply@tlc-online.ru';
		$subject = 'Новая задача';
		$message = 'Для Вас поступила новая задача';
		//$subject = '=?Windows-1251?b?' . base64_encode($subject) . '?=';
		$headers = "From: $mail_from\n";
		$headers .= "To: $mailTo\n";
		$headers .= "Reply-To: $mail_from\n";
		$headers .= "Return-Path: $mail_from\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "X-Mailer: {$_SERVER['HTTP_HOST']}\n";
		$headers .= "Content-Type: multipart/mixed; boundary=MailBoundary\n";
		$ret = mail($mailTo,$subject,$message,$headers);
		//$r = print_r($ret,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessSS.txt", "w");fputs($des, $mailTo.'  '.$subject.'  '.$message.'  '.$headers.'  '.$r); fclose($des);
	}
}
class sessPageSet {
	function __construct($Cache) {
		if($_REQUEST['mod'] == 'save'){
			$wert = (intval($_REQUEST['s']) * ($_SESSION['config']['main']['currWinStep'])+30);
			$savePosition = $_REQUEST['s'].'|'.$wert;
			if(intval($_REQUEST['l']) == 0){
				$sql = "SELECT count(*) AS [cnt] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = '".addslashes($_REQUEST['p'])."' AND ([Таблица] = '0' OR [Таблица] = '00')";
			}else{
				$sql = "SELECT count(*) AS [cnt] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = '".addslashes($_REQUEST['p'])."' AND [Таблица] = '".addslashes($_REQUEST['l'])."'";
			}
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('srv');
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			$presen = $row['cnt'];
			//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2nnnnnn.txt", "w");fputs($des, $presen.'  '.$sql.'  '.$r); fclose($des);
			if($presen < 1){
				$sql = "INSERT INTO [srv].[dbo].[црм_Пользователь_Установки] ([Высота_Таблиц],[Пользователь],[Проект], [Таблица])VALUES('".addslashes($savePosition)."','".$_SESSION['usr']['user_id']."', '".$_REQUEST['p']."','".addslashes($_REQUEST['l'])."')";
			}else{
				$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Высота_Таблиц] = '".addslashes($savePosition)."' WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = '".$_REQUEST['p']."' AND [Таблица] = '".addslashes($_REQUEST['l'])."'";
			}
			//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sess.txt", "w");fputs($des, $sql.'  '.$r); fclose($des);
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('srv');
			$Cache['core']->PDO(array("exec"=>true));
		}
		if($_REQUEST['mod'] == 'load') {
			$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = '".$_REQUEST['p']."' AND [Таблица] = '".addslashes($_REQUEST['l'])."'";
			$Cache['core']->con_database('srv');
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			print $row['Высота_Таблиц'];
		}
		
	}
}
class crmGetSubTempls{
	function __construct ($Cache) {

		$Cache['core']->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[[tig50].[dbo].[Шаблоны_Заданий] ORDER BY [Шаблон]";
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		while($row = $stm->fetch()){
			$primeArray[] = $row;
		}
		$tmpArr = json_encode($primeArray); 		print $tmpArr;
	}
}

class crmGetSubthemes{
	function __construct ($Cache) {

		$Cache['core']->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[црм_вызовы_Темы] WHERE [Родитель] = 0";
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		while($row = $stm->fetch()){
			$primeArray[] = $row;
		}
		foreach($primeArray as $k => $v){
			$Cache['core']->query = "SELECT TOP 100000 * FROM [tig50].[dbo].[црм_вызовы_Темы] WHERE [Родитель] = '".$v['Код']."'";
			$Cache['core']->con_database('tig50');
			$stm = $Cache['core']->PDO();
			while($row = $stm->fetch()){
				$outArray[$row['Код']] = $v['Название'].'/'.$row['Название'];
			}
		}
		//print "<pre>"; print_r($outArray); print "</pre>";
		$tmpArr = json_encode($outArray); 		print $tmpArr;
	}
}
class callsChStatus{
	function __construct ($Cache) {
		if($_SESSION['usr']['rgt']['bask'][20] > 0){
			$Cache['core']->query = "SELECT TOP 1 * FROM [tig50].[dbo].[Контрагенты_Работа] WHERE [Код] = '".(int)$_REQUEST['o']."'";
			$Cache['core']->con_database('tig50');
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			if($row['Закрыта'] == 1){
				$Sql = "UPDATE [tig50].[dbo].[Контрагенты_Работа] SET [Закрыта] = '0' WHERE [Код] = '".(int)$_REQUEST['o']."'";
			}else{
				$Sql = "UPDATE [tig50].[dbo].[Контрагенты_Работа] SET [Закрыта] = '1' WHERE [Код] = '".(int)$_REQUEST['o']."'";
			}
			$Cache['core']->con_database('tig50');
			$Cache['core']->query = $Sql;
			$Cache['core']->PDO(array("exec"=>true));
		}
	}
}

class crmSaveDate{ //
	function dataOver($dat){
		$data = explode('-',$dat);
		$out = $data[2].'-'.$data[1].'-'.$data[0];
		return $out;
	}
function __construct ($Cache) {
	if(trim($_REQUEST['lasDate']) != '' && trim($_REQUEST['newDatComment']) != '' ){ //
		$lastDate = $_REQUEST['lasDate'];
		$_REQUEST['lasDate'] = $this->dataOver($_REQUEST['lasDate']);
		$CdATE = date(Y) . '-' . date(m) . '-' . date(d);
		$Cache['core']->query = "SELECT TOP 1 * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_REQUEST['rowID']."'";
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		$roww = $stm->fetch();
		$oldDatArray = explode(' ',$roww['Дата_Контрольная']);
		//$newDatArray = explode();
		$oldDatArray = explode(' ',$roww['Дата_Окончания_Факт']);
		$commenttext = 'Изменение даты завершения задачи c '.$oldDatArray[0].' на '.$lastDate.':'."\n".$_REQUEST['newDatComment'];
		$sql = "INSERT INTO [tig50].[dbo].[Задачи_Комментарии] ([Комментарий],[Дата],[Задача],[Пользователь]) VALUES ('".addslashes(trim($commenttext))."', convert(date, '".$CdATE."', 102), '".(int)$_REQUEST['rowID']."','".$_SESSION['usr']['user_id']."' )";
		//$r = print_r($_SESSION,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sess.txt", "w");fputs($des, $sql.'  '.$r); fclose($des);
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$Cache['core']->PDO(array("exec"=>true));
		$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Дата_Контрольная] = convert(date, '".$_REQUEST['lasDate']."', 102) WHERE [Код] = '".(int)$_REQUEST['rowID']."' ";
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$Cache['core']->PDO(array("exec"=>true));
		//print "<script>\n location.replace(\"https://".$_SERVER['SERVER_NAME']."\"); \n</script>\n";
	}

	if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}
class saveAddForm{ //
	function dataOver($dat){
		$data = explode('-',$dat);
		$out = $data[2].'-'.$data[1].'-'.$data[0];
		return $out;
	}
	function __construct ($Cache) {
		$CdATE = date(Y) . '-' . date(m) . '-' . date(d);
		$Cache['core']->query = "SELECT TOP 1 * FROM [tig50].[dbo].[Сотрудники_Задачи] ORDER BY [Код]";
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		$roww = $stm->fetch();
		$userId = $_SESSION['usr']['user_id'];
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2day1.txt", "w");fputs($des, $r); fclose($des);

		if(count($_SESSION['seluser']) > 0 && trim($_REQUEST['Кому_Поставили_Пользователь_Код']) == ''){
			foreach($_SESSION['seluser'] as $k => $v){
				$this->saveMission($Cache,$roww,$userId,$CdATE,$k);
			}
			unset($_SESSION['seluser']);
		}else{
			$this->saveMission($Cache,$roww,$userId,$CdATE,'');
		}
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
	private function saveMission($Cache,$roww,$userId,$CdATE,$toUser){
		//$r = print_r($roww,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2day3.txt", "a");fputs($des, $r."\n"); fclose($des);
		unset($sqlReq,$sqlVal);
		foreach($roww as $k => $v){
			if(trim($_REQUEST[$k]) != '' || $k == 'Кому_Поставили_Пользователь_Код'){
				$sqlReq[] = ' ['.$k.'] ';
				if(mb_substr($k,0,4) == 'Дата') {
					$fDate = $Cache['dateSQLinv']->dateToSql($_REQUEST[$k]);
					$sqlVal[] = " convert(datetime, '" . $fDate . "', 120) ";
				}elseif($k == 'Кто_Поставил_Код'){
					$sqlVal[] = " '".addslashes($userId)."' ";
					//Кому_Поставили_Пользователь_Код
				}elseif($k == 'Кому_Поставили_Пользователь_Код'){
					if($toUser == ''){
						$sqlVal[] = " '".addslashes($_REQUEST[$k])."' ";
					}else{
						$sqlVal[] = " '".(int)$toUser."' ";
					}
					
				}else{
					$sqlVal[] = " '".addslashes(trim($_REQUEST[$k]))."' ";
				}
			}
		}
		$sqlReq[] = '[Дата_Постановки]';
		$sqlVal[] = " convert(datetime, '" . $CdATE . "', 120) ";
		$sqlReq[] = ' [Кто_Поставил_Код] ';
		$sqlVal[] = addslashes($userId);
		$sql = "INSERT INTO [tig50].[dbo].[Сотрудники_Задачи] (".implode(',',$sqlReq).") VALUES (".implode(',',$sqlVal).")";
		//$r = print_r($roww,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2day.txt", "w");fputs($des, $r.' '.$sql."\n"); fclose($des);
		$Cache['core']->con_database('tig50');
		$Cache['core']->query = $sql;
		$Cache['core']->PDO(array("exec"=>true));
	}
}
class countArchive{ //Запрашиваем, пуст ли архив для текущего пользователя
	function __construct ($Cache) {
		$sql = "SELECT * FROM [tig50].[dbo].[Пользователи] WHERE [Код] = '".$_SESSION['usr']['user_id']."'"; //Выбираем группы, в которых он состоит
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		$user = $stm->fetch();
		$sql = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE ([Кто_Поставил_Код] = '".$_SESSION['usr']['user_id']."' OR  [Кому_Поставили_Группа] = '".$user['Division']."') AND [Завершена] = '1'";
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		$seri = $stm->fetch();
		if($seri['cnt'] == 0){//Если архив пуст, стираем флаг этого пользователя
			$sqL = "DELETE FROM [srv].[dbo].[црм_Пользователь_Флаги] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND [Таблица] = '0' AND [Ключ] = 'arch00'";
			$Cache['core']->query = $sqL;
			$Cache['core']->con_database('srv');
			$Cache['core']->PDO(array("exec" => true));
		}
		print $seri['cnt'];
	}
}
class setSessionArray{
	function __construct ($Cache) {
		$tmpArr = json_decode($_REQUEST['o'],1);
		//$r = print_r($tmpArr,1);$r1 = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessssseeegggg.txt", "w");fputs($des, $r1.' '.$r); fclose($des);
		if($tmpArr['clear'] > 0){
			unset($_SESSION[$_REQUEST['a']]);
		}
		if($tmpArr['subtheme'] > 0){
			$sql = "SELECT * FROM [tig50].[dbo].[црм_вызовы_Темы] WHERE [Код] = '".(int)$tmpArr['subtheme']."'";
			//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessSQL.txt", "w");fputs($des, $sql); fclose($des);
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('tig50');
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			if($row['Шаблон'] >= 1){
				$tmpArr['subtheme'] = $row['Шаблон'];
			}else{
				$tmpArr['subtheme'] = 0;
			}
		}
		unset($tmpArr['clear']);
		//$r = print_r($tmpArr,1);$r1 = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessPPPPPPP.txt", "w");fputs($des, $r1.' '.$r); fclose($des);
		$_SESSION[$_REQUEST['a']] = $tmpArr;
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
		//$r = print_r($_SESSION,1);$r1 = print_r($tmpArr,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sesssss.txt", "w");fputs($des, $sql.'  '.$r1.' '.$r); fclose($des);
	}
}
class getSessionUsers{
	function __construct ($Cache) {
		error_reporting(E_ERROR|E_CORE_ERROR|E_COMPILE_ERROR|E_USER_ERROR|E_RECOVERABLE_ERROR);//(E_ALL);
		foreach($_SESSION['seluser'] as $k => $v){
			$sql = "SELECT * FROM [srv].[dbo].[Пользователи] WHERE [Код] = '".$k."'";
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('tig50');
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			$tmpArr[] = $row['Full_name'];
		}
		$tmpOut = implode(', ',$tmpArr);
		//$r = print_r($tmpArr,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessqqq.txt", "w");fputs($des,$tmpOut); fclose($des);
		print htmlspecialchars($tmpOut);
	}
}

class testSessionArray{
	function __construct ($Cache) {
		$tmp = count($_SESSION[$_REQUEST['a']]);
		//$r = print_r($tmp,1);$r1 = print_r($_SESSION,1);$r2 = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessqqq.txt", "w");fputs($des, $r2.' '.$r1.' '.$r); fclose($des);
		print $tmp;
	}
}
class getSessionArray{
	function __construct ($Cache) {
		$tmpArr = json_encode($_SESSION[$_REQUEST['a']]);
		//$r = print_r($tmpArr,1);$r1 = print_r($_SESSION,1);$r2 = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sessqqq.txt", "w");fputs($des, $r2.' '.$r1.' '.$r); fclose($des);
		print $tmpArr;
	}
}
class dropSessionArray{
	function __construct ($Cache) {
		unset($_SESSION[addslashes($_REQUEST['s'])]);
	}
}
class flag_get{
	function __construct ($Cache) {
		$flag = $_REQUEST['o'];
		$sql = "SELECT [Флаг] FROM [srv].[dbo].[црм_Пользователь_Флаги] WHERE [Пользователь] = '".(int)$_REQUEST['us']."' AND [Проект] = '".addslashes($_REQUEST['pr'])."' AND [Таблица] = '".addslashes($_REQUEST['gr'])."' AND [Ключ] = '".addslashes($_REQUEST['f'])."'";
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rme.txt", "w");fputs($des, $sql); fclose($des);
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		if((int)$row['Флаг'] > 0){
			$flag = 0;
		}else{
			$flag = 1;
		}
		print $flag;
	}
}
class flag_set{
	function __construct ($Cache) {
		$flag = $_REQUEST['o'];
		if($_REQUEST['o'] == 999 || $_REQUEST['o'] == 998){
			$sql = "SELECT [Флаг] FROM [srv].[dbo].[црм_Пользователь_Флаги] WHERE [Пользователь] = '".(int)$_REQUEST['us']."' AND [Проект] = '".addslashes($_REQUEST['pr'])."' AND [Таблица] = '".addslashes($_REQUEST['gr'])."' AND [Ключ] = '".addslashes($_REQUEST['f'])."'";
			//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rmr.txt", "w");fputs($des, $sql); fclose($des);
			$Cache['core']->query = $sql;
			$Cache['core']->con_database('tig50');
			$stm = $Cache['core']->PDO();
			$row = $stm->fetch();
			if((int)$row['Флаг'] > 0){
				$flag = 0;
			}else{
				$flag = 1;
			}
		}
		$sql = "DELETE FROM [srv].[dbo].[црм_Пользователь_Флаги] WHERE [Пользователь] = '".(int)$_REQUEST['us']."' AND [Проект] = '".addslashes($_REQUEST['pr'])."' AND [Таблица] = '".addslashes($_REQUEST['gr'])."' AND [Ключ] = '".addslashes($_REQUEST['f'])."'";
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('srv');
		$Cache['core']->PDO(array("exec" => true));
		$sql = "INSERT INTO [srv].[dbo].[црм_Пользователь_Флаги] ([Пользователь],[Проект],[Таблица],[Флаг],[Ключ])VALUES('".addslashes($_REQUEST['us'])."','".addslashes($_REQUEST['pr'])."','".addslashes($_REQUEST['gr'])."','".addslashes($flag)."','".addslashes($_REQUEST['f'])."') ";
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('srv');
		$Cache['core']->PDO(array("exec" => true));
		if($_REQUEST['o'] == 998){
			$Cache['cache']->flush();
			if($flag == 1){
				print 0;
			}else{
				print 1;
			}
		}
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}
class payment_set{
	function __construct ($Cache) {
		$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Флаг_Неопл] = '".(int)$_REQUEST['o']."' WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."'";
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rmr.txt", "w");fputs($des, $sql); fclose($des);
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('srv');
		$Cache['core']->PDO(array("exec" => true));
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}

class owner_set{
	function __construct ($Cache) {
		$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Владелец] = '".(int)$_REQUEST['o']."' WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."'";
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rmr.txt", "w");fputs($des, $sql); fclose($des);
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('srv');
		$Cache['core']->PDO(array("exec" => true));
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}
class get_prev_status{ //Вычисляем следующее значение статуса
	function __construct ($Cache) {
		require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/holiday.php";
		$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] "; //Создаем имя основной таблицы
		$baseName2 = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['t2']."] ";  //Создаем имя справочной таблицы
		$sql = "SELECT TOP 1 * FROM ".$baseName." WHERE [".addslashes($_REQUEST['f'])."] = '".(int)$_REQUEST['b']."' ORDER BY [Код] DESC ";
		$Cache['core']->query = $sql;
		$Cache['core']->con_database('tig50');
		$stm = $Cache['core']->PDO();
		$row = $stm->fetch();
		$prevStatus = $row['Статус'];
		$sql1 = "SELECT TOP 1 * FROM ".$baseName2." WHERE [Код] > '".(int)$prevStatus."' ORDER BY [Порядок] ASC , [Код] ASC";
		$Cache['core']->query = $sql1;
		$Cache['core']->con_database('tig50');
		$stm1 = $Cache['core']->PDO();
		$row1 = $stm1->fetch();
		$nextStatus = $row1['Код'];
		//$newStamp = date('U')+(86400*$row1['Длительность_Дней']);	$out = array('prew'=>$prevStatus, 'next'=>$nextStatus,'datenext'=>date('Y-m-d',$newStamp));
		$nDat = new holidaysCount($Cache['core']);
		$newStamp = $nDat->dcProcess($row1['Длительность_Дней']);
		$out = array('prew'=>$prevStatus, 'next'=>$nextStatus,'datenext'=>$newStamp);
		//$r2 = print_r($out,1);$r1 = print_r($row1,1);$r = print_r($row,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $r2.'|||'.$r1.'|||'.$r."\n".$r); fclose($des);
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
	}
}
class set_user_tablesetting{
	function __construct ($Cache) { //j:'bill',t:'jqGridIdent, p:index,s:newwidth    [j] => bill		[t] => 04    [p] => 1    [s] => 284
		//[Пользователь]	,[Проект]      ,[Таблица]      ,[Поле]      ,[Ширина]
		$sql1 = "DELETE FROM [srv].[dbo].[црм_Пользователь_Колонки] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."'	AND [Проект] = '".(string)addslashes($_REQUEST['j'])."' AND [Таблица] = '".(string)addslashes($_REQUEST['t'])."' AND [Поле] = '".(string)addslashes($_REQUEST['p'])."'";
		$Cache['core']->query = $sql1;
		$Cache['core']->PDO(array("exec" => true));
		$sql2 = "INSERT INTO [srv].[dbo].[црм_Пользователь_Колонки] ([Пользователь],[Проект],[Таблица],[Поле],[Ширина])VALUES ( CONVERT(int,'".(int)$_SESSION['usr']['user_id']."'), CONVERT(varchar(50), '".addslashes($_REQUEST['j'])."'), CONVERT(varchar(50), '".addslashes($_REQUEST['t'])."'),'".(int)$_REQUEST['p']."',CONVERT(int,'".(int)$_REQUEST['s']."'))";
		//$r = print_r($sql2,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $r); fclose($des);
		$Cache['core']->query = $sql2;
		$Cache['core']->PDO(array("exec" => true));
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}
class put_group_right_table{ //аписываем таблицу скрытия строк
	function __construct ($Cache) {
		$rFlag = 0;
		if($_SESSION['usr']['rgt']['bask'][8] == 1){$rFlag = 1;	}
		if($_REQUEST['bill'] > 0 && $_SESSION['usr']['user_id'] > 0 && $rFlag == 1){
			$sql1 = "DELETE FROM [tig50].[dbo].[црм_Счета_Невидимость] WHERE [Счет] = '".(int)$_REQUEST['bill']."'";//Удаляем из таблицы невидимости счетов все записи этого счета
			//$r = print_r($sql1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $rFlag.' |%%| '.$r); fclose($des);
			$Cache['core']->query = $sql1;
			$Cache['core']->PDO(array("exec" => true));
			$sql1 = "SELECT TOP 1 * FROM [tig50].[dbo].[црм_счета_Статус_Счета] WHERE [Счет] = '".(int)$_REQUEST['bill']."' ORDER BY [Код] DESC"; //Получаем последний статус текущего счета
			//$r = print_r($sql1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_.txt", "w");fputs($des, $r); fclose($des);
			$Cache['core']->query = $sql1;
			$Cache['core']->con_database('tig50');
			$stm1 = $Cache['core']->PDO();
			$lastStatus = $stm1->fetch();
			$sql2 = "DELETE FROM [tig50].[dbo].[црм_Счета_последние_Статусы] WHERE [Счет] = '".(int)$_REQUEST['bill']."'";
			$Cache['core']->query = $sql2;
			$Cache['core']->PDO(array("exec" => true));//Чистим таблицу последних статусов по текущему счету
			$sql2 = "INSERT INTO [tig50].[dbo].[црм_Счета_последние_Статусы] ([Счет],[Статус],[Владелец],[Группа])VALUES('".(int)$_REQUEST['bill']."','".(int)$lastStatus['Статус']."','".(int)$lastStatus['Владелец']."','".(int)$lastStatus['Группа']."')";
			//$r = print_r($sql1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_.txt", "w");fputs($des, $sql1.'|||'.$sql2); fclose($des);
			$Cache['core']->query = $sql2;
			$Cache['core']->PDO(array("exec" => true));//Записываем последний статус в таблицу сопоставления, это понадобится для быстрого вывода таблицы счетов
			$sql1 = "SELECT TOP 1 * FROM [tig50].[dbo].[црм_счета_Статусы] WHERE [Код] = '".(int)$lastStatus['Статус']."'";//Получаем данные текущего статуса
			$Cache['core']->query = $sql1;
			$Cache['core']->con_database('tig50');
			$stm1 = $Cache['core']->PDO();
			$currStatus = $stm1->fetch();
			$groups = explode(',',$currStatus['Скрыто_для_групп']);//Из этих данных извлекаем, для каких групп он скрыт
			foreach($groups as $k => $v){ //аписываем сопоставление счета и группы, для которой он скрыт
				if(trim($v) != ''){
					$sql = "INSERT INTO [tig50].[dbo].[црм_Счета_Невидимость] ([Счет],[Группа])VALUES('".(int)$_REQUEST['bill']."','".(int)$v."')";
					$Cache['core']->query = $sql;
					$Cache['core']->PDO(array("exec" => true));
				}
			}
			if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
		}
	}
}
class get_single_table{
	function __construct ($Cache) {
		error_reporting(E_ERROR);
		unset($exArray,$chArray,$filter1);
		$rFlag = 0;
		if($_REQUEST['table'] == 'црм_Праздники' && $_SESSION['usr']['rgt']['bask'][16] == 1) {$rFlag = 1;	}
		if($_REQUEST['table'] == 'црм_счета_События_Счета' && $_SESSION['usr']['rgt']['bask'][14] == 1) {$rFlag = 1;	}
		if($_REQUEST['table'] == 'црм_счета_События' && $_SESSION['usr']['rgt']['bask'][14] == 1) {$rFlag = 1;	}
		if($_REQUEST['table'] == 'црм_Примечания' && $_SESSION['usr']['rgt']['bask'][12] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_Примечания' && $_SESSION['usr']['rgt']['bask'][1] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'V_Счета' && $_SESSION['usr']['rgt']['bask'][1] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_счета_Статус_Счета' && $_SESSION['usr']['rgt']['bask'][1] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_счета_Статусы' && $_SESSION['usr']['rgt']['bask'][1] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_счета_Статусы' && $_SESSION['usr']['rgt']['bask'][7] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_Права' && $_SESSION['usr']['rgt']['bask'][5] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_группы' && $_SESSION['usr']['rgt']['bask'][3] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Пользователи' && $_SESSION['usr']['rgt']['bask'][3] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Контрагенты_Работа_АП' && $_SESSION['usr']['rgt']['bask'][19] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Контрагенты_Работа' && $_SESSION['usr']['rgt']['bask'][19] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Контрагенты' && $_SESSION['usr']['rgt']['bask'][19] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Типы' && $_SESSION['usr']['rgt']['bask'][21] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Б_Города' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Типы' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Темы' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Выбор_клиенты_RU' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}

		if($_SESSION['usr']['user_id'] > 0 && $rFlag == 1){
			$filterBill = '';
			$returnFlag = 0;
			$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
			$keyX = __CLASS__.'-d-'.$baseName; // .$_REQUEST['sidx'].$_REQUEST['sord'].$_REQUEST['filters']
			$keyy = __CLASS__.$baseName.$_REQUEST['modus'].$_REQUEST['filters'].$_REQUEST['line'].$_REQUEST['ex'].$_REQUEST['page'].$_REQUEST['rows'].$_REQUEST['sord'].$_REQUEST['sidx'].$_REQUEST['mode'];
			if($Cache['cacherules']['privatCache'] != 1 && $_REQUEST['privat'] == 1){
				$outX = $Cache['cache']->get($keyX);
				if($outX != 'false' && $outX != ''){
					echo json_encode($outX, JSON_UNESCAPED_UNICODE);
					$returnFlag = 1;
				}
			}
			//обработчики для отдельных таблиц
			//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2v.txt", "a");fputs($des, $returnFlag.' '.$r2."\n".$r); fclose($des);
			if($returnFlag != 1){
				$out = $Cache['cache']->get($keyy);
				if($out != 'false' && $out != '' && $Cache['cacherules']['stop'] != 1){
				}else{
					if($_REQUEST['table'] == 'црм_группы'){
						require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/servers/srv01lib/for_table_crm_groups.php"; //
						$crm_groups_ex = new crm_groups($Cache);
						$sFilter = new grpFilter($Cache);
						if($_REQUEST['tfilter'] == 'group'){
							$filter1 .= $sFilter->grp_data();
						}
					}
					
					if($_REQUEST['table'] == 'Пользователи') {
						if(trim($_REQUEST['g']) != ''){
							require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/servers/srv01lib/for_table_crm_users_groups.php"; //
							$additionalSelect1 = new for_table_crm_users_groups();
							$addArray = $additionalSelect1->runSelect($Cache);
							$addSelect .= $addArray[0]; //Указываем join
							$addInsert .= $addArray[1]; //Указываем, какие переменные выбирать
							//$filterBill .= $addArray[2];//Указываем фильтры (where)
							
						}
					}
					
					if($_REQUEST['table'] == 'црм_Права') {
						if(isset($_REQUEST['Меню']) && trim($_REQUEST['Меню']) != ''){
							$filter1 .= " AND [Меню] = '".addslashes($_REQUEST['Меню'])."' ";
						}
					}
					if($_REQUEST['table'] == 'V_Счета'){
						require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/servers/srv01lib/get_single_table_v_summa.php"; // get_single_table_filters
						$filterBill = " AND [Дата] > convert(date, '2016-05-06', 102) ";
						$additionalSelect = new addSelectStatus($Cache); //Подключаем таблицу последних статусов для исключения чужих задач и скрытия по группам
						$addArray = $additionalSelect->runSelect();
						$addSelect = $addArray[0]; //Указываем join
						$addInsert = $addArray[1]; //Указываем, какие переменные выбирать
						$filterBill .= $addArray[2];//Указываем фильтры (where)
						$lastTblDate = new getStatusTable($Cache); //Вытаскиваем группу полей статусов и событий
						//$pSorter = new addSorts($Cache); //Подклбчаем модификатор запроса при внешних сортировках
						//$srt = $pSorter->powerSort();  //$_REQUEST['sidx']
						$addNote = new getNote($Cache); //Создаем объект поиска комментариев
					}
					if($_REQUEST['table'] == 'Контрагенты_Работа') {
						require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/servers/srv01lib/get_single_table_calls.php"; // get_single_table_filters
						$wObj = new contragenta_works($Cache);
						$addArray = $wObj->getContragents();
						$addSelect .= $addArray[0]; //Указываем join
						$addInsert .= $addArray[1]; //Указываем, какие переменные выбирать
						$filterBill .= $addArray[2];//Указываем фильтры (where)
						//$prewSel .= $addArray[3].' ';//Указываем пресеты
					}
					$page = $_REQUEST['page'];
					if ($page <= 1) {$page = 1;}
					$limit = $_REQUEST['rows'];
					$count = 0;
					$Count = 0;
					if ($limit < 1) {$limit = 10;}
					if ($_REQUEST['sord'] == '') {$_REQUEST['sord'] = 'ASC';}
					if ($_REQUEST['sidx'] == '') {
						if($_REQUEST['table'] == 'Логистика_Стадии') {
							$_REQUEST['sidx'] = 'Порядок';
						}elseif($_REQUEST['table'] == 'црм_вызовы_Темы'){
							$_REQUEST['sidx'] = 'Название';
						}else{
							$_REQUEST['sidx'] = 'Код';
						}
					}
					$start = $page * $limit;
					if ($start <= 0) {$start = 0;}
					if(isset($_REQUEST['ex'])){ //Если есть строка динамического подключения внешниъх таблиц, разбираем ее (var ex = "exstatusdat%црм_счета_Статус_Счета!tig50!dbo%Счет%id%last%Дата_Завершения|";)
						$exArr = explode('|',$_REQUEST['ex']);
						foreach($exArr as $k => $v){
							$ex1arr = explode('%',$v);
							if(trim($ex1arr[1]) != ''){
								$exArray[$ex1arr[0]] = $ex1arr; //Создаем мессив для передачи данных подключения в функцию. Формируется массив вида
								// [0] => event, [1] => црм_счета_Статус_Счета!tig50!dbo, [2] => Счет, [3] => id, [4] => last, [5] => Дата_Завершения
							}
						}
					}
					if(isset($_REQUEST['ch'])){ //Если есть строка динамическогй замены поля (ch = "exstatus%црм_счета_Статусы!tig50!dbo|";)
						$chArr = explode('|',$_REQUEST['ch']);
						foreach($chArr as $k => $v){
							$ch1arr = explode('%',$v);
							if(trim($ch1arr[1]) != ''){
								$chArray[$ch1arr[0]] = $ch1arr; //Создаем мессив для передачи данных подключения в функцию. Формируется массив вида
							}
						}
					}
					if ($_REQUEST['line'] > 0) {$filter1 .= " AND [Код] = '" . (int)$_REQUEST['line'] . "' ";} //Фильтр выборки линии по коду
					if(trim($_REQUEST['modus']) != ''){$filter1 .= " AND ".urldecode($_REQUEST['modus'])." ";} //Фильтр выборки по выражению
					if(isset($_REQUEST['filters']) && trim($_REQUEST['filters']) != ''){ //ильтр выборки по коду, формируемому Гридом
						require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/servers/srv01lib/get_single_table_filters.php"; // get_single_table_filters
						$filtre = new get_single_table_filters($Cache['core']);
						$filterArray = json_decode($_REQUEST['filters']);
						if(count($filterArray->rules)){
							foreach($filterArray->rules as $k => $v){
								$field = $filtre->trField($v->field,$v->data);
								$filter1 .= $field;
							}
						}
					}
					$selNames = ' '.$baseName.'.* ';
					$orders = "[".$_REQUEST['sidx']."]";
					if($_REQUEST['snames'] != ''){
						$selNames .= ' '.$_REQUEST['snames'].' ';
						$orders = " ".$baseName.".[".$_REQUEST['sidx']."]";
					}
					if($_REQUEST['sidx'] == 'Дата' && $_REQUEST['table'] == 'Контрагенты_Работа'){
						// cast(FORMAT([".$_REQUEST['sidx']."], 'dd-MM-yyyy', 'ru-RU' ) as date)
						$orders = " cast(FORMAT([Дата], 'dd-MM-yyyy', 'ru-RU' ) as date) ";
					}
					$sql = $prewSel."SELECT ".$baseName.".[Код] AS [id] ".$addInsert.", ".$selNames." FROM ".$baseName." ".$addSelect." WHERE 1 = 1 ".$filter1.$filterBill." ORDER BY ".$orders." ".$_REQUEST['sord'];
					if($_REQUEST['mode'] < 1){
						$sql .= " OFFSET ".(int)($start-$limit)." ROW FETCH NEXT ".(int)$limit." ROWS ONLY";
					}
					//$sql = preg_replace("/WHERE 1 . 1(\s+)ORDER BY/i", 'ORDER BY', $sql);
					$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rr.txt", "w");fputs($des, $addInsert."\n ".$selNames."\n".$sql.' '."\n ".$r); fclose($des);
					$Cache['core']->query = $sql;
					$Cache['core']->con_database($_REQUEST['base']);
					$res = $Cache['core']->PDO();
					$getAdmin = new get_admin();
					$vDataUsr = new get_vdata();
					while ($row = $res->fetch()){
						if((int)$row['Порядок'] < 1 || $row['Порядок'] == 'NULL' || $row['Порядок'] == '' || $row['Порядок'] == '0'){$row['Порядок'] = $row['id'];}
						if($_REQUEST['table'] == 'Пользователи'){
							$row['Название'] = $row['Full_name'];
						}
						if($_REQUEST['table'] == 'црм_счета_Статус_Счета' || $_REQUEST['table'] == 'црм_счета_События_Счета') {
							if (isset($row['Статус'])) {
								$dicTable = '';
								if ($_REQUEST['table'] == 'црм_счета_Статус_Счета') {$dicTable = 'црм_счета_Статусы';}
								if ($_REQUEST['table'] == 'црм_счета_События_Счета') {$dicTable = 'црм_счета_События';}
								if ($dicTable != '') {
									$stPar = $vDataUsr->v_get($Cache['core'], $Cache['cache'], $row['Статус'], '[tig50].[dbo].[' . $dicTable . ']', $field = 'Код');
									$row['Статус'] = $stPar['Название'];
								}
							}
						}
						if($_REQUEST['table'] == 'црм_группы'){
							$row['Тип_Группы'] = $crm_groups_ex->ex_data($row['Тип_Группы']);
						}
						if($_REQUEST['table'] == 'V_Счета') {
							if(isset($row['Пользователи_Код'])){
								$admin = $getAdmin->usr_get($Cache['core'],$Cache['cache'],(int)$row['Пользователи_Код'],0);
								$row['Пользователь'] = $admin['user']['Full_name'];
							}
							if(isset($row['Контрагенты_Код'])) {
								$dicTable =  'Контрагенты';
								$stPar = $vDataUsr->v_get($Cache['core'],$Cache['cache'],$row['Контрагенты_Код'],'[tig50].[dbo].['.$dicTable.']', $field='Код');
								$row['Контрагенты_Код'] = $stPar['Название_RU'];
							}
							//$summ = new V_Summa($Cache,$_REQUEST['base'],$_REQUEST['tprefix']);
							//$row['Итого_Сумма'] = number_format($summ->v_summa($row),2);
							$row['Итого_Сумма'] = number_format($row['Итого_Сумма'],2,'.','');
							$lastArr = $lastTblDate->lastData($row['id']);
							$row['exstatusdat'] = $lastArr['status']['exstatusdat'];
							$row['exstatus'] = $lastArr['status']['exstatus'];
							$row['owner'] = $lastArr['status']['owner'];
							$row['group'] = $lastArr['status']['group'];
							$row['eventdat'] = $lastArr['event']['eventdat'];
							$row['event'] = $lastArr['event']['event'];
							
							$tmpNote = $addNote->getTableNote($row['id'],2);
							if($tmpNote != ''){
								$row['Примечание'] = $tmpNote;
							}
						}
						//
						if($_REQUEST['table'] == 'Контрагенты_Работа') {
							$row['Дата'] = $Cache['dateSQL']->dateFromSql($row['Дата']);
							$row['Дата_События'] = $Cache['dateSQL']->dateFromSql($row['Дата_События']);
							$SqL = "SELECT count(*) AS [cnt] FROM [tig50].[dbo].[црм_Файлы] WHERE [Цель] = '".$row['Контрагент_Код']."'";
							$Cache['core']->query = $SqL;
							$Cache['core']->con_database('tig50');
							$ReS = $Cache['core']->PDO();
							$RoW = $ReS->fetch();
							//$CounT = $RoW['cnt'];
							if($RoW['cnt'] > 0){
								$row['Контрагент'] .= " (".$RoW['cnt'].")";
							}
						}
						if($_REQUEST['table'] == 'црм_счета_Статус_Счета'){
							if(isset($row['Владелец'])){
								$admin = $getAdmin->usr_get($Cache['core'],$Cache['cache'],(int)$row['Владелец'],0);
								$row['Владелец'] = $admin['user']['Full_name'];
							}
							if(isset($row['Группа'])){ //($Core,$Cache,$vData,$bTable, $field='Код')
								$group = $vDataUsr->v_get($Cache['core'],$Cache['cache'],(int)$row['Группа'],'[srv].[dbo].[црм_группы]','Код');
								$row['Группа'] = $group['Название'];
							}
						}
						if(isset($row['Пользователь']) && $_REQUEST['table'] == 'црм_Примечания'){
							$admin = $getAdmin->usr_get($Cache['core'],$Cache['cache'],(int)$row['Пользователь'],0);
							$row['Пользователь'] = $admin['user']['Full_name'];
						}
						if(isset($row['Администратор'])){
							$admin = $getAdmin->usr_get($Cache['core'],$Cache['cache'],(int)$row['Администратор'],0);
							$row['Администратор'] = $admin['user']['Full_name'];
						}
						foreach($exArray as $k => $v){ //Обходим массив внешних подключений
							$row[$k] = $this->excludeField($v,$row[$v[3]],$Cache['core']); //И на каждое запускаем процедуру подключения
						}
						foreach($chArray as $k => $v){//Делаем динамическую замену полей
							if($row[$k] != ''){
								$row[$k] = $this->dynExFields($Cache,$k,$v,$row); // отдаем название по этому ключу
							}
						}
						foreach ($row as $k => $v){
							if(mb_substr(trim($k),0,4) == 'Дата'){	$row[$k] = $Cache['dataconv']->dataCorrect($row[$k]);	}
						}
						if($row['Статус_Дата'])$row['Статус_Дата'] = $Cache['dataconv']->dataCorrect($row['Статус_Дата']);
						if($row['Статус_Дата_Delay'])$row['Статус_Дата_Delay'] = $Cache['dataconv']->dataCorrect($row['Статус_Дата_Delay']);
						$OUT[] = $row;
					}
					$sqlA = "SELECT count(*) AS cnt FROM ".$baseName." ".$addSelect." WHERE 1 = 1 ".$filter1.$filterBill;
					$Cache['core']->query = $sqlA;
					$Cache['core']->con_database($_REQUEST['base']);
					$res = $Cache['core']->PDO();
					$row = $res->fetch();
					$Count = $row['cnt'];
					$total_pages = ceil($Count / $limit);
					$out['page'] = $page;
					$out['total'] = $total_pages;
					$out['records'] = $Count;
					$out['nowdate'] = date(Y) . '-' . date(m) . '-' . date(d);
					if($_REQUEST['mode'] < 1 || $_REQUEST['mode'] == 0){ //Задаем формат массива на выходе: это массив для jqGrid с постраничным делением
						$out['rows'] = $OUT;
					}elseif($_REQUEST['mode'] == 1){//Простой массив без деления на страницы
						unset($out);
						$out = $OUT;
					}
					if($Cache['cacherules']['stop'] == 0){
						$Cache['cache']->add($keyy,$out);
					}
					//if(trim($_SESSION['dbo_error']) != ''){$out['emess'] = 1;}
				}
				echo json_encode($out, JSON_UNESCAPED_UNICODE);
			}
			if($_REQUEST['privat'] > 0 && $Cache['cacherules']['privatCache'] != 1){
				$Cache['cache']->add($keyX,$out);
			}
			//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2v.txt", "w");fputs($des, $sql.' '.$_SESSION['dbo_error']."\n".$r); fclose($des);
			//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2v.txt", "w");fputs($des, $sql.' '.$_SESSION['dbo_error']."\n".$r); fclose($des);
		}
	}
	private function dynExFields($Cache,$k,$v,$row){
		$basArr = explode('!',$v[1]);
		if(trim($basArr[1] != '')){$baseName = "[".$basArr[1]."]";$basE = $basArr[1];}else{$baseName = "[".$_REQUEST['base']."]";$basE = $_REQUEST['base'];}
		if(trim($basArr[2] != '')){$baseName .= ".[".$basArr[2]."]";}else{$baseName .= ".[".$_REQUEST['tprefix']."]";}
		$baseName .= ".[".$basArr[0]."]"; //Составляем имя базы; если префикса или базы не указали, берем дефолтные
		$sqlM = "SELECT TOP 1 * FROM ".$baseName." WHERE [Код] = '".$row[$k]."' "; //ерем в указаной базе ключ по переданному значению
		//$r = print_r($sqlM,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rr3mm.txt", "w");fputs($des,$k.'|||'.$r); fclose($des);

		$Cache['core']->query = $sqlM;
		$Cache['core']->con_database($basE);
		$resM = $Cache['core']->PDO();
		$rowM = $resM->fetch();
		return $rowM['Название']; // отдаем название по этому ключу

	}
	private function excludeField($datArray,$keyField,$Core) { //Функция нешнегго значения
		$basArr = explode('!',$datArray[1]);
		if(trim($basArr[1] != '')){$baseName = "[".$basArr[1]."]";$basE = $basArr[1];}else{$baseName = "[".$_REQUEST['base']."]";$basE = $_REQUEST['base'];}
		if(trim($basArr[2] != '')){$baseName .= ".[".$basArr[2]."]";}else{$baseName .= ".[".$_REQUEST['tprefix']."]";}
		$baseName .= ".[".$basArr[0]."]";
		$sql = "SELECT TOP 1 * FROM ".$baseName." WHERE [".$datArray[2]."] = '".addslashes($keyField)."' ";
		if($datArray[4] == 'last'){
			$sql .= " ORDER BY [Код] DESC ";
		}
		//if($keyField == '58045'){
		//	$r2 = print_r($datArray,1);$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2v.txt", "a");fputs($des, $sql.' '.$r2."\n".$r); fclose($des);
		//}
		$res = $Core->query = $sql;
		$Core->con_database($basE);
		$res = $Core->PDO();
		$row = $res->fetch();
		return $row[$datArray[5]];
	}
}
class del_single_table {
	function __construct ($Cache) {
		$rFlag = 0;
		if($_REQUEST['table'] == 'црм_счета_Статус_Счета' && $_SESSION['usr']['rgt']['bask'][8] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_счета_Статусы' && $_SESSION['usr']['rgt']['bask'][8] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_Права' && $_SESSION['usr']['rgt']['bask'][5] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_группы' && $_SESSION['usr']['rgt']['bask'][3] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Пользователи' && $_SESSION['usr']['rgt']['bask'][3] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Контрагенты_Работа' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Темы' && $_SESSION['usr']['rgt']['bask'][21] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Типы' && $_SESSION['usr']['rgt']['bask'][21] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_Праздники' && $_SESSION['usr']['rgt']['bask'][21] == 1){$rFlag = 1;}
		$baseName = " [" . $_REQUEST['base'] . "].[" . $_REQUEST['tprefix'] . "].[" . $_REQUEST['table'] . "] ";
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2v0.txt", "w");fputs($des, $r.'||||||'.$rFlag); fclose($des);
		if($_REQUEST['id'] > 0 && $rFlag == 1){
			$sql = "DELETE FROM ".$baseName." WHERE [Код] = ".(int)$_REQUEST['id'];
			//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2v.txt", "w");fputs($des, $sql); fclose($des);
			$Cache['core']->query = $sql;
			$Cache['core']->PDO(array("exec" => true));
			if($_REQUEST['table'] == 'црм_счета_Статус_Счета'){
				require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/servers/srv01lib/del_single_table_lastsync.php';
				new syncLastTable($Cache['core'],$_REQUEST['id']);
			}
			if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
		}
	}
}
class get_table_line{
	function __construct ($Cache) {
		error_reporting(E_ERROR);
		unset($out,$Out);
		$rFlag = 0;
		$id = $_REQUEST['id'];
		if(trim($id) == ''){$id = 'Код';}
		if ($_REQUEST['table'] == 'Шаблоны_Заданий' && $_SESSION['usr']['rgt']['bask'][22] == 1) {$rFlag = 1;}
		if ($_REQUEST['table'] == 'црм_счета_События_Счета' && $_SESSION['usr']['rgt']['bask'][4] == 1) {$rFlag = 1;}
		if ($_REQUEST['table'] == 'V_Счета' && $_SESSION['usr']['rgt']['bask'][1] == 1) {$rFlag = 1;}
		if ($_REQUEST['table'] == 'црм_счета_Статус_Счета' && $_SESSION['usr']['rgt']['bask'][4] == 1) {$rFlag = 1;}
		if ($_REQUEST['table'] == 'Контрагенты_Работа' && $_SESSION['usr']['rgt']['bask'][20] == 1) {$rFlag = 1;}
		
		if ($rFlag == 0) {die;}
		$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		$keyy = __CLASS__.$baseName.$_REQUEST['id'].$id.$_REQUEST['v'];
		$out = $Cache['cache']->get($keyy);
		if($out != 'false' && $out != '' && $Cache['cacherules']['stop'] != 1){
		}else {
			if(isset($_REQUEST['external'])){ //Если передан список полей, в которых ID нужно заменить значениями
				$external = urldecode(trim($_REQUEST['external']));
				$extArr = explode('|',$external);
				foreach($extArr as $k => $v){
					if(trim($v) != ''){
						$inExt[] = $v; //Составляем два массива - прямой и обратный
						$outExt[$v] = 1; //Форматируем эти поля как ключи обратного массива (чтобы было легче искать)
					}
				}
			}
			$sql0 = "SELECT * FROM ".$baseName." WHERE [".addslashes($id)."] = '".addslashes($_REQUEST['v'])."'";
			$Cache['core']->query = $sql0;
			$Cache['core']->con_database($_REQUEST['base']);
			$res0 = $Cache['core']->PDO();
			$row0 = $res0->fetch();
			foreach($row0 as $k => $v){
				if(mb_substr($k,0,4) == 'Дата'){
					$row0[$k] = $Cache['dateSQLinv']->dateFromSql($v);
					//$row0[$k] = $Cache['dataconv']->dataCorrect($v);
				}
				if($outExt[$k] == 1){
					if($k == 'Администратор' || $k == 'Пользователи_Код'){
						$row0[$k] = $this->getExtAdm($Cache,$v);
					}
					if($k == 'Статус'){
						$row0[$k] = $this->getExtStatu($Cache,$v);
					}
				}
				$row0[$k] = stripslashes($row0[$k]);
				$row0[$k] = stripslashes($row0[$k]);
			}
			$out = $row0;
		}
		//if(trim($_SESSION['dbo_error']) != ''){$out['emess'] = 1;}else{$out['emess'] = '0';}
		$Out = json_encode($out, JSON_UNESCAPED_UNICODE);
		//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2o.txt", "w");fputs($des, $Out."\n".$r); fclose($des);
		//foreach($out as )
		echo $Out;
	}
	function getExtAdm($Cache,$value){ //Поиск админа по ключу
		$out = $this->getField($Cache,'srv','dbo','Пользователи',$value);
		return $out['Full_name'];
	}
	function getExtStatu($Cache,$value){ //Поиск админа по ключу
		if($_REQUEST['table'] == 'црм_счета_Статус_Счета'){$dicTable =  'црм_счета_Статусы';}
		if($_REQUEST['table'] == 'црм_счета_События_Счета'){$dicTable =  'црм_счета_События';}
		$out = $this->getField($Cache,'tig50','dbo',$dicTable,$value);
		return $out['Название'];
	}
	function getField($Cache,$base,$prefix,$table,$id){
		$baseName = " [".$base."].[".$prefix."].[".$table."] ";
		$sql = "SELECT * FROM ".$baseName." WHERE [Код] = '".(int)$id."'";
		$Cache['core']->query = $sql;
		$Cache['core']->con_database($base);
		$res0 = $Cache['core']->PDO();
		$row0 = $res0->fetch();
		return $row0;
	}
}
class put_table_line{
	function __construct ($Cache) {
		unset($sqlArr,$sqlArrK,$sqlArrV,$fNull);
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2qr.txt", "w");fputs($des, $r."\n"); fclose($des);
		$rFlag = 0;
		if ($_REQUEST['table'] == 'Шаблоны_Заданий' && $_SESSION['usr']['rgt']['bask'][22] == 1) {$rFlag = 1;}
		if ($_REQUEST['table'] == 'црм_счета_События_Счета' && $_SESSION['usr']['rgt']['bask'][15] == 1) {$rFlag = 1;	}
		if ($_REQUEST['table'] == 'црм_счета_Статус_Счета' && $_SESSION['usr']['rgt']['bask'][8] == 1) {$rFlag = 1;	}
		if ($_REQUEST['table'] == 'црм_Примечания' && $_SESSION['usr']['rgt']['bask'][9] == 1) {$rFlag = 1;	}
		if ($_REQUEST['table'] == 'црм_группы' && $_SESSION['usr']['rgt']['bask'][4] == 1) {$rFlag = 1;	}
		if($_REQUEST['table'] == 'Контрагенты' && $_SESSION['usr']['rgt']['bask'][4] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Контрагенты_Контакты' && $_SESSION['usr']['rgt']['bask'][4] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Контрагенты_Работа' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Типы' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Темы' && $_SESSION['usr']['rgt']['bask'][20] == 1){$rFlag = 1;}
		if ($rFlag == 0) {die;}
		$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		if($_REQUEST['modus'] == 1){
			$sql0 = "SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME='".$_REQUEST['table']."'";
			$this->Core->query = $sql0;
			$this->Core->con_database($_REQUEST['base']);
			$stm = $this->Core->PDO();
			while($row = $stm->fetch()){
				$tRow[] = $row['COLUMN_NAME'];
				$fNull[$row['COLUMN_NAME']] = $row['IS_NULLABRE'];
			}
		}
		$savBlcArr = explode('|',urldecode($_REQUEST['d']));
		//$r = print_r($fNull,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $sql0.'  '.$r."\n"); fclose($des);
		foreach($savBlcArr as $k => $v){
			$cArr = explode('#', trim($v));
			$cArr[1] = stripcslashes($cArr[1]);
			$cArr[1] = stripcslashes($cArr[1]);
			$cArr[1] = stripcslashes($cArr[1]);
			$cArr[1] = stripcslashes($cArr[1]);
			if(trim($cArr[0]) != ''){
				if(mb_substr($cArr[0],0,4) == 'Дата'){
					$cArr[1] = $Cache['dateSQLinv']->dateToSql(trim($cArr[1]));
					if($cArr[1] != ''){
						$sqlArr[] = " [".$cArr[0]."] = convert(date, '" .$cArr[1]. "', 102) ";
						$sqlArrK[] = " [".$cArr[0]."] ";
						$sqlArrV[] = " convert(date, '" .$cArr[1]. "', 102) ";
					}
				}else{
					if($fNull[$cArr[0]] == 'YES'){
						$sqlArr[] = " [".$cArr[0]."] = null ";
						$sqlArrK[] = " [".$cArr[0]."] ";
						$sqlArrV[] = " null ";
					}else{
						if($cArr[1] == 'null'){$cArr[1] = '';}
						$sqlArr[] = " [".$cArr[0]."] = '".addslashes($cArr[1])."' ";
						$sqlArrK[] = " [".$cArr[0]."] ";
						$sqlArrV[] = " '".addslashes($cArr[1])."' ";
					}
				}
			}
		}
		if(trim($_REQUEST['v']) != '' && trim($_REQUEST['v']) != 'undefined'){
			$sql = "UPDATE ".$baseName." SET ".implode(',',$sqlArr)." WHERE [".$_REQUEST['id']."] = '".addslashes($_REQUEST['v'])."' ";
			//$r2 = print_r($_REQUEST,1);$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2r_GG2222.txt", "a");fputs($des, $r2.' '.$r."\n"); fclose($des);
			$OutId = $_REQUEST['v'];
		}else{
			$sql = "INSERT INTO ".$baseName." (".implode(',',$sqlArrK).") VALUES (".implode(',',$sqlArrV).") ";
		}
		//$r2 = print_r($_REQUEST,1);$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2r_GG.txt", "a");fputs($des, $r2.' '.$r."\n"); fclose($des);
		$Cache['core']->query = $sql;
		$Cache['core']->con_database($_REQUEST['base']);
		$Cache['core']->PDO(array("exec" => true));
		if($OutId < 1){
			$SQl = "SELECT TOP 1 * FROM ".$baseName." WHERE ".implode(' AND ',$sqlArr)." ORDER BY [Код] DESC";
			//$r2 = print_r($_REQUEST,1);$r = print_r($SQl,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2r_GG1.txt", "a");fputs($des, $r2.' '.$r."\n"); fclose($des);
			//$SQl = "SELECT scope_identity() AS [newID]";
			$Cache['core']->query = $SQl;
			$Cache['core']->con_database($_REQUEST['base']);
			$stm1 = $Cache['core']->PDO();
			$row1 = $stm1->fetch();
			$OutId = $row1['Код'];
		}
		//$r2 = print_r($_REQUEST,1);$r = print_r($sql,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2r_WW.txt", "a");fputs($des, $OutId.' |||| '.$r2.' '.$r."\n"); fclose($des);
		print $OutId;
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}
class put_single_table{
	function __construct ($Cache) {
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2b.txt", "w");fputs($des, $_SESSION['usr']['rgt']['bask'][16].'  '.$r); fclose($des);
		$rFlag = 0;
		if ($_REQUEST['table'] == 'црм_Праздники' && $_SESSION['usr']['rgt']['bask'][16] == 1) {$rFlag = 1;	}
		if ($_REQUEST['table'] == 'црм_счета_События' && $_SESSION['usr']['rgt']['bask'][15] == 1) {$rFlag = 1;	}
		if($_REQUEST['table'] == 'црм_счета_Статусы' && $_SESSION['usr']['rgt']['bask'][8] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_Права' && $_SESSION['usr']['rgt']['bask'][6] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_группы' && $_SESSION['usr']['rgt']['bask'][4] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'Пользователи' && $_SESSION['usr']['rgt']['bask'][4] == 1){$rFlag = 1;}
		if($_REQUEST['table'] == 'црм_вызовы_Типы' && $_SESSION['usr']['rgt']['bask'][21] == 1){$rFlag = 1;}
		//$r = print_r($_REQUEST,1);$r1 = print_r($_GET,1);$r2 = print_r($_POST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_.txt", "w");fputs($des, $r1.'  '.$r1.'  '.$r); fclose($des);

		if($rFlag == 0){die;}
		$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		$sql1 = "SELECT name FROM syscolumns WHERE id=object_id('" . $_REQUEST['table'] . "')";
		$Cache['core']->query = $sql1;
		$Cache['core']->con_database($_REQUEST['base']);
		$res = $Cache['core']->PDO();
		while ($row = $res->fetch()) {
			$row1[$row['name']] = 1;
		}
		if (trim($_REQUEST['id']) == '_empty' || (trim($_REQUEST['id']) == '' && $_REQUEST['table'] == 'црм_Праздники')) {
			if ($_REQUEST['table'] == 'Заказы_Состав') {
				$sqlZ = "INSERT INTO " . $baseName . " ([Заказы_Код],[Вид_Груза_Код])VALUES('" . (int)$_REQUEST['oid'] . "','0')";
			} elseif ($_REQUEST['table'] == 'црм_Праздники') {
				$sqlZ = "INSERT INTO " . $baseName . " ([Год])VALUES('" . (int)$_REQUEST['Год'] . "')";
			} elseif ($_REQUEST['table'] == 'Пользователи') {
				$configPath = $_SERVER['DOCUMENT_ROOT'] . '/config.ini';
				$Config = parse_ini_file($configPath, 1); //Конфиг сайта
				$passw = crypt($Config['main']['defpass'], $Config['main']['prefix']);
				$marker = crypt(rand(10000, 99999999), '345');
				$restNum = rand(500, 9999999);
				$restHash = md5($restNum);
				$sqlZ = "INSERT INTO " . $baseName . " ([Название],[Статус],[Пароль],[Хэш],[Маркер])VALUES('New',0,'" . $passw . "','" . $restHash . "','" . $marker . "')";
			} else {
				$sqlZ = "INSERT INTO " . $baseName . " ([Название],[Статус])VALUES('New',0)";
			}
			//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2xx.txt", "w");fputs($des, $r); fclose($des);
			//$r = print_r($sqlZ."\n",1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2xxx.txt", "a");fputs($des, $r); fclose($des);
			$Cache['core']->query = $sqlZ;
			$Cache['core']->con_database($_REQUEST['base']);
			$Cache['core']->PDO(array("exec" => true));
			$sql0 = "SELECT TOP 1 * FROM " . $baseName . " ORDER BY [Код] DESC";
			$Cache['core']->query = $sql0;
			$Cache['core']->con_database($_REQUEST['base']);
			$res = $Cache['core']->PDO();
			$row0 = $res->fetch();
			$_REQUEST['id'] = $row0['Код'];
		}
		$sortFlag = 0;
		//$r = print_r($row1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "a");fputs($des, $r."\n"); fclose($des);
		foreach ($row1 as $k => $v) {
			if (isset($_REQUEST[$k]) && $k != 'table' && $k != 'tprefix' && $k != 'base' && $k != 'id' && $k != 'oper' && $k != 'Код' && $k != 'm') {
				if ($k == 'Порядок' || $k == 'Длительность_Дней' || $k == 'Длительность_Часов') { // $k == 'Длительность_дней' || $k == 'Длительность_часов'
					$sortFlag = 1;
					if (trim($_REQUEST[$k]) == '') {
						$_REQUEST[$k] = '0';
					}
					$sql2 = "UPDATE " . $baseName . " SET [" . addslashes($k) . "] = '" . trim((int)$_REQUEST[$k]) . "' WHERE [Код] = '" . (int)$_REQUEST['id'] . "'";
				} else {
					$sql2 = "UPDATE " . $baseName . " SET [" . addslashes($k) . "] = '" . addslashes($_REQUEST[$k]) . "' WHERE [Код] = '" . (int)$_REQUEST['id'] . "'";
				}
				//$r = print_r($sql2."\n",1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2w.txt", "a");fputs($des, $r); fclose($des);
				$Cache['core']->query = $sql2;
				$Cache['core']->PDO(array("exec" => true));
			}
		}
		if ($sortFlag == 1) {
			$oCount = 1;
			$sqlA = "SELECT * FROM " . $baseName . " ORDER BY [Порядок],[Код]";
			$Cache['core']->query = $sqlA;
			$Cache['core']->con_database($_REQUEST['base']);
			$res = $Cache['core']->PDO();
			while ($row = $res->fetch()) {
				$sqlI = "UPDATE " . $baseName . " SET [Порядок] = '" . (int)$oCount . "' WHERE [Код] = '" . (int)$row['Код'] . "'";
				$Cache['core']->query = $sqlI;
				$Cache['core']->PDO(array("exec" => true));
				$oCount++;
			}
		}
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
		//}
	}
}
class get_group_rights{
	function __construct ($Cache) {
		//if($_SESSION['usr']['rgt']['bask'][3] != 1){die;}
		@$keyy = __CLASS__.$_REQUEST['g'];
		$out = $Cache['cache']->get($keyy);
		if($out != 'false' && $out != '' && $Cache['cacherules']['stop'] != 1){
		}else{
			$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
			$sql0 = "SELECT TOP 1000000 * FROM ".$baseName." WHERE [Группа] = '".(int)$_REQUEST['g']."'";
			//$r = print_r($sql0."\n",1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2wss.txt", "a");fputs($des, $r); fclose($des);
			$Cache['core']->query = $sql0;
			$Cache['core']->con_database($_REQUEST['base']);
			$res = $Cache['core']->PDO();
			while ($row = $res->fetch()) {
				$out[$row['Право']] = 1;
			}
			if($Cache['cacherules']['stop'] == 0)$Cache['cache']->add($keyy,$out);
		}
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
	}
}
class get_group_ids{
	function __construct ($Cache) {
		if($_SESSION['usr']['rgt']['bask'][3] != 1){die;}
		@$keyy = __CLASS__.$_REQUEST['g'];
		$out = $Cache['cache']->get($keyy);
		if($out != 'false' && $out != '' && $Cache['cacherules']['stop'] != 1){
		}else{
			//$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
			$sql0 = "SELECT TOP 1000000 * FROM  [srv].[dbo].[црм_Пользователь_Группа] WHERE [Группа] = '".(int)$_REQUEST['g']."'";
			$Cache['core']->query = $sql0;
			$Cache['core']->con_database('srv');
			$res = $Cache['core']->PDO();
			while ($row = $res->fetch()) {
				$out[$row['Пользователь']] = 1;
			}
			if($Cache['cacherules']['stop'] == 0)$Cache['cache']->add($keyy,$out);
		}
		//print $sql0;
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
	}
}
class put_group_ids{
	function __construct ($Cache) {
		if($_SESSION['usr']['rgt']['bask'][4] != 1){die;}
		//$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		$sql0 = "SELECT count(*) AS [cnt] FROM  [srv].[dbo].[црм_Пользователь_Группа] WHERE [Группа] = '".(int)$_REQUEST['g']."' AND [Пользователь] = '".(int)$_REQUEST['u']."'";
		$Cache['core']->query = $sql0;
		$Cache['core']->con_database('srv');
		$res = $Cache['core']->PDO();
		$row = $res->fetch();
		if($row['cnt'] == 1){
			$sql1 = "DELETE FROM [srv].[dbo].[црм_Пользователь_Группа] WHERE [Группа] = '".(int)$_REQUEST['g']."' AND [Пользователь] = '".(int)$_REQUEST['u']."'";
			$Cache['core']->query = $sql1;
			$Cache['core']->PDO(array("exec" => true));
		}else{
			$sql1 = "INSERT INTO [srv].[dbo].[црм_Пользователь_Группа] ([Группа],[Пользователь])VALUES('".(int)$_REQUEST['g']."','".(int)$_REQUEST['u']."')";
			$Cache['core']->query = $sql1;
			$Cache['core']->PDO(array("exec" => true));
		}
		if($Cache['cacherules']['isflush'] == 1 && $Cache['cacherules']['stop'] != 1)$Cache['cache']->flush();
	}
}
?>

