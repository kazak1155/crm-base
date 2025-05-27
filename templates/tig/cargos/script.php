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
	var cargos = new jqGrid$({
		defaultPage:false,
		main:true,
		useLs:false,
		name:'cargos',
		table:'Категории_груза',
		tableQuery:'Категории_груза',
		id:'Код',
		title:'Категории грузов',
		tableSort:'ASC',
		formpos:false,
		filterToolbar:true,
		goToBlank:true,
		minimize_right_pager:true,
		beforeSubmitCell:true,



		addBlank:false,
		navGridOptions:{add:true,search:true},
		navGrid:true,
		delOpts:true,
		gridToForm:true,
		cn:[
			"Код","Название"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,
				hidden:false,
				editable:false,editrules:{edithidden:false},hidedlg:true,
				searchoptions: {sopt:['eq'],searchhidden: true}
			},


			{
				name: "Название",index:"Название",width:250,resizable:false,editrules:{required: true}
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

				/*agents_acc.subgridpost.mainid = rowid;
				agents_rates.subgridpost.mainid = rowid;*/
				cargo_types.subgridpost.mainid = rowid;

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
				else if(cargo_types.grid_first_load === false)
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

	var cargo_types = new jqGrid$({

		prepare:true,
		defaultPage:false,
		subgrid:true,
		resize:false,
		main:false,
		name:'cargo_types',
		name_linked_grid:'cargos',
		table:'Виды_Груза',
		tableQuery:'Виды_Груза',
		id:'Код',
		hideBottomNav:true,
		useLs:false,
		navGrid:true,
		formpos:false,
		navGridOptions:{add:true},
		//removeFromAddForm:['Услуги_Код'],
		beforeSubmitCell:true,
		delOpts:true,
		subgridpost:{
			mainid:'',
			mainidName:'Категории_Код'
		},
		cn:[
			"Код","Название","Категории_Код"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:false,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Название",index:"Название",width:50,hidden:false,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Категории_Код",index:"Категории_Код",formatter:'select',width:100,stype:'select',editable:true,
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Категории_груза']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Категории_груза'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Категории_груза'},opts,this); }
				}
			}
		],
		options:{
			shrinkToFit:true,
			height:600,
			hoverrows:false,
			cellEdit: true
		},
		subGridOps:[
			{
				subgrid:true,
				main:false,
				name:'TNVED',
				table:'Коды_ТНВЭД',
				id:'Код',
				beforeSubmitCell:true,
				delOpts:true,
				navGrid:true,
				goToBlank:true,
				onCellSelect:true,
				filterToolbar:false,
				hideBottomNav:true,


				navGridOptions:{
					add:false
				},
				subgridpost:{
					mainidName:'Код_вид_груза'
				},
				cn:[
					"Код_материал","Код_ТНВЭД","Таможенное описание","Требуется сертификат","Примечание"
				],
				cm:[


					{
						name: "Код_материал",index:"Код_материал",formatter:'select',stype:'select',
						formatoptions:{
							value:<?php echo $this->Core->get_lib(['tname'=>'Материалы']) ?>
						},
						searchoptions:{
							value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Материалы'})}
						},
						editoptions:{
							dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Материалы'},opts,this); }
						}
					},

					{
						name: "Код_ТНВЭД",index:"Код_ТНВЭД",width:50
					},

					{
						name: "Таможенное_описание",index:"Таможенное_описание",width:200
					},

					{
						name: "Требуется_сертификат",index:"Требуется_сертификат",width:30,gtype:'checkbox',stype:'select',addformeditable:false,
						formatoptions: {
							disabled: false
						},
						searchoptions:{
							width:30,value: ":;1:Да;0:Нет",dataInit:dataSelect2
						}
					},
					{
						name: "Примечание",index:"Примечание",width:50
					}

				],
				options:{
					shrinkToFit:true,
					autowidth:false,
					width:1000,
					height:'100%',
					cellEdit:true
				}

			}
		]
	});


})

</script>
