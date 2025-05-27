<?php
class File_stream extends Core
{
	CONST file_stream_table = 'files';

	CONST file_stream_table_main_folder = 'Files';

	public $file_database;

	public $file_tablename;

	public $file_rowid;

	public $file_folder;

	public $active_path = array();

	public $working_folder_streamid;

	public $working_folder_pathlocator;

	function __construct()
	{
		$this->init_prm_conn();
		$this->process_stream_request();
		$this->serialize_path();
		$this->folders();
	}
	public function process_stream_request()
	{
		global $Site;
		$Site->process_requests();
		if(isset($_REQUEST['reference']) || isset($_SERVER['HTTP_REFERER']))
			$this->file_database = $Site->get_reference_database();
		if(!isset($_REQUEST['oper']))
		{
			$this->file_tablename = filter_var($_REQUEST['tname'],FILTER_SANITIZE_STRING);
			$this->file_rowid = filter_var($_REQUEST['rowid'],FILTER_SANITIZE_NUMBER_INT);
			$this->file_folder = filter_var($_REQUEST['folder'],FILTER_SANITIZE_STRING);
		}
		elseif(isset($_SERVER['HTTP_REFERER']))
		{
			parse_str(parse_url($_SERVER['HTTP_REFERER'],PHP_URL_QUERY),$url);
			$this->file_tablename = filter_var($url['tname'],FILTER_SANITIZE_STRING);
			$this->file_rowid = filter_var($url['rowid'],FILTER_SANITIZE_NUMBER_INT);
			$this->file_folder = filter_var($url['folder'],FILTER_SANITIZE_STRING);
		}
		elseif(isset($_REQUEST['oper']) && !isset($_SERVER['HTTP_REFERER']))
		{
			$this->file_tablename = filter_var($_REQUEST['tname'],FILTER_SANITIZE_STRING);
			$this->file_rowid = filter_var($_REQUEST['rowid'],FILTER_SANITIZE_NUMBER_INT);
			$this->file_folder = filter_var($_REQUEST['folder'],FILTER_SANITIZE_STRING);
		}
	}
	public function serialize_path()
	{
		$self_vars = get_object_vars($this);
		$this->active_path['keys'] = array();
		foreach ($self_vars as $key => $value) {
			if(strpos($key,'file') === false)
				continue;
			if(empty($value) === true)
				continue;
			$this->{$key} = $value = $this->transliterate($this->{$key});
			$this->active_path['full_path'] .= DS.$value;
			array_push($this->active_path['keys'],$key);
		}
	}
	public function path_string()
	{
		return "GetPathLocator(FileTableRootPath(:fs_name) + :path).ToString() +
				CONVERT(VARCHAR(20), CONVERT(BIGINT, SUBSTRING(CONVERT(BINARY(16), NEWID()), 1, 6))) + '.' +
				CONVERT(VARCHAR(20), CONVERT(BIGINT, SUBSTRING(CONVERT(BINARY(16), NEWID()), 7, 6))) + '.' +
				CONVERT(VARCHAR(20), CONVERT(BIGINT, SUBSTRING(CONVERT(BINARY(16), NEWID()), 13, 4))) + '/'";
	}
	public function folders()
	{

		foreach ($this->active_path['keys'] as $key => $value)
		{
			if(strpos($this->active_path['full_path'],DS.$this->{$value}) === 0)
				$path = DS.$this->{$value};
			else
			{
				$path = substr($this->active_path['full_path'], 0, strpos($this->active_path['full_path'],DS.$this->{$value}));
				$path_to_wh = $path;
				$path .= DS.$this->{$value};
			}
			$this->find_and_create($value,$path,$path_to_wh);
		}
	}
	private function find_and_create(String $wh,String $path_to,String $path_to_wh = null)
	{
		$this->query = "SELECT GetPathLocator(FileTableRootPath(:fs_name) + :path_to)";
		$this->query_params = array(
			array(':fs_name',self::file_stream_table,PDO::PARAM_STR),
			array(':path_to',$path_to,PDO::PARAM_STR)
		);
		$folder = $this->PDO(array('global_db'=>true,'strict'=>true))->fetchColumn();
		if(empty($folder))
		{
			if(substr_count($path_to,DS) > 1)
			{
				$this->query = "INSERT INTO ".self::file_stream_table." (path_locator,name,is_directory) VALUES (".$this->path_string().",:folder_name,1)";
				$this->query_params = array(
					array(':fs_name',self::file_stream_table,PDO::PARAM_STR),
					array(':path',$path_to_wh,PDO::PARAM_STR),
					array(':folder_name',$this->{$wh},PDO::PARAM_STR)
				);
				$this->PDO(array('global_db'=>true,'strict'=>true));
			}
			else
			{
				$this->query = "INSERT INTO ".self::file_stream_table." (name,is_directory) VALUES (:folder_name,1)";
				$this->query_params = array(
					array(':folder_name',$this->{$wh},PDO::PARAM_STR)
				);
				$this->PDO(array('global_db'=>true,'strict'=>true));
			}
		}
	}
	public function register_working_folder()
	{
		if(count($this->active_path) === 0)
			$this->serialize_path();
		$this->query = "SELECT GetPathLocator(FileTableRootPath(:fs_name) + :path_to).ToString()";
		$this->query_params = array(
			array(':fs_name',self::file_stream_table,PDO::PARAM_STR),
			array(':path_to',$this->active_path['full_path'],PDO::PARAM_STR)
		);
		$this->working_folder_pathlocator = $this->PDO(array('global_db'=>true,'strict'=>true))->fetchColumn();
		$this->query = "SELECT stream_id FROM ".self::file_stream_table." WHERE path_locator = GetPathLocator(FileTableRootPath(:fs_name) + :path_to)";
		$this->query_params = array(
			array(':fs_name',self::file_stream_table,PDO::PARAM_STR),
			array(':path_to',$this->active_path['full_path'],PDO::PARAM_STR)
		);
		$this->working_folder_streamid = $this->PDO(array('global_db'=>true,'strict'=>true))->fetchColumn();
	}
	public function transliterate($string)
	{
		$alphas = array(
			"а" => "a","б" => "b","в" => "v","г" => "g","д" => "d","е" => "e",
			"э" => "e","ё" => "yo","ж" => "zh","з" => "z","и" => "i","й" => "j",
			"к" => "k","л" => "l","м" => "m","н" => "n","о" => "o","п" => "p","р" => "r",
			"с" => "s","т" => "t","у" => "u","ф" => "f","х" => "h","ц" => "ts","ч" => "ch",
			"ш" => "sh","щ" => "sch","ь" => "","ъ" => "","ы" => "y","ю" => "yu","я" => "ya"
		);
		$lc_string = mb_convert_case($string, MB_CASE_LOWER, "utf-8");
		return strtr($lc_string,$alphas);
	}
}
?>
