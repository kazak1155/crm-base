<?php
$sql = "SELECT * FROM [tig50].[dbo].[Контрагенты] WHERE [Код] = '".(int)$_REQUEST['c']."'";
$res = $Cache['core']->query = $sql;
$Cache['core']->con_database('tig50');
$res = $Cache['core']->PDO();
$contragent = $res->fetch();
if($_REQUEST['p'] == 1){
$out .= '<div style="height: 400px;width: 100%;position:relative;">';
$out .= '<form method="post" action="" id="seformm">';
	$out .= '<table cellpadding="5" cellspacing="3">';
	$out .= '<tr>';
	$out .= '<td>Название</td><td>';
	$out .= '<input type="text" name="Название_RU" value="'.$contragent['Название_RU'].'" class="inp_shot"/> &#160;  &#160;  &#160; &#160;  &#160; ';
	$out .= '</td>';
	$out .= '<td>Name</td><td>';
	$out .= '<input type="text" name="Название_EN" value="'.$contragent['Название_EN'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	$out .= '<tr>';
	$out .= '<td>Код страны</td><td>';
	$sql = "SELECT * FROM [tig50].[dbo].[Страны] ORDER BY [Код]";
	$res = $Cache['core']->query = $sql;
	$Cache['core']->con_database('tig50');
	$res = $Cache['core']->PDO();
	$out .= '<select name="Страны_Код" class="inp_shot">';
	while($row = $res->fetch()){
		$sel = ''; if($row['Код'] == $contragent['Страны_Код']){$sel = ' selected ';}
		$out .= '<option value="'.$row['Код'].'" '.$sel.'>'.$row['Код'].'</option>';
	}
	$out .= '</select>';
	$out .= '</td>';
	$out .= '<td>Код сайта</td><td>';
	$out .= '<input type="text" name="Сайт_Код" value="'.$contragent['Сайт_Код'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	$out .= '<tr>';
	$out .= '<td>Email Счета</td><td>';
	$out .= '<input type="text" name="Email_Счета" value="'.$contragent['Email_Счета'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '<td>Продавца_Код</td><td>';
	$out .= '<input type="text" name="Продавца_Код" value="'.$contragent['Продавца_Код'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<td>Покупателя Код</td><td>';
	$out .= '<input type="text" name="Покупателя_Код" value="'.$contragent['Покупателя_Код'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '<td>Счета</td><td>';
	$out .= '<input type="text" name="Счета" value="'.$contragent['Счета'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<td>Дата создания</td><td>';
	$out .= '<input type="text" name="Дата_создания" value="'.$contragent['Дата_создания'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '<td>Счета</td><td>';
	$out .= '<input type="text" name="Контрагенты_Бух_Код" value="'.$contragent['Контрагенты_Бух_Код'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<td>Пользователи Код</td><td>';
	$out .= '<input type="text" name="Пользователи_Код" value="'.$contragent['Пользователи_Код'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '<td>Тариф Европа</td><td>';
	$out .= '<input type="text" name="Тариф_Европа" value="'.$contragent['Тариф_Европа'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<td>Тариф Россия</td><td>';
	$out .= '<input type="text" name="Тариф_Россия" value="'.$contragent['Тариф_Россия'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '<td>Тариф Хранение</td><td>';
	$out .= '<input type="text" name="Тариф_Хранение" value="'.$contragent['Тариф_Хранение'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<td>Персональный Менеджер Код</td><td>';
	$out .= '<input type="text" name="Персональный_Менеджер_Код" value="'.$contragent['Персональный_Менеджер_Код'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '<td>Торговая марка</td><td>';
	$out .= '<input type="text" name="Торговая_марка" value="'.$contragent['Торговая_марка'].'" class="inp_shot"/>';
	$out .= '</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<td>Архив</td><td>';
	$out .= '<input type="text" name="Архив" value="'.$contragent['Архив'].'" class="inp_shot"/> ';
	$out .= '</td>';
	$out .= '<td>';
	$out .= '</td>';
	$out .= '</tr>';
	/*
      ,[Архив]
	*/
	$out .= '</table>';
	$out .= '<input type="hidden" name="sekod" value="'.$contragent['Код'].'"/>';
	$out .= '<input type="hidden" name="seform1" value="1"/>';
$out .= '</form>';
	$out .= '<div style="position:absolute;z-index: 311;bottom:2px;right:9px;">
	<table id="key_table" style="">
		<tr>
			<td id="key_s" onClick="javascript:return testSeForm();" style="padding:0;margin:0;position:relative;"><div style="position:absolute;top:23px;left:40px;font-size:9px;"></div></td>
		</tr>
	</table>
</div>';
	$out .= '</div>
<script>
	function testSeForm(){
		document.getElementById(\'seformm\').submit();
	}
</script>
';

}
?>