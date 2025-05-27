<?php
namespace Plugins;

class DB_edit
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Factory->Core->query = urldecode($_REQUEST['query']);
		$this->Factory->Core->PDO();
	}
}
?>
