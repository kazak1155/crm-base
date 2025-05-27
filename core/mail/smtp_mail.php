<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

class smtp_mail
{
	private $user_email;
	private $user_password;

	private $smtp_connection;

	private $smtp_request;

	private $message;

	private $smtp_mail;
	private $smtp_responce;

	private $attached_files = array();
	
	public function __construct()
	{
		
		$this->user_email = filter_var($_REQUEST['mail_from'], FILTER_SANITIZE_EMAIL);
		
		//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2ggg_crm.txt", "w");fputs($des, 'ss '.$_REQUEST['mail_from']."    ".$_REQUEST['mail_pass']); fclose($des);
		
		if(isset($_REQUEST['mail_pass']))
			$this->user_password = filter_var($_REQUEST['mail_pass'], FILTER_SANITIZE_STRING);
		else
		{
				$this->user_password = $_SERVER['AUTH_PASSWORD'];
		}
		if(method_exists($this,$_REQUEST['mail_action']))
			$this->{$_REQUEST['mail_action']}();
		
		//$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2ggg_crm.txt", "w");fputs($des,"3 ".$_REQUEST['mail_id']); fclose($des);	
		
		
		if(isset($_REQUEST['mail_id']))
			$this->mail_action_after_send();
	}

	private function connect()
	{
		global $Core;
		$this->smtp_mail = new PHPMailer(true);		
		//$this->smtp_mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
		$this->smtp_mail->isSMTP();                                            // Send using SMTP
		$this->smtp_mail->Host       = $Core->server_prm['SMTPServer'];                    // Set the SMTP server to send through
		$this->smtp_mail->SMTPAuth   = $Core->server_prm['smtpauthenticate'];                                  // Enable SMTP authentication
		$this->smtp_mail->Username   = $this->user_email;                    // SMTP username
		$this->smtp_mail->Password   = $this->user_password;                              // SMTP password
		switch ($Core->server_prm['SMTPUseSSL']) {
		case 0: 
			$this->smtp_mail->SMTPSecure = false;
			$this->smtp_mail->SMTPAutoTLS = false;			
			break;
		default:
			$this->smtp_mail->SMTPSecure = 'ssl';
			break;
		}
		$this->smtp_mail->Port		= $Core->server_prm['SMTPServerPort'];                                    // TCP port to connect to
		$this->smtp_mail->CharSet 	= $Core->server_prm['Charset'];		
		
		$this->smtp_mail->isHTML(false);                                  // Set email format to HTML
		$this->smtp_mail->Subject = filter_var($_REQUEST["mail_theme"], FILTER_SANITIZE_STRING);
		$this->smtp_mail->Body    = filter_var($_POST["mail_text"], FILTER_SANITIZE_STRING);
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';		
	}

	private function add_attachments()
	{
		global $Core;			
		if(strlen($_FILES['mail_files']['name'][0]) > 0)
		{
			foreach ($_FILES['mail_files']['name'] as $key => $file_name)
		
			{
				$this->smtp_mail->addAttachment($_FILES['mail_files']['tmp_name'][$key]);  
			}
		}
	}

	private function add_fs_attachments()
	{
		global $Core;
		switch ($_REQUEST['mail_id'])
		{
			case 55:
				$Core->query = "SELECT files.* FROM srv.dbo.files files".QUERY_SPACE;
				$Core->query .= "INNER JOIN srv.dbo.files files_folder ON files_folder.name = 'ft_ex_docs' AND files.parent_path_locator = files_folder.path_locator".QUERY_SPACE;
				$Core->query .= "INNER JOIN srv.dbo.files files_rowid ON files_folder.parent_path_locator = files_rowid.path_locator".QUERY_SPACE;
				$Core->query .= "INNER JOIN tig50_view.dbo.Форма_заказы З ON files_rowid.name = cast(З.Код as nvarchar(max))".QUERY_SPACE;
				$Core->query .= "WHERE files.is_directory = 0 AND З.Удалено = 0 AND З.Рейсы_код = ?";
				$Core->query_params = array($_REQUEST['rowid']);
				$rows = $Core->PDO(array('global_db'=>true));
				$i = 0;

				while($file = $rows->fetch())
				{
					if(!file_exists($file['name']))
						file_put_contents($file['name'],$file['file_stream']);

					$this->smtp_mail->addAttachment($file['name']); 
					/*
					$attachment = new FileAttachmentType();
					$this->attached_files[] = $file['name'];
					$finfo_resource = finfo_open(FILEINFO_MIME_TYPE);
					$attachment->Content = file_get_contents($file['name']);
					$attachment->Name = ++$i.'_fattura.'.$file['file_type'];
					$attachment->ContentType = finfo_file($finfo_resource, $file['name']);
					finfo_close($finfo_resource);
					$this->message->Attachments->FileAttachment[] = clone($attachment);
					*/
				}
				break;
			/*
			case 56:
				$attachment = new FileAttachmentType();
				$Core->query = "SELECT file_stream,name FROM files INNER JOIN tig50_view.dbo.Форма_заказы З ON".QUERY_SPACE;
				$Core->query .= "file_stream.GetFileNamespacePath(0) LIKE '%\\'+ cast(З.Код as nvarchar(100)) +'\\%'".QUERY_SPACE;
				$Core->query .= "WHERE З.Клиенты_Код = 856 AND З.Удалено = 0 AND is_directory = 0 AND З.Рейсы_код = ?";
				$Core->query_params = array($_REQUEST['rowid']);
				$rows = $Core->PDO(array('global_db'=>true));
				$i = 0;
				while($file = $rows->fetch())
				{
					if(!file_exists($file['name']))
						file_put_contents($file['name'],$file['file_stream']);

					$attachment = new FileAttachmentType();
					$this->attached_files[] = $file['name'];
					$finfo_resource = finfo_open(FILEINFO_MIME_TYPE);
					$attachment->Content = file_get_contents($file['name']);
					$attachment->Name = ++$i.'_fattura.'.$file['file_type'];
					$attachment->ContentType = finfo_file($finfo_resource, $file['name']);
					finfo_close($finfo_resource);
					$this->message->Attachments->FileAttachment[] = clone($attachment);
				}
				break;
			*/
			default:
				break;
		}
	}
	
	private function add_message_recipients()
	{
		global $Core;
		if(strpos($_REQUEST['mail_to'], ',') !== false )
			$_REQUEST['mail_to'] = explode(',', $_REQUEST['mail_to']);
		elseif(strpos($_REQUEST['mail_to'], ';') !== false )
			$_REQUEST['mail_to'] = explode(';', $_REQUEST['mail_to']);
		if(is_array($_REQUEST['mail_to']))
		{
			foreach ($_REQUEST['mail_to'] as $i => $value)
			{
				$this->smtp_mail->addAddress(filter_var($value, FILTER_SANITIZE_EMAIL));
			}
		}
		else
		{
			$this->smtp_mail->addAddress(filter_var($_REQUEST["mail_to"], FILTER_SANITIZE_EMAIL));
		}
	}	
	
	private function output()
	{
		if(!$this->smtp_mail->send()) 
			//$err = json_encode($this->smtp_mail->ErrorInfo);
			$err = $this->smtp_mail->ErrorInfo;
		else
			//$err = json_encode('Оправлено');	
			$err = 'Оправлено';
		
		echo $err;
	}	
	
	private function add_messege_sender()
	{
		global $Core;
		$lang = filter_var($_REQUEST["lang"], FILTER_SANITIZE_STRING);
		if ($lang === null)
		{
			$lang = 'RU';
		}
		$this->smtp_mail->setFrom($this->user_email,$Core->server_prm['sendusername_'.$lang]);		
		$this->smtp_mail->addReplyTo($this->user_email,$Core->server_prm['sendusername_'.$lang]);		
	}

	public function send()
	{
		global $Core;
		$this->connect();
		$this->add_messege_sender();
		$this->add_message_recipients();
		$this->add_attachments();
		$this->add_fs_attachments();
		$this->output();
	}
	
	public function mail_action_after_send()
	{
		global $Core;
	
		switch ($_REQUEST['mail_id']) {
			case 38:
			case 70:
				$Core->query="UPDATE tig50.dbo.Заказы SET Статус_Код = 20 WHERE Код = ?";
				$Core->query_params = array($_REQUEST['rowid']);
				$Core->PDO();
				break;
			default:
				break;
		}
		
	}
	
}

?>