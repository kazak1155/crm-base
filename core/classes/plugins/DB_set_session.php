<?php
namespace Plugins;

class DB_set_session
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		if(isset($_REQUEST['remove_name']))
			$this->Factory->Core->remove_sess(filter_var($_REQUEST['remove_name'], FILTER_SANITIZE_STRING));
		if(isset($_REQUEST['name']))
		{
			$this->Factory->Core->set_sess(
				filter_var($_REQUEST['subname'],FILTER_SANITIZE_STRING),
				filter_var($_REQUEST['val'],FILTER_SANITIZE_STRING),
				filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING)
			);
		}
		else
		{
			$this->Factory->Core->set_sess(
				filter_var($_REQUEST['subname'],FILTER_SANITIZE_STRING),
				filter_var($_REQUEST['val'],FILTER_SANITIZE_STRING)
			);
		}
	}
}
?>
