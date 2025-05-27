<style>
	.key_s1{height:34px;width:123px;max-width:123px;background-image: url(/css/images/key_s_b.png)}
	.key_s1:hover{height:34px;width:123px;max-width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_s1:active{height:34px;width:123px;max-width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_r1{height:34px;width:98px;max-width:98px;background-image: url(/css/images/key_r_b.png)}
	.key_r1:hover{height:34px;width:98px;max-width:98px;background-image: url(/css/images/key_r_h.png)}
	.key_r1:active{height:34px;width:98px;max-width:98px;background-image: url(/css/images/key_r_h.png)}
</style>
<div style="padding:25px;background-color:#99bbee;">
	<div style="position:absolute;top:3px;right:4px;"><a href="javascript:void(0);" style="color:#ffffff;text-decoration:none;" onClick="javascript:$('#horizont6').hide();$('#horizont5').show();">
			<img src="/css/images/crus004.png" border="0"/> </a></div>

<fieldset>
	<legend>Создание нового шаблона</legend>

	<form method="post" action="" id="tmpladd">
<table cellpadding="5">
	<tr><td><input type="text" class="llong" name="tplname" value=""/></td><td> Имя шаблона</td></tr>
	<tr><td><select name="tplcont" class="llong">
				<option>Входящий</option>
				<option>Исходящий</option>
			</select></td><td> Тип контакта</td></tr>
	<tr><td><input type="text" class="llong" name="tpltyp" value=""/></td><td> Тип обращения</td></tr>
	<tr><td><input type="text" class="llong" name="tpltem" value=""/></td><td> Тема</td></tr>
	<tr><td><input type="text" class="llong" name="tpldes" value=""/></td><td> Описание</td></tr>
	<tr><td><select name="tplgrp" class="llong" onChange="javascript:usersSel(this.value);">
				<option></option>
				<?php
				//tplname tplcont tpltyp tpltem tpldes tpluser tpluser tpldays
				$Core->query = "SELECT DISTINCT [Division] FROM [tig50].[dbo].[Пользователи] ORDER BY [Division]";
				$Core->con_database('tig50');
				$stm = $Core->PDO();
				while($roww = $stm->fetch()){
					print '<option value="'.$roww['Division'].'">'.$roww['Division'].'</option>';
				}
				?>
			</select></td><td> Группе</td></tr>
	<tr><td><select name="tpluser" class="llong" id="tpluser">


			</select></td><td> Пользователю</td></tr>
	<!--<tr><td><input type="text" class="llong" name="tpltest" value=""/></td><td> Дней до проверки</td></tr>-->
	<tr><td><input type="text" class="llong" name="tpldays" value=""/></td><td> Дней на выполнение</td></tr>
	<input type="hidden" name="addtplsubm" value="1"/>
</table>
		<table style="float:right;">
		<tr>
			<!--<td><input type="button" value="сохранить" onClick="javascript:addtemplformsub();"/></td>-->
			<td class="key_s1" onClick="javascript:addtemplformsub();"></td>
			<td class="key_r1"
				onClick="javascript:document.getElementById('tmpladd').reset();$('#horizont6').hide();$('#horizont5').show();"></td>
		</tr>
		</table>
</form>
</fieldset>
</div>
<script>
function addtemplformsub(){
	document.cookie = "prime_modus=horizont5; path=/";
	document.getElementById('tmpladd').submit();
}
function usersSel(positio){
	$.ajax({url:"/core/reqsave?m=load_user", async:false, type:'POST', data:{m: "load_user",modus:1,grp:positio},success:function(data) {
		pdoerrors();
		//document.getElementById('grtext').value = data;
		$('#tpluser').find('option').remove();
		$('#tpluser').append(jQuery.parseJSON(data));
	}});
}
usersSel();
</script>