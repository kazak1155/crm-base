<script type="text/javascript">
$(function() {
	var report = new jqGrid$({
		main:true,
		name:'orders_report_status',
		table:'Заказы_Отчет_Статус',
		title:'Отчет - Статус',
		id:'Код',
		tableSort:'ASC',
		navGrid:true,
		filterToolbar:true,
		cn:[
			"Код","Клиент","Фабрика","Статус","Номер заказа","Дата","Пользователь"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Клиенты_Код",index:"Клиенты_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Клиенты']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Клиенты'})}
				}
			},

			{
				name: "Фабрики_Код",index:"Фабрики_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Фабрики'})}
				}
			},

			{
				name: "Статус_Код",index:"Статус_Код",formatter:'select',width:160,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Заказы_Статус','order'=>1]) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_Статус'})}
				}
			},

			{
				name: "Номер_заказа",index:"Номер_заказа",formatter:'select',width:160,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'З_Б_Номер_заказа','fields'=>['Название','Название'],'order'=>1]) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Номер_заказа'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Номер_заказа'},opts,this); }
				}
			},

			{
				name: "Дата",index:"Дата",width:120,formatter:'date',
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},
				editoptions: {maxlengh: 10,dataInit: elemWd}
			},

			{
				name: "Пользователь",index:"Пользователь",formatter:'select',width:160,stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Пользователи']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Пользователи'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Пользователи'},opts,this); }
				}
			}
		],
		options:{
			autowidth: true,
			shrinkToFit:true
		}
	})
})
</script>
