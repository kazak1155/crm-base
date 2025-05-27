<?php
$types = $this->Core->get_lib(['tname'=>'Контрагенты_типы','empty'=>false,'cache'=>false,'encode'=>false]);
?>
<script>
$(function(){
	$('.cb-container').bind('click',function(event)
	{
		var this_icon$ = $(this).find('.fa');
		this_icon$.toggleClass('fa-check fa-times');
	});
	$('.save-types').bind('click',function(event)
	{
		var data = new Object();
		data.client_id = this.dataset.clientid;
		data.checked_types = new Array();
		$.each($('.fa-check').parent(),function()
		{
			data.checked_types.push(this.dataset.id);
		});
		$.ajaxShort({
			data:{
				data:JSON.stringify(data),
				action:'clients_process_types',
			},
			success:function(response)
			{
				window.parent.$('.confirm-html').dialog('close');
			}
		})
	})
});
</script>
<?php
foreach ($types as $key => $value)
{
	?>
	<div class="cb-container">
		<div class="value-container"><?php echo $value ?></div>
		<div data-id="<?php echo $key; ?>" class="fa-container" >
		<?php  if(strpos($this->req_data['Типы'],$value) !== false) : ?>
			<i class="fa fa-check"></i>
		<?php else: ?>
			<i class="fa fa-times"></i>
		<?php endif; ?>
		</div>
	</div>
	<?php
}
?>
<button data-clientid="<?php echo $this->req_rowid ?>" class="float_input save-types">Сохранить</button>
