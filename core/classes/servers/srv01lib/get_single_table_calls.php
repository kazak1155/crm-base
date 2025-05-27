<?php
class contragenta_works{
	function __construct($Cache) {
		$this->Core = $Cache['core'];
	}
	public function getContragents(){
		$out[0] = " LEFT JOIN [srv].[dbo].[Б_Города] ON [srv].[dbo].[Б_Города].[Код] = [tig50].[dbo].[Контрагенты_Работа].[Город] ";
		$out[1] = " , [srv].[dbo].[Б_Города].[Название] AS [Город_Название] ";
		$out[0] .= " LEFT JOIN [tig50].[dbo].[црм_вызовы_Типы] ON [tig50].[dbo].[црм_вызовы_Типы].[Код] = [tig50].[dbo].[Контрагенты_Работа].[Тип] ";
		$out[1] .= " , [tig50].[dbo].[црм_вызовы_Типы].[Название] AS [Тип_Название] ";
		$out[0] .= " LEFT JOIN [tig50].[dbo].[црм_вызовы_Темы] AS [t0] ON [t0].[Код] = [tig50].[dbo].[Контрагенты_Работа].[Тема] ";
		$out[1] .= " , [t0].[Название] AS [Тема_Название] ";
		$out[0] .= " LEFT JOIN [tig50].[dbo].[црм_вызовы_Темы] AS [t1] ON [t1].[Код] = [tig50].[dbo].[Контрагенты_Работа].[Тема_1] ";
		$out[1] .= " , [t1].[Название] AS [Тема_1_Название] ";
		$out[0] .= " LEFT JOIN [srv].[dbo].[Пользователи] ON [srv].[dbo].[Пользователи].[Код] = [Контрагенты_Работа].[Пользователь_Код] ";
		$out[1] .= " , [srv].[dbo].[Пользователи].[Full_name] AS [Пользователь] ";

		$out[0] .= " LEFT JOIN [tig50].[dbo].[Выбор_клиенты_RU] ON [tig50].[dbo].[Выбор_клиенты_RU].[Код] = [tig50].[dbo].[Контрагенты_Работа].[Контрагент_Код] ";
		$out[1] .= " , [tig50].[dbo].[Выбор_клиенты_RU].[Название] AS [Контрагент] ";
		//$out[3] .= "DECLARE @stus0 varchar(10), @stus1 varchar(10) SET @stus0 = 'Открыто' SET @stus1 = 'Закрыто' ";
		$out[1] .= " ,  IIF ( [tig50].[dbo].[Контрагенты_Работа].[Закрыта] = 1,'Закрыто','Открыто') AS [Статус] ";

		//$out[0] .= " LEFT JOIN [srv].[dbo].[Б_Города] ON [srv].[dbo].[Б_Города].[Код] = [tig50].[dbo].[Контрагенты_Работа].[Город] ";
		//$out[1] .= " , [srv].[dbo].[Б_Города].[Название] AS [Город_Название] ";

		//$flags = json_decode($_REQUEST['flg']);
		$sql = "SELECT [Флаг],[Ключ] FROM [srv].[dbo].[црм_Пользователь_Флаги] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = '".addslashes($_REQUEST['pr'])."' AND [Таблица]= '".addslashes($_REQUEST['gr'])."'";
		$res = $this->Core->query = $sql;
		$this->Core->con_database('srv');
		$res = $this->Core->PDO();
		while($rRow = $res->fetch()){
			$flags[$rRow['Ключ']] = $rRow['Флаг'];
		}
		
		//$r = print_r($flags->flg01,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rr3.txt", "a");fputs($des, $r); fclose($des);
		if($flags['flg01'] == 1){ //Обработчик флага "Только мои"
			$out[2] .= " AND [Пользователь_Код] = '".(int)$_SESSION['usr']['user_id']."' ";
		}
		if($flags['flg02'] == 1){ //Обработчик флага "Только открытые"
			$out[2] .= " AND [Закрыта] = 1 ";
		}else{
			$out[2] .= " AND [Закрыта] = 0 ";
		}
		//$field = '[Контрагенты_Работа].[Дата]';
		//$out[2] .= "  CONCAT(substring(cast(100+DATEPART(day, ".$field.") as varchar),2,2) ,'-',substring(cast(100+DATEPART(month, ".$field.") as varchar),2,2),'-',DATENAME(year, ".$field.")) ";

		return $out;
	}
}
?>