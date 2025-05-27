<?php
class Mssql_factory
{
	const PLUGIN_PREFIX = 'DB';

	const SCHEMA = 'dbo.';

	const DEFAULT_DATABASE = 'tig50_view';

	public $User;

	public $Core;

	public $Site;

	public $Cache;

	public $op;

	public $op_action;

	public $op_db;

	public $op_get_id;

	public $op_tbl;
	public $op_tbl_r;

	public $op_tbl_id_name;
	public $op_tbl_id_val;

	public function __construct($User,$Core,$Site)
	{
		$this->Core = $Core;
		$this->User = $User;
		$this->Site = $Site;
		$this->Cache = new WinCache();
		$this->init();
	}
	public function init()
	{
		$this->process_request();
		$this->process_connection();
		if(method_exists($this,$this->op))
			$this->{$this->op}();
		if((bool)$this->op_get_id === true)
			echo $this->Core->PDO_last_inserted_id($this->op_tbl);

	}
	public function process_request()
	{
		$this->op = $_REQUEST['oper'] ?? exit('Unkown operation');
		$this->op_action = $_REQUEST['action'] ?? null;
		$this->op_db = $_REQUEST['db'] ?? $this->Site->get_reference_database();
		$this->op_get_id = $_REQUEST['getid'] ?? false;
		$this->op_tbl = $_REQUEST['tname'] ?? null;
		$this->op_tbl_r ?? $_REQUEST['source_table'] ?? $this->op_tbl;
		$this->op_tbl_id_name = $_REQUEST['tid'] ?? null;
		$this->op_tbl_id_val = $_REQUEST['id'] ?? null;

		$this->clear_request();
	}
	public function process_connection()
	{
		if(is_null($this->Site->reference_url))
			$this->Core->con_database(self::DEFAULT_DATABASE);
		else
			$this->Core->con_database($this->Site->get_reference_database());
	}
	public function clear_request()
	{
		$keys_to_clear = array(
			'oper',
			'db',
			'tname',
			'tid',
			'id',
			'getid'
		);
		foreach ($keys_to_clear as $key => $value) {
			if(array_key_exists($value,$_REQUEST))
			{
				unset($_REQUEST[$value]);
			}
		}
	}
	public function custom()
	{
		switch($this->op_action)
		{
			case 'kill_sess':
				session_destroy();
				break;
			case 'flush_cache':
				$this->Cache->flush();
				break;
			case 'flush_cache_specify':
				$this->Cache->delete($this->op_db.'_'.$this->op_tbl);
				break;
			case 'raw_lib_data':
				echo $this->Core->get_lib_html(['tname'=>$this->op_tbl]);
				break;
			default:
				try
				{
					$class_name = __NAMESPACE__.'Plugins\\'.self::PLUGIN_PREFIX.'_'.$this->op_action;
					new $class_name($this);
				}
				catch(Exception $e)
				{
					print_r($e);
				}
				break;
		}

	}
	public function view()
	{
		$response = array();

		$this->Core->remove_sess($this->op_db.'_'.$this->op_tbl.'_search_current');
		$this->Core->remove_sess($this->op_db.'_'.$this->op_tbl.'_search_permament');
		if(isset($_REQUEST['_search']) && $_REQUEST['_search'] === 'true')
		{
			$filters = preg_replace('/\t+/', " ", $_REQUEST['filters']);
			$json = json_decode($filters);
			if(!is_null($json))
			{
				$search_string = $this->Core->process_filters($json,$this->op_tbl);
				$this->Core->set_sess($this->op_db.'_'.$this->op_tbl.'_search_current',$search_string);
			}
		}
		if(isset($_REQUEST['perm_filters']))
		{
			$filters = preg_replace('/\t+/', " ", $_REQUEST['perm_filters']);
			$json = json_decode($filters);
			if(!is_null($json) && count($json->rules) > 0)
			{
				$s = $this->Core->process_filters($json,$this->op_tbl);
				$this->Core->set_sess($this->op_db.'_'.$this->op_tbl.'_search_permament',$s);
				if(isset($search_string))
					$search_string = $search_string.' AND '.$s;
				else
					$search_string = $s;
			}
		}

		// start count query
		$this->Core->query = "SELECT COUNT($this->op_tbl_id_name) FROM ".self::SCHEMA."$this->op_tbl".QUERY_SPACE;
		// add where clause
		if(isset($search_string))
			$this->Core->query .= "WHERE $search_string";
		// add subgrid clause if where is set
		if(isset($_REQUEST['mainId']) && isset($_REQUEST['mainIdname']) && isset($search_string))
			$this->Core->query .= " AND ".$_REQUEST['mainIdname'].'='.$this->Core->PDO_quote($_REQUEST['mainId']);
		// add subgrid clause
		elseif(isset($_REQUEST['mainId']) && isset($_REQUEST['mainIdname']) && !isset($search_string))
			$this->Core->query .= "WHERE ".$_REQUEST['mainIdname'].'='.$this->Core->PDO_quote($_REQUEST['mainId']);

		$total_rows = $this->Core->PDO()->fetchColumn();

		$response['page'] = $_REQUEST['page'];
		$response['total'] = ceil($total_rows / $_REQUEST['rows']);
		$response['records'] = $total_rows;
		$response['where'] = $search_string;
		$response['rows'] = array();

		if(isset($_REQUEST['check_files']) && $_REQUEST['check_files'] === true)
		{
			$this->Core->query = "SELECT ".self::SCHEMA."$this->op_tbl.*,";
			$injection = $this->Core->PDO_quote($this->Site->get_reference_database()).",".$this->Core->PDO_quote($this->op_tbl_r).",".$this->Core->PDO_quote($this->op_tbl).",".$this->Core->PDO_quote($this->op_tbl_id_name);
			$this->Core->query .= GLOBAL_DB.self::SCHEMA."ft_filestream_data($injection)".QUERY_SPACE;
		}
		else
			$this->Core->query = "SELECT ".self::SCHEMA."$this->op_tbl.* FROM ".self::SCHEMA."$this->op_tbl".QUERY_SPACE;

		if(isset($_REQUEST['sortQue']))
		{
			$sort_data = json_decode($_REQUEST['sortQue']);
			$_REQUEST['sidx'] = "$sort_data->join_tname.$sort_data->order_fld";
			$this->Core->query .= "LEFT JOIN $sort_data->join_tname ON $sort_data->join_tname.$sort_data->join_fld = $this->op_tbl.$sort_data->ref_fld".QUERY_SPACE;
		}
		if(isset($search_string))
		{
			$this->Core->set_sess($this->op_db.'_'.$this->op_tbl.'_search_global',$search_string);
			$this->Core->query .= "WHERE $search_string".QUERY_SPACE;
		}
		if(isset($_REQUEST['mainId']) && isset($_REQUEST['mainIdname']) && isset($search_string))
			$this->Core->query .= "AND ".$_REQUEST['mainIdname'].'='.$this->Core->PDO_quote($_REQUEST['mainId']).QUERY_SPACE;
		elseif(isset($_REQUEST['mainId']) && isset($_REQUEST['mainIdname']) && !isset($search_string))
			$this->Core->query .= "WHERE ".$_REQUEST['mainIdname'].'='.$this->Core->PDO_quote($_REQUEST['mainId']).QUERY_SPACE;

		$this->Core->query .= "ORDER BY ".$_REQUEST['sidx']." ".$_REQUEST['sord'].QUERY_SPACE;

		$this->Core->query .= "OFFSET ".(($_REQUEST['page'] * $_REQUEST['rows']) - $_REQUEST['rows'])." ROWS FETCH NEXT ".$_REQUEST['rows']." ROWS ONLY";

		$rows = $this->Core->PDO();
		while($row = $rows->fetch())
		{
			$helper = array('id'=>reset($row));
			foreach ($row as $key => $value)
			{
				$helper[$key] = $value;
			}
			array_push($response['rows'],$helper);
		}
		if($_REQUEST['tree'] == true)
		{
			$response['rows'] = $this->Core->arrange_tree_data($response['rows'],$this->op_tbl_id_name,$_REQUEST['tree_parent_id']);
		}

		echo json_encode($response,JSON_UNESCAPED_UNICODE);
	}
	public function add()
	{
		foreach ($_REQUEST as $key => $value)
		{
			$_REQUEST[$key] = $this->Core->is_wrong_value($key,$value);
		}

		$fieldsname = array_keys(array_filter($_REQUEST,'filterZeros'));
		$fieldsvalues = array_values(array_filter($_REQUEST,'filterZeros'));

		if(!empty($fieldsvalues) && !empty($fieldsvalues))
		{
			$this->Core->query = "INSERT INTO $this->op_tbl (".implode(',',$fieldsname).") VALUES (".implode(",",$fieldsvalues).")";
			$this->Core->PDO(array("exec"=>true));
		}
	}
	public function edit()
	{
		if(count($_REQUEST) === 1)
		{
			$key = key($_REQUEST);
			$value = $this->Core->is_wrong_value($key,reset($_REQUEST));
			if(empty($value))
				$value = 'NULL';
			$this->Core->query = "UPDATE $this->op_tbl SET $key = $value WHERE $this->op_tbl_id_name = $this->op_tbl_id_val";
		}
		else
		{
			array_filter($_REQUEST,'filterZeros');
			foreach ($_REQUEST as $key => $value) {
				$value = $this->Core->is_wrong_value($key,$value);
				if(!empty($value))
				{
					if(key($_REQUEST) == $key)
						$this->Core->query = "UPDATE $this->op_tbl SET $key = $value".QUERY_SPACE;
					else
					{
						if(key($_REQUEST) == $key)
							$this->Core->query .= "$key = $value";
						else
							$this->Core->query .= ",$key = $value".QUERY_SPACE;
					}
				}
			}
			$this->Core->query .= "WHERE $this->op_tbl_id_name = $this->op_tbl_id_val";
		}
		$this->Core->PDO(array("exec"=>true));
	}
	public function del()
	{
		if(strpos($this->op_tbl_id_val,',') !== false)
			$this->Core->query = "DELETE FROM $this->op_tbl WHERE $this->op_tbl_id_name IN ($this->op_tbl_id_val)";
		else
			$this->Core->query = "DELETE FROM $this->op_tbl WHERE $this->op_tbl_id_name = ".$this->Core->PDO_quote($this->op_tbl_id_val);
		if(isset( $_REQUEST['filters']))
		{
			$filters = preg_replace('/\t+/', " ", $_REQUEST['filters']);
			$json = json_decode($filters);
			if(!is_null($json))
				$this->Core->query .= QUERY_SPACE.'AND'.QUERY_SPACE.$this->Core->process_filters($json,$this->op_tbl);
		}
		$this->Core->PDO(array("exec"=>true));
	}
	public function view_selects()
	{
		$response = array('results'=>array());
		$query_search = $_REQUEST['search'];
		$query_data = json_decode($_REQUEST['info']);
		if(!empty($this->Core->get_sess($this->op_db.'_'.$query_data->ref_tname.'_search_global')))
		{
			$view_search_current = $this->Core->get_sess($this->op_db.'_'.$query_data->ref_tname.'_search_current');
			$view_search_permament = $this->Core->get_sess($this->op_db.'_'.$query_data->ref_tname.'_search_permament');
			if(strpos($view_search_current,$query_data->ref_fld))
			{
				$static = "$query_data->ref_tname.$query_data->ref_fld";
				$pattern = "\sAND\s\($static\s\=\s\'[A-z0-9]*\'\)|\($static\s\=\s\'[A-z0-9]*\'\)\sAND\s|\($static\s\=\s\'[A-z0-9]*\'\)";
				$view_search_current = mb_ereg_replace($pattern,"",$view_search_current);
			}
			if(!empty($view_search_permament))
			{
				$search_string = $view_search_permament;
				if(!empty($view_search_current))
					$search_string .= QUERY_SPACE."AND $view_search_current";
			}
			else
				$search_string = $view_search_current ?? null;
		}
		$this->Core->query = $this->view_selects_query($query_data,$_REQUEST['search'],$search_string);
		$rows = $this->Core->PDO(array('fetch_mode'=>'num'));
		while($row = $rows->fetch())
		{
			if($query_data->id_only == true)
				array_push($response['results'],array('id'=>$row[0],'text'=>$row[0]));
			else
			{
				if($query_data->id == true)
					array_push($response['results'],array('id'=>$row[0],'text'=>$row[1]));
				else
					array_push($response['results'],array('id'=>$row[1],'text'=>$row[1]));
			}
		}
		if(empty($query_search) && count($response['results']) > 5)
		{
			unset($response['results'][5]);
			$response['results'][5] = array('id'=>'disabled','text'=>'More available...','disabled'=>'disabled');
		}
		echo json_encode($response,JSON_UNESCAPED_UNICODE);
	}
	public function view_ac_selects()
	{
		$query_data = json_decode($_REQUEST['info']);
		$response = array();
		if($query_data->getNull == true)
			array_push($response,['value'=>'NULL','label'=>'---']);
		$this->Core->query = $this->view_selects_query($query_data,$_REQUEST['search'],$query_data->search,$query_data->top);
		$rows = $this->Core->PDO(array('fetch_mode'=>'num'));
		while($row = $rows->fetch())
		{
			array_push($response,['value'=>$row[0],'label'=>$row[1]]);
		}
		echo json_encode($response,JSON_UNESCAPED_UNICODE);
	}
	private function view_selects_query($query_data,$search,$s_string = null,$top = 6)
	{
		/* TODO
		 * Add operands
		 */
		if(empty($s_string))
		{
			if(empty($search))
			{
				$query_string = "SELECT DISTINCT TOP $top ".$this->Core->process_fields_v2($query_data->flds).QUERY_SPACE;
				$query_string .= "FROM $query_data->tname".QUERY_SPACE;
			}
			else
			{
				$search = $this->Core->PDO_quote('%'.$search.'%');
				$query_string = "SELECT DISTINCT ".$this->Core->process_fields_v2($query_data->flds).QUERY_SPACE;
				$query_string .= "FROM $query_data->tname".QUERY_SPACE;
				$query_string .= "WHERE $query_data->sfld LIKE ($search)".QUERY_SPACE;
			}
		}
		else
		{
			if(empty($search))
			{
				$query_string = "SELECT TOP $top ".$this->Core->process_fields_v2($query_data->flds).QUERY_SPACE;
				$query_string .= "FROM $query_data->tname".QUERY_SPACE;
				$query_string .= "WHERE $query_data->refid IN".QUERY_SPACE;
				$query_string .= "(SELECT DISTINCT $query_data->ref_fld FROM $query_data->ref_tname WHERE $s_string)".QUERY_SPACE;
			}
			else
			{
				$search = $this->Core->PDO_quote('%'.$search.'%');
				$query_string = "SELECT ".$this->Core->process_fields_v2($query_data->flds).QUERY_SPACE;
				$query_string .= "FROM $query_data->tname".QUERY_SPACE;
				$query_string .= "WHERE $query_data->sfld LIKE ($search) AND $query_data->refid IN".QUERY_SPACE;
				$query_string .= "(SELECT DISTINCT $query_data->ref_fld FROM $query_data->ref_tname WHERE $s_string)".QUERY_SPACE;

			}
		}
		if($query_data->order != false)
			$query_string .= "ORDER BY $query_data->order";
		return $query_string;
	}
}
?>
