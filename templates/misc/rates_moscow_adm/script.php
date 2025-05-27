<script type="text/javascript">
document.title = 'Тарифы';
$(function()
{
var rate_params;
var rate_clients;
var rate_description_form_proto;
function set_rate_description(rate_id)
{
	$('input[type="hidden"]').val(rate_id);
	$.ajaxShort({
		dataType:'JSON',
		data:{
			responseType:'single',
			action:'view',
			query:'SELECT Описание FROM Тарифы2 WHERE Код = ' + rate_id
		},
		success:function(data)
		{
			if(data['Описание'] != null)
				$('#rate-description').val(data['Описание']);
			else
				$('#rate-description').val('');
		}
	});
	if(typeof rate_description_form_proto === typeof undefined)
		rate_description_form_proto = new $ajaxForm({ name:'rate-description-form', table:'Тарифы2' });
};
function get_child_rates(rate_id)
{
	$.ajaxShort({
		dataType:'HTML',
		data:{
			responseType:'options',
			action:'view',
			tname:'Тарифы2',
			filters:{
				groupOp:'AND',
				parent:{
					field:"Родитель",
					op:"eq",
					data:rate_id
				}
			}
		},
		success:function(data)
		{
			$('#child-rate').children().remove();
			if(data.length > 17)
				$('#child-rate').append(data);
			$('#child-rate').trigger('change.select2');
		}
	});
};
function process_rate_prices(rate_id)
{
	$.ajaxShort({
		dataType:'HTML',
		data:{
			action:'rates_prices',
			rate_id:rate_id
		},
		success:function(data)
		{
			var html = $.parseHTML(data);
			$('.init-none').css('display','none');
			$('#rate-volume,#rate-weight,#rate-cats').empty();
			$.each(html,function(i,el)
			{
				if(this.id || this.tagName === 'STYLE')
				{
					if(this.id === 'cats-table')
					{
						$('.rate-data-dummy').remove();
						$('#rate-cats').append(this);
						$('#rate-cats').closest('div').css('display','block');
						dataSelect2.call(this,$('.select2me',this));
						new $ajaxForm({
							name:'rate-cats',
							table:'Тарифы2_Категории',
							table_form:true,
							add:true,
							del:true,
							add_nav:true,
							sub_form:true,
							subid:'Тариф_Код'
						});
					}
					else if(this.id === 'weight-table')
					{
						$('#rate-weight').append(this);
						dataSelect2.call(this,$('.select2me',this));
						new $ajaxForm({
							name:'rate-weight',
							table:'Тарифы2_Вес',
							table_form:true,
							add:true,
							del:true,
							add_nav:true,
							sub_form:true,
							subid:'Тариф_Код'
						});
					}
					else if(this.id === 'volume-table')
					{
						$('.rate-data-dummy').remove();
						$('#rate-volume').append(this);
						$('#rate-volume').closest('div').css('display','block');
						new $ajaxForm({
							name:'rate-volume',
							table:'Тарифы2_Объем',
							table_form:true,
							add:true,
							del:true,
							add_nav:true,
							sub_form:true,
							subid:'Тариф_Код'
						});
					}
				}
			});
		}
	});
};
$('#base-rate,#child-rate').on('change',function(event)
{
	if($('.active-rate-select').length > 0)
		$('.active-rate-select').removeClass('active-rate-select');
	$(this).data('select2').$selection.addClass('active-rate-select');


	$('#button-new-child-rate').removeAttr('disabled');
	var child = false;
	var null_value = false;
	if(!this.value)
		null_value = true;
	if(this.id === 'child-rate')
		child = true;
	if(child)
	{
		$('#button-del-child-rate').removeAttr('disabled');
		if(null_value)
		{
			$('#base-rate').trigger('change');
			$('#button-del-child-rate').attr('disabled',true);
			return;
		}
	}
	process_rate_prices(this.value);
	if(/^base.*/.test(this.id))
		get_child_rates(this.value);
	set_rate_description(this.value);
	if(child)
	{
		rate_params.delOpts = false;
		rate_params.onCellSelect = false;
	}
	else
	{
		rate_params.delOpts = true;
		rate_params.onCellSelect = true;
	}

	rate_params.renew_external_sub(this.value);
	rate_clients.renew_external_sub(this.value);
	if(child)
		rate_params.remove_blank_row();
})
.on('select2:unselecting',function(event)
{
	$(this).val([]).trigger('change');
	event.preventDefault();
});
$('.rates-button-navigation').on('click',function(event)
{
	var base_rate_value = $('#base-rate').val();
	var child_rate_value = $('#child-rate').val();
	switch(this.id)
	{
		case 'button-new-rate':
		$.confirmHTML({
			yes:'Создать',
			no:'Закрыть',
			title:'Создание тарифа',
			html:function()
			{
				return $.genHTML({type:'input-top-label',options:{ label_text:'Название тарифа',name:'Название',required:true }});
			},
			done_func:function()
			{
				var name = $(this).find('input[name="Название"]').val();
				if(!name)
					return false;
				$.ajaxShort({
					data:{
						action:'edit',
						query:"INSERT INTO Тарифы2 (Название) VALUES ('"+ name +"')"
					},
					success:function()
					{
						location.reload();
					}
				});
			}
		});
			break;
		case 'button-new-child-rate':
			$.confirmHTML({
				yes:'Создать',
				no:'Закрыть',
				title:'Создание дочернего тарифа',
				html:function()
				{
					return $.genHTML({type:'input-top-label',options:{ label_text:'Название тарифа',name:'Название',required:true }});
				},
				done_func:function()
				{
					var name = $(this).find('input[name="Название"]').val();
					if(!name)
						return false;
					$.ajaxShort({
						data:{
							action:'add',
							query:"exec SP_Тарифы_Копирование_тарифа " + base_rate_value + ",'" + name + "'"
						},
						success:function()
						{
							location.reload();
						}
					});
				}
			});
			break;
		case 'button-del-child-rate':
			$.ajaxShort({
				data:{
					action:'edit',
					query:"DELETE FROM Тарифы2 WHERE Код = "+ child_rate_value
				},
				success:function()
				{
					location.reload();
				}
			});
			break;
		case 'button-open-category':
			var empty_proto = new jqGrid$();
			empty_proto.plugin({dialog_width:600,dialog_height:600,name:'goods_cats',id:'',data:''});
			break;
	}
});
rate_params = new jqGrid$({
	defaultPage:false,
	subgrid:true,
	main:false,
	name:'rate-params',
	table:'Тарифы2_Параметры',
	id:'Код',
	resize:false,
	tableSort:'ASC',
	hideBottomNav:true,
	useLs:false,
	beforeSubmitCell:true,
	onCellSelect:true,
	subgridpost:{
		mainid:'',
		mainidName:'Тариф_Код'
	},
	delOpts:true,
	cn:[
		"Код","Тариф_Код","Название параметра","Значение параметра","Примечание"
	],
	cm:[
		{
			name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
		},

		{
			name: "Тариф_Код",index:"Тариф_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
		},

		{
			name: "Параметр_Название",index:"Параметр_Название",width:150
		},

		{
			name: "Параметр_Значение",index:"Параметр_Значение",width:150
		},

		{
			name: "Примечание",index:"Примечание",width:300,align:"left",
			cellattr:textAreaCellAttr,
			edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
		}
	],
	options:{
		height:295,
		cellEdit:true,
		shrinkToFit:true,
		caption:'Параметры тарифа'
	}
});

rate_clients = new jqGrid$({
	defaultPage:false,
	subgrid:true,
	main:false,
	name:'rate-clients',
	table:'Контрагенты_Тарифы',
	id:'Код',
	resize:false,
	tableSort:'ASC',
	hideBottomNav:true,
	useLs:false,
	beforeSubmitCell:true,
	onCellSelect:true,
	subgridpost:{
		mainid:'',
		mainidName:'Тариф_Код'
	},
	delOpts:true,
	cn:[
		"Код","Тариф_Код","Контрагент"
	],
	cm:[
		{
			name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
		},

		{
			name: "Тариф_Код",index:"Тариф_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
		},

		{
			name: "Клиент_Код",index:"Клиент_Код",width:200,stype:'select',formatter:'select',
			formatoptions:{
				value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Клиенты_EN']) ?>
			},
			searchoptions:{
				value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Клиенты_EN'})}
			},
			editoptions:{
				dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_Клиенты_EN'},opts,this); }
			},
			cellattr:function(rowId, val, rawObject, cm , rdata )
			{
				if (rawObject['Весь_состав'] == 1)
					return "style='background-color:#4ca64c;font-weight:bold;color:#FFF'";
			}
		},
	],
	options:{
		height:295,
		cellEdit:true,
		shrinkToFit:true,
		caption:'Клиенты тарифа'
	}
});

});

</script>
