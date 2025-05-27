<?php
class Connection extends Grid
{
	const CONN_LANG = 'RUSSIAN';

	public $prm_conn;

	public $connection;

	public $current_db_name;

	public $query;

	public $query_params;

	public $last_query;

	public $last_inserted_id;

	public $query_debug = false;

	public $linked_db = false;

	public function con_database(String $db)
	{
		/* TODO */
		$this->current_db_name = $db;
		$this->init_conn($this->current_db_name);
	}
	/* TODO */
	public function register_changes()
	{

	}
	public function init_conn($db)
	{
		try
		{
			$this->connection = new PDO(DB_DRIVER.":server=".SRV_NAME.";database=".$db.";", "", "");
			$this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function init_prm_conn()
	{
		try
		{
			$this->prm_conn = new PDO(DB_DRIVER.":server=".SRV_NAME.";database=".GLOBAL_DB.";", "", "");
			$this->prm_conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function PDO(array $params = null)
	{
		$global_db = $params['global_db'] ?? false;
		$exec = $params['exec'] ?? false;
		$strict = $params['strict'] ?? false;
		$connection = $params['connection'] ?? false;
		$fetch_mode = $params['fetch_mode'] ?? 'assoc';
		$col_number = $params['col_number'] ?? 0;
		$rollback_on_error = $params['rollback_on_error'] ?? false;
		// use custom connection
		$connection ?? $conn = $connection;
		// use GLOBAL_DB or current connection
		if(!isset($conn))
			$global_db === false ? $conn = $this->connection : $conn = $this->prm_conn;
		$this->last_query = $this->query;
		//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2r_GG2222.txt", "a");fputs($des, $global_db.' '.$this->prm_conn.' '.$this->connection."\n"); fclose($des);
		//print_pre($conn);
		if(is_null($conn))
			exit('Undefined connection');
		$conn->exec("SET LANGUAGE ".self::CONN_LANG);
		try
		{
			if($exec === true)
			{
				$rows = $conn->exec($this->query);
				if($rows > 0)
					$this->register_changes();
			}
			else
			{
				if(is_null($this->query_params))
					$stmt = $conn->query($this->query);
				else
				{
					if($strict === false)
					{
						$stmt = $conn->prepare($this->query);
						$stmt->execute($this->query_params);
					}
					else
					{
						$stmt = $conn->prepare($this->query);
						foreach($this->query_params as $param)
						{
							$stmt->bindParam(...$param);
						}
						$stmt->execute();
					}
				}
				switch ($fetch_mode)
				{
					case 'assoc':
						$stmt->setFetchMode(PDO::FETCH_ASSOC);
						break;
					case 'num':
						$stmt->setFetchMode(PDO::FETCH_NUM);
						break;
					case 'col':
						$stmt->setFetchMode(PDO::FETCH_COLUMN,$col_number);
						break;
					case 'both':
						$stmt->setFetchMode(PDO::FETCH_BOTH);
						break;
					case 'lazy':
						$stmt->setFetchMode(PDO::FETCH_LAZY);
						break;
					case 'obj':
						$stmt->setFetchMode(PDO::FETCH_OBJ);
						break;
				}
			}
			if($this->query_debug === true)
				$this->PDO_debug_query('');
			else
				$this->PDO_refresh();
			return $stmt;
		}
		catch(Exception $e)
		{
			if($this->query_debug === true)
				$this->PDO_debug_query($e);
			$this->PDO_refresh();
			if(strstr($e->getMessage(), 'SQLSTATE['))
			{
				if($rollback_on_error == true)
					$conn->rollBack();
				preg_match_all('/\]([^\]\:\[]*)/', $e->getMessage(),$matches);
				$errorText = (array_values(array_filter($matches[1])));
				if(trim($_SESSION['dbo_error']) != ''){$_SESSION['dbo_error'] .= "\n\n";}
				$_SESSION['dbo_error'] .= $errorText[0].' Query: '.$this->last_query;
				//$r = print_r($e,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2yyy_call.txt", "w");fputs($des, $r."\n\n".$errorText[0].' Query: '.$this->last_query); fclose($des);
				exit($errorText[0].' Query: '.$this->last_query);
			}
			else
				exit($e->getMessage());
		}
	}
	public function PDO_refresh()
	{
		$this->query = null;
		$this->query_params = null;
	}
	public function PDO_quote($val)
	{
		if((!empty($val) || $val === '0') && $val !== 'NULL' && $val !== 'null')
			$val = $this->prm_conn->quote($val);
		return $val;
	}
	public function PDO_last_inserted_id($tname)
	{
		$this->query = "SELECT IDENT_CURRENT(?) AS int";
		$this->query_params = array($tname);
		//echo("1<br>");
		$this->last_inserted_id = $this->PDO(array('global_db'=>false))->fetchColumn();
		//echo("2<br>");
		$this->last_inserted_id = intval($this->last_inserted_id);
		/* TODO make PDO please, not this shit */
		//if($this->linked_db === true && empty($this->last_inserted_id))
			//$this->last_inserted_id = catchPDO("EXEC [srv].[dbo].[sp_getid] default,'$tname'",$conn)->fetchColumn();
		return $this->last_inserted_id;
	}
	public function PDO_debug_query($e)
	{
		print_pre($this->query);
		print_pre($this->query_params);
		print_pre($e);
		$this->PDO_refresh();
		exit();
	}
}
