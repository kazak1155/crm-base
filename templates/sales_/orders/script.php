<script type="text/javascript">
$(function() {
	var orders = new jqGrid$({
		main:true,
		name:'orders_ready',
		table:'Форма_Заказы_Ready',
		title:'Заказы',
		formpos:false,
		tableQuery:'Заказы',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		beforeSubmitCell:true,
		contextMenuFileTree: true,
		contextMenuItems:{
			order_contains:{
				name:'Состав заказа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cubes';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					gridPseudo.iframe_sub({
						url:'/themes/shop/orders/frame_order_contains',
						id:rowid,
						data:rowObjectFormatted,
						dialog_width:800,
						dialog_height:400,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Состав заказа №'+rowid,
								draggable:true,
								modal:false,
								close:function(){
									$(this).remove();
								}
							},
							dialog_extend_opts:{
								minimizable:true
							}
						}
					});
				}
			},
			order_params:{
				name:'Параметры заказа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-database';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					gridPseudo.iframe_sub({
						url:'/themes/shop/orders/frame_order_params',
						id:rowid,
						data:rowObject,
						dialog_width:300,
						dialog_height:300,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Параметры заказа №'+rowid,
								modal:false,
								draggable:true,
								close:function(){
									$(grid).trigger("reloadGrid",{current:true});
									$(this).remove();
								}
							},
							dialog_extend_opts:{
								minimizable:true
							}
						}
					});
				}
			},
			order_cross:{
				name:'Кросс курсы заказа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-exchange';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					gridPseudo.iframe_sub({
						url:'/themes/shop/orders/frame_order_cross',
						id:rowid,
						data:rowObjectFormatted,
						dialog_width:600,
						dialog_height:400,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Кросс курсы заказа №'+rowid,
								modal:true,
								close:function(){
									$(this).remove();
								}
							}
						}
					});
				}
			},
			order_services:{
				name:'Услуги по заказу',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-gift ';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					gridPseudo.iframe_sub({
						url:'/themes/shop/orders/frame_order_services',
						id:rowid,
						data:rowObjectFormatted,
						dialog_width:600,
						dialog_height:400,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Услуги по заказу №'+rowid,
								modal:true,
								close:function(){
									$(this).remove();
								}
							}
						}
					});
				}
			},
			order_delay:{
				name:'Отложить дату окончания статуса',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-clock-o';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					$.confirmHTML({
						yes:'Сохранить',
						no:'Закрыть',
						width:300,
						preventOkClose:true,
						html:function()
						{
							var wrapper = document.createElement('form');
							var hidden_submit = document.createElement('input');
							hidden_submit.type ='submit';
							$(hidden_submit).css('display','none');
							wrapper.className = wrapper.id = 'delay_form';
							wrapper.appendChild($.genHTML({type:'input-top-label',options:{label_text:'Статус окончание',name:'delay'}}));
							wrapper.appendChild($.genHTML({type:'input-top-label',options:{label_text:'Причина',name:'delay_prim',autofocus:false}}));
							wrapper.appendChild(hidden_submit);
							$('input[name="delay"]',wrapper).datepicker();
							return wrapper;
						},
						done_func:function()
						{
							var dialog = this;
							var frm = $('.delay_form',this);
							if(frm[0].checkValidity() == false)
							{
								frm.find(':submit').click();
								return false;
							}
							var frm_obj = frm.serializeObject();
							var date = frm_obj['delay'].split('.');
							date = date[2]+date[1]+date[0];
							var delay_prim_full = 'Дата изменения: ' + getDate(true,false,false,true) + '. Причина: '+frm_obj['delay_prim'];
							$.ajaxShort({
								data:{
									action:'edit',
									query:'UPDATE Заказы SET Статус_Дата_delay = \''+ date +'\',Статус_Примечание = \''+ delay_prim_full +'\' WHERE Код = '+ rowid
								},
								success:function()
								{
									$(grid).trigger("reloadGrid",{current:true});
									$(dialog).remove();
								}
							});
						},
						dialog_extend_opts:{
							titlebar:'none'
						}
					});
				}
			},
			order_delegation:{
				name:'Делегирование заказа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-users';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					$.confirmHTML({
						yes:'Делегировать',
						no:'Закрыть',
						width:300,
						dialog_opts:{
							open:function()
							{
								dataSelect2.call(this,$('.select2me'),undefined,{allowClear:false});
							}
						},
						html:function()
						{
							var select  = document.createElement('select');
							select.className = 'select2me';
							select.innerHTML = "<?php echo $this->Core->get_lib_html(['tname'=>'srv_З_Б_Пользователи']); ?>";
							$(select).css('width','calc(100% - 15px)');
							return select;
						},
						done_func:function()
						{
							var dialog = this;
							var selected = $('select',this).val();
							$.ajaxShort({
								data:{
									action:'edit',
									query:'UPDATE Заказы SET Пользователь = \''+ selected +'\' WHERE Код = '+ rowid
								},
								success:function()
								{
									$(grid).trigger("reloadGrid",{current:true});
								}
							});
						},
						dialog_extend_opts:{
							titlebar:'none'
						}
					});
				}
			}
		},
		permFilter:{
			groupOp:"AND",rules:[
				{field:"Статус_Код",op:"lt",data:"200"}
			]
		},
		permFilterButtons:[
			{caption:'Архив',data:{field:"Статус_Код",op:"ge",data:"200",perm:true}},
		],
		navGrid:true,
		navGridOptions:{add:false},
		cn:[
			"№",
			"Тип клиента","Клиент_Код",
			"Клиент","Телефон",
			"Фабрика",
			"Статус","Статус дата","Статус окончание","Статус примечание",
			"Создал",
			"Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:40,hidden:false,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Тип_Код",index:"Тип_Код",width:120,formatter:'select',stype:'select',celleditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Контрагенты_Тип']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Контрагенты_Тип'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Контрагенты_Тип',getNull:false},opts,this); }
				}
			},

			{
				name: "Клиент_Код",index:"Клиент_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Клиент",index:"Клиент",width:350,celleditable:false
			},

			{
				name: "Телефон",index:"Телефон",width:120,editable:false
			},


			{
				name: "Фабрика_Код",index:"Фабрика_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Фабрики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Фабрики',getNull:false},opts,this); }
				}
			},

			{
				name: "Статус_Код",index:"Статус_Код",formatter:'select',width:200,stype:'select',celleditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Заказы_Статусы','order'=>'Код']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_Статусы',order:'Код'})}
				},
				editoptions:{
					disabled:true,
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Заказы_Статусы',getNull:false,order:"Код"},opts,this); }
				}
			},

			{
				name: "Статус_Дата",index:"Статус_Дата",width:120,formatter:'date',editable:false,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},
				formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Статус_Срок",index:"Статус_Срок",width:130,formatter:'date',editable:false,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},
				formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					var date_start = new Date(),date_end = new Date(rawObject['Статус_Срок']);
					var t_diff = date_end.getTime() - date_start.getTime();
					var d_diff = Math.ceil(t_diff / (1000 * 3600 * 24));
					if(d_diff == 1)
						return "style='background-color:#FFFF66;color:#000'";
					else if(d_diff <= 0)
						return "style='background-color:#FF3232;color:#000'";
					else if(d_diff > 1)
						return "style='background-color:#228b22;color:#FFF'";
				}
			},

			{
				name: "Статус_Примечание",index:"Статус_Примечание",width:250,align:"left",editable:false,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Пользователь",index:"Пользователь",formatter:'select',width:160,stype:'select',editable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'srv_З_Б_Пользователи']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_З_Б_Пользователи'})}
				},
				editoptions:{
					disabled:true,
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_З_Б_Пользователи'},opts,this); }
				}
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",addformeditable:false,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			cellEdit:true,
			shrinkToFit:true
		},
		subGridOps:[
			{
				subgridBeforeCreate:function(options,gridrow,gridrow_unformatted){
					/* TODO verification */
					//console.log(gridrow_unformatted['Сформирован'])
					return options;
				},
				//delOpts:true,
				subgrid:true,
				main:false,
				name:'orders_fin',
				table:'Заказы_Фин_Движение',
				id:'Код',
				beforeSubmitCell:true,
				navGrid:true,
				hideBottomNav:true,
				formpos:false,
				contextMenuFileTree: true,
				navGridOptions:{
					add:true
				},
				addFormOptions:{
					afterComplete:function(response, postdata, formid)
					{
						if($(this).getRowData().length == 1)
							$(this.p.gridProto.parent_grid).trigger("reloadGrid",{current:true});
					}
				},
				subgridpost:{
					mainidName:'Заказы_Код'
				},
				cn:[
					"Код","Заказы_Код","Тип","Плательщик","Получатель","Категория","Способ оплаты","Валюта","Значение","Accept","Примечание"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Движение_Тип_Код",index:"Движение_Тип_Код",width:150,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Движение_Тип']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Движение_тип_only'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Движение_тип_only',getNull:false},opts,this); }
						}
					},

					{
						name: "Плательщик",index:"Плательщик",width:150,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Поставщики_Получатели']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Поставщики_Получатели'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Поставщики_Получатели',getNull:false},opts,this); }
						}
					},

					{
						name: "Получатель",index:"Получатель",width:150,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Поставщики_Получатели']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Поставщики_Получатели'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Поставщики_Получатели',getNull:false},opts,this); }
						}
					},

					{
						name: "Движение_Категория_Код",index:"Движение_Категория_Код",width:150,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Движение_Категория']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Движение_Категория'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Движение_Категория',getNull:false},opts,this); }
						}
					},

					{
						name: "Способ_Оплаты_Код",index:"Способ_Оплаты_Код",width:150,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Способы_Оплаты']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Способы_Оплаты'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Способы_Оплаты',getNull:false},opts,this); }
						}
					},

					{
						name: "Валюта_Код",index:"Валюта_Код",width:100,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
						}
					},

					{
						name: "Значение",index:"Значение",width:100,formatter:floatFormatter,editrules:{number:true}
					},

					{
						name: "Подтверждено",index:"Подтверждено",width:70,gtype:'checkbox',stype:'select',addformeditable:false,
						searchoptions:{
							width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
						}
					},

					{
						name: "Примечание",index:"Примечание",width:250,align:"left",addformeditable:false,
						cellattr:textAreaCellAttr,
						edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
					}
				],
				options:{
					caption:'Баланс заказа',
					shrinkToFit:true,
					cellEdit:true,
					height:'100%',
					autowidth:true
				},
				events:{
					afterSaveCell:function(rowid, cellname, value, iRow, iCol){
						$(this).jqGrid("footerData","set",
							{
								Значение:$(this).jqGrid('getCol','Значение', false,'sum')
							},false
						)
					}
				}
			}
		]
	});
})
</script>
