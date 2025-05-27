<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once 'processor.php';
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300italic,700,300&subset=cyrillic-ext,latin" rel="stylesheet" type="text/css">
<link href="/css/plugins/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="/css/homepage.css" rel="stylesheet" type="text/css" />
<link href="/css/global.css" rel="stylesheet" type="text/css" />
<link href="/css/js/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="/css/plugins/select2.css" rel="stylesheet" />
<link href="style.css" rel="stylesheet" />
<script src="/js/jquery/jquery-1.11.2.js" type="text/javascript"></script>
<script src="/js/jquery/jquery-ui-1.11.2.js" type="text/javascript"></script>
<script src="/js/plugins/jquery.dialogextend/dialogextend.js"></script>
<script src="/js/core/App.ajax.js" type="text/javascript"></script>
<script src="/js/jquery/extend/jquery-extend-0.1.js" type="text/javascript"></script>
<script src="/js/plugins/select2/select2.full.js"></script>
<script src="/js/plugins/select2/i18n/<?php echo $Site->lang; ?>.js"></script>
<script src="/js/misc/i18n/app.locale-<?php echo $Site->lang; ?>.js" type="text/javascript"></script>
<script src="/js/misc/global.js" type="text/javascript"></script>
<script src="/js/core/App.const.js" type="text/javascript"></script>
<script src="/js/core/App.constructors.js" type="text/javascript"></script>
<script src="/js/core/App.functions.js" type="text/javascript"></script>
<script src="/js/core/App.ajax.form.js" type="text/javascript"></script>
<script src="/js/core/jqGrid_constructor/jqGrid_anonymous_functions.js" type="text/javascript"></script>
<script src="script.js" type="text/javascript"></script>
</head>
<body>
<div id="form_wrapper">
	<div style="display:inline-block;position:relative;width:100%;margin-top:25px;margin-bottom: 15px;margin-left: 1px;">
		<select class="select2me" style="width:100%;" id="email_type">
			<?php echo $Site->Core->get_lib_html(['tname'=>'Письма_Шаблоны','selected'=>$mail_id,'srv'=>true]); ?>
		</select>
		<label for="email_type" class="float_label_select">Шаблоны писем</label>
	</div>
	<?php if(!empty($data['Клиенты_Код']) || !empty($data['Фабрики_Код'])):?>
	<form id="orderinfo">
		<fieldset>
			<legend>Данные заказа</legend>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Клиент" class="float_input" value="<?php echo $data['Клиенты_Код'] ?>"/>
				<label class="float_label">Клиент</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(50% - 15px);">
				<input type="text" name="Фабрика" class="float_input" value="<?php echo $data['Фабрики_Код'] ?>"/>
				<label class="float_label">Фабрика</label>
			</div>
		</fieldset>
	</form>
	<?php endif; ?>
	<form id="mail" class="gridToForm">
		<fieldset>
			<legend>Состав письма</legend>
			<input type="hidden" name="mail_action" class="float_input" value="send"/>
			<input type="hidden" name="rowid" class="float_input" value="<?php echo $_REQUEST['rowid'] ?>"/>
			<input type="hidden" name="mail_id" class="float_input" value="<?php echo $mail_id ?>"/>
			<input type="hidden" name="lang" class="float_input" value="<?php echo $mail_init['lang']  ?>"/>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="email" name="mail_to" multiple id="mail_to" class="float_input"
                       <?php if(empty($data['Фабрики_Код']) && $mail_init['to'] == 'Почта не указана'):?>
                           style="color:red;font-weight:bold"
                       <?php endif; ?> required
                       value="<?php echo $mail_init['to'] ?>"
                />
				<label class="float_label" for="mail_to">Кому</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<?php 
				$username = $Core->server_prm['smtpserverlogin_'.$User->user_name];
				//if ($Core->server_prm['smtpserverlogin'] !== null) 
				if ($username !== null) 
				{
					echo '<input type="email" name="mail_from" id="mail_from" class="float_input"  value="'.$username.'" required/>';
				}
				else
				{
					echo '<input type="email" name="mail_from" id="mail_from" class="float_input" value="'.$Core->server_prm['smtpserverlogin'].'" required/>';
				};
			?>
				<label for="mail_from" class="float_label">От кого</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
			<?php 
				$userpass = $Core->server_prm['sendpassword_'.$User->user_name];
				//if ($Core->server_prm['sendpassword'] !== null) 
				if ($userpass !== null)
				{			
					echo '<input type="password" name="mail_pass" id="mail_pass" class="float_input" value="'.$userpass.'" required/>';
				}
				else
				{
					echo '<input type="password" name="mail_pass" id="mail_pass" class="float_input" value="'.$Core->server_prm['sendpassword'].'" required/>';
				};
			?>
				
				<label class="float_label" for="mail_pass">Пароль</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="text" name="mail_theme" id="mail_theme" class="float_input" required value="<?php echo $mail_init['theme'] ?>"/>
				<label class="float_label" for="mail_theme">Тема</label>
			</div>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<textarea name="mail_text" id="mail_text" class="float_input" required><?php echo $mail_init['text'] ?></textarea>
				<label class="float_label" for="mail_text">Текст</label>
			</div>
			<?php if($mail_id !== 56 && $mail_id !== 55):?>
			<div class='float_label_wrapper' style="width:calc(100% - 15px);">
				<input type="file" name="mail_files[]" class="float_input" multiple/>
				<label class="float_label">Файл</label>
			</div>
			<?php endif;?>
		</fieldset>
		<input type="submit"/>
	</form>
</div>
</body>
</html>
