<?php
namespace Plugins;

class DB_statistics_client_delivery
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Factory->Core->query = "SELECT * FROM Статистика_Контрагенты_ДоставкаРФ WHERE Клиенты_Код = :client_id AND Год = :year";
		$this->Factory->Core->query_params = array(
			array(':client_id',$_REQUEST['client_id'],\PDO::PARAM_INT),
			array(':year',$_REQUEST['year'],\PDO::PARAM_INT)
		);
		$rows = $this->Factory->Core->PDO(array('strict'=>true));
		$rows_cells = 20;
		$yaxis_max = 0;
		$data_helper = array();
		$order = 1;
		while($row = $rows->fetch())
		{
			if((int)$row['Сумма'] > $yaxis_max)
				$yaxis_max = (int)$row['Сумма'];
			if(!isset(${'rate_'.$row['Тариф_Код']}))
			{
				${'rate_'.$row['Тариф_Код']} = array(
					'label'=>$row['Тариф_Название'],
					'bars'=>array('order'=>$order),
					'data'=>array()
				);
			}
			${'rate_'.$row['Тариф_Код']}['data'][] = array(
				(int)$row['Месяц'],
				(int)$row['Сумма']
			);
			$data_helper['lines']['rate_'.$row['Тариф_Код']] = ${'rate_'.$row['Тариф_Код']};
		}
		$bar_width = round(1 / count(array_values($data_helper['lines'])),1,PHP_ROUND_HALF_DOWN);
		$response = array('yaxis_tick'=>ceil($yaxis_max / $rows_cells),'bars_width'=>$bar_width,'data'=>array_values($data_helper['lines']));
		echo json_encode($response,JSON_UNESCAPED_UNICODE);
	}
}
?>
