// grid - float formatter
var floatFormatter = function (cellvalue, options, rowObject)
{
	if(options.rowId !== 'blank')
	{
		if(typeof cellvalue === 'string')
		{
			if(cellvalue.indexOf(',') >= 0)
				cellvalue = cellvalue.replace(/[,]+/g, '.');
			var cv = Number(cellvalue)//parseFloat(cellvalue.replace(/[,]+/g, '.')).toFixed(2);
			var res = isNaN(cv) ? '0.000': cv;
			return res;
		}
		else if (typeof cellvalue === 'number')
			return cellvalue.toFixed(2);
		else if (typeof cellvalue === 'object')
			return '0.000';
		else if (typeof cellvalue === 'undefined')
			return '0.000';
	}
	else return '';
},
// grid - cell background-color based of row amount of files loaded
cellAttrFiles = function(rowId, val, rawObject, cm , rdata)
{
	//http://stackoverflow.com/a/13542669
	function shadeColor2(color, percent) {
		var f=parseInt(color.slice(1),16),t=percent<0?0:255,p=percent<0?percent*-1:percent,R=f>>16,G=f>>8&0x00FF,B=f&0x0000FF;
		return "#"+(0x1000000+(Math.round((t-R)*p)+R)*0x10000+(Math.round((t-G)*p)+G)*0x100+(Math.round((t-B)*p)+B)).toString(16).slice(1);
	}
	var files_count = parseInt(rawObject['files_count']);
	var percent = files_count / 10;
	if(percent > 0.5)
		percent = 0.5
	if(files_count > 0)
		return 'background-color:'+shadeColor2('#00bfff',-percent);
}
// grid - float formatter
floatFormatterNulls = function (cellvalue, options, rowObject)
{
	if(options.rowId !== 'blank')
	{
		if(typeof cellvalue === 'string')
		{
			var cv = parseFloat(cellvalue.replace(/[,]+/g, '.')).toFixed(2),
			res = isNaN(cv) ? '&nbsp': cv
			return cv;
		}
		else if (typeof cellvalue === 'number')
			return cellvalue.toFixed(2);
		else if (typeof cellvalue === 'object')
			return '&nbsp';
		else if (typeof cellvalue === 'undefined')
			return '&nbsp';
	}
	else return '';
},
// grid - textarea nice formatting
textAreaHeight = function(elem)
{
	var h = elem.scrollHeight - 4; // calibrating xD
	if($(elem).parents('tr')[0].id === 'blank') // if in new row
	{
		$(elem).css({'height':'18px','overflow-y':'hidden','vertical-align':'middle'});
	}
	else
	{
		if (h !== -4)
			$(elem).css({'height':'auto','overflow-y':'hidden','vertical-align':'middle'}).height(h);
		else
			$(elem).css({'height':'auto','overflow-y':'hidden','vertical-align':'middle'});
	}
},
textAreaCellAttr = function(rowId, val, rawObject, cm , rdata)
{
	return "style='line-height:1.5em;white-space:pre-line;word-wrap:break-word;'"
},
// grid - column for inlineEdit
inlineRowEdit = function(cellvalue, options, rowObject)
{
	if($.isEmptyObject(rowObject))
		return '<span title="Новая запись" class="ui-icon ui-icon-gear" name="add"></span>';
	else
		return '<span title="Удалить запись" class="ui-icon ui-icon-trash" name="del"></i></span>';
},
// grid - checkBox formatting
checkBox = function (cellvalue, options, rowObject,action)
{
	var disabled,style = 'width:100%;';
	options.colModel.editable ?  disabled = '' : disabled = 'disabled="disabled"'
	if(typeof action === typeof undefined && options.rowId !== 'blank')
		cellvalue = '0';

	if(typeof cellvalue !== typeof undefined && options.rowId !== 'blank')
	{
		if(options.colModel.editable == false)
			style += 'cursor:no-drop;';
		else
			style += 'cursor:cell';
		if(cellvalue == null)
			cellvalue = '0';
		var checked = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? ' checked="checked"': '';
		var inputControl = '<input '+disabled+' id="'+ options.rowId +'_'+ options.colModel.name +'_t" class="view" name="' +options.colModel.name +'" style="'+ style +'" type="checkbox" ' + checked + ' value="' + cellvalue + '" onclick="MakeCellEditable.call(this,&apos;'+ options.rowId + '&apos;,&apos;'+options.colModel.name+'&apos;,&apos;'+'#'+this.id+'&apos;)" />'
		return inputControl;
	}
	else
		return '';
},
// grid - checkBox unformatting
checkBoxUn = function (cellvalue, options, cell)
{
	if(typeof cellvalue !== typeof undefined)
		return $('input',cell).attr("checked") ? "1" : "0";
	else
		return '';
},
// grid - autocomplete-combobox
dataInitAcComboBox = function(elem)
{
	$(elem).combobox({grid:this});
},
dataSelect2 = function(elem,options,init_opts)
{
	var data = $(elem).data('search');
	var $t = this;
	var $t_hDiv,postData,elem_options;
	var old = typeof data === typeof undefined ? true:false;
	var wd = null,edge = 70;
	var init_obj;
	var in_search_form = false;
	if(typeof options !== typeof undefined)
		wd = options.hasOwnProperty('width') ? options.width : '100%';
	else
		wd = $(elem).width();
	if(!old)
	{
		// defaults
		data.flds = data.hasOwnProperty('flds') ? data.flds : ['Код','Название'];
		data.sfld =	data.hasOwnProperty('sfld') ? data.sfld : data.flds[1];
		data.order = data.hasOwnProperty('order') ? data.order : '2';
		data.id = data.hasOwnProperty('id') ? data.id : true;
		data.id_only = data.hasOwnProperty('id_only') ? data.id : false;
	}
	// if called within grid
	if(this.grid)
	{
		// dirty overflow fix
		if(this.p.shrinkToFit == false && wd == '100%')
			wd = $(this).getColProp(elem.name).width;

		if($(elem).parents('.searchFilter').length > 0)
			in_search_form = true;

		$t_hDiv = this.grid.hDiv;
		postData = $($t).getGridParam('postData');
		elem_options = $($t).getColProp(elem.name);

		// if select got only empty option
		if(options.value.length <= 1)
		{
			// if old init
			if(old)
			{
				data =
				{
					tname : $(elem).attr('tname'),
					flds : ($(elem).attr('flds')).split(','),
					sfld : $(elem).attr('sfld'),
					order : $(elem).attr('order'),
					id : $(elem).attr('idz')
				}
			}
			// defaults
			data.refid = data.hasOwnProperty('refid') ? data.refid : 'Код';
			data.ref_tname = data.hasOwnProperty('ref_tname') ? data.ref_tname : postData.tname;
			data.ref_fld =	data.hasOwnProperty('ref_fld') ? data.ref_fld : elem_options.name;
			// search form hot fix
			if(in_search_form === true)
				data.ref_fld = $(elem).parents('table').find('.columns').find('select').val();
			if(old)
				$(elem).data('search',data);

			$(this).setColProp(elem.name, {
				searchoptions : {
					sopt : data.hasOwnProperty('sopt') ?  [data.sopt] : ['eq']
				}
			});
			init_obj = {
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				placeholder:wd < edge ? '' : app.select2placeHolder,
				language:app.select2langVal,
				ajax:{
					beforeSend:function(request)
					{
						$('.select2-results__options li').not(':first').remove()
					},
					url: REQUEST_URL,
					type: 'POST',
					dataType: 'json',
					delay: 150,
					data:function(params){
						return {
							oper:'view_selects',
							search: params.term,
							info : JSON.stringify(data)
						}
					},
					processResults: function (data, page) {
						return {
							results: data.results
						};
					},
					cache: true
				}
			};
			if(wd <= 100)
				$.extend(init_obj, {dropdownCss:{'min-width':'150px'}});

			if(typeof init_opts !== typeof undefined)
				$.extend(init_obj, init_opts);

			$(elem)
				.select2(init_obj)
				.on('select2:selecting',function(e)
				{
					var container = $(this).data('select2')['$container'];
					if(wd <= 100)
					{
						$('.select2-selection__rendered',container).css('width',(wd - 20)+'px'); // remove right padding, which is 20px
						$('.select2-selection__arrow',container).css('display','none');
					}

				})
				.on('select2:select',function(e)
				{
					var container = $(this).data('select2')['$container'];
					if($('.select2-selection__rendered',container).width() < 20)
					{
						$('.select2-selection__rendered',container).contents().filter(function()
						{
							return this.nodeType===3;
						}).remove();
					}
				})
				.on('select2:unselecting',function(e)
				{
					$('.select2-selection__arrow',$(this).data('select2')['$container']).css('display','block');
					$(this).val([]).trigger('change');
					e.preventDefault();
				})
				.on('select2:close',function(e)
				{
					// remove prev results
					$(this).data('select2').results.$results.empty();
				})
		}
		else
		{
			init_obj = {
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				language:app.select2langVal,
				placeholder:wd < edge ? '' : app.select2placeHolder,
				dropdownAutoWidth:true
			};
			if(typeof init_opts !== typeof undefined)
				$.extend(init_obj, init_opts);
			$(elem)
				.select2(init_obj)
				.on('select2:selecting',function(e)
				{
					var container = $(this).data('select2')['$container'];
					if(wd <= 100)
					{
						$('.select2-selection__rendered',container).css('width',(wd - 20)+'px'); // remove right padding, which is 20px
						$('.select2-selection__arrow',container).css('display','none');
					}

				})
				.on('select2:select',function(e)
				{
					var container = $(this).data('select2')['$container'];
					if($('.select2-selection__rendered',container).width() < 20)
					{
						$('.select2-selection__rendered',container).contents().filter(function()
						{
							return this.nodeType===3;
						}).remove();
					}
				})
				.on('select2:unselecting',function(e)
				{
					$('.select2-selection__arrow',$(this).data('select2')['$container']).css('display','block');
					$(this).val([]).trigger('change');
					e.preventDefault();
				});
		}
	}
	else
	{
		if(typeof data !== typeof undefined)
		{
			init_obj = {
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				placeholder:wd < edge ? '' : app.select2placeHolder,
				language:app.select2langVal,
				ajax:{
					url: REQUEST_URL,
					type: 'POST',
					dataType: 'json',
					delay: 0,
					data:function(params){
						return {
							oper:'view_selects',
							search: params.term,
							info : JSON.stringify(data)
						}
					},
					processResults: function (data, page) {
						return { results: data.results }
					},
					cache: true
				},
				dropdownAutoWidth:true
			};
			if(typeof init_opts !== typeof undefined)
				$.extend(init_obj, init_opts);
			$(elem).select2(init_obj);
		}
		else
		{
			if($(elem).length > 1)
			{
				$.each($(elem), function(i,el)
				{

					wd = $(this).width();
					init_obj = {
						width:wd,
						multiple:false,
						minimumInputLength:0,
						allowClear:true,
						language:app.select2langVal,
						placeholder:wd < edge ? '' : app.select2placeHolder,
						dropdownAutoWidth:true
					};
					if(typeof init_opts !== typeof undefined)
						$.extend(init_obj, init_opts);
					if($(this).data('selectops'))
					{
						var data_opts = $(this).data('selectops');
						$.extend(init_obj, data_opts);
					}
					$(this).select2(init_obj);
				});
			}
			else
			{
				init_obj = {
					width:wd,
					multiple:false,
					minimumInputLength:0,
					allowClear:true,
					language:app.select2langVal,
					placeholder:wd < edge ? '' : app.select2placeHolder,
					dropdownAutoWidth:true
				};
				if($(elem).data('selectops'))
				{
					var data_opts = $(elem).data('selectops');
					$.extend(init_obj, data_opts);
				}
				if(typeof init_opts !== typeof undefined)
					$.extend(init_obj, init_opts);
				$(elem).select2(init_obj);
			}
		}
	}
}
// grid - jquery selectmenu
dataJqSelectMenu = function(elem)
{
	$(elem)[ 0 ].selectedIndex = -1;
	setTimeout(function () {
		var t = ($(elem).parents('th'));
		for(i=0;i < t.length;i++){
			$(t[i]).css({"padding":"0"});
			$(t[i]).children('div').css({"padding":"0"});
		}
		$(elem).selectmenu({
			icons : {button : 'ui-icon-search'},
			search:true,
			searchText:'Поиск'
		}).selectmenu("menuWidget").css({"max-height":"300px"});

		var elTextSpanWidth = $(elem).width() - 36;
		$(elem).siblings('span.ui-selectmenu-button').css({'width':'95%','margin-top':'2px'})
				.children('span.ui-selectmenu-text').css({'padding':'1px 0px 0px 10px','width':elTextSpanWidth+'px'});
	},0);
},
// grid - datepicker formatter in Edit
elemWd = function(elem)
{
	//noinspection JSAnnotator
	$(elem).datepicker({
		showButtonPanel:true,
		showAnim:"",
		minDate: new Date(2012, 01, 01),
		dateFormat: 'dd.mm.yy',
		changeYear: true,
		changeMonth: true,
		showWeek: true,
		onClose:function(){
			$(elem).focus();
			$(elem).addClass('datePicked');
		}
	});
},
// grid - datepicker formatter in Search
cusDp = function (elem)
{
	var inForm = $(elem).parents('.searchFilter').length > 0 ? true : false;
	var span = $('<span>');
	span
		.click(function(e){
			$(elem).select().focus();
		})
		.addClass('fa fa-lg fa-calendar')
		.css({
			'width':'calc(100% - 84% - 6px)',
			'padding':'2px',
			'padding-left':'3px'
		})
	if(inForm == false)
		$(elem).after(span);
	$(elem).css({
		'float':'left',
		'padding':'0px',
		'width':'84%'
	})


	var $this = $(this);
	//noinspection JSAnnotator
	$(elem).datepicker({
		showButtonPanel:true,
		showAnim:"slideDown",
		minDate: new Date(2012, 01, 01),
		dateFormat: 'dd.mm.yy',
		changeYear: true,
		changeMonth: true,
		showWeek: true,
		gotoCurrent:true,
		beforeShow: function(input,inst){
			setTimeout(function () {
				var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
				$("<button>",
				{
					text:"Выбрать интервал дат",
					css:{'width':'calc(100% - 5px)','outline':'none'},
					click: function (e)
					{
						inst.range = true;
						$(this).hide();
						$.alert('Выберите <strong style="color:#FF0000">первую</strong> дату из интервала','',{
							classNames:'dp_alert',
							min_wd:'0px',
							inline_css:{
								'border':'red'
							},
							dialog_opts:{
								height:80,
								buttons:null,
								modal:false,
								position:{ my: "left top", at: "right top", of: $(inst.dpDiv) }
							},
							dialog_extend_opts:{
								titlebar:'none',
								closable:false
							}
						});
					}
				}).appendTo(buttonPane).addClass("ui-datepicker-range ui-state-default ui-priority-primary ui-corner-all");
				inst.rangeButton = $('.ui-datepicker-range');
			},0);
		},
		onClose: function (dateText, inst)
		{
			$('.dp_alert').dialog('destroy');
			var sep = dateText.indexOf(':');
			delete inst.first;
			$(this).data().datepicker.inline = false;
			if (sep !== -1)
				$this.jqGrid("setColProp", elem.name, {
					searchoptions : {
						sopt : ['dateBn']
					}
				});
			else
				$this.jqGrid("setColProp", elem.name, {
					searchoptions : {
						sopt : ['dateEq', 'dateNe', 'dateLe', 'dateGe']
					}
				});

			if ($('#' + elem.id).parent().hasClass('ui-search-input')) {
				if (dateText !== '')
					$this[0].triggerToolbar();
			} else {
				return true
			}
		},
		onSelect: function(selectedDate,inst)
		{
			if(inst.range === true || inst.inline === true)
			{
				if(!inst.first)
				{
					$('.dp_alert').html('Выберите <strong style="color:#FF0000">вторую</strong> дату из интервала');
					inst.inline = true;
					inst.first = selectedDate;
				}
				else
				{
					if(selectedDate > inst.first)
						$(this).val(inst.first+":"+selectedDate);
					else if (selectedDate < inst.first)
						$(this).val(selectedDate+":"+inst.first);
					$('.dp_alert').dialog('destroy');
					inst.inline = false;
					inst.range = false;
				}
			}
		}
	})
},
// grid - forms position
positionCenter = function(form)
{
	if(form[0].id.indexOf('DelTbl') >= 0)
		$('#delmod'+this.id).focus();

	var grid_container = $('#gview_'+this.id);
	var position = {
		my: "center center",
		at: "center center"
	};
	form.closest(".ui-jqdialog").effect("highlight");

	if(!this.p.hasOwnProperty('gridProto') || !this.p.gridProto.hasOwnProperty('parent_grid'))
		position.of = grid_container;
	else if(this.p.gridProto.parent_grid)
		position.of = $(this.p.gridProto.parent_grid).closest('div[id^="gview_"]')

	form.closest(".ui-jqdialog").position(position);
}
