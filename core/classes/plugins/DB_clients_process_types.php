<?php
namespace Plugins;

class DB_clients_process_types
{
	private $Factory;
	private $Core;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Core = $this->Factory->Core;
		$data = json_decode($_REQUEST['data']);
		$this->Core->query = "SELECT Код,Типы_Код FROM Контрагенты_Связь_Типы WHERE Контрагенты_Код = ?";
		$this->Core->query_params = array($data->client_id);
		$client_types = $this->Core->PDO();
		while($type = $client_types->fetch())
		{
			$i = array_search($type['Типы_Код'],$data->checked_types);
			if($i === false)
			{
				$this->Core->query = "DELETE FROM Контрагенты_Связь_Типы WHERE Код = ?";
				$this->Core->query_params = array($type['Код']);
				$this->Core->PDO();
			}
			else
				unset($data->checked_types[$i]);
		}
		foreach(array_values($data->checked_types) as $value)
		{
			$this->Core->query = "INSERT INTO Контрагенты_Связь_Типы (Контрагенты_Код,Типы_Код) VALUES (?,?)";
			$this->Core->query_params = array($data->client_id,$value);
			$this->Core->PDO();
		}
	}
}
?>
