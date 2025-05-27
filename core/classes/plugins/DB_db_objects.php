<?php
namespace Plugins;

class DB_get_cols
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		/*TODO BROKEN */
		$db = $source['db'];
		$responce = $this->catchPDO("USE $db;SELECT * FROM sys.all_objects WHERE schema_id NOT IN(3,4) AND type_desc NOT IN('PRIMARY_KEY_CONSTRAINT','FOREIGN_KEY_CONSTRAINT','UNIQUE_CONSTRAINT','SERVICE_QUEUE') AND name NOT LIKE '%diagram%' AND is_ms_shipped = 0 ORDER BY name ASC");
		$responce->nextRowset();
		while($row = $responce->fetch(PDO::FETCH_ASSOC))
		{
			$tables[$row['name']] = $row['name'];
		}
		echo $this->construct_options($tables);
	}
}
?>
