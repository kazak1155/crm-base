<script type="text/javascript">
$(function() {
	var haul_id = $('.select2me').val();
	var haul_text = $('option[value="'+ haul_id +'"]','.select2me').text();
	if(haul_id.length > 0)
		populate_journal();
	function populate_journal(){
		$('#journal').val('');
		$.ajaxShort({
			dataType:'JSON',
			data:{
				action:'view',
				responseType:'multiple',
				query:'SELECT Дата,Пользователь,Текст FROM Маршрут_Журнал WHERE Рейсы_Код = ' + haul_id
			},
			success:function(data)
			{
				if(data.length == 0)
					return $('#journal').val('Отсутствуют данные журнала');
				var text = new String(),date;
				for(var i = 0; i < data.length; i++)
				{
					date = getDate(true,false,data[i]['Дата'],true);
					text += date + ' >>> ' + data[i]['Пользователь']+' >>> '+data[i]['Текст'] + '\n';
				}
				$('#journal').val(text);
			}
		});
	}
	$('.select2me').change(function(e)
	{
		$('#journal').val('');
		haul_id = this.value;
		haul_text = $('option[value="'+ haul_id +'"]',this).text();

		if (history.pushState)
		{
			var search = window.location.search;
			if(search.indexOf('&haul') > 0)
				search = search.substring(0,search.indexOf('&haul'));
			var newurl = parent.window.location.protocol + "//" + parent.window.location.host + parent.window.location.pathname + search + '&haul='+haul_id;
			parent.window.history.pushState({path:newurl},'',newurl);
		}

		populate_journal();
		route.renew_external_sub(haul_id);
	});
	var route = new jqGrid$({
		defaultPage:false,
		subgrid:true,
		main:true,
		name:'route',
		table:'Форма_Маршрут',
		id:'Код',
		orderName:'Очередность',
		tableSort:'ASC',
		hideBottomNav:true,
		useLs:false,
		delOpts:true,
		beforeSubmitCell:true,
		navGrid:true,
		navGridOptions:{add:true},
		customButtons:[
			{
				caption:'Экспорт на склад',
				icon:'fa fa-truck',
				click:function(e)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					var grid = this;
					$("#lui_"+route.name+",#load_"+route.name+"").show();
					$.ajaxShort({
						data:{ action:'haul_to_wh', rowid:haul_id,rowid_text:haul_text },
						success:function(data){
							if(data.length > 0)
								$.alert(data);
							$("#lui_"+route.name+",#load_"+route.name+"").hide();
						}
					});
				}
			},
			{
				caption:'Стандартный маршрут',
				icon:'fa fa-cogs',
				click:function(e)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					var t = this
					$.ajaxShort({
						data:{
							action:'edit',
							query:"EXEC sp_route_actions 'default',default,"+ haul_id +",default"
						},
						success:function()
						{
							populate_journal();
							$(t).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			{
				caption:'В архив',
				icon:'fa fa-archive',
				click:function(e)
				{
					var t = this;
					if(!haul_id)
						return $.alert('Рейс не выбран');
					$.confirmHTML({
						width:300,
						html:function()
						{
							var text = 'Дата архивации рейса';
							return $.genHTML({type:'input-top-label',options:{input_value:getDate(true),label_text:text}});
						},
						done_func:function()
						{
							var val = $('input',this).val();
							if(!val)
								val = getDate(true);
							val = val.split('.');
							val = val[2] + val[1] + val[0];
							$.ajaxShort({
								data:{
									action:'edit',
									query:"EXEC sp_route_actions 'archive',default,"+ haul_id +",'"+ val +"'"
								},
								success:function()
								{
									populate_journal();
									$(t).trigger("reloadGrid",{current:true});
								}
							});
						},
						dialog_opts:
						{
							open:function(){
								$('input',this).datepicker();
							},
							close:function(){
								$(this).remove();
							}
						}
					});
				}
			},
			{
				caption:'Заявка на рейс',
				icon:'fa fa-file-pdf-o',
				click:function(e)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					get_file_url({qry:haul_id,reference:this.p.gridProto.location},'Маршрут рейса №'+ haul_text,{prefix:'pdf',folder:'tig_route_haul'});
				}
			},
			{
				caption:'Заявка на рейс DK',
				icon:'fa fa-file-pdf-o',
				click:function(e)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					get_file_url({qry:haul_id,reference:this.p.gridProto.location},'Маршрут рейса №'+ haul_text,{prefix:'pdf',folder:'tig_route_haul_dk'});
				}
			},
			{
				caption:'Заявка на рейс PRGS',
				icon:'fa fa-file-pdf-o',
				click:function(e)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					get_file_url({qry:haul_id,reference:this.p.gridProto.location},'Маршрут рейса '+ haul_text,{prefix:'pdf',folder:'tig_route_haul_prgs'});
				}
			},
			{
				caption:'Заявка на рейс РБ-РФ',
				icon:'fa fa-file-pdf-o',
				click:function(e)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					get_file_url({qry:haul_id,reference:this.p.gridProto.location},'Маршрут рейса '+ haul_text,{prefix:'pdf',folder:'tig_route_haul_rb'});
				}
			}

		],
		contextMenuItems:{
			move_up:{
				name:'Вверх',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-arrow-up';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					var t = this,prio = rowObject['Очередность'];
					$.ajaxShort({
						data:{
							action:'edit',
							query:"EXEC sp_route_actions 'up',"+ rowid +","+ haul_id +","+ prio
						},
						success:function()
						{
							populate_journal();
							$(t).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			move_down:{
				name:'Вниз',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-arrow-down';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					var t = this,prio = rowObject['Очередность'];
					$.ajaxShort({
						data:{
							action:'edit',
							query:"EXEC sp_route_actions 'down',"+ rowid +","+ haul_id +","+ prio
						},
						success:function()
						{
							populate_journal();
							$(t).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			add_fabric:{
				name:'Добавить фабрику',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-industry';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					var t = this,prio = rowObject['Очередность'];
					$.ajaxShort({
						data:{
							action:'edit',
							query:"EXEC sp_route_actions 'add_fabric',"+ rowid +","+ haul_id +","+ prio
						},
						success:function()
						{
							populate_journal();
							$(t).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			add_wh:{
				name:'Добавить склад',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-cubes';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					if(!haul_id)
						return $.alert('Рейс не выбран');
					var t = this,prio = rowObject['Очередность'];
					$.ajaxShort({
						data:{
							action:'edit',
							query:"EXEC sp_route_actions 'add_wh',"+ rowid +","+ haul_id +","+ prio
						},
						success:function()
						{
							populate_journal();
							$(t).trigger("reloadGrid",{current:true});
						}
					});
				}
			},
			journal:{
				name:'Журнал',
				icon:function(opt, $itemElement, itemKey, item)
				{
					return 'context-menu-icon-fa fa-book';
				},
				custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
				{
					gridPseudo.plugin({
						name:'journal',
						dialog_title:'Журнал события: "'+rowObject['Маршрут_События_Код']+'"',
						dialog_width:600,
						dialog_height:450
					});
				}
			}
		},
		subgridpost:{
			mainid:haul_id.length > 0 ? haul_id : '',
			mainidName:'Рейсы_Код'
		},
		cn:[
			"Код","Заказы_Код","Очередность","Событие","Контрагент","Дата начала","Дата конца","Мест","Вес","Объем","Примечание"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Рейсы_Код",index:"Рейсы_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Очередность",index:"Очередность",width:50,hidden:true,editable:true,editrules:{edithidden:true},hidedlg:true,
				editoptions:{
					attr:{disabled:'disabled',style:"background-image:none !important;background-color:rgb(235, 235, 228) !important"},
					defaultValue:function(){
						var last_row_id = $(this).find('tr:eq('+this.p.reccount+')')[0].id;
						var last_row_data = $(this).getRowData(last_row_id);
						return parseInt(last_row_data['Очередность']) + 1;
					}
				}
			},

			{
				name: "Маршрут_События_Код",index:"Маршрут_События_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Маршрут_События']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Маршрут_События'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Маршрут_События'},opts,this); }
				}
			},

			{
				name: "Контрагенты_Код",index:"Контрагенты_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Контрагенты_EN']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Контрагенты_EN'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Контрагенты_EN'},opts,this); }
				}
			},

			{
				name: "Дата_Н",index:"Дата_Н",width:120,formatter:'date',inlinedefaultvalue:getDate(true),
				searchoptions:{
					sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp
				},
				formatoptions: {
					srcformat:'Y-m-d',newformat:'d.m.Y'
				},
				editoptions: {
					defaultValue:getDate(true),maxlengh: 10,dataInit: elemWd
				},
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					var date_start = rawObject['Дата_Н'],date_end = rawObject['Дата_К'];
					if(date_start)
					{
						if(date_start <= date_end)
							return 'style="background-color:#0D640D;color:#FFF"';
						else if(date_start > date_end)
							return 'style="background-color:#b20000;color:#FFF"';
					}
				}
			},

			{
				name: "Дата_К",index:"Дата_К",width:120,formatter:'date',inlinedefaultvalue:getDate(true),
				searchoptions:{
					sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp
				},
				formatoptions: {
					srcformat:'Y-m-d',newformat:'d.m.Y'
				},
				editoptions: {
					defaultValue:getDate(true),maxlengh: 10,dataInit: elemWd
				},
				cellattr:function(rowId, val, rawObject, cm , rdata)
				{
					var date_start = rawObject['Дата_Н'],date_end = rawObject['Дата_К'];
					if(date_start)
					{
						if(date_start <= date_end)
							return 'style="background-color:#0D640D;color:#FFF"';
						else if(date_start > date_end)
							return 'style="background-color:#b20000;color:#FFF"';
					}
				}
			},

			{
				name: "Мест",index:"Мест",width:50
			},

			{
				name: "Вес",index:"Вес",width:50,formatter:floatFormatter
			},

			{
				name: "Объем",index:"Объем",width:50,formatter:floatFormatter
			},

			{
				name: "Примечание",index:"Примечание",width:250,align:"left",
				cellattr:textAreaCellAttr,
				edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
			}
		],
		options:{
			cellEdit:true,
			shrinkToFit:true
		},
		events:{
			afterSaveCell:function(rowid, cellname, value, iRow, iCol)
			{
				var grid = this;
				populate_journal();
				if(cellname == 'Дата_К' && this.p.data[rowid]['Маршрут_События_Код'] == <?php echo $this->Core->server_prm['wh_move_event'] ?> && value)
				{
					$.confirm({
						width:250,
						message:'Передать рейс на склад?',
						done_func:function(){
							$("#lui_"+route.name+",#load_"+route.name+"").show();
							$.ajaxShort({
								data:{ action:'haul_to_wh', rowid:haul_id,rowid_text:haul_text },
								success:function(data){
									if(data.length > 0)
										$.alert(data);
									$("#lui_"+route.name+",#load_"+route.name+"").hide();
									$(grid).editCell(iRow, ++iCol,true);
								}
							});
						},
						cancel_func:function(){
							$(grid).editCell(iRow, ++iCol,true);
							$(this).dialog('close');
						},
						dialog_extend_opts:{
							titlebar:'none'
						}
					});
				}
			},
			rowattr:function(rowData)
			{
				var date_start = rowData['Дата_Н'],date_end = rowData['Дата_К'],ret_obj;
				if(date_end)
					ret_obj = {'style':'background:#7AC67A'};
				else if(date_start && !date_end)
					ret_obj = {'style':'background:#ffb732'};
				else if(!date_start && !date_end)
					ret_obj = {'style':'background:#ff4c4c;color:#FFF'};
				return ret_obj;
			}
		}
	})
})
</script>
