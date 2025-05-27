<?php
namespace Plugins;

class DB_view
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Factory->Core->query = urldecode($_REQUEST['query']);
		if($_REQUEST['responseType'] === 'single')
		{
			$responce = $this->Factory->Core->PDO()->fetch();
		}
		elseif($_REQUEST['responseType'] === 'multiple')
		{
			$responce = array();
			$rows = $this->Factory->Core->PDO();
			while($row = $rows->fetch())
			{
				array_push($responce,$row);
			}
		}
		elseif($_REQUEST['responseType'] === 'column')
			$responce = $this->Factory->Core->PDO()->fetchColumn();
		elseif($_REQUEST['responseType'] === 'options')
		{
			$params = array();
			$params['cache'] = false;
			$params['tname'] = $this->Factory->op_tbl;
			if(isset($_REQUEST['filters']))
				$params['filters'] = $_REQUEST['filters'];
			echo $this->Factory->Core->get_lib_html($params);
			return;
		}
		echo json_encode($responce,JSON_UNESCAPED_UNICODE);
	}
}
?>
