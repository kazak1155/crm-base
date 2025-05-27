$(function() {
	new $ajaxForm({
		name:'orderinfo',
		floatLabel:true,
		view_only:true
	});
	$('#email_type').on('change',function(e)
	{
		var url = window.location.search;
		url = decodeURIComponent(url);
		url = url.replace(/\"mail_id\"\:[0-9]+/,'"mail_id":'+this.value);
		window.location.search = url;
	});
	$('#mail_from').on('change',function(e)
	{
			$('#mail_pass').attr('disabled',false);
	});
	$('#mail').submit(function(e)
	{
		e.preventDefault();
		var form = this;
		var $file_input = $('input[name="mail_files"]',this);
		var form_data = new FormData(this);
		var form_init_data = $(this).serializeObject();
		var parent_dialog = window.parent.$('.email-dialog');
		$('body').append('<div class="overlay-loading data-loading"></div>');
		form_data.append('get_data',JSON.stringify(val_get()));
		$.ajaxShort({
			progress_bar:true,
			// The current page
			url:window.location.origin + window.location.pathname,
			data:form_data,
			dataType:'text',
			success:function(response)
			{
				//console.log (response);
				$('.data-loading').remove();
				if(response.length>0)
				{
					$.alert(response,'');
					return;
				}
			}
		});
	});
});
