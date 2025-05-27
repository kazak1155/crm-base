<?php
if(!isset($this))
{
	header("Status: 301 Moved Permanently");
	header("Location:http://".$_SERVER['HTTP_HOST']."/php/tmpl/iframe_tmpl?forced=true&reference=".$_SERVER['REQUEST_URI']);
	exit;
}
$this->con_database('sales');
$rowid = $_GET['rowid'];
$data = json_decode($_REQUEST['row_data'],true);
?>
<script>
$(function() {
	var step = parseInt(<?php echo $step ?>);
	$('#next,#prev').click(function(e)
	{
		if(step == 3 && this.id != 'prev')
		{
			$.ajaxShort({
				data:{
					oper:'edit',
					tname:'Заказы_Параметры',
					tid:'Код',
					id:<?php echo $rowid ?>,
					'Сформирован':1
				},
				success:function(){
					window.parent.$('.confirm-html ').dialog('close');
				}
			});
		}
		if($('#form-contains').length > 0 && $('#form-params').length > 0 && this.id != 'prev')
		{
			if($('#form-contains')[0].checkValidity() == false)
			{
				$('#form-contains').find(':submit').click();
				return false;
			}
			if($('#form-params')[0].checkValidity() == false)
			{
				$('#form-params').find(':submit').click();
				return false;
			}
		}
		switch(this.id){
			case 'next':
				step++
			break;
			case 'prev':
				--step;
			break;
		}
		var s = document.location.search;
		if(s.indexOf('step') > 0)
			s = s.substr(0,s.indexOf('&step=')) + '&step='+ step;
		else
			s = s + '&step='+ step;
		document.location.search = s;
	});
})
</script>
