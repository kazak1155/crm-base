<script type="text/javascript">
var get_fabrics_orders = function(grid,selval)
{
	var fabric_sel = document.getElementById('Фабрики_Код');
	var fabric_sel_prot = fabric_sel.jqGrid_aw_combobox_proto;
	fabric_sel_prot.element.value = '';
	fabric_sel_prot.enable();
	fabric_sel_prot.clear_results();
	fabric_sel_prot.aj_data.search = 'Рейсы_Код = ' + grid.p.postData.mainId + ' AND Клиенты_Код = '+ selval;
	$(fabric_sel_prot.select_helper).on('change',function(event)
	{
		fabric_sel_prot.isOpened = false;
		$.ajaxShort({
			dataType:'JSON',
			data:{
				responseType:'multiple',
				action:'view',
				query:'SELECT Код,Номер_Заказа FROM Заказы WHERE Рейсы_Код = ' +grid.p.postData.mainId+ ' AND Клиенты_Код = ' +selval+ ' AND Фабрики_Код = ' + this.value
			},
			success:function(data)
			{

				if(data.length == 0)
					return $.alert('Critical error!');
				if(data.length === 1)
					document.getElementById('Заказы_Код').value = data[0]['Код'];
				else
				{
					$.confirmHTML({
						title:'Выберите номер заказа',
						width:250,
						dialog_opts:
						{
							modal:false
						},
						dialog_extend_opts:
						{
							load:function()
							{
								$(this).parent('div').css('z-index',1000);
							}
						},
						html:function()
						{
							var options = new Array();
							$.each(data,function(i,el)
							{
								options.push({text:el['Номер_Заказа'],value:el['Код']});
							});
							return $.genHTML({type:'simple-select',options:{name:'Валюта',data:options}});
						},
						done_func:function(html)
						{
							document.getElementById('Заказы_Код').value = html.value;
						}
					})
				}
			}
		});
	});
}
$(function() {
	new jqGrid$({
		main:true,
		name:'hauls',
		table:'V_Рейсы',
		title:'Рейсы',
		id:'Код',
		orderName:'Код',
		tableSort:'DESC',
		useLs:false,
		filterToolbar:true,
		beforeSubmitCell:true,
		delOpts:true,
		navGridOptions:{search:true,add:true,edit:true},
		delFormOptions:{
			delData:{
				tid:'Код',
				tname:'Рейсы'
			}
		},

		contextMenuFileTree: true,
		contextMenuItems:{
			haul_inv:{
				name:'FT_Ex',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-envelope';
				},
				items:{	
					EX_mailto:{
						name : "По email",
						icon: function(opt, $itemElement, itemKey, item)
						{
							return 'context-menu-icon-fa fa-envelope';
						},
						custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
						{
							rowObject['mail_id'] = 55;
							rowObject['formatted_data'] = rowObjectFormatted;
							gridPseudo.plugin({
								frame_type:'email',
								dialogClassName:'email-dialog',
								id:rowid,
								data:rowObject,
								dialog_height:600
							});
						}
					},
					EX_download:{
						name : "Скачать",
						icon: function(opt, $itemElement, itemKey, item)
						{
							return 'context-menu-icon-fa fa-envelope';
						},
						custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
						{
							window.location.href='/core/ex_dl.php?haul='+rowid;
						}
					}
				}
			},
			haul_Noakko:{
				name:'Docs carico Noakko',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-envelope';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					rowObject['mail_id'] = 56;
					rowObject['formatted_data'] = rowObjectFormatted;
					gridPseudo.plugin({
						frame_type:'email',
						dialogClassName:'email-dialog',
						id:rowid,
						data:rowObject,
						dialog_height:600
					});
				}
			},
			haul_details:{
				name:'Подробные данные',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-truck';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					gridPseudo.plugin({
						name:'border',
						dialog_title:'Подробные данные',
						dialog_width:600,
						dialog_height:550,
						dialog_refresh_grid:true
					});
				}
			},
			haul_wh:{
				name:'Складская обработка',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cogs';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					$.confirm({
						message:'Вы уверены, что хотите оформить складскую обработку ?',
						done_func:function(){
							$.ajaxShort({
								dataType:'JSON',
								data:{
									action:'view',
									responseType:'multiple',
									query:'SELECT * FROM Расчет_СкладскаяОбработка WHERE Рейсы_Код = ' + rowid
								},
								success:function(data)
								{
									if(data.length > 0)
									{
										$.confirmHTML({
											yes:'Сохранить',
											no:'Отмена',
											width:600,
											preventOkClose:true,
											title:'Складская обработка заказов рейса №'+rowObject['Номер'],
											html:function()
											{
												var form = document.createElement('form');
												form.orderdata = new Object();
												var f_submit = document.createElement('button');
												f_submit.style.display = 'none';
												form.id = 'wh-form';
												Object.assign(form.style,{overflowY:'scroll',height:'500px'});
												form.onsubmit = function(event) { return false; };
												var f_table = document.createElement('table');
												for(var order of data)
												{
													order['Объем'] = Number(10 * order['Объем']).toFixed(2);
													form.orderdata[order['Заказы_Код']] = new Object();
													$.extend(form.orderdata[order['Заказы_Код']],order);

													var row = document.createElement('tr');
													var cc_name_td = document.createElement('td');
													var f_name_td = document.createElement('td');
													var vol_td = document.createElement('td');
													cc_name_td.appendChild($.genHTML({type:'input-top-label',options:{required:false,name:'Клиент',input_value:order['Клиент'],label_text:'Клиент',disabled:true}}));
													row.appendChild(cc_name_td);
													f_name_td.appendChild($.genHTML({type:'input-top-label',options:{required:false,name:'Фабрика',input_value:order['Фабрика'],label_text:'Фабрика',disabled:true}}));
													row.appendChild(f_name_td);
													vol_td.appendChild($.genHTML({type:'input-top-label',wd:'100px',options:{name:order['Заказы_Код'],input_value:order['Объем'],label_text:'Сумма'}}));
													row.appendChild(vol_td);
													f_table.appendChild(row);
												}
												form.appendChild(f_table);
												form.appendChild(f_submit);
												return form;
											},
											done_func:function()
											{
												var dialog = this;
												var form = document.getElementById('wh-form');
												if(form.checkValidity() === false)
												{
													$(':submit',form).click();
													return false;
												}

												var form_data = new FormData(form);
												var form_rows = form.orderdata;
												var form_rows_keys = Object.keys(form_rows);
												$.asyncloop({
													length:form_rows_keys.length,
													loop_action:function(loop,i)
													{
														var r = form_rows[form_rows_keys[i]];
														var sum = form_data.get(r['Заказы_Код'])
														$.ajaxShort({
															data:{
																action:'edit',
																query:'DELETE FROM V_Услуги_Приход WHERE Назнач_Платеж_Код = 37 AND Заказы_Код = '+ r['Заказы_Код']// +' AND Рейсы_Код = '+ r['Рейсы_Код']
															},
															success:function()
															{
																var query = "INSERT INTO V_Услуги_Приход (Заказы_Код,Фабрики_Код,Контрагенты_Код,Назнач_Платеж_Код,Сумма,Валюты_Код,Дата,Рейсы_Код,Примечание) VALUES";
																query += "("+r['Заказы_Код']+",";
																query += r['Фабрики_Код']+",";
																query += r['Клиенты_Код']+",";
																query += "37,";
																query += "'"+sum+"',";
																query += "'EUR',";
																query += "'"+getDate(false,true)+"',";
																query += r['Рейсы_Код']+",";
																query += "'Объем: "+r['Объем'] + "')";
																$.ajaxShort({
																	data:{
																		action:'edit',
																		query:query
																	},
																	success:function()
																	{
																		loop();
																	}
																});
															}
														});
													},
													callback:function()
													{
														$(dialog).dialog("close");
														$(grid).trigger("reloadGrid",{current:true});
													}
												})
											}
										});
									}
									else
										return $.alert('Невозможно оформить складскую обработку!');
								}
							});
						}
					})
				}
			},
			haul_eu:{
				name:'Доставка по Европе',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cogs';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var t = this;
					$.confirm({
						message:'Вы уверены, что хотите оформить доставку по Европе ?',
						done_func:function(){
							$.ajaxShort({
								dataType:'JSON',
								data:{
									action:'view',
									responseType:'multiple',
									query:'SELECT * FROM Расчет_ДоставкаПоЕвропе_Шаг2 WHERE Рейсы_Код = ' + rowid
								},
								success:function(data)
								{
									if(data.length > 0)
									{
										$.asyncloop({
											length:data.length,
											loop_action:function(loop,i)
											{
												$.confirmHTML({
													width:450,
													html:function()
													{
														var text = 'Для клиента '+data[i]['Клиент']+' с фабрики '+data[i]['Фабрика'];
														return $.genHTML({type:'input-top-label',options:{input_value:data[i]['Сумма'],label_text:text}});
													},
													done_func:function()
													{
														var sum = parseFloat($('input',this).val());
														var insert_vals = data[i]['Клиенты_Код']+",12,"+sum+",'EUR','"+getDate(false,true)+ "',"+data[i]['Рейсы_Код']+','+data[i]['Фабрики_Код']+','+data[i]['Заказы_Код'];
														if(!isNaN(sum))
														{
															$.ajaxShort({
																data:{
																	action:'edit',
																	query:'DELETE FROM V_Услуги_Приход WHERE Назнач_Платеж_Код = 12 AND Заказы_Код = '+ data[i]['Заказы_Код'] +' AND Рейсы_Код = '+ data[i]['Рейсы_Код']
																},
																success:function()
																{
																	$.ajaxShort({
																		data:{
																			action:'edit',
																			query:"INSERT INTO V_Услуги_Приход (Контрагенты_Код,Назнач_Платеж_Код,Сумма,Валюты_Код,Дата,Рейсы_Код,Фабрики_Код,Заказы_Код) VALUES ("+ insert_vals +")"
																		},
																		success:function()
																		{
																			loop();
																		}
																	});
																}
															});

														}
													},
													cancel_func:function()
													{
														$(this).dialog('destroy');
													}
												});
											},
											callback:function()
											{
												$(t).trigger("reloadGrid",{current:true});
											}
										})
									}
									else
										return $.alert('Невозможно оформить доставку по Европе');
								}
							});
						}
					})
				}
			},
			haul_sucret:{
				name:'Дополнительная инф-ция',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cube';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.confirmHTML({
						title:'',
						width:150,
						html:function()
						{
							return $.genHTML({type:'simple-input',options:{input_inline_style:'text-align:center',input_type:'password'}});
						},
						done_func:function()
						{
							rowObject['require'] = $('input',this).val();
							gridPseudo.plugin({
								name:'sucret',
								data:rowObject,
								dialog_title:'Дополнительная инф-ция',
								dialog_width:1200,
								dialog_height:500,
								dialog_refresh_grid:true
							});
						}
					})
				}
			}
		},
		navGrid:true,
		cn:[
			"Код","Номер рейса","Номер машины","Номер машины 2","Объем","Дата начала<br>Расчетная дата приезда","Европа","Планируемая дата прибытия","Примечание",
			'Таможни_Код','ГТД','Платеж','Валюты_Код','Брутто_ЭД','Мест_ЭД','Перевозчики_Код'
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,
				hidden:<? if($this->User->user_group_name === 'adm'): ?> false <? else: ?> true <? endif; ?>,
				editable:false,editrules:{edithidden:false},hidedlg:true,
				searchoptions: {sopt:['eq'],searchhidden: true}
			},

			{
				name: "Номер",index:"Номер",width:100,stype:'select',
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Рейсы',flds:['Название_'],id_only:true,order:1,sfld:'Название_',refid:'Название_'})}
				}
			},

			{
				name: "НомерМашины",index:"НомерМашины",width:100
			},

			{
				name: "НомерМашины2",index:"НомерМашины2",width:100
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:'integer',
				editoptions:{
					dataInit:function(elem,opts) {
						new jqGrid_aw_combobox$(elem,{},opts,this,{
							list:[['---','NULL'],['0',0],['93',93],['120',120],['82',82],['20f',20],['40f',40],['HQ',60]]
						});
					}
				}
			},

			{
				name: "Дата_Начала_Окончания_Расчет",index:"Дата_Начала_Окончания_Расчет",width:100,formatter:'textarea',editable:false,
				//searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'}
			},

			{
				name: "Европа",index:"Европа",width:50,stype:'select',edittype:'checkbox',editable:false,
				formatter:function(cellvalue, options, rowObject){
					if(cellvalue == 0)
						return '<i style="font-size:2em;color:#ff0000" class="fa fa-times"></i>';
					else if(cellvalue > 0)
						return '<i style="font-size:2em;;color:#228b22" class="fa fa-check"></i>';
				}
			},

			/*
			{
				name: "Склад",index:"Склад",width:50,stype:'select',edittype:'checkbox',editable:false,
				formatter:function(cellvalue, options, rowObject){
					if(cellvalue == 0)
						return '<i style="font-size:2em;color:#ff0000" class="fa fa-times"></i>';
					else if(cellvalue > 0)
						return '<i style="font-size:2em;;color:#228b22" class="fa fa-check"></i>';
				}
			},

			{
				name: "files_count",index:"files_count",width:50,stype:'select',edittype:'checkbox',editable:false,
				formatter:function(cellvalue, options, rowObject){
					if(cellvalue > 0)
						return '<i style="font-size:2em;;color:#228b22" class="fa fa-check"></i>';
					else
						return '<i style="font-size:2em;color:#ff0000" class="fa fa-times"></i>';
				}
			},
			*/
			{
				name: "Планируемая_Дата_Прибытия",index:"Планируемая_Дата_Прибытия",width:120,formatter:'date',
				searchoptions:{
					sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp
				},
				formatoptions: {
					srcformat:'Y-m-d',newformat:'d.m.Y'
				},
				editoptions: {
					maxlengh: 10,dataInit: elemWd
				}
			},
			{
				name: "Примечание",index:"Примечание",width:400,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Таможни_Код",index:"Таможни_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			},

			{
				name: "ГТД",index:"ГТД",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			},

			{
				name: "Платеж",index:"Платеж",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			},

			{
				name: "Валюты_Код",index:"Валюты_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			},

			{
				name: "Брутто_ЭД",index:"Брутто_ЭД",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			},

			{
				name: "Мест_ЭД",index:"Мест_ЭД",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			},

			{
				name: "Перевозчики_Код",index:"Перевозчики_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,searchoptions: {searchhidden: true}
			}
		],
		options:{
			cellEdit:true,
			shrinkToFit:true
		},
		subGridOps:[
			{
				subgrid:true,
				main:false,
				name:'hauls_service',
				table:'V_Услуги_приход',
				tableQuery: "Услуги_Приход",
				id:'Код',
				beforeSubmitCell:true,
				delOpts:true,
				delFormOptions:{
					delData:{
						tid:'Код',
						tname:'Услуги_Приход'
					}
				},
				navGrid:true,
				formpos:false,
				customButtons:[
					{
						caption:'Удалить обработку',
						icon:'fa fa-trash',
						click:function(e)
						{
							var grid = this;
							var haulId = grid.p.postData.mainId;
							if(!this.p.postData.mainId)
								return;
							$.confirm({
								message:'Вы уверены, что хотите удалить складскую обработку ?',
								done_func:function()
								{
									$.ajaxShort({
										data:{
											oper:'del',
											tname:'V_Услуги_приход',
											id:haulId,
											tid:'Рейсы_Код',
											filters:JSON.stringify({
												groupOp:'AND',
												rules:[
													{field: 'Назнач_Платеж_Код',op: 'eq', data: '37'}
												]
											})
										},
										success:function(){
											$(grid).trigger("reloadGrid",{current:true});
										}
									});
								}
							});
						}
					},
					{
						caption:'Удалить доставку по EU',
						icon:'fa fa-trash',
						click:function(e)
						{
							var grid = this;
							var haulId = grid.p.postData.mainId;
							if(!this.p.postData.mainId)
								return;
							$.confirm({
								message:'Вы уверены, что хотите удалить доставку по Европе ?',
								done_func:function()
								{
									$.ajaxShort({
										data:{
											oper:'del',
											tname:'Услуги_приход',
											id:haulId,
											tid:'Рейсы_Код',
											filters:JSON.stringify({
												groupOp:'AND',
												rules:[
													{field: 'Назнач_Платеж_Код',op: 'eq', data: '12'}
												]
											})
										},
										success:function(){
											$(grid).trigger("reloadGrid",{current:true});
										}
									});
								}
							});
						}
					},
					{
						caption:'Заполнить EX',
						icon:'fa fa-leaf',
						click:function(e)
						{
							var grid = this;
							var haulId = grid.p.postData.mainId;
							if(!this.p.postData.mainId)
								return;
							$.confirm({
								message:'Вы уверены, что хотите заполнить EX?',
								done_func:function()
								{
									var query = "INSERT INTO Услуги_Приход (Контрагенты_Код, Назнач_Платеж_Код, Сумма, Валюты_Код, Рейсы_Код, Заказы_Код, Фабрики_Код, Дата) ";
									query += "SELECT Клиенты_Код, 22, 25, 'EUR', Рейсы_Код, Код, Фабрики_Код, getdate() FROM Заказы WHERE Рейсы_Код = " + haulId + " AND charindex('ex',lower(Примечание_фин))>0";
									query += " AND charindex('ex в',lower(Примечание_фин))=0;";
									$.ajaxShort({
										data:{
											action:'edit',	
											query:query
										},
										success:function(){
											$(grid).trigger("reloadGrid",{current:true});
										}
									});
								}
							});
						}
					},
					{
						caption:'Удалить EX',
						icon:'fa fa-trash',
						click:function(e)
						{
							var grid = this;
							var haulId = grid.p.postData.mainId;
							if(!this.p.postData.mainId)
								return;
							$.confirm({
								message:'Вы уверены, что хотите удалить EX?',
								done_func:function()
								{
									$.ajaxShort({
										data:{
											oper:'del',
											tname:'Услуги_приход',
											id:haulId,
											tid:'Рейсы_Код',
											filters:JSON.stringify({
												groupOp:'AND',
												rules:[
													{field: 'Назнач_Платеж_Код',op: 'eq', data: '22'}
												]
											})
										},
										success:function(){
											$(grid).trigger("reloadGrid",{current:true});
										}
									});
								}
							});
						}
					}										
				],
				beforeShowForm:function(form)
				{
					var selrow_data = $(this).getRowData(this.p.selrow);
					var order_input = $('input[name="Заказы_Код"]',form);
					if(!selrow_data['Заказы_Код'] && selrow_data['Контрагенты_Код'] && selrow_data['Фабрики_Код'])
					{
						$.ajaxShort({
							dataType:'JSON',
							data:{
								responseType:'multiple',
								action:'view',
								query:'SELECT Код,Номер_Заказа FROM Заказы WHERE Рейсы_Код = ' +selrow_data['Рейсы_Код']+ ' AND Клиенты_Код = ' +selrow_data['Контрагенты_Код']+ ' AND Фабрики_Код = ' + selrow_data['Фабрики_Код']
							},
							success:function(data)
							{
								if(data.length == 0)
									return $.alert('Critical error!');
								if(data.length === 1)
								{
									order_input.val(data[0]['Код']);
									order_input.trigger('change');
								}
								else
								{
									$.confirmHTML({
										title:'Выберите номер заказа',
										width:250,
										dialog_opts:
										{
											modal:false,
										},
										dialog_extend_opts:
										{
											load:function()
											{
												$(this).parent('div').css('z-index',1000);
											}
										},
										html:function()
										{
											var options = new Array();
											$.each(data,function(i,el)
											{
												options.push({text:el['Номер_Заказа'],value:el['Код']});
											});
											return $.genHTML({type:'simple-select',options:{name:'Валюта',data:options}});
										},
										done_func:function(html)
										{
											order_input.val(html.value);
											order_input.trigger('change');
										}
									})
								}
							}
						});
					}
				},
				navGridOptions:{add:true,edit:true},
				subgridpost:{
					mainidName:'Рейсы_Код'
				},
				footer:[
					{col:'Сумма',calc:'sum'}
				],
				cn:[
					"Код","Рейсы_Код","№ Заказа","Контрагент","Фабрика","Номер_Заказа","Услуга","Сумма","Валюта","Дата","Примечание"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Рейсы_Код",index:"Рейсы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Заказы_Код",index:"Заказы_Код",width:50,celleditable:false,editoptions:{disabled:true},
					},

					{
						name: "Контрагенты_Код",index:"Контрагенты_Код",width:200,formatter:'select',celleditable:false,
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Б_Контрагенты_EN']) ?>
						},
						editoptions:{
							dataInit:function(elem,opts)
							{
								var grid = this;
								var this_combo = new jqGrid_aw_combobox$(elem,{
									tname:'Б_Контрагенты_EN',
									getNull:false,
									refid:'Код',
									ref_fld:'Клиенты_Код',
									ref_tname:'Заказы',
									search:'Рейсы_Код = ' + this.p.postData.mainId
								},opts,this);
								if(!this_combo.init_value)
								{
									$(this_combo.select_helper).on('change',function(event)
									{
										this_combo.isOpened = false;
										get_fabrics_orders(grid,this.value);
									});
								}
							}
						}
					},

					{
						name: "Фабрики_Код",index:"Фабрики_Код",width:200,formatter:'select',celleditable:false,
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Фабрики']) ?>
						},
						editoptions:{
							disabled:true,
							dataInit:function(elem,opts)
							{
								new jqGrid_aw_combobox$(elem,{tname:'Б_Контрагенты_EN',refid:'Код',ref_fld:'Фабрики_Код',ref_tname:'Заказы',getNull:false},opts,this)
							}
						}
					},
					{
						name: "Номер_Заказа",index:"Номер_Заказа",width:250,editable:false
					},
					{
						name: "Назнач_Платеж_Код",index:"Назнач_Платеж_Код",width:200,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Б_Рейсы_Услуги_Приход']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Рейсы_Услуги_Приход'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{getNull:false,tname:'Б_Рейсы_Услуги_Приход'},opts,this); }
						}
					},

					{
						name: "Сумма",index:"Сумма",width:50,formatter:floatFormatter
					},

					{
						name: "Валюты_Код",index:"Валюты_Код",width:200,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Валюты']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Валюты'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{getNull:false,tname:'Валюты'},opts,this); }
						}
					},

					{
						name: "Дата",index:"Дата",width:120,formatter:'date',inlinedefaultvalue:getDate(true),
						searchoptions:{
							sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp
						},
						formatoptions: {
							srcformat:'Y-m-d',newformat:'d.m.Y'
						},
						editoptions: {
							defaultValue:getDate(true),maxlengh: 10,dataInit: elemWd
						}
					},

					{
						name: "Примечание",index:"Примечание",width:250,align:"left",
						cellattr:textAreaCellAttr,
						edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
					}
				],
				options:{
					shrinkToFit:true,
					height:'100%',
					cellEdit:true
				}
			}
		]
	})
})
</script>
