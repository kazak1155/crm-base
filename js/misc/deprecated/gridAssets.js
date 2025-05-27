// create grid
function createGrid(grid){
	if($('#'+grid.name).length === 0)
		return;
	
	var location = (window.location.href).match('\/([^.][A-Za-z0-9 _]+)\/');
	
	var $t = grid.name,
	table = grid.table,
	tableId = grid.id,
	cn = grid.cn,
	cm = grid.cm,
	lsName = location[1]+'_'+$t+'.columnState',
	gridLs = restoreColumnState(cm,lsName),orderName;
	
	if(typeof grid.orderName !== typeof undefined)
		orderName = grid.orderName;
	else
		orderName = tableId;
	
	var useLs = (typeof grid.useLs === typeof undefined ) ? true : grid.useLs;	
	var navGridOptions = {
		refreshstate:'current',
		refreshcurrent:true
	};
	
	if(typeof grid.navGridOptions !== typeof undefined)
		$.extend(true,navGridOptions,grid.navGridOptions);
		
	$('<div id="'+$t+'_p"></div>').insertAfter('#'+$t);
	
	resetDefaults();
	
	if(grid.hideBottomNav === true)
	{
		grid.options.viewrecords = false;
		grid.options.rowList = '';
		grid.options.pgbuttons = false;
		grid.options.pgtext = null;
	}
	
	$.extend($.jgrid.defaults,grid.options);
	$.extend($.jgrid.defaults,grid.events);
	
	
	for(var i = 0;i < cm.length;i++)
	{
		if(cm[i].stype === 'select')
			cm[i].searchoptions.sopt = ['eq'];
	}

	var p_data;
	
	grid.subgrid ? p_data = {'tname':table,'tid':tableId,'mainId':grid.subgridpost.mainid,'mainIdname':grid.subgridpost.mainidName}: p_data = {'tname':table,'tid':tableId};
	if(typeof grid.permFilter !== typeof undefined)
	{
		for(var i = 0;i < grid.permFilter.rules.length ; i++)
		{
			grid.permFilter.rules[i].perm = true;
		}
		p_data['perm_filters'] = JSON.stringify(grid.permFilter);
	}
		
		
	$('#'+$t).jqGrid("initFontAwesome").jqGrid({
		colNames: cn,
		colModel: cm,
		
		pager:'#'+$t+'_p',
		
		postData: p_data,
		
		page: useLs ? gridLocalStorage(cm,lsName) ? gridLs.page : 1 : 1,
		sortname: useLs ? gridLocalStorage(cm,lsName) ? gridLs.sortname : orderName : orderName,
		sortorder: useLs ? gridLocalStorage(cm,lsName) ? gridLs.sortorder : grid.tableSort : grid.tableSort,
		
		
		beforeProcessing: function(data, status, xhr)
		{
			if(grid.events.hasOwnProperty('beforeProcessing'))
			{
				grid.events.beforeProcessing.call(this,data, status, xhr);
				useLs ? columnState.apply(this,[data,cm,lsName]) : null;
				filterState.apply(this,[data, status, xhr]);
				dep_selects.call(this);
			} 
			else 
			{
				useLs ? columnState.apply(this,[data,cm,lsName]) : null;
				filterState.apply(this,[data, status, xhr]);
				dep_selects.call(this);
			}
		},
		resizeStop: function(newwidth,index)
		{
			if(grid.events.hasOwnProperty('resizeStop'))
			{
				grid.events.resizeStop.call(this,newwidth,index);
			} 
			else 
			{
				useLs ? columnSize.apply(this,[newwidth,index,lsName]) : null ; 
			}
		},
		loadComplete:function(data)
		{
			function lc_func(data)
			{
				if(grid.events.hasOwnProperty('onCellSelect') !== false)
					$(this).jqGrid('addRowData', 'blank', {});
				
				if(grid.gridToForm === true)
				{
					if(typeof data.rows !== typeof undefined && data.rows.length === 1) 
					{
						var m_rowid = data.rows[0]['Код'];
						$('.submitForm').trigger('reset');
						$(this).jqGrid('GridToForm',m_rowid,".submitForm");
						$('select','.submitForm').trigger("change",true);
					}
				}
			}
			if(grid.events.hasOwnProperty('loadComplete'))
			{
				lc_func.call(this,data);
				grid.events.loadComplete.call(this,data);
			} 
			else 
			{
				lc_func.call(this,data);
			}
		}
	})
		
		
	if(grid.filterToolbar === true)
	{
		$('#'+$t).jqGrid('filterToolbar',{stringResult:true,autosearch:true,searchOnEnter:false,defaultSearch: 'bw'});
	}
	
	if(grid.navGrid === true)
	{
		//grid.editFormOptions
		//grid.addFormOptions
		//grid.delFormOptions
		$('#'+$t).navGrid ('#'+$t+'_toppager',navGridOptions,
			grid.hasOwnProperty('editFormOptions') ? grid.editFormOptions : {
				beforeInitData:function()
				{
					$(this).saveCell(globalScope.gRow, globalScope.gCol);
				}
			}, // edit
			grid.hasOwnProperty('addFormOptions') ? grid.addFormOptions : {}, // add
			grid.hasOwnProperty('delFormOptions') ? grid.delFormOptions : {}, // delete
			grid.hasOwnProperty('searchFormOptions') ? grid.searchFormOptions : {}, // search
			{}) // view
		.navButtonAdd('#'+$t+'_toppager',
			{
				id:'ex_import_'+$t,caption:'Экспорт в Excel',buttonicon:'fa fa-file-excel-o',
				onClickButton:function(e)
				{
					excelChooser.call(this,lsName);
				}
			}
		)
		.navButtonAdd('#'+$t+'_toppager',{id:'misс_'+$t,caption:app.miscmenutext,buttonicon:'fa fa-angle-down',menu:true})
		.navButtonAdd('#'+$t+'_toppager',
			{
				id:'clearSort_'+$t,caption:app.clearsorttext,buttonicon:'fa fa-trash',append:'misс_'+$t,
				onClickButton:function(e)
				{ 
					removeSort.apply(this,[tableId,grid.tableSort]);
				}
			}
		)
		if(grid.main === true){
			$('#'+$t).navGrid()
				.navButtonAdd('#'+$t+'_toppager',{id:'clearSession_'+$t,caption:app.reloadSession,buttonicon:'fa fa-recycle',
					onClickButton:function(e)
					{
						AjaxKillSession(true,false);
					}
				})
		}
		if(useLs === true)
		{
			$('#'+$t).navGrid()
				.navButtonAdd('#'+$t+'_toppager',
				{id:'clearGridLs_'+$t,caption:app.clearglstext,buttonicon:'fa fa-trash',append:'misс_'+$t,
					onClickButton: function(e){
						removeObjectFromLocalStorage.call(e,lsName,true);
					}
				})
				.navButtonAdd('#'+$t+'_toppager',
				{id:'rewampColumns_'+$t,caption:app.rewampcoltext,buttonicon:'fa fa-eye',//append:'misс_'+$t,
					onClickButton:function(e)
					{ 
						var $t = this;
						$(this).jqGrid('columnChooser',
						{
							done: function (perm)
							{
								if (perm)
								{
									$($t).jqGrid("remapColumns", perm, true);
									saveColumnState.call($($t), perm, lsName);
								}
							},
							width:500,
							height:500
						});
					}
				});
		}
		if(grid.goToBlank === true)
		{
			$('#'+$t).navGrid()
				.navButtonAdd('#'+$t+'_toppager',
				{id:'goToBlank'+$t,caption:'Добавить...',buttonicon:'fa fa-angle-double-down',
					onClickButton: function(e){
						scrollToNew.apply(this,[e,grid.goToBlankOptions]);
					}
				})
		}
	}
	pager.call($('#'+$t)[0]);
}
function pager(g_name)
{
	var pager = $('#'+this.id+'_toppager');
	var overflow = checkOverflow(pager[0]),overflow_parts,overflow_button,buttons_test = {},t = this;
	var pager_parts = $('#'+this.id+'_toppager > div > table > tbody > tr > td:visible'); // fuck you that why!
	
	
	$('#'+this.id+'_p_left').remove();
	
	// nav table fixed width is 215px, min rowcount width is 100px
	// pro skillz here!
	if(this.offsetWidth - 215 <= 100)
	{
		$('#'+this.id+'_p_center').width(this.offsetWidth);
		$('#'+this.id+'_p_right').remove();
	}
	else
	{
		$('#'+this.id+'_p_center').width(this.offsetWidth / 2);
		$('#'+this.id+'_p_center').attr('align','right');
		$('#'+this.id+'_p_right').width(121);
	}
	
	$('#clearSession_'+this.id).appendTo('#'+this.id+'_toppager_right');
	$('#rewampColumns_'+this.id).appendTo('#'+this.id+'_toppager_right');
	$('#ex_import_'+this.id).appendTo('#'+this.id+'_toppager_right');
	$('#refresh_'+this.id+'_top').appendTo('#'+this.id+'_toppager_right');
	$('#misс_'+this.id).appendTo('#'+this.id+'_toppager_right');
	$('#pg_'+this.id+'_toppager').css('border','0px');
	
	function minimize(object)
	{
		if(Object.keys(object).length > 0)
		{
			var next_self = $(object.el).prev().nextAll().andSelf().not('#misс_'+this.id), /// SHIT
			this_li,menu_ul;
			
			$(this).navGrid().navButtonAdd(pager[0].id,{id:'minimize_'+this.id+'_'+object.of.id,caption:'&nbsp;',buttonicon:'fa fa-angle-double-down',menu:true})
			$('#minimize_'+this.id+'_'+object.of.id).appendTo(object.of);
			menu_ul = $('#minimize_'+this.id+'_'+object.of.id).find('ul');
			$.each(next_self,function(i,v)
			{
				
				$(this).find('span')
					.removeClass('ui-icon')
					.attr('style','float:left !important;padding:2px;')
					.wrapAll('<li id="'+this.id+'" class="ui-corner-all" style="width:47px"></li>');
				this_li = $(this).find('li');
				
				this_li.hover(
					function ()
					{
						if (!$(this).hasClass('ui-state-disabled'))
							$(this).addClass('ui-state-hover');
					},
					function ()
					{
						$(this).removeClass("ui-state-hover");
					}
				);
				
				$(this).copyEventTo('click',this_li,true);
				/*
				 * Hide menu
				 */
				this_li.bind('click',function(e)
				{
					$(this).parent().hide();
				})

				this_li.appendTo(menu_ul);
	
				this.remove();
			})
		}
	}
	if(overflow)
	{
		$.each(pager_parts,function(i,v)
		{
			overflow_parts = checkOverflow(v);
			
			if(overflow_parts)
			{
				$.each($(v).find('td:visible'),function(ind,val)
				{
					overflow_button = checkOverflow(val,v,'horizontal');
					if(overflow_button)
					{
						buttons_test.el = val;
						buttons_test.of = v;
						minimize.call(t,buttons_test)
						return false;
					}
				})
				
			}
		})
	}
}
// grid - function for checkbox(find here "checkBox")
function MakeCellEditable(rowId,colName,gridname)
{
	var checked = $(this).is(':checked');
	var rowids =  $(gridname).getDataIDs();
	var colModel =  $(gridname).getGridParam().colModel;
	
	for (var i = 0; i < rowids.length; i++)
	{
		if(rowId == rowids[i])
		{
			i++;
			for (var j = 0; j < colModel.length; j++)
			{
				if (colModel[j].name == colName)
				{
					$(gridname).editCell(i,j,true);
					$('#'+i+'_'+colName).prop('checked', checked);
					$(gridname).saveCell(i,j);
				}
			}
		}                
	}            
}
// grid - scroll to new row
// param_2 - inlineOptions object
function scrollToNew(e,inlineOptions)
{
	$(this).jqGrid('restoreCell', globalScope.gRow, globalScope.gCol);
	$(this).setGridParam({cellEdit:false});
	if(typeof inlineOptions !== typeof undefined){
		$(this).jqGrid('editRow','blank',inlineOptions);
	} else {
		$(this).jqGrid('editRow','blank',inlineEditOptions);
	}
}
// grid - refresh function
function refreshGrid(e)
{
	$(this).trigger("reloadGrid");
}
// grid - repairForm combobox;
function repairForm(form)
{
	$('tr').has('.ui-autocomplete-input').find('span.ui-button').css({'width':'13%','height':'16px'});
}
// grid - refresh after save cell
// param_1 - (fields:['Name_1','Name_2']);
// param_2 - passed event params [rowid, cellname, value, iRow, iCol];
function afterSaveCellReload(cnameObject,gridParams)
{
	var cellName = gridParams[1],t = this;
	for(var i=0;i < cnameObject['fields'].length;i++){
		if(cellName === cnameObject['fields'][i]){
			$(t).trigger("reloadGrid");
			// remove saved row,timeout needed.
			setTimeout(function() { t.p.savedRow = []; }, 0);
		}
	}
}
// grid - clear filters button
function clearFilters(e)
{
	if ($('#clearFilters_'+this.id).hasClass('ui-state-hover-filter')){
		$(this)[0].clearToolbar();
		$('select[id^=gs_]').selectmenu("selectNone");
	}
}
// grid - clear filters button add class
function filterState(data, status, xhr)
{
	if ($(this).jqGrid('getGridParam', 'search') == true) {
		$('#clearFilters_'+this.id+'_top').addClass('ui-state-hover-filter');
	} else  {
		$('#clearFilters_'+this.id+'_top').removeClass('ui-state-hover-filter');
	}
}
// grid - saving iRow/iCol for global usage
function beforeEditCellActions(rowid, cellname, value, iRow, iCol){
	globalScope.gRow = iRow;
	globalScope.gCol = iCol;
}
// grid - focus all cell text
function focusInputHTML(rowid,cellname,value,iRow,iCol)
{
		$("#"+iRow+"_"+cellname).select().focus();
}
// ?
// ?
function deleteSub(subgrid_id, row_id)
{
	$("div[id ^=alertmod_][id $=_t]").remove();
	mainGridRowId.splice( $.inArray(row_id, mainGridRowId), 1 );
}
// grid - collapse subgrid on subgrid expand
function collapseAll(subgrid_id, row_id)
{
	var expanded = $("td.sgexpanded", $(this))[0],
	expandedRow = $(expanded).parent()[0],
	t=this;
	if (expandedRow !== undefined)
	{
		setTimeout(function(){
			$(t).collapseSubGridRow(expandedRow.id);
		}, 0)
	}
}
// remove sort order to default
function removeSort(column,order)
{
	column = typeof column !== 'undefined' ? column : 'Код';
	order = typeof order !== 'undefined' ? order : 'ASC';
	
	if($('span[sort="asc"]').length > 0 && $('span[sort="desc"]').length > 0)
	{
		$('span[sort="asc"]').parent().css('display','none');
		$('span[sort="asc"]').remove()
		$('span[sort="desc"]').remove()
	}
	$(this).jqGrid('setGridParam', {sortorder: order});
	$(this).jqGrid('sortGrid',column,true);
}
/*
 *	Libs grid creator
 * 	Nuff to sad. 
 */
function libsGrid(val)
{
	$('#libsel').change(function(e){
		var thisVal = this.value;
		var default_cn = 
		[
			"Код"
		];
		var default_cm = 
		[
			{name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true}
		]
		if(thisVal.length > 0)
		{
			$.ajax({
				url:'/php/misc',
				type:'post',
				async:false,
				dataType:'json',
				data:
				{
					oper:"get_cols",
					tname:thisVal,
				},
				success:function(data)
				{
					if(($.isArray(data) && data.length > 0) && (data.length > default_cm.length))
					{
						for(var i=0;i < data.length;i++)
						{
							if(data[i] !== 'Код')
							{
								var if_exists = default_cm.some(function(element, index, array) { return element.name === data[i] });
								var lower_field = data[i].toLowerCase();
								var field_name = data[i].replace('Код','').replace('_',' ');
								if(if_exists === false)
								{
									default_cn.push(field_name)
									if(lower_field.indexOf('date') >= 0 || lower_field.indexOf('дата') >= 0)
									{
										default_cm.push({name:data[i],index:data[i],searchoptions:{sopt:['dateEq','dateNe','dateLe','dateGe'],dataInit: cusDp},formatter:'date',formatoptions: {srcformat:'Y-m-d',newformat:'d.m.Y'},editoptions: {maxlengh: 10,dataInit: elemWd}})
									}
									else if(lower_field.indexOf('код') >= 0)
									{
										lower_field = 'Б_'+lower_field.substring(0,lower_field.indexOf('_код'));
										var f_name = 'Название';
										
										/* tables with translation Страны/Города */
										if(lower_field.indexOf('страны') > -1 || lower_field.indexOf('города') > -1)
										{
											f_name += '_'+getUserPref('lang');
										}
										
										var lib_data = ajaxSelectSingle({type:'SELECT',order:'2',id:true},{tName:lower_field,tfields:[{'field':'Код'},{'field':f_name}]});
										
										default_cm.push({
											name:data[i],
											index:data[i],
											formatter:'select',
											stype:'select',searchoptions:{sopt:['eq'],value:':',dataInit:dataSelect2,attr: {multiple: 'multiple',tname:lower_field,flds:"Код,"+f_name,sfld:f_name,order:'2',idz:'true'}},
											edittype:'select',editoptions:{value:'NULL:;'+lib_data,dataInit:dataInitAcComboBox}
										})
									}
									else
									{
										default_cm.push({name:data[i],index:data[i]})
									}
								}
							}
						}
					}
				}
				
			})
		}
		
		$('#gridcontainer').remove();
		$('body').append('<div id="gridcontainer"></div>');
		$('#gridcontainer').append("<table class='gridclass' id='lib'></table>");
		createGrid({
			main:true,
			name:'lib',
			table:thisVal,
			id:'Код',
			tableSort:'ASC',
			filterToolbar:true,
			navGrid:true,
			cn:default_cn,
			cm:default_cm,
			useLs:false,
			options:{
				height:gridHeight(30),
				shrinkToFit:true,
				cellEdit:true,
				autowidth: true,
				rowNum: 50
			},
			events:{
				beforeSubmitCell:function(rowid, cellname, value, iRow, iCol)
				{ 	
					return {tid:'Код',tname:thisVal}; 
				},
				afterEditCell:function(rowid,cellname,value,iRow,iCol)
				{ 
					focusInputHTML.apply(this,[rowid,cellname,value,iRow,iCol]);
				},
				onCellSelect:function(rowid,iCol,cellcontent,e)
				{
					if(rowid === 'blank'){
						$(this).jqGrid('saveCell', globalScope.gRow, globalScope.gCol);
						$(this).setGridParam({cellEdit:false});			
						$(this).jqGrid('editRow','blank',{extraparam:{oper:'add',tname:thisVal}});
					} else {
						if($('input[class="editable"]').length > 0){
							$(this).jqGrid('restoreRow','blank');
							$(this).setGridParam({cellEdit:true});
						}
					}
				},
				gridComplete:function()
				{
					gridTools.call(this,
						function(rowid){  $(this).jqGrid('saveRow','blank',{extraparam:{oper:'add',tname:thisVal}}); },
						function(rowid){ $(this).jqGrid('delGridRow', rowid,{delData:{tid:'Код',tname:thisVal}}); });
				}
			}
		});
	})
}
/*
 * inline edit function
 */
function inlineSucFunc(responce)
{
	var id = responce.responseText,
	valObject = $('#blank',this).find('td'),
	$t = this,blankData = {};
	valObject.each(function(i){
		var column = $(this).attr('aria-describedby').replace($t.id+'_',''),
		value = $(this).children().val();
		
		if((column.indexOf('data') > -1 || column.indexOf('Дата') > -1 || column.indexOf('дата') > -1) && typeof value !== typeof undefined && value.length >= 10 )
		{
			value = value.split('.');
			value = value[2]+'-'+value[1]+'-'+value[0];
		}
		if(value) 
			blankData[column] = value;
		else 
			blankData[column] = null;
	})
	$(this).jqGrid('addRowData',id,blankData,'before','blank');
	$('#blank',this).remove();
	$(this).jqGrid('addRowData', 'blank', {});
	$(this).setGridParam({cellEdit:true});
	
	if(this.p.footerrow === true){
		var footerData = $(this).jqGrid('footerData','get');
		for(key in footerData){
			if(footerData[key] !== '&nbsp;' && footerData[key] !== 'Итого'){
				var x = {};
				x[key] = $(this).jqGrid('getCol',key,false,'sum')
				$(this).jqGrid("footerData","set",x,false);
			}
		}
	}
}
/*
 * calculate grid height
 */
function gridHeight(other)
{
	var mHeight = 136,height;
	height = $(window).height() - mHeight - other;
	return height;
}
/*
 * Dependend selects
 */
function dep_selects()
{
	var postData = $(this).getGridParam('postData'),$t = this,$t_hDiv = this.grid.hDiv;
	function construct(f_data)
	{
		var temp_filters,perm_filters,selects = [],cur_col,sel_data,found_field,found_data,found_op,filtersLength;
		
		perm_filters = f_data.hasOwnProperty('perm_filters') ? JSON.parse(f_data.perm_filters) : undefined;
		temp_filters = f_data.hasOwnProperty('filters') ? JSON.parse(f_data.filters) : undefined;
		
		filtersLength = typeof perm_filters !== typeof undefined ? typeof temp_filters !== typeof undefined ? perm_filters.rules.length + temp_filters.rules.length : perm_filters.rules.length : 0
		if(typeof perm_filters !== typeof undefined)
		{
			selects = $('select.select2-hidden-accessible',$t_hDiv);
			$.each(selects,function(i,v)
			{
				if($(v).children().length > 1)
					return true;
					
				s_data = $(v).data('search');
				options = $($t).getColProp(v.name);
				
				s_data.sub = new Array();
				
				for(var y = 0;y < perm_filters.rules.length;y++)
				{
					rule = perm_filters.rules[y];
						s_data.sub.push({
							sfld:rule.field,
							op:rule.op,
							search:rule.data,
							perm_search:rule.perm
						})
				}
				$(v).data('search',s_data);
			})
		}
		if(typeof temp_filters !== typeof undefined)
		{
			for(var i = 0;i < temp_filters.rules.length;i++)
			{
				rule = temp_filters.rules[i];
				cur_col = $($t).getColProp(rule.field);
				if(cur_col.stype === 'select')
					selects = $('select.select2-hidden-accessible',$t_hDiv).not('#gs_'+cur_col.name);
				else
					selects = $('select.select2-hidden-accessible',$t_hDiv);
				$.each(selects,function(i,v)
				{
					if($(v).children().length > 1)
						return true;
					
					s_data = $(v).data('search');
					options = $($t).getColProp(v.name);
					
					if(!$.isArray(s_data.sub))
						s_data.sub = new Array();
					
					// found_field if current iteration of filter field already presented in search-data then = true
					found_field = $.grepStop(rule.field,s_data.sub,'sfld',['perm_search',false]);
					// found_data if current iteration of filter searchData already presented in search-data then = true
					found_data = $.grepStop(rule.field,s_data.sub,'sfld',['search',rule.data]);
						
					if(found_field === true && found_data === false)
					{
						for(var x = 0;x < s_data.sub.length;x++)
						{
							if(s_data.sub[x].sfld === rule.field && s_data.sub[x].perm_search === false)
							{
								s_data.sub[x].search = rule.data;
								break;	
							}
						}
					}
					// if filter already set 
					else if(found_field === true && found_data === true)
					{
						return true;
					}
					else
					{
						s_data.sub.push({
							sfld:rule.field,
							op:rule.op,
							search:rule.data,
							perm_search:false
						})
					}
					$(v).data('search',s_data);
				})
			}
		}
	}
	if(postData['_search'] === false && postData.hasOwnProperty('perm_filters') === false)
	{
		selects = $('select.select2-hidden-accessible',$t_hDiv)
		$.each(selects,function(i,v)
		{
			if($(v).children().length > 1)
				return true;
				
			s_data = $(v).data('search');
			
			if(typeof s_data !== typeof undefined && s_data.hasOwnProperty('sub'))
				s_data.sub = undefined;
		})
	}
	else
	{
		construct(postData);
	}
		
}
/*
 * smart filers
 */
function smart_filters(op,op_)
{
	if(op === 'eq' && op_ === 'ne')
		return false
	else if(op === 'ne' && op_ === 'eq')
		return false
	else if((op === 'gt' || op === 'lt' || op === 'ge' || op === 'le') && (op_ === 'gt' || op_ === 'lt' || op_ === 'ge' || op_ === 'le'))
		return false
	return true;
}
/*
 * Permament filter button
 */
function filter_button(e,filterObj)
{
	var button = e.currentTarget,permFilter,spliceIndex,isArray,$t = this,$t_pDiv = this.grid.topDiv;
	
	permFilter = typeof this.p.postData.perm_filters !== typeof undefined ? JSON.parse(this.p.postData.perm_filters) : undefined;
	
	isArray = filterObj instanceof Array ? true : false;
	
	function filter(init_f_object,f_object,index,proceed)
	{
		if($(button).hasClass('ui-state-hover-filter'))
		{
			if(typeof this.p.savedPermFilter !== typeof undefined)
			{
				for(var i = 0;i < this.p.savedPermFilter.length;i++)
				{
					if(this.p.savedPermFilter[i].field === f_object.field && smart_filters(this.p.savedPermFilter[i],f_object.op) === true)
					{
						init_f_object.rules.push(this.p.savedPermFilter[i]);
						this.p.savedPermFilter.splice(i,1);
					}
				}
			}
			if(typeof index !== typeof undefined)
				init_f_object.rules.splice(index,1);
		}
		else
		{
			if(typeof index !== typeof undefined)
			{
				if(typeof this.p.savedPermFilter === typeof undefined)
					this.p.savedPermFilter = [init_f_object.rules[index]];
				else
					this.p.savedPermFilter.push(init_f_object.rules[index]);
				
				init_f_object.rules.splice(index,1);
			}
			init_f_object.rules.push(f_object);
		}
		this.p.postData.perm_filters = JSON.stringify(init_f_object);
		
		if(proceed)
		{
			$('td[id^="refresh"]',$t_pDiv).trigger('click');
			$(button).toggleClass('ui-state-hover-filter');
			$(button).find('.fa').toggleClass('fa-unlock-alt fa-lock')
		}
	}
	
	if(typeof permFilter !== typeof undefined)
	{
		if(!isArray)
		{
			for(var i = 0;i < permFilter.rules.length;i++)
			{
				if(permFilter.rules[i].field instanceof Array && permFilter.rules[i].field.equals(filterObj.field) === true)
				{
					spliceIndex = i;
					break;
				}
				else if(permFilter.rules[i].field === filterObj.field && smart_filters(permFilter.rules[i].op,filterObj.op) === false)
				{
					spliceIndex = i;
					break;
				}
				else if(permFilter.rules[i].field === filterObj.field && permFilter.rules[i].op === filterObj.op && permFilter.rules[i].data === filterObj.data)
				{
					spliceIndex = i;
					break;
				}
			}
			filter.call(this,permFilter,filterObj,spliceIndex,true);
		}
		else
		{
			for(var i = 0;i < filterObj.length;i++)
			{
				for(var y = 0;y < permFilter.rules.length;y++)
				{	
					if(permFilter.rules[y].field === filterObj[i].field)
					{
						spliceIndex = y;
						break;
					}
				}
				if(i != (filterObj.length - 1))
					filter.call(this,permFilter,filterObj[i],spliceIndex,false);
				else
					filter.call(this,permFilter,filterObj[i],spliceIndex,true);
			}
		}
	}
}
function excelChooser(localStorageName)
{
	var $t = this;
	var colModel = $(this).getGridParam("colModel");
	var colNames = $(this).getGridParam("colNames");

	var wrapper = $('<div><select style="width:460px;height:385px" multiple="multiple"></select></div>');
	var select = $('select',wrapper);
	var selected,ls_data,ls_data_search,filters,excel_obj = new Object;
	
	ls_data = typeof localStorageName !== typeof undefined ? getObjectFromLocalStorage(localStorageName) : undefined;
	
	
		
	$.each(colModel, function(i,v)
	{
		selected = new String;
		if (this.hidedlg)
			return;
		if(ls_data && ls_data.hasOwnProperty('importState'))
		{
			ls_data_search = $.grep(ls_data.importState,function(val,ind)
			{
				return val === v.name
			})
			if(ls_data_search.length > 0)
				selected = 'selected="selected"'
		}
		
		select.append("<option value='"+this.name+"' "+selected+">"+$.jgrid.stripHtml(colNames[i])+"</option>");
	});
					
	wrapper.dialog({
		modal:true,
		width:500,
		height:500,
		title:'Выбор полей для импорта',
		buttons:{
			'Экспортировать':function()
			{
				var dialog_inst = $(this).data('uiDialog');
				var flds = [],filters,options,sel_this,sel_data,selected = new Array;
				
				$('option:selected',select).each(function(i) {
					sel_this = this.value;
					selected.push(sel_this);
					options = $.grep(colModel,function(index,value){
						return colModel[value].name === sel_this;
					})
					if(options[0].formatter === 'select')
					{
						sel_data = $('#gs_'+options[0].name).data('search');
						if(typeof sel_data !== typeof undefined)
							flds.push({name:'(SELECT '+sel_data.sfld+' FROM '+sel_data.tname+' WHERE '+sel_data.refid+'='+sel_data.ref_fld+') as ['+this.text+']'});
						else
							flds.push({name:this.value});
					}
					else if(options[0].formatter === 'date')
						flds.push({name:'(CONVERT(VARCHAR(10),'+this.value+',104))as ['+this.text+']'});

					else
						flds.push({name:'['+this.value+'] as ['+this.text+']'});
				})
				if(flds.length > 0)
				{
					if(dialog_inst._saved === true && ls_data)
					{
						ls_data = getObjectFromLocalStorage(localStorageName);
						ls_data.importState = selected;
						saveObjectInLocalStorage(localStorageName,ls_data);
					}
					if($t.p.postData.hasOwnProperty('perm_filters'))
						filters = JSON.parse($t.p.postData.perm_filters);
						
					if($t.p.postData.hasOwnProperty('filters'))
					{
						if(filters)
							filters.rules.push.apply(filters.rules, (JSON.parse($t.p.postData.filters)).rules);
						else
							filters = JSON.parse($t.p.postData.filters);
					}
					
					excel_obj.qry = {type:'SELECT',tname:$t.p.postData.tname,fields:flds};
					excel_obj.filters = typeof filters !== typeof undefined ? filters:null;
					excel_obj.tname = $t.p.postData.tname
					
					if($t.p.postData.hasOwnProperty('mainIdname') && $t.p.postData.hasOwnProperty('mainId'))
					{
						excel_obj.qry.mainIdname = $t.p.postData.mainIdname;
						excel_obj.qry.mainId = $t.p.postData.mainId;
					}
					
					getExcel(excel_obj.qry,excel_obj.filters,excel_obj.tname);

				}
				$(this).dialog('destroy');
			},
			'Сохранить выбранные поля':function(e)
			{
				var dialog_inst = $(this).data('uiDialog'),this_button = e.currentTarget;
				
				$(this_button).toggleClass('ui-state-highlight').blur();
				
				if($(this_button).hasClass('ui-state-highlight'))
					dialog_inst._saved = true;
				else
					dialog_inst._saved = false;
				
				$(this).data('uiDialog',dialog_inst);
			},
			'Закрыть':function()
			{
				$(this).dialog('destroy');
			}
		}
	})
	$(select).multiselect({
		animated:'fast',
		dividerLocation: 0.5,
		hide:'slideUp',
		searchable: true,
		show: "slideDown",
		sortable: false,
		nodeComparator: function(node1,node2)
		{
			var text1 = node1.text(),
			    text2 = node2.text();
			return text1 == text2 ? 0 : (text1 < text2 ? -1 : 1);
		}
	});
}
// grid - float formatter
var floatFormatter = function (cellvalue, options, rowObject)
{
	if(options.rowId !== 'blank')
	{
		if(typeof cellvalue === 'string')
		{
			var cv = parseFloat(cellvalue.replace(/[,]+/g, '.')).toFixed(2),
			res = isNaN(cv) ? '0.00': cv
			return res;
		} 
		else if (typeof cellvalue === 'number') return cellvalue.toFixed(2);
		else if (typeof cellvalue === 'object') return '0.00';
		else if (typeof cellvalue === 'undefined') return '0.00';
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
	var disabled;
	options.colModel.editable ?  disabled = '' : disabled = 'disabled="disabled"'
	if(typeof action === typeof undefined && options.rowId !== 'blank')
		cellvalue = '0';
		
	if(typeof cellvalue !== typeof undefined && options.rowId !== 'blank')
	{
		var checked = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? ' checked="checked"': '';
		var inputControl = '<input '+disabled+' id="'+ options.rowId +'_'+ options.colModel.name +'_t" class="view" name="'+options.colModel.name+'" style="width:100%" type="checkbox" ' + checked + ' value="' + cellvalue + '" onclick="MakeCellEditable.call(this,&apos;'+ options.rowId + '&apos;,&apos;'+options.colModel.name+'&apos;,&apos;'+'#'+this.id+'&apos;)" />'
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
dataSelect2 = function(elem,options)
{
	var data = $(elem).data('search'),$t = this,$t_hDiv,postData,elem_options
	var old = typeof data === typeof undefined ? true:false;
	var wd;
	if(typeof options !== typeof undefined)
	{
		wd = typeof options.width === typeof undefined ? '100%':options.width;
	}
	else
		wd = $(elem).width();	
		
	if(!old)
	{
		// defaults
		data.flds = data.hasOwnProperty('flds') ? data.flds : ['Код','Название'];
		data.sfld =	data.hasOwnProperty('sfld') ? data.sfld : data.flds[1];
		data.order = data.hasOwnProperty('order') ? data.order : '2';
		data.id = data.hasOwnProperty('id') ? data.id : true;
	}
	
	
	// if called within grid
	if(this.grid){
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
			
			if(old)
			{
				$(elem).data('search',data);
			}
			
			$(elem).select2({
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				placeholder: app.select2placeHolder,
				language:app.select2langVal,
				ajax:{
					url: '/php/libs',
					type: 'POST',
					dataType: 'json',
					delay: 0,
					data:function(params){
						return {
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
			});
		} 
		else
		{
			$(elem).select2({
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				language:app.select2langVal,
				placeholder:app.select2placeHolder,
				dropdownAutoWidth:true
			})
		}
	} 
	else
	{
		if(typeof data !== typeof undefined)
		{
			$(elem).select2({
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				placeholder:app.select2placeHolder,
				language:app.select2langVal,
				ajax:{
					url: '/php/libs',
					type: 'POST',
					dataType: 'json',
					delay: 0,
					data:function(params){
						return {
							search: params.term,
							info : JSON.stringify(data),
							page: params.page
						}
					},
					processResults: function (data, page) {
						return { results: data.results }
					},
					cache: true
				},
				dropdownAutoWidth:true
			});
		}
		else
		{
			$(elem).select2({
				width:wd,
				multiple:false,
				minimumInputLength:0,
				allowClear:true,
				language:app.select2langVal,
				placeholder:app.select2placeHolder,
				dropdownAutoWidth:true
			})
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
	$(elem).after('<i style="padding:2px;padding-left:3px" class="fa fa-calendar-o"></i>');
	$(elem).attr('style','float:left;padding:0px;width:84%')
	
	
	var $this = $(this);
	$(elem).datepicker({
		showButtonPanel:true,
		showAnim:"slideDown",
		minDate: new Date(2012, 01, 01),
		dateFormat: 'dd.mm.yy',
		changeYear: true,
		changeMonth: true,
		showWeek: true,
		beforeShow: function(input,inst){
			setTimeout(function () {
				var t = this;
				var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
				$("<button>",
				{
					text:"Выбрать интервал дат",
					css:{'width':'100%','outline':'none'},
					click: function (e)
					{
						inst.range = true;
						$(inst.dpDiv[0]).find('a.ui-state-highlight').attr('style', 'color:#0073ea !important;background:#f6f6f6');

						$(this).addClass('ui-state-highlight');
					}
				}).appendTo(buttonPane).addClass("ui-datepicker-range ui-state-default ui-priority-primary ui-corner-all");
				inst.rangeButton = $('.ui-datepicker-range');
			},0);
		},
		onClose: function (dateText, inst)
		{
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
					var t = this
					
					inst.inline = true;
					inst.first = selectedDate;
					setTimeout(function ()
					{
						$(inst.dpDiv[0]).find('a.ui-state-highlight').attr('style', 'color:#0073ea !important;background:#f6f6f6');
						inst.rangeButton.appendTo(".ui-datepicker-buttonpane",t);
					},0)
				}
				else
				{
					if(selectedDate > inst.first)
						$(this).val(inst.first+":"+selectedDate);
					else if (selectedDate < inst.first)
						$(this).val(selectedDate+":"+inst.first);
						
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
	{
		$('#delmod'+this.id).focus();
	}
	form.closest(".ui-jqdialog").position({of: window,my: "center center",at: "center center"});
}