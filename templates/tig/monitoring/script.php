<script type="text/javascript">
$(function() {
	var noborder = function()
	{
		return "style='border-right:0px;";
	}
	var pg_formatter = function(cellvalue, options, rowObject)
	{
		var status = parseInt(rowObject['Статус_Код']);
		var green = '<div class="progressbar progressbar-green"><div class="progressbar-inner"></div></div>';
		var red = '<div class="progressbar progressbar-red"><div class="progressbar-inner"></div></div>';
		var blue = '<div class="progressbar progressbar-blue"><div class="progressbar-inner"></div></div>';
		switch(options.colModel['name']){
			case 'Заявка':
				if(status > 0)
					return  '<div class="progressbar_start progressbar-green"><div class="progressbar-inner"></div></div>';
				else
					return '<div class="progressbar_start progressbar-red"><div class="progressbar-inner"></div></div>';
			break;
			case 'Старт':
				if(status == 2)
					return red;
				else if( status > 2)
					return green;
				else
					return blue;
			break;
			case 'Фабрика':
				if(status == 3)
					return red;
				else if(status > 3)
					return green;
				else
					return blue;
			break;
			case 'Склад':
				if(status == 4)
					return red;
				else if(status > 4)
					return green;
				else
					return blue;
			break;
			case 'EX':
				if(status == 5)
					return red;
				else if(status > 5)
					return green;
				else
					return blue;
			break;
			case 'Транзит':
				if(status == 20)
					return red;
				else if(status > 5)
					return green;
				else
					return blue;
				break;
			case 'IMP':
				if(status == 6)
					return red;
				else if((status > 6) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'ТСД':
				if(status == 7)
					return red;
				else if((status > 7) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'TIR':
				if(status == 8)
					return red;
				else if((status > 8) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'Граница':
				if(status == 9)
					return red;
				else if((status > 9) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'СВХ':
				if(status == 10)
					return red;
				else if((status > 10) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'Выпуск':
				if(status == 11)
					return red;
				else if((status > 11) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'РФ':
				if(status == 12)
					return red;
				else if((status > 12) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'Разгрузка':
				if(status == 13)
					return red;
				else if((status > 13) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'OUT':
				if(status == 14)
					return red;
				else if((status > 14) && (status !== 20))
					return green;
				else
					return blue;
			break;
			case 'Резерв':
				if(status == 255)
					return '<div class="progressbar_end progressbar-red"><div class="progressbar-inner"></div></div>';
				else if(status > 250)
					return '<div class="progressbar_end progressbar-green"><div class="progressbar-inner"></div></div>';
				else
					return '<div class="progressbar_end progressbar-blue"><div class="progressbar-inner"></div></div>';
				break;
		}
	}
	new jqGrid$({
		main:true,
		name:'monitoring',
		table:'Форма_МониторингРейсов_CRM',
		id:'НомерРейса',
		orderName:'НомерРейса',
		tableSort:'DESC',
		useLs:false,
		navGrid:true,
		filterToolbar:true,
		permFilter:{
			groupOp:"OR",rules:[
				{field:"Статус_Код",op:"isN",data:"is Null"},
				{field:"Статус_Код",op:"lt",data:"100"}
			]
		},
		permFilterButtons:[
			{caption:'Архив',data:{field:"Статус_Код",op:"ge",data:"0",perm:true}}
		],
		cn:[
			"Номер рейса","НомерМашины","Счет","Заявка","Старт","Фабрика","Склад","EX","Транзит","IMP","ТСД","TIR","Граница","СВХ","Выпуск","РФ","Разгрузка","OUT","Резерв","Статус","Дата события"
		],
		cm:[
			{
				name: "НомерРейса",index:"НомерРейса",width:150,stype:'select',
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Рейсы',flds:['Название_'],id_only:true,order:1,sfld:'Название_',refid:'Название_'})}
				},
				cellattr:function(rowId, val, rawObject, cm , rdata )
				{
					if(rawObject['Статус_Код'] == null)
						return;
					else if (rawObject['Статус_Код'] < 4)
						return "style='background-color:#ffa500'";
					else if (rawObject['Статус_Код'] < 8)
						return "style='background-color:#ff0000;color:#FFF'";
					else if (rawObject['Статус_Код'] < 11)
						return "style='background-color:#10498D;color:#FFF'";
					else if (rawObject['Статус_Код'] < 256)
						return "style='background-color:#228b22;color:#FFF'";
				}
			},
			{
				name: "НомерМашины",index:"НомерМашины",width:150
			},
			{
				name: "ФлагСчет",index:"ФлагСчет",width:50,search:false,
				formatter:function(cellvalue, options, rowObject){
					if(cellvalue == 0)
						return '<i style="font-size:2em;color:#ff0000" class="fa fa-times"></i>';
					else if(cellvalue > 0)
						return '<i style="font-size:2em;;color:#228b22" class="fa fa-check"></i>';
				}
			},

			{
				name: "Заявка",index:"Заявка",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Старт",index:"Старт",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Фабрика",index:"Фабрика",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Склад",index:"Склад",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "EX",index:"EX",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Транзит",index:"Транзит",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "IMP",index:"IMP",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "ТСД",index:"ТСД",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "TIR",index:"TIR",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Граница",index:"Граница",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "СВХ",index:"СВХ",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Выпуск",index:"Выпуск",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "РФ",index:"РФ",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Разгрузка",index:"Разгрузка",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "OUT",index:"OUT",width:50,search:false,formatter:pg_formatter,cellattr:noborder
			},

			{
				name: "Резерв",index:"Резерв",width:50,search:false,formatter:pg_formatter
			},

			{
				name: "Статус",index:"Статус",width:250,search:false
			},

			{
				name: "Маршрут_Дата",index:"Маршрут_Дата",width:100,formatter:'date',editable:false,search:false,
				searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'}
			}
		],
		options:{
			autowidth:true,
			shrinkToFit:true,
			hoverrows:false
		}
	})
})
</script>
