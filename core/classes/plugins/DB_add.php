<?php
namespace Plugins;

class DB_add
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$this->Factory->Core->query = urldecode($_REQUEST['query']);
		$this->Factory->Core->PDO();
		if(isset($_REQUEST['getid']) && isset($_REQUEST['tname'])){
			print $this->Factory->Core->PDO_last_inserted_id($_REQUEST['tname']);
		}
	}
}
?>
