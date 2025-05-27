<?php
class Reference
{
	const REF_PREFIX = array('Б','Выбор');

	public function get_lib(array $args)
	{
		extract($args);

		$tname ?? die('No source table');

		$fields ?? $fields = array('Код','Название');
		$fields = $this->process_fields_v2($fields);

		if(!is_null($filters))
		{
			$filters_obj = new stdClass();
			$filters_obj->rules = array();
			if(array_key_exists('groupOp',$filters))
				$filters_obj->groupOp = $filters['groupOp'];
			else
				$filters_obj->groupOp = 'AND';
			foreach($filters as $key => $val)
			{
				if(is_array($val))
				{
					$filter_object = new stdClass();
					//$filter_object->field = 'lcase('.$val['field'].')';
					$filter_object->field = $val['field'];
					$filter_object->op = $val['op'];
					//$filter_object->data = 'lcase('.$val['data'].')';
					$filter_object->data = $val['data'];
					array_push($filters_obj->rules,$filter_object);
				}
			}
			$filters = 'WHERE'.QUERY_SPACE.$this->process_filters($filters_obj);
		}

		$order ?? $order = 2;
		$order_by ?? $order_by = null;
		$with_id ?? $with_id = true;
		$labels_only ?? $labels_only = false;
		$empty ?? $empty = true;
		$encode ?? $encode = true;
		$cache ?? $cache = true;

		$pdo_array = array('fetch_mode'=>'num');
		$srv ?? $srv = false;
		if($srv === true)
			$pdo_array['global_db'] = true;
		$response = new stdClass;
		if($empty === true)
			$response->NULL = '';

		if($cache == true)
		{
			$Cache_name = $this->current_db_name.'_'.$tname;
			if($this->wincache->exists($Cache_name)) {
				//echo 't222' . $filters;
				return $encode ? json_encode($this->wincache->get($Cache_name), JSON_UNESCAPED_UNICODE) : $this->wincache->get($Cache_name);
			}
			else
			{
				//echo 't111'.$filters;
				$this->query = "SELECT DISTINCT $fields FROM $tname $filters ORDER BY $order $order_by";
				$stmt = $this->PDO($pdo_array);
				while($row = $stmt->fetch())
				{
					$row[0] = addslashes($row[0]);
					$row[1] = addslashes($row[1]);

					$id = $labels_only === true ? $row[1] : $row[0];
					$value = $with_id === false ? $row[0] : $row[1];

					$response->$id = $value;
				}
				$this->wincache->add($Cache_name,$response);
				return $encode ? json_encode($this->wincache->get($Cache_name),JSON_UNESCAPED_UNICODE) : $this->wincache->get($Cache_name);
			}
		}
		else
		{
			$this->query = "SELECT DISTINCT $fields FROM $tname $filters ORDER BY $order $order_by";
			//Echo $this->query;
			$stmt = $this->PDO($pdo_array);
			while($row = $stmt->fetch())
			{
				$row[0] = addslashes($row[0]);
				$row[1] = addslashes($row[1]);

				$id = $labels_only === true ? $row[1] : $row[0];
				$value = $with_id === false ? $row[0] : $row[1];

				$response->$id = $value;
			}
			return $encode ? json_encode($response,JSON_UNESCAPED_UNICODE) : $response;
		}
	}
	public function get_lib_html(array $args)
	{
		extract($args);
		$tname ?? die('No source table');
		$fields ?? $fields = ['Код','Название'];
		$filters ?? $filters = null;
		$order ?? $order = 2;
		$with_id ?? $with_id = true;
		$labels_only ?? $labels_only = false;
		$empty ?? $empty = true;
		$selected ?? $selected = null;
		$db_option_attrs ?? $db_option_attrs = '';
		$init_data ?? $init_data = null;
		$cache ?? $cache = true;

		if($init_data == null)
		{
			if(is_array($db_option_attrs) == true)
				$db_option_attrs = $this->get_lib_html_attr(['tname'=>$tname,'order'=>$order,'order_by'=>$order_by],$db_option_attrs);
			else
				$db_option_attrs = array();
			$args['empty'] = false;
			$args['encode'] = false;
			$prepared_data = $this->get_lib($args);
		}
		else
			$prepared_data = $init_data;

		$empty ? $html .= "<option></option>": null;
		foreach ($prepared_data as $key => $value)
		{
			if($key == 'NULL')
				$key = '';

			if(!is_null($selected) && ($selected == $key || $selected == $value))
			{
				if($selected == $value)
					$saved_selection_by_text = "<option $db_option_attrs[$key] selected='selected' value='$key'>$value</option>";
				if($selected == $key)
				{
					$id_priority = true;
					$option_attrs = "$db_option_attrs[$key] selected='selected' value='$key'";
				}
			}
			else
				$option_attrs = "$db_option_attrs[$key] value='$key'";

			$option = "<option $option_attrs>$value</option>";
			$html .= $option;
			if(empty($id_priority) && !empty($saved_selection_by_text) && $value === end($prepared_data))
				$html .= $saved_selection_by_text;
		}
		return $html;
	}
	public function get_lib_html_attr(array $html_init,array $db_option_attrs)
	{
		$stm = $this->catchPDO("SELECT ".$db_option_attrs['id'].",".$this->processFields_v2($db_option_attrs['attrs_fields'])." FROM ".$html_init['tname']." ORDER BY ".$html_init['order']." ".$html_init['order_by']);
		$response = array();
		$i = 0;
		while($row = $stm->fetch(PDO::FETCH_ASSOC))
		{
			$attrs = '';
			for($i = 0; $i < count($db_option_attrs['attrs_names']); $i++)
			{
				$attrs .= $db_option_attrs['attrs_names'][$i].'="'.$row[$db_option_attrs['attrs_fields'][$i]].'" ';
			}
			$response[$row[$db_option_attrs['id']]] = $attrs;
		}
		return $response;
	}
	public function htmlReadyLibData(array $data,$empty)
	{
		$empty ? $html .= "<option></option>": null;
		foreach ($data as $key => $value) {
			$html .= "<option value=$key>$value</option>";
		}
		return $html;
	}
	public function construct_options(array $data)
	{
		foreach ($data as $key => $value)
		{
			$r .= "<option value='$key'>$value</option>";
		}
		return $r;
	}
	public function get_reference_tables()
	{
		$select_options = array();

		$this->query = "SELECT name,'tables' as [type] FROM sys.Tables WHERE".QUERY_SPACE;
		$this->get_reference_tables_generator();
		$this->query .= "UNION".QUERY_SPACE;
		$this->query .= "SELECT name,'views' as [type] FROM sys.Views WHERE".QUERY_SPACE;
		$this->get_reference_tables_generator();

		$rows = $this->PDO(array('strict'=>true,'multiple_bindings'=>true));
		while($row = $rows->fetch())
		{
			//array_push($table_names,$row);
			//$permission_rows = $
			$this->query = "EXEC dbo.sp_user_perms :db_name,:table_name,:table_type";
			$this->query_params = array(
				array(':db_name',$this->current_db_name,\PDO::PARAM_STR),
				array(':table_name',$row['name'],\PDO::PARAM_STR),
				array(':table_type',$row['type'],\PDO::PARAM_STR)
			);
			$permission = $this->PDO(array('global_db'=>true,'strict'=>true))->fetch();
			if(!empty($permission))
			{
				$clean_name = mb_strtolower($row['name'],'UTF-8');
				$preg_array = self::REF_PREFIX;
				array_walk($preg_array, function(&$item) { $item = "/^(".$item."_)/iu"; });
				array_push($preg_array,'/_/');
				$clean_name = preg_replace($preg_array,array("",""," "),$clean_name);
				$clean_name = mb_ucfirst($clean_name);
				$select_options[$row['name']] = $clean_name;
			}
		}
		return $this->construct_options($select_options);
	}
	private function get_reference_tables_generator()
	{
		$key_helper = is_array($this->query_params) ? count(self::REF_PREFIX) : 0;
		foreach(self::REF_PREFIX as $key=>$value)
		{
			if(array_key_exists(++$key,self::REF_PREFIX))
				$this->query .= "name LIKE ?".QUERY_SPACE."OR".QUERY_SPACE;
			else
				$this->query .= "name LIKE ?".QUERY_SPACE;
			if(is_array($this->query_params))
				array_push($this->query_params,array($key + $key_helper,$value."[_]%",PDO::PARAM_STR));
			else
				$this->query_params = array(array($key + $key_helper,$value."[_]%",PDO::PARAM_STR));
		}
	}
}
?>
