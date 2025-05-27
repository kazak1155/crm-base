<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'orders',
		table:'Форма_Заказы_MgSystem',
		tableQuery:'Заказы',
		title:'Заказы',
		id:'Код',
		orderName:'Дата_ПолучЗаявки',
		tableSort:'DESC',
		filterToolbar:true,
		beforeSubmitCell:true,
		contextMenuItems:{
			mysystem_files:{
				name:'Добавить документы',
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
			}
		},
		footer:[
			{col:'Объем',calc:'sum'},
			{col:'Вес',calc:'sum'},
			{col:'ИнСклад_Мест',calc:'sum'}
		],
		permFilter:{
			groupOp:"AND",rules:[
				{field:"Удалено",op:"eq",data:"0"},
				{field:"Статус_Код",op:"lt",data:"100"}
			]
		},
		navGrid:true,
		cn:[
			"Код",
			"Статус",
			"Фабрика",
			"Номер заказа",
			"Д.ПолучЗаявки",
			"Д.Готовности",
			"Д.ИнСклад",
			"Объем",
			"Вес",
			"Мест",
			"На инсклад",
			"Дост.фабрикой",
			"Доки",
			"<i class='fa fa-lg fa-exclamation-triangle'></i>",
			"Перевозчик",
			"Квадрат",
			"Адрес фабрики",
			"Контакты фабрики",
			"Примечание для курьера",
			"Примечание пакинг"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},searchoptions: {searchhidden: true}
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
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Заказы_статус',flds:['Код','Название','Порядок'],order:'Порядок'},opts,this,{maxItems:100}); }
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",width:200,formatter:'select',stype:'select',editable:false,
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
				name: "Номер_Заказа",index:"Номер_Заказа",width:200,align:"left",editable:false,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Дата_ПолучЗаявки",index:"Дата_ПолучЗаявки",width:120,formatter:'date',editable:false,
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
				},
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter,
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if(rawObject['Импортеры_Код'] !== null && rawObject['Пакинг_Мест'] !== null && rawObject['Пакинг_Вес'] !== null)
						return "style='background-color:#32cd32'";
					else if(rawObject['Импортеры_Код'] !== null)
						return "style='background-color:#ffa500'";
					else if (rawObject['Пакинг_Мест'] !== null)
						return "style='background-color:#ffa500'";
					else if (rawObject['Пакинг_Вес'] !== null)
						return "style='background-color:#ffa500'";
				}
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			},

			{
				name: "ИнСклад_Мест",index:"ИнСклад_Мест",width:50
			},

			{
				name: "Заявка_на_инсклад",index:"Заявка_на_инсклад",width:30,gtype:'checkbox',stype:'select',editable:false,
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Доставлен_фабрикой",index:"Доставлен_фабрикой",width:30,gtype:'checkbox',stype:'select',editable:false,
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Документы",index:"Документы",width:30,stype:'select',gtype:'checkbox',
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Битый_груз",index:"Битый_груз",width:30,gtype:'checkbox',stype:'select',
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
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
				name: "Квадрат",index:"Квадрат",width:200,editable:false
			},

			{
				name: "Адрес_Фабрики",index:"Примечание",width:300,align:"left",editable:false,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Контакты_Фабрики",index:"Контакты_Фабрики",width:300,align:"left",editable:false,
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Примечание_курьер",index:"Примечание_курьер",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Примечание_пакинг",index:"Примечание_пакинг",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}

		],
		options:{
			cellEdit:true
		}
	})
})
</script>
