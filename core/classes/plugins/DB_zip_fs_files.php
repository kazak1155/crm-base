<?php
namespace Plugins;

class DB_zip_fs_files
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		/* TODO broken */
		if(isset($source['qry']))
		{
			$zip = new ZipArchive();
			$zip_name = time().".zip";
			if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
				die('Something went wrong');

			$data = json_decode($source['qry']);
			if(array_key_exists('qw',$source))
				$ex_data = json_decode($source['qw']);
			if(property_exists($data,'find'))
			{
				$q = "SELECT ".$data->realIdCol." FROM ".$data->tname;
				if(isset($ex_data) && property_exists($ex_data,'filters'))
				{
					$qw = 'WHERE';
					foreach ($ex_data->filters as $key => $rule) {
						$qw .= ' '.$this->processFilters_v2(json_decode(json_encode($rule), true));
						if(($key + 1) != count($ex_data->filters))
							$qw .= ' AND ';
					}
				}
				$search = $this->catchPDO("SELECT ".$data->realIdCol." FROM ".$data->tname." ".$qw);
				while($row = $search->fetch(PDO::FETCH_NUM))
				{
					$id = $row[0];
					$url = iconv('utf-8','cp1251','\\\\sql01\\MSSQLSERVER\\files\\Files\\'.$this->current_db_name.'\\'.$data->tname.'\\'.$id.'\\');
					if(is_dir($url))
					{
						$listing = scandir($url);
						for($i = 2;$i < count($listing); $i++)
						{
							if(isset($ex_data) && property_exists($ex_data,'file_filters'))
							{
								switch ($ex_data->file_filters->op)
								{
									case 'eq':
										$cur_fn = iconv('cp1251','utf-8',$listing[$i]);
										if($cur_fn === $ex_data->file_filters->data)
											$zip->addFile($url.$listing[$i],$cur_fn);
									break;
									case 'ne':
										$cur_fn = iconv('cp1251','utf-8',$listing[$i]);
										if($cur_fn !== $ex_data->file_filters->data)
											$zip->addFile($url.$listing[$i],$cur_fn);
									break;
									case 'cn':
										$cur_fn = iconv('cp1251','utf-8',$listing[$i]);
										if(strpos($cur_fn,$ex_data->file_filters->data) !== false)
											$zip->addFile($url.$listing[$i],$cur_fn);
									break;
								}
							}
							else
							{
								if(is_dir($url.$listing[$i].'\\'))
								{
									$zip->addEmptyDir(iconv('cp1251','utf-8',$listing[$i]));
									$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($url.$listing[$i]), RecursiveIteratorIterator::SELF_FIRST);
									foreach ($files as $file)
									{
										if( in_array(substr($file, strrpos($file, '\\')+1), array('.', '..')) )
											continue;
										if (is_dir($file) === true)
											$zip->addEmptyDir($listing[$i]);
										else if (is_file($file) === true)
										{
											$str1 = $listing[$i].'/'.str_replace($url.$listing[$i].'\\','',$file);
											$str1 = iconv('cp1251','utf-8',$str1);
											$zip->addFromString($str1, file_get_contents($file));
										}
									}
								}
								else
									$zip->addFile($url.$listing[$i],iconv('cp1251','utf-8',$listing[$i]));
							}
						}
					}
				}
			}
			else
			{
				$id = $data->qVal;
				$url = iconv('utf-8','cp1251','\\\\sql01\\MSSQLSERVER\\files\\Files\\'.$this->current_db_name.'\\'.$data->tname.'\\'.$id.'\\');
				if(is_dir($url))
				{
					$listing = scandir($url);
					for($i = 2;$i < count($listing); $i++)
					{
						if(is_dir($url.$listing[$i].'\\'))
						{
							$zip->addEmptyDir(iconv('cp1251','utf-8',$listing[$i]));
							$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($url.$listing[$i]), RecursiveIteratorIterator::SELF_FIRST);
							foreach ($files as $file)
							{
								if( in_array(substr($file, strrpos($file, '\\')+1), array('.', '..')) )
									continue;
								if (is_dir($file) === true)
									$zip->addEmptyDir($listing[$i]);
								else if (is_file($file) === true)
								{
									$str1 = $listing[$i].'/'.str_replace($url.$listing[$i].'\\','',$file);
									$str1 = iconv('cp1251','utf-8',$str1);
									$zip->addFromString($str1, file_get_contents($file));
								}
							}
						}
						else
							$zip->addFile($url.$listing[$i],iconv('cp1251','utf-8',$listing[$i]));
					}
				}
			}
			$zip->close();
			if(!file_exists($zip_name))
			{
				$zip = new ZipArchive();
				$zip_name = time().".zip";
				if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
					die('Something went wrong');
				$zip->addFromString('Файлов не найдено.txt', '');
				$zip->close();
			}
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="'.$zip_name.'"');
			readfile($zip_name);
			unlink($zip_name);
		}
	}
}
?>
