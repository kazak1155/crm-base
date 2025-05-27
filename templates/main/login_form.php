<!DOCTYPE html>
<html>
<head>
<?php $Site->get_meta(); ?>
<title></title>
<link href="/css/plugins/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300italic,700,300&subset=cyrillic-ext,latin" rel="stylesheet" type="text/css">
<link href="/css/homepage.css" rel="stylesheet" type="text/css" />
<link href="/css/global.css" rel="stylesheet" type="text/css" />
<link href="/css/index-page.css" rel="stylesheet" type="text/css" />
<link href="/css/plugins/jquery.ddmenu.css" rel="stylesheet" type="text/css">
<script src="/js/jquery/jquery-1.11.2.js" type="text/javascript"></script>
<script src="/js/jquery/jquery-ui-1.11.2.js" type="text/javascript"></script>
<script src="/js/plugins/jquery.ddmenu/jquery.ddmenu.js" type="text/javascript"></script>
<script src="/js/misc/index-page.js" type="text/javascript"></script>
<script src="/js/core/App.ajax.js" type="text/javascript"></script>
<style>
.login-form
{
	width:450px;
	height:200px;

	margin: 0 auto;

	background-color:#999;
	padding:25px;
}
.login-form .float_input
{
	border: 1px solid #10498D;
}
</style>
</head>
<body>
	<form class="login-form" method="post">
		<div class='float_label_wrapper' style="width:calc(100% - 11px);">
			<input type="text" name="user_login" id="user_login" class="float_input" required autocomplete="off"/>
			<label class="float_label" for="user_login">Логин</label>
		</div>
		<div class='float_label_wrapper' style="width:calc(100% - 11px);">
			<input type="password" name="user_password" id="user_password" class="float_input" required/>
			<label class="float_label" for="user_password">Пароль</label>
		</div>
		<button>qwe</button>
	</form>
</body>
</html>
