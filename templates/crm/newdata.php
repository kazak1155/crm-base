<style>
	.key_s4{height:34px;width:123px;background-image: url(/css/images/key_s_b.png)}
	.key_s4:hover{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_s4:active{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_r4{height:34px;width:98px;background-image: url(/css/images/key_r_b.png)}
	.key_r4:hover{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
	.key_r4:active{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
</style>
<script>
	function getData2(rowID,key){ //alert(rowID);
		$.post("/core/reqsave?m=crm_finaldata", { m: "crm_finaldata", rowID: rowID},
			function(data){
				document.getElementById('lasDate').value = data;
			}
		);
		$('#pagend').val($('.ui-pg-input[role=textbox]').val());
		$('#row__id').val(rowID);
		$('#lasDate').datepicker({dateFormat:"dd-mm-yy"});  // Привязать вызов календаря к полю с CSS идентификатором #calenda
	}
	function testForm(){
		if($('#newDatComment').val() != '' && $('#newDatComment').val() != 'undefined' && $('#newDatComment').val() != 'NaN'){
			//$('#newDataForm').submit();
			formSave();
		}else{
			alert('Ведите комментарий');
		}
	}
	function formSave(){
		$.ajax({url: "/core/srv_01.php",async: false,type: 'get',data: {m: "crmSaveDate", rowID: $('#row__id').val(),lasDate:$('#lasDate').val(),newDatComment:$('#newDatComment').val()},success: function (data) {
			pdoerrors();
			//data = jQuery.parseJSON(data);
			$('#list').trigger("reloadGrid",{current:true});
			$('#horizont4').hide();
		}});


	}
</script>
<div style="padding:25px;background-color:#4479BA;color:#ffffff;">
	<div style="position:absolute;top:3px;right:4px;"><a href="javascript:void(0);" style="color:#ffffff;text-decoration:none;" onClick="javascript:document.getElementById('horizont4').style.display='none';"> <img src="/css/images/crus004.png" border="0"/> </a></div>
	<form method="post" action="" id="newDataForm">
		<!--<input type="hidden" name="ecommform" value="1"/>-->
		<input type="hidden" name="page" id="pagend"/>
		<fieldset><legend>Изменение даты окончания</legend>
			<p>
				Введите новую дату:
			</p>
			<p><input type="text" name="lasDate" id="lasDate"/></p>
			<input type="hidden" name="newfinaldata" value="1"/>
			<input type="hidden" name="rowID" id="row__id"/>
			<p>
				Обязательный комментарий к изменению даты:
			</p>
			<p>
				<textarea name="newDatComment" id="newDatComment" style="width:100%;height:90px;"></textarea>
			</p>
			<table style="float:right;">
				<tr>
					<td class="key_s4" onClick="javascript:testForm();"></td>
					<td class="key_r4" onClick="javascript:getData2(document.getElementById('rw').value,0);$('#horizont4').hide();"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>


