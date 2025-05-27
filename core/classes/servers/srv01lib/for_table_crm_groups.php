<?php
class crm_groups{
	public function __construct ($Cache) {
		//$this->Cache = $Cache;
	}
	public function ex_data($data){
		return $_SESSION['config']['library']['groups'][$data];
	}
}
class grpFilter{
	public function __construct ($Cache) {
		//$this->Cache = $Cache;
	}
	public function grp_data(){
		$out = " AND [Тип_Группы] = '2' ";
		return $out;
	}

}