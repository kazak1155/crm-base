$(function() {
	/* select2 class */
	//pdoerrors();
	if($('.select2me').length > 0)
		dataSelect2.call(this,$('.select2me'))
	/* datepicker class */
	if($('.defaultDatePicker').length > 0)
		$('.defaultDatePicker').datepicker();
	/* block class */
	if($('.blockMe').length > 0)
		$('.blockMe').block({message: null,overlayCSS:{cursor:'default'},baseZ:100});
	$('.integer-only').keypress(function(event){
		var key = (event.which) ? event.which : event.keyCode
		if (key > 31 && (key < 48 || key > 57))
	 		return false;
	});
	$('.numeric-only').keypress(function(event){
		var key = (event.which) ? event.which : event.keyCode;
		if (key > 31 && (key < 48 || key > 57))
		{
			if(key != 46 && key != 44)
				return false;
		}
	});
	$(window).on('resize',function(e)
	{
		if($('table.gridclass').length > 0)
		{
			$.each($('table.gridclass'),function(i,el)
			{
				var $el = $(el);
				if(el.hasOwnProperty('jqGrid$'))
				{
					if(el.jqGrid$.resize !== false)
						$el.setGridHeight(el.jqGrid$.adjust_height());
				}
				else if(el.hasOwnProperty('p') && el.p.hasOwnProperty('gridProto'))
				{
					if(el.p.gridProto.resize !== false)
						$el.setGridHeight(el.p.gridProto.adjust_height());
				}

			})
		}
	})
});
function pdoerrors(){
	/*
	$.get("/core/srv_01.php", { m: "get_dbo_error",leave_error:1}, function(data){
		//console.log('||'+data+'##');
		if(data.replace(/\s+/g,'') != 'none'){
			var dat = jQuery.parseJSON(data);
			var pdoErr = dat['pdo'];
			myin=open('/core/errpopup.php','ErrorPageWin1','height=300,width=600,fullscreen=no,status=no,toolbar=no,scrollbars=yes,resizable=yes,screenX=0,screenY=0,left=0,top=0');
			if(!myin.opener) {myin.opener=self;}
			myin.focus();
		}
	});
	*/
}
function pdoerrors_m(ms){
	if(ms == 1){
		myin=open('/core/errpopup.php','ErrorPageWin1','height=300,width=600,fullscreen=no,status=no,toolbar=no,scrollbars=yes,resizable=yes,screenX=0,screenY=0,left=0,top=0');
		if(!myin.opener) {myin.opener=self;}
		myin.focus();
	}
}