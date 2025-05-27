<?php //include_once "barcode.php"; ?>
<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'orders',
		table:'Форма_Заказы',
		tableQuery:'Заказы',
		title:'Заказы',
		id:'Код',
		ref_db:'tig50',
		tableSort:'ASC',
		filterToolbar:true,
		beforeSubmitCell:true,
		formpos:false,
		delOpts:true,
		footer:[
			{col:'Объем',calc:'sum'},
			{col:'Вес',calc:'sum'},
			{col:'ИнСклад_Мест',calc:'sum'}
		],
		navGridOptions:{search:true,add:true,edit:true},
		editFormOptions:{
			beforeShowForm:function(formid)
			{
				$('#tr_Примечание_состав').remove();
			}
		},
		addFormOptions:{
			beforeSubmit:function(postdata, formid)
			{
				postdata.tname = 'Заказы';
				postdata.getid = true;
				if(postdata['Примечание_состав'].length > 0)
				{
					this.composition_data = postdata['Примечание_состав'];
					delete postdata['Примечание_состав'];
				}
				return[true];
			},
			afterComplete:function(response, postdata, formid)
			{
				var new_id = parseInt(response.responseText),grid = this;
				var composition = this.composition_data;
				delete this.composition_data;
				if(isNaN(new_id))
					return $.alert('Что-то пошло не так!');
				if(composition)
				{
					$.ajaxShort({
						data:{
							oper:'add',
							tname:'Заказы_Состав',
							'Заказы_Код':new_id,
							'Примечание':composition
						},
						success:function()
						{
							var perm_filters_buttons = $('.ui-pg-button.ui-state-hover-filter','#gview_orders').not('#clearFilters_orders_top'),button;
							for(var i = 0;i < perm_filters_buttons.length;i++)
							{
								button = perm_filters_buttons[i];
								$(button).trigger('click',false);
							}
							$('#clearFilters_orders_top').trigger('click',false);
							$('#gs_Код').val(new_id);
							setTimeout(function(){
								grid.triggerToolbar();
							},0);
						}
					})
				}
				else
				{
					var perm_filters_buttons = $('.ui-pg-button.ui-state-hover-filter','#gview_orders').not('#clearFilters_orders_top'),button;
					for(var i = 0;i < perm_filters_buttons.length;i++)
					{
						button = perm_filters_buttons[i];
						$(button).trigger('click',false);
					}
					$('#clearFilters_orders_top').trigger('click',false);
					$('#gs_Код').val(new_id);
					setTimeout(function(){
						grid.triggerToolbar();
					},0);
				}
			}
		},
		delFormOptions:{
			delData:{
				tid:'Код',
				tname:'Заказы'
			},
			beforeShowForm:function(form)
			{
				var del = $(this).jqGrid('getCell',this.p.selrow,'Удалено');
				if(del == 1)
					$(form).append('<tr><td style="color:red;font-weight:bold">Заказ будет удален окончательно!</td></tr>')
			}
		},
		contextMenuFileTree: true,
		contextMenuFileTreeItems:{
			default:true,
			ft_ex:{
				name:'FT Export',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-folder-open';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.file_Tree({
						s_prm:{
							reference:gridPseudo.location,
							tname:'Заказы',
							folder:'ft_ex_docs',
							rowid:rowid
						}
					});
				}
			},
			site_docs:{
				name:'Документы на сайт',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-folder-open';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.file_Tree({
						s_prm:{
							reference:gridPseudo.location,
							tname:'Заказы',
							folder:'site',
							rowid:rowid
						}
					});
				}
			}			
			
			/*
			formaro_docs:{
				name:'Formaro',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-folder-open';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.file_Tree({
						s_prm:{
							reference:gridPseudo.location,
							tname:'Заказы',
							folder:'formaro_docs',
							rowid:rowid
						}
					});
				}
			},
			mgsystem_docs:{
				name:'Mg System',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-folder-open';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.file_Tree({
						s_prm:{
							reference:gridPseudo.location,
							tname:'Заказы',
							folder:'mg_docs',
							rowid:rowid
						}
					});
				}
			}
			*/
		},
		contextMenuItems:{
			mail_fabric_req:{
				name:'Запрос на фабрику',
				disabled:function(name,other)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Статус_Код'] != 0 && sel_row['Статус_Код'] != 1 && sel_row['Статус_Код'] != 20)
						return true;
				},
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-envelope';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Фабрика_Страна'] == 'ITA'){
						mail_id = 38
					}
					else
					{
						mail_id = 70
					}
					rowObject['mail_id'] = mail_id;
					rowObject['formatted_data'] = new Object();
					rowObject['formatted_data']['Клиенты_Код'] = rowObjectFormatted['Клиенты_Код'];
					rowObject['formatted_data']['Фабрики_Код'] = rowObjectFormatted['Фабрики_Код'];
					rowObject['formatted_data']['Номер_Заказа'] = rowObjectFormatted['Номер_Заказа'];
					gridPseudo.plugin({
						frame_type:'email',
						dialogClassName:'email-dialog',
						id:rowid,
						data:rowObject,
						dialog_refresh_grid:false
					});
				}
			},
			mail_client_courier:{
				name:'Заказ отдан курьеру',
				disabled:function(name,other)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if((sel_row['Транспортника_Код'] =='') || (sel_row['Транспортника_Код'] == 'NULL'))
						return true;
				},
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-envelope';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					mail_id = 78;

					rowObject['mail_id'] = mail_id;
					rowObject['formatted_data'] = new Object();
					rowObject['formatted_data']['Клиенты_Код'] = rowObjectFormatted['Клиенты_Код'];
					rowObject['formatted_data']['Фабрика'] = rowObjectFormatted['Фабрика'];
					rowObject['formatted_data']['Фабрики_Код'] = rowObjectFormatted['Фабрики_Код'];
					rowObject['formatted_data']['Номер_Заказа'] = rowObjectFormatted['Номер_Заказа'];
					rowObject['formatted_data']['Транспортник'] = rowObjectFormatted['Транспортник'];
					rowObject['formatted_data']['Транспортника_Код'] = rowObjectFormatted['Транспортника_Код'];
					gridPseudo.plugin({
						frame_type:'email',
						dialogClassName:'email-dialog',
						id:rowid,
						data:rowObject,
						dialog_refresh_grid:false
					});
				}
			},
			mail_mailto:{
				name:'Написать письмо',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-envelope';
				},
				items:{
					mailto_client:{
						name : "Клиент",
						icon: function(opt, $itemElement, itemKey, item)
						{
							return 'context-menu-icon-fa fa-user';
						},
						custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
						{
							rO = new Object();
							rO['mail_id'] = 57;
							rO['formatted_data'] = rowObjectFormatted;
							gridPseudo.plugin({
								frame_type:'email',
                                dialog_height:650,
								dialogClassName:'email-dialog',
								id:rowid,
								data:rO,
								dialog_refresh_grid:false
							});
						}
					},
					mailto_fabric:{
						name : "Фабрика",
						disabled:function(name,other)
						{
							var sel_id = $(this)[0].p.selrow;
							var sel_row = $(this).getRowData(sel_id);
							if(!sel_row['Фабрики_Код'])
								return true;
						},
						icon: function(opt, $itemElement, itemKey, item)
						{
							return 'context-menu-icon-fa fa-industry';
						},
						custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
						{
							rowObject['mail_id'] = 58;
							rowObject['formatted_data'] = rowObjectFormatted;
							gridPseudo.plugin({
								frame_type:'email',
                                dialog_height:650,
								dialogClassName:'email-dialog',
								id:rowid,
								data:rowObject,
								dialog_refresh_grid:false
							});
						}
					}
				}
			},
			expences:{
				name:'Расходы',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-book';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var text = rowObject['Номер_Заказа'].length > 0 ? rowObject['Номер_Заказа'] : 'Не установлено';
					gridPseudo.plugin({
						name:'expences',
						dialog_title:'Расходы заказа: '+ text,
						dialog_width:800,
						dialog_height:300,
						dialog_refresh_grid:false
					});
				}
			},			
			journal:{
				name:'Журнал',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-book';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var text = rowObject['Номер_Заказа'].length > 0 ? rowObject['Номер_Заказа'] : 'Не установлено';
					gridPseudo.plugin({
						name:'journal',
						dialog_title:'Журнал заказа: '+ text,
						dialog_width:800,
						dialog_height:300,
						dialog_refresh_grid:false
					});
				}
			},
			journal_prim:{
				name:'Журнал примечаний',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-book';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var text = rowObject['Номер_Заказа'].length > 0 ? rowObject['Номер_Заказа'] : 'Не установлено';
					gridPseudo.plugin({
						name:'journal_note',
						dialog_title:'Журнал примечаний заказа: '+ text,
						dialog_width:700,
						dialog_height:500,
						dialog_refresh_grid:false,
						dialog_custom_opts:{
							dialog_opts:
							{
								draggable:true,
								modal:false
							},
							dialog_extend_opts:{
								minimizable:true
							}
						}
					});
				}
			},
			paking:{
				name:'Пакинг',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cubes';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					gridPseudo.plugin({
						name:'paking',
						dialog_title:'Пакинг заказа: "'+rowObject['Номер_Заказа']+'"',
						dialog_width:600,
						dialog_height:520,
						dialog_refresh_grid:true
					});
				}
			},
			order_ready:{
				name:'Весь состав',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cubes';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this,value = rowObject['Весь_состав'] == 0 ? 1 : 0;
					$.ajaxShort({
						data:{
							oper:'edit',
							tname:'Заказы',
							id:rowid,
							tid:'Код',
							'Весь_состав':value
						},
						success:function(){
							$(grid).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			/*print_barcode:{
				name:'Печать баркодов',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-barcode';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					print_barcode.call(this,e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted);
				}
			},
			*/
			paking_report:{
				name:'Отчет "Пакинг заказа"',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-file-excel-o';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var excel_obj = new Object();
					excel_obj.reference = gridPseudo.location;
					excel_obj.type = 'excel';
					excel_obj.qry = {
						tname:'Отчет_Заказы_Пакинг',
						type:'SELECT',
						fields:[
							{name:'Фабрика'},
							{name:'Импортер'},
							{name:'MRN'},
							{name:'Номер_Инвойса as [Инвойс]'},
							{name:'Сумма_Инвойса as [Сумма инвойса]'},
							{name:'Пакинг_Мест as [Мест]'},
							{name:'Пакинг_Вес as [Вес]'},
							{name:'НомерМашины as [Номер машины]'},
							{name:'Примечание_пакинг as [Примечание]'}
						]
					};
					excel_obj.qw = {
						groupOp:'AND',
						rules:[{data:rowObjectFormatted['Рейсы_Код'],field:'Рейсы_Код',op:'eq'}]
					};
					get_file_url(excel_obj,'Рейс '+ rowObject['Рейсы_Код'] +' пакинг');
				}
			},
			wh_report:{
				name:'Отчет "Разгрузка на склад"',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-file-word-o';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.confirmHTML({
						yes:'Сформировать',
						no:'Закрыть',
						html:function()
						{
							var wrapper = document.createElement('div');
							wrapper.className = 'float_label_wrapper';
							wrapper.style.width = '98%';
							$(wrapper).append('<label style="margin-right:10px">Рейс<label>')
							var haul = document.createElement('select');
							haul.style.width = 'calc(100% - 50px)';
							$(haul).append("<?php echo $this->Core->get_lib_html(['tname'=>'Б_Рейсы']); ?>");
							$(haul).find('option[value="'+rowObjectFormatted['Рейсы_Код']+'"]').attr('selected',true);
							wrapper.appendChild(haul);
							return wrapper;
						},
						done_func:function()
						{
							var haul = $('select',this).val();
							if(!haul)
								haul = rowObjectFormatted['Рейсы_Код'];
							get_file_url({qry:haul,reference:gridPseudo.location},'Разгрузка рейса №'+rowObject['Рейсы_Код'],{prefix:'word',folder:'tig_wh_unload'});
						},
						dialog_opts:{
							modal:false,
							open:function()
							{
								dataSelect2($('select',this),undefined,{allowClear:false});
							}
						}
					})
				}
			},
			orders_route:{
				name:'Маршрут заказа',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-road';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var text = rowObject['Номер_Заказа'].length > 0 ? rowObject['Номер_Заказа'] : 'Не установлено';
					gridPseudo.plugin({
						name:'orders_route',
						dialog_title:'Маршрут заказа: '+ text,
						dialog_width:1200,
						dialog_height:500,
						dialog_refresh_grid:false
					});
				}
			}
		},
		permFilter:{
			groupOp:"AND",
			rules:[
				{field:"Удалено",op:"eq",data:"0"},
				{field:"Статус_Код",op:"lt",data:"100"}
			]
		},
		permFilterButtons:[
			{caption:'Принята',data:{field:"Статус_Код",op:"eq",data:"0",perm:true}},
			{caption:'В процессе', data:{ field: ["Статус_Код", "Статус_Код"], op: ['lt','gt'], data:[2, 4],groupOps:'OR',perm:true  }},
			//{caption:'В доставке', data:{ field: "Статус_Код", op:"in", data:"-1, 0, 1, 2, 3, 4, 20, 25" ,perm:true}},
			//{caption:'Заказы на ин.склад',data:{field:["Статус_Код","ШК_Печатался","Инсклад_код"],op:["le","eq","eq"],data:["4","1","4964"],groupOps:'AND',perm:true}},
			//{caption:'Заказы Formaro',data:{field:"Инсклад_код",op:"eq",data:"6920",perm:true}},
			{caption:'Удаленные',data:{field:"Удалено",op:"ne",data:"0",perm:true}},
			{caption:'Архив',data:{field:"Статус_Код",op:"ge",data:"0",perm:true}},
			{caption:'Не Trasporter',data:{field:"Клиенты_Код",op:"ne",data:"150",perm:true}},
			{caption:'Не Int.Market',data:{field:"Клиенты_Код",op:"ne",data:"61298",perm:true}},
			{caption:'Не Италия',data:{field:["Фабрика_Страна","Заявка_на_инсклад"],op:['ne','eq'],data:["ITA","1"],groupOps:'AND',perm:true}},
			//{caption:'Не A-Pan',data:{field:"Клиенты_Код",op:"ne",data:"8429",perm:true}},
			{caption:'Без рейса', data:{field:"Рейсы_Код",op:"isNull",perm:true}}
		],
		customButtons:[
			{
				caption:'Актуальные заявки',
				icon:'fa fa-file-excel-o',
				click:function(e)
				{
					var location = this.p.gridProto.location;
					$.confirmHTML({
						yes:'Сформировать',
						no:'Закрыть',
						width:250,
						html:function()
						{
							var select = document.createElement('select');
							select.style.width = 'calc(100% - 15px)';
							$(select).append("<?php echo $this->Core->get_lib_html(['tname'=>'Перевозчики']); ?>");
							return select;
						},
						done_func:function()
						{
							var val = $('select',this).val();
							if(!val)
								return $.alert('Перевозчик не выбран!');
							var text = $("option[value='"+ val +"']").html();
							var stm = {
								type:'excel',
								reference:location,
								qry:{
									tname:'FT_Актуальные_Заявки('+ val +')',
									type:'SELECT',
									fields:[
										{name:'Клиенты_Код as [Клиент]'},
										{name:'Контрагент_Отгрузки as [Контрагент отгрузки]'},
										{name:'Номер_Заказа as [Номер заказа]'},
										{name:'Примечание_курьер as [Примечание для курьера]'},
										{name:'CONVERT(VARCHAR(10), Дата_Готовность, 104) as [Дата готовности]'},
										{name:'CONVERT(VARCHAR(10), Дата_ИнСклад, 104) as [Дата ИнСклад]'},
										{name:'Объем'},
										{name:'Вес'},
										{name:'Мест'},
										{name:'Примечание_П as [Примечание перевозчика]'},
										{name:'Адрес'},
										{name:'Контакт'}
									]
								}
							};
							get_file_url(stm,'Актуальные_заявки_'+text);
						},
						dialog_opts:{
							modal:false,
							open:function()
							{
								dataSelect2($('select',this),undefined,{allowClear:false});
							}
						}
					})
				}
            }/*,
            {
				caption:'Отгруженные ШК',
				icon:'fa fa-file-excel-o',
				click:function(e)
				{
					var location = this.p.gridProto.location;
					$.confirmHTML({
						yes:'Сформировать',
						no:'Закрыть',
						width:250,
						html:function()
						{
							var select = document.createElement('select');
							select.style.width = 'calc(100% - 15px)';
							$(select).append("<?//php echo $this->Core->get_lib_html(['tname'=>'Б_Рейсы']); ?>");
							return select;
						},
						done_func:function()
						{

							var val = $('select',this).val();
							if(!val)
								return $.alert('Рейс не выбран!');
							var text = $("option[value='"+ val +"']").html();
							var stm = {
								type:'excel',
								reference:location,
								qry:{
									tname:'VIA_Отгруженые_ШК_По_Рейсу',
									type:'SELECT',
									fields:[
										{name:'Клиент'},
										{name:'Подклиент'},
										{name:'Фабрика'},
										{name:'Номер_Заказа as [Номер заказа]'},
										{name:'ИнСклад_Мест as Мест'},
										{name:'ШтрихКод'},
										{name:'FullName as [Название терминала]'},
										{name:"CONVERT(VARCHAR(20), Дата, 113) as [Дата]"}
									]
								},
								qw:{
									groupOp:'AND',
									rules:[{data:val,field:'Рейсы_Код',op:'eq'}]
								}
							};
							get_file_url(stm,'Отгруженные_ШК_по_рейсу_№'+text);
						},
						dialog_opts:{
							modal:false,
							open:function()
							{
								dataSelect2($('select',this),undefined,{allowClear:false});
							}
						}
					})
				}
			}*/
		],
		navGrid:true,
		cn:[
			"Код","Цвет","Рейс","Статус","Клиент","ПодКлиент","Фабрика","Склад доставки","Заказ","ex_it","Нужна_EX","Примечание для курьера","Д.ПолучЗаявки","Д.Готовности","Д.ИнСклад","Объем","Вес","Мест",
			"На инсклад","Дост.фабрикой","Доки","Перевозчик","Ин.склад","Страна","Квадрат","Тариф Европа","Тариф Россия","Агент заказа","Удалено","Примечание","ex","Сбор_на_инсклад","Примечание-Д","Примечание_Д_курьер","Примечание: Cостав заказа","Примечание пакинг",
			"Дата изм.статуса","Дата оплаты","ИнСклад ШтрихКод","Весь состав","ШК_Печатался","Пакинг Мест","Объем ИнСклад","Пакинг Вес","РФ Объем","Контрагент отгрузки","Импортер","Регион отгрузки",
			"<i class='fa fa-lg fa-exclamation-triangle'></i>","Состав_Мест","Состав_Объем","Состав_Вес","MRN","Номер Инвойса","Сумма Инвойса", "Подклиент_Текст","Ex_Files","Контакт","Телефон","Email","Фабрика","Транспортник"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			},

			{
				name: "Цвет",index:"Цвет",width:30,search:false,formatter:'integer',addformeditable:false,
				editoptions:{
					dataInit:function(elem,opts) {
						new jqGrid_aw_combobox$(elem,{},opts,this,{
							list:[['Зеленый',0],['Желтый',1],['Красный',2],['Синий',3],['Белый',4],['Оранжевый',5],['Коричневый',6],['Фиолетовый',7],['Голубой',8]]
						});
					}
				},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					switch (val)
					{
						case '0' :
							return "style='background-color:#228b22;color:#228b22' title='Зеленый'";
							break;
						case '1' :
							return "style='background-color:#FFFF66;color:#FFFF66' title='Желтый'";
							break;
						case '2' :
							return "style='background-color:#FF3232;color:#FF3232' title='Красный'";
							break;
						case '3' :
							return "style='background-color:#104e8b;color:#104e8b' title='Синий'";
							break;
						case '4' :
							return "style='background-color:#f7f7f7;color:#f7f7f7' title='Белый'";
							break;
						case '5' :
							return "style='background-color:#ffa500;color:#ffa500' title='Оранжевый'";
							break;
						case '6' :
							return "style='background-color:#8b4513;color:#8b4513' title='Коричневый'";
							break;
						case '7' :
							return "style='background-color:#c71585;color:#c71585' title='Фиолетовый'";
							break;
						case '8' :
							return "style='background-color:#00bfff;color:#00bfff' title='Голубой'";
							break;
						default : return ""; break;
					}
				}
			},

			{
				name: "Рейсы_Код",index:"Рейсы_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Рейсы']) ?>
				},
				searchoptions:{
					sopt:['eq'],
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Рейсы'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Рейсы'},opts,this); }
				}
			},

			{
				name: "Статус_Код",index:"Статус_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Заказы_статус','fields'=>['Код','Название','Порядок'],'order'=>"Порядок"]) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Заказы_статус',order:"Порядок"})}
				},
				editoptions:{
					defaultValue:'0',
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Заказы_статус',flds:['Код','Название','Порядок'],order:'Порядок'},opts,this,{maxItems:100,sort:null}); }
				}
			},

			{
				name: "Клиенты_Код",index:"Клиенты_Код",width:200,stype:'select',formatter:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Клиенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Клиенты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Клиенты'},opts,this); }
				},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if (rawObject['Весь_состав'] == 1)
						return "style='background-color:#4ca64c;font-weight:bold;color:#FFF'";
				}
			},

			{
				name: "Подклиенты_Код",index:"Подклиенты_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Подклиенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Подклиенты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Подклиенты'},opts,this); }
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Фабрики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Фабрики'},opts,this); }
				}
			},

			{
				name: "Склад_Доставки_Код",index:"Склад_Доставки_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Склады_Доставки']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Склады_Доставки'})}
				},
				editoptions:{
					defaultValue:'1601',
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Склады_Доставки'},opts,this); }
				},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if(val === 'EUR WH')
						return "style='background-color:#7d6199;color:white'";
					else if (val === 'SPB WH')
						return "style='background-color:#00bfff'";
					else if (val === 'KRD WH')
						return "style='background-color:#00ffbf'";
				}
			},

			{
				name: "Номер_Заказа",index:"Номер_Заказа",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{	
					if	((rawObject['Состав_Объем'] == 0) 
						|| (rawObject['Состав_Вес'] == 0))
						{
							return "style='background-color:red;color:white;font-weight:bold;";
						}
					else if ((rawObject['Состав_Мест'] != rawObject['ИнСклад_Мест']) 
						|| (rawObject['Состав_Объем'] != rawObject['Объем']) 
						|| (rawObject['Состав_Вес'] != rawObject['Вес']))
						{
							return "style='background-color:#ffa500;color:white;font-weight:bold;";
						}
				}
			},
			{
				name: "ex_it",index:"ex_it",width:30,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},
			{
				name: "Нужна_EX",index:"Нужна_EX",width:30,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},
			{
				name: "Примечание_курьер",index:"Примечание_курьер",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Дата_ПолучЗаявки",index:"Дата_ПолучЗаявки",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Дата_Готовность",index:"Дата_Готовность",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Дата_ИнСклад",index:"Дата_ИнСклад",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd},
				cellattr:function(rowId, val, rawObject, cm , rdata){
					if(rawObject['Статус_Код'] === '2' && val !== '&#160;'){
						var curDate = new Date();
						val = val.split('.');
						val = val[2]+'-'+val[1]+'-'+val[0];val = new Date(val);
						val.setHours(0,0,0,0);
						curDate.setHours(0,0,0,0);
						if(val < curDate)
							return "style='background-color:#ff0000;'";
					}
				}
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter,formatoptions: {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 3, prefix: ""},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					/*if(rawObject['Импортеры_Код'] !== null && rawObject['Пакинг_Мест'] !== null && rawObject['Пакинг_Вес'] !== null)
						return "style='background-color:#32cd32'";
					else if(rawObject['Импортеры_Код'] !== null)
						return "style='background-color:#ffa500'";
					else if (rawObject['Пакинг_Мест'] !== null)
						return "style='background-color:#ffa500'";
					else if (rawObject['Пакинг_Вес'] !== null)
						return "style='background-color:#ffa500'";
						*/
					if (rawObject['Пакинг_Мест'] == rawObject['ИнСклад_Мест'] && rawObject['Пакинг_Вес'] == rawObject['Вес'])
						return "style='background-color:#32cd32'"; //зеленый
					else if((rawObject['Пакинг_Мест'] === null) || (rawObject['Пакинг_Мест'] == 0)  || (rawObject['Пакинг_Вес'] === null)|| (rawObject['Пакинг_Вес'] == 0))
						return "style='background-color:#ffa500'"; //желтый
					else if ((rawObject['Пакинг_Мест'] !== rawObject['ИнСклад_Мест']) || (rawObject['Пакинг_Вес'] !== rawObject['Вес']))
						return "style='background-color:#ff0000'"; //красный
					/*else if (rawObject['Импортеры_Код']===null)
						return "style='background-color:#0000ff'"; //синий*/
				}
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter,formatoptions: {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 3, prefix: ""}
			},

			{
				name: "ИнСклад_Мест",index:"ИнСклад_Мест",width:50,
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(rawObject['ШК_Печатался'] == 1)
						return "style='background-color:#32cd32'";
				}
			},

			{
				name: "Заявка_на_инсклад",index:"Заявка_на_инсклад",width:30,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Доставлен_фабрикой",index:"Доставлен_фабрикой",width:30,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Документы",index:"Документы",width:30,stype:'select',gtype:'checkbox',addformeditable:false,
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				},
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(rawObject['Ex_Files'] == '0')
						return "style='background-color:#FFFF66'";
				}				
			},

			{
				name: "Транспортника_Код",index:"Транспортника_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Перевозчики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Перевозчики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Перевозчики'},opts,this); }
				}
			},

			{
				name: "ИнСклад_Код",index:"ИнСклад_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Склады']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Склады'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Склады'},opts,this); }
				}
			},

			{
				name: "Фабрика_Страна",index:"Фабрика_Страна",width:30,editable:false
			},

			{
				name: "Квадрат",index:"Квадрат",width:100,editable:false
			},

			{
				name: "Тариф_Европа",index:"Тариф_Европа",width:200,formatter:'select',stype:'select',addformeditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Тарифы_Европа']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Тарифы_Европа'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Тарифы_Европа'},opts,this); }
				}
			},

			{
				name: "Тариф_Россия",index:"Тариф_Россия",width:200,formatter:'select',stype:'select',addformeditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Тарифы_Россия2']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Тарифы_Россия2'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Тарифы_Россия2'},opts,this); }
				}
			},

			{
				name: "Агент_Заказа_Код",index:"Агент_Заказа_Код",width:200,formatter:'select',stype:'select',addformeditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Контрагенты_доставки']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Контрагенты_доставки'})}
				},
				editoptions:{
					defaultValue:'7170',
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Контрагенты_доставки'},opts,this); }
				}
			},

			{
				name: "Удалено",index:"Удалено",width:100,gtype:'checkbox',stype:'select',hidden:true,editable:false,
				searchoptions:{
					width:100,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},																	           

			{
				name: "ex",index:"ex",width:70,editable:true,formatter:floatFormatter,formatoptions: {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: ""}
			},	
			{
				name: "Сбор_на_инсклад",index:"Сбор_на_инсклад",width:70,editable:true,formatter:floatFormatter,formatoptions: {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: ""}
			},

			{
				name: "Примечание_фин",index:"Примечание_фин",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},
			{
				name: "Примечание_Д_Курьер",index:"Примечание_Д_Курьер",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},
			{
				name: "Примечание_состав",index:"Примечание_состав",hidden:true,hidedlg:true,width:150,align:"left",addformeditable:true,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Примечание_пакинг",index:"Примечание_пакинг",addformeditable:false,width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Статус_Дата",index:"Статус_Дата",width:120,formatter:'date',hidden:true,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Дата_Оплаты_Заявки",index:"Дата_Оплаты_Заявки",width:120,formatter:'date',hidden:true,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "ИнСклад_ШтрихКод",index:"ИнСклад_ШтрихКод",width:100,hidden:true,
			},

			{
				name: "Весь_состав",index:"Весь_состав",width:100,hidden:true,
			},

			{
				name: "ШК_Печатался",index:"ШК_Печатался",width:30,gtype:'checkbox',stype:'select',hidden:true,editable:false,
				formatoptions: {
					disabled: false
				},
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Пакинг_Мест",index:"Пакинг_Мест",width:50,hidden:true
			},

			{
				name: "Пакинг_Объем",index:"Пакинг_Объем",width:50,formatter:floatFormatter,hidden:true
			},

			{
				name: "Пакинг_Вес",index:"Пакинг_Вес",width:50,formatter:floatFormatter,hidden:true
			},

			{
				name: "РФСклад_Объем",index:"РФСклад_Объем",width:50,formatter:floatFormatter,hidden:true,
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if(parseFloat(rawObject['Объем']) < parseFloat(val))
						return "style='background-color:#ffa500'";
				}
			},

			{
				name: "Контрагент_Отгрузки_Код",index:"Контрагент_Отгрузки_Код",width:200,formatter:'select',stype:'select',hidden:true,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Фабрики',order:false})}
				},
				editrules:{
					edithidden:true
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Фабрики'},opts,this); }
				}
			},

			{
				name: "Импортеры_Код",index:"Импортеры_Код",width:200,formatter:'select',stype:'select',hidden:true,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Импортеры']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Импортеры'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Импортеры'},opts,this); }
				}
			},

			{
				name: "Регион_Отгрузки",index:"Регион_Отгрузки",width:50,hidden:true,editable:false
			},

			{
				name: "Битый_груз",index:"Битый_груз",width:30,gtype:'checkbox',stype:'select',addformeditable:false,
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
            },
			{
				name: "Состав_Мест",index:"Состав_Мест",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 
			{
				name: "Состав_Объем",index:"Состав_Объем",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 
			{
				name: "Состав_Вес",index:"Состав_Вес",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 	
			{
				name: "MRN",index:"MRN",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 
			{
				name: "Номер_Инвойса",index:"Номер_Инвойса",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 
			{
				name: "Сумма_Инвойса",index:"Сумма_Инвойса",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 
			{
				name: "Подклиент_Текст",index:"Подклиент_Текст",width:50,hidden:false,editable:true,editrules:{edithidden:false},searchoptions: {searchhidden: true}
			}, 
			{
				name: "Ex_Files",index:"Ex_Files",width:10,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: false}
			}, 

			{
				name: "Контакт",index:"Контакт",width:70,editable:false
			},																	           
			{
				name: "Телефон",index:"Телефон",width:70,editable:false
			},	
			{
				name: "Email",index:"Email",width:70,editable:false
			},	
			{
				name: "Фабрика",index:"Фабрика",hidden:true,width:70,editable:false
			},
			{
				name: "Транспортник",index:"Транспортник",hidden:true,width:70,editable:false
			}
		],
		options:{
			cellEdit:true
		},
		events:{
			afterSaveCell:function(rowid, cellname, value, iRow, iCol)
			{
				afterSaveCellReload.apply(this,[{fields:['Цвет','Статус_Код']},[rowid, cellname, value, iRow, iCol]]);
			}
		},
		subGridOps:[
			{
				subgrid:true,
				main:false,
				name:'orders_details',
				table:'Заказы_Состав',
				id:'Код',
				beforeSubmitCell:true,
				delOpts:true,
				navGrid:true,
				goToBlank:true,
				onCellSelect:true,
				filterToolbar:true,
				hideBottomNav:true,
				customButtons:[
					{
						caption:'Сводная',
						icon:'fa fa-shopping-cart',
						click:function(e)
						{
							this.p.gridProto.plugin({
								name:'composition',
								id:this.p.postData.mainId,
								dialog_title:'Сводная по составу заказа',
								dialog_width:400,
								dialog_height:200,
								dialog_custom_opts:{
									dialog_opts:
									{
										draggable:true,
										modal:false
									}
								},
								dialog_refresh_grid:false
							});
						}
					}
				],
				footer:[
					{col:'Кол_предм',calc:'sum'},
					{col:'Кол_мест',calc:'sum'},
					{col:'Объем',calc:'sum'},
					{col:'Вес',calc:'sum'}
				],
				navGridOptions:{
					add:true
				},
				subgridpost:{
					mainidName:'Заказы_Код'
				},
				cn:[
					"Код","Заказы_Код","Вид груза","Артикул","# предм","Материал","# мест","Объем","Вес","Примечание","Код_ТН_ВЭД"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Виды_груза_Код",index:"Виды_груза_Код",width:200,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Виды_груза']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Виды_груза'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Виды_груза'},opts,this); }
						}
					},

					{
						name: "Артикул",index:"Артикул",width:50
					},

					{
						name: "Кол_предм",index:"Кол_предм",width:50
					},

					{
						name: "Материалы_Код",index:"Материалы_Код",width:200,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Материалы']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Материалы'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Материалы'},opts,this); }
						}
					},

					{
						name: "Кол_мест",index:"Кол_мест",width:50
					},

					{
						name: "Объем",index:"Объем",width:50,formatter:floatFormatter
					},

					{
						name: "Вес",index:"Вес",width:50,formatter:floatFormatter
					},

					{
						name: "Примечание",index:"Примечание",width:250,align:"left",
						cellattr:textAreaCellAttr,
						edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
					},
					{
						name: "Код_ТН_ВЭД",index:"Код_ТН_ВЭД",width:50
					}
				],
				options:{
					shrinkToFit:true,
					autowidth:false,
					width:1500,
					height:'100%',
					cellEdit:true
				},
				events:{
					afterSaveCell:function(rowid, cellname, value, iRow, iCol){
						$(this).jqGrid("footerData","set",
							{
								Виды_груза_Код:'Итого',
								Кол_предм:$(this).jqGrid('getCol','Кол_предм', false,'sum'),
								Кол_мест:$(this).jqGrid('getCol','Кол_мест', false,'sum'),
								Объем:$(this).jqGrid('getCol','Объем', false,'sum'),
								Вес:$(this).jqGrid('getCol','Вес', false,'sum')
							},false
						)
					}
				}
			}
		]
	})
})
</script>
