<?php
$Site->init_tmpl_connection();
if(isset($_REQUEST['mail_action']))
{
	$sendmailtype = $Core->server_prm['sendmailtype'];
	switch ($sendmailtype){
		case 'exchange':
			new Send_mail();		
			break;
		case 'smtp':
			new smtp_mail();
			break;
		default:
			break;
	}
	exit();
}
$mail_id = $Site->req_data['mail_id'];
$mail_init = array();

$Core->query = "SELECT Код,Тема,Текст,Язык FROM Письма_Шаблоны WHERE Код = :mail_id";
$Core->query_params = array($mail_id);
$row = $Core->PDO(array('global_db'=>true))->fetch();
$mail_init['text'] = $row['Текст'];
$mail_init['theme'] = $row['Тема'];
$mail_init['lang'] = $row['Язык'];


/*
if(isset($Site->req_data['formatted_data']['Фабрики_Код']))
{
	$Core->query = "SELECT Email FROM Контрагенты_контакты WHERE Контрагенты_Код = ?";
	$Core->query_params = array($Site->req_data['formatted_data']['Фабрики_Код']);
	$mail_init['to'] = $Core->PDO()->fetchColumn();
	if(empty($mail_init['to']))
		$mail_init['to'] = 'Почта не указана';
	else
		$mail_init['to'] = str_replace(';',',',$mail_init['to']);
}
else
{
	if(isset($Site->req_data['mail_to']))
		$mail_init['to'] = $Site->req_data['mail_to'];
	else
		$mail_init['to'] = 'Почта не указана';
}

*/

switch ($mail_id)
{
	case 38:
	case 70:
		$Core->query = "SELECT Email FROM Контрагенты_контакты WHERE Контрагенты_Код = ?";
		$Core->query_params = array($Site->req_data['formatted_data']['Фабрики_Код']);
		$mail_init['to'] = $Core->PDO()->fetchColumn();
		if(empty($mail_init['to']))
			$mail_init['to'] = 'Почта не указана';
		else
			$mail_init['to'] = str_replace(';',',',$mail_init['to']);
		break;
	case 55:
		$mail_init['to'] = '';
		$Core->query = "SELECT files.* FROM srv.dbo.files files".QUERY_SPACE;
		$Core->query .= "INNER JOIN srv.dbo.files files_folder ON files_folder.name = 'ft_ex_docs' AND files.parent_path_locator = files_folder.path_locator".QUERY_SPACE;
		$Core->query .= "INNER JOIN srv.dbo.files files_rowid ON files_folder.parent_path_locator = files_rowid.path_locator".QUERY_SPACE;
		$Core->query .= "INNER JOIN dbo.Форма_заказы З ON files_rowid.name = cast(З.Код as nvarchar(max))".QUERY_SPACE;
		$Core->query .= "WHERE files.is_directory = 0 AND З.Удалено = 0 AND З.Рейсы_код = ?";
		$Core->query_params = array($_REQUEST['rowid']);
		$rows = $Core->PDO();
		$i = 0;
		while($row = $rows->fetch())
			$mail_init['text'] .= ++$i.". ".filter_var($row['name'],FILTER_SANITIZE_STRING)." \n\n";		
			//$mail_init['text'] .= ++$i.". ".$i."_".filter_var($row['name'],FILTER_SANITIZE_STRING)." \n\n";	
			//$mail_init['text'] .= ++$i.". ".$i."_fattura.".$row['file_type']." \n\n";
		break;
	case 56:
		$mail_init['to'] = 'info@ankel.it';
		$Core->query = "SELECT name FROM srv.dbo.files INNER JOIN Форма_заказы З ON".QUERY_SPACE;
		$Core->query .= "file_stream.GetFileNamespacePath(0) LIKE '%\\'+ cast(З.Код as nvarchar(100)) +'\\%'".QUERY_SPACE;
		$Core->query .= "WHERE З.Клиенты_Код = 856 AND З.Удалено = 0 AND is_directory = 0 AND З.Рейсы_код = ? ";
		$Core->query_params = array($_REQUEST['rowid']);
		$rows = $Core->PDO();
		$i = 0;
		while($row = $rows->fetch())
			$mail_init['text'] .= ++$i.". ".$row['name']."\n\n";
		break;
	case 57:
	case 77:
	case 78:
		$Core->query = "SELECT Email FROM Клиенты К INNER JOIN Контрагенты_Контакты Кк ON Кк.Контрагенты_Код = К.Код WHERE К.Код = ?";
		$Core->query_params = array($Site->req_data['formatted_data']['Клиенты_Код']);
		$mail_init['to'] = $Core->PDO()->fetchColumn();
		break;
	case 58:
		$Core->query = "SELECT Email FROM Фабрики Ф INNER JOIN Контрагенты_Контакты Кк ON Кк.Контрагенты_Код = Ф.Код WHERE Ф.Код = ?";
		$Core->query_params = array($Site->req_data['formatted_data']['Клиенты_Код']);
		$mail_init['to'] = $Core->PDO()->fetchColumn();
		break;
	default:
		break;
}
//print_pre($Site->req_data);

$init_strings = array();
$replace_strings = array();
foreach ($mail_init as $key => $value)
{
	//preg_match_all('/\%.*\%/', $value,$matches,PREG_OFFSET_CAPTURE);
	preg_match_all('/%[^%\s]*%/', $value,$matches,PREG_OFFSET_CAPTURE);
	if(count($matches[0]) > 0)
	{
		foreach ($matches[0] as $key_ => $value_)
		{
			$match_len = strlen($value_[0]);
			$match_trim = substr($value_[0], 1, -1);
			//print_pre($value_[0]).'<br>';
			//foreach ($Site->req_data['formatted_data'] as $key__ => $values__)
			foreach ($Site->req_data as $key__ => $values__)
			{
				if($key__ == $match_trim)
				{
					array_push($init_strings,$value_[0]);
					array_push($replace_strings,$values__);
					break;
				}
			}
		}
		$mail_init[$key] = str_replace($init_strings,$replace_strings,$mail_init[$key]);
	}
}
?>
