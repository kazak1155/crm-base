<?php
if(!isset($this))
{
	header("Status: 301 Moved Permanently");
	header("Location:http://".$_SERVER['HTTP_HOST']."/php/tmpl/iframe_tmpl?forced=true&reference=".$_SERVER['REQUEST_URI']);
	exit;
}
$this->con_database('sales');
$rowid = $_GET['rowid'];
?>
<script>
$(function() {
	var rowid = <?php echo $rowid ?>;
	$('.niceButton_blue').click(function(e){
		var o_data = {},summ_real = summ_fact = 0;
		$.each($('#o-data').find('input[type!="hidden"]:enabled'),function(){
			var id = $(this).closest('tr').find('input[type="hidden"]').val(),
			real = parseFloat($(this).closest('tr').find('input[name="Стоимость_фабрика"]').val()) + parseFloat($(this).closest('tr').find('input[name="Стоимость_логистика"]').val());
			real =  real.toFixed(2);
			summ_real += parseFloat(real);
			summ_fact += parseFloat(this.value);
			o_data[id] = {'Стоимость':this.value,'Себестоимость': real};
		});
		$.asyncloop({
			length:Object.keys(o_data).length,
			loop_action:function(loop,i)
			{
				var row = Object.keys(o_data)[i];
				$.ajaxShort({
					data:{
						action:'edit',
						query:'DELETE FROM Заказы_Счета WHERE Заказы_Код = '+ rowid + ' AND Заказы_Состав_Код = '+ row
					},
					success:function(){
						$.ajaxShort({
							data:{
								action:'add',
								query:"INSERT INTO Заказы_Счета (Заказы_Код,Заказы_Состав_Код,Стоимость,Стоимость_предв) VALUES ("+ rowid +","+ row +","+ o_data[row]['Стоимость'] +","+ o_data[row]['Себестоимость'] +")"
							},
							success:function(){
								loop();
							}
						});
					}
				});
			},
			callback:function()
			{
				$.ajaxShort({
					data:{
						action:'edit',
						query:'DELETE FROM Заказы_Фин_Движение WHERE Заказы_Код = '+ rowid + ' AND Движение_Тип_Код IN (3,4)'
					},
					success:function(){
						$.ajaxShort({
							data:{
								action:'add',
								query:"INSERT INTO Заказы_Фин_Движение (Заказы_Код,Движение_Тип_Код,Плательщик,Получатель,Движение_Категория_Код,Валюта_Код,Значение,Подтверждено) VALUES ("+ rowid +",3,35,35,4,'EUR',"+ summ_real +",1)"
							},
							success:function(){
								$.ajaxShort({
									data:{
										action:'add',
										query:"INSERT INTO Заказы_Фин_Движение (Заказы_Код,Движение_Тип_Код,Плательщик,Получатель,Движение_Категория_Код,Валюта_Код,Значение,Подтверждено) VALUES ("+ rowid +",4,35,35,4,'EUR',"+ summ_fact +",1)"
									}
								});
							}
						});
					}
				});
				get_file_url({qry:rowid},'Счет',{prefix:'pdf',folder:'shop_bill'});
				window.top.close();
			}
		});
	})
});
</script>
