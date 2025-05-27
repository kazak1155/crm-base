<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		db:'tig50_view',
		name:'hauls_details',
		table:'Форма_СоставРейсов',
		title:'Состав рейсов',
		id:'Рейсы_Код',
		tableSort:'DESC',
		useLs:false,
		filterToolbar:true,
		navGrid:true,
		footer:[
			{col:'Кол_предм',calc:'sum'},
			{col:'Вес',calc:'sum'},
			{col:'Кол_мест',calc:'sum'},
			{col:'Объем',calc:'sum'}
		],
		cn:[
			"Рейс","Клиент","Фабрика","Торговая марка","Вид груза","Категория","Материал","Артикул","Страна","Код ТНВЭД","Код ТНВЭД Состав","Таможенное описание","Требуется сертификат","# предм","Вес","# мест","Объем","Примечание","Вес таможня",
			"Д_Заявитель","Д_Номер","Д_От","Д_До"
		],
		cm:[
			{
				name: "Рейсы_Код",index:"Рейсы_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Рейсы']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Рейсы'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Рейсы'},opts,this); }
				}
			},

			{
				name: "Клиенты_Код",index:"Клиенты_Код",width:200,formatter:'select',stype:'select',
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
						return "style='background-color:#4ca64c;font-weight:bold'";
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
				name: "Торговая_марка",index:"Торговая_марка",width:100
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
				name: "Категории_Код",index:"Категории_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Категории_груза']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Категории_груза'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Категории_груза'},opts,this); }
				}
			},
			{
				name: "Материалы_Код",index:"Материалы_Код",width:100,formatter:'select',stype:'select',
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
				name: "Артикул",index:"Артикул",width:100
			},

			{
				name: "Страны_Код",index:"Страны_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Страны']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Страны'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Страны'},opts,this); }
				}
			},

			{
				name: "Код_ТНВЭД",index:"Код_ТНВЭД",width:100
			},

			{
				name: "Код_ТН_ВЭД",index:"Код_ТН_ВЭД",width:100
			},

			{
				name: "Таможенное_описание",index:"Таможенное_описание",width:150
			},

			{
				name: "Требуется_сертификат",index:"Требуется_сертификат",width:50,gtype:'checkbox',stype:'select',addformeditable:false,
				formatoptions: {
					disabled: true
				},
				searchoptions:{
					width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
				}
			},

			{
				name: "Кол_предм",index:"Кол_предм",width:50
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			},

			{
				name: "Кол_мест",index:"Кол_мест",width:50
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter
			},

			{
				name: "Примечание",index:"Примечание",width:400,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Вес_Таможня",index:"Вес_Таможня",width:100,formatter:floatFormatter
			},
			{
				name: "Д_Заявитель",index:"Д_Заявитель",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},
			{
				name: "Д_Номер",index:"Д_Номер",width:150,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},
			{
				name: "Д_От",index:"Д_От",width:120,formatter:'date',editable:false,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},
			{
				name: "Д_До",index:"Д_До",width:120,formatter:'date',editable:false,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			}
		],
		options:{
			shrinkToFit:true
		}
	})
})
</script>
