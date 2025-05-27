<script type="text/javascript">
$(function() {
	new jqGrid$({
		subgrid:true,
		pseudo_subrgid:true,
		main:false,
		name:'order_journal',
		table:'Заказы_Статус_Журнал',
		id:'Код',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		navGrid:false,
		subgridpost:{
			mainid:<?php echo $this->req_rowid ?>,
			mainidName:'Заказы_Код'
		},
		cn:[
			"Код","Заказы_Код","Статус","Дата изменения","Пользователь"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Статус_Код",index:"Статус_Код",width:160,formatter:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Заказы_Статус']) ?>
				}
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Full_name",index:"Full_name",width:100
			}
		],
		options:{
			autowidth: true,
			shrinkToFit:true
		}
	})
})
</script>
