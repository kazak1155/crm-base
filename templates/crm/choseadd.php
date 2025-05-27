<select data-placeholder="<?=$selPlaceholoder;?>" class="chosen-select ttitle" style="font-size:11px;width:300px;" tabindex="2" name="<?=$selectID;?>" id="<?=$selectID;?>">
</select>
<script>
	var datArray;
	function edpict(lne,lneid){
		document.getElementById('newedit<?=$selectClass;?>').value = lne;
		document.getElementById('codedit<?=$selectClass;?>').value = lneid;
	}
	function rmpict(lne,lneid){
		if(confirm('Подтвердите удление')){
			$.post("/core/reqsave?m=crm_selnamedelete", {m: "crm_selnamedelete", i:lneid, class:"<?=$selectClass;?>"},function(data){ //alert(data);
				pdoerrors();
				$("#chosetextsel<?=$selectClass;?>").empty();
				getSelectData();
				$("#newedit<?=$selectClass;?>").val('');
				//$("#chosesel<?=$selectClass;?>").hide();
			});
		}
	}
	function saveselect(){
		var p = $("#newedit<?=$selectClass;?>").val();
		var i = $("#codedit<?=$selectClass;?>").val();
		$.post("/core/reqsave?m=crm_selnamesave", {m: "crm_selnamesave", p: p, i:i, class:"<?=$selectClass;?>"},function(data){ //alert(data);
			pdoerrors();
			$("#chosetextsel<?=$selectClass;?>").empty();
			getSelectData();
			$("#newedit<?=$selectClass;?>").val('');
			$("#chosesel<?=$selectClass;?>").hide();
		});
	}
	function closeselect(){
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
	function getSelectData(){ //alert('ssssssssssssss');
		$.post("/core/reqsave?m=crm_selnamelist", {m: "crm_selnamelist", class: <?=$selectClass;?>, id:'<?=$selectID;?>'},function(data){ //alert(data);
			pdoerrors();
			datArray = jQuery.parseJSON(data);
			$("#chosetextsel<?=$selectClass;?>").empty();
			$("#<?=$selectID;?>").empty();
			$("#<?=$selectID;?>").append('<option></option>');
			var lines = 1;
			for (dtKey in datArray) {
				if(typeof(datArray[dtKey][0]) != "function" &&  typeof(datArray[dtKey][0]) != "undefined") {
					var style2 = '';
					if(lines > 0){style2 = 'background-color:#f0f0f0;'; lines = 0;}else{lines ++;}
					$("#<?=$selectID;?>").append('<option value="'+datArray[dtKey][0]+'">'+datArray[dtKey][0]+'</option>');
					var toString = "<div style='"+style2+"'>"+datArray[dtKey][0]
						+ "<div style='float:right;"+style2+"'><a href='javascript:void(0);' onClick='javascript:edpict(\""+datArray[dtKey][0]+"\",\""+datArray[dtKey][1]+"\");'>" + "<img src='/css/images/pensel03.png'></a>"
						+ "&#160;"
						+ "<div style='float:right;"+style2+"'><a href='javascript:void(0);' onClick='javascript:rmpict(\""+datArray[dtKey][0]+"\",\""+datArray[dtKey][1]+"\");'>" + "<img src='/css/images/crus01.png'></a>"
						+ "</div></div>";
					//alert(toString);
					$("#chosetextsel<?=$selectClass;?>").append(toString);

				}
			}
			$("#<?=$selectID;?>").trigger("chosen:updated");
		});
	}
	//getSelectData();
</script>
<div id="chosesel<?=$selectClass;?>" class="chosesel<?=$selectClass;?>" style="position:absolute;height:300px;max-height:300px;width:400px;overflow: auto;background-color: #ffffff;z-index:30000010;display:none;padding:15px;">
	<p><input type="text" name="newedit<?=$selectClass;?>" id="newedit<?=$selectClass;?>" style="width:300px;"/><input type="hidden" name="codedit<?=$selectClass;?>" id="codedit<?=$selectClass;?>"/>
		<span style="float:right;">
	<a href='javascript:void(0);' onClick='javascript:saveselect();' style="text-decoration: none;color:#000000;"><img src="/css/images/arrow01.png"></a> &#160;
	<a href='javascript:void(0);' onClick='javascript:closeselect();' style="text-decoration: none;color:#000000;"><img src="/css/images/crus01.png"></a>
	</span>
	</p><div id="chosetextsel<?=$selectClass;?>" style="background-color: #ffffff;width:100%;">

	</div>
</div>


