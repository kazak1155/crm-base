<?php
namespace Plugins;

class DB_get_column_meta
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		$Cache_name = $this->Factory->op_db.'_'.$this->Factory->op_tbl.'_columns_meta';
		if($this->Factory->Cache->exists($Cache_name))
			echo json_encode($this->Factory->Cache->get($Cache_name),JSON_UNESCAPED_UNICODE);
		else
		{
			$response = array();
			$this->Factory->Core->query = "EXEC sp_table_columns_meta :tname,:dbname";
			$this->Factory->Core->query_params = array(
				array(':tname',$this->Factory->op_tbl,\PDO::PARAM_STR),
				array(':dbname',$this->Factory->op_db,\PDO::PARAM_STR)
			);
			$res = $this->Factory->Core->PDO(array('global_db'=>true,'strict'=>true));
			while($row = $res->fetch())
			{
				$response[$row['COLUMN_NAME']] = array('IS_NULLABLE'=>$row['IS_NULLABLE'],'COLUMN_DEFAULT'=>$row['COLUMN_DEFAULT'],'DATA_TYPE'=>$row['DATA_TYPE']);
			}
			$this->Factory->Cache->add($Cache_name,$response);
			echo json_encode($this->Factory->Cache->get($Cache_name),JSON_UNESCAPED_UNICODE);
		}
	}
}
?>
