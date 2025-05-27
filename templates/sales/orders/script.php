<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'orders',
		table:'Заказы_view',
		title:'Форма Заказы',
		tableQuery:'Заказы',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		goToBlank:true,
		onCellSelect:true,
		beforeSubmitCell:true,
		footer:[
			{col:'Объем',calc:'sum'},
			{col:'Вес',calc:'sum'},
			{col:'Дельта_Деньги',calc:'sum'}
		],
		delFormOptions:{
			delData:{
				tid:'Код',
				tname:'Заказы'
			},
			beforeShowForm:function(form)
			{
				var deleted = $(this).getCell(this.p.selrow,'Удалено');
				deleted = parseInt(deleted);
				if(deleted == 1)
					$('#DelError').css({'display':'inline'}).find('td').html('Заказ будет удален окончательно!')
			}
		},
		addFormOptions:{
			beforeSubmit:function(postdata, formid)
			{
				postdata.tname = 'Заказы';
				return[true];
			}
		},
		contextMenuFileTree: true,
		contextMenuItems:{
			get_order_docs:{
				name:'Подробная информация',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-pie-chart';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
				{
					gridPseudo.plugin({
						name:'finance',
						dialog_title:'Подробная информация по заказу: "'+rowObject['Номер_заказа']+'"',
						dialog_width:1050,
						dialog_height:700
					});
				}
			},
			status_order_journal:{
				name:'Журнал изменения статуса',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-book';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
				{
					gridPseudo.plugin({
						name:'journal',
						dialog_title:'Журнал изменения статуса по заказу: "'+rowObject['Номер_заказа']+'"',
						dialog_width:650,
						dialog_height:420
					});
				}
			},
			money_income:{
				name:'Деньги приход',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-thumbs-o-up';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
				{
					gridPseudo.plugin({
						name:'income',
						dialog_title:'Приход по заказу: "'+rowObject['Номер_заказа']+'"',
						dialog_width:750,
						dialog_height:420
					});
				}
			},
			money_outcome:{
				name:'Расход по рейсу',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-thumbs-o-down';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
				{
					gridPseudo.plugin({
						name:'outcome',
						dialog_title:'Расход по заказу: "'+rowObject['Номер_заказа']+'"',
						dialog_width:750,
						dialog_height:420
					});
				}
			}
			<?php if($this->User->user_group_name === 'adm') { ?>,
			admin_block_order:{
				name:'Администрирование заказа',
				icon: function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-shield';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
				{
					gridPseudo.plugin({
						name:'adm',
						dialog_title:'Подробная информация по заказу: "'+rowObject['Номер_заказа']+'"',
						dialog_width:650,
						dialog_height:420,
						dialog_refresh_grid:true
					});
				}
			}<?php } ?>
		},
		navGridOptions:{add:true,edit:true},
		permFilter:{
			groupOp:"AND",rules:[
				{field:"Удалено",op:"eq",data:"0"},
				{field:"Статус_Код",op:"lt",data:"255"}
			]
		},
		permFilterButtons:[
			{caption:'Удаленные',data:{field:"Удалено",op:"ne",data:"0",perm:true}},
			{caption:'Архив',data:{field:"Статус_Код",op:"ge",data:"0",perm:true}},
		],
		navGrid:true,
		cn:[
			"Код","Клиент","Фабрика","Статус","Номер заказа","Дата подтв.заявки","Дата оплаты клиент","Дата оплаты фабр.","Номер рейса","Объем","Вес","Удалено","Примечание",
			"Дельта деньги",
			"Док-ты логистика",
			"Блокировка","Дата_согласование","Сумма_согласование","Сумма_транспорт"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Клиенты_Код",index:"Клиенты_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Клиенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Клиенты'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Клиенты'},opts,this); }
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Фабрики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Фабрики'},opts,this); }
				}
			},

			{
				name: "Статус_код",index:"Статус_код",formatter:'select',width:160,stype:'select',inlinedefaultvalue:'5',inlinedisabled:true,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Заказы_Статус']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_Статус'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Заказы_Статус'},opts,this); }
				},
				cellattr:function(rowId,val,rawObject,cm,rdata)
				{
					if(parseInt(rawObject['Блокировка']) === 1)
						return 'class="not-editable-cell" style="color:#545454;background-color:#EBEBE4;"';
				}
			},

			{
				name: "Номер_заказа",index:"Номер_заказа",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Дата_получания_заявки",index:"Дата_получания_заявки",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Дата_оплаты_клиентом",index:"Дата_оплаты_клиентом",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Дата_оплаты_фабрика",index:"Дата_оплаты_фабрика",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Рейс",index:"Рейс",width:100
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter,
				cellattr:function(rowId,val,rawObject,cm,rdata)
				{
					if(parseInt(rawObject['Блокировка']) === 1)
						return 'class="not-editable-cell" style="color:#545454;background-color:#EBEBE4;"';
				}
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter,
				cellattr:function(rowId,val,rawObject,cm,rdata)
				{
					if(parseInt(rawObject['Блокировка']) === 1)
						return 'class="not-editable-cell" style="color:#545454;background-color:#EBEBE4;"';
				}

			},

			{
				name: "Удалено",index:"Удалено",width:100,hidden:true,formatter: checkBox,unformat:checkBoxUn,formatoptions: { disabled: false},stype:'select',edittype:'checkbox',
				searchoptions:{width:100,value: ":;1:Да;0:Нет",dataInit:dataSelect2},
				editoptions:{value:"1:0"}
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Дельта_Деньги",index:"Дельта_Деньги",width:100,formatter:floatFormatter,editable:false
			},

			{
				name:"print_tlc_doc",index:"print_tlc_doc",width:110,editable:false,formatter:'button',
				formatoptions:{
					value:'Импорт в Excel',
					onButtonClick:function()
					{
						console.log(arguments)
					}
				}
			},

			{
				name: "Блокировка",index:"Блокировка",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Дата_согласование",index:"Дата_согласование",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,
				formatter:'date',formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'}
			},

			{
				name: "Сумма_согласование",index:"Сумма_согласование",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,formatter:floatFormatterNulls
			},

			{
				name: "Сумма_транспорт",index:"Сумма_транспорт",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true,formatter:floatFormatterNulls
			}
		],
		options:{
			cellEdit:true,
			shrinkToFit:true
		}
	})
})
</script>
