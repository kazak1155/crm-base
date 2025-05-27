<?php
if(!isset($this))
{
	header("Status: 301 Moved Permanently");
	header("Location:http://".$_SERVER['HTTP_HOST']."/php/tmpl/iframe_tmpl?forced=true&reference=".$_SERVER['REQUEST_URI']);
	exit;
}
$this->con_database('sales');
?>
<script type="text/javascript">
$(function() {
	var orders = new jqGrid$({
		main:true,
		name:'orders_prepare',
		table:'Форма_Заказы_Prepare',
		title:'Заказы',
		formpos:false,
		tableQuery:'Заказы',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		beforeSubmitCell:true,
		contextMenuFileTree: true,
		contextMenuItems:{
			/*
			order_creation:{
				name:'Мастер формирования заказа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-book';
				},
				disabled:function(name,other)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(isNaN(parseInt(sel_row['Кол_во'])))
						return true;
				},
				visible:function()
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Статус_Код'] > 20)
						return false;
					else
						return true;
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					gridPseudo.iframe_sub({
						url:'/themes/shop/orders/frame_order_form',
						id:rowid,
						data:rowObject,
						dialog_width:1000,
						dialog_height:550,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Мастер заказа №'+ rowid,
								draggable:true,
								modal:false,
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
			*/
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
						url:'/themes/shop/orders_prepare/frame_order_cross',
						id:rowid,
						data:rowObjectFormatted,
						dialog_width:600,
						dialog_height:400,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Кросс курсы заказа',
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
						url:'/themes/shop/orders_prepare/frame_order_services',
						id:rowid,
						data:rowObjectFormatted,
						dialog_width:600,
						dialog_height:400,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Услуги по заказу',
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
							select.innerHTML = "<?php echo $this->get_lib_html(['tname'=>'srv_З_Б_Пользователи']); ?>";
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
			},
			order_contract:{
				name:'Сформировать договор',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-file-pdf-o';
				},
				visible:function()
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Статус_Код'] > 20)
						return false;
					else
						return true;
				},
				disabled:function(name,other)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Сформирован'] == 0)
						return true;
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					gridPseudo.iframe_sub({
						url:'/themes/shop/orders_prepare/frame_order_bill',
						id:rowid,
						data:rowObject,
						dialog_width:1000,
						dialog_height:550,
						dialog_custom_opts:{
							dialog_opts:{
								title:'Cчет заказа №'+ rowid,
								draggable:true,
								modal:false,
								close:function(event,ui){
									var pdf = $(this).attr('button-close'),dialog = this;
									if(pdf)
									{
										if(parseInt(rowObjectFormatted['Статус_Код']) == 10)
										{
											$.ajaxShort({
												data:{
													action:"edit",
													query:"UPDATE Заказы SET Статус_Код = 20 WHERE Код = " + rowid
												},
												success:function(){ $(grid).trigger("reloadGrid",{current:true}); $(dialog).remove(); }
											});
										}
										get_file_url({qry:rowid},'Счет',{prefix:'pdf',folder:'shop_bill'});
									}
									else
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
			order_bill:{
				name:'Выставить счет',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-file-excel-o';
				},
				visible:function()
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Статус_Код'] != 20)
						return false;
					else
						return true;
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.alert('Выставляем счет > Высылаем счет > Ставим статус "В ожидании предоплаты"','Скоро')
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
		navGridOptions:{add:true},
		delOpts:true,
		addFormOptions:{
			zIndex:100,
			modal:false,
			drag:false,
			onClose:function()
			{
				if($('.confirm-html').length > 0)
					$('.confirm-html').dialog("close");
				if($('.choose-client').length > 0)
					$('.choose-client').dialog("close");
			},
			beforeShowForm:function(form)
			{
				$('.ui-widget-overlay').remove();
				var grid = this;
				var $client_input = $('#Клиент'),$client_type = $('#Тип_Код');
				$client_input.attr('readonly','readonly');
				$client_input.focus(function(e){
					if($client_type.val().length > 0 || $('.choose-client').length > 0)
						return;
					$.alert('Выберите тип клиента','',{
						classNames:'choose-client',
						dialog_opts:{
							open:function(){
								$client_input.blur();
							},
							height:80,
							buttons:null,
							modal:false,
							position:{ my: "left top", at: "right top", of: form.parents('.ui-jqdialog') }
						},
						dialog_extend_opts:{
							closable:false,
							titlebar:'none'
						}
					})
				})
				$client_type.change(function(e)
				{
					if($('.choose-client').length > 0)
						$('.choose-client').dialog("close");
					var type_input = this;
					if(this.value == 2)
					{
						if($('.confirm-html').length > 0)
							$('.confirm-html').remove();
						if(!$client_input[0].hasOwnProperty('jqGrid_aw_combobox_proto'))
							new jqGrid_aw_combobox$($client_input[0],{tname:'З_Б_Клиенты',getNull:false},{name:'Клиент_Код',index:'Клиент_Код'},grid);
					}
					else if(this.value == 1)
					{
						if($client_input[0].hasOwnProperty('jqGrid_aw_combobox_proto'))
							$client_input[0].jqGrid_aw_combobox_proto.destroy();
						$client_input = $('#Клиент');
						$.confirmHTML({
							title:'Контактные данные клиента',
							yes:'Создать',
							no:'Закрыть',
							width:250,
							preventOkClose:true,
							dialog_opts:{
								show: 'drop',
								modal:false,
								draggable:false,
								position:{ my: "left", at: "right", of: form.parents('.ui-jqdialog') }
							},
							html:function()
							{
								var wrapper  = document.createElement('form');
								var hidden_submit = document.createElement('input');
								hidden_submit.type ='submit';
								$(hidden_submit).css('display','none');
								wrapper.className = 'client_info_form';
								wrapper.appendChild($.genHTML({type:'input-top-label',options:{label_text:'Email',name:'email',input_type:'email',autofocus:true}}));
								wrapper.appendChild($.genHTML({type:'input-top-label',options:{label_text:'Фамилия',name:'fname'}}));
								wrapper.appendChild($.genHTML({type:'input-top-label',options:{label_text:'Имя',name:'sname'}}));
								wrapper.appendChild($.genHTML({type:'input-top-label',options:{label_text:'Телефон',name:'phone',required:false}}));
								wrapper.appendChild(hidden_submit)
								$('input[name="email"]',wrapper).bind('keyup',function(e){
									if( e.keyCode == 86)
										return;
									var em = this.value,t = this;
									if(this.checkValidity() && this.value.indexOf('.') > 0)
									{
										$.ajaxShort({
											dataType:'JSON',
											data:{
												action:'view',
												responseType:'single',
												query:"SELECT * FROM Форма_Контрагенты WHERE Тип_Код = "+ type_input.value +" AND Email = '" + em + "'"
											},
											success:function(data)
											{
												if(data !== false)
												{
													var name = data['Название'].split(' ');
													$('input[name="fname"]',wrapper).val(name[0]).attr('disabled','disabled');
													$('input[name="sname"]',wrapper).val(name[1]).attr('disabled','disabled');
													$('input[name="phone"]',wrapper).val(data['Телефон']).attr('disabled','disabled');
													$(t).attr('disabled','disabled');
													$client_input.val(data['Название']).attr({'disabled':'disabled',realval:data['Код']}).css({'background':'rgb(235, 235, 228)'});
													$(type_input)[0].jqGrid_aw_combobox_proto.disable();
													$('.confirm-html').siblings('.ui-dialog-buttonpane').remove();
													/* TODO FIND NON READY ORDERS! */
												}
											}
										});
									}
								})
								return wrapper;
							},
							done_func:function()
							{
								var dialog = this,frm = $('.client_info_form');
								if(frm[0].checkValidity() == false)
								{
									frm.find(':submit').click();
									return false;
								}
								var frm_obj = frm.serializeObject()
								$.ajaxShort({
									dataType:'JSON',
									data:{
										action:'view',
										responseType:'single',
										query:"SELECT * FROM Форма_Контрагенты WHERE Тип_Код = "+ type_input.value +" AND Email = '" + frm_obj.email + "'"
									},
									success:function(data)
									{
										if(data.length == 0 || data === false)
										{
											$.ajaxShort({
												data:{
													action:'add',
													getid:true,
													tname:'Контрагенты',
													query:"INSERT INTO Контрагенты (Тип_Код,Название) VALUES ("+ type_input.value +",'"+ frm_obj.fname.concat(' ',frm_obj.sname) +"')"
												},
												success:function(clid){
													$.ajaxShort({
														data:{
															action:'add',
															query:"INSERT INTO Контрагенты_Контакты (Контрагенты_Код,Телефон,Email) VALUES ("+ clid +",'"+ frm_obj.phone +"','"+ frm_obj.email +"')"
														},
														success:function(){
															$client_input.val(frm_obj.fname.concat(' ',frm_obj.sname)).attr({'disabled':'disabled',realval:clid}).css({'background':'rgb(235, 235, 228)'});
															$(type_input)[0].jqGrid_aw_combobox_proto.disable();
															$(dialog).dialog('close');
														}
													});
												}
											});
										}
										else
										{
											if(data.blacklisted == 1)
											{
												$.alert('Найден клиент с данным email-ом, он находится в черном списке.');
											}
											else
											{
												/* TODO Open list of non-paid orders */
											}
										}
									}
								});
							},
							cancel_func:function()
							{
								$(type_input).siblings('.awesomplete').find('input').val('')
								$(this).dialog('close');
							}
						})
					}
				});
			},
			beforeSubmit:function(postdata, formid)
			{
				if($('[realval]',formid).length > 0)
				{
					delete postdata['Клиент'];
					postdata['Клиент_Код'] = $('[realval]',formid).attr('realval');
				}
				postdata.tname = 'Заказы';
				postdata.getid = true;
				delete postdata['Тип_Код'];
				return[true];
			},
			afterComplete:function(response, postdata, formid)
			{
				var new_id = parseInt(response.responseText);
				var grid = this;
				$.confirm({
						title:'',
						width:250,
						message:'Заполнить состав заявки?',
						done_func:function(){
							grid.p.gridProto.iframe_sub({
								url:'/themes/shop/orders/frame_order_contains',
								id:new_id,
								data:postdata,
								dialog_width:800,
								dialog_height:400,
								dialog_custom_opts:{
									dialog_opts:{
										close:function(){
											$(grid).trigger("reloadGrid",{current:true});
											$(this).remove();
										}
									}
								}
							});
						},
						dialog_extend_opts:{
							titlebar:'none'
						}
					});
			}
		},
		delFormOptions:{
			delData:{
				tid:'Код',
				tname:'Заказы'
			},
			beforeSubmit:function(postdata, formid)
			{
				var status = parseInt($(this).jqGrid('getCell',this.p.selrow,'Статус_Код')),
				client_type = parseInt($(this).jqGrid('getCell',this.p.selrow,'Тип_Код')),
				client_id = parseInt($(this).jqGrid('getCell',this.p.selrow,'Клиент_Код'));
				if(status == 100)
					return[false,'Заказ в архиве.'];
				else if(status > 20)
					return[false,'Невозможно удалить заказ в текущем статусе'];
				else
				{
					if(client_type == 1)
					{
						$.confirm({
							message:'Занести клиента в черный список ?',
							done_func:function()
							{
								$.ajaxShort({
									data:{
										action:"edit",
										query:"INSERT INTO Контрагенты_blacklist (Контрагенты_Код) VALUES ("+ client_id +")"
									}
								});
							}
						});
					}
					return[true];
				}
			},
			beforeShowForm:function(form)
			{
				var status = parseInt($(this).jqGrid('getCell',this.p.selrow,'Статус_Код'));
				if(status != 100)
					$('.delmsg',form).text("Заказ будет перенесен в архив.");
			}
		},
		cn:[
			"№",
			"<i class='fa fa-lg fa-calculator'></i>",
			"Тип клиента","Клиент_Код",
			"Клиент","Телефон",
			"Фабрика",
			"Статус","Статус дата","Статус end","Статус примечание",
			"Оплата",
			"У/e за куб","Наценка %",
			"У/е лог.","У/е дог.","Swift","Сформирован",
			"∑ Объем","∑ Вес","∑ Кол-во","Об.сч","Вес.сч",
			"Создал",
			"Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:40,hidden:false,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name:"print_tlc_doc",index:"print_tlc_doc",width:30,editable:false,formatter:'button',search:false,
				formatoptions:{
					value:"<i class='fa fa-cube'></i>",
					onButtonClick:function(elem,rowid,grid)
					{
						var row = $(grid).getRowData(rowid);
						if(isNaN(parseInt(row['Кол_во'])))
							return $.alert('Состав заказа не заполнен.');
						window.open("?reference=shop/orders_master&rowid="+encodeURIComponent(rowid)+"&row_data="+encodeURIComponent(JSON.stringify(row)));
					}
				},
				cellattr:function(){
					return "style='cursor:pointer'";
				}
			},

			{
				name: "Тип_Код",index:"Тип_Код",width:120,formatter:'select',stype:'select',celleditable:false,
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'Контрагенты_Тип']) ?>
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
				name: "Фабрика_Код",index:"Фабрика_Код",formatter:'select',width:150,stype:'select',
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'З_Б_Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Фабрики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Фабрики',getNull:false},opts,this); }
				}
			},

			{
				name: "Статус_Код",index:"Статус_Код",formatter:'select',width:200,stype:'select',//celleditable:false,
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'З_Б_Заказы_Статусы','order'=>'Код']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_Статусы',order:'Код'})}
				},
				editoptions:{
					//disabled:true,
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
						return "style='background-color:#ff6666;color:#000'";
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
				name: "Способ_Оплаты_Код",index:"Способ_Оплаты_Код",formatter:'select',width:80,stype:'select',editable:false,
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'З_Б_Способы_Оплаты']) ?>
				},
				searchoptions:{
					width:80,
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Способы_Оплаты'})}
				},
				editoptions:{
					disabled:true,
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Способы_Оплаты'},opts,this); }
				},
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(!rawObject['Способ_Оплаты_Код'])
						return "style='background-color:#ff6666;color:#000'";
				}
			},


			{
				name: "Стоимость_куба",index:"Стоимость_куба",width:100,editable:false,editable:false,
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(parseFloat(val) == 0 || isNaN(parseFloat(val)))
						return "style='background-color:#ff6666;color:#000'";
				}
			},

			{
				name: "Наценка",index:"Наценка",width:100,editable:false,editable:false,formatter:'nullable_percentage',
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(parseFloat(val) == 0 || isNaN(parseFloat(val)))
						return "style='background-color:#ff6666;color:#000'";
				}
			},

			{
				name: "Валюта_логистика",index:"Валюта_логистика",width:80,formatter:'select',stype:'select',editable:false,
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
				},
				searchoptions:{
					width:80,
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
				},
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(!rawObject['Валюта_логистика'])
						return "style='background-color:#ff6666;color:#000'";
				}
			},

			{
				name: "Валюта_договора",index:"Валюта_договора",width:80,formatter:'select',stype:'select',editable:false,
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
				},
				searchoptions:{
					width:80,
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
				},
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(!rawObject['Валюта_договора'])
						return "style='background-color:#ff6666;color:#000'";
				}
			},

			{
				name: "Swift",index:"Swift",width:100,hidden:true
			},

			{
				name: "Сформирован",index:"Сформирован",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,formatter:'integer'
			},

			{
				name: "Объем",index:"Объем",width:70,formatter:floatFormatter,editable:false,
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(parseFloat(val) == 0 || isNaN(parseFloat(val)))
						return "style='background-color:#ff6666;color:#000'";
				}
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter,editable:false
			},

			{
				name: "Кол_во",index:"Кол_во",width:70,editable:false,
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					if(parseFloat(val) == 0 || isNaN(parseFloat(val)))
						return "style='background-color:#ff6666;color:#000'";
				}
			},

			{
				name: "Об_сч",index:"Об_сч",width:50,editable:false,hidden:true
			},

			{
				name: "Вес_сч",index:"Вес_сч",width:50,editable:false,hidden:true
			},

			{
				name: "Пользователь",index:"Пользователь",formatter:'select',width:160,stype:'select',editable:false,
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'srv_З_Б_Пользователи']) ?>
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
		},
		subGridOps:[
			{
				delOpts:true,
				subgrid:true,
				main:false,
				name:'orders_details',
				table:'Заказы_Состав',
				id:'Код',
				beforeSubmitCell:true,
				navGrid:true,
				hideBottomNav:true,
				formpos:false,
				contextMenuFileTree: true,
				onCellSelect:true,
				navGridOptions:{add:true},
				addFormOptions:{
					reloadAfterSubmit:false,
					afterComplete:function()
					{
						$(this.p.gridProto.parent_grid).trigger("reloadGrid",{current:true});
					}
				},
				subgridpost:{
					mainidName:'Заказы_Код'
				},
				cn:[
					"Код","Заказы_Код","Вид груза","Валюта","Описание","Артикул","Себ.Фабрики","Объем","Кол-во","Вес"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Вид_Груза_Код",index:"Вид_Груза_Код",width:150,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->get_lib(['tname'=>'Б_Виды_Груза']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Виды_Груза'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Виды_Груза',getNull:false},opts,this); }
						}
					},

					{
						name: "Валюта_Код",index:"Валюта_Код",width:100,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->get_lib(['tname'=>'srv_Фин_З_Б_Валюты']) ?>
						},
						searchoptions:{
							width:100,
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_Фин_З_Б_Валюты'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_Фин_З_Б_Валюты',getNull:false},opts,this); }
						}
					},

					{
						name: "Описание",index:"Описание",width:250,align:"left",
						cellattr:textAreaCellAttr,
						edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
					},

					{
						name: "Артикул",index:"Артикул",width:100
					},

					{
						name: "Стоимость_фабрика",index:"Стоимость_фабрика",width:100,formatter:floatFormatter
					},

					{
						name: "Объем",index:"Объем",width:50,formatter:floatFormatter
					},

					{
						name: "Кол_во",index:"Кол_во",width:50
					},
					{
						name: "Вес",index:"Вес",width:50,formatter:floatFormatter
					},
				],
				options:{
					caption:'Состав заказа',
					shrinkToFit:true,
					autowidth:false,
					cellEdit:true,
					width:1500,
					height:'100%'
				}
			}
		]
	});
})
</script>
