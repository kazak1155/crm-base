<?php
class Site
{
	public $Core;
	public $User;

	public $lang;

	const MAX_URL_DEPTH = 2;

	protected $references_json;
	protected $translation_json;
	protected $reference_database_json;

	public $req_rowid;
	public $req_data;
	public $plugin_name;

	public $reference_url;
	public $reference_module;
	public $reference_component;
	public $reference_url_parts = array();

	public $reference_permitted;

	public $reference_db;

	public function __construct($User,$Core)
	{
		$this->Core = $Core;
		$this->User = $User;

		$this->references_json = json_decode(file_get_contents(JSON_DIR."reference.json"),true);
		$this->reference_database_json = json_decode(file_get_contents(JSON_DIR."reference_database.json"),true);
		$this->translation_json = json_decode(file_get_contents(JSON_DIR."translation.json"),true);
		$this->lang = array_key_exists('lang',$this->User->user_preferences) ? $this->User->user_preferences['lang'] : $this->User::DEFAULT_LANG;

		if($this->isset_request() === true)
		{
			$this->process_requests();
			$this->register_reference();
			$this->request_permission();
		}
	}
	public function isset_request()
	{
		$valid = false;
		if(isset($_REQUEST['reference']))
		{
			$this->reference_url = $_REQUEST['reference'];
			$valid = true;
		}
		elseif(isset($_SERVER['HTTP_REFERER']))
		{
			parse_str(parse_url($_SERVER['HTTP_REFERER'],PHP_URL_QUERY),$tpm_url);
			if(array_key_exists('reference',$tpm_url))
			{
				$this->reference_url = $tpm_url['reference'];
				$valid = true;
			}
		}
		return $valid;
	}
	public function process_requests()
	{
		$this->reference_url_parts = explode('/',$this->reference_url);
		if(count($this->reference_url_parts) < $this::MAX_URL_DEPTH)
		{
			$this->redirect_reference('misc','404');
		}
		else
		{
			$this->reference_module = $this->reference_url_parts[0];
			$this->reference_component = $this->reference_url_parts[1];
		}

		if(isset($_REQUEST['p_name']))
			$this->plugin_name = $_REQUEST['p_name'];
		if(isset($_REQUEST['rowid']))
			$this->req_rowid = filter_var($_REQUEST['rowid'],FILTER_VALIDATE_INT);
		if(isset($_REQUEST['row_data']))
			$this->req_data = json_decode($_REQUEST['row_data'],true);
	}
	public function register_reference()
	{
		if(!isset($this->reference_url))
			return;

		if(is_dir(TEMPLATES_DIR.$this->reference_url) === true)
		{
			if(array_key_exists($this->reference_module,$this->references_json))
			{
				$module = $this->references_json[$this->reference_module];
				if(!in_array($this->reference_component,$module))
				{
					$this->Core->update_references($this->reference_module,$this->reference_component);
				}
			}
			else
			{
				$this->Core->update_references($this->reference_module,$this->reference_component);
			}
		}
	}
	public function request_permission()
	{
		if($this->User->user_group_name !== 'adm' && $this->reference_component !== '404')
		{
			$this->reference_permitted = $this->User->get_permissions();
			$permitted = false;
			foreach ($this->reference_permitted as $item)
			{
				if($this->reference_module === $item['module'] && $this->reference_component === $item['component'])
				{
					$permitted = true;
					break;
				}
			}
			if($permitted === false)
			{
				//$this->redirect_reference('misc','404');
			}
		}
	}
	public function redirect_reference($module, $component)
	{
		$location = WWW_ROOT.'?'.http_build_query(array('reference' => $module.'/'.$component));
		header('Location: '.urldecode($location),true, 301);
	}
	public function lang($var,$lang = null)
	{
		$lang ?? $lang = $this->lang;
		$json_iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->translation_json[$lang]),RecursiveIteratorIterator::SELF_FIRST);
		foreach ($json_iterator as $key => $value) {
			if(!is_array($value))
			{
				if($key == $var)
					return $value;
			}
		}
	}
	public function get_meta($utf8 = true,$ie_edge = true,$no_search = true,$device_viewport = true)
	{
		if($utf8 == true)
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n";
		if($ie_edge == true)
			echo "<meta http-equiv='X-UA-Compatible' content='IE=EmulateIE11'/>\n";
		if($no_search == true)
			echo "<meta name='robots' content='noindex, nofollow'/>\n<meta name='robots' content='noarchive'/>\n";
		if($device_viewport == true)
			echo "<meta name='viewport' content='width=5000, initial-scale=2, user-scalable=1, minimum-scale=0.1,maximum-scale=2.0'>";
	}
	public function get_files($local_js = false,$local_css = false)
	{
		include_once TEMPLATES_CORE_DIR.'css_tmpl.php';
		include_once TEMPLATES_CORE_DIR.'js_tmpl.php';
		if($local_js == true)
			include_once('script.php');
		if($local_css == true)
			include_once('style.css');
		if($this->User->user_name !== 'new' && v($this->reference_url) && empty($this->plugin_name) && strpos($_SERVER['REQUEST_URI'],'file_tree') === false)
			$this->load_reference();
	}
	public function scan_reference_folder()
	{
		if(@is_dir(TEMPLATES_DIR.$this->reference_url))
			return true;
		else
			return false;
	}
	public function get_reference_css($file_name = 'style.css')
	{
		$path = '/'.TEPLMATES_DIR_NAME.'/'.$this->reference_url.'/'.$file_name;
		if(file_exists(CRM_ROOT.$path))
			echo '<link href="'.$path.'" rel="stylesheet" type="text/css">';
	}
	public function get_plugin_css($file_name = 'tpl_style.css')
	{
		$path = '/'.TEPLMATES_DIR_NAME.'/'.$this->reference_url.'/'.'plugins'.'/'.$this->plugin_name.'/'.$file_name;
		if(file_exists(CRM_ROOT.$path))
			echo '<link href="'.$path.'" rel="stylesheet" type="text/css">';
	}
	public function get_reference_script($file_name = 'script.php')
	{
		$path = TEMPLATES_DIR.$this->reference_url.DS.$file_name;
		if(file_exists($path))
			include_once $path;
	}
	public function get_reference_html($file_name = 'index.php')
	{
		$path = TEMPLATES_DIR.$this->reference_url.DS.$file_name;
		if(file_exists($path))
			include_once $path;
	}
	public function get_reference_database()
	{
		if(count($this->reference_url_parts) === 0)
			return;

		$plugin = count($this->reference_url_parts) > 2 ? true : false;
		$json_iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->reference_database_json),RecursiveIteratorIterator::SELF_FIRST);
		foreach ($json_iterator as $key => $value)
		{
			if($plugin)
			{
				if(is_array($value))
				{
					if($key === 'plugins' && isset($value[$this->reference_url_parts[2]]))
						$return_db = $value[$this->reference_url_parts[2]];
					elseif($key === $this->reference_url_parts[1] && isset($value['db_name']))
						$return_db = $value['db_name'];
					elseif($key === $this->reference_url_parts[0] && isset($value['db_name']))
						$return_db = $value['db_name'];
				}
			}
			else
			{
				if(is_array($value))
				{
					if($key === $this->reference_url_parts[1] && isset($value['db_name']))
					{
						$return_db = $value['db_name'];
					}
					elseif($key === $this->reference_url_parts[0] && isset($value['db_name']))
					{
						$return_db = $value['db_name'];
					}
				}
			}
		}
		return $return_db;
	}
	public function load_reference()
	{
		if($this->scan_reference_folder() === false)
			$this->current_url = 'misc/underconstruction';
		$this->reference_db = $this->get_reference_database();
		$this->init_tmpl_connection();
		$this->get_reference_css();
		$this->get_reference_script();
	}
	public function render_plugins_tmpl()
	{
		array_push($this->reference_url_parts,$this->plugin_name);
		$this->reference_db = $this->get_reference_database();
		$this->init_tmpl_connection();
		$this->get_reference_html(TEMPLATE_PLUGINS.$this->plugin_name."/tpl.php");
	}
	public function init_tmpl_connection()
	{
		if(empty($this->reference_db))
			$this->reference_db = $this->get_reference_database();
		if(!is_null($this->reference_db))
			$this->Core->con_database($this->reference_db);
	}
	public function render_tmpl($path)
	{
		include_once $path;
	}
}
?>
