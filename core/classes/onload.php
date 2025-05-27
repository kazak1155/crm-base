<?php
class onLoad{
	function __construct($Core) {
		$this->Core = $Core;
		$cDate = date('Y') . '-' . date('m') . '-' . date('d');
		$hDate = date('G');
		$sql1 = "SELECT * FROM [tig50].[dbo].[црм_Переменные] WHERE [Ключ] = 'onload'";
		$res1 = $this->Core->query = $sql1;//Выбираем из библиотечной таблицы коды, соответствующие запросу
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		$row1 = $res1->fetch();
		$sql0 = "SELECT * FROM [tig50].[dbo].[црм_Переменные] WHERE [Ключ] = 'onload_h'";
		$res0 = $this->Core->query = $sql0;//Выбираем из библиотечной таблицы коды, соответствующие запросу
		$this->Core->con_database('tig50');
		$res0 = $this->Core->PDO();
		$row0 = $res0->fetch();
		//$r = print_r($row1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $sql1.' ||| '.$r); fclose($des);
		if($row0['Значение'] != $hDate){ //Запускаем первым запуском в час
			$sql2 = "UPDATE [tig50].[dbo].[црм_Переменные] SET [Значение] = '".$hDate."' WHERE [Ключ] = 'onload_h'";
			$this->Core->query = $sql2;
			$this->Core->con_database('tig50');
			$this->Core->PDO(array("exec" => true));
			//$this->cbr();
		}
		if($row1['Значение'] != $cDate){ //апускаем первым запуском в сутки
			$sql2 = "UPDATE [tig50].[dbo].[црм_Переменные] SET [Значение] = '".$cDate."' WHERE [Ключ] = 'onload'";
			$this->Core->query = $sql2;
			$this->Core->con_database('tig50');
			$this->Core->PDO(array("exec" => true));
			$this->cbr();
		}
		//$this->cbr();
	}
	function cbr(){
		$link = "http://www.cbr.ru/scripts/XML_daily.asp";
		$fd = fopen($link, "r");
		$text="";
		$v1 = '/R01235/';
		$v2 = '/R01239/';
		$out = '';
		if (!$fd){}else{
			while (!feof ($fd)){
				$tmp = fgets($fd);
				$out .= $tmp;
			}
			fclose ($fd);
			//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2cours.txt", "w");fputs($des, $r); fclose($des);
			$out = str_replace('> <','><',$out);
			$out = str_replace('><','>|<',$out);
			$oArray = explode('|',$out);
			foreach($oArray as $k => $v){
				if(preg_match($v1,$v)){
					$temp = $oArray[$k+5];
					$temp = str_replace('<Value>','',$temp);
					$temp = str_replace('</Value>','',$temp);
					$dollar = trim($temp);
				}
				if(preg_match($v2,$v)){
					$temp = $oArray[$k+5];
					$temp = str_replace('<Value>','',$temp);
					$temp = str_replace('</Value>','',$temp);
					$euro = trim($temp);
				}
			}
		}
		if($dollar != ''){
			$sql2 = "UPDATE [tig50].[dbo].[црм_Переменные] SET [Значение] = '".$dollar."' WHERE [Ключ] = 'USD'";
			$this->Core->query = $sql2;
			$this->Core->con_database('tig50');
			$this->Core->PDO(array("exec" => true));
		}
		if($euro != ''){
			$sql2 = "UPDATE [tig50].[dbo].[црм_Переменные] SET [Значение] = '".$euro."' WHERE [Ключ] = 'EUR'";
			$this->Core->query = $sql2;
			$this->Core->con_database('tig50');
			$this->Core->PDO(array("exec" => true));
		}
		
	}
}
?>