<?php
//session_start();
$Core = new Core;
$Cache = new WinCache();
$isflush = 1; //Сбрасывать весь кеш при записи
$userId = $_SESSION['usr']['user_id'];
//$userId = '86';
if($userId < 1){
	$Core->query = "SELECT * FROM [tig50].[dbo].[Пользователи] WHERE [Login] = '".(int)$_SERVER['PHP_AUTH_USER']."'";
	$Core->con_database('tig50');
	$stm = $Core->PDO();
	$uzwerFull = $stm->fetch();
	$userId = $uzwerFull['Код'];
}
$CdATE = date(Y) . '-' . date(m) . '-' . date(d);
if(isset($_REQUEST['page'])&&($_REQUEST['page'] > 0)){
	$_SESSION['crm']['maintable_page'] = (int)$_REQUEST['page'];
}
if(isset($_POST['sform']) && $_POST['sform'] != '') {
	if($isflush == 1){$Cache->flush();}
	$Core->query = "SELECT TOP 1 * FROM [tig50].[dbo].[Сотрудники_Задачи] ORDER BY [Код]";
	$Core->con_database('tig50');
	$stm = $Core->PDO();
	$roww = $stm->fetch();
	unset($sqlReq,$sqlVal);
	foreach($roww as $k => $v){
		if(trim($_POST[$k]) != ''){
			$sqlReq[] = ' ['.$k.'] ';
			if(mb_substr($k,0,5) == 'Дата_') {
				//$_POST[$k] = trim($_POST[$k]).'T00:00:00.000';
				//print $_POST[$k]; die;
				$sqlVal[] = " convert(datetime, '" . $_POST[$k] . "', 120) ";
			}elseif($k == 'Кто_Поставил_Код'){
				$sqlVal[] = " '".addslashes($userId)."' ";
			}else{
				$sqlVal[] = " '".addslashes($_POST[$k])."' ";
			}
		}
	}
	$sqlReq[] = '[Дата_Постановки]';
	$sqlVal[] = " convert(datetime, '" . $CdATE . "', 120) ";
	$sqlReq[] = ' [Кто_Поставил_Код] ';
	$sqlVal[] = addslashes($userId);
	$sql = "INSERT INTO [tig50].[dbo].[Сотрудники_Задачи] (".implode(',',$sqlReq).") VALUES (".implode(',',$sqlVal).")";
	//print "<pre>"; print_r($_POST); print "</pre>"; print $sql; die;
	$Core->con_database('tig50');
	$Core->query = $sql;
	$Core->PDO(array("exec"=>true));
	//die;
	print "<script>\n location.replace(\"https://".$_SERVER['SERVER_NAME']."\"); \n</script>\n";
}
if(isset($_POST['ecommform']) && $_POST['ecommform'] != '') {
	if($isflush == 1){$Cache->flush();}
	if(trim($_POST['commtext']) != ''){
		$sql = "INSERT INTO [tig50].[dbo].[Задачи_Комментарии] ([Комментарий],[Дата],[Задача],[Пользователь]) VALUES ('".addslashes(trim($_POST['commtext']))."', convert(date, '".$CdATE."', 102), '".(int)$_POST['rowID']."','".$userId."' )";
		$Core->con_database('tig50');
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
		print "<script>\n location.replace(\"https://".$_SERVER['SERVER_NAME']."\"); \n</script>\n";
	}
}
if(isset($_POST['delegateDataForm']) && $_POST['delegateDataForm'] != '') {
	if($isflush == 1){$Cache->flush();}
	$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Кому_Поставили_Пользователь_Код] = '".addslashes($_REQUEST['deleguser'])."' WHERE [Код] = '".(int)$_POST['rowID']."'";
	$Core->con_database('tig50');
	$Core->query = $sql;
	$Core->PDO(array("exec"=>true));
	//print "<script>\n location.replace(\"http://".$_SERVER['SERVER_NAME']."\"); \n</script>\n";
}
if(isset($_POST['newfinaldata']) && $_POST['newfinaldata'] != '') {
	if($isflush == 1){$Cache->flush();}
	if(trim($_POST['lasDate']) != '' && trim($_POST['newDatComment']) != '' ){ //
		//print "<pre>"; print_r($_POST); print "</pre>"; die;
		$Core->query = "SELECT TOP 1 * FROM [tig50].[dbo].[Сотрудники_Задачи] WHERE [Код] = '".(int)$_POST['rowID']."'";
		$Core->con_database('tig50');
		$stm = $Core->PDO();
		$roww = $stm->fetch();
		//$newDatArray = explode();
		$oldDatArray = explode(' ',$roww['Дата_Окончания_Факт']);
		$commenttext = 'Изменение даты завершения задачи c '.$oldDatArray[0].' на '.$_POST['lasDate'].':'."\n".$_POST['newDatComment'];
		$sql = "INSERT INTO [tig50].[dbo].[Задачи_Комментарии] ([Комментарий],[Дата],[Задача],[Пользователь]) VALUES ('".addslashes(trim($commenttext))."', convert(date, '".$CdATE."', 102), '".(int)$_POST['rowID']."','".$userId."' )";
		$Core->con_database('tig50');
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
		$sql = "UPDATE [tig50].[dbo].[Сотрудники_Задачи] SET [Дата_Контрольная] = convert(date, '".$_POST['lasDate']."', 102) WHERE [Код] = '".(int)$_POST['rowID']."' ";
		$Core->con_database('tig50');
		$Core->query = $sql;
		$Core->PDO(array("exec"=>true));
		print "<script>\n location.replace(\"https://".$_SERVER['SERVER_NAME']."\"); \n</script>\n";
	}
}
if(isset($_POST['addtplsubm']) && $_POST['addtplsubm'] != '') {
	if ($isflush == 1) {
		$Cache->flush();
	}
	$sql = "INSERT INTO [tig50].[dbo].[Шаблоны_Заданий]([Шаблон],[Тип_Контакта],[Тип_Обращения],[Тема],[Описание_задачи],[Группе],[Пользователю],[Дата])VALUES(
	'".addslashes($_REQUEST['tplname'])."','".addslashes($_REQUEST['tplcont'])."','".addslashes($_REQUEST['tpltyp'])."','".addslashes($_REQUEST['tpltem'])."',
	 '".addslashes($_REQUEST['tpldes'])."','".addslashes($_REQUEST['tplgrp'])."','".addslashes($_REQUEST['tpluser'])."','".addslashes($_REQUEST['tpldays'])."') ";
	$Core->con_database('tig50');
	$Core->query = $sql;
	$Core->PDO(array("exec"=>true));
	$_SESSION['prime_modus'] = 'horizont5';
	print "<script>\n location.replace(\"https://".$_SERVER['SERVER_NAME']."\"); \n</script>\n";
}
?>