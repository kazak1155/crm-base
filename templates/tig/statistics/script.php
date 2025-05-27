<script>
$(function()
{
	$('.stat-type').on('change',function(event,rowid)
	{
		var stat_content = $('.statistics-content');
		stat_content.empty();
		if(this.value.length > 0)
		{
			var iframe = document.createElement('iframe');
			iframe.align = 'top';
			iframe.height = '100%';
			iframe.width = '100%';
			iframe.style.borderWidth = 0;
			iframe.name = 'current-iframe';
			iframe.src = 'plugin?p_name='+this.value+'&reference=<?php echo $this->reference_url ?>';
			stat_content.append(iframe);
		}
	});
});
</script>
