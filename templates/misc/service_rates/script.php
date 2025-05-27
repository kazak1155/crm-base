<script>
function process_service_rate(rate_id,params_tpl,row_data)
{
	$.ajaxShort({
		dataType:'HTML',
		data:{
			action:'service_delivery',
			rate_id:rate_id,
			params_tpl:params_tpl.length > 0 ? params_tpl : false
		},
		success:function(data)
		{
			var html = $.parseHTML(data);
			$('.rate-data').empty();
			$('.rate-data').append(html);
			$.each($('.rate-data').find('form'),function(i,el)
			{
				switch (this.id) {
					case 'rate-cats':
						dataSelect2.call(this,$('.select2me',this));
						new $ajaxForm({
							name:'rate-cats',
							table:'Тарифы2_Категории',
							table_form:true,
							t_head:['','','Категория груза','Цена','Валюта','Коэф. веса'],
							add:true,
							del:true,
							add_nav:true,
							sub_form:true,
							subid:'Тариф_Код'
						});
						break;
					case 'rate-volume':
						new $ajaxForm({
							name:'rate-volume',
							table:'Тарифы2_Объем',
							table_form:true,
							t_head:['','','Цена','Плотность min','Плотность max'],
							add:true,
							del:true,
							add_nav:true,
							sub_form:true,
							subid:'Тариф_Код'
						});
						break;
					case 'rate-weight':
						dataSelect2.call(this,$('.select2me',this));
						new $ajaxForm({
							name:'rate-weight',
							table:'Тарифы2_Вес',
							table_form:true,
							t_head:['','','Категория груза','Цена за килограмм'],
							add:true,
							del:true,
							add_nav:true,
							sub_form:true,
							subid:'Тариф_Код'
						});
						break;
					case 'rate-params':
						var timeout;
						$('input',this).on('change keyup',function(e)
						{
							var self = this;
							if(typeof timeout !== typeof undefined)
								clearTimeout(timeout);
							timeout = setTimeout(function(){
								$.ajaxShort({
									data:{
										action:'edit',
										query:'UPDATE [dbo].[Тарифы2_Параметры] SET [Параметр_Значение] = '+ self.value +' WHERE [Параметр_Название] = \''+ self.name +'\' AND [Тариф_Код] = ' + rate_id
									}
								});
							},300);
						});
						break;

					default:break;

				}
			});
		}
	});
};
$(function()
{
	$('#service').change(function(event)
	{
		var new_rate_b = document.getElementById('new-rate')
		if(this.value == 41)
			new_rate_b.disabled = true;
		else
			new_rate_b.disabled = false;
		rates_tree.renew_external_sub(this.value);
		$('.rate-data').empty();
	});
	$('.rates-button-navigation').on('click',function(event)
	{
		var dummy = new jqGrid$();
		switch (this.id) {
			case 'new-rate':
				var grid = document.getElementById('rates-tree');
				var service_id = document.getElementById('service').value;
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
								action:'add',
								query:"INSERT INTO Тарифы2 (Название,Услуги_Код) VALUES ('"+name+"',"+service_id+")"
							},
							success:function()
							{
								$(grid).trigger("reloadGrid",{current:true});
							}
						});
					}
				});
				break;
			case 'goods-cats':
				dummy.plugin({dialog_width:800,dialog_height:600,name:'category_lib',id:'',data:''});
				break;
			case 'ita-carrier-prm':
				dummy.plugin({dialog_width:800,dialog_height:600,name:'ita_params',id:'',data:''});
				break;
			case 'ita-prices':
				dummy.plugin({dialog_width:1000,dialog_height:600,name:'ita_prices',id:'',data:''});
				break;
			default:break;
		}
	});
	var rates_tree = new jqGrid$({
		name:'rates-tree',
		hideBottomNav:true,
		defaultPage:false,
		title:'Тарифы',
		main:true,
		resize:false,
		useLs:false,
		table:'Тарифы2_tree',
		navGrid:true,
		delOpts:false,
		subgrid:true,
		subgridpost:{
			mainid: '',
			mainidName:'Услуги_Код'
		},
		navGridOptions:{edit:true},
		contextMenuFileTree: true,
		no_excel:true,
		no_sort:true,
		no_etc:true,
		adjust_height_els:105,
		contextMenuItems:{
			add_child:{
				name:'Создать дочерний тариф',
				visible:function(name,other)
				{
					var sel_row = $(this).getRowData($(this)[0].p.selrow);
					if(sel_row['Услуги_Код'] != 41)
						return false;
					if(sel_row['isLeaf'] === 'true')
						return false;
					return true;
				},
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-plus';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
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
									query:"exec SP_Тарифы_Копирование_тарифа " + rowid + ","+ rowObjectFormatted['Услуги_Код'] +",'" + name + "'"
								},
								success:function()
								{
									$(grid).trigger("reloadGrid",{current:true});
								}
							});
						}
					});
				}
			},
			delete_child:
			{
				name:'Удалить дочерний тариф',
				visible:function(name,other)
				{
					var sel_row = $(this).getRowData($(this)[0].p.selrow);
					if(sel_row['Услуги_Код'] != 41)
						return false;
					if(sel_row['isLeaf'] === 'false')
						return false;
					return true;
				},
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-trash';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$(this).delGridRow(rowid, {delData:{ tid:'Код',tname:'Тарифы2' }});
				}
			},
			delete:
			{
				name:'Удалить тариф',
				visible:function(name,other)
				{
					var sel_row = $(this).getRowData($(this)[0].p.selrow);
					if(sel_row['Услуги_Код'] == 41)
						return false;
					return true;
				},
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-trash';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$(this).delGridRow(rowid, {delData:{ tid:'Код',tname:'Тарифы2' }});
				}
			},
			rate_clients:
			{
				name:'Клиенты тарифа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-users';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					gridPseudo.plugin({
						name:'clients',
						dialog_title:'Клиенты тарифа "'+rowObject['Название']+'"',
						dialog_width:600,
						dialog_height:300,
						dialog_refresh_grid:false
					});
				}
			}
		},
		id:'Код',
		cn:[
			'Код','Родитель','isLeaf','expanded','','Услуга','Название','Описание','Default','Tpl'
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "parent",index:"parent",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "isLeaf",index:"isLeaf",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "expanded",index:"expanded",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name:'expand',index:'expand',width:50,editable:false
			},

			{
				name: "Услуги_Код",index:"Услуги_Код",width:200,sortable:false,formatter:'select',hidden:true,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Назнач_Платеж']) ?>
				},
			},

			{
				name: "Название",index:"Название",width:250,sortable:false
			},

			{
				name: "Описание",index:"Описание",width:250,align:"left",sortable:false,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Default",index:"Default",width:70,gtype:'checkbox',editable:false,sortable:false
			},

			{
				name: "Tpl_name",index:"Tpl_name",width:50,sortable:false,hidden:true
			}
		],
		events:{
			loadComplete:function()
			{
				$('.tree-minus').removeClass('ui-icon ui-icon-triangle-1-s tree-minus treeclick').addClass('fa fa-lg fa-arrow-right');
				$('.tree-leaf').removeClass('ui-icon ui-icon-triangle-1-s tree-minus treeclick').addClass('fa fa-lg fa-chevron-up');
			},
			onSelectRow:function(rowid,status,e)
			{
				var row_data = $(this).getRowData(rowid);
				if(row_data['parent'])
				{
					var parent = $(this).getRowData(row_data['parent']);
					row_data.Tpl_name = parent.Tpl_name;
				}
				if(e.type === "contextmenu")
					return;
				$('.row-highlight').removeClass('row-highlight');
				$('tr#'+rowid).toggleClass('row-highlight');
				process_service_rate(rowid,row_data.Tpl_name,row_data);
			}
		},
		options:{
			hoverrows:false,
			shrinkToFit: true,
			treeGrid: true,
			treeGridParent_id: 'parent',
			treeGridModel: 'adjacency',
			treeReader: {
				parent_id_field:'parent'
			},
			ExpandColumn: 'expand',
			ExpandColClick:false
		}
	});
});

</script>
