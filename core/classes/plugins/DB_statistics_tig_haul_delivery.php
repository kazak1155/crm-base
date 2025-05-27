<?php
namespace Plugins;

class DB_statistics_tig_haul_delivery
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$query =
		$tname = 'Статистика_Рейсы_Тарифы_Текущие';
		if(isset($_REQUEST['haul_id']))
		{
			$this->Factory->Core->query = "SELECT * FROM Статистика_Рейсы_Тарифы WHERE Рейсы_Код = :haul_id";
			$this->Factory->Core->query_params = array(
				array(':haul_id',$_REQUEST['haul_id'],\PDO::PARAM_INT)
			);
		}
		else
			$this->Factory->Core->query = "SELECT * FROM Статистика_Рейсы_Тарифы_Текущие ORDER BY 1";
		$rows = $this->Factory->Core->PDO(array('strict'=>true));

		$data_helper = array();
		$order = 1;
		while($row = $rows->fetch())
		{
			if(!isset(${'rate_'.$row['Тариф_Код']}))
			{
				${'rate_'.$row['Тариф_Код']} = array(
					'label'=>$row['Тариф_Название'],
					'bars'=>array('order'=>$order),
					'data'=>array()
				);
				$order++;
			}

			if(!isset($data_helper['xaxis_ticks'][(int)$row['Рейсы_Код']]))
				$data_helper['xaxis_ticks'][(int)$row['Рейсы_Код']] = array(
					(int)$row['Рейсы_Код'],
					$row['Рейс']
				);
			${'rate_'.$row['Тариф_Код']}['data'][] = array(
				(int)($row['Рейсы_Код']),
				(int)$row['Сумма']
			);
			$data_helper['lines']['rate_'.$row['Тариф_Код']] = ${'rate_'.$row['Тариф_Код']};
		}
		$response = array('yaxis_tick'=>5000,'xaxis_ticks'=>array_values($data_helper['xaxis_ticks']),'data'=>array_values($data_helper['lines']));
		echo json_encode($response,JSON_UNESCAPED_UNICODE);
	}
}
?>
