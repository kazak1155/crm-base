<?php
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use \jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\MessageType;
use \jamesiarmes\PhpEws\Type\FileAttachmentType;
use \jamesiarmes\PhpEws\Type\SingleRecipientType;
use \jamesiarmes\PhpEws\Type\ReplyToItemType;

class Send_mail
{
	const VERSION = 'Exchange2013_SP1';
	const VIA_PWD = 'sarocenoudine';

	private $user_email;
	private $user_password;

	private $ews_connection;

	private $ews_request;

	private $message;

	private $ews_responce;

	private $attached_files = array();

	public function __construct()
	{
		$this->user_email = filter_var($_REQUEST['mail_from'], FILTER_SANITIZE_EMAIL);
		if(isset($_REQUEST['mail_pass']))
			$this->user_password = filter_var($_REQUEST['mail_pass'], FILTER_SANITIZE_STRING);
		else
		{
			if($this->user_email === 'via@tlc-online.ru')
				$this->user_password = self::VIA_PWD;
			else
				$this->user_password = $_SERVER['AUTH_PASSWORD'];
		}
		if(method_exists($this,$_REQUEST['mail_action']))
			$this->{$_REQUEST['mail_action']}();
		if(isset($_REQUEST['mail_id']))
			$this->mail_action_after_send();
	}
	private function connect()
	{
		global $Core;
		$this->ews_connection = new Client($Core->server_prm['SMTPServer']);
		$this->ews_connection->setVersion(self::VERSION);
		$this->ews_connection->setUsername(filter_var($this->user_email, FILTER_SANITIZE_EMAIL));
		$this->ews_connection->setPassword($this->user_password);
	}
	private function create_request()
	{
		$this->ews_request = new CreateItemType();
		$this->ews_request->Items = new NonEmptyArrayOfAllItemsType();
		$this->ews_request->MessageDisposition = MessageDispositionType::SEND_AND_SAVE_COPY;
	}
	private function add_messege_sender()
	{
		$this->message->From = new SingleRecipientType();
		$this->message->From->Mailbox = new EmailAddressType();
		$this->message->From->Mailbox->EmailAddress = $this->user_email;

		$this->message->ReplyTo = new ArrayOfRecipientsType();
		$replyto = new EmailAddressType();
		$replyto->EmailAddress = $this->user_email;
		$this->message->ReplyTo->Mailbox[] = $replyto;
	}
	private function add_message_recipients()
	{
		$recipient = new EmailAddressType();
		if(strpos($_REQUEST['mail_to'], ',') !== false )
			$_REQUEST['mail_to'] = explode(',', $_REQUEST['mail_to']);
		elseif(strpos($_REQUEST['mail_to'], ';') !== false )
			$_REQUEST['mail_to'] = explode(';', $_REQUEST['mail_to']);
		if(is_array($_REQUEST['mail_to']))
		{
			foreach ($_REQUEST['mail_to'] as $i => $value)
			{
				$recipient->EmailAddress = filter_var($value, FILTER_SANITIZE_EMAIL);
				$this->message->ToRecipients->Mailbox[$i] = clone($recipient);
			}
		}
		else
		{
			$recipient->EmailAddress = filter_var($_REQUEST["mail_to"], FILTER_SANITIZE_EMAIL);
			$this->message->ToRecipients->Mailbox[] = $recipient;
		}
	}
	private function add_attachments()
	{
		$attachment = new FileAttachmentType();
		foreach ($_FILES['mail_files']['name'] as $key => $file_name)
		{
			$file = new SplFileObject($_FILES['mail_files']['tmp_name'][$key]);
			$this->attached_files[] = $file_name;
			$finfo_resource = finfo_open();
			$attachment->Content = file_get_contents($_FILES['mail_files']['tmp_name'][$key]);
			$attachment->Name = $file_name;
			$attachment->ContentType = finfo_file($finfo_resource, $_FILES['mail_files']['tmp_name'][$key]);
			finfo_close($finfo_resource);
			$this->message->Attachments->FileAttachment[] = clone($attachment);
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
			default:
				break;
		}
	}
	
	private function unlink_attached_files()
	{
		foreach ($this->attached_files as $key => $value) {
			if(file_exists($value))
				unlink($value);
		}
	}
	
	private function add_message_to_request()
	{
		$this->ews_request->Items->Message[] = $this->message;
	}
	
	private function output()
	{
		$response_messages = $this->ews_responce->ResponseMessages->CreateItemResponseMessage;
		foreach ($response_messages as $response_message)
		{
			if(empty($response_message->MessageText))
				$response_message->MessageText = 'Отправлено';
			$err = json_encode($response_message);
		}
		echo $err;
	}
	
	public function send()
	{
		$this->connect();
		$this->create_request();
		$this->message = new MessageType();
		$this->message->ToRecipients = new ArrayOfRecipientsType();
		$this->message->Subject = filter_var($_REQUEST["mail_theme"], FILTER_SANITIZE_STRING);
		$this->add_messege_sender();
		$this->add_message_recipients();
		$this->message->Body = new BodyType();
		$this->message->Body->BodyType = BodyTypeType::TEXT;
		$this->message->Body->_ = filter_var($_POST["mail_text"], FILTER_SANITIZE_STRING);
		if(strlen($_FILES['mail_files']['name'][0]) > 0)
			$this->add_attachments();
		if(isset($_REQUEST['mail_id']))
			$this->add_fs_attachments();
		$this->add_message_to_request();
		$this->ews_responce = $this->ews_connection->CreateItem($this->ews_request);
		if(count($this->attached_files) > 0)
			$this->unlink_attached_files();
		$this->output();
	}
	public function mail_action_after_send()
	{
		global $Site;
		switch ($_REQUEST['mail_id']) {
			case 38:
			case 70:
				$Site->Core->query="UPDATE Заказы SET Статус_Код = 20 WHERE Код = ?";
				$Site->Core->query_params = array($_REQUEST['rowid']);
				$Site->Core->PDO();
				break;
			default:
				break;
		}
	}
}
