$(function() {
	/* select2 class */
	if($('.select2me').length > 0)
	{
		dataSelect2.call(this,$('.select2me'))
	}
	/* datepicker class */
	$('.defaultDatePicker').datepicker();
	/* ctrl + q = reload grid */
	$(this).keypress(function(e) 
	{
		if(e.ctrlKey && e.keyCode === 17) // ctrl + q
		{
			$('td[id^="refresh"]').trigger('click');
			$(this).focus();
		}
			
	});
	$('.blockMe').block({
		message: null,
		overlayCSS:{cursor:'default'},
		baseZ:100
	})
	/* tabs defaults */
	$( ".menu_t,.menu_sub_t" ).tabs({
		beforeActivate:function(event, ui)
		{
			var id = ui.newTab[0].id;
			setSess({oper:'setSession',name:'connection',subname:'db',val:id},false,true)
			if((ui.newTab.context.href).indexOf('#empty') > -1)
				return false;
		},
		create:function(event,ui)
		{
			$(this).center();
		}
	});
	
})