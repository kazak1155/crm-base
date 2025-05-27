<script src="/js/plugins/jquery.flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="/js/plugins/jquery.flot/jquery.flot.orderBars.js" type="text/javascript"></script>
<script>
$(function()
{
	function fetch(haul_id)
	{
		$('body').append('<div class="overlay-loading data-loading"></div>');
		var data = new Object();
		data.action = '<?php echo $this->plugin_name ?>';
		if(haul_id)
		{
			data.haul_id = haul_id;
			stat_grid.grid_p.postData.tname = stat_grid.grid_p.postData.tname.source_table = 'Статистика_Рейсы_Тарифы';
			stat_grid.grid_p.postData.perm_filters = JSON.stringify({"groupOp":"AND","rules":[{"field":"Рейсы_Код","op":"eq","data":haul_id}]});
			stat_grid.grid_element.trigger("reloadGrid",{current:true});
		}
		else
		{
			if(typeof stat_grid !== typeof undefined && stat_grid.hasOwnProperty('grid_p') && stat_grid.grid_p.postData.hasOwnProperty('perm_filters'))
			{
				delete stat_grid.grid_p.postData.perm_filters;
				stat_grid.grid_p.postData.tname = stat_grid.grid_p.postData.source_table = 'Статистика_Рейсы_Тарифы_Текущие';
				stat_grid.grid_p.postData._search = false;
				stat_grid.grid_element.trigger("reloadGrid",{current:true});
			}
		}
		$.ajaxShort({
			dataType:'JSON',
			data:data,
			success:function(response)
			{
				$('.data-loading').remove();
				var plot = $.plot('.plot',response.data,
					{
						xaxis:{
							ticks:response.xaxis_ticks,
							autoscaleMargin:1,
							tickDecimals:0
						},
						yaxis:{
							tickSize:response.yaxis_tick,
						},
						grid: {
							hoverable:true
						},
						series:
						{
							lines:{
								show:false
							},
							bars:{
								show:true,
								barWidth: 0.3
							}
						}
					}
				);
				$('.plot-tooltip').remove();
				$("<div class='plot-tooltip'></div>").appendTo("body");
				$(".plot").bind("plothover", function (event, pos, item)
				{
					if (item)
					{
						$(".plot-tooltip").html('По тарифу "'+item.series.label+'" - <strong>'+item.datapoint[1]+'€</strong>');
						$(".plot-tooltip").css({top: item.pageY - 20, left: item.pageX - $(".plot-tooltip").outerWidth()});
						$(".plot-tooltip").show();
					}
					else
						$(".plot-tooltip").hide();
				});
			}
		});
	};
	$('.haul_select').on('change',function(event)
	{
		fetch(this.value);
	})
	fetch();
	var stat_grid = new jqGrid$({
		defaultPage:false,
		name:'tig_haul_delivery',
		table:'Статистика_Рейсы_Тарифы_Текущие',
		id:'Рейс',
		useLs:false,
		navGrid:false,
		hideBottomNav:true,
		cn:[
			"Рейс","Тариф","Сумма",
		],
		cm:[
			{
				name: "Рейс",index:"Рейс",width:200
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
			shrinkToFit:true
		}
	});
});
</script>
<div>
	<select class="select2me haul_select" data-selectops='{"width":"100%"}' data-placeholder="Выберите рейс">
		<?php echo $this->Core->get_lib_html(['tname'=>'Б_Рейсы','selected'=>$_REQUEST['haul'],'cache'=>false,'order_by'=>'DESC']);?>
	</select>
	<hr class="ni-divider" />
	<div class="plot"></div>
	<hr class="ni-divider" />
	<div>
		<table class='gridclass' id='tig_haul_delivery'></table>
	</div>
</div>
