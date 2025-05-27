<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$Site->init_tmpl_connection();

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
	ООО \"VITA\"<br/>
	<span style='font-size:10px'>
		115477 Moscow  Kantemirovskaya str. 53, building 1                      INN/KPP 7724807105/ 772401001
	</span>
</h1>
<table id='veh_table'>
	<tr>
		<td class='bold_cell'>$date</td>
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
		<td class='bold_cell'>Дата и время погрузки</td>
		<td colspan='2'>$vehicle_date</td>
	</tr>
	<tr>
		<td class='bold_cell'>Маршрут</td>
		<td colspan='2'>$vehicle_route</td>
	</tr>
	<tr>
		<td class='bold_cell'>Адрес погрузки</td>
		<td colspan='2'></td>
	</tr>
	<tr>
		<td align='center' colspan='3' class='bold_cell underline_cell'>Адреса погрузок смотреть в приложении №$order_number</td>
	</tr>
	<tr>
		<td class='bold_cell'>Объем</td>
		<td colspan='2'>115м³</td>
	</tr>
	<tr>
		<td class='bold_cell'>Таможня</td>
		<td colspan='2'>Россия, г.Москва/Московская область</td>
	</tr>
	<tr>
		<td class='bold_cell'>Адрес разгрузки</td>
		<td colspan='2'>Россия, г.Москва/Московская область</td>
	</tr>
	<tr>
		<td class='bold_cell'>Оплата за качественно выполненную перевозку</td>
		<td colspan='2'>Оплата будет произведена в течение 14 рабочих дней со дня разгрузки товара в сумме $EUR</td>
	</tr>
	<tr>
		<td class='bold_cell'>Дополнительные условия</td>
		<td colspan='2'>Прилагается</td>
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
		<td colspan='3'><h3 style='width:100%;text-align:center'>Обязательства сторон</h3></td>
	</tr>
	<tr>
		<td colspan='3'>
			Вы можете  принять этот заказ , если диспонируете  TIR  CARNET книжкой  и  всеми транспортными разрешениями и визами.
			<br/>
			<strong>ОБЯЗАНЫ ГАРАНТИРОВАТЬ НЕЙТРАЛЬНОСТЬ  ПО ОТНОШЕНИЮ К КЛИЕНТУ.</strong>
			<br/>
			На место погрузки автомобиль должен приехать технически исправным, водитель должен иметь все нужные документы.
			В случае опоздания на место разгрузки из суммы перевозки вычитается 100 ЕUR в день.
			Простои на месте погрузки 100Eur за каждые сутки, разгрузки таможни от 72 ч. 100Eur за каждые сутки.
			В  случае  отказа  перевезти  груз  после подписания  и подтверждения заявки,  перевозчик   обязуется  оплатить  10%  компенсаций  от  суммы за  перевозку.
			Если  во время погрузки, из-за  каких – то причин  нельзя проверить количество груза, увидеть не повреждена ли  упаковка  груза, водитель  должен об этом записать  в  ЦМР (CMR).
			За  правильное  оформление  и  наличие документов  отвечает  водитель. Если  не хватает  каких-либо документов, об этом  сразу  надо информировать.
			О  простоях  в  местах  погрузки – выгрузки нужно информировать письменно. Всё это должно быть зафиксировано в ЦМР (CMR) и подтверждено печатью.
			Заказчик  и  Перевозчик отвечают  за  точное  выполнение  обязательств, указанных в договоре и в  соответствии с Конвенцией Международных Перевозок ЦМР (CMR) от 19.05.1956 года.
		</td>
	</tr>
	<tr>
		<td>Фамилия,должность</td>
		<td></td>
		<td>Фамилия,должность</td>
	</tr>
	<tr>
		<td class='bottom_border_cell'></td>
		<td></td>
		<td class='bottom_border_cell'></td>
	</tr>
</table>
";

$mpdf=new \Mpdf\Mpdf();
$mpdf->AddPage();
$mpdf->WriteHTML($html2);
$mpdf->AddPage('L','','','','',0,0,0,0,0,0);
$mpdf->WriteHTML($html);
$mpdf->Output($_REQUEST['fn'].".pdf",'D');
exit;
?>
