<script>
	$('#fdiv').empty();
	function showFieldsListC(){
		$('#fdiv').empty();
		var fString = $('#fieldsListC').val();
		//alert(fString);
		var fList = fString.split(',');
		var cntr = 0;
		for(key in fList) {
			if(typeof(fList[key]) == 'string' && fList[key] != ''){
				var fElem = fList[key].replace(/"/g, '') ;
				fElem = fElem.replace(/<br\/>/g, '') ;
				//var crrCheck =
				if(fieldChArr[cntr] > 0){
					$('#fdivC').append('<input type="checkbox" value="1" name="fieldC_'+cntr+'" name="fieldC_'+cntr+'" /> '+fElem+' <br/>');
				}else{
					$('#fdivC').append('<input type="checkbox" value="1" name="fieldC_'+cntr+'" name="fieldC_'+cntr+'" checked/> '+fElem+' <br/>');
				}
				cntr =  cntr + 1;
			}
		}
	}
</script>
<div style="position:absolute;top:10px;right:6px;"><a href="javascript:void(0);" onClick="javascript:$('#horizont15').hide();"><img src="/css/images/crus003.png"/></a></div>
<fieldset style="height:96%;">
	<legend>Показываемые поля</legend>
	<form method="post">
		<div id="fdivC"></div>
		<table style="position:absolute;bottom:10px;right: 3px;">
			<tr><td><input type="submit" name="flistsubmC" value="Сохранить"></td><td>
					<form method="post">
						<input type="submit" name="defaultFieldsC" value="Сбросить настройки таблицы"/>
					</form>
				</td></tr>
		</table>
	</form>
</fieldset>