<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$Site->init_tmpl_connection();

$main_data = new stdClass;
$qry_Orders = array();
$sumWeight = int;
$sumAmount = int;
$sumWolume = int;

$Core->query = "SELECT Номер_рейса,НомерМашины,Клиент,Фабрика,Номер_Заказа,Объем,Вес,Мест,Заказы_Код FROM Отчет_Склад_Разгрузка WHERE Рейсы_Код = ?";
$Core->query_params = array(json_decode($_REQUEST['qry']));
$data = $Core->PDO()->fetchAll();

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->setDefaultFontName('Calibri');


$main_data->haul = $data[0]['Номер_рейса'];
$main_data->car = $data[0]['НомерМашины'];

$section = $phpWord->createSection(array('orientation' => 'landscape'));
$section->getSettings()->setMarginLeft(300);
$section->getSettings()->setMarginRight(300);
$section->getSettings()->setMarginTop(300);
$section->getSettings()->setMarginBottom(300);

$textrunHead = $section->createTextRun();

$textrunHead->addText('Рейс № ',array('size'=>14));
$textrunHead->addText($main_data->haul,array('size'=>14, 'bold'=>true));
$textrunHead->addText("\t\t\t\t");
$textrunHead->addText($main_data->car,array('size'=>14, 'bold'=>true));
$textrunHead->addText("\t\t\t\t\t\t\t\t\t\t");
$textrunHead->addText('Разгрузка на склад',array('size'=>14));

$table = $section->addTable();

$style = array('bold'=>true);
$border = array('borderSize'=>6,'borderColor'=>'#000');
$border_bg = array('borderSize'=>6,'borderColor'=>'#000','bgColor'=>'#B2B2B2');

$table->addRow(null, array('tblHeader' => false, 'cantSplit' => false));
$table->addCell(2000,$border_bg)->addText('Клиент',$style);
$table->addCell(3000,$border_bg)->addText('Фабрика',$style);
$table->addCell(6500,$border_bg)->addText('Номер заказа',$style);
$table->addCell(1500,$border_bg)->addText('Объем',$style);
$table->addCell(1500,$border_bg)->addText('Вес',$style);
$table->addCell(2500,$border_bg)->addText('Кол-во мест',$style);


for($i = 0;$i < count($data);$i++)
{
	$sumWolume += round($data[$i]['Объем'],2);
	$sumWeight += round($data[$i]['Вес'],2);
	$sumAmount += round($data[$i]['Мест'],2);

	$table->addRow();
	$table->addCell(null,$border)->addText(htmlspecialchars($data[$i]['Клиент']));
	$table->addCell(null,$border)->addText(htmlspecialchars($data[$i]['Фабрика']));
	$table->addCell(null,$border)->addText(htmlspecialchars($data[$i]['Номер_Заказа']));
	$table->addCell(null,$border)->addText(htmlspecialchars(round($data[$i]['Объем'],2)));
	$table->addCell(null,$border)->addText(htmlspecialchars(round($data[$i]['Вес'],2)));
	$table->addCell(null,$border)->addText(htmlspecialchars(round($data[$i]['Мест'],2)));

	$Core->query = "SELECT * FROM Отчет_Склад_Разгрузка_Подробно WHERE Заказы_Код = ?";
	$Core->query_params = array($data[$i]['Заказы_Код']);
	$data_order = $Core->PDO()->fetchAll();

	$table->addRow();
	$table->addCell()->addText("\t",array('borderSize' => 0));
	$table->addCell(null,$border_bg)->addText(htmlspecialchars('Вид груза'),$style);
	$table->addCell(null,array('gridSpan'=>3,'borderSize'=>6,'borderColor'=>'#000','bgColor'=>'#B2B2B2'))->addText(htmlspecialchars('Примечание'),$style);
	$table->addCell(null,$border_bg)->addText(htmlspecialchars('Кол-во'),$style);

	for($y = 0;$y < count($data_order);$y++)
	{
		$table->addRow();
		$table->addCell()->addText("\t");
		$table->addCell(null,$border)->addText(htmlspecialchars($data_order[$y]['Вид_Груза']));
		$table->addCell(null,array('gridSpan'=>3,'borderSize'=>6,'borderColor'=>'#000'))->addText(htmlspecialchars($data_order[$y]['Примечание']));
		$table->addCell(null,$border)->addText(htmlspecialchars($data_order[$y]['Кол_предм']));
	}

	$table->addRow();
	$table->addCell()->addText('',array('gridSpan' => 6, 'valign' => 'center'));

	if($data[$i]['Клиент'] !== $data[$i + 1]['Клиент'])
	{
		$table->addRow();
		$table->addCell()->addText("\t");
		$table->addCell()->addText(htmlspecialchars($data[$i]['Клиент']),array('bold'=>true,'size'=>12));
		$table->addCell()->addText(htmlspecialchars('ИТОГО'),array('bold'=>true,'size'=>12));
		$table->addCell()->addText(htmlspecialchars($sumWolume),array('bold'=>true,'size'=>12));
		$table->addCell()->addText(htmlspecialchars($sumAmount),array('bold'=>true,'size'=>12));
		$table->addCell()->addText("\t");

		$table->addRow();
		$table->addCell()->addText("\t");
		$table->addCell()->addText("\t");
		$table->addCell()->addText("\t");
		$table->addCell()->addText(htmlspecialchars($sumWolume));
		$table->addCell()->addText(htmlspecialchars($sumWeight));
		$table->addCell()->addText(htmlspecialchars($sumAmount));

		$sumWolume = 0;
		$sumWeight = 0;
		$sumAmount = 0;
	}
}

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment;filename=\"".$_REQUEST['fn'].".docx\"");
header("Cache-Control: max-age=0");
$objWriter->save('php://output');
exit;
?>
