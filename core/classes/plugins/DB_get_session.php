<?php
namespace Plugins;

class DB_get_session
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		echo json_encode($this->Factory->Core->get_sess(filter_var($_REQUEST['remove_name'], FILTER_SANITIZE_STRING)),JSON_UNESCAPED_UNICODE);
	}
}
?>
