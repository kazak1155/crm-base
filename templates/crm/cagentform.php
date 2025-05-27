<?php
$selPlaceholoder = "Выберите контрагента";
$selectID = "Контрагент_Код";
$selectClass = 3;
//$ajaxPahh =	'';
//print $conragents;
?>
<input type="hidden" id="sem<?=$selectClass;?>"/>
<select data-placeholder="<?=$selPlaceholoder;?>" class="chosen-select ttitle" style="border:0;font-size:11px;width:294px;margin-left: 4px;" tabindex="2" name="<?=$selectID;?>" id="<?=$selectID;?>">
</select>
<script>
	//alert('TTTTTTTTTTTTTTTT');
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
	function getSelectData_<?=$selectClass;?>(){
		var datArray;
		var dataCag = localStorage.getItem('contragent02');
		//alert(typeof dataCag);
		if(typeof dataCag !== 'string'){
			$.ajax({url: "/core/srv_01.php",async: false, type: "get", data: {m: "getCagentFull"}, success: function (data){
				pdoerrors();
				localStorage.setItem('contragent02', data);
			}});
			dataCag = localStorage.getItem('contragent02');
			//alert(dataCag);
		}else{
		}
		//alert(dataCag);
	$("#chosetextsel<?=$selectClass;?>").empty();
	$("#<?=$selectID;?>").empty();
	$("#<?=$selectID;?>").append(dataCag);
	$("#<?=$selectID;?>").trigger("chosen:updated");
	//$("#_________chosen").width('400px');
	$(".chosen-container").width('<?=$rowWidth;?>px');
	$("#sem<?=$selectClass;?>").val('1');
	}
	getSelectData_<?=$selectClass;?>();
</script>
<div id="chosesel<?=$selectClass;?>" class="chosesel<?=$selectClass;?>" style="position:absolute;height:300px;max-height:300px;width:300px;overflow: auto;background-color: #ffffff;z-index:30000010;display:none;padding:15px;">
	<p><input type="text" name="newedit<?=$selectClass;?>" id="newedit<?=$selectClass;?>" style="width:300px;"/><input type="hidden" name="codedit<?=$selectClass;?>" id="codedit<?=$selectClass;?>"/>
	</p><div id="chosetextsel<?=$selectClass;?>" style="background-color: #ffffff;width:100%;">

	</div>
</div>