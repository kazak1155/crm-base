<?php
class Grid extends Reference
{
	public function process_filters(stdClass $object,$table = null)
	{
		if(!is_null($table))
			$table = $table.'.';
		$firstElem = true;
		$qWhere = '';
		if (empty($object->rules))
		{
			$searchMulti = $object->groups[0];

			foreach ($searchMulti->rules as $rule)
			{
				if (!$firstElem)
					$qWhere .= ' '.$searchMulti->groupOp.' ';
				else
					$firstElem = false;
				switch ($rule->op)
				{
					case 'dateEq':
						if (strlen(trim($rule->data)) == 0)
							$qWhere .= '('.$table.$rule->field.' IS NULL )';
						else
						{
							$rule->data = $this->small_date_to_server($rule->data);
							$qWhere .= 'CONVERT(CHAR(10),'.$table.$rule->field.',112) = '.$this->PDO_quote($rule->data);
						}
						break;
					case 'dateNe':
						if (strlen(trim($rule->data)) == 0)
							$qWhere .= '('.$table.$rule->field.' IS NOT NULL'.')';
						else
						{
							$rule->data = $this->small_date_to_server($rule->data);
							$qWhere .= '(CONVERT(CHAR(10),'.$table.$rule->field.',112) <> '.$this->PDO_quote($rule->data).' OR '.$table.$rule->field.' IS NULL'.')';
						}
						break;
					case 'dateLe':
						$rule->data = $this->small_date_to_server($rule->data);
						$qWhere .= 'CONVERT(CHAR(10),'.$table.$rule->field.',112) <= '.$this->PDO_quote($rule->data);
						break;
					case 'dateGe':
						$rule->data = $this->small_date_to_server($rule->data);
						$qWhere .= 'CONVERT(CHAR(10),'.$table.$rule->field.',112) >= '.$this->PDO_quote($rule->data);
	                    break;
					case 'dateBn':
						$datas = explode(':',$rule->data);
						$qWhere .= $table.$rule->field.' BETWEEN '.$this->PDO_quote($this->small_date_to_server($datas[0])).' AND '.$this->PDO_quote($this->small_date_to_server($datas[1]).' 23:59:59');
						break;
					case 'eq':
						if (strlen(trim($rule->data)) == 0) $qWhere .= '('.$table.$rule->field.' IS NULL )';
						else $qWhere .= '('.$table.$rule->field.' = '.$this->PDO_quote($rule->data).')';
						break;
					case 'ne':
						if (strlen(trim($rule->data)) == 0) $qWhere .= '('.$table.$rule->field.' IS NOT NULL'.')';
						else $qWhere .= '('.$table.$rule->field.' <> '.$this->PDO_quote($rule->data).' OR '.$table.$rule->field.' IS NULL'.')';
						break;
					case 'gt': $qWhere .= $table.$rule->field.' > '.$rule->data; break;
					case 'ge': $qWhere .= $table.$rule->field.' >= '.$rule->data; break;
					case 'lt': $qWhere .= $table.$rule->field.' < '.$rule->data; break;
					case 'le': $qWhere .= $table.$rule->field.' <= '.$rule->data; break;
					case 'bw': $qWhere .= $table.$rule->field.' LIKE '.$this->PDO_quote($rule->data.'%'); break;
					case 'bn': $qWhere .= $table.$rule->field.' NOT LIKE '.$this->PDO_quote($rule->data.'%'); break;
					case 'cn': $qWhere .= $table.$rule->field.' LIKE '.$this->PDO_quote('%'.$rule->data.'%'); break;
					case 'nc': $qWhere .= $table.$rule->field.' NOT LIKE '.$this->PDO_quote('%'.$rule->data.'%'); break;
					case 'isN': $qWhere .= $table.$rule->field.' '.$rule->data; break;
					case 'isNull': $qWhere .= $table.$rule->field.' IS NULL'; break;
					case 'isNotNull':
						$qWhere .= '('.$table.$rule->field.' IS NOT NULL AND '.$table.$rule->field.' <> \'\')';
						break;
					case 'in': $qWhere .= $table.$rule->field.' IN ('.$rule->data.')'; break;
					default: break;
				}
			}
		}
		else
		{
			foreach ($object->rules as $key=>$rule)
			{
				if (!$firstElem)
				{
					if(isset($object->groupOp))
						$qWhere .= ' '.$object->groupOp.' ';
					else
						$qWhere .= ' AND ';
				}
				else
					$firstElem = false;
				if(count((array)$rule->op) > 1)
				{
					$sub = new stdClass;
					$sub->groups[0] = new stdClass;
					$sub->groups[0]->groupOp = $object->rules[$key]->groupOps;
					for($index = 0;$index < count($rule->op);$index++)
					{
						$sub->groups[0]->rules[$index] = new stdClass;
						$sub->groups[0]->rules[$index]->field = $object->rules[$key]->field[$index];
						$sub->groups[0]->rules[$index]->op = $object->rules[$key]->op[$index];
						$sub->groups[0]->rules[$index]->data = $object->rules[$key]->data[$index];
					}
					$qWhere .= '('.$this->process_filters($sub).')';
					
					continue;
				}
				switch ($rule->op)
				{
					case 'dateEq':
						if (strlen(trim($rule->data)) == 0)
							$qWhere .= '('.$table.$rule->field.' IS NULL )';
						else
						{
							$rule->data = $this->small_date_to_server($rule->data);
							$qWhere .= 'CONVERT(CHAR(10),'.$table.$rule->field.',112) = '.$this->PDO_quote($rule->data);
						}
					break;
					case 'dateNe':
						if (strlen(trim($rule->data)) == 0)
							$qWhere .= '('.$table.$rule->field.' IS NOT NULL'.')';
						else
						{
							$rule->data = $this->small_date_to_server($rule->data);
							$qWhere .= '(CONVERT(CHAR(10),'.$table.$rule->field.',112) <> '.$this->PDO_quote($rule->data).' OR '.$table.$rule->field.' IS NULL'.')';
						}
					break;
					case 'dateLe':
						$rule->data = $this->small_date_to_server($rule->data);
						$qWhere .= '(CONVERT(CHAR(10),'.$table.$rule->field.',112) <= '.$this->PDO_quote($rule->data).')';
					break;
					case 'dateGe':
						$rule->data = $this->small_date_to_server($rule->data);
						$qWhere .= '(CONVERT(CHAR(10),'.$table.$rule->field.',112) >= '.$this->PDO_quote($rule->data).')';
					break;
					case 'dateBn':
						$datas = explode(':',$rule->data);
						$qWhere .= $table.$rule->field.' BETWEEN '.$this->PDO_quote($this->small_date_to_server($datas[0])).' AND '.$this->PDO_quote($this->small_date_to_server($datas[1]).' 23:59:59');
					break;
					case 'eq':
						if (strlen(trim($rule->data)) == 0) $qWhere .= '('.$table.$rule->field.' IS NULL )';
						else $qWhere .= '('.$table.$rule->field.' = '.$this->PDO_quote($rule->data).')';
						break;
					case 'ne':
						if (strlen(trim($rule->data)) == 0) $qWhere .= '('.$table.$rule->field.' IS NOT NULL'.')';
						else $qWhere .= '('.$table.$rule->field.' <> '.$this->PDO_quote($rule->data).' OR '.$table.$rule->field.' IS NULL'.')';
						break;
					case 'gt': $qWhere .= '('.$table.$rule->field.' > '.$rule->data.')'; break;
					case 'ge': $qWhere .= '('.$table.$rule->field.' >= '.$rule->data.')'; break;
					case 'lt': $qWhere .= '('.$table.$rule->field.' < '.$rule->data.')'; break;
					case 'le': $qWhere .= '('.$table.$rule->field.' <= '.$rule->data.')'; break;
					case 'bw': $qWhere .= '('.$table.$rule->field.' LIKE '.$this->PDO_quote($rule->data.'%').')'; break;
					case 'bn': $qWhere .= '('.$table.$rule->field.' NOT LIKE '.$this->PDO_quote($rule->data.'%').')'; break;
					case 'cn': $qWhere .= '('.$table.$rule->field.' LIKE '.$this->PDO_quote('%'.$rule->data.'%').')'; break;
					case 'nc': $qWhere .= '('.$table.$rule->field.' NOT LIKE '.$this->PDO_quote('%'.$rule->data.'%').')'; break;
					case 'isN': $qWhere .= $table.$rule->field.' '.$rule->data; break;
					case 'isNull': $qWhere .= $table.$rule->field.' IS NULL'; break;
					case 'isNotNull':
						$qWhere .= '('.$table.$rule->field.' IS NOT NULL AND '.$table.$rule->field.' <> \'\')';
						break;
					case 'in': $qWhere .= '('.$table.$rule->field.' IN ('.$rule->data.'))'; break;
					default: break;
				}
			}
		}
		//print_pre($qWhere);
		return $qWhere;
	}
	public function process_filters_v2(array $array)
	{
		$field = $array['field'];
		$op = $array['op'];
		$data = $array['data'];
		switch ($op)
		{
			case 'dateEq':
				if (strlen(trim($data)) == 0)
					$qWhere = '('.$field.' IS NULL )';
				else
				{
					$data = $this->small_date_to_server($data);
					$qWhere = 'CONVERT(CHAR(10),'.$field.',112) = '.$this->PDO_quote($data);
				}
				break;
			case 'dateNe':
				if (strlen(trim($data)) == 0)
					$qWhere = '('.$field.' IS NOT NULL'.')';
				else
				{
					$data = $this->small_date_to_server($data);
					$qWhere = '(CONVERT(CHAR(10),'.$field.',112) <> '.$this->PDO_quote($data).' OR '.$field.' IS NULL'.')';
				}
				break;
			case 'dateLe':
				$data = $this->small_date_to_server($data);
				$qWhere = '(CONVERT(CHAR(10),'.$field.',112) <= '.$this->PDO_quote($data).')';
				break;
			case 'dateGe':
				$data = $this->small_date_to_server($data);
				$qWhere = '(CONVERT(CHAR(10),'.$field.',112) >= '.$this->PDO_quote($data).')';
				break;
			case 'dateBn':
				$datas = explode(':',$data);
				$qWhere = $field.' BETWEEN '.$this->PDO_quote($this->small_date_to_server($datas[0])).' AND '.$this->PDO_quote($this->small_date_to_server($datas[1]).' 23:59:59');
				break;
			case 'eq':
				if (strlen(trim($data)) == 0)
					$qWhere = '('.$field.' IS NULL )';
				else
					$qWhere = '('.$field.' = '.$this->PDO_quote($data).')';
				break;
			case 'ne':
				if (strlen(trim($data)) == 0)
					$qWhere = '('.$field.' IS NOT NULL'.')';
				else
					$qWhere = '('.$field.' <> '.$this->PDO_quote($data).' OR '.$field.' IS NULL'.')';
				break;
			case 'gt':
				$qWhere = '('.$field.' > '.$data.')';
				break;
			case 'ge':
				$qWhere = '('.$field.' >= '.$data.')';
				break;
			case 'lt':
				$qWhere = '('.$field.' < '.$data.')';
				break;
			case 'le':
				$qWhere = '('.$field.' <= '.$data.')';
				break;
			case 'bw':
				$qWhere = '('.$field.' LIKE '.$this->PDO_quote($data.'%').')';
				break;
			case 'bn':
				$qWhere = '('.$field.' NOT LIKE '.$this->PDO_quote($data.'%').')';
				break;
			case 'cn':
				$qWhere = '('.$field.' LIKE '.$this->PDO_quote('%'.$data.'%').')';
				break;
			case 'nc':
				$qWhere = '('.$field.' NOT LIKE '.$this->PDO_quote('%'.$data.'%').')';
				break;
			case 'isN':
				$qWhere = $field.' '.$data;
				break;
			case 'in':
				$qWhere = $field.' IN ';
				break;
			default:
				break;
		}
		return $qWhere;
	}
	public function arrange_tree_data($data,$id,$parent_id)
	{
		function sort_tree($data,$id,$parent_id,$pid = 0,&$result = array(),&$depth = 0)
		{
			foreach ($data as $key => $value)
			{
				if ($value[$parent_id] == $pid)
				{
					$value['depth'] = $depth;
					array_push($result,$value);
					unset($data[$key]);
					$prev_parent = $pid;
					$pid = $value[$id];
					$depth++;
					sort_tree($data,$id,$parent_id,$pid,$result,$depth);
					$pid = $prev_parent;
					$depth--;
				}
			}
			return $result;
		}
		return sort_tree($data,$id,$parent_id);
	}
	public function process_fields($object)
	{
		for ($i=0; $i < count($object); $i++) {
			if ($i < (count($object) - 1 ))
				$string .= $object[$i]->field.',';
			else
				$string .= $object[$i]->field;
		}
		return $string;
	}
	public function process_fields_v2(array $array)
	{
		for ($i=0; $i < count($array); $i++) {
			if ($i < (count($array) - 1 ))
				$string .= $array[$i].',';
			else
				$string .= $array[$i];
		}
		return $string;
	}
	public function is_wrong_value($name,$val)
	{
		$val = ltrim($val);
		if($this->is_wrong_date($name,$val) == true)
			$val = $this->full_date_to_server($val);
		elseif($this->is_wrong_number($val) == true)
			$val = str_replace(',', '.', $val);
		$val = $this->PDO_quote($val);
		return $val;
	}
	public function is_wrong_number($val)
	{
		if(!empty($val) && strpos($val, ','))
		{
			$val = str_replace(',', '.', $val);
			if(is_numeric($val))
				return true;
		}
	}
	public function is_wrong_date($name,$val)
	{
		if((!empty($val)) && $val !== 'null' && (strpos($name, 'data') !== false || strpos($name, 'Дата') !== false || strpos($name, 'дата') !== false))
			return true;
	}
}
?>
