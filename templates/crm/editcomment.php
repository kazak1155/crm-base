<style>
	.key_s{height:34px;width:123px;background-image: url(/css/images/key_s_b.png)}
	.key_s:hover{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_s:active{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_r{height:34px;width:98px;background-image: url(/css/images/key_r_b.png)}
	.key_r:hover{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
	.key_r:active{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
</style>
<link href="/css/js/jquery-ui2.css" rel="stylesheet" type="text/css" />
<!--<link href="/css/grid/jqGridNew.css" rel="stylesheet" type="text/css" />
document.getElementById('rw').value = parentData;-->
<script type="text/javascript" src="/css/grid/jquery_flexbox.js"></script>
<script type="text/javascript" src="/css/grid/jquery-ui.min.js"></script>
<script src="/js/jqgrid/i18n/grid.locale-ru2.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/plugins/jqgrid/jquery.jqGrid.min.js"></script>
<script>
function getData(rowID,pKey){
	var k = 0;
	$('#page').val($('.ui-pg-input[role=textbox]').val());
	//alert(aaa);
	if(pKey == 'Добавить'){
		k = 1;
		$('.key_s').css('background-image', 'url(/css/images/key_s_b.png)');
		$('.key_r').css('background-image', 'url(/css/images/key_r_b.png)');
	}else{
		$('.key_s').css('background-image', 'url(/)');
		$('.key_r').css('background-image', 'url(/)');
	}
	$.post("/core/reqsave?m=crm_comments", { m: "crm_comments", rowID: rowID,k:k},
		function(data){
			pdoerrors();
			document.getElementById('comments').innerHTML = data;
		}
	);
}
if(document.getElementById('pKey') != null) {
	if (document.getElementById('pKey').value !== 'Добавить') {
		document.getElementById('key_s').style.display = 'none';
		document.getElementById('key_r').style.display = 'none';
	}
}
</script>
<div style="padding:25px;background-color:#4479BA;color:#ffffff;">
	<div style="position:absolute;top:3px;right:4px;"><a href="javascript:void(0);" style="color:#ffffff;text-decoration:none;" onClick="javascript:document.getElementById('horizont3').style.display='none';"><img src="/css/images/crus004.png" border="0"/></a></div>
	<form method="post" action="" id="edCommentForm">
		<input type="hidden" name="ecommform" value="1"/>
		<input type="hidden" name="page" id="page" value=""/>
		<fieldset><legend>История комментариев</legend>
			<div style="height:650px;width:750px;" id="comments"></div>

<table style="float:right;">
	<tr>
		<td id="key_s" class="key_s" onClick="javascript:$('#edCommentForm').submit();"></td>
		<td id="key_r" class="key_r" onClick="javascript:getData(document.getElementById('rw').value,'Добавить');$('#horizont3').hide();"></td>
	</tr>
</table>
</fieldset>
</form>
</div>


