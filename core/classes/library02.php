<?php
class get_admin{
	function usr_get($Core,$Cache,$user,$grpMod=0) { //$grpMod > 0 - запрашивать группы пользователя? 2-с именами групп
		if(is_int($user)){
			$SQl = "SELECT * FROM [srv].[dbo].[Пользователи] WHERE [Код] = '".(int)$user."'";
		}else{
			$SQl = "SELECT * FROM [srv].[dbo].[Пользователи] WHERE [Login] = '".addslashes($user)."'";
		}
		$Core->query = $SQl;
		$Core->con_database('srv');
		$stm = $Core->PDO();
		$uzwerFull = $stm->fetch();
		if($grpMod > 0){
			$sql1 = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Группа] WHERE [Пользователь] = '".(int)$uzwerFull['Код']."'";
			$Core->query = $sql1;
			$Core->con_database('srv');
			$stm = $Core->PDO();
			while($row1 = $stm->fetch()){
				$groups[] = $row1['Группа'];
				$groupsBack[$row1['Группа']] = 1;
			}
			//$r = print_r($groups,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2a.txt", "w");fputs($des, $r); fclose($des);
			if($grpMod == 2){
				$sql2 = "SELECT * FROM [srv].[dbo].[црм_группы] WHERE [Код] IN (".implode(',',$groups).")";
				$Core->query = $sql2;
				$Core->con_database('srv');
				$stm = $Core->PDO();
				while($row2 = $stm->fetch()){
					$groupsNames[] = $row2['Название'];
				}
			}
		}
		return array('user'=>$uzwerFull,'groups'=>$groups,'groupsback'=>$groupsBack,'groupnames'=>$groupsNames);
		
	}
}
class get_vdata{
	function v_get($Core,$Cache,$vData,$bTable, $field='Код') {
		$SQl = "SELECT * FROM ".$bTable." WHERE [".$field."] = '".(int)$vData."'";
		//$r = print_r($groups,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2a.txt", "a");fputs($des, $SQl."\n"); fclose($des);
		$Core->query = $SQl;
		$Core->con_database('srv');
		$stm = $Core->PDO();
		$vDataFull = $stm->fetch();
		return $vDataFull;
	}
}
class create_line_select{
	function __construct ($Core) { //$stream-массив $_REQUEST;
		$this->Core = $Core;
	}
	function saveTable($base,$tprefix,$table,$stream,$excludeArr,$mode='insert',$fielD='Код',$fValue=''){
		unset($tRow, $fRow, $fNam, $namArr, $valArr, $updArr);
		//$excludeArr - массив вида $excludeArr['Имя_Поля'] = 1, блокирующий рассмотрение соответствующих полей
		$baseName = '['.$base.']'.'.'.'['.$tprefix.']'.'.'.'['.$table.']';
		//$excludeArr[] =
		$sql0 = "SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME='".$table."'";
		$this->Core->query = $sql0;
		$this->Core->con_database($base);
		$stm = $this->Core->PDO();
		while($row = $stm->fetch()){
			$tRow[] = $row['COLUMN_NAME'];
			if($excludeArr[$row['COLUMN_NAME']] < 1){
				$fRow[] = $row['COLUMN_NAME'];
				$fNam[$row['COLUMN_NAME']] = 1;
			}
		}
		foreach($fRow as $k => $v){
			unset($namTmp,$valTmp,$updTmp);
			if(isset($stream[$v])){
				$namTmp = ' ['.$v.'] ';
				if(mb_substr(trim($v),0,4) == 'Дата'){
					$valTmp = " convert(date, '" .$stream[$v]. "', 102) ";
				}else{
					if($v == 'Город_Код'){
						$valTmp = " '".(int)$stream[$v]."' ";
					}else{
						$valTmp = " '".trim(addslashes($stream[$v]))."' ";
					}
				}
				if(trim($stream[$v]) != '' && trim($stream[$v]) != 'NULL'){
					$namArr[] = $namTmp;
					$valArr[] = $valTmp;
					$updArr[] = $namTmp.' = '.$valTmp;
				}
			}
		}
		//if(count($namArr)){
			if($mode == 'insert'){
				$sql = "INSERT INTO ".$baseName." (".implode(',',$namArr).")VALUES(".implode(',',$valArr).") ";
			}else{
				$sql = "UPDATE ".$baseName." SET ".implode(',',$updArr)." WHERE [".$fielD."] = '".addslashes($fValue)."' ";
			}
			//print $sql."<pre>"; print_r($fRow);print_r($_REQUEST); print "</pre>"; //die;

			//$r = print_r($_REQUEST,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rbr.txt", "w");fputs($des, $sql.'  '.$r); fclose($des);

			$this->Core->query = $sql;
			$this->Core->con_database($base);
			$this->Core->PDO(array("exec" => true));
		//}
	}
}
class create_field_select{
	function __construct ($Core) { //$stream-массив $_REQUEST;
		$this->Core = $Core;
	}
	function saveTable($base,$tprefix,$table,$stream,$excludeArr,$mode='insert',$fielD='Код',$fValue=''){
		unset($tRow, $fRow, $fNam, $namArr, $valArr, $updArr);
		//$excludeArr - массив вида $excludeArr['Имя_Поля'] = 1, блокирующий рассмотрение соответствующих полей
		$baseName = '['.$base.']'.'.'.'['.$tprefix.']'.'.'.'['.$table.']';
		$sql0 = "SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME='".$table."'";
		$this->Core->query = $sql0;
		$this->Core->con_database($base);
		$stm = $this->Core->PDO();
		while($row = $stm->fetch()){
			$tRow[] = $row['COLUMN_NAME'];
			if($excludeArr[$row['COLUMN_NAME']] < 1){
				$fRow[] = $row['COLUMN_NAME'];
				$fNam[$row['COLUMN_NAME']] = 1;
			}
			$fNull[$row['COLUMN_NAME']] = $row['IS_NULLABRE'];
		}
		foreach($fRow as $k => $v){
			unset($namTmp,$valTmp,$updTmp);
			if(isset($stream[$v])){
				$namTmp = ' ['.$v.'] ';
				if(mb_substr(trim($v),0,4) == 'Дата'){
					$valTmp = " convert(date, '" .$stream[$v]. "', 102) ";
				}else{
					if($v == 'Город_Код'){
						$valTmp = " '".(int)$stream[$v]."' ";
					}else{
						$valTmp = " '".trim(addslashes($stream[$v]))."' ";
					}
				}
				if(trim($stream[$v]) != '' && trim($stream[$v]) != 'NULL'){
					$sql = "UPDATE ".$baseName." SET ".$namTmp." = ".$valTmp." WHERE [".$fielD."] = '".addslashes($fValue)."' ";
					$this->Core->query = $sql;
					$this->Core->con_database($base);
					$this->Core->PDO(array("exec" => true));
				}else{
					if($fNull[$v] == 'YES'){
						$sql = "UPDATE ".$baseName." SET ".$namTmp." = null WHERE [".$fielD."] = '".addslashes($fValue)."' ";
						$this->Core->query = $sql;
						$this->Core->con_database($base);
						$this->Core->PDO(array("exec" => true));
					}else{
						$sql = "UPDATE ".$baseName." SET ".$namTmp." = '' WHERE [".$fielD."] = '".addslashes($fValue)."' ";
						$this->Core->query = $sql;
						$this->Core->con_database($base);
						$this->Core->PDO(array("exec" => true));
					}
				}
			}
		}
	}
}
class create_mix_select{
	function __construct ($Core) { //$stream-массив $_REQUEST;
		$this->Core = $Core;
	}
	function saveTable($base,$tprefix,$table,$stream,$excludeArr,$mode='insert',$fielD='Код',$fValue=''){
		unset($tRow, $fRow, $fNam, $namArr, $valArr, $updArr,$reqMain,$fType);
		//$excludeArr - массив вида $excludeArr['Имя_Поля'] = 1, блокирующий рассмотрение соответствующих полей
		$baseName = '['.$base.']'.'.'.'['.$tprefix.']'.'.'.'['.$table.']';
		$sql0 = "SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME='".$table."'";
		$this->Core->query = $sql0;
		$this->Core->con_database($base);
		$stm = $this->Core->PDO();
		while($row = $stm->fetch()){
			$tRow[] = $row['COLUMN_NAME'];
			if($excludeArr[$row['COLUMN_NAME']] < 1){
				$fRow[] = $row['COLUMN_NAME'];
				$fNam[$row['COLUMN_NAME']] = 1;
			}
			$fNull[$row['COLUMN_NAME']] = $row['IS_NULLABRE'];
			$fType[$row['COLUMN_NAME']] = $row['DATA_TYPE'];
		}
		foreach($fRow as $k => $v){
			unset($namTmp,$valTmp,$updTmp);
			if(isset($stream[$v])){
				$namTmp = ' ['.$v.'] ';
				if(mb_substr(trim($v),0,4) == 'Дата'){
					$valTmp = " convert(date, '" .$stream[$v]. "', 102) ";
				}else{
					if($v == 'Город_Код'){
						$valTmp = " '".(int)$stream[$v]."' ";
					}else{
						$valTmp = " CAST('".trim(addslashes($stream[$v]))."' AS ".$fType[$v].") ";
						if(mb_strpos($fType[$v],'char')) {
						}elseif(mb_strpos($fType[$v],'int')){
						}else{
							//$valTmp = " '".trim(addslashes($stream[$v]))."' ";
						}
					}
				}
				if(trim($stream[$v]) != '' && trim($stream[$v]) != 'NULL'){
					$sql = "UPDATE ".$baseName." SET ".$namTmp." = ".$valTmp." WHERE [".$fielD."] = '".addslashes($fValue)."' ";
					$reqMain[] = " ".$namTmp." = ".$valTmp." ";
					//$this->Core->query = $sql;
					//$this->Core->con_database($base);
					//$this->Core->PDO(array("exec" => true));
				}else{
					if($fNull[$v] == 'YES'){
						$sql = "UPDATE ".$baseName." SET ".$namTmp." = null WHERE [".$fielD."] = '".addslashes($fValue)."' ";
						$reqMain[] = " ".$namTmp." = null  ";
						//$this->Core->query = $sql;
						//$this->Core->con_database($base);
						//$this->Core->PDO(array("exec" => true));
					}else{
						$sql = "UPDATE ".$baseName." SET ".$namTmp." = '' WHERE [".$fielD."] = '".addslashes($fValue)."' ";
						//$reqMain[] = " ".$namTmp." = ''  ";
						//$this->Core->query = $sql;
						//$this->Core->con_database($base);
						//$this->Core->PDO(array("exec" => true));
					}
				}
			}
		}
		$sql = "UPDATE ".$baseName." SET ".implode(',',$reqMain)." WHERE [".$fielD."] = '".addslashes($fValue)."' ";
		$this->Core->query = $sql;
		$this->Core->con_database($base);
		$this->Core->PDO(array("exec" => true));
		
	}
}
?>