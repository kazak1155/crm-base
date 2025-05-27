<?php
class File_stream_query extends File_stream
{
	const MAX_UPLOAD_SIZE = 200;
	const ALLOWED_FILE_EXT = array(
		'.xls','.xlsx','.doc','.docx','.rtf',
		'.pdf','.msg','.txt','.csv',
		'.jpg','.jpeg','.png','.bmp','.tif'
	);

	protected $file;
	protected $file_name;
	protected $file_hex;

	protected $file_post_data;

	function __construct()
	{
		$this->init_prm_conn();
		$this->process_stream_request();
		$this->register_working_folder();
		if(isset($_REQUEST['postData']))
		{
			$this->file_post_data = json_decode($_REQUEST['postData'],true);
			$this->{$this->file_post_data['oper']}();
		}
		else
			$this->{$_REQUEST['oper']}();
	}
	public function view()
	{
		$json_data = array();
		array_push($json_data,
			array(
				"id"=>$this->working_folder_streamid,
				"parent"=>"#",
				"text"=>"",
				"icon"=>"fa fa-lg fa-folder-open-o",
				"state"=>array("opened"=>true,"selected"=>true),
				"type"=>"selected",
				"a_attr"=>array("directory"=>1,"stream_id"=>$this->working_folder_streamid,"root"=>true)
			)
		);
		$this->query = "SELECT stream_id,name,file_type,parent_path_locator.ToString() as parent_path_locator,is_directory,file_stream.GetFileNamespacePath() AS file_path,";
		$this->query .= "(SELECT stream_id FROM files WHERE path_locator = fs.parent_path_locator) as parent_stream_id".QUERY_SPACE;
		$this->query .= "FROM ".parent::file_stream_table." as fs".QUERY_SPACE;
		$this->query .= "WHERE parent_path_locator.IsDescendantOf(:path) = 1".QUERY_SPACE;
		$this->query .= "ORDER BY creation_time DESC";
		$this->query_params = array(array(':path',$this->working_folder_pathlocator,PDO::PARAM_STR));
		$files = $this->PDO(array('global_db'=>true,'strict'=>true));

		while($file = $files->fetch())
		{
			$file_href = str_replace([DS.self::file_stream_table_main_folder.DS,DS,$file['name']],['','/',''],$file['file_path']).rawurlencode($file['name']);
			array_push($json_data,array(
				"id"=>$file['stream_id'],
				"parent"=>$file['parent_stream_id'],
				"text"=>$file['name'],
				"icon"=>$this->get_icon($file['file_type']),
				"type"=>"selected",
				"a_attr"=>array(
					"directory"=>$file['is_directory'],
					"stream_id"=>$file['stream_id'],
					"type"=>$file['file_type'],
					// "javascript: w = window.open('".$_SERVER['HTTP_ORIGIN']."/filestream/$file_href');w.print();"; // print example
					"href"=>'/filestream/'.$file_href,
					"root"=>false
				)
			));
		}
		echo json_encode($json_data,JSON_UNESCAPED_UNICODE);
	}
	public function view_tlc()
	{
		$json_data = array();
		$this->query = "SELECT name".QUERY_SPACE;
		$this->query .= "FROM ".parent::file_stream_table.QUERY_SPACE;
		$this->query .= "WHERE parent_path_locator.IsDescendantOf(:path) = 1 AND file_type NOT IN ('pdf','xls') AND is_directory = 0".QUERY_SPACE;
		$this->query .= "ORDER BY creation_time DESC";
		$this->query_params = array(array(':path',$this->working_folder_pathlocator,PDO::PARAM_STR));
		$files = $this->PDO(array('global_db'=>true,'strict'=>true));
		while($file = $files->fetch()) {
			array_push($json_data,$file['name']);
		}
		echo json_encode($json_data,JSON_UNESCAPED_UNICODE);
	}
	public function rename()
	{
		$_REQUEST['name'] = $this->transliterate($_REQUEST['name']);
		$this->query = "UPDATE ".parent::file_stream_table." SET name = :name WHERE stream_id = :stream_id";
		$this->query_params = array(
			array(':name',$_REQUEST['name'],PDO::PARAM_STR),
			array(':stream_id',$_REQUEST['stream_id'],PDO::PARAM_STR)
		);
		$this->PDO(array('global_db'=>true,'strict'=>true));
	}
	public function new_path()
	{
		return ":parent_path + CONVERT(VARCHAR(20), CONVERT(BIGINT, SUBSTRING(CONVERT(BINARY(16), NEWID()), 1,
					6))) + '.' + CONVERT(VARCHAR(20), CONVERT(BIGINT, SUBSTRING(CONVERT(BINARY(16),
					NEWID()), 7, 6))) + '.' + CONVERT(VARCHAR(20), CONVERT(BIGINT, SUBSTRING(CONVERT
					(BINARY(16), NEWID()), 13, 4))) + '/'";
	}
	public function add_folder()
	{
		$_REQUEST['name'] = $this->transliterate($_REQUEST['name']);
		$this->query = "SELECT path_locator.ToString() FROM ".self::file_stream_table." WHERE stream_id = :stream_id";
		$this->query_params = array(array(':stream_id',$_REQUEST['stream_id'],PDO::PARAM_STR));
		$parent_path = $this->PDO(array('global_db'=>true,'strict'=>true))->fetchColumn();
		$this->query = "DECLARE @tmp table(stream_id uniqueidentifier);";
		$this->query .= "INSERT INTO ".self::file_stream_table." (path_locator,name,is_directory) OUTPUT inserted.stream_id INTO @tmp VALUES (".$this->new_path().",:name,1);";
		$this->query .= "SELECT stream_id from @tmp;";
		$this->query_params = array(
			array(':parent_path',$parent_path,PDO::PARAM_STR),
			array(':name',$_REQUEST['name'],PDO::PARAM_STR)
		);
		$r = $this->PDO(array('global_db'=>true,'strict'=>true));
		$r->nextRowset();
		echo $r->fetchColumn();
	}
	public function add_file()
	{
		if(isset($_POST['replaceFile']) && $_POST['replaceFile'] == true)
			$this->replace();
		$this->query = "SELECT path_locator.ToString() FROM ".self::file_stream_table." WHERE stream_id = :stream_id";
		$this->query_params = array(array(':stream_id',$this->file_post_data['stream_id'],PDO::PARAM_STR));
		$parent_path = $this->PDO(array('global_db'=>true,'strict'=>true))->fetchColumn();
		foreach ($_FILES as $file) {
			$this->file = $file;
			$this->validate_file();

			$file_handle = fopen($this->file['tmp_name'],"r");
			$file_size = filesize($this->file['tmp_name']);
			$file_contents = fread($file_handle,$file_size);
			fclose($file_handle);

			$this->file_name = $this->transliterate($this->file['name']);
			$this->file_hex = $this->hex($file_contents);

			$this->query = "INSERT INTO ".parent::file_stream_table." (file_stream,path_locator,name) VALUES ($this->file_hex,".$this->new_path().",".$this->PDO_quote($this->file_name).")";
			$this->query_params = array(
				array(':parent_path',$parent_path,PDO::PARAM_STR)
			);
			$this->PDO(array('global_db'=>true,'strict'=>true));
		}
	}
	public function delete()
	{
		if($_REQUEST['directory'] == true)
		{
			$this->query = "EXEC sp_delete_filetable :stream_id";
			$this->query_params = array(array(':stream_id',$_REQUEST['stream_id'],\PDO::PARAM_STR));
			$this->PDO(array('global_db'=>true,'strict'=>true));
		}
		else
		{
			$this->query = "DELETE FROM ".parent::file_stream_table." WHERE stream_id = :stream_id";
			$this->query_params = array(array(':stream_id',$_REQUEST['stream_id'],PDO::PARAM_STR));
			$this->PDO(array('global_db'=>true,'strict'=>true));
		}
	}
	public function move_node()
	{
		$node_id = $_POST['node_moved']['stream_id'];
		$node_directory = $_POST['node_moved']['directory'];
		$node_name = $_POST['node_moved']['name'];
		$node_old_parent = $_POST['node_old_parent'];
		$node_new_parent = $_POST['node_new_parent'];
		if($node_directory == true)
		{
			/* TODO add directory support */
			/*
			$inner_files = array();
			$inner_folders = array();
			$new_path = $this->catchPDO("SELECT path_locator.ToString() FROM ".self::file_stream_table." WHERE stream_id = '$node_new_parent'",$this->prm_conn) -> fetchColumn();
			$new_path_real = $new_path;
			$new_path = $this->new_path($new_path);
			$old_path = $this->catchPDO("SELECT path_locator.ToString() FROM ".self::file_stream_table." WHERE stream_id = '$node_id'",$this->prm_conn) -> fetchColumn();
			$res = $this->catchPDO("SELECT stream_id,is_directory,name FROM ".self::file_stream_table." WHERE path_locator.GetAncestor(1) = '$old_path' AND stream_id <> '$node_id'",$this->prm_conn);
			while($row = $res->fetch(PDO::FETCH_ASSOC))
			{
				if($row['is_directory'] == true)
				{
					array_push($inner_folders,['stream_id'=>$row['stream_id'],'name'=>$row['name']]);
				}
				else
					array_push($inner_files,$row['stream_id']);
			}
			if(isset($_POST['replaceFile']) && $_POST['replaceFile'] == true)
			{
				foreach ($inner_files as $stream)
				{
					$stream_id = $stream['stream_id'];
					$name = $stream['name'];
					$this->execute("DELETE FROM ".parent::file_stream_table." WHERE name = '$name' AND path_locator.ToString() = '$new_path_real'");
					$this->execute("UPDATE ".parent::file_stream_table." SET path_locator = $new_path WHERE stream_id = '$stream_id'");
				}
				$this->execute("EXEC sp_delete_filetable '$node_id'");

				if(count($inner_folders) > 0)
				{

				}
				else
				{

				}
			}
			else
			{
				if(count($inner_folders) > 0)
				{
					if(!isset($primary_node_id))
					{
						$_POST['node_new_parent'] = $this->catchPDO("DECLARE @Tmp table(id uniqueidentifier);INSERT INTO ".self::file_stream_table." (path_locator,name,is_directory) OUTPUT inserted.stream_id INTO @Tmp VALUES ($new_path,'$node_name',1)",$this->prm_conn);
						$_POST['node_new_parent'] -> nextRowset();
						$_POST['node_new_parent'] = $_POST['node_new_parent'] -> fetchColumn();
						$new_path = $this->catchPDO("SELECT path_locator.ToString() FROM ".self::file_stream_table." WHERE stream_id = '".$_POST['node_new_parent']."'",$this->prm_conn) -> fetchColumn();
						$new_path = $this->new_path($new_path);
						$primary_node_id = $node_id;
					}
					else
					{
						$new_path = $this->catchPDO("DECLARE @Tmp table(path nvarchar(max));INSERT INTO ".self::file_stream_table." (path_locator,name,is_directory) OUTPUT inserted.path_locator.ToString() INTO @Tmp VALUES VALUES ($new_path,'$node_name',1)",$this->prm_conn);
						$new_path -> nextRowset();
						$new_path = $new_path -> fetchColumn();
						$new_path = $this->new_path($new_path);
					}
					foreach ($inner_files as $stream_id)
					{
						$this->execute("INSERT INTO ".parent::file_stream_table." (file_stream,path_locator,name) SELECT file_stream,$new_path,name FROM ".parent::file_stream_table." WHERE stream_id = '$stream_id'");
					}
					foreach ($inner_folders as $folder_data)
					{
						$_POST['node_moved']['stream_id'] = $folder_data['stream_id'];
						$_POST['node_moved']['directory'] = true;
						$_POST['node_moved']['name'] = $folder_data['name'];
						$this->move_node();
					}
					$this->execute("EXEC sp_delete_filetable '$primary_node_id'");
				}
				else
				{
					$new_path = $this->catchPDO("DECLARE @Tmp table(path nvarchar(max));INSERT INTO ".self::file_stream_table." (path_locator,name,is_directory) OUTPUT inserted.path_locator.ToString() INTO @Tmp VALUES ($new_path,'$node_name',1)",$this->prm_conn);
					$new_path -> nextRowset();
					$new_path = $new_path -> fetchColumn();
					$new_path = $this->new_path($new_path);
					foreach ($inner_files as $stream_id)
					{
						$this->execute("INSERT INTO ".parent::file_stream_table." (file_stream,path_locator,name) SELECT file_stream,$new_path,name FROM ".parent::file_stream_table." WHERE stream_id = '$stream_id'");
					}
					$this->execute("EXEC sp_delete_filetable '$node_id'");
				}
			}
			*/
		}
		elseif ($node_directory == false)
		{
			if(isset($_REQUEST['replaceFile']) && $_REQUEST['replaceFile'] == true)
				$this->replace();

			$this->query = "SELECT path_locator.ToString() FROM ".self::file_stream_table." WHERE stream_id = :stream_id";
			$this->query_params = array(array(':stream_id',$_REQUEST['node_new_parent'],PDO::PARAM_STR));
			$parent_path = $this->PDO(array('global_db'=>true,'strict'=>true))->fetchColumn();

			$this->query = "UPDATE ".parent::file_stream_table." SET path_locator = ".$this->new_path()." WHERE stream_id = :stream_id";
			$this->query_params = array(
				array(':parent_path',$parent_path,PDO::PARAM_STR),
				array(':stream_id',$_REQUEST['node_moved']['stream_id'],PDO::PARAM_STR)

			);
			$this->PDO(array('global_db'=>true,'strict'=>true));
		}
	}
	public function replace()
	{
		if(is_array($_REQUEST['replaceId']) === true)
		{
			foreach ($_REQUEST['replaceId'] as $stream_id)
			{
				$this->query = "DELETE FROM ".self::file_stream_table." WHERE stream_id = :stream_id";
				$this->query_params = array(array(':stream_id',$stream_id,PDO::PARAM_STR));
				$this->PDO(array('global_db'=>true,'strict'=>true));
			}
		}
		else
		{
			$this->query = "DELETE FROM ".self::file_stream_table." WHERE stream_id = :stream_id";
			$this->query_params = array(array(':stream_id',$_REQUEST['replaceId'],PDO::PARAM_STR));
			$this->PDO(array('global_db'=>true,'strict'=>true));
		}
	}
	public function hex($string)
	{
		$string = unpack('H*hex', $string);
		$string = '0x'.$string['hex'];
		return $string;
	}
	public function unhex($string)
	{
		$string = pack('H*',$string);
		return $string;
	}
	public function get_icon($f_type)
	{
		$fa = 'fa fa-lg ';
		if(empty($f_type))
			$fa .= 'fa-folder-o';
		else
		{
			$f_type = mb_strtolower($f_type);
			switch($f_type)
			{
				case 'doc':
				case 'docx':
				case 'rtf':
					$fa .= 'fa-file-word-o';
				break;
				case 'xls':
				case 'xlsx':
				case 'csv':
					$fa .= 'fa-file-excel-o';
				break;
				case 'pdf':
					$fa .= 'fa-file-pdf-o';
				break;
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'bmp':
				case 'tif':
					$fa .= 'fa-file-image-o';
				break;
				case 'txt':
					$fa .= 'fa-file-text-o';
				break;
				case 'msg':
					$fa .= 'fa-envelope-o';
				break;
				default:
					$fa .= 'fa-question';
				break;
				return $fa;
			}
		}
		return $fa;
	}
	private function validate_file()
	{
		if(filesize($this->file['tmp_name']) / 1048576 > self::MAX_UPLOAD_SIZE)
		{
			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: application/json; charset=UTF-8');
			exit(json_encode(array('message' => 'Maximum upload size is 200M', 'code' => 1337)));
		}
		preg_match_all('/\.(\w+)$/',$this->file['name'],$f_name);
		if(!in_array(mb_strtolower($f_name[0][0]),self::ALLOWED_FILE_EXT))
		{
			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: application/json; charset=UTF-8');
			exit(json_encode(array('message' => 'Non allowed file extension', 'code' => 1337)));
		}
	}
}
?>
