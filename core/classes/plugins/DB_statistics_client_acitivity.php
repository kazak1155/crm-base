<?php
namespace Plugins;

class DB_statistics_client_acitivity
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Factory->Core->query = "SELECT Месяц,Сумм_Зак,Сумм_Объем,Сумм_Вес / 1000 as Сумм_Вес,Сумм_Ср as Сумм_Ср FROM Статистика_Контрагенты_Активность WHERE Клиенты_Код = :client_id AND Год = :year";
		$this->Factory->Core->query_params = array(
			array(':client_id',$_REQUEST['client_id'],\PDO::PARAM_INT),
			array(':year',$_REQUEST['year'],\PDO::PARAM_INT)
		);
		$rows = $this->Factory->Core->PDO(array('strict'=>true));

		$rows_cells = 20;
		$yaxis_max = 0;

		$line_1 = array('color'=>'#c00000','label'=>'Кол-во заказов','data'=>array());
		$line_2 = array('color'=>'#83ce16','label'=>'Суммарный объем','data'=>array());
		$line_3 = array('color'=>'#3399FF','label'=>'Суммарный вес (в тоннах)','data'=>array());
		$line_4 = array('color'=>'#FFFF00','label'=>'Средний объем','data'=>array());
		while($row = $rows->fetch())
		{
			if($yaxis_max < max($row))
				$yaxis_max = max($row);
			$line_1['data'][(int)$row['Месяц']] = array((int)$row['Месяц'],(int)$row['Сумм_Зак']);
			$line_2['data'][(int)$row['Месяц']] = array((int)$row['Месяц'],(float)$row['Сумм_Объем']);
			$line_3['data'][(int)$row['Месяц']] = array((int)$row['Месяц'],(float)$row['Сумм_Вес']);
			$line_4['data'][(int)$row['Месяц']] = array((int)$row['Месяц'],(float)$row['Сумм_Ср']);
		}
		$i = 1;
		while( $i <= 4)
		{
			if(count(${'line_'.$i}['data']) < 11)
			{
				foreach (range(1,12) as $month)
				{
					if(!array_key_exists($month,${'line_'.$i}['data']))
					{
						${'line_'.$i}['data'][$month] = array($month,0);
					}
				}
			}
			asort(${'line_'.$i}['data']);
			${'line_'.$i}['data'] = array_values(${'line_'.$i}['data']);
			$i++;
		}
		$response = array('yaxis_tick'=>ceil($yaxis_max / $rows_cells),'data'=>array($line_1,$line_2,$line_3,$line_4));
		echo json_encode($response,JSON_UNESCAPED_UNICODE);
	}
}
?>
