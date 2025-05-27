<?php
class syncLastTable{
	function __construct ($Core,$bill) {
		$sql1 = "SELECT TOP 1 * FROM [tig50].[dbo].[црм_счета_Статус_Счета] WHERE [Счет] = '".(int)$bill."' ORDER BY [Код] DESC"; //Получаем последний статус текущего счета
		//$r = print_r($sql1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_.txt", "w");fputs($des, $r); fclose($des);
		$Core->query = $sql1;
		$Core->con_database('tig50');
		$stm1 = $Core->PDO();
		$lastStatus = $stm1->fetch();
		$sql2 = "DELETE FROM [tig50].[dbo].[црм_Счета_последние_Статусы] WHERE [Счет] = '".(int)$bill."'";
		$Core->query = $sql2;
		$Core->PDO(array("exec" => true));//Чистим таблицу последних статусов по текущему счету
		$sql3 = "INSERT INTO [tig50].[dbo].[црм_Счета_последние_Статусы] ([Счет],[Статус],[Владелец],[Группа])VALUES('".(int)$bill."','".(int)$lastStatus['Статус']."','".(int)$lastStatus['Владелец']."','".(int)$lastStatus['Группа']."')";
		//$r = print_r($sql1,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_.txt", "w");fputs($des, $sql1.'|||'.$sql2.'|||'.$sql3); fclose($des);
		$Core->query = $sql3;
		$Core->PDO(array("exec" => true));//Записываем последний статус в таблицу сопоставления, это понадобится для быстрого вывода таблицы счетов
	}
}
?>