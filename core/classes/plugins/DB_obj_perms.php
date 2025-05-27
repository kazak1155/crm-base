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
		$obj = $source['obj'];
		$responce = $this->catchPDO("USE $db;SELECT pr.name,permission_name FROM sys.database_permissions pe JOIN sys.database_principals pr on pe.grantee_principal_id = pr.principal_id WHERE pe.class = 1 AND pe.major_id = object_id('$obj') AND pe.minor_id = 0;");
		$responce->nextRowset();
		echo json_encode($responce->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE);
	}
}
?>
