<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$Site->init_tmpl_connection();

if(isset($_REQUEST['qry']))
{
	$filename = $_REQUEST['fileName'];
	if(empty($filename))
		$filename = $_REQUEST['fn'];
	$queryData = json_decode($_REQUEST['qry']);
	$columns = array();

	foreach ($queryData->fields as $field)
	{
		$i++;
		if($i !== count($queryData->fields))
			$comma = ',';
		else
			$comma = '';
		if(strpos($field->name, 'SELECT') || strpos($field->name, 'CONVERT') || strpos($field->name, 'as'))
			$queryFields .= $field->name.$comma;
		else
			$queryFields .= '['.$field->name.']'.$comma;

		if(strpos($field->name,'as') !== false)
			$field->name = substr($field->name, strpos($field->name, "as") + 3);

		array_push($columns,$field->name);
	}
	switch ($queryData->type)
	{
		case 'SELECT':
			if(isset($_REQUEST['qw']))
				$_REQUEST['filters'] = $_REQUEST['qw'];
			if(isset($_REQUEST['filters']) && $_REQUEST['filters'] !== 'null' && !is_null($_REQUEST['filters']))
			{
				$queryFilters = json_decode($_REQUEST['filters']);
				$qWhere = $Core->process_filters($queryFilters,$conn);

				if(isset($queryData->mainIdname))
				{
					$qWhere .= " AND $queryData->mainIdname = $queryData->mainId";
				}

				$query = $queryData->type.' '.$queryFields.' FROM '.$queryData->tname.' WHERE '.$qWhere;
			} else
			{
				$query = $queryData->type.' '.$queryFields.' FROM '.$queryData->tname;
				if(isset($queryData->mainIdname))
				{
					$query.= " WHERE $queryData->mainIdname = $queryData->mainId";
				}
			}
			if(isset($_REQUEST['order']) === true)
			{
				$queryOrder = json_decode($_REQUEST['order']);
				$query .= ' ORDER BY '.$queryOrder->orderBy.' '.$queryOrder->orderSort;
			}
			break;
		case 'EXEC':
			$query = $queryData->type.' '.$queryData->tname.' '.$queryFields;
			break;
		default: break;
	}
	$Core->query = $query;
	$stm = $Core->PDO();
	if($stm !== false)
	{
		$columns = array();
		$data = $stm->fetchAll();
		if(!empty($data))
		{
			foreach ($data[0] as $key => &$value)
			{
				if(mb_strpos($value,'=') !== false)
				{
					$pos = mb_strpos($value,'=');
					$value = mb_substr($value,0,$pos)."'".mb_substr($value,$pos);
				}
				array_push($columns, $key);
			}
		}
	}
	else
		$data = array();
	$excelObject = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

	$excelObject->getActiveSheet()->fromArray($columns, null, 'A1');
	$excelObject->getActiveSheet()->fromArray($data, null, 'A2');
	$sheet = $excelObject->getActiveSheet();

	$styleArray = array('borders' => array('allborders' => array('style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)));
	for ($i = 0; $i < count($columns); $i++)
	{
		if($i > 25)
			$cell = chr(65+(floor($i / 26)-1)).chr(65+($i % 26)).(1);
		else
			$cell = chr(65+$i).(1);
		if(strpos($queryData->tname,'FT_Актуальные_Заявки') === false)
			$sheet->getColumnDimension(rtrim($cell,'1'))->setAutoSize(true);

		$sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
		$sheet->getStyle($cell)->getFill()->getStartColor()->setRGB('FFFF00');
		$sheet->getStyle($cell)->getFont()->setBold(true);
		$sheet->getStyle($cell)->applyFromArray($styleArray);

	}
	if(strpos($queryData->tname,'FT_Актуальные_Заявки') >= 0){
		$sheet->getStyle('C2:C1000')->getAlignment()->setWrapText(true);
		$sheet->getStyle('D2:D1000')->getAlignment()->setWrapText(true);
		$sheet->getStyle('J2:I1000')->getAlignment()->setWrapText(true);
		$sheet->getStyle('K2:J1000')->getAlignment()->setWrapText(true);
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(26);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(16);
		$sheet->getColumnDimension('F')->setWidth(16);
		$sheet->getColumnDimension('G')->setWidth(10);
		$sheet->getColumnDimension('H')->setWidth(10);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(57);
		$sheet->getColumnDimension('K')->setWidth(65);
		$sheet->getColumnDimension('L')->setWidth(65);
	}
	if (headers_sent())
		die("**Error: headers sent");

	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment;filename=\"$filename.xlsx\"");
	header("Cache-Control: max-age=0");

	$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excelObject, 'Xlsx');
	$objWriter->save('php://output');

}
