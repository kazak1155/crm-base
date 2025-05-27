<?php
error_reporting(E_ERROR);
class V_Summa{
	function __construct($Cache,$base,$tprefix) {
		$this->Core = $Cache['core'];
		$this->base = $base;
		$this->tprefix = $tprefix;
		
	}
	function v_summa($rOld){
		$summ1 = 0;
		$baseName = " [".$this->base."].[".$this->tprefix."].[Счета_Состав] ";
		$sql0 = "SELECT [Сумма],[Скидка_проц] FROM ".$baseName." WHERE [Счета_Код] = '".$rOld['Код']."'";
		//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2g.txt", "w");fputs($des, $sql0); fclose($des);
		$res0 = $this->Core->query = $sql0;
		$this->Core->con_database($this->base);
		$res0 = $this->Core->PDO();
		while($row0 = $res0->fetch()){
			$summ1 = $summ1 + ($row0['Сумма'] * (1 - $row0['Скидка_проц']));
		}
		return $summ1 - $rOld['Скидка'];
	}
}
class addSelectStatus{
	function __construct ($Cache) {
		$this->Core = $Cache['core'];
	}
	public function runSelect() {
		$out[0] = " LEFT JOIN [црм_Счета_последние_Статусы] ON [црм_Счета_последние_Статусы].[Счет] = [Код] ";
		$out[1] = " , [црм_Счета_последние_Статусы].[Статус] AS [Status] ";
		if(count($_SESSION['usr']['groups_exc']['statuses'])){
			$out[2] = " AND [црм_Счета_последние_Статусы].[Статус] NOT IN (".implode(',',$_SESSION['usr']['groups_exc']['statuses']).") OR (dbo.црм_Счета_последние_Статусы.Статус IS NULL) ";
		}else{
			$out[2] = '';
		}
		$sqlQ = "SELECT [Владелец] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."'";
		$resQ = $this->Core->query = $sqlQ;
		$this->Core->con_database('srv');
		$resQ = $this->Core->PDO();
		$rowQ = $resQ->fetch();
		//$_SESSION['usr']['user_id']
		if($rowQ['Владелец'] > 0){
			if(count($_SESSION['usr']['groups_dlg']['line'])){
				$out[2] .= " AND [црм_Счета_последние_Статусы].[Группа] IN (".implode(',',$_SESSION['usr']['groups_dlg']['line']).") ";
			}
			$out[2] .= " AND [црм_Счета_последние_Статусы].[Владелец] = '".$_SESSION['usr']['user_id']."' ";
		}
		$out[1] .= " , dbo.bill_summ([tig50].[dbo].[V_Счета].[Код]) AS [Итого_Сумма] ";
		//$out[1] .= " , (SELECT [Сумма] FROM [tig50].[dbo].[Счета_Состав] WHERE [tig50].[dbo].[Счета_Состав].[Счета_Код] = [V_Счета].[Код]) AS [sum]";
		//$out[1] .= " , (SELECT [Скидка_проц] FROM [tig50].[dbo].[Счета_Состав] WHERE [tig50].[dbo].[Счета_Состав].[Счета_Код] = [V_Счета].[Код]) AS [sum_disc]";
		//$out[1] .= " , ([sum] * (1 - [sum_disc]) ) AS [Итого_Сумма]";
//$summ1 + ($row0['Сумма'] * (1 - $row0['Скидка_проц']))

		$fieldArray = array('owner','group','Пользователь','exstatus','exstatusdat','event','eventdat');
		if(in_array($_REQUEST['sidx'], $fieldArray)){ //Если сортировка в месиве полей, к которым нужно подключать дополнительное поле сортировки
			$out1 = $this->sortLink($out); //Подключаем дополнительно е поле сортировки к таблице
			unset($out);
			$out = $out1;//Заменяем старый массив включений в таблицу на новый, с сортировкой
			//$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2w.txt", "w");fputs($des, $r); fclose($des);
		}


		return $out;
	}
	private function sortLink($out){ //ункция подключения дополнительной таблицы для сортировки
		if($_REQUEST['sidx'] == 'Итого_Сумма'){
			$_REQUEST['sidx'] = ' dbo.bill_summ([tig50].[dbo].[V_Счета].[Код]) ';
		}
		if($_REQUEST['sidx'] == 'owner') {
			$out[0] .= " LEFT JOIN [srv].[dbo].[Пользователи] ON [srv].[dbo].[Пользователи].[Код] = [tig50].[dbo].[црм_Счета_последние_Статусы].[Владелец]";
			$_REQUEST['sidx'] = "srv].[dbo].[Пользователи].[Full_name";
		}
		if($_REQUEST['sidx'] == 'group') {
			$out[0] .= " LEFT JOIN [srv].[dbo].[црм_группы] ON [srv].[dbo].[црм_группы].[Код] = [tig50].[dbo].[црм_Счета_последние_Статусы].[Группа]";
			$_REQUEST['sidx'] = "srv].[dbo].[црм_группы].[Название";
		}
		if($_REQUEST['sidx'] == 'Пользователь') {
			$out[0] .= " LEFT JOIN [srv].[dbo].[Пользователи] ON [srv].[dbo].[Пользователи].[Код] = [tig50].[dbo].[V_Счета].[Пользователи_Код]";
			$_REQUEST['sidx'] = "srv].[dbo].[Пользователи].[Full_name";
		}
		if($_REQUEST['sidx'] == 'eventdat') {
			$out[1] .= ", (SELECT TOP 1 [Дата_Завершения] FROM [tig50].[dbo].[црм_счета_События_Счета] WHERE [tig50].[dbo].[црм_счета_События_Счета].[Счет]=[tig50].[dbo].[V_Счета].[Код] ORDER BY [tig50].[dbo].[црм_счета_События_Счета].[Код] DESC) AS [edat] ";
			$_REQUEST['sidx'] = "edat";
		}
		if($_REQUEST['sidx'] == 'exstatusdat') {
			$out[1] .= ", (SELECT TOP 1 [Дата_Завершения] FROM [tig50].[dbo].[црм_счета_Статус_Счета] WHERE [tig50].[dbo].[црм_счета_Статус_Счета].[Счет]=[tig50].[dbo].[V_Счета].[Код] ORDER BY [tig50].[dbo].[црм_счета_Статус_Счета].[Код] DESC) AS [esdat] ";
			$_REQUEST['sidx'] = "esdat";
		}

		if($_REQUEST['sidx'] == 'event') {
			$out[1] .= ", (SELECT [Название] FROM [tig50].[dbo].[црм_счета_События] WHERE [tig50].[dbo].[црм_счета_События].[Код] = (SELECT TOP 1 [Статус] FROM [tig50].[dbo].[црм_счета_События_Счета] WHERE [tig50].[dbo].[црм_счета_События_Счета].[Счет]=[tig50].[dbo].[V_Счета].[Код] ORDER BY [tig50].[dbo].[црм_счета_События_Счета].[Код] DESC)) AS [evnt] ";
			$_REQUEST['sidx'] = "evnt";
		}
		if($_REQUEST['sidx'] == 'exstatus') {
			$out[1] .= ", (SELECT [Название] FROM [tig50].[dbo].[црм_счета_Статусы] WHERE [tig50].[dbo].[црм_счета_Статусы].[Код] = (SELECT TOP 1 [Статус] FROM [tig50].[dbo].[црм_счета_Статус_Счета] WHERE [tig50].[dbo].[црм_счета_Статус_Счета].[Счет]=[tig50].[dbo].[V_Счета].[Код] ORDER BY [tig50].[dbo].[црм_счета_Статус_Счета].[Код] DESC)) AS [stus] ";
			$_REQUEST['sidx'] = "stus";
		}
		return $out;
	}
}
class getStatusTable{
	function __construct ($Cache) {
		unset($this->stsus,$this->event);
		$this->Core = $Cache['core'];
	}
	function lastData($billId){
		unset($this->stsus,$this->event);
		$sql0 = "SELECT TOP 1 [b].*, [s].[Название] AS [statusname], [u].[Full_name] AS [owner], [g].[Название] AS [group]  
			FROM [tig50].[dbo].[црм_счета_Статус_Счета] AS [b] 
			LEFT JOIN [tig50].[dbo].[црм_счета_Статусы] AS [s] ON [s].[Код] = [b].[Статус] 
			LEFT JOIN [srv].[dbo].[Пользователи] AS [u] ON [u].[Код] = [b].[Владелец] 
			LEFT JOIN [srv].[dbo].[црм_группы] AS [g] ON [g].[Код] = [b].[Группа] 
			WHERE [b].[Счет] = '".$billId."' ORDER BY [Код] DESC";
		$s1 = $sql0;
		$this->Core->query = $sql0;
		$this->Core->con_database('tig50');
		$res0 = $this->Core->PDO();
		$row0 = $res0->fetch();
		$this->stsus['exstatusdat'] = $row0['Дата_Завершения'];
		$this->stsus['exstatus'] = $row0['statusname'];
		$this->stsus['owner'] = $row0['owner'];
		$this->stsus['group'] = $row0['group'];
		//$sql1 = "SELECT TOP 1 * FROM [tig50].[dbo].[црм_счета_События_Счета] WHERE [Счет] = '".$billId."' ORDER BY [Код] DESC";
		$sql1 = "SELECT TOP 1 [b].*, [s].[Название] AS [eventname] FROM [tig50].[dbo].[црм_счета_События_Счета] AS [b] LEFT JOIN [tig50].[dbo].[црм_счета_События] AS [s] ON [s].[Код] = [b].[Статус] WHERE [b].[Счет] = '".$billId."' ORDER BY [Код] DESC";
		$this->Core->query = $sql1;
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		$row1 = $res1->fetch();
		$this->event['eventdat'] = $row1['Дата_Завершения'];
		$this->event['event'] = $row1['eventname'];
		$out =  array('status'=>$this->stsus,'event'=>$this->event);
//		$r = print_r($out,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2rqr.txt", "a");fputs($des, $r.' '.$s1.'|||||'.$sql0."\n"); fclose($des);

		return $out;
	}
	//function powerSort(){

	//}
}
class addSorts{ //Подключаем сортировки
	function __construct ($Cache) {
		unset($this->stsus,$this->event);
		$this->Core = $Cache['core'];
	}
	function powerSort(){
		unset($out);
		if($_REQUEST['sidx'] == 'owner'){
			$out[1] = " LEFT JOIN  ";
		}

	}

}
class getNote{
	function __construct ($Cache) {
		//unset($this->stsus,$this->event);
		$this->Core = $Cache['core'];
	}
	function getTableNote($id,$clss){ //1- прим. статуса, 2-общее, 3-события
		$sql1 = "SELECT TOP 1 * FROM [tig50].[dbo].[црм_Примечания] WHERE [Цель] = '".(int)$id."' AND [Источник] = '".(int)$clss."' ORDER BY [Код] DESC ";
		$this->Core->query = $sql1;
		$this->Core->con_database('tig50');
		$res1 = $this->Core->PDO();
		$row1 = $res1->fetch();
		return trim($row1['Текст']);
	}
	
	
}
?>