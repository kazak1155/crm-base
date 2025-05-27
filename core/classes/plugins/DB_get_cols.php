<?php
namespace Plugins;

class DB_get_cols
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Factory->Core->query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
		$this->Factory->Core->query_params = array(array(1,$this->Factory->op_tbl,\PDO::PARAM_STR));
		echo json_encode($this->Factory->Core->PDO(array('strict'=>true,'fetch_mode'=>'col'))->fetchAll(),JSON_UNESCAPED_UNICODE);
	}
}
?>
