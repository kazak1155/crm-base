<?php
$selPlaceholoder = "Выберите контрагента";
$selectID = "Контрагент_Код";
$selectClass = 3;
$rowWidth = 400;
$selectNames = "[Код],[Название]";
$ajaxPahh =	'$.ajax({url: "/core/srv_01.php",async: false, type: "post", data: {m: "getCagentSmall"}, success: function (data)';
?>
<input type="hidden" id="sem<?=$selectClass;?>"/>
<select data-placeholder="<?=$selPlaceholoder;?>" class="chosen-select ttitle" style="border:0;font-size:11px;width:294px;margin-left: 4px;" tabindex="2" name="<?=$selectID;?>" id="<?=$selectID;?>">
</select>
<script>
	var config = {
		'.chosen-select'           : {},
		'.chosen-select-deselect'  : { allow_single_deselect: true },
		'.chosen-select-no-single' : { disable_search_threshold: 10 },
		'.chosen-select-no-results': { no_results_text: 'Oops, nothing found!' },
		'.chosen-select-rtl'       : { rtl: true },
		'.chosen-select-width'     : { width: '95%' }
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
	function closeselect<?=$selectClass;?>(){
		$("#newedit<?=$selectClass;?>").val('');
		$("#chosesel<?=$selectClass;?>").hide();
	}
	$(document).mouseup(function (e) {
		var container = $(".chosesel<?=$selectClass;?>");
		var container2 = $(".noclose");
		if (container.has(e.target).length === 0 && container2.has(e.target).length === 0){
			container.hide();
		}
	});
	function getSelectData<?=$selectClass;?>(){
		var datArray;
			var dataCag = localStorage.getItem('contragents');
			if(typeof dataCag !== 'string'){
				<?=$ajaxPahh;?>{
				pdoerrors();
				localStorage.setItem('contragents', data);
				}});
				var dataCag = localStorage.getItem('contragents');
			}
				//alert();
			dat = jQuery.parseJSON(dataCag);
			datArray = dat;
			$("#chosetextsel<?=$selectClass;?>").empty();
			$("#<?=$selectID;?>").empty();
			$("#<?=$selectID;?>").append('<option></option>');
			var lines = 1;
			for (dtKey in datArray) {
				//console.log(datArray[dtKey].Код);
				//console.log(datArray[dtKey].Название);
				if (typeof(datArray[dtKey]) != "function" && typeof(datArray[dtKey].Код) != "undefined") {
					var style2 = '';
					if (lines > 0) {
						style2 = 'background-color:#f0f0f0;';
						lines = 0;
					} else {
						lines++;
					}
					$("#<?=$selectID;?>").append('<option value="' + datArray[dtKey].Код + '">' + datArray[dtKey].Название + '</option>');
				}
			}
			$("#<?=$selectID;?>").trigger("chosen:updated");
			//$("#_________chosen").width('400px');
			$(".chosen-container").width('<?=$rowWidth;?>px');
			$("#sem<?=$selectClass;?>").val('1');
	}
	getSelectData<?=$selectClass;?>();
</script>
<div id="chosesel<?=$selectClass;?>" class="chosesel<?=$selectClass;?>" style="position:absolute;height:300px;max-height:300px;width:300px;overflow: auto;background-color: #ffffff;z-index:30000010;display:none;padding:15px;">
	<p><input type="text" name="newedit<?=$selectClass;?>" id="newedit<?=$selectClass;?>" style="width:300px;"/><input type="hidden" name="codedit<?=$selectClass;?>" id="codedit<?=$selectClass;?>"/>
	</p><div id="chosetextsel<?=$selectClass;?>" style="background-color: #ffffff;width:100%;">

	</div>
</div>