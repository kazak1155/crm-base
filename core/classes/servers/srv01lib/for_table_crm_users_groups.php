<?php
class for_table_crm_users_groups{
	function __construct($Cache) {
		$this->Core = $Cache['core'];
	}
	function runSelect(){
		$out[0] = " LEFT JOIN [srv].[dbo].[црм_Пользователь_Группа] ON [srv].[dbo].[црм_Пользователь_Группа].[Пользователь] = [srv].[dbo].[Пользователи].[Код] AND [srv].[dbo].[црм_Пользователь_Группа].[Группа] = '".(int)$_REQUEST['g']."' ";
		//$out[1] = " [srv].[dbo].[црм_Пользователь_Группа]  ";
		$out[1] .= " ,  IIF ( [srv].[dbo].[црм_Пользователь_Группа].[Группа] > 0,'В группе','Не в группе') AS [inGrp], [srv].[dbo].[црм_Пользователь_Группа].[Группа] AS [grrp] ";
		return $out;
	}
}
?>