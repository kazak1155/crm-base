<?php
$selPlaceholoder = "Выберите контрагента";
$selectID = "Контрагент_Код";
$selectClass = 3;
//print $conragents;
?>
<select data-placeholder="<?=$selPlaceholoder;?>" class="chosen-select ttitle" style="border:0;font-size:11px;width:294px;margin-left: 4px;" tabindex="2" name="<?=$selectID;?>" id="<?=$selectID;?>">
</select>
<script>
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
		$("#<?=$selectID;?>").append(conragents);
	}
	getSelectData<?=$selectClass;?>();
</script>
<div id="chosesel<?=$selectClass;?>" class="chosesel<?=$selectClass;?>" style="position:absolute;height:300px;max-height:300px;width:300px;overflow: auto;background-color: #ffffff;z-index:30000010;display:none;padding:15px;">
	<p><input type="text" name="newedit<?=$selectClass;?>" id="newedit<?=$selectClass;?>" style="width:300px;"/><input type="hidden" name="codedit<?=$selectClass;?>" id="codedit<?=$selectClass;?>"/>
	</p><div id="chosetextsel<?=$selectClass;?>" style="background-color: #ffffff;width:100%;">

	</div>
</div>