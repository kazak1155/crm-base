
<script type="text/javascript">
	$(function()
	{
		$('.slide-set > legend').on('click',function(e)
		{
			var self_slide = this;
			var parent = this.parentElement;
			var p_classes = parent.className;
			var elements;

			if(p_classes.indexOf('slide-set-single') >= 0)
			{
				elements = document.getElementsByClassName('slide-set');
				for(var i = 0; i < elements.length;i++)
				{
					if(parent != elements[i] && elements[i].className.indexOf('slide-set-hidden') == -1 )
						elements[i].className += " slide-set-hidden";
				}
			}
			else
			{
				elements = document.getElementsByClassName('slide-set-single');
				for(var i = 0; i < elements.length;i++)
				{
					if(parent != elements[i] && elements[i].className.indexOf('slide-set-hidden') == -1 )
						elements[i].className += " slide-set-hidden";
				}
			}

			if(p_classes.indexOf('slide-set-hidden') >= 0)
			{
				parent.className = parent.className.replace(/(?:^|\s)slide-set-hidden(?!\S)/g ,'');
				if($(parent).find('table.gridclass').length > 0)
				{
					var grid_var_name = $(parent).find('table.gridclass').attr('id');
					var grid_proto = eval(grid_var_name);
					grid_proto.init();
				}
			}
			else
			{
				parent.className += " slide-set-hidden";
				if($(parent).find('table.gridclass').length > 0)
				{
					var grid_var_name = $(parent).find('table.gridclass').attr('id');
					var grid_proto = eval(grid_var_name);
					$('div#'+ grid_var_name +'_p').remove();
					grid_proto.prepare = true;
				}
			}
		});
		var agents = new jqGrid$({
			defaultPage:false,
			main:true,
			useLs:true,
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
			contextMenuItems:{
				client_types:{
					name:'Присвоить типы',
					icon: function(opt, $itemElement, itemKey, item)
					{
						return 'context-menu-icon-fa fa-sitemap';
					},
					custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
					{
						gridPseudo.plugin({
							name:'client_types',
							dialog_title:'Типы клиента ' + rowObject['Название_EN'],
							dialog_width:800,
							dialog_height:800,
							dialog_refresh_grid:true
						});
					}
				}
			},
			/*permFilterButtons:[
				{caption:'Клиенты',data:{field:"is_Client",op:"isNotNull",perm:true}},
				{caption:'Активные ('+new Date().getFullYear()+')',data:{field:"Движение",op:"isNotNull",perm:true}}
			],*/
			addFormOptions:{
				afterComplete:function(response, postdata, formid)
				{
					var name_ru = postdata['Название_RU'];
					$('#gs_Название_RU').val(name_ru);
					this.triggerToolbar();
				},
				afterShowForm:function(formid)
				{
					var grid_container = $('#gview_'+this.id);
					formid.closest(".ui-jqdialog").position({of: grid_container,my: "center center",at: "right center"});
				}
			},
			addBlank:false,
			navGridOptions:{add:true,search:true},
			navGrid:true,
			delOpts:true,
			gridToForm:true,
			cn:[
				"Код","Контрагенты_Код","Название Rus","Название Eng","Страна","Квадрат","Адрес","Контакт","Телефон",
				"Сайт","Email","Сайт Код","Email счета","Email Имя","Часы работы","Примечание контакты","Пользователь","Регион","Город","Типы","Архив","Торговая_марка"
			],
			cm:[
				{
					name: "Код",index:"Код",width:50,
					hidden:false,
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
					name: "Страны_Код",index:"Страны_Код",formatter:'select',width:100,stype:'select',
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
					name: "Email_Имя",index:"Email_Имя",hidden:true,searchoptions: {searchhidden: true},editable:false
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
					name: "Город_Код",index:"Город_Код",width:150,formatter:'select',stype:'select',editable:false,hidedlg:true,
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Выбор_Города']) ?>
					},
					searchoptions:{
						value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Выбор_Города'})}
					},
					editoptions:{
						dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Выбор_Города'},opts,this); }
					}

				},

				{
					name: "Типы",index:"Типы",width:150,editable:false,stype:'select',
					searchoptions:{
						value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({ref_tname:'',tname:'Контрагенты_типы',id:false,sopt:'cn'})}
					}
				},

				{
					name: "Архив",index:"Архив",width:30,gtype:'checkbox',stype:'select',addformeditable:false,hidden:true,
					formatoptions: {
						disabled: false
					},
					searchoptions:{
						width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
					}
				},
				{
					name: "Торговая_марка",index:"Торговая_марка",hidden:true,searchoptions: {searchhidden: true},editable:false
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
					var rowdata = $(this).getRowData(rowid);
					var opened_grid = $('table.gridclass').not(this).filter(function(){ return $(this).css('visibility') != 'hidden';});
					var opened_plot = $('div.plot').not(this).filter(function(){ return $(this).css('visibility') != 'hidden';});
					var opened_grid_proto;

					$('.row-highlight').removeClass('row-highlight');
					$('tr#'+rowid).toggleClass('row-highlight');

					$.each($('.gridToForm'),function(i,n){
						$(':input[type="hidden"]',this).not(':checkbox, :submit').val('');
						this.reset();
					});
					$(this).GridToForm(rowid,".gridToForm");
					$('select','.gridToForm').trigger("change",true);

					//agents_acc.subgridpost.mainid = rowid;
					agents_rates.subgridpost.mainid = rowid;
					//agents_comments.subgridpost.mainid = rowid;
					//agents_bills.subgridpost.mainid = rowid;
					agents_phones.subgridpost.mainid = rowid;

					if(opened_grid.length > 0)
					{
						opened_grid_proto = eval(opened_grid.attr('id'));
						opened_grid_proto.renew_external_sub(rowid);
					}
				},
				loadComplete:function(data)
				{
					var opened_grid = $('table.gridclass').not(this).filter(function(){ return $(this).css('visibility') != 'hidden';});
					var opened_grid_proto;
					if(typeof data.rows !== typeof undefined && data.rows.length === 1)
					{
						var m_rowid = data.rows[0]['Код'];
						$('tr#'+m_rowid).toggleClass('row-highlight');
						if(opened_grid.length > 0)
						{
							opened_grid_proto = eval(opened_grid.attr('id'));
							opened_grid_proto.renew_external_sub(m_rowid);
						}
					}
					else if(agents.grid_first_load === false)
					{
						if(opened_grid.length > 0)
						{
							opened_grid_proto = eval(opened_grid.attr('id'));
							opened_grid_proto.renew_external_sub('null');
						}
					}
				}
			}
		});
		var agents_rates = new jqGrid$({
			prepare:false,
			defaultPage:false,
			subgrid:true,
			resize:false,
			main:false,
			name:'agents_rates',
			name_linked_grid:'agents',
			table:'view_Контрагенты_Тарифы',
			tableQuery:'Контрагенты_Тарифы',
			id:'Код',
			hideBottomNav:true,
			useLs:false,
			navGrid:true,
			formpos:false,
			navGridOptions:{add:true},
			removeFromAddForm:['Услуги_Код'],
			beforeSubmitCell:true,
			delOpts:true,
			subgridpost:{
				mainid:'',
				mainidName:'Клиент_Код'
			},
			contextMenuItems: {
				tarif_desc: {
					name: 'Описание тарифа',
					icon: function (opt, $itemElement, itemKey, item) {
						return 'context-menu-icon-fa fa-envelope';
					},
					custom_callback: function (e, rowid, val, cellName, options, rowObject, gridPseudo, rowObjectFormatted) {
						//rowObject['mail_id'] = 38;
						/*
						rowObject['formatted_data'] = new Object();
						rowObject['formatted_data']['Тариф_Код'] = rowObjectFormatted['Тариф_Код'];
						//rowObject['formatted_data']['Фабрики_Код'] = rowObjectFormatted['Фабрики_Код'];


						gridPseudo.plugin({
							name:'rate_desc',
							dialog_title:'Описание тарифа: ',
							dialog_width:800,
							dialog_height:300,
							dialog_refresh_grid:false
						});
						*/
						wwin(e, rowid, val, cellName, options, rowObject, gridPseudo, rowObjectFormatted);
					}
				}
			},
			cn:[
				"Код","Клиент_Код","Услуга","Тариф"
			],
			cm:[
				{
					name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
				},

				{
					name: "Клиент_Код",index:"Клиент_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:false
				},

				{
					name: "Услуги_Код",index:"Услуги_Код",formatter:'select',width:100,stype:'select',
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Услуги_Тарифицированные']) ?>
					},
					editoptions:{
						dataInit:function(elem,opts) {
							var this_combo = new jqGrid_aw_combobox$(elem,{tname:'Услуги_Тарифицированные',getNull:false},opts,this);
							$(this_combo.select_helper).on('change',function(event)
							{
								this_combo.isOpened = false;
								var client_sel = this;
								var rate_sel = document.getElementById('Тариф_Код');
								var rate_sel_prot = rate_sel.jqGrid_aw_combobox_proto;
								rate_sel_prot.clear_results();
								rate_sel_prot.enable();
								rate_sel_prot.aj_data.search = 'Услуги_Код = '+ this.value;
							});
						}
					}
				},

				{
					name: "Тариф_Код",index:"Тариф_Код",formatter:'select',width:100,stype:'select',
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Тарифы2']) ?>
					},
					editoptions:{
						disabled:true,
						dataInit:function(elem,opts) {
							new jqGrid_aw_combobox$(elem,{tname:'Тарифы2',refid:'Код',ref_fld:'Код',ref_tname:'Тарифы2',getNull:false},opts,this);
						}
					}
				}
			],
			options:{
				shrinkToFit:true,
				height:200,
				hoverrows:false
			}
		});


		var agents_phones = new jqGrid$({
			prepare:false,
			defaultPage:false,
			subgrid:true,
			resize:false,
			main:false,
			name:'agents_phones',
			name_linked_grid:'agents',
			table:'Контрагенты_Телефонный_Справочник',
			id:'Код',
			tableSort:'ASC',
			hideBottomNav:true,
			useLs:false,
			navGrid:true,
			navGridOptions:{add:true,edit:true},
			beforeSubmitCell:true,
			subgridpost:{
				mainid:'',
				mainidName:'Контрагенты_Код'
			},
			delOpts:true,
			cn:[
				"Код","Контрагенты_Код","Имя","Фамилия","Номер"
			],
			cm:[
				{
					name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
				},

				{
					name: "Контрагенты_Код",index:"Контрагенты_Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
				},

				{
					name: "FirstName",index:"FirstName",width:150,align:"left",
					cellattr:textAreaCellAttr,
					edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
				},

				{
					name: "LastName",index:"LastName",width:200,align:"left",
					cellattr:textAreaCellAttr,
					edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
				},

				{
					name: "PhoneNumber",index:"PhoneNumber",width:200,align:"left",
					cellattr:textAreaCellAttr,
					edittype:'textarea',editoptions:{rows:'1',dataInit:textAreaHeight}
				}
			],
			options:{
				cellEdit:true,
				shrinkToFit:true,
				height:200
			}
		});

		
		new $ajaxForm({
			eventHandlers:['keyup','change'],
			name:'agentinfo',
			gridLink:agents.name,
			id:'Код',
			table:'Контрагенты',
			floatLabel:true
		});
		new $ajaxForm({
			eventHandlers:['keyup','change'],
			eventExtend:function(e,form,proto)
			{
				if(this.getAttribute('name') != 'Индекс')
					return;
				if(this.getAttribute('name') == 'Индекс') {
					var index_value = this.value,
						agent_country = $('.select2me[name="Страны_Код"]').val(),
						region_sel = $('.region_province'),
						reset = true;
					if (this.value.length == 5 && agent_country == 'ITA') {
						var index, min_index, max_index;
						$.each(region_sel.find('option'), function (i, n) {
							index = this.value.split('|');
							min_index = parseInt(index[0]);
							max_index = parseInt(index[1]);
							if (index_value >= min_index && index_value <= max_index) {
								reset = false;
								this.selected = true;
								region_sel.trigger('change', true);
							}
						});
					}
					if (reset == true)
						region_sel.val('').trigger('change', true);
				}
			},
			name:'agentcontacts',
			gridLink:agents.name,
			id:'Контрагенты_Код',
			table:'Контрагенты_Контакты',
			sub_form:true,
			floatLabel:true,
			add:true
		});
	});
	function wwin(e, rowid, val, cellName, options, rowObject, gridPseudo, rowObjectFormatted){
		var h = 800;
		var w = 1400;
		//alert(JSON.stringify(rowObjectFormatted));
		//alert(rowObjectFormatted['Тариф_Код']);
		var uril = 'templates/agents/info/win_prp01.php?r='+JSON.stringify(rowObjectFormatted)+'&h='+h+'&w='+w;
		$('#modal_container_prp01').empty();
		$('<iframe>', { src:uril , height:h+'px', width:w+'px', id: 'ordDet', frameborder: 2, marginheight:1, marginwidth:1, scrolling: 'no' }).appendTo('#modal_container_prp01');
		$('#modal_winn_prp01').show();
	}
</script>

