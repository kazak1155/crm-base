<script src="/js/plugins/jquery.flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="/js/plugins/jquery.flot/jquery.flot.orderBars.js" type="text/javascript"></script>
<script>
$(function()
{
	var rowid = <?php echo $this->req_rowid ?>;
	var year_select = $('.year');
	function fetch(year)
	{
		$('body').append('<div class="overlay-loading data-loading"></div>');
		$.ajaxShort({
			dataType:'JSON',
			data:{
				client_id:rowid,
				year:year,
				action:'<?php echo $this->plugin_name ?>',
			},
			success:function(response)
			{
				$('.data-loading').remove();
				var plot = $.plot('.plot',response.data,
					{
						xaxis:{
							ticks: [
								[0,''],[1,'Янв.'],[2,'Фев.'],[3,'Март'],[4,'Апр.'],[5,'Май'],[6,'Июнь'],[7,'Июль'],[8,'Авг.'],[9,'Сен.'],[10,'Окт.'],[11,'Ноя.'],[12,'Дек.']
							]
						},
						yaxis:{
							tickSize:response.yaxis_tick,
							min:0,
						},
						lines:{
							show:false
						},
						bars:{
							show:true,
							barWidth: response.bars_width
						},
						grid:{
							hoverable:true
						}
					}
				);
				$('.plot-tooltip').remove();
				$("<div class='plot-tooltip'></div>").appendTo("body");
				$(".plot").bind("plothover", function (event, pos, item)
				{
					if (item)
					{
						$(".plot-tooltip").html(item.series.label+': <strong>'+item.datapoint[1]+'€</strong>');
						$(".plot-tooltip").css({top: item.pageY - 20, left: item.pageX});
						$(".plot-tooltip").show();
					}
					else
						$(".plot-tooltip").hide();
				});
			}
		});
	};
	year_select.on('change',function(event)
	{
		fetch(this.value);
	});
	fetch('<?php echo date("Y") ?>');
	var stat_grid = new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		defaultPage:false,
		name:'client_delivery',
		table:'Статистика_Контрагенты_ДоставкаРФ',
		id:'Клиенты_Код',
		useLs:false,
		navGrid:false,
		hideBottomNav:true,
		orderName:'Год,Месяц',
		tableSort:'ASC',
		permFilter:{
			groupOp:"AND",rules:[
				{field:"Год",op:"eq",data:year_select.val()},
			]
		},
		subgridpost:{
			mainid:rowid,
			mainidName:'Клиенты_Код'
		},
		cn:[
			"Тариф","Месяц","Сумма"
		],
		cm:[
			{
				name: "Тариф_Название",index:"Тариф_Название",width:200
			},

			{
				name: "Months",index:"Months",width:200
			},

			{
				name: "Сумма",index:"Сумма",width:50,formatter:floatFormatter,search:false
			}
		],
		options:{
			rowNum:1000,
			cellEdit:false,
			shrinkToFit:true,
			height:310
		}
	});
})
</script>
<div>
	<div style="display:inline-block;width:calc(100% - 9px);margin-left:5px;margin-top:15px;margin-bottom:15px;">
		<select class="select2me year" data-selectops='{"allowClear": false}' data-placeholder="Год" style="width:100%">
			<?php
				echo $this->Core->get_lib_html([
					'tname'=>'Статистика_Контрагенты_ДоставкаРФ',
					'filters'=>[['field'=>'Клиенты_Код','op'=>'eq','data'=>$this->req_rowid]],
					'selected'=>date("Y"),
					'with_id'=>false,
					'cache'=>false,
					'order'=>1,
					'fields'=>['Год'],
				])
			?>
		</select>
	</div>
	<div class="plot"></div>
	<hr class="ni-divider" />
	<div >
		<table class='gridclass' id='client_delivery'></table>
	</div>
</div>
