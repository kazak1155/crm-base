<script type="text/javascript">
$(function()
{
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'order_journal',
		table:'Журнал',
		id:'Код',
		orderName:'Дата',
		tableSort:'DESC',
		hideBottomNav:true,
		useLs:false,
		navGrid:false,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Запись_Код'
		},
		cn:[
			"Код","Дата","Пользователь","Текст","Код записи","Серьезность"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'ISO8601Long',newformat:'d.m.Y H:i:s'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Пользователи_Код",index:"Пользователи_Код",width:100,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Пользователи']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Пользователи'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Пользователи'},opts,this); }
				}
			},

			{
				name: "Текст",index:"Текст",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			},

			{
				name: "Запись_Код",index:"Запись_Код",width:100
			},

			{
				name: "Серьезность",index:"Серьезность",width:100
			}
		],
		options:{
			shrinkToFit:true
		}
	});
})
</script>
