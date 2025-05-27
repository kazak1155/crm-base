<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'docs',
		table:'Разрешительные_Документы',
		tableQuery:'Разрешительные_Документы',
		title:'Разрешительные документы',
		id:'Код',
		ref_db:'tig50',
		tableSort:'ASC',
		filterToolbar:true,
		beforeSubmitCell:true,
		formpos:false,
		delOpts:true,
		navGridOptions:{search:true,add:true,edit:true},
		contextMenuFileTree: true,
		navGrid:true,
		cn:[
			"Код","Номер","Заявитель","Действие_От","Действие_До"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false}, searchoptions: {searchhidden: true}
			},

			{
				name: "Номер",index:"Номер",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Заявитель",index:"Заявитель",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Действие_От",index:"Действие_От",width:120,formatter:'date',
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					var now = new Date();
					var today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).valueOf();
					var c_date = new Date(rawObject['Действие_От']).valueOf();

					if(c_date > today)
						return "style='background-color:#990000;color:white'";
				},
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Действие_До",index:"Действие_До",width:120,formatter:'date',
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					var now = new Date();
					var today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).valueOf();
					var c_date = new Date(rawObject['Действие_До']).valueOf();

					if((c_date < today)&&(c_date!==0))
						return "style='background-color:#990000;color:white'";
				},
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
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
				name:'docs_details',
				table:'Разрешительные_Документы_Применимость',
				id:'Код',
				beforeSubmitCell:true,
				delOpts:true,
				navGrid:true,
				useLs:true,
				goToBlank:true,
				onCellSelect:true,
				filterToolbar:true,
				hideBottomNav:true,
				navGridOptions:{
					add:true
				},
				subgridpost:{
					mainidName:'Документ_Код'
				},
				cn:[
					"Код","Документ","Фабрика","Вид груза"
				],
				cm:[
					{
						name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Документ_Код",index:"Документ_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
					},

					{
						name: "Фабрика_Код",index:"Фабрика_Код",width:200,formatter:'select',stype:'select',
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
						name: "Вид_Груза_Код",index:"Вид_Груза_Код",width:200,formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Виды_груза']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Виды_груза'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Виды_груза'},opts,this); }
						}
					}
				],
				options:{
					shrinkToFit:true,
					autowidth:false,
					width:1500,
					height:'100%',
					cellEdit:true
				}
				}

		]
	})
})
</script>
