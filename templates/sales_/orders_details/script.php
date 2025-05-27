<?php
if(!isset($this))
{
	header("Status: 301 Moved Permanently");
	header("Location:http://".$_SERVER['HTTP_HOST']."/php/tmpl/iframe_tmpl?forced=true&reference=".$_SERVER['REQUEST_URI']);
	exit;
}
$this->con_database('sales');
?>
<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'orders',
		table:'Сводная_Заказы_Состав',
		title:'Сводная состав',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		navGrid:true,
		navGridOptions:{search:true},
		cn:[
			"Код",
			"№ зак.","Клиент","Телефон","Вид груза","Фабрика","Статус",
			"Нетто фабрика","Нетто логистика","Объем","Вес","Кол-во","Ст.фабрика","Ст.лог","Ст.итог","Цена п/счету","Пользователь"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:false,editable:false,editrules:{edithidden:false},hidedlg:true,hidden:true
			},

			{
				name: "Заказы_Код",index:"Заказы_Код",width:40
			},

			{
				name: "Клиент",index:"Клиент",width:300
			},

			{
				name: "Телефон",index:"Телефон",width:150
			},

			{
				name: "Вид_Груза_Код",index:"Вид_Груза_Код",width:150,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'Б_Виды_Груза']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Виды_Груза'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Виды_Груза',getNull:false},opts,this); }
				}
			},

			{
				name: "Фабрика_Код",index:"Фабрика_Код",formatter:'select',width:150,stype:'select',
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'З_Б_Фабрики']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Фабрики'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Фабрики',getNull:false},opts,this); }
				}
			},

			{
				name: "Статус_Код",index:"Статус_Код",formatter:'select',width:200,stype:'select',
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'З_Б_Заказы_Статусы','order'=>'Код']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'З_Б_Заказы_Статусы',order:'Код'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'З_Б_Заказы_Статусы',getNull:false,order:"Код"},opts,this); }
				}
			},

			{
				name: "Себестоимость_фабрика",index:"Себестоимость_фабрика",width:100
			},

			{
				name: "Себестоимость_логистика",index:"Себестоимость_логистика",width:100
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			},

			{
				name: "Кол_во",index:"Кол_во",width:50,formatter:'integer'
			},

			{
				name: "Стоимость_фабрика",index:"Стоимость_фабрика",width:100
			},

			{
				name: "Стоимость_логистика",index:"Стоимость_логистика",width:100
			},

			{
				name: "Стоимость_Raw",index:"Стоимость_Raw",width:200
			},

			{
				name: "Стоимость_счет",index:"Стоимость_счет",width:100
			},

			{
				name: "Пользователь",index:"Пользователь",formatter:'select',width:100,stype:'select',
				formatoptions:{
					value:<?php echo $this->get_lib(['tname'=>'srv_З_Б_Пользователи']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'srv_З_Б_Пользователи'})}
				},
				editoptions:{
					disabled:true,
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'srv_З_Б_Пользователи'},opts,this); }
				}
			}
		],
		options:{
			shrinkToFit:true
		}
	});
})
</script>
