<?php
print_pre($this);
exit();
 ?>
<script src="/js/plugins/jquery.flot/jquery.flot.min.js" type="text/javascript"></script>
<script>
$(function()
{
	new jqGrid$({
		defaultPage:false,
		name:'tig_haul_delivery',
		table:'Статистика_Рейсы_Тарифы',
		id:'Рейсы_Код',
		orderName:'Тариф_Код',
		tableSort:'ASC',
		useLs:false,
		navGrid:false,
		hideBottomNav:true,
		cn:[
			"Номер рейса","Тариф","Сумма",
		],
		cm:[

			{
				name: "Рейсы_Код",index:"Рейсы_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Рейсы']) ?>
				}
			},

			{
				name: "Тариф_Код",index:"Тариф_Код",width:200,formatter:'select',stype:'select',addformeditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Тарифы2']) ?>
				}
			},

			{
				name: "Сумма",index:"Сумма",width:50,formatter:floatFormatter,search:false
			}

		],
		options:{
			rowNum:1000,
			cellEdit:false,
			shrinkToFit:true,
			height:400
		}
	})
});
</script>
<div>
	<div class="plot"></div>
	<hr class="ni-divider" />
	<div >
		<table class='gridclass' id='tig_haul_delivery'></table>
	</div>
</div>
