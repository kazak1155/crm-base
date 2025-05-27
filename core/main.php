<?php
if(!isset($_SESSION))
	session_start();
error_reporting(E_ALL ^ E_NOTICE);
mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Moscow');

global $User;
global $Core;
global $Site;

$Core = new Core;
$User = new User($Core);
$Site = new Site($User,$Core);
?>
