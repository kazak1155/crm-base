<?php
class User {
	const DEFAULT_NEW_USER = 'new';
	const DEFAULT_LANG = 'ru';
	public $user_login;
	public $user_lang;
	public $user_id;
	public $user_full_name;
	public $user_email;
	public $user_group_id;
	public $user_group_name;
	public $user_subgroups = array();
	public $user_preferences = array();
	public $Core;
	public $user_permitted_references = array('mics/404');
	public function __construct ($Core) {
		$this->Core = $Core;
		$this->init();
		if (isset($_REQUEST['newuser'])) $this->reg_new_user($_REQUEST['name'], $_REQUEST['s_name'], $_REQUEST['email']);
	}
	function getConfig(){
		//$configPath = __file__.'/config.ini';$sArrr = explode('php',$configPath);
		//$Config = parse_ini_file($sArrr[0].'/config.ini', 1); //Конфиг сайта
		$configPath = $_SERVER['DOCUMENT_ROOT'].'/config.ini';
		$Config = parse_ini_file($configPath, 1); //Конфиг сайта
		return $Config;
	}
	private function groupDat(){//ыбираем все группы пользователя
		if($_SESSION['usr']['user_id'] > 0){
			unset($_SESSION['usr']['groups'],$_SESSION['usr']['groups_exc'],$_SESSION['usr']['groups_exc']['status_id'],$tmp);//носим все старые группы в сессии
			//unset($_SESSION['usr'][0],$_SESSION['usr'][1],$_SESSION['usr'][2],$_SESSION['usr'][3],$_SESSION['usr'][4],$_SESSION['usr'][5]);
			$sql = "SELECT c.*,g.[Тип_Группы] FROM [црм_Пользователь_Группа] AS c LEFT JOIN [црм_группы] AS g on g.[Код] = c.[Группа] WHERE c.[Пользователь] = '".(int)$_SESSION['usr']['user_id']."'";
			//print $sql."<br/>";
			$this->Core->query = $sql;
			$this->Core->query_params = array($this->user_groups);
			$user_groups = $this->Core->PDO(array('global_db' => true));
			while ($subgroup = $user_groups->fetch()) {
				//print "<pre>"; print_r($subgroup); print "</pre>";
				$_SESSION['usr']['groups'][] = $subgroup['Группа'];//Записываем группы в сессию
				if($subgroup['Тип_Группы'] == 1){ //Массив исключающих групп
					$_SESSION['usr']['groups_exc']['back'][$subgroup['Группа']] = 1;//Массив, в которм ключами - исключающие группы
					//$_SESSION['usr']['groups_exc']['line'][] = $subgroup['Группа'];//Записываем группы в сессию
				}
				if($subgroup['Тип_Группы'] == 2){ //Массив делегирующих групп
					$_SESSION['usr']['groups_dlg']['back'][$subgroup['Группа']] = 1;//Массив, в которм ключами - исключающие группы
					$tmp[] = $subgroup['Группа'];//Записываем группы в сессию
				}
			}
			if(count((array)$tmp)){
				@$_SESSION['usr']['groups_dlg']['line'] = array_unique($tmp);
			}
			$sql = "SELECT * FROM [tig50].[dbo].[црм_счета_Статусы]";
			$this->Core->query = $sql;
			$ars = $this->Core->PDO(array('global_db' => true));
			while ($statLine = $ars->fetch()){
				$stLinArr = explode(',',$statLine['Скрыто_для_групп']);
				foreach($stLinArr as $k => $v){
					//print trim($v)."   ";
					if($_SESSION['usr']['groups_exc']['back'][trim($v)] == 1){
						$_SESSION['usr']['groups_exc']['status_id'][$statLine['Код']] = 1; //Массив, в котором ключами - статусы, мне невидимые
						$_SESSION['usr']['groups_exc']['statuses'][] = $statLine['Код']; //Массив, в котором значениями - статусы, мне невидимые
					}
				}

			}
		}
	}
	private function groupRights(){ //Выбираем все права пользователя
		if($_SESSION['usr']['user_id'] > 0){
			unset($_SESSION['usr']['rgt'],$rgtLst); //Сначала сносим все старые права в сессии
			//$r = print_r($_SESSION,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2.txt", "w");fputs($des, $r); fclose($des);
			if(count((array)$_SESSION['usr']['groups'])){
				$this->Core->query = "SELECT DISTINCT [Право] FROM [црм_Группа_Право] WHERE [Группа] IN (".implode(',',$_SESSION['usr']['groups']).")";
				$this->Core->query_params = array($this->user_rgts);
				$user_rgts = $this->Core->PDO(array('global_db' => true));
				while ($subrights = $user_rgts->fetch()) {//Вытаскиваем из базы права
					$_SESSION['usr']['rgt']['line'][] = $subrights['Право'];//Записываем права в сессию в прямом виде
					$rgtLst[$subrights['Право']] = 1; //Составляем альтернативный вариант листа прав, где индекс означает право а единица - то, что это право есть.
				}
			}
			$_SESSION['usr']['rgt']['bask'] = $rgtLst; //Пишем альтернативный лист прав в сессию
			$_SESSION['tempo']['prev'] = $this->getTempo();
		}
	}
	function getTempo(){
		$nowdate = date("Y") . '-' . date("m") . '-' . date("d");
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/holiday.php';
		$dC = new holidaysCount($this->Core);
		$prev1d = $dC->dcProcess(1);
		$prev2d = $dC->dcProcess(2);
		$prev3d = $dC->dcProcess(3);
		$out = array('prew0d'=>$nowdate, 'prew1d'=>$prev1d, 'prew2d'=>$prev2d, 'prew3d'=>$prev3d);
		return $out;
	}
	public function init () {
		$this->user_login = $this->Core->get_sess('user_login', 'usr');
		if (empty($this->user_login)) {
			$this->user_login = preg_replace('/' . DOMAIN_NAME . '\/|' . DOMAIN_NAME . '\\\/i', '', $_SERVER['REMOTE_USER']);
			$this->Core->query = "SELECT П.Код AS user_id,Full_name AS user_name,Группа_Код AS user_group_id,Email AS user_email,Гр.Название AS user_group_name FROM Пользователи П INNER JOIN Б_Пользователи_Группы Гр ON П.Группа_Код = Гр.Код WHERE Login = ?";
			$this->Core->query_params = array($this->user_login);
			$user_data = $this->Core->PDO(array('global_db' => true))->fetch();
			if ($user_data !== false) {
				$this->Core->set_sess('user_login', $this->user_login, 'usr');
				$this->refresh($user_data);
			} else {
				$this->Core->set_sess('user_name', self::DEFAULT_NEW_USER, 'usr');
				//$this->refresh();
			}
		} else
			$this->refresh();
		//$this->Core->set_sess('/', $this->getConfig(), 'config');
		$_SESSION['config'] = $this->getConfig(); //Кладем в сессию конфиг
		$this->groupDat(); //Кладем в сессию лист групп пользователя
		$this->groupRights(); //Кладем в сесию лист прав пользователя
	}
	public function refresh ($user_data = null) {
		if (!is_null($user_data) && is_array($user_data)) {
			foreach ($user_data as $key => $data) {
				$this->Core->set_sess($key, $data, 'usr');
				$this->{$key} = $data;
			}
			//$this->get_user_subgroups();
			//$this->get_user_preferences();
		} else {
			$user_data = $this->Core->get_sess('usr');
			if (empty($user_data)) return;
			foreach ($user_data as $key => $data) {
				if ($key != 'user_login') $this->{$key} = $data;
			}
		}
	}
	public function get_user_subgroups () {
		$this->Core->query = "SELECT Группа_Код AS subgroup_id,Гр.Название AS subgroup_name FROM Пользователи_под_группы ПодГ INNER JOIN Б_Пользователи_Группы Гр ON ПодГ.Группа_Код = Гр.Код WHERE Пользователи_Код = ?";
		$this->Core->query_params = array($this->user_id);
		$user_subgroups = $this->Core->PDO(array('global_db' => true));
		while ($subgroup = $user_subgroups->fetch()) {
			$this->user_subgroups[$subgroup['subgroup_id']] = $subgroup['subgroup_name'];
		}
		$this->Core->set_sess('user_subgroups', $this->user_subgroups, 'usr');
	}
	public function get_user_preferences () {
		$this->Core->query = "SELECT ПН.Название AS pref_value,БН.Название AS pref_name FROM Пользователи_Настройки ПН";
		$this->Core->query .= QUERY_SPACE . 'INNER JOIN Б_Пользователи_Настройки БН ON ПН.Настройки_код = БН.Код';
		$this->Core->query .= QUERY_SPACE . 'WHERE Пользователь_код = ?';
		$this->Core->query_params = array($this->user_id);
		$user_preferences = $this->Core->PDO(array('global_db' => true));
		while ($preference = $user_preferences->fetch()) {
			$this->user_preferences[$preference['pref_name']] = $preference['pref_value'];
		}
		$this->Core->set_sess('user_preferences', $this->user_preferences, 'usr');
	}
	public function set_user_preference ($pref_name, $pref_value) {
		$this->Core->query = "SELECT Код FROM Б_Пользователи_Настройки WHERE Название = ?";
		$this->Core->query_params = array(array(1, $pref_name, PDO::PARAM_STR));
		$pref_id = $this->Core->PDO(array('global_db' => true, 'strict' => true))->fetchColumn();
		if ($pref_id !== false) {
			$this->Core->query = "SELECT Название FROM Пользователи_Настройки WHERE Пользователь_код = :user_id AND Настройки_код = :pref_id";
			$this->Core->query_params = array(array(':user_id', $this->user_id, PDO::PARAM_INT), array(':pref_id', $pref_id, PDO::PARAM_INT));
			$preference = $this->Core->PDO(array('global_db' => true, 'strict' => true))->fetchColumn();
			if ($preference !== false && $preference !== $pref_value) {
				$this->Core->query = "UPDATE Пользователи_Настройки SET Название = :pref_value WHERE Пользователь_код = :user_id AND Настройки_код = :pref_id";
				$this->Core->query_params = array(array(':pref_value', $pref_value, PDO::PARAM_STR), array(':user_id', $this->user_id, PDO::PARAM_INT), array(':pref_id', $pref_id, PDO::PARAM_INT));
				$this->Core->PDO(array('global_db' => true, 'strict' => true));
				$this->user_preferences[$pref_name] = $pref_value;
				$this->Core->set_sess('user_subgroups', $this->user_preferences, 'usr');
			} elseif ($preference === false) {
				$this->Core->query = "INSERT INTO Пользователи_Настройки (Пользователь_код,Настройки_код,Название)";
				$this->Core->query .= QUERY_SPACE . "VALUES (:user_id,:pref_id,:pref_value)";
				$this->Core->query_params = array(array(':user_id', $this->user_id, PDO::PARAM_INT), array(':pref_id', $pref_id, PDO::PARAM_INT), array(':pref_value', $pref_value, PDO::PARAM_STR));
				$this->Core->PDO(array('global_db' => true, 'strict' => true));
				$this->user_preferences[$pref_name] = $pref_value;
				$this->Core->set_sess('user_subgroups', $this->user_preferences, 'usr');
			}
		} else
			die('Unknown preference');
	}
	public function get_permissions () {
		$this->Core->query = "SELECT module,component FROM srv.dbo.Routes AS R";
		$this->Core->query .= QUERY_SPACE . "INNER JOIN srv.dbo.Permissions AS P ON";
		$this->Core->query .= QUERY_SPACE . "R.id = P.route_id AND (user_id = :user_id OR group_id = :group_id)";
		$this->Core->query_params = array(array('user_id', $this->user_id, PDO::PARAM_INT), array('group_id', $this->user_group_id, PDO::PARAM_INT));
		$permissions = $this->Core->PDO(array('global_db' => true, 'strict' => true));
		while ($permission = $permissions->fetch()) {
			$response[] = $permission;
		}
		return $response;
	}
	protected function reg_new_user ($name, $s_name, $email) {
		$name = filter_var($name, FILTER_SANITIZE_STRING);
		$s_name = filter_var($s_name, FILTER_SANITIZE_STRING);
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$this->Core->query = "INSERT INTO Пользователи (Login,Full_name,Email) VALUES (:login,:full_name,:email)";
		$this->Core->query_params = array(array(':login', $this->user_login, PDO::PARAM_STR), array(':full_name', $name . ' ' . $s_name, PDO::PARAM_STR), array(':email', $email, PDO::PARAM_STR));
		$this->Core->PDO(array('global_db' => true, 'strict' => true));
		$this->user_id = $this->Core->PDO_last_inserted_id('Пользователи');
		$this->set_user_preference('lang', self::DEFAULT_LANG);
		header('Location: ' . WWW_ROOT);
		exit();
	}
}
?>
