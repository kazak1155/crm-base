<?php
namespace Plugins;

class DB_service_delivery
{
	const SERVICE_DELIVERY_ID = 41;

	const SERVICE_EU_WH_ID = 12;

	private $Factory;

	private $rate_id;

	private $rate_service;

	private $rate_data = array();

	private $category_rate = false;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		if(isset($_REQUEST['rate_id']))
			$this->rate_id = filter_var($_REQUEST['rate_id'], FILTER_SANITIZE_NUMBER_INT);
		else
			exit('Rate_id not found');
		if($_REQUEST['params_tpl'] != 'false')
			$this->rate_params_tpl = $_REQUEST['params_tpl'];

		$this->Factory->Site->req_data['Тариф_Код'] = $this->rate_id;

		$this->get_rate_data();
		$this->process_responce();
		exit();
	}
	private function get_rate_data()
	{
		$this->Factory->Core->query = "SELECT * FROM Тарифы2 WHERE Код = ?";
		$this->Factory->Core->query_params = array($this->rate_id);
		$data = $this->Factory->Core->PDO()->fetchAll();
		$data = array_shift($data);
		$this->rate_data = $data;
	}
	private function process_responce()
	{
		switch ($this->rate_data['Услуги_Код'])
		{
			case self::SERVICE_DELIVERY_ID:
				$this->get_categories();
				$this->get_volume();
				$this->get_weight();
				$this->get_weight_volume();
				$this->get_rate_params();
				break;
			case self::SERVICE_EU_WH_ID:
				$this->get_rate_params();
				break;
			default:	break;
		}
		exit();
	}
	private function get_categories()
	{
		$this->Factory->Site->req_data['category_prices'] = array();
		$this->Factory->Core->query = "SELECT Код,Категория_Груза,Цена_Объем,Валюты_Код,КВ FROM Тарифы2_Категории WHERE Тариф_Код = ?";
		$this->Factory->Core->query_params = array($this->rate_id);
		$rows = $this->Factory->Core->PDO();
		while($row = $rows->fetch())
		{
			array_push($this->Factory->Site->req_data['category_prices'],$row);
		}
		if(!empty($this->Factory->Site->req_data['category_prices']))
			echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."category/tpl.php");
	}
	private function get_weight()
	{
		$this->Factory->Site->req_data['weight_prices'] = array();
		$this->Factory->Core->query = "SELECT Код,Категория_Груза,Цена FROM Тарифы2_Вес WHERE Тариф_Код = ?";
		$this->Factory->Core->query_params = array($this->rate_id);
		$rows = $this->Factory->Core->PDO();
		while($row = $rows->fetch())
		{
			array_push($this->Factory->Site->req_data['weight_prices'],$row);
		}
		if(!empty($this->Factory->Site->req_data['weight_prices']))
			echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."weight/tpl.php");

		//echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."weight/tpl.php");
	}
	private function get_volume()
	{
		$this->Factory->Site->req_data['volume_prices'] = array();
		$this->Factory->Core->query = "SELECT Код,Цена,Плотность_мин,Плотность_макс FROM Тарифы2_Объем WHERE Тариф_Код = ?";
		$this->Factory->Core->query_params = array($this->rate_id);
		$rows = $this->Factory->Core->PDO();
		while($row = $rows->fetch())
		{
			array_push($this->Factory->Site->req_data['volume_prices'],$row);
		}
		if(!empty($this->Factory->Site->req_data['volume_prices']))
			//echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."weight/tpl.php");
			echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."volume/tpl.php");
		
			//echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."volume/tpl.php");
	}

	private function get_weight_volume()
	{
		$this->Factory->Site->req_data['weight_volume_prices'] = array();
		$this->Factory->Core->query = "SELECT Код, Категория_Груза, Вес, Объем FROM Тарифы2_Вес_Объем WHERE Тариф_Код = ?";
		$this->Factory->Core->query_params = array($this->rate_id);
		$rows = $this->Factory->Core->PDO();
		while($row = $rows->fetch())
		{
			array_push($this->Factory->Site->req_data['weight_volume_prices'],$row);
		}
		if(!empty($this->Factory->Site->req_data['weight_volume_prices']))
			//echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."weight/tpl.php");
			echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."weight_volume/tpl.php");
		
			//echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS."volume/tpl.php");
	}
	
	private function get_rate_params()
	{
		$this->Factory->Site->req_data = array();
		$this->Factory->Site->req_rowid = $this->rate_id;
		$this->Factory->Core->query = "SELECT Код,[Параметр_Название],[Параметр_Значение],[Примечание] FROM [dbo].[Тарифы2_Параметры] WHERE Тариф_Код = ?";
		$this->Factory->Core->query_params = array($this->rate_id);
		$rows = $this->Factory->Core->PDO();
		while($row = $rows->fetch())
		{
			$this->Factory->Site->req_data[$row['Параметр_Название']] = array('id'=>$row['Код'],'value'=>$row['Параметр_Значение']);
		}
		echo $this->Factory->Site->get_reference_html(TEMPLATE_PLUGINS.$this->rate_params_tpl."/tpl.php");
	}
}
?>
