<script src="/js/plugins/jquery.flot/jquery.flot.min.js" type="text/javascript"></script>
<script>
$(function()
{
	var rowid = <?php echo $this->req_rowid ?>;
	$('.year').on('change',function(event)
	{
		$.ajaxShort({
			dataType:'JSON',
			data:{
				client_id:rowid,
				year:this.value,
				action:'<?php echo $this->plugin_name ?>',
			},
			success:function(response)
			{
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
						lines: {
							show: true
						},
						points: {
							show: true
						},
						grid:{
							clickable:true,
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
						var x = item.series.xaxis.ticks[item.datapoint[0]].label;
						var y = item.datapoint[1].toFixed(2);
						$(".plot-tooltip").html(item.series.label + " на " + x + " = " + y);
						$(".plot-tooltip").css({top: item.pageY - 20, left: item.pageX - $(".plot-tooltip").outerWidth()});
						$(".plot-tooltip").show()
					}
					else
						$(".plot-tooltip").hide();
				});
				$(".plot").bind("plotclick", function (event, pos, item)
				{
					if(item)
					{
						if(item.series.label === 'Средний объем')
						{
							plot.unhighlight();
							plot.highlight(item.series, item.datapoint);
							var title = 'Заказы «'+ agents.grid_element.getRowDataRaw(agents.grid_p.selrow)['Название_RU']+'»';
							title += ' за '+item.series.xaxis.ticks[item.datapoint[0]].label;
							title += ' ' + $('select[name="Год"]').val() + ' года';
							agents.plugin({
								name:'plot_avg_volume',
								data:{
									month:item.datapoint[0],
									year:$('select[name="Год"]').val()
								},
								dialog_title:title,
								dialog_width:800,
								dialog_height:400,
								dialog_refresh_grid:false
							});
						}
					}
				});
			}
		});
	});
	var statistics_details = new jqGrid$({
		defaultPage:false,
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'client_acitivity_details',
		table:'Статистика_Контрагенты_Активность',
		id:'Клиенты_Код',
		orderName:'Год,Месяц',
		tableSort:'ASC',
		filterToolbar:true,
		useLs:false,
		navGrid:false,
		hideBottomNav:true,
		footer:[
			{col:'Сумм_Зак',calc:'sum'},
			{col:'Сумм_Объем',calc:'sum'},
			{col:'Сумм_Вес',calc:'sum'},
			{col:'Сумм_Ср',calc:'sum'}
		],
		subgridpost:{
			mainid:rowid,
			mainidName:'Клиенты_Код'
		},
		cn:[
			"Код","Год","Месяц","Σ заказов","Σ объем","Σ вес","Σ среднее"
		],
		cm:[
			{
				name: "Клиенты_Код",index:"Клиенты_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Год",index:"Год",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib([
							'tname'=>'Статистика_Контрагенты_Активность',
							'fields'=>['Год'],
							'filters'=>[['field'=>'Клиенты_Код','op'=>'eq','data'=>$this->req_rowid]],
							'with_id'=>false,
							'cache'=>false,
							'order'=>1,
						]) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,
					attr:{'data-search':JSON.stringify({
						tname:'Статистика_Контрагенты_Активность',
						flds:['Год'],
						order:1,
						id:true,
						id_only:true
					})}
				}
			},

			{
				name: "Months",index:"Months",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib([
							'tname'=>'Статистика_Контрагенты_Активность',
							'fields'=>['Months'],
							'filters'=>[['field'=>'Клиенты_Код','op'=>'eq','data'=>$this->req_rowid]],
							'with_id'=>false,
							'cache'=>false,
							'order'=>1,
						]) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,
					attr:{'data-search':JSON.stringify({
						tname:'Статистика_Контрагенты_Активность',
						flds:['Months'],
						order:1,
						id:true,
						id_only:true
					})}
				}
			},

			{
				name: "Сумм_Зак",index:"Сумм_Зак",width:50,formatter:floatFormatter,search:false
			},

			{
				name: "Сумм_Объем",index:"Сумм_Объем",width:50,formatter:floatFormatter,search:false
			},

			{
				name: "Сумм_Вес",index:"Сумм_Вес",width:50,formatter:floatFormatter,search:false
			},

			{
				name: "Сумм_Ср",index:"Сумм_Ср",width:50,formatter:floatFormatter,search:false
			}

		],
		options:{
			rowNum:1000,
			cellEdit:false,
			shrinkToFit:true,
			height:255
		}
	})
})
</script>
<div>
	<div style="display:inline-block;width:calc(100% - 9px);margin-left:5px;margin-top:15px;margin-bottom:15px;">
		<select class="select2me year" data-selectops='{"allowClear": false}' data-placeholder="Год" style="width:100%">
			<?php
				echo $this->Core->get_lib_html([
					'tname'=>'Статистика_Контрагенты_Активность',
					'with_id'=>false,
					'cache'=>false,
					'order'=>1,
					'fields'=>['год'],
				])
			?>
		</select>
	</div>
	<div class="plot"></div>
	<hr class="ni-divider" />
	<div >
		<table class='gridclass' id='client_acitivity_details'></table>
	</div>
</div>
