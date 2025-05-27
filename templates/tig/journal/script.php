<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'tig_journal',
		table:'Форма_Журнал',
		id:'Код',
		orderName:'Дата',
		tableSort:'DESC',
		navGrid:true,
		filterToolbar:true,
		delOpts:true,
		useLs:false,
		cn:[
			'Код','Тип DML','Таблица','Код таблицы','Текст','Дата','Пользователь'
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Тип_DML",index:"Тип_DML",width:150
			},

			{
				name: "Таблица",index:"Таблица",width:150
			},

			{
				name: "Код_Таблица",index:"Код_Таблица",width:100
			},

			{
				name: "Текст",index:"Текст",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d H:i:s',newformat:'d.m.Y H:i:s'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Пользователь",index:"Пользователь",width:150,formatter:'select',stype:'select',editable:false,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Пользователи_CRM']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Пользователи_CRM'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Пользователи_CRM'},opts,this); }
				}
			}

		],
		options:{
			shrinkToFit:true,
			cellEdit:false
		}
	})
})
</script>
