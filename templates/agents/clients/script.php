<script type="text/javascript">
	$(function() {
		new jqGrid$({
			main:true,
			name:'clients',
			table:'Форма_Клиенты',
			tableQuery:'Контрагенты',
			title:'Клиенты',
			id:'Код',
			ref_db:'tig50',
			tableSort:'ASC',
			filterToolbar:true,
			beforeSubmitCell:true,
			formpos:false,
			delOpts:false,
			navGrid:true,
			navGridOptions:{search:false,add:false,edit:false},
			cn:[
				"Цвет","Клиент","Город","ПМ","Дата создания","Последний контакт","Последняя заявка"
			],
			cm:[
				{
					name: "Цвет",index: "Цвет",width:50,formatter:'integer',addformeditable:false,
					editoptions:{
						dataInit:function(elem,opts) {
							new jqGrid_aw_combobox$(elem,{},opts,this,{
								list:[['Синий',1],['Зеленый',2],['Желтый',3],['Красный',4]]
							});
						}
					},
					cellattr:function(rowId, val, rawObject, cm , rdata )
					{
						switch (val)
						{
							case '1' :
								return "style='background-color:#104e8b;color:#104e8b' title='Синий'";
								break;
							case '2' :
								return "style='background-color:#228b22;color:#228b22' title='Зеленый'";
								break;
							case '3' :
								return "style='background-color:#FFFF66;color:#FFFF66' title='Желтый'";
								break;
							case '4' :
								return "style='background-color:#FF3232;color:#FF3232' title='Красный'";
								break;
							default : return ""; break;
						}
					}
				},
				{
					name: "Код",index:"Код",width:50,editable:false,stype:'select',formatter:'select',
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Клиенты']) ?>
					},
					searchoptions:{
						value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Клиенты'})}
					},
					editoptions:{
						dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Клиенты'},opts,this); }
					}
				},

				{
					name: "Город",index:"Город",width:250,align:"center", cellattr:textAreaCellAttr,edittype:'textarea'
				},

				{
					name: "ПМ_Код",index:"ПМ_Код",width:100,stype:'select',formatter:'select',editable:false,
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_ПМ']) ?>
					},
					searchoptions:{
						value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_ПМ'})}
					},
					editoptions:{
						dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_ПМ'},opts,this); }
					}
				},

				{
					name: "Дата_Создания",index:"Дата_Создания",width:120,formatter:'date',
					searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
					editoptions: {maxlengh: 10,dataInit: elemWd}
				},

				{
					name: "Дата_Контакта",index:"Дата_Контакта",width:120,formatter:'date',
					searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
					editoptions: {maxlengh: 10,dataInit: elemWd}
				},

				{
					name: "Дата_Заявки",index:"Дата_Заявки",width:120,formatter:'date',
					searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
					editoptions: {maxlengh: 10,dataInit: elemWd}
				}

			],
			options:{
				cellEdit:false
			}
		})
	})
</script>
