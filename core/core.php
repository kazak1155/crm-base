<?php
class Core extends Base {

	public $server_prm = Array();
	public $usr_cfg = Array();
	public $data_query;

	public $wincache;

	public function __construct()
	{
		$this->init_prm_conn();
		$this->init_global_params();
		if(UPDATE_TRANSLATE === true)
			$this->update_translate();
		$this->wincache = new WinCache();
	}
	public function init_global_params()
	{
		$this->server_prm = $this->get_sess('server_prm');
		if(empty($this->server_prm))
		{
			$this->query = "SELECT Переменная,Значение FROM Б_Настройки";
			$stmt = $this->PDO(array('global_db'=>true));
			while($param = $stmt->fetch())
			{
				$this->set_sess($param['Переменная'],$param['Значение'],'server_prm');
				$this->server_prm[$param['Переменная']] = $param['Значение'];
			}
		}
	}
	public function update_translate()
	{
		$this->query = "SELECT Язык as [lang],Переменная as [key],Наименование as [value] FROM З_Б_Перевод";
		$translation_data = $this->PDO(array('global_db'=>true));
		$json = array();
		while($translation = $translation_data->fetch())
		{
			if(is_array($json[$translation['lang']]))
				$json[$translation['lang']][$translation['key']] = $translation['value'];
			else
				$json[$translation['lang']] = array($translation['key']=>$translation['value']);
		}
		file_put_contents(JSON_DIR."translation.json", json_encode($json,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}
	public function update_references($module, $component)
	{
		if(!is_null($module) && !is_null($component))
		{
			if(is_dir(TEMPLATES_DIR.$module.DS.$component) === true)
			{
				$this->query = "INSERT INTO srv.dbo.Routes (module, component) VALUES (:module, :component)";
				$this->query_params = array(
					array(':module', $module, PDO::PARAM_STR),
					array(':component', $component, PDO::PARAM_STR)
				);
				$this->PDO(array('global_db'=>true,'strict'=>true));
				$json_file = file_get_contents(JSON_DIR."reference.json");
				$json_temp = json_decode($json_file,true);
				unset($json_file);
				if(array_key_exists($module,$json_temp))
				{
					array_push($json_temp[$module],$component);
				}
				else
				{
					$json_temp[$module] = array();
					array_push($json_temp[$module],$component);
				}
				$json_temp = json_encode($json_temp,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
				file_put_contents(JSON_DIR."reference.json",$json_temp);
			}
		}
	}
}
?>
