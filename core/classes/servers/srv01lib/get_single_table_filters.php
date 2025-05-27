<?php
class get_single_table_filters{
	function __construct($Core) {
		$this->Core = $Core;
	}
	function trField($reqName,$dat) { //Функция фильтров
		//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2p.txt", "w");fputs($des, $reqName.' '.$r.' '.$out); fclose($des);
		$baseName = " [".$_REQUEST['base']."].[".$_REQUEST['tprefix']."].[".$_REQUEST['table']."] ";
		$datt = $dat;if($reqName == 'id'){$reqName = 'Код';}
		//if($_REQUEST['table'] == 'Контрагенты_Работа') {
		if ($reqName == 'Тип_Название' && $_REQUEST['table'] == 'Контрагенты_Работа') {
			$out = $this->ex_field_hand($reqName, $dat, 'црм_вызовы_Типы', 'Название', 'Код', '', 'Тип');
		} elseif ($reqName == 'Тема_Название' && $_REQUEST['table'] == 'Контрагенты_Работа') {
			$out = $this->ex_field_hand($reqName, $dat, 'црм_вызовы_Темы', 'Название', 'Код', '', 'Тема', ' [tig50].[dbo].[црм_вызовы_Темы].[Родитель] = 0 ');
		} elseif ($reqName == 'Тема_1_Название' && $_REQUEST['table'] == 'Контрагенты_Работа') {
			$out = $this->ex_field_hand($reqName, $dat, 'црм_вызовы_Темы', 'Название', 'Код', '', 'Тема_1', ' [tig50].[dbo].[црм_вызовы_Темы].[Родитель] > 0 ');
		} elseif ($reqName == 'Город_Название' && $_REQUEST['table'] == 'Контрагенты_Работа') {
			$out = $this->ex_field_hand($reqName, $dat, 'Б_Города', 'Название', 'Код', '', 'Город','','srv');
		}elseif ($reqName == 'Пользователь') {
			$out = $this->adminis($reqName, $dat, 'Пользователь_Код');
		}elseif($reqName == 'Контрагент'){
			$out = $this->ex_field_hand($reqName,$dat,'Контрагенты','Название_RU','Код','','Контрагент_Код');
		}elseif($reqName == 'Аналитический_Тег' && $_REQUEST['table'] == 'Контрагенты_Работа'){ //Контрагенты_Работа_АП
			$out = $this->ex_field_hand($reqName,$dat,'Контрагенты_Работа_АП','Название','Код','','АП');
		}elseif($reqName == 'Контрагенты_Код' && $_REQUEST['table'] == 'Контрагенты_Работа'){
			$out = $this->ex_field_hand($reqName,$dat,'Выбор_клиенты_RU','Название','Код','','Контрагенты_Код');
		}elseif($reqName == 'event'){
			$out = $this->event_hand($reqName,$dat,'црм_счета_События','Название','црм_счета_События_Счета','Статус','Счет');
		}elseif($reqName == 'exstatus'){
			$out = $this->event_hand($reqName,$dat,'црм_счета_Статусы','Название','црм_счета_Статус_Счета','Статус','Счет');
		}elseif($reqName == 'exstatusdat'){
			$out = $this->ex_field_hand($reqName,$dat,'црм_счета_Статус_Счета','Дата_Завершения','Счет','dat');
		}elseif($reqName == 'eventdat'){
			$out = $this->ex_field_hand($reqName,$dat,'црм_счета_События_Счета','Дата_Завершения','Счет','dat');
		}elseif($reqName == 'owner'){
			$out = $this->adminis($reqName,$dat,'[црм_Счета_последние_Статусы].[Владелец]');
		}elseif($reqName == 'group'){
			$out = $this->groups($reqName,$dat,'[црм_Счета_последние_Статусы].[Группа]');
			
		}elseif(mb_stripos($reqName,'Итого_Сумма') || $reqName == 'Итого_Сумма'){
			$out = " AND dbo.bill_summ([tig50].[dbo].[V_Счета].[Код]) LIKE('%" . addslashes($dat) . "%') ";
		}elseif($reqName == 'Дата' && $_REQUEST['table'] == 'Контрагенты_Работа'){
			$ouT = " CONCAT(substring(cast(100+DATEPART(day, [".$reqName."]) as varchar),2,2) ,'-',substring(cast(100+DATEPART(month, [".$reqName."]) as varchar),2,2),'-',DATENAME(year, [".$reqName."])) ";
			$out = " AND ".$ouT." LIKE('%" . addslashes($dat) . "%') ";
		}elseif(mb_stripos($reqName,'Дата') || $reqName == 'Дата'){
			$out = $this->ex_data_hand($reqName,$dat);
		}elseif($reqName == 'inGrp'){
			if(preg_match("/не/i",$dat)){
				$out = " AND ([srv].[dbo].[црм_Пользователь_Группа].[Группа] < '1' OR [srv].[dbo].[црм_Пользователь_Группа].[Группа] IS NULL ) ";
			}else{
				$out = " AND [srv].[dbo].[црм_Пользователь_Группа].[Группа] > 0 ";
			}
			
		}else { //Фильтр по основной таблице используется, если нет альтернативы
			$out = " AND ".$baseName.".[" . addslashes($reqName) . "] LIKE('%" . addslashes($dat) . "%') ";
		}
		return $out;
	}
	function ex_data_hand($reqName,$dat) { //Функция с обращением ко внешней таблице
		//$dat = str_replace('-','_',$dat);
		$out = " AND CAST(CAST([" . addslashes($reqName) . "] AS date) AS varchar) LIKE('%" . addslashes($dat) . "%') ";
		return $out;
	}
	function ex_field_hand($reqName,$dat,$linkTable,$tabField1,$tabField2,$mod='',$filterField='Код',$add='',$bas='tig50') { //Функция с обращением ко внешней таблице
		if($mod == 'dat'){
			$sql1 = "SELECT * FROM [".addslashes($bas)."].[dbo].[".$linkTable."] WHERE CAST(CAST([" . addslashes($tabField1) . "] AS date) AS varchar) LIKE('%".$dat."%') ";
		}else{
			$sql1 = "SELECT * FROM [".addslashes($bas)."].[dbo].[".$linkTable."] WHERE [".$tabField1."] LIKE('%".$dat."%') ";
		}
		if($add != ''){
			$sql1 .= " AND ".$add.' ';
		}
		//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2p.txt", "w");fputs($des, $sql1); fclose($des);
		$res1 = $this->Core->query = $sql1;//Выбираем из таблицы коды, соответствующие запросу
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		while($row1 = $res1->fetch()){
			$sCode[] = intval($row1[$tabField2]); // Получаем массив кодов, соответствующих запросу
		}
		//$r = print_r($sCode,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $sql1.' '.$out."\n".$r); fclose($des);
		if(count($sCode)) {//Делаем запрос по итоговому массиву
			$out = " AND [".$filterField."] IN (" . implode(',', $sCode) . ") ";
		}else{ //Или отдаем нуль, если итоговый массив пустой
			$out = " AND [".$filterField."] = '0' " ;
		}
		//$r = print_r($sCode,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2fff.txt", "w");fputs($des, $sql1т.' '.$out."\n".$r); fclose($des);
		return $out;
	}
	function event_hand($reqName,$dat,$libTable,$libField,$linkTable,$tabField1,$tabField2){ //Функция с обращением ко внейней таблице через библиотечную таблицу
		//$sql1 = "SELECT [Код] FROM [tig50].[dbo].[црм_счета_Статусы] WHERE [Название] LIKE('%".$dat."%') ";
		$sql1 = "SELECT [Код] FROM [tig50].[dbo].[".$libTable."] WHERE [".$libField."] LIKE('%".$dat."%') ";
		$res1 = $this->Core->query = $sql1;//Выбираем из библиотечной таблицы коды, соответствующие запросу
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		while($row1 = $res1->fetch()){
			$sCode[] = $row1['Код']; // Получаем массив кодов (ID библиотечной таблицы), соответствующих запросу
		}
		//$sql1 = "SELECT [Код], [Счет] FROM [tig50].[dbo].[црм_счета_Статус_Счета] WHERE [Статус] IN (".implode(',',$sCode).") ";
		$sql1 = "SELECT * FROM [tig50].[dbo].[".$linkTable."] WHERE [".$tabField1."] IN (".implode(',',$sCode).") ";
		$res1 = $this->Core->query = $sql1;//Выбираем из таблицы линковки (между библиотекой и основной таблицей) строки, в которых поле $tabField1 содержит код, соответствующий запросу
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		unset($oStringAr,$workEvents);
		while ($row1 = $res1->fetch()){
			$workEvents[$row1['Код']] = $row1;//Получаем массив, содержащий поля таблицы линковки, соответствующие запросу
		}
		//$r = print_r($workEvents ,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2b.txt", "w");fputs($des, $r); fclose($des);
		foreach($workEvents as $k => $v){ //Для каждого из этих полей вычисляем, последний элемент соответствует запросу или есть следующие (не последний элемент считается неактивным и запросу не удовлетворяет)
			//$sql2 = "SELECT count(*) AS [cnt] FROM [црм_счета_Статус_Счета] WHERE [Счет] = '".$row1['Счет']."' AND [Код] > ".$row1['Код']." ";
			$sql1 = "SELECT count(*) AS [cnt] FROM [".$linkTable."] WHERE [".$tabField2."] = '".$v[$tabField1]."' AND [Код] > ".$v['Код']." ";
			//$r = print_r($workEvents ,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2b.txt", "a");fputs($des, $r); fclose($des);
			$res1 = $this->Core->query = $sql1;
			$this->Core->con_database('tig50');
			$res1 = $this->Core->PDO();
			$row1 = $res1->fetch();
			if($row1['cnt'] == 0){//И если запросу соответствует последний (активный) элемент и следующих нет
				if(trim($v['Счет']) != ''){
					$oStringAr[] = $v['Счет']; //Тогда включаем этот номер в итоговый массив
				}
			}
		}
		if(count($oStringAr)) {//Делаем запрос по итоговому массиву
			$out = " AND [Код] IN (" . implode(',', $oStringAr) . ") ";
		}else{ //Или отдаем нуль, если итоговый массив пустой
			$out = " AND [Код] = '0' " ;
		}
		return $out;
	}
	function adminis($reqName,$dat,$qField='[Пользователи_Код]'){
		$sql1 = "SELECT * FROM [srv].[dbo].[Пользователи] WHERE [Full_name] LIKE('%" . $dat . "%') ";
		$res1 = $this->Core->query = $sql1;
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		while($row1 = $res1->fetch()){
			$oStringArray[] = "'" . $row1['Код'] . "'";
		}
		if (count($oStringArray)) {
			$out = " AND ".$qField." IN (" . implode(',', $oStringArray) . ") ";
		}
		return $out;
	}
	function groups($reqName,$dat,$qField='[црм_Счета_последние_Статусы].[Группа]'){
		$sql1 = "SELECT * FROM [srv].[dbo].[црм_Группы] WHERE [Название] LIKE('%" . $dat . "%') ";
		//$r = print_r($sql1 ,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2b.txt", "w");fputs($des, $r); fclose($des);
		$res1 = $this->Core->query = $sql1;
		$this->Core->con_database('srv');
		$res1 = $this->Core->PDO();
		while($row1 = $res1->fetch()){
			$oStringArray[] = "'" . $row1['Код'] . "'";
		}
		if (count($oStringArray)) {
			$out = " AND ".$qField." IN (" . implode(',', $oStringArray) . ") ";
		}
		return $out;
	}
}
?>