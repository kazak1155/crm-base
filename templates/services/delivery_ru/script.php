<script>
$(function(){
	$('.select2me').select2('open');
	$('.select2me').change(function(e)
	{
		delivery.renew_external_sub(this.value);
	});
	var delivery = new jqGrid$({
		defaultPage:false,
		subgrid:true,
		main:true,
		name:'delivery',
		filterToolbar:true,
		table:'Услуги_Доставка_РФ',
		title:'Доставка РФ',
		id:'Код',
		navGrid:true,
		navGridOptions:{},
		customButtons:[
			{
				caption:'Зарегистрировать',
				icon:'fa fa-pencil',
				click:function(e)
				{
					var grid = this;
					var haul_sel = $('#hail_sel');
					var haul_text = haul_sel.find('option:selected').text();
					var anything_registered = $(this).getRowData().shift()['Счета_Выставлен'];
					//if(anything_registered == 1)
					//	return $.alert('Услуги рейса №'+haul_text+' уже зарегистрованы!');
					$.confirmHTML({
						width:300,
						title:'Регистрация услуг на рейс №'+ haul_text +'.',
						html:function()
						{
							var date = $.genHTML({type:'input-top-label',options:{ label_text:'Дата регистрации',name:'Дата',readonly:true,input_value:getDate(true,false,false,true) }});
							$(date).find('input').datepicker({
								dateFormat:"mm.dd.yy" + " " + getTime()
							});
							return date;
						},
						done_func:function(html)
						{
							var dialog = this;
							var data = $(html).find('input[name="Дата"]').get(0);
							var s_data = data.value.split(' ');
							var r_s_data = s_data[0].split('.');
							r_s_data = r_s_data[2]+r_s_data[1]+r_s_data[0];
							var exec = "exec SP_Выставить_Услуги " + haul_sel.val() + ",'"+r_s_data+" "+ s_data[1] +":00'";
							$.ajaxShort({
								data:{
									action: 'add',
									query: exec
								},
                                success:function()
                                {
                                    $.confirm({
                                        message: 'Уведомить о выставлении счета на рейс ' + haul_text + ' ?',
                                        done_func:function()
                                        {
                                            grid.p.gridProto.plugin({
                                                frame_type: 'email',
                                                dialog_height:650,
                                                data:{
                                                    mail_id: 59,
                                                    mail_to: 'info@prgs.pro',
                                                    'Рейс': haul_text
                                                },
                                                dialog_refresh_grid:true
                                            });
                                        }
                                    })
                                }
							});
						}
					});
				}
			}
		],
		contextMenuFileTree: true,
		contextMenuFileTreeItems:{
			ft_ex:{
				name:'Все',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-folder-open';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					$.file_Tree({
						s_prm:{
							reference:'tig/orders',
							tname:'Заказы',
							folder:'',
							rowid:rowid
						}
					});
				}
			},
		},

		contextMenuItems:{
			add_comodity:{
				name:'Комплекс',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-truck';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					$.confirmHTML({
						yes:'Присвоить',
						no:'Закрыть',
						title:'',
						width:300,
						html:function()
						{
							var wrapper = document.createElement('div');
							wrapper.appendChild($.genHTML({
								type:'simple-select',
								options:{
									width:'270px',
									name:'Валюта',
									data:[
										{text:'Евро',value:'EUR'},
										{text:'Доллар США',value:'USD'},
										{text:'Рубли',value:'RUB'}
									]
								}
							}));
							$(wrapper).find('select').select2();
							wrapper.appendChild($.genHTML({
								type:'input-top-label',
								options:{
									autofocus:true,
									input_verification:'numeric',
									label_text:'Сумма',
									name:'Сумма',
									required:true
								}
							}));
							return wrapper;
						},
						done_func:function()
						{
							var cost = $(this).find('input[name="Сумма"]').val();
							if(cost.indexOf(',') > -1)
								cost = cost.replace(',','.');
							var currency = $(this).find('select[name="Валюта"]').val();
							if(!cost || !currency)
								return false;
							var query = "INSERT INTO Услуги_Приход (Контрагенты_Код,Назнач_Платеж_Код,Сумма,Валюты_Код,Рейсы_Код,Заказы_Код,Дата)";
							query += " VALUES ("+rowObjectFormatted['Клиенты_Код']+",9,"+ cost +",'"+currency+"',"+rowObjectFormatted['Рейсы_Код']+","+rowid+",'"+getDate(true)+"')";
							$.ajaxShort({
								data:{
									action:'add',
									query:query
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
			del_comodity:{
				name:'Удалить комплекс',
				disabled:function(name,other)
				{
					var sel_id = $(this)[0].p.selrow;
					var sel_row = $(this).getRowData(sel_id);
					if(sel_row['Расчет'] == 1)
						return true;
				},
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-trash';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					$.ajaxShort({
						data:{
							action:'edit',
							//query:"DELETE FROM Услуги_Приход WHERE Назнач_Платеж_Код = 9 AND Рейсы_Код = "+rowObjectFormatted['Рейсы_Код']+" AND Контрагенты_Код = "+rowObjectFormatted['Клиенты_Код']+" AND Заказы_Код = "+rowid
							query:"DELETE FROM Услуги_Приход WHERE Назнач_Платеж_Код = 9 AND Рейсы_Код = "+rowObjectFormatted['Рейсы_Код']+" AND Заказы_Код = "+rowid
						},
						success:function()
						{
							$(grid).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			edit_bill:{
				name:'Зарегистрировать на заказ',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-recycle';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					var grid = this;
					var haul_sel = $('#hail_sel');
					$.ajaxShort({
						data:{
							action:"edit",
							query:"SP_Выставить_Услугу "+haul_sel.val()+","+rowid
						},
						success:function()
						{
							$(grid).trigger("reloadGrid",{current:true});
						}
					});
				}
			}
		},
		footer:[
			{col:'Объем',calc:'sum'},
			{col:'Обьем_состав',calc:'sum'},
			{col:'Вес',calc:'sum'},
			{col:'Вес_состав',calc:'sum'},
			{col:'Сумма',calc:'sum'},
			{col:'Сумма_Услуги',calc:'sum'}
		],
		subgridpost:{
			mainid:'',
			mainidName:'Рейсы_Код'
		},
		cn:[
			"Код","РейсКод","Клиент","Подклиент","Фабрика","№ заказа","Объем","Σ Объем","Вес","Σ Вес","Ин.Склад","Заезд","Расчет","Σ (€)",'Σ/др',"Сбор","Тариф","<i class='fa fa-thumbs-up'></i>"
			,"ИнСклад_Выставлен_Кальк"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:false
			},

			{
				name: "Рейсы_Код",index:"Рейсы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:false
			},

			{
				name: "Клиенты_Код",index:"Клиенты_Код",width:250,stype:'select',formatter:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Клиенты_EN']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Клиенты_EN'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_Клиенты_EN'},opts,this); }
				},
			},

			{
				name: "Подклиенты_Код",index:"Подклиенты_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Подклиенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Подклиенты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_Подклиенты'},opts,this); }
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Фабрики_EN']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Фабрики_EN'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_Фабрики_EN'},opts,this); }
				}
			},

			{
				name: "Номер_Заказа",index:"Номер_Заказа",width:200,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter
			},

			{
				name: "Объем_состав",index:"Объем_состав",width:50,formatter:floatFormatter,
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					var vol = Number(rawObject['Объем']);
					var f_val = Number(val);
					if(f_val > vol)
						return "style='background-color:#228b22;color:#FFF'";
					else if(f_val < vol && f_val >= (vol * 0.9))
						return "style='background-color:#FFFF66;'";
					else if(f_val < (vol * 0.9))
						return "style='background-color:#FF3232;color:#FFF'";
				}
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			},

			{
				name: "Вес_состав",index:"Вес_состав",width:50,formatter:floatFormatter,
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					var vol = Number(rawObject['Вес']);
					var f_val = Number(val);
					if(f_val > vol)
						return "style='background-color:#228b22;color:#FFF'";
					else if(f_val < vol && f_val >= (vol * 0.9))
						return "style='background-color:#FFFF66;'";
					else if(f_val < (vol * 0.9))
						return "style='background-color:#FF3232;color:#FFF'";
				}
			},

			{
				name: "Заявка_на_инсклад",index:"Заявка_на_инсклад",width:60,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:60,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},
			{
				name: "Заезд",index:"Заезд",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:false
			},	
	

			{
				name: "Расчет",index:"Расчет",width:60,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:60,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Сумма",index:"Сумма",width:200,formatter:floatFormatter,
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if(Number(val) == 0)
						return "style='background-color:#FF3232;color:#FFF'";

					if(val.indexOf('Просчет') > -1)
					{
						var ret = "style='background-color:#FFFF66;color:#000;font-weight:700'";
						if(val.indexOf('-1') > -1)
							ret += 'title="Нетарифная страна"';
						else if(val.indexOf('-2') > -1)
							ret += 'title="Не указан Объем/Вес"'; //Есть Объем, но нет веса (нельзя посчитать плотность)"';
						else if(val.indexOf('-3') > -1)
							ret += 'title="Плотность превышает максимально допустимую по тарифу"';
						else if(val.indexOf('-4') > -1)
							ret += 'title="Есть категории с незаданной стоимостью (Тариф по категориям и весу)"';
						else if(val.indexOf('-6') > -1)
							ret += 'title="Нерасчетный регион, провинция"';
						else if(val.indexOf('-7') > -1)
							ret += 'title="Нерасчетный перевозчик"';														
						return ret;
					}

				},
				formatter:function(cellvalue, options, rowObject)
				{
					if(parseFloat(cellvalue) < 0)
						return 'Просчет '+ parseInt(cellvalue).toFixed(0);
					return cellvalue;
				},
				unformat:function(cellvalue, options, cell)
				{
					if(cellvalue.indexOf('Просчет') > -1)
						return 0.00;
					return cellvalue;
				}
			},

			{
				name: "Сумма_Услуги",index:"Сумма_Услуги",width:80,
				formatter:function(cellvalue, options, rowObject)
				{
					/*if(cellvalue.indexOf instanceof Function && cellvalue.indexOf('br') != -1)
						return cellvalue.replace(/ br /g,' <br />');
					else
						return cellvalue;
					*/
					if(cellvalue != null)
						return cellvalue.replace(/ br /g,' <br />');
					else
						return cellvalue;
				},
				unformat:function(cellvalue, options, cell)
				{
					var cv_return = 0.00;
					if(!cellvalue)
						return cv_return;
					var match = cellvalue.match(/EUR\:\s(.?\d+\.\d{2})/);
					if(match[1] !== undefined)
						cv_return = parseFloat(match[1]);
					return cv_return
				},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					var podvoz = Number(rawObject['Тип_Самоподвоза']);
					var sbor = Number(rawObject['Сбор']);
					var Raschet = Number(rawObject['Расчет']);
					var Insklad_Vystavlen_Calc = Number(rawObject['ИнСклад_Выставлен_Кальк']);
					var f_val = Number(val);
					if (((podvoz == 1) || (podvoz == 3)) && (sbor < 0) && (Raschet == 1) && (Insklad_Vystavlen_Calc == 0))
						return "style='background-color:#FFFF66;color:#000;'";
				}
			},
			{
				name: "Сбор",index:"Сбор",width:60,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:false
			},	


			{
				name: "Тариф_Россия",index:"Тариф_Россия",width:100,formatter:'select',stype:'select',addformeditable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Тарифы2']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Тарифы2'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Тарифы2'},opts,this); }
				}
			},

			{
				name: "Счета_Выставлен",index:"Счета_Выставлен",width:30,gtype:'checkbox',search:false
			},
			{
				name: "ИнСклад_Выставлен_Кальк",index:"ИнСклад_Выставлен_Кальк",hidden:true,editable:false,editrules:{edithidden:false},hidedlg:false
			},
			
		],
		options:{
			hoverrows:false,
			shrinkToFit:true
		},
		events:{
			ondblClickRow:function(rowid,iRow,iCol,event)
			{
				var row_data = $(this).getRowDataRaw(rowid);
				this.p.gridProto.plugin({
					name:'order_services',
					dialog_title:'Услуги клиента '+ row_data['Клиенты_Код'] +' фабрики '+ row_data['Фабрики_Код'] +'.',
					data:$(this).getRowData(rowid),
					dialog_width:1000,
					dialog_height:400
				});
			}
		},
		subGridOps:[
			{
				subgrid:true,
				main:false,
				name:'orders_details',
				table:'Заказы_Состав',
				id:'Код',
				hideBottomNav:true,
				footer:[
					{col:'Кол_предм',calc:'sum'},
					{col:'Кол_мест',calc:'sum'},
					{col:'Объем',calc:'sum'},
					{col:'Вес',calc:'sum'}
				],
				subgridpost:{
					mainidName:'Заказы_Код'
				},
				cn:[
					"Код","Заказы_Код","Вид груза","Артикул","# предм","Материал","# мест","Объем","Вес","Примечание"
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
					}
				],
				options:{
					shrinkToFit:true,
					autowidth:false,
					width:1500,
					height:'100%',
				},
				events:{
				}
			}
		]
	})
});
</script>
