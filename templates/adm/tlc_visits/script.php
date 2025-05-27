<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'usrs',
		table:'Сайт_Посещения',
		id:'Контрагенты_Код',
		tableSort:'ASC',
		navGrid:true,
		useLs:false,
		filterToolbar:true,
		footer:[
			{col:'kol',calc:'sum'}
		],
		cn:[
			"Клиент","Страница","Месяц","Год","Кол-во посещений"
		],
		cm:[
			{
				name: "Контрагенты_Код",index:"Контрагенты_Код",width:200,formatter:'select',stype:'select',
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
				name: "Страница",index:"Страница",width:250,formatter:'link'
			},

			{
				name: "Месяц",index:"Месяц",width:250
			},

			{
				name: "Год",index:"Год",width:250
			},

			{
				name: "kol",index:"kol",width:100,formatter:'integer'
			}
		],
		options:{
			shrinkToFit:true
		}
	})
})
</script>
