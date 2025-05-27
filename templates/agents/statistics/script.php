<script type="text/javascript">
$(function()
{
	$('.stat-type').on('change',function(event,rowid)
	{
		var client_id;
		var stat_content = $('.statistics-content');
		stat_content.empty();
		if(rowid === 'null' && agents.grid_p.selrow == null)
		{
			if(this.value.length > 0)
				$(this).val([]).trigger('change');
		}
		client_id = rowid ? rowid : agents.grid_p.selrow;
		if(client_id && this.value)
		{
			var iframe = document.createElement('iframe');
			iframe.align = 'top';
			iframe.height = '100%';
			iframe.width = '100%';
			iframe.style.borderWidth = 0;
			iframe.name = 'current-iframe';
			iframe.src = 'plugin?p_name='+this.value+'&reference=agents/statistics&rowid='+client_id;
			stat_content.append(iframe);
		}
	});
	var agents = new jqGrid$({
		defaultPage:false,
		main:true,
		useLs:false,
		name:'agents',
		table:'Форма_Контрагенты',
		tableQuery:'Контрагенты',
		id:'Код',
		title:'Контрагенты',
		tableSort:'ASC',
		formpos:false,
		filterToolbar:true,
		goToBlank:true,
		minimize_right_pager:true,
		beforeSubmitCell:true,
		addBlank:false,
		navGridOptions:{add:false,search:true},
		navGrid:true,
		delOpts:true,
		gridToForm:true,
		permFilter:{
			groupOp:"AND",rules:[
				{field:"is_Client",op:"isNotNull"}
			]
		},
		permFilterButtons:[
			{caption:new Date().getFullYear(),data:{field:"Движение",op:"isNotNull",perm:true}},
			{caption:'Не активны',data:{field:"is_Not_Active",op:"isNotNull",perm:true}}
		],
		cn:[
			"Код","Контрагенты_Код","Название Rus","Название Eng","Страна","Квадрат","Адрес","Контакт","Телефон",
			"Сайт","Email","Сайт Код","Email счета","Часы работы","Примечание контакты","Пользователь","Регион","Город","Типы"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,
				hidden:<?php if($this->User->user_group_name === 'adm'){ ?>false <?php } else { ?>true<?php } ?>,
				editable:false,editrules:{edithidden:false},hidedlg:true,
				searchoptions: {sopt:['eq'],searchhidden: true}
			},

			{
				name: "Контрагенты_Код",index:"Контрагенты_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Название_RU",index:"Название_RU",width:250,resizable:false,editrules:{required: true}
			},
			{
				name: "Название_EN",index:"Название_EN",width:250,resizable:false,editrules:{required: true}
			},

			{
				name: "Страны_Код",index:"Страны_Код",formatter:'select',width:100,stype:'select',hidden:true,searchoptions: {searchhidden: true},
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
				name: "Индекс",index:"Индекс",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Адрес",index:"Адрес",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Контакт",index:"Контакт",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Телефон",index:"Телефон",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Сайт",index:"Сайт",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Email",index:"Email",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Сайт_Код",index:"Сайт_Код",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Email_Счета",index:"Email_Счета",hidden:true,searchoptions: {searchhidden: true},editable:false
			},


			{
				name: "Часы_работы",index:"Часы_работы",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Примечание",index:"Примечание",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Пользователь",index:"Пользователь",hidden:true,searchoptions: {searchhidden: true},editable:false
			},

			{
				name: "Регион",index:"Регион",hidden:true,editable:false
			},

			{
				name: "Город_Код",index:"Город_Код",hidden:true,editable:false
			},

			{
				name: "Типы",index:"Типы",width:150,editable:false,stype:'select',
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({ref_tname:'',tname:'Контрагенты_типы',id:false,sopt:'cn'})}
				}
			}
		],
		options:{
			autowidth: true,
			cellEdit:false,
			shrinkToFit:true,
			viewrecords:false,
			hoverrows:false
		},
		events:{
			onSelectRow:function(rowid,e)
			{
				$('.row-highlight').removeClass('row-highlight');
				$('tr#'+rowid).toggleClass('row-highlight');
				$('.stat-type').trigger('change',this.p.selrow);
			},
			loadComplete:function(data)
			{
				if(typeof data.rows !== typeof undefined && data.rows.length === 1)
					$(this).setSelection(data.rows[0]['Код'],true);
				else if(agents.grid_first_load === false)
					$('.stat-type').trigger('change','null');
			}
		}
	});
})
</script>
