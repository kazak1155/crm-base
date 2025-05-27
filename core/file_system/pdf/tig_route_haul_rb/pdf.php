<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$Site->init_tmpl_connection();

/*
$Core->query = "SELECT * FROM Отчет_ЗаявкаРейс_Транспорт WHERE Рейсы_Код = ?";
$Core->query_params = array(json_decode($_REQUEST['qry']));
$res = $Core->PDO();
while($row = $res->fetch()){
	$haul_name = $row['Рейс'];
	$rows .= '
		<tr>
			<td>'.$row['Клиент'].'</td>
			<td>'.$row['Фабрика'].'</td>
			<td>'.$row['Адрес'].'</td>
			<td>'.$row['Контакт'].'</td>
			<td>'.$row['Номер_Заказа'].'</td>
			<td>'.$Core->full_date_from_server($row['Дата_Готовность']).'</td>
			<td>'.($row['Объем']+ 0).'</td>
			<td>'.($row['Вес']+ 0).'</td>
		</tr>
	';
	$header = '
	<div class="header">
		<h2 style="width:100%;text-align:center">Приложение №'.$haul_name.'</h2>
		<table>
			<tr>
				<td class="bold_cell">Рейс</td>
				<td>'.$row['Рейс'].'</td>
				<td class="bold_cell">Примечание</td>
				<td>'.$row['Примечание'].'</td>
			</tr>
			<tr>
				<td class="bold_cell">Дата начала рейса</td>
				<td>'.$Core->full_date_from_server($row['ДатаНачалаРейса']).'</td>
			</tr>
			<tr>
				<td class="bold_cell">Объем машины</td>
				<td>'.($row['Рейсы_Объем'] + 0).'</td>
			</tr>
		</table>
	</div>
	';
}

$style = '
<style>
#veh_table
{
	border-collapse:separate;
	border-spacing:8px;
}
#veh_table td
{
	padding:5px;
}
.bold_cell
{
	font-weight:bold;
}
.bottom_border_cell
{
	border-bottom:1px solid black;
}
.underline_cell
{
	text-decoration:underline
}
.dataTable{
	margin-top:10px;
}
.dataTable th,.dataTable td {
	border:1px solid black;
}
.header td {
	width:250px;
}
</style>
';
$table = '
	<table class="dataTable">
		<tr>
			<th>Клиент</th>
			<th>Фабрика</th>
			<th>Адрес</th>
			<th>Контакт</th>
			<th>Номер заказа</th>
			<th>Дата готовности</th>
			<th>Объем</th>
			<th>Вес</th>
		</tr>
		'.$rows.'
	</table>
';
$html = $style.$header.$table;
*/

$Core->query = "SELECT * FROM Отчет_ЗаявкаРейс_Транспорт_Титул WHERE Код = ?";
$Core->query_params = array(json_decode($_REQUEST['qry']));
$res = $Core->PDO();
while($row = $res->fetch(PDO::FETCH_ASSOC))
{
	$order_number = $row['Номер'];
	$date = $Core->full_date_from_server($row['Дата_Заявки']);
	$transporter = $row['Перевозчик'];
	$vehicle = $row['НомерМашины'];
	$vehicle_date = $Core->full_date_from_server($row['Дата_Начала']);
	$vehicle_route = $row['Маршрут'];
	$EUR = $row['Фрахт_Аделантекс'];
}
$html2 = "
<style>
#veh_table
{
	border-collapse:separate;
	border-spacing:5px;
}
#veh_table td
{
	padding:5px;
}
.bold_cell
{
	font-weight:bold;
}
.bottom_border_cell
{
	border-bottom:1px solid black;
}
.underline_cell
{
	text-decoration:underline
}
</style>
<h1 style='width:100%;text-align:center'>
		ООО \"Прогресс\"<br/>
	<span style='font-size:10px'>
	ИНН/КПП 7719464043/772801001<br>
	117246, Москва г, Научный проезд, дом 17, офис 14-12.
	</span>
</h1>
<table id='veh_table'>
	<tr>
		<td class='bold_cell'>".date('Y/m/d')."</td>
		<td class='bold_cell' width='280px'>Заявка на грузоперевозку $order_number</td>
		<td class='bold_cell'>$transporter</td>
	</tr>
	<tr>
		<td class='bold_cell'>Просим перевезти груз</td>
		<td colspan='2'>Мебель</td>
	</tr>
	<tr>
		<td class='bold_cell'>Тип транспорта</td>
		<td colspan='2'>Автотранспорт, 120м³</td>
	</tr>
	<tr>
		<td class='bold_cell'>Дата и время погрузки</td>".
		//<td colspan='2'>$vehicle_date</td>
		"<td colspan='2'></td>
	</tr>
	".
	/*<tr>
		<td class='bold_cell'>Маршрут</td>
		<td colspan='2'></td>
	</tr>
	*/"
	<tr>
		<td class='bold_cell'>Адрес погрузки</td>
		<td colspan='2'>ПТО Полоцк</td>
	</tr>

	<tr>
		<td class='bold_cell'>Объем</td>
		<td colspan='2'>115м³</td>
	</tr>
	<tr>
		<td class='bold_cell'>Адрес разгрузки</td>
		<td colspan='2'>Россия, г.Москва/Московская область, ул. Луговая, д.1</td>
	</tr>
	<tr>
		<td class='bold_cell'>Автомобиль</td>
		<td colspan='2'>$vehicle</td>
	</tr>
	<tr>
		<td class='bold_cell'>Фамилия водителя</td>
		<td colspan='2' class='bottom_border_cell'></td>
	</tr>
	
	<tr>
		<td>Печать, подпись</td>
		<td><img src='prgs.png' width='150'>
		<img src='prgs_dir.png' width='100'></td><td></td>
	</tr>

</table>
";

$mpdf=new \Mpdf\Mpdf();
$mpdf->AddPage();
$mpdf->WriteHTML($html2);
//$mpdf->AddPage('L','','','','',0,0,0,0,0,0);
//$mpdf->WriteHTML($html);
$mpdf->Output($_REQUEST['fn'].".pdf",'D');
exit;
?>
