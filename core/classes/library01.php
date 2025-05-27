<?php
//error_reporting(0);
class fileTypes{
	function checkType($ext){ //print "|||||".$ext.'%%%%%';
		$typeArr['doc'] = 'doc';
		$typeArr['docx'] = 'doc';
		$typeArr['jpg'] = 'jpg';
		$typeArr['jpeg'] = 'jpg';
		$typeArr['jpe'] = 'jpg';
		$typeArr['gif'] = 'jpg';
		$typeArr['png'] = 'jpg';
		$typeArr['bmp'] = 'jpg';
		$typeArr['xlsx'] = 'xls';
		$typeArr['xls'] = 'xls';
		$typeArr['rtf'] = 'rtf';
		$typeArr['pdf'] = 'pdf';
		$typeArr['dll'] = 'fil';
		$typeArr['exe'] = 'fil';
		$typeArr['com'] = 'fil';
		$typeArr['zip'] = 'rar';
		$typeArr['rar'] = 'rar';
		$Ext = strtolower($ext);
		$typ = $typeArr[$Ext];
		if(!$typ){$typ = $Ext;}
		return $typ;
	}
}
class dataConv{
	function dataShift($days){
		$date = date("Y-m-d");
		$date = strtotime($date);
		$date = strtotime($days." day", $date);
		return date('Y-m-d', $date);
	}

	function dataCorrect($dat){
		$tmp = explode(' ', $dat);
		return $tmp[0];
	}
}
class iconvarr{
	function iconvKeys(array &$rgData, $sIn, $sOut)	{
		$rgData = array_combine(
			array_map(
				function($sKey) use ($sIn, $sOut){
					return iconv($sIn, $sOut, $sKey);
				},
				array_keys($rgData)
			),
			array_values($rgData)
		);
		foreach($rgData as &$mValue){
			if(is_array($mValue))
				$mValue = iconvKeys($mValue, $sIn, $sOut);
		}
		return $rgData;
	}
	function keyrecode($line,$modus=0){
		unset($out);
		if(count($line)){ //print "|||||||||||||||||||||||".$v;
			//$r = print_r($line,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2sq.txt", "a");fputs($des, $v.' '.$r."\n"); fclose($des);

			foreach($line as $k => $v){
				$tmp = iconv("cp1251","utf-8//IGNORE",$k);
				if($modus == 1){$v = iconv("cp1251","utf-8//IGNORE",$v);}
				$out[$tmp] = $v;
			}
		}
		return $out;
	}
	function nullrecode($line,$modus=0){
		unset($out);
		if(count($line)){
			foreach($line as $k => $v){
				if($v == 'NULL'){
					$v = '';
				}
				$out[$tmp] = $v;
			}
		}
		return $out;
	}
}
class date_sql{ // -
	function __construct($splitter='-') {
		$this->splitter = $splitter;
	}
	function dateFromSql($dt){
		$dat = explode(' ',$dt);
		$datArray = explode('-',$dat[0]);
		return ($datArray[2].'-'.$datArray[1].'-'.$datArray[0]);
	}
}
class date_sql_inv{
	function __construct($splitter='-') {
		$this->splitter = $splitter;
	}
	function dateFromSql($dt){
		$dat = explode(' ',trim($dt));
		$datArray = explode('-',$dat[0]);
		if(mb_strlen($datArray[0]) == 4){
			$out = $datArray[2].'-'.$datArray[1].'-'.$datArray[0];
		}else{
			$out = $datArray[0].'-'.$datArray[1].'-'.$datArray[2];
			//return $dt;
		}
		if(trim($datArray[0]) != '' && trim($datArray[1]) != '' && trim($datArray[2]) != ''){
			return $out;
		}else{
			return '';
		}
	}
	function dateToSql($dt){
		$dat = explode(' ',trim($dt));
		$datArray = explode('-',$dat[0]);
		if(mb_strlen($datArray[0]) == 4){
			$out = $datArray[0].'-'.$datArray[1].'-'.$datArray[2];
			//return $dt;
		}else{
			$out = $datArray[2].'-'.$datArray[1].'-'.$datArray[0];
		}
		if(trim($datArray[0]) != '' && trim($datArray[1]) != '' && trim($datArray[2]) != ''){
			return $out;
		}else{
			return '';
		}
	}
}
class sqlfunc{ //Составляет запрос для INSERT и UPDATE
	function __construct() {
	}
	function convToArr($updArr){ // Конвертируем массив для UPDATE (вида $sqlArr['Кто_Поставил_Код'] = " [Кто_Поставил_Код] = '". $_SESSION['usr']['user_id']."'";) в два массива для INSERT
		unset($Out,$OutV);
		$Cou = 0;
		foreach($updArr as $k => $v){
			$t = explode('=', $v);
			$Out[$Cou] = $t[0];
			$OutV[$Cou] = $t[1];
			$Cou++;
		}
		return array($Out,$OutV);
	}
	function typeField($rows,$fname){
		$out = '';
		$ftype = $rows[$fname];
		if(mb_strpos('_' . $ftype, 'data')){
			$out = 'd';
		}elseif (mb_strpos('_' . $ftype, 'time')){
			$out = 't';
		}elseif (mb_strpos('_' . $ftype, 'char')){
			$out = 'c';
		}elseif (mb_strpos('_' . $ftype, 'int')) {
			$out = 'i';
		}else{
			$out = 'n';
		}
	}
	function copyRequest($Cache,$rw,$exArray,$table,$modus = 0){
		//$Cache - либо массив Аякс-модуля либо должен содержать массив $Cache['core'], при $modus == 0 может быть пустым
		//$rw - массив из Request (ключ-значение) или иммитируют его
		//$exArray - массив полей-исключений, которые не надо опрашивать
		//$table = имя таблицы вида [tig50].[dbo].[Сотрудники_Задачи], при $modus == 0 может быть пустым
		//$modus 0 - простой режим, таблица не опрашивается. 1 - запрашиваем схему таблицы и проверяем поля по именам (только)
		unset($out,$rows);
		if($modus == 1 || $modus == 2){
			preg_match("/\[([^\]]+)\]\.\[([^\]]+)\]\.\[([^\]]+)\]$/",$table,$oArr);
			$sql0 = "SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME='".$oArr[3]."' ";
			$Cache['core']->query = $sql0;
			$Cache['core']->con_database($oArr[1]);
			$stm = $Cache['core']->PDO();
			while($row = $stm->fetch()) {
				$rows[$row['COLUMN_NAME']] = $row['DATA_TYPE'];
				//$tRow[] = $row['COLUMN_NAME'];
				$fNull[$row['COLUMN_NAME']] = $row['IS_NULLABLE'];
			}
		}
		if($modus == 2){
			foreach ($rw as $k => $v) {
				if (!in_array($k, $exArray)) {
					if($rows[$k] != '') {
						$datType = $this->typeField($rows,$k);
						if ($datType == 'd') {
							$out[$k] = " [" . $k . "] =  convert(date, '" . $v . "', 102) ";
						} elseif($datType == 'i') {
							if(trim($v) == '') {
								if($fNull[$k] == 'YES'){
									$out[$k] = " [" . $k . "] =  null ";
								}else{
									$out[$k] = " [" . $k . "] =  '" . intval($v) . "' ";
								}
							}else{
								$out[$k] = " [" . $k . "] =  '" . intval($v) . "' ";
							}
						} elseif($datType == 'c')  {
							if(trim($v) == ''){
								if($fNull[$k] == 'YES'){
									$out[$k] = " [" . $k . "] =  null ";
								}else{
									$out[$k] = " [" . $k . "] =  '' ";
								}
							}else{
								$out[$k] = " [" . $k . "] =  '" . addslashes($v) . "' ";
							}
						} else {
							$out[$k] = " [" . $k . "] =  '" . addslashes($v) . "' ";
						}
					}
				}
			}
		}
		if($modus == 1){
			foreach ($rw as $k => $v) {
				if (!in_array($k, $exArray)) {
					if($rows[$k] != '') {
						$datPos = mb_strpos('_' . $k, 'Дата');
						if ($datPos) {
							$out[$k] = " [" . $k . "] =  convert(date, '" . $v . "', 102) ";
						} else {
							$out[$k] = " [" . $k . "] =  '" . addslashes($v) . "' ";
						}
					}
				}
			}
		}
		if($modus == 0) {
			foreach($rw as $k => $v){
				if(!in_array($k, $exArray)){
					$datPos = mb_strpos('_'.$k,'Дата');
					if($datPos){
						$out[$k] = " [".$k."] =  convert(date, '" .$v. "', 102) ";
					}else{
						$out[$k] = " [".$k."] =  '".$v."' ";
					}
				}
			}
		}
		//$r2 = print_r($out,1);$r = print_r($fNull,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2Dkay1.txt", "a");fputs($des, ' || '.$r2); fclose($des);
		return $out; //Возвращаем массив для UPDATE вида $sqlArr['Кто_Поставил_Код'] = " [Кто_Поставил_Код] = '". $_SESSION['usr']['user_id']."'";
	}
	
}
?>