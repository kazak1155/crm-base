<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'haul_journal',
		table:'V_Маршрут_Аудит',
		id:'Запись_Код',
		orderName:'Дата',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		navGrid:false,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Запись_Код'
		},
		cn:[
			"Запись_Код","Дата","Пользователь","Текст"
		],
		cm:[
			{
				name: "Запись_Код",index:"Запись_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{
					sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp
				},
				formatoptions: {
					srcformat:'Y-m-d',newformat:'d.m.Y'
				}
			},

			{
				name: "Пользователь",index:"Пользователь",width:100
			},

			{
				name: "Текст",index:"Текст",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			shrinkToFit:true
		}
	})
})
</script>
