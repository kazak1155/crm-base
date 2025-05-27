<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$Site->init_tmpl_connection();

$id = $_REQUEST['id'];
$counter = 0;
$pageSize = array($_REQUEST['width'],$_REQUEST['height']);
$fontSize = $pageSize[1] / 3;
$barcodeSize = $pageSize[1] / 35;
$order_number_max_length =  15;
$client_subclient_max_length = 15;

$Core->query = "SELECT Count(ШтрихКод)  FROM dbo.Отчет_Штрихкоды_Печать WHERE Заказы_Код = ?";
$Core->query_params = array($_REQUEST['id']);
$count = $Core->PDO()->fetchColumn();

$Core->query = "SELECT ШтрихКод,Заказы_Код,Принят,Расход_Код,Ячейки_Код,Примечание,Рейсы_Код,НомерРейса,ПолныйШтрихкод,Склады_Код,".QUERY_SPACE;
$Core->query .= "Фабрика,Клиент,Мест,Подклиент,РФСклад_Мест,ИнСклад_Мест,Клиент_EN,ШК_Печатался,Склад_Доставки,cast(Номер_Заказа as nvarchar(max)) as Номер_Заказа".QUERY_SPACE;
$Core->query .= "FROM dbo.Отчет_Штрихкоды_Печать WHERE Заказы_Код = ? ORDER BY Клиент, Фабрика, Заказы_Код, ШтрихКод";
$Core->query_params = array($_REQUEST['id']);
$rows = $Core->PDO();
while($row = $rows->fetch())
{
	if(mb_strlen($row['Клиент']) > $client_subclient_max_length)
		$row['Клиент'] = mb_substr($row['Клиент'], 0,$client_subclient_max_length).'...';
	if(mb_strlen($row['Клиент_EN']) > $client_subclient_max_length)
		$row['Клиент_EN'] = mb_substr($row['Клиент_EN'], 0,$client_subclient_max_length).'...';
	if(mb_strlen($row['Подклиент']) > $client_subclient_max_length)
		$row['Подклиент'] = mb_substr($row['Подклиент'], 0,$client_subclient_max_length).'...';
	if(mb_strlen($row['Номер_Заказа']) > $order_number_max_length)
		$row['Номер_Заказа'] = mb_substr($row['Номер_Заказа'], 0,$order_number_max_length).'...';

	if($_REQUEST['ref'] == false)
		$row['НомерРейса'] = '-';
	if(empty($row['РФСклад_Мест']))
		$row['РФСклад_Мест'] = '';
	else
		$row['РФСклад_Мест'] = '('.$row['РФСклад_Мест'].')';
	$table .= '
		<table empty-cells="show">
			<tr>
				<td rowspan="2">'.$row['ШтрихКод'].'/'.$row['ИнСклад_Мест'].' '.$row['РФСклад_Мест'].'</td>
				<td>-</td>
			</tr>
			<tr>
				<td>'.$row['Склад_Доставки'].'</td>
			<tr/>
			<tr>
				<td colspan="2" class="barcode-cell">
					<barcode  size="'.$barcodeSize.'"  code="'.$row['ПолныйШтрихкод'].'" type="C128B" class="barcode" />
				</td>
			</tr>
			<tr>
				<td colspan="2">'.$row['ПолныйШтрихкод'].'</td>
			</tr>
			<tr>
				<td colspan="2" >'.mb_strtolower($row['Номер_Заказа']).'</td>
			</tr>
			<tr>
				<td class="small-font">'.mb_strtolower($row['Клиент']).'<br/>('.mb_strtolower($row['Клиент_EN']).')<br/>'.mb_strtolower($row['Подклиент']).'</td>
				<td class="small-font">'.mb_strtolower($row['Фабрика']).'</td>
			</tr>
		</table>
	';
	if(++$counter < $count)
		$table .= '<pagebreak />';
}

$html = "
<style>
	@page
	{
		margin-top:2;
		margin-right:2;
		margin-left:2;
	}
	table
	{
		border-collapse:collapse;
		font-size:$fontSize\px;
	}
	table tr td
	{
		border:1px solid black;
		text-align: center;
	}
	.small-font
	{
		height:80px;
	}
	.barcode-cell
	{
		padding-top:5;
		padding-bottom:5;
	}
	.barcode
	{
		padding: 0;
		margin: 0;
		color: #000;
	}
</style>
$table";

$mpdf = new \Mpdf\Mpdf('',$pageSize, '', '', '', '', '', '', '', '', 'L');
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
$mpdf->AddPage();
$mpdf->Output("barcode-".$_REQUEST['id'].".pdf",'I');
exit;

?>
