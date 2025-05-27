<style>
	.key_s1{height:34px;width:123px;background-image: url(/css/images/key_s_b.png)}
	.key_s1:hover{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_s1:active{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_r1{height:34px;width:98px;background-image: url(/css/images/key_r_b.png)}
	.key_r1:hover{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
	.key_r1:active{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
</style>
<link href="/css/js/jquery-ui2.css" rel="stylesheet" type="text/css" />
<!--<link href="/css/grid/jqGridNew.css" rel="stylesheet" type="text/css" />
document.getElementById('rw').value = parentData;-->
<script type="text/javascript" src="/css/grid/jquery_flexbox.js"></script>
<script type="text/javascript" src="/css/grid/jquery-ui.min.js"></script>
<script src="/js/jqgrid/i18n/grid.locale-ru2.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/plugins/jqgrid/jquery.jqGrid.min.js"></script>
<script>
	//$('#page').val($('.ui-pg-input[role=textbox]').val());
	function delegate(rowid){//Функция, запускаемая по нажатию кнопки в таблице
		//alert(rowid);
		$.post("/core/reqsave?m=crm_delegatedata", { m: "crm_delegatedata", rowID: rowid},
			function(data){
				pdoerrors();
			//alert(data);
			var datArr = data.split('|||');
				$("#usr").empty();
				$("#usr").append(datArr[0]);
				$("#grpp").html(datArr[1]);
				//alert(datArr[1]);
			}
		);
		$('#pagedl').val($('.ui-pg-input[role=textbox]').val());
		//$('#lasDate').val(value);
		$('#rowid').val(rowid);
		$('#lasDate').datepicker({dateFormat:"yy-mm-dd"});  // Привязать вызов календаря к полю с CSS идентификатором #calenda
	}
	function testForm1(){
			$('#delegateDataForm').submit();
	}
</script>
<div style="padding:25px;background-color:#4479BA;color:#ffffff;">
	<div style="position:absolute;top:3px;right:4px;"><a href="javascript:void(0);" style="color:#ffffff;text-decoration:none;" onClick="javascript:document.getElementById('horizont').style.display='none';"> <img src="/css/images/crus004.png" border="0"/> </a></div>
	<form method="post" action="" id="delegateDataForm">
		<input type="hidden" name="ecommform" value="1"/>
		<input type="hidden" name="page" id="pagedl"/>
		<fieldset><legend>Делегирование задачи</legend>
			<p>
				Возможен выбор из группы:
			</p>
			<p>
				<div id="grpp"><br/></div>
			</p>

			<input type="hidden" name="delegateDataForm" value="1"/>
			<input type="hidden" name="rowID" id="rowid"/>
			<p>
				Укажите пользователя:
			</p>
			<p>
				<select name="deleguser" id="usr">
				</select>
			</p>
			<br/>
			<table style="float:right;">
				<tr>
					<td class="key_s1" onClick="javascript:testForm1();"></td>
					<td class="key_r1" onClick="javascript:document.getElementById('delegateDataForm').reset();$('#horizont').hide();"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

<div id="delegateroom"></div>



