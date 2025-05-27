/*
 * Это **здец, товарищи!
 *
 *
 *
 */
function jqGrid$(source) {
	setter.call(this, source);
	this.default_markup = typeof this.defaultPage === typeof undefined ? true : this.defaultPage;
	this.location = window.location.search.replace(/(\?reference=)|(\&.*)/gi,'');
	if(!this.name)
		return;
	this.name = this.name.replace(/\s+/g, '');

	this.hasOwnProperty('options') ? undefined : this.options = new Object();

	this.device = isDevice();

	if (this.default_markup === true && (this.subgrid !== true || this.pseudo_subrgid === true))
		this.create_markup();

	this.grid_element = $('#' + this.name);
	this.grid_element_pure = document.getElementById(this.name);
	this.grid_first_load = true;

	if (this.grid_element.length === 0)
		return $.alert('No element table found.', 'Set table ASAP!' + this.name);

	if(this.prepare === true)
		return;

	this.prepare_post_data();
	this.prepare_col_model();
	this.grid_element_pure.jqGrid$ = this;
}
jqGrid$.prototype.create_markup = function() {
	var wrapper = document.createElement('div');
	var grid_table = document.createElement('table');

	wrapper.id = 'gridcontainer';
	grid_table.id = this.name;
	grid_table.className = 'gridclass';
	wrapper.appendChild(grid_table);
	document.body.appendChild(wrapper);
}
jqGrid$.prototype.prepare_defaults = function() {
	this.order_name = typeof this.orderName !== typeof undefined ? this.orderName : this.id;
	this.sop_ops = ['cn', 'ne', 'eq', 'bw'];
	if ( typeof this.navGridOptions !== typeof undefined )
		this.navigator_opts();
	if ( typeof this.onCellSelect !== typeof undefined )
		this.cell_select();
	if ( typeof this.beforeSubmitCell !== typeof undefined )
		this.add_cell_params();
	if ( typeof this.subGridOps !== typeof undefined )
		this.add_subgrids();
	if ( typeof this.footer !== typeof undefined )
		this.options.footerrow = true;

	this.pager_name = this.name + '_p';
	$('<div id="' + this.pager_name + '"></div>').insertAfter(this.grid_element);

	this.set_defaults();

	if (this.hideBottomNav === true) {
		this.options.viewrecords = false;
		this.options.rowList = '';
		this.options.pgbuttons = false;
		this.options.pgtext = null;
	}
	$.extend(this.jgrid_init_obj, this.options, this.events);
	$.extend($.jgrid.defaults, this.jgrid_init_obj);
}
jqGrid$.prototype.prepare_col_model = function() {
	var self = this;
	if ( typeof this.libsGrid !== typeof undefined && this.libsGrid === true) {
		this.navGridOptions = {
			add:true
		};
		if ($('select.select2me').length === 0)
			$.alert('Не обнаружен select!');
		else {
			if($('select.select2me').val().length == 0)
			{
				this.cm_names = Object();
				this.cm_names.q_string = String();
				this.cm_names.arr = Array();
				self.col_meta = {};
				self.modify_col_model();
				self.prepare_defaults();
				self.prepare_local_storage();
				self.create_jqGrid();
			}
			$('select.select2me').change(function(e) {
				self.table = this.value;
				self.prepare_post_data();
				$.ajaxShort({
					dataType:'JSON',
					data:{
						action:'get_column_meta',
						tname:self.post_data.source_table,
						db:self.ref_db
					},
					success:function(data)
					{
						if(Object.keys(data).length > 0)
							self.col_meta = data;
						else
							$.alert('Failed');

						self.grid_element.GridUnload(self.name);
						self.grid_element = $('#' + self.name);
						self.grid_element_pure = document.getElementById(self.name);
						self.grid_first_load = true;

						self.prepare_libs_col_model(data);
						self.cell_select();
						self.add_cell_params();
						self.navigator_opts(true);

						$.extend($.jgrid.defaults, self.events);
						self.create_jqGrid();
					}
				});
			})
		}
	}
	else
	{
		this.cm_names = Object();
		this.cm_names.q_string = String();
		this.cm_names.arr = Array();
		this.get_column_meta();
	}
}
jqGrid$.prototype.get_column_meta = function()
{
	var self = this;
	$.ajaxShort({
		dataType:'JSON',
		data:{
			action:'get_column_meta',
			tname:self.post_data.source_table,
			db:self.ref_db
		},
		success:function(data)
		{
			if(Object.keys(data).length > 0)
			{
				if(self.hasOwnProperty('col_meta'))
					$.extend(self.col_meta,data);
				else
					self.col_meta = data;
			}
			self.modify_col_model();
			self.prepare_defaults();
			self.prepare_local_storage();
			self.create_jqGrid();
		}
	});
}
jqGrid$.prototype.modify_col_model = function()
{
	for (var i = 0; i < this.cm.length; i++)
	{
		this.cm_names.q_string += this.cm[i].name + (i == (this.cm.length - 1) ? '' : ',');
		this.cm_names.arr[i] = this.cm[i].name;
		if((this.cm[i].hidden == false || typeof this.cm[i].hidden == typeof undefined) && (this.cm[i].editable == false || this.cm[i].celleditable == false))
		{
			if(!this.cm[i].hasOwnProperty('cellattr') && this.cm[i].formatter !== 'button')
				this.cm[i].cellattr = function(){ return 'style="cursor:no-drop"'; }
			// If cellattr is set via grid init, cursor added in jqGrid.src
		}
		if(this.cm[i].formatter == 'integer')
		{
			this.cm[i].searchoptions = {
				clearSearch:false,
				sopt:['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']
			};
		}
		if((this.hasOwnProperty('navGridOptions') && this.navGridOptions.add == true) || (this.onCellSelect === true))
			this.cm[i] = this.prepare_col_model_formoptions(this.cm[i],i + 1);
		if(this.cm[i].hasOwnProperty('gtype'))
			this.cm[i] = this.prepare_col_model_gtype(this.cm[i],this.cm[i].gtype);
	}
	delete this.hiddenindex;
	delete this.movedindex;
}
jqGrid$.prototype.prepare_col_model_gtype = function(cop_opts,gtype)
{
	var self = this;
	switch (gtype)
	{
		case 'checkbox':
			function onCheckBoxClickEv(rowid,grid,name)
			{
				var $this = $(this);
				var value = $this.attr('value'),new_value = value == 1 ? 0 : 1;
				var checked = $(this).is(':checked');
				var rowids =  $(grid).getDataIDs();
				var colModel =  $(grid).getGridParam().colModel;
				$this.parents('td').css('outline','none');
				for (var i = 0; i < rowids.length; i++)
				{
					if(rowid == rowids[i])
					{
						i++;
						for (var j = 0; j < colModel.length; j++)
						{

							if (colModel[j].name == name)
							{

								$(grid).editCell(i,j,true);
								$('#'+ i +'_'+ name).attr('value', new_value);
								$(grid).saveCell(i,j);
							}
						}
					}
				}
			};
			var custom_element_func = function(value,options)
			{
				var style = 'cursor:pointer;border-radius:2px;border:1px solid #dddddd;font-size:2em;width:96%;text-align:center;',className;
				value == 1 ? className = 'fa fa-check' : className = 'fa fa-times';
				value == 1 ? style += 'color:green;' : style += 'color:red;';
				var el = $('<i value="'+value+'" class="'+ className +'" style="'+ style +'"></i>');
				el.click(function(event)
				{
					var value_scope = $(this).attr('value'),new_value_scope = value_scope == 1 ? 0 : 1;
					if(new_value_scope == 1)
						$(this).css('color','green').switchClass('fa-times','fa-check').attr('value',new_value_scope);
					else
						$(this).css('color','red').switchClass('fa-check','fa-times').attr('value',new_value_scope);
				})
				return el;
			};
			cop_opts.formatter = 'custom_checkbox';
			cop_opts.edittype = 'custom';
			if(cop_opts.hasOwnProperty('formatoptions') && !cop_opts.formatoptions.hasOwnProperty('onCheckBoxClick'))
				cop_opts.formatoptions.onCheckBoxClick = function(element,rowid,grid) { onCheckBoxClickEv.call(element,rowid,grid,cop_opts.name) };
			else
				cop_opts.formatoptions = { onCheckBoxClick : function(element,rowid,grid) { onCheckBoxClickEv.call(element,rowid,grid,cop_opts.name) } };

			if(cop_opts.hasOwnProperty('editoptions'))
			{
				cop_opts.editoptions.custom_element = custom_element_func;
			}
			else
			{
				cop_opts.editoptions = {
					value:'1:0',
					custom_element: custom_element_func,
					custom_value:function(element,action)
					{
						return $(element).attr('value');
					}
				};
			}
			break;
	}
	return cop_opts;
}
jqGrid$.prototype.prepare_col_model_formoptions = function(cop_opts,index)
{
	var hide = false;
	if(cop_opts.hidden == true)
	{
		if(cop_opts.hasOwnProperty('editrules') && cop_opts.editrules.edithidden == false)
			hide = true;
		else if(cop_opts.editable == false || cop_opts.addformeditable == false)
			hide = true;
		else
		{
			if(typeof cop_opts.editable == typeof undefined && cop_opts.addformeditable != true)
				hide = true;
			else if (typeof cop_opts.addformeditable == typeof undefined & cop_opts.editable != true)
				hide = true;
			else if (cop_opts.hasOwnProperty('editrules') == false && cop_opts.editable != true && cop_opts.addformeditable != true)
				hide = true;
		}
	}
	else
	{
		if(cop_opts.editable == false || cop_opts.addformeditable == false)
			hide = true;
	}
	if(hide == true)
	{
		if(!this.hasOwnProperty('hiddenindex'))
			this.hiddenindex = [index];
		else
			this.hiddenindex.push(index);
		return cop_opts;
	}
	if(this.hasOwnProperty('hiddenindex'))
	{
		if(!this.hasOwnProperty('movedindex'))
		{
			this.movedindex = [index];
			index = this.hiddenindex[0];
			this.hiddenindex.shift();
			if(this.hiddenindex.length == 0)
				delete this.hiddenindex;
		}
		else
		{
			if(this.movedindex[0] < this.hiddenindex[0])
			{
				this.movedindex.push(index);
				index = this.movedindex[0];
				this.movedindex.shift();
				if(this.movedindex.length == 0)
					delete this.movedindex;
			}
			else
			{
				this.movedindex.push(index);
				index = this.hiddenindex[0];
				this.hiddenindex.shift();
				if(this.hiddenindex.length == 0)
					delete this.hiddenindex;
			}

		}
	}
	else if(this.hasOwnProperty('movedindex'))
	{
		this.movedindex.push(index);
		index = this.movedindex[0];
		this.movedindex.shift();

		if(this.movedindex.length == 0)
			delete this.movedindex;
	}
	function is_even(num)
	{
		if(num & 1)
			return false;
		else
			return true;
	}
	if(!cop_opts.hasOwnProperty('formoptions'))
	{
		//console.log(cop_opts.name + ' ' + Math.ceil(index / 2).toString() + ' ' + (is_even(index) ? 2 : 1).toString())
		if(this.formpos == false)
			cop_opts.formoptions = {};
		else
			cop_opts.formoptions = { rowpos:Math.ceil(index / 2),colpos:is_even(index) ? 2 : 1 };
		if(cop_opts.hasOwnProperty('editrules'))
			$.extend(true,cop_opts.editrules,{edithidden:true});
		else
			cop_opts.editrules = { edithidden : true };
		if(this.hasOwnProperty('col_meta') && this.col_meta.hasOwnProperty(cop_opts.name))
		{
			/* TODO ADD editrules COLUMNS BY TYPE */
			if(this.col_meta[cop_opts.name]['IS_NULLABLE'] == 'NO' && cop_opts.edittype != 'checkbox' && cop_opts.gtype != 'checkbox')
			{
				cop_opts.formoptions.elmsuffix = '<i style="color:red" class="fa fa-lg fa-asterisk"></i>';
				if(cop_opts.hasOwnProperty('editrules'))
					$.extend(true,cop_opts.editrules,{ required:true });
				else
					cop_opts.editrules = { required:true };
			}
			if(this.bound_by_datatype !== false)
			{
				if(this.col_meta[cop_opts.name]['DATA_TYPE'] == 'int')
				{
					if(cop_opts.hasOwnProperty('editrules'))
						$.extend(true,cop_opts.editrules,{ integer:true });
					else
						cop_opts.editrules = { integer:true };
				}
				else if(this.col_meta[cop_opts.name]['DATA_TYPE'] == 'numeric' || this.col_meta[cop_opts.name]['DATA_TYPE'] == 'decimal')
				{
					if(cop_opts.hasOwnProperty('editrules'))
						$.extend(true,cop_opts.editrules,{ number:true });
					else
						cop_opts.editrules = { number:true };
				}
			}
			if(this.col_meta[cop_opts.name]['COLUMN_DEFAULT'] != null)
			{
				var string = this.col_meta[cop_opts.name]['COLUMN_DEFAULT'],defaultValue = false;
				if(/getdate/.test(string) == true)
				{
					defaultValue = getDate(true);
				}
				else if(/\(\([0-9]*\)\)/.test(string) == true) {
					defaultValue = string.match(/\d+/)[0];
				}
				else if(/\(\'([A-Z]*)\'\)/.test(string) == true) {
					defaultValue = string.match(/\(\'([A-Z]*)\'\)/)[1];
				}
				if(cop_opts.hasOwnProperty('editoptions') && !cop_opts.editoptions.hasOwnProperty('defaultValue'))
					cop_opts.editoptions.defaultValue = defaultValue;
			}
		}
	}
	else
	{
		/* TODO */
		$.extend(cop_opts.formoptions,{

		})
	}
	return cop_opts;
}
jqGrid$.prototype.prepare_libs_col_model = function(data) {
	// IMPORTANT libs table possible names;
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	this.lib_tables_prefix = ['Б_', 'Выбор_',''];
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	if (!$.isPlainObject(data))
		return $.alert('Wrong data.');

	var name_toLower,
	    clear_name,
	    temp_tname,
	    select_data;

	this.cm = new Array;
	this.cn = new Array;

	var self = this;

	var i = 0;
	for(index in data)
	{
		name_toLower = index.toLowerCase();
		clear_name = index.replace(/Код/g, '').replace(/_/g, ' ');

		this.cn[i] = clear_name;

		this.cm[i] = new Object();
		this.cm[i].name = this.cm[i].index = index;
		if (index == Object.keys(data)[0]) {
			this.cm[i].hidden = true;
			this.cm[i].editable = false;
			this.cm[i].editrules = {
				edithidden : false
			};
			this.cm[i].hidedlg = false;
		}
		if(data[index]['DATA_TYPE'] == 'bit')
		{
			this.cm[i] = this.prepare_col_model_gtype(this.cm[i],'checkbox');
		}
		if (name_toLower.indexOf('date') >= 0 || name_toLower.indexOf('дата') >= 0) {
			this.cm[i].searchoptions = {
				sopt : ['dateEq', 'dateNe', 'dateLe', 'dateGe'],
				dataInit : cusDp
			};
			this.cm[i].formatter = 'date';
			this.cm[i].formatoptions = {
				srcformat : 'Y-m-d',
				newformat : 'd.m.Y'
			}
			this.cm[i].editoptions = {
				maxlengh : 10,
				dataInit : elemWd
			}
		} else if (name_toLower.indexOf('код') >= 0 && name_toLower.length > 3) {
			for (var y = 0; y < this.lib_tables_prefix.length; y++) {
				temp_tname = this.lib_tables_prefix[y] + name_toLower.substring(0, name_toLower.indexOf('_код'));
				try {
					select_data = $.ajaxShort({
						async : false, // we are sorry xD
						data : {
							action : 'raw_lib_data',
							tname : temp_tname
						}
					});
					break;
				} catch(ex) {
					continue;
				}
			}
			this.cm[i].formatter = this.cm[i].stype = 'select';
			this.cm[i].formatoptions = {
				value : select_data
			};
			this.cm[i].searchoptions = {
				value : ':',
				dataInit : dataSelect2,
				attr : {
					'data-search' : JSON.stringify({
						tname : temp_tname,
						order : '2'
					})
				}
			};
			if ( select_data instanceof Object) {
				select_data = $.map(select_data, function(v, n) {
					return n + ':' + v;
				}).join(';')
			}
			function bindInit(temp_tname){
				return function(elem,opts) {
					new jqGrid_aw_combobox$(elem,{tname:temp_tname},opts,this);
				}
			};
			this.cm[i].editoptions = {
				dataInit:bindInit(temp_tname)
			};
			this.cm[i].editable = typeof select_data === typeof undefined ? false : true;
			select_data = undefined;
		}
		i++;
	}
}
jqGrid$.prototype.prepare_post_data = function()
{
	if (this.subgrid)
		this.post_data = {
			oper : 'view',
			tname : this.table,
			tid : this.id,
			mainId : this.subgridpost.mainid,
			mainIdname : this.subgridpost.mainidName
		};
	else
		this.post_data = {
			oper : 'view',
			tname : this.table,
			tid : this.id
		};
	if(this.options.treeGrid === true)
	{
		this.post_data.tree = true;
		this.post_data.tree_parent_id = this.options.treeGridParent_id;
	}
	if(this.hasOwnProperty('db'))
		this.post_data.db = this.db;
	this.post_data.source_table = this.hasOwnProperty('tableQuery') ? this.tableQuery : this.table;
	if(this.contextMenuFileTree == true)
		this.post_data.check_files = true;
	if (this.permFilter) {
		for (var i = 0; i < this.permFilter.rules.length; i++) {
			this.permFilter.rules[i].perm = true;
		}
		this.post_data['perm_filters'] = JSON.stringify(this.permFilter);
	}
}
jqGrid$.prototype.prepare_local_storage = function()
{
	if (this.main === true) {
		document.title = $.normalize(this.title || this.table.replace(/_/g,' '));
		//document.title = $.capitalize(this.title);
		this.use_local_storage = typeof this.useLs === typeof undefined ? true : this.useLs;
		this.local_storage = new jqGrid_local_storage$({
			grid_element_pure : this.grid_element_pure,
			grid_element : this.grid_element,
			has_sub: this.hasOwnProperty('subGridOps') && this.subGridOps.length > 0 ? true : false,
			col_model : this.cm,
			name : this.location + '.' + this.name + '.jqGrid'
		});
	}
	this.page = 1;
	this.sortname = this.use_local_storage ? this.local_storage.data ? this.local_storage.data.sortname : this.order_name : this.order_name;
	this.sortorder = this.use_local_storage ? this.local_storage.data ? this.local_storage.data.sortorder : this.tableSort : this.tableSort;
	this.rowNum = this.use_local_storage ? this.local_storage.data ? this.local_storage.data.rownum : this.jgrid_init_obj.rowNum : this.jgrid_init_obj.rowNum;
}
jqGrid$.prototype.create_jqGrid = function() {
	var self = this;
	this.grid_element.jqGrid("initFontAwesome").jqGrid({
		colNames : this.cn,
		colModel : this.cm,
		pager : '#' + this.pager_name,
		postData : this.post_data,
		page : this.page,
		sortname : this.sortname,
		sortorder : this.sortorder,
		rowNum : this.rowNum,
		beforeRequest:function()
		{
			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('beforeRequest'))
				self.events.beforeProcessing.call(this);
			if(self.hasOwnProperty('subgridpost'))
			{
				if(self.subgridpost.mainid.length == 0)
					return false;
			}
			if(this.p.postData.hasOwnProperty('sortQue'))
				delete this.p.postData.sortQue;
			if(this.p.postData.sidx != self.order_name)
				self.sorting();
		},
		beforeProcessing : function(data, status, xhr) {
			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('beforeProcessing'))
				self.events.beforeProcessing.call(this, data, status, xhr);
			self.use_local_storage ? self.local_storage.remap_jqGrid_columns() : undefined;

			if ($(this).getGridParam('search') === true)
				$('#clearFilters_' + this.id + '_top').addClass('ui-state-hover-filter');
			else
				$('#clearFilters_' + this.id + '_top').removeClass('ui-state-hover-filter');

		},
		resizeStop : function(newwidth, index) {
			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('resizeStop'))
				self.events.resizeStop.call(this, newwidth, index);

			self.use_local_storage ? self.local_storage.remap_jqGrid_columns_width(newwidth, index) : undefined;
		},
		afterEditCell : function(rowid, cellname, value, iRow, iCol) {
			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('afterEditCell'))
				self.events.afterEditCell.call(this, rowid, cellname, value, iRow, iCol);

			self.cellEdit_timed_restore(rowid, cellname, value, iRow, iCol);
		},
		afterSubmitCell : function(serverresponse, rowid, cellname, value, iRow, iCol) {
			/*
			if ((serverresponse.responseText).length > 0) {
				if (this.p.colModel[iCol].edittype === 'select')
					value = $("td:eq(" + iCol + ")", this.rows[iRow]).find('input').data('uiAutocomplete').selectedItem.value
				var dialog_text = '<span style="color:red;font-size:13px;font-weight:bold">Значение "' + value + '" не установлено в поле "' + this.p.colNames[iCol] + '".</span></br>'

				dialog_text += serverresponse.responseText;
				$(this).block({message: null,overlayCSS:{cursor:'default'},baseZ:100});
				return [false, dialog_text];
			} else
				return [true, ''];
			*/
			return [true, ''];
		},
		beforeEditCell : function(rowid, cellname, value, iRow, iCol) {
			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('beforeEditCell'))
				self.events.beforeEditCell.call(this, rowid, cellname, value, iRow, iCol);

			self.cell_row = this.p.prev_iRow = iRow;
			self.cell_col = this.p.prev_iCol = iCol;
		},
		gridComplete : function() {

			if (self.grid_first_load === true) {
				self.context_menu();
				self.adjust_bottom_pager();
			}
			self.grid_p = this.p;
			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('gridComplete'))
				self.events.gridComplete.call(this);
		},
		loadComplete : function(data) {
			this.p.gridProto = self;
			// insert bulk data
			if(data.hasOwnProperty('rows') && this.p.treeGrid !== true)
			{
				for(var i = 0;i < data.rows.length;i++)
				{
					this.p.data[data.rows[i].id] = data.rows[i];
				}
			}

			$("#lui_"+this.id).hide();

			if (self.hasOwnProperty('expandedGrid') && self.expandedGrid.length > 0) {
				for (var i = 0; i < self.expandedGrid.length; i++) {
					$(this).expandSubGridRow(self.expandedGrid[i]);
				}
			}
			if(this.p.footerrow)
				$(this).footerData("set", self.get_footer_data());

			if (self.hasOwnProperty('events') && self.events.hasOwnProperty('loadComplete'))
				self.events.loadComplete.call(this, data);

			if (self.gridToForm === true) {
				if ( typeof data.rows !== typeof undefined && data.rows.length === 1) {
					var m_rowid = data.rows[0]['Код'];
					$('.blockMe').unblock({ fadeOut : 0 });
					$.each($('.gridToForm'),function(i,n){
						$(':input[type="hidden"]',this).not(':checkbox, :submit').val('');
						this.reset();
					});
					$(this).jqGrid('GridToForm', m_rowid, ".gridToForm");
					$('select', '.gridToForm').trigger("change", true);
					if($.isFunction(self.gridToFormAfterInsert))
						self.gridToFormAfterInsert.call(this,data);
				} else {
					$('.blockMe').block({ message : null,overlayCSS : { cursor : 'default' },baseZ : 100 });
					$('.gridToForm').trigger('reset');
					$('select', '.gridToForm').trigger("change", true);
				}

			}
			self.grid_first_load = false;
		}
	})
	this.bind_handlers();
	if (this.filterToolbar === true)
		this.grid_element.jqGrid('filterToolbar', {
			stringResult : true,
			autosearch : true,
			searchOnEnter : false,
			defaultSearch : 'bw'
		});
	if (this.navGrid === true)
	{
		this.create_nav();
		if ( typeof this.permFilterButtons !== typeof undefined)
			this.filter_button();
		if ( typeof this.customButtons !== typeof undefined)
			this.add_buttons();
		this.adjust_nav();
	} else
		$('#' + this.name + '_toppager').remove();

}
jqGrid$.prototype.cellEdit_timed_restore = function(rowid, cellname, value, iRow, iCol)
{
	var self = this;
	var $cell = $('.edit-cell');
	var cell_props = this.grid_element_pure.p.colModel[self.cell_col];
	var cell_dim = new Object($('.edit-cell').offset());
	var saved_mouse_pos = new Object();
	var current_value = value;
	var event_function = function(mouse_pos)
	{
		if(mouse_pos.type === 'keyup')
			current_value = $('#'+iRow+'_'+cellname).val();
		if(!mouse_pos.hasOwnProperty('clientY') && !mouse_pos.hasOwnProperty('clientX'))
			mouse_pos = saved_mouse_pos;

		var timeout = 30000;
		var mouseY = mouse_pos.clientY;
		var mouseX = mouse_pos.clientX
		var restore = false;
		var interval_observer = function()
		{
			if(mouse_pos.clientY <= cell_dim.top)
				restore = true;
			else if(mouse_pos.clientX <= cell_dim.left)
				restore = true;
			else if(mouse_pos.clientY >= cell_dim.top + cell_dim.height)
				restore = true;
			else if(mouse_pos.clientX >= cell_dim.left + cell_dim.width)
				restore = true;
			else
			{
				if(timeout === 30000)
				{
					timeout += 30000; // = 10s
					mouseY = mouse_pos.clientY;
					mouseX = mouse_pos.clientX;
					clearInterval(self.cellEdit_timer);
					self.cellEdit_timer = setInterval(interval_observer,timeout);
					return;
				}
				else
				{
					if(mouseY === mouse_pos.clientY && mouseX === mouse_pos.clientX)
						restore = true;
					else
					{
						mouseY = mouse_pos.clientY;
						mouseX = mouse_pos.clientX;
						clearInterval(self.cellEdit_timer);
						self.cellEdit_timer = setInterval(interval_observer,timeout);
						return;
					}
				}
			}
			if(restore === true)
			{
				if(value !== current_value)
				{
					if(cell_props.formatter === 'date' && cell_props.formatoptions.newformat === 'd.m.Y')
					{
						if(/\d{2}\.\d{2}\.\d{4}/.test(current_value))
							self.grid_element.saveCell(self.cell_row,self.cell_col);
						else
							self.grid_element.restoreCell(self.cell_row,self.cell_col);
					}
					else if(cell_props.formatter === 'select')
						self.grid_element.restoreCell(self.cell_row,self.cell_col);
					else
						self.grid_element.saveCell(self.cell_row,self.cell_col);
				}
				else if(value === current_value)
					self.grid_element.restoreCell(self.cell_row,self.cell_col);
			}

		};

		clearInterval(self.cellEdit_timer);
		cell_dim.width = $cell.outerWidth();
		cell_dim.height = $cell.outerHeight();
		if(cell_props.formatter === 'date')
		{
			cell_dim.width += 107;
			cell_dim.height += 221;
		}
		else if(cell_props.formatter === 'select' && typeof $cell.find('ul').attr('hidden') === typeof undefined)
			cell_dim.height += $cell.find('ul').outerHeight();

		saved_mouse_pos = mouse_pos;
		self.cellEdit_timer = setInterval(interval_observer,timeout);
	};

	this.grid_element.parents('.ui-jqgrid-view').unbind('mousemove');

	$cell.bind('keyup',event_function);
	if(typeof self.cellEdit_timer === typeof undefined)
	{
		this.grid_element.parents('.ui-jqgrid-view').bind('mousemove',function(event){
			event_function({clientY:event.clientY,clientX:event.clientX});
		});
	}
	else
	{
		clearInterval(self.cellEdit_timer);
		self.cellEdit_timer = undefined;
		self.cellEdit_timed_restore(rowid, cellname, value, iRow, iCol)
	}
}
jqGrid$.prototype.bind_handlers = function() {
	var self = this;
	this.grid_element.bind('jqGridAfterLoadComplete', function(e, data) {
		var rows,
		    add = false,
		    type = e.type;
		if (data.hasOwnProperty('rows'))
			rows = data.rows
		else if ($.isArray(data))
			rows = data
		else
			rows = [];

		if (self.singleBlank === true && rows.length === 0)
			add = true
		else if (self.singleBlank !== true && self.hasOwnProperty('events') && self.events.hasOwnProperty('onCellSelect') && self.addBlank !== false)
			add = true
		else
			add = false;

		// add is true and blank is not found
		if (add && $('#blank', this).length === 0)
			$(this).jqGrid('addRowData', 'blank', {});
		// add is false and blank is found
		else if (!add && $('#blank', this).length !== 0) {
			$(this).restoreRow('blank');
			$('#blank', this).remove();
		}

		$("#jqg_" + self.name + "_blank").remove();
		$('#blank', this).css({}).children('td.sgcollapsed').unbind('click').html('');
	}).bind('jqGridInlineAfterSaveRow jqGridAddEditAfterComplete jqGridDelAfterComplete jqGridAfterSaveCell', function(e) {
		$(this).triggerHandler("jqGridAfterLoadComplete", [$(this).getRowData()]);
		self.grid_element.parents('.ui-jqgrid-view').unbind('mousemove');
		if(typeof self.cellEdit_timer !== typeof undefined)
		{
			clearInterval(self.cellEdit_timer);
			self.cellEdit_timer = undefined;
		}
		if (self.hasOwnProperty('refreshMainOnChange'))
			self.grid_element.trigger("reloadGrid",{current:true});
	}).bind('jqGridAfterRestoreCell',function(e){
		self.grid_element.parents('.ui-jqgrid-view').unbind('mousemove');
		if(typeof self.cellEdit_timer !== typeof undefined)
		{
			clearInterval(self.cellEdit_timer);
			self.cellEdit_timer = undefined;
		}
	});
}
jqGrid$.prototype.create_nav = function() {
	var toppager_selector = '#' + this.name + '_toppager';
	var self = this;
	this.toppager_element = $(toppager_selector);
	this.headbox_element = $(toppager_selector).next('.ui-state-default');
	this.grid_element.navGrid(toppager_selector, this.nav_ops,
		this.hasOwnProperty('editFormOptions') ? this.editFormOptions : {}, // edit
		this.hasOwnProperty('addFormOptions') ? this.addFormOptions : {}, // add
		this.hasOwnProperty('delFormOptions') ? this.delFormOptions : {}, // del
		this.hasOwnProperty('searchFormOptions') ? this.searchFormOptions : {} // search
	);
	if (this.main === true)
	{
		if(document.body.contains(document.getElementById('ex_import_'+this.name)) === false)
		{
			if(this.no_excel != true)
			{
				this.grid_element.navGrid().navButtonAdd(toppager_selector, {
					id : 'ex_import_' + this.name,
					caption : 'Экспорт в Excel',
					buttonicon : 'fa fa-file-excel-o',
					onClickButton : function(e) {
						self.excel_import();
					}
				});
			}
		}
		if(document.body.contains(document.getElementById('misc_'+this.name)) === false)
		{
			if(this.no_etc != true)
			{
				this.grid_element.navGrid().navButtonAdd(toppager_selector, {
					id : 'misc_' + this.name,
					caption : app.miscmenutext,
					buttonicon : 'fa fa-angle-down',
					menu : true
				});
			}
		}
		if(document.body.contains(document.getElementById('clearUsrData_'+this.name)) === false)
		{
			this.grid_element.navGrid().navButtonAdd(toppager_selector, {
				id : 'clearUsrData_' + this.name,
				caption : app.reloadSession,
				buttonicon : 'fa fa-retweet',
				append : 'misc_' + this.name,
				onClickButton : function(e) {
					$.ajaxShort({
						data : {
							action : 'kill_sess'
						},
						success : function() {
							self.local_storage.remove_Object_from_local_storage(true);
						}
					});
				}
			});
		}
		if(document.body.contains(document.getElementById('clearSort_'+this.name)) === false)
		{
			if(this.no_sort != true)
			{
				this.grid_element.navGrid().navButtonAdd(toppager_selector, {
					id : 'clearSort_' + this.name,
					caption : app.clearsorttext,
					buttonicon : 'fa fa-trash',
					append : 'misc_' + this.name,
					onClickButton : function(e) {
						self.remove_sorting();
					}
				});
			}
		}
	}
	if (this.use_local_storage)
	{
		if(document.body.contains(document.getElementById('clearGridLs_'+this.name)) === false)
		{
			this.grid_element.navGrid().navButtonAdd(toppager_selector, {
				id : 'clearGridLs_' + this.name,
				caption : app.clearglstext,
				buttonicon : 'fa fa-trash',
				append : 'misc_' + this.name,
				onClickButton : function(e) {
					self.local_storage.remove_Object_from_local_storage(true);
				}
			});
		}
		if(document.body.contains(document.getElementById('rewampColumns_'+this.name)) === false)
		{
			this.grid_element.navGrid().navButtonAdd(toppager_selector, {
				id : 'rewampColumns_' + this.name,
				caption : app.rewampcoltext,
				buttonicon : 'fa fa-eye',
				onClickButton : function(e) {
					var t = this;
					$(this).jqGrid('columnChooser', {
						done : function(perm) {
							if (perm) {
								$(t).jqGrid("remapColumns", perm, true);
								self.local_storage.build_jqGrid_local_storage();
								if(t.p.shrinkToFit === true)
								{
									self.local_storage;
									self.grid_element.GridUnload(self.name);
									self.recreate_jqGrid();
								}
							}
						},
						width : 500,
						height : 500
					});
				}
			});
		}
	}
	if (this.goToBlank === true && typeof this.onCellSelect !== typeof undefined) {
		this.grid_element.navGrid().navButtonAdd(toppager_selector, {
			id: 'goToBlank_' + this.name,
			caption: '&nbsp',
			buttonicon: 'fa fa-level-down',
			onClickButton: function (e) {
				if (this.p.prev_iRow && this.p.prev_iCol)
					$(this).restoreCell(this.p.prev_iRow, this.p.prev_iCol);

				$(this).setGridParam({
					cellEdit: false
				});

				$('#blank', this).find('td:visible').first()[0].scrollIntoView();
				$('#blank', this).find('td:visible').first().trigger('click');
			},
			onMouseDown: function (e) {
				$('#blank', this).find('input,textarea').off('blur');
			}
		});
		$('#goToBlank_' + this.name).find('div').css({
			'height': '18px'
		});
		$('#goToBlank_' + this.name).find('span').css({
			'font-size': '14px'
		})
		$('#goToBlank_' + this.name).appendTo('#' + this.name + '_p_left');
	}
	$('#rewampColumns_' + this.name).appendTo('#' + this.name + '_toppager_right');
	$('#ex_import_' + this.name).appendTo('#' + this.name + '_toppager_right');
	$('#refresh_' + this.name + '_top').appendTo('#' + this.name + '_toppager_right');
	$('#misc_' + this.name).appendTo('#' + this.name + '_toppager_right');
	$('#pg_' + this.name + '_toppager').css('border', '0px');
}

jqGrid$.prototype.adjust_nav = function() {
	var self = this;
	var overflow = checkOverflow(this.toppager_element[0]);
	var pager_parts = $('#' + this.toppager_element[0].id + ' > div > table > tbody > tr > td:visible');
	var overflow_parts,
	    overflow_button,
	    buttons_test = {};

	function minimize(object) {
		if (Object.keys(object).length > 0)
		{
			var next_self = $(object.el).siblings().andSelf();
			//var next_self = $(object.el).siblings().andSelf().not('#misc_' + this.id + ',#refresh_' + this.id + '_top'), /// SHIT
			var this_li;
			var menu_ul;
			$(this).navGrid().navButtonAdd(self.toppager_element[0], {
				id : 'minimize_' + this.id + '_' + object.of.id,
				caption : '&nbsp;',
				buttonicon : 'fa fa-bars',
				menu : true
			})
			$('#minimize_' + this.id + '_' + object.of.id).appendTo(object.of);
			menu_ul = $('#minimize_' + this.id + '_' + object.of.id).find('ul');
			$.each(next_self, function(i, v)
			{
				if($(this).find('.ui-jqgrid-nav-menu-container').length > 0)
				{
					$.each($(this).find('li'),function(i,el){
						$(this).appendTo(menu_ul);
					});
				}
				else
				{
					$(this).find('span')
						.removeClass('ui-icon')
						.attr('style', 'float:left !important;padding:2px;')
						.wrapAll('<li id="' + this.id + '" class="ui-corner-all" style="width:47px"></li>');
					this_li = $(this).find('li');

					this_li.hover(function()
					{
						if (!$(this).hasClass('ui-state-disabled'))
							$(this).addClass('ui-state-hover');
					}, function()
					{
						$(this).removeClass("ui-state-hover");
					});

					$(this).copyEventTo('click', this_li, true);
					this_li.bind('click', function(e)
					{
						$(this).parent().hide();
					});
					this_li.appendTo(menu_ul);
				}
				$(this).remove();
			});
		}
	}
	if (overflow) {
		$.each(pager_parts, function(i, v) {
			overflow_parts = checkOverflow(v);
			if (overflow_parts) {
				$.each($(v).find('td:visible'), function(ind, val) {
					overflow_button = checkOverflow(val, v, 'horizontal');
					if (overflow_button) {
						buttons_test.el = val;
						buttons_test.of = v;
						minimize.call(self.grid_element_pure, buttons_test)
						return false;
					}
				})
			}
		})
	}
	if(this.minimize_left_pager === true)
	{
		$.each($(pager_parts[0]).find('td:visible'), function(ind, val) {
			buttons_test.el = val;
			buttons_test.of = pager_parts[0];
			minimize.call(self.grid_element_pure, buttons_test)
			return false;
		});
	}
	if(this.minimize_right_pager === true)
	{
		$.each($(pager_parts[1]).find('td:visible'), function(ind, val) {
			buttons_test.el = val;
			buttons_test.of = pager_parts[1];
			minimize.call(self.grid_element_pure, buttons_test)
			return false;
		});
	}
}
jqGrid$.prototype.adjust_bottom_pager = function() {
	if (this.options.autowidth != true && this.subgrid != true ) {
		var grid_container = this.grid_element.parents('.ui-jqgrid-view');
		var grid_container_wd = grid_container[0].offsetWidth


		var $left = $('#' + this.name + '_p_left');
		var $center = $('#' + this.name + '_p_center');
		var $center_paginator = $center.children('table');
		var $right = $('#' + this.name + '_p_right');
		var min_$right_wd = $right.children().width() + 20;
		$right.removeAttr('align');

		$left.width($left.children().width());
		$right.width(min_$right_wd);

		grid_container_wd = grid_container_wd - $left.children().width() - $right.width();

		$center.removeAttr('align');
		$center.width(grid_container_wd);
		$center_paginator.css('left', (($center.parents('table').width() + $left.width() + $right.width()) / 2) - $center_paginator.width());
	}
}
jqGrid$.prototype.get_footer_data = function() {
	var object = new Object;
	for (var i = 0; i < this.footer.length; i++) {
		if(typeof this.footer[i].calc === 'object')
		{
			var last_col = Object.keys(this.footer[i].calc);
			last_col = last_col[last_col.length - 1];
			object[this.footer[i].col] = '';
			for(col in this.footer[i].calc)
			{
				// if column not exists, take value from col prop
				if(this.grid_element.getCol(col,false).length == 0)
					object[this.footer[i].col] = this.footer[i].calc[col]
				else
					object[this.footer[i].col] = object[this.footer[i].col] + this.grid_element.getCol(col,false,this.footer[i].calc[col]);

				if(col !== last_col)
					object[this.footer[i].col] = object[this.footer[i].col] + this.footer[i].self_calc;
			}
			object[this.footer[i].col] = eval(object[this.footer[i].col]);
			object[this.footer[i].col] = object[this.footer[i].col].toFixed(2);
		}
		else if(this.footer[i].calc instanceof Function)
		{
			object[this.footer[i].col] = this.footer[i].calc.call(this,this.grid_element.getCol(this.footer[i].col,true))
		}
		else
		{
			object[this.footer[i].col] = this.grid_element.getCol(this.footer[i].col,false,this.footer[i].calc);
			object[this.footer[i].col] = Number(object[this.footer[i].col]).toFixed(2);
		}
		if(this.footer[i].hasOwnProperty('postfix'))
			object[this.footer[i].col] = object[this.footer[i].col] + ' ' + this.footer[i].postfix;
		if(this.footer[i].hasOwnProperty('prefix'))
			object[this.footer[i].col] = this.footer[i].prefix + ' ' + object[this.footer[i].col];
	}
	return object;
}
jqGrid$.prototype.filter_button = function()
{
	var options,
	    button_opts,
	    icon,
	    caption;
	var self = this,event_obj;

	function smart_filters_rules(op, op_)
	{
		if (op === 'eq' && op_ === 'ne')
			return false
		else if (op === 'ne' && op_ === 'eq')
			return false
		else if ((op === 'gt' || op === 'lt' || op === 'ge' || op === 'le') && (op_ === 'gt' || op_ === 'lt' || op_ === 'ge' || op_ === 'le'))
			return false
		return true;
	}
	function filter(e, init_f_object, f_object, index, proceed)
	{
		var $t_pDiv = this.grid.topDiv;
		var button = e.currentTarget;
		if ($(button).hasClass('ui-state-hover-filter'))
		{
			if ( typeof this.p.savedPermFilter !== typeof undefined)
			{
				for (var i = 0; i < this.p.savedPermFilter.length; i++)
				{
					if (this.p.savedPermFilter[i].field === f_object.field && smart_filters_rules(this.p.savedPermFilter[i], f_object.op) === true)
					{
						init_f_object.rules.push(this.p.savedPermFilter[i]);
						this.p.savedPermFilter.splice(i, 1);
					}
				}
			}
			if ( typeof index !== typeof undefined)
				init_f_object.rules.splice(index, 1);
		}
		else
		{
			if ( typeof index !== typeof undefined)
			{
				if ( typeof this.p.savedPermFilter === typeof undefined)
					this.p.savedPermFilter = [init_f_object.rules[index]];
				else
					this.p.savedPermFilter.push(init_f_object.rules[index]);

				init_f_object.rules.splice(index, 1);
			}
			init_f_object.rules.push(f_object);
		}
		this.p.postData.perm_filters = JSON.stringify(init_f_object);

		if (proceed)
		{
			if(event_obj != false)
				self.grid_element.trigger("reloadGrid",{current:true});
			$(button).toggleClass('ui-state-hover-filter');
			$(button).find('.fa').toggleClass('fa-unlock-alt fa-lock')
		}
	}

	function prep_filter(e, init_f_object, filterObj)
	{
		var spliceIndex;
		var isArray = filterObj instanceof Array ? true : false;
		if (!isArray)
		{
			for (var i = 0; i < init_f_object.rules.length; i++)
			{
				if (init_f_object.rules[i].field instanceof Array && init_f_object.rules[i].field.equals(filterObj.field) === true)
				{
					spliceIndex = i;
					break;
				}
				else if (init_f_object.rules[i].field === filterObj.field && smart_filters_rules(init_f_object.rules[i].op, filterObj.op) === false)
				{
					spliceIndex = i;
					break;
				}
				else if (init_f_object.rules[i].field === filterObj.field && init_f_object.rules[i].op === filterObj.op && init_f_object.rules[i].data === filterObj.data)
				{
					spliceIndex = i;
					break;
				}
			}
			filter.call(this, e, init_f_object, filterObj, spliceIndex, true);
		}
		else
		{
			for (var i = 0; i < filterObj.length; i++)
			{
				for (var y = 0; y < init_f_object.rules.length; y++)
				{
					if (init_f_object.rules[y].field === filterObj[i].field)
					{
						spliceIndex = y;
						break;
					}
				}
				if (i != (filterObj.length - 1))
					filter.call(this, e, init_f_object, filterObj[i], spliceIndex, false);
				else
					filter.call(this, e, init_f_object, filterObj[i], spliceIndex, true);
			}
		}
	}
	function callBack(filterObj)
	{
		return function(e,trigger)
		{
			event_obj = trigger;
			var permFilter = typeof this.p.postData.perm_filters !== typeof undefined ? JSON.parse(this.p.postData.perm_filters) : undefined;

			if ( typeof permFilter !== typeof undefined)
				prep_filter.call(this, e, permFilter, filterObj);
			else
			{
				permFilter = {
					groupOp : "AND",
					rules : []
				};
				prep_filter.call(this, e, permFilter, filterObj);
			}
		}
	}
	for (var i = 0; i < this.permFilterButtons.length; i++)
	{
		options = this.permFilterButtons[i];
		if (!options.hasOwnProperty('data'))
			return $.alert('No filter data is set');

		icon = typeof options.buttonicon === typeof undefined ? "fa fa-unlock-alt" : options.buttonicon;
		caption = typeof options.caption === typeof undefined ? "&nbsp" : options.caption;
		button_opts = new Object();
		$.extend(button_opts, {
			caption : caption,
			buttonicon : icon,
			onClickButton : callBack(options.data)
		});
		this.grid_element.navGrid().navButtonAdd(this.toppager_element[0].id, button_opts);
	}
}
jqGrid$.prototype.add_buttons = function() {

	if ($.isArray(this.customButtons)) {
		var props;
		for (var i = 0; i < this.customButtons.length; i++) {
			props = new Object();
			props.caption = typeof this.customButtons[i].caption !== typeof undefined ? this.customButtons[i].caption : '&nbsp';
			props.buttonicon = typeof this.customButtons[i].icon !== typeof undefined ? this.customButtons[i].icon : 'fa fa-cog';
			props.onClickButton = this.customButtons[i].click;
			this.grid_element.navGrid().navButtonAdd(this.toppager_element[0].id, props)
		}
	}
}
jqGrid$.prototype.context_menu = function() {
	var self = this;
	var isFirefox = typeof InstallTrigger !== 'undefined';
	if(this.device == true)
	{
		$.extend($.contextMenu.handle,{
			taphold_contextmenu:$.proxy($.contextMenu.handle.contextmenu)
		})
	}
	$.contextMenu({
		selector : '#' + this.grid_element_pure.id,
		animation : {
			duration : 25,
			show : 'fadeIn',
			hide : 'fadeOut'
		},
		zIndex:5,
		reposition : false,
		build : function($trigger, e)
		{
			if (($trigger)[0].p.prev_iRow && ($trigger)[0].p.prev_iCol)
			{
				var test = {iRow: $(e.target).parents('tr')[0].rowIndex,iCol: $.jgrid.getCellIndex($(e.target))};
				if(test.iRow == ($trigger)[0].p.prev_iRow && test.iCol == ($trigger)[0].p.prev_iCol)
					e.target = $(e.target).parents('td')[0];
				$($trigger).restoreCell(($trigger)[0].p.prev_iRow, ($trigger)[0].p.prev_iCol);
			}

			var target = e.target;
			var rowid = $(target).parents('tr')[0].id;
			if(!rowid)
				return false;
			var isBlank = rowid === 'blank' ? true : false;
			var grid_params = $($trigger)[0].p;
			var target_grid_value,
			    target_grid_value_pure,
			    target_grid_colname,
			    target_grid_col_options;
			var iRow = $(target).parents('tr')[0].rowIndex;
			var iCol = $.jgrid.getCellIndex(target);

			var allowPaste = false;
			var col_type;

			var searchIcon = function() {
				return "context-menu-icon-fa fa-search";
			};
			var raw_row_data = $trigger.getRowDataRaw(rowid);
			var row_data = $trigger.getRowData(rowid);

			target.id = 'context_target';

			if (target.tagName === 'TD') {
				// if target TD have checkbox get checkbox value, instead of TD-s text
				if ($(target).find('input[type="checkbox"]').length > 0)
					target_grid_value = target_grid_value_pure = $(target).find('input[type="checkbox"]').val();
				else
					target_grid_value = target_grid_value_pure = $(target).text();
				// column name defined as TD-s attr 'aria-describedby'
				target_grid_colname = $(target).attr('aria-describedby').replace(self.grid_element_pure.id + '_', '');
			} else {
				target_grid_value = target_grid_value_pure = $(target).val();
				target_grid_colname = $(target).attr('name');
			}

			target_grid_col_options = grid_params.colModel[iCol];
			var cellid = '#' + iRow + '_' + target_grid_colname;
			// add dots and cut if length > 20
			if (target_grid_value.length > 20)
				target_grid_value = target_grid_value.substring(0, 17) + '...';
			// replace empty value with "Пустые"
			if (target_grid_value.length === 0 || (target_grid_value.trim()).length === 0)
				target_grid_value = 'Пусто';
			var isCb = target_grid_col_options.edittype === 'checkbox' ? true : false;
			if (!target_grid_col_options.formatter)
			{
				if (!isNaN(target_grid_value_pure))
					col_type = 'integer';
				else
					col_type = 'text';
			}
			else
			{
				if (target_grid_col_options.formatter === 'date')
					col_type = 'date';
				else if (target_grid_col_options.edittype === 'textarea')
					col_type = 'text';
				else if (!isNaN(target_grid_value_pure))
					col_type = 'integer';
				else
					col_type = target_grid_col_options.formatter;
			}
			if (grid_params.copyData instanceof Object) {
				if (isBlank === false && isCb === false) {
					if (grid_params.copyData.type === col_type)
						allowPaste = true;

					if (allowPaste === true && grid_params.copyData.type === 'select') {
						var sel_value = target_grid_col_options.editoptions.value.split(';'),
						    sel_values,
						    found = false;
						for (var i = 0; i < sel_value.length; i++) {
							sel_values = sel_value[i].split(':');
							if (sel_values[1] === $($trigger)[0].p.copyData.val) {
								$($trigger)[0].p.copyData.val = {
									value : sel_values[0],
									name : sel_values[1]
								};
								found = true;
								break;
							}
						}
						if (!found)
							allowPaste = false;
					}
					if (col_type === 'text')
						allowPaste = true;
				}
			}
			var custom_search_items;
			if (col_type === 'text') {
				custom_search_items = {
					grid_search_bw : {
						name : "Содержит: " + target_grid_value,
						so : 'bw',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_bn : {
						name : "Не содержит: " + target_grid_value,
						so : 'bn',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_cn : {
						name : "Начинается с: " + target_grid_value,
						so : 'cn',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_nc : {
						name : "Не начинается с: " + target_grid_value,
						so : 'nc',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					}
				}
			} else if (col_type === 'date') {
				custom_search_items = {
					grid_search_date_ge : {
						name : "Больше чем: " + target_grid_value,
						so : 'dateGe',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_date_le : {
						name : "Меньше чем: " + target_grid_value,
						so : 'dateLe',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					}
				}
			} else if (col_type === 'integer') {
				custom_search_items = {
					grid_search_gt : {
						name : "Больше чем: " + target_grid_value,
						so : 'gt',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_ge : {
						name : "Равно и больше чем: " + target_grid_value,
						so : 'ge',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_lt : {
						name : "Меньше чем: " + target_grid_value,
						so : 'lt',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
					grid_search_le : {
						name : "Равно и меньше чем: " + target_grid_value,
						so : 'le',
						icon : searchIcon,
						disabled : !isBlank && target_grid_col_options.search ? false : true
					},
				}
			};
			var menu_items = {
				copy : {
					name : "Копировать",
					icon : "copy",
					disabled : !isBlank ? isFirefox ? true : false : true
				},
				copy_row : {
					name : "Копировать строку",
					icon : "copy",
					disabled : !isBlank ? isFirefox ? true : false : true
				},
				sep : "---------",
				grid_cut : {
					name : "Вырезать",
					icon : "cut",
					disabled : !isBlank && !isCb ? grid_params.cellEdit === true ? false : true : true
				},
				grid_copy : {
					name : "Копировать",
					icon : "copy",
					disabled : !isBlank && !isCb ? grid_params.cellEdit === true ? false : true : true
				},
				grid_paste : {
					name : "Вставить",
					icon : "paste",
					disabled : allowPaste ? false : true
				},
				grid_delete : {
					name : "Удалить",
					icon : "delete",
					disabled : !isBlank ? false : true,
					visible: self.hasOwnProperty('delFormOptions') || self.delOpts == true ? true : false
				},
				sep1 : "---------",
				grid_empty_select:{
					name:'-|-',
					icon:function(opt, $itemElement, itemKey, item){
						return 'context-menu-icon-fa fa-times ';
					},
					disabled: true,
					visible: col_type === 'select' && target_grid_value === 'Пусто' ? true : false
				},
				grid_empty:{
					name:'Равно: Пустые',
					so:'isNull',
					icon : searchIcon,
					disabled : !isBlank && self.filterToolbar && target_grid_col_options.search ? false : true,
					visible : col_type === 'select' ? false : true
				},
				grid_non_empty:{
					name:'Равно: Не пустые',
					so:'isNotNull',
					icon : searchIcon,
					disabled : !isBlank && self.filterToolbar && target_grid_col_options.search ? false : true,
					visible : col_type === 'select' ? false : true
				},
				grid_search_eq : {
					name : "Равно: " + target_grid_value,
					so : 'eq',
					icon : searchIcon,
					visible : target_grid_value != 'Пусто' ? true : false,
					disabled : !isBlank && self.filterToolbar && target_grid_col_options.search ? false : true
				},
				grid_search_ne : {
					name : "Не равно: " + target_grid_value,
					so : 'ne',
					icon : searchIcon,
					visible : target_grid_value != 'Пусто' ? true : false,
					disabled : !isBlank && self.filterToolbar && target_grid_col_options.search ? false : true
				},
				grid_search_custom : {
					name : "Поиск...",
					items : custom_search_items,
					visible : !self.filterToolbar || col_type === 'select' || target_grid_value == 'Пусто' || typeof custom_search_items === typeof undefined ? false : true
				},
				sep3 : "---------",
				grid_misc : {
					name : "Сортировка",
					icon:function(opt, $itemElement, itemKey, item){
						return 'context-menu-icon-fa fa-sort';
					},
					items : {
						grid_sort_asc : {
							disabled:target_grid_col_options.sortable === false ? true : false,
							name : "Сортировка от А до Я",
							icon : function(opt, $itemElement, itemKey, item) {
								return 'context-menu-icon-fa fa-sort-amount-asc'
							}
						},
						grid_sort_desc : {
							disabled:target_grid_col_options.sortable === false ? true : false,
							name : "Сортировка от Я до А",
							icon : function(opt, $itemElement, itemKey, item) {
								return 'context-menu-icon-fa fa-sort-amount-desc'
							}
						}
					}
				}
			};
			if(self.hasOwnProperty('navGridOptions') && self.navGridOptions.edit == true)
			{
				$.extend(menu_items,{
					edit_row:{
						name:'Редактировать',
						icon: function(opt, $itemElement, itemKey, item)
						{
							return 'context-menu-icon-fa fa-pencil';
						},
						custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
						{
							$(this).editGridRow(rowid,gridPseudo.editFormOptions);
						}
					}
				});
			}
			if(self.contextMenuFileTree == true)
			{
				var fileTree;
				if(self.hasOwnProperty('contextMenuFileTreeItems'))
				{
					fileTree = {
						grid_fs: {
							name: 'Документы',
							icon : function(opt, $itemElement, itemKey, item)
							{
								return 'context-menu-icon-fa fa-folder-open'
							},
							items: {}
						}
					};

					if(self.contextMenuFileTreeItems.hasOwnProperty('default'))
					{
						if(self.contextMenuFileTreeItems.default === true)
						{
							$.extend(fileTree.grid_fs.items,{
								grid_fs:{
									name:'Все документы',
									icon : function(opt, $itemElement, itemKey, item) {
										return 'context-menu-icon-fa fa-folder-open'
									},
									custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
									{
										$.file_Tree({
											s_prm:{
												reference:self.location,
												tname:gridPseudo.hasOwnProperty('tableQuery') ? gridPseudo.tableQuery : gridPseudo.table,
												rowid:rowid
											},
											onClose:function(tree_container,tree_object)
											{
												if($.isFunction(self.fileTreeCallback))
													self.fileTreeCallback.call($trigger[0],tree_container,tree_object,rowid,raw_row_data,self,row_data);
											}
										});
									}
								}
							});
						}
					}
					for(var item in self.contextMenuFileTreeItems)
					{
						if(item === 'default')
							continue;
						var option = new Object();
						option[item] = self.contextMenuFileTreeItems[item];
						$.extend(fileTree.grid_fs.items,option);
					}
				}
				else
				{
					fileTree = {
						grid_fs:{
							name:'Файлы',
							icon : function(opt, $itemElement, itemKey, item) {
								return 'context-menu-icon-fa fa-folder-open'
							},
							custom_callback:function(e,rowid,val,cellName,options,rowObject,gridPseudo)
							{
								$.file_Tree({
									s_prm:{
										reference:self.location,
										tname:gridPseudo.hasOwnProperty('tableQuery') ? gridPseudo.tableQuery : gridPseudo.table,
										rowid:rowid
									},
									onClose:function(tree_container,tree_object)
									{
										if($.isFunction(self.fileTreeCallback))
											self.fileTreeCallback.call($trigger[0],tree_container,tree_object,rowid,raw_row_data,self,row_data);
									}
								});
							}
						}
					};
				}
				$.extend(menu_items,fileTree);
			}
			if (self.hasOwnProperty('contextMenuItems'))
			{
				if(Object.keys(self.contextMenuItems).length > 12)
				{
					self.contextMenuItems = {
						grid_actions: {
							name:'Разное',
							icon : function(opt, $itemElement, itemKey, item)
							{
								return 'context-menu-icon-fa fa-database'
							},
							items:self.contextMenuItems
						}
					}
					$.extend(menu_items, {sep5 : "---------"},self.contextMenuItems);
				}
				else
					$.extend(menu_items, {sep5 : "---------"},self.contextMenuItems);
			}
			return {
				events : {
					show : function(options)
					{
						if($('#' + rowid,this).hasClass('row-highlight'))
						{
							options.remove_highlight = false;
							return;
						}
						else
						{
							options.remove_highlight = true;
							$('.row-highlight',this).removeClass('row-highlight');
							if (rowid !== 'blank')
								$('#' + rowid,this).addClass('row-highlight');
						}

					},
					hide : function(options)
					{
						target.removeAttribute('id');
						if(options.remove_highlight == true)
							$('.row-highlight',this).removeClass('row-highlight');
					}
				},
				callback : function(key, options) {
					var t = this;
					switch(key)
					{
					case 'copy':
						selectInnerText(target.id)
						document.execCommand('Copy');
						window.getSelection().removeAllRanges();
						break;
					case 'copy_row':
						selectInnerText(rowid)
						document.execCommand('Copy');
						window.getSelection().removeAllRanges();
						break;
					case 'grid_cut':
						$(this)[0].p.copyData = {
							type : col_type,
							val : target_grid_value_pure
						};
						$(this).editCell(iRow, iCol, true);
						if (col_type === 'select')
							$(cellid).val('NULL');
						else
							$(cellid).val('');

						$(this).saveCell(iRow, iCol);
						break;
					case 'grid_copy':
						$(this)[0].p.copyData = {
							type : col_type,
							val : target_grid_value_pure
						};
						break;
					case 'grid_paste':
						$(this).editCell(iRow, iCol, true);

						if (col_type === 'select' && $(cellid)[0].hasAttribute('customcombobox')) {
							var option = document.createElement('option');
							option.value = $(this)[0].p.copyData.val.value;
							option.text = $(this)[0].p.copyData.val.name;
							$(cellid).append(option);
							$(cellid).val($(this)[0].p.copyData.val.value);
						} else
							$(cellid).val($(this)[0].p.copyData.val);

						$(this).saveCell(iRow, iCol);
						if ($('.ui-datepicker').length > 0)
							$('.ui-datepicker').remove();
						break;
					case 'grid_delete':
						var delObj = self.delFormOptions || self.delOpts;
						delObj instanceof Object ? undefined : delObj = {
							delData : {
								tid : self.id,
								//tname : (self.hasOwnProperty('tableQuery') ? self.tableQuery : self.table).replace("V_","")
								tname : self.hasOwnProperty('tableQuery') ? self.tableQuery : self.table
							}
						};
						if($(this)[0].p.hasOwnProperty('selarrrow') && $(this)[0].p.selarrrow.length > 0)
							$(this).delGridRow($(this)[0].p.selarrrow, delObj);
						else
							$(this).delGridRow(rowid, delObj);
						break;
					case 'grid_empty':
					case 'grid_non_empty':
						var oper = options.commands[key].so;
						/*console.log(oper);*/
						var tmp_sopt = $(this)[0].p.colModel[iCol].searchoptions.sopt;
						/*
						if(key == 'grid_empty')	{
							$('#gs_' + target_grid_colname).val('Пустые');
							/*console.log('kek1');
						}
						else {
							$('#gs_' + target_grid_colname).val('Не пустые');
							/*console.log('kek2');
						}
						*/

						$(this)[0].p.colModel[iCol].searchoptions.sopt = [oper];
						var pre = (key == 'grid_empty') ? '' : 'NOT ';

						$('#gs_' + target_grid_colname).append(optionCreate('IS '  + pre  + 'NULL:' + pre + 'Null'));
						$('#gs_' + target_grid_colname).val('IS ' + pre + 'NULL');

						$('#gs_' + target_grid_colname).trigger('change');
						/*$(this)[0].triggerToolbar();*/
						$(this)[0].p.colModel[iCol].searchoptions.sopt = tmp_sopt;
						/*$('#gs_' + target_grid_colname).attr('disabled', true);*/
						break;
					case 'grid_search_eq':
					case 'grid_search_ne':
						var oper = options.commands[key].so;
						var tmp_sopt = $(this)[0].p.colModel[iCol].searchoptions.sopt;
						if (target_grid_col_options.stype === 'select') {
							$(this)[0].p.colModel[iCol].searchoptions.sopt = [oper];
							if(target_grid_value_pure.trim().length == 0)
							{
								$(this)[0].p.colModel[iCol].searchoptions.sopt = ['isN'];
								$('#gs_' + target_grid_colname).append(optionCreate('IS NULL:Null'));
								$('#gs_' + target_grid_colname).val('IS NULL');
								$('#gs_' + target_grid_colname).trigger('change');
								$(this)[0].p.colModel[iCol].searchoptions.sopt = tmp_sopt;
								$('#gs_' + target_grid_colname).attr('disabled', true);
							}
							else
							{
								$.ajax({
									url : REQUEST_URL,
									type : 'POST',
									dataType : 'json',
									async : true,
									data : {
										oper : 'view_selects',
										search : target_grid_value_pure,
										info : JSON.stringify($('#gs_' + target_grid_colname).data('search'))
									},
									success : function(data) {
										$('#gs_' + target_grid_colname).append(optionCreate(data.results[0].id + ':' + data.results[0].text));
										$('#gs_' + target_grid_colname).val(data.results[0].id);
										$('#gs_' + target_grid_colname).trigger('change');
										$(t)[0].p.colModel[iCol].searchoptions.sopt = tmp_sopt;
										$('#gs_' + target_grid_colname).attr('disabled', true);
									}
								})
							}
						} else {
							if (col_type === 'date' && oper === 'eq')
								oper = 'dateEq';
							else if (col_type === 'date' && oper === 'ne')
								oper = 'dateNe';

							if (target_grid_value_pure.length === 0)
								$('#gs_' + target_grid_colname).val(null);
							else
								$('#gs_' + target_grid_colname).val(target_grid_value_pure);

							$(this)[0].p.colModel[iCol].searchoptions.sopt = [oper];
							$(this)[0].triggerToolbar();
							$(this)[0].p.colModel[iCol].searchoptions.sopt = tmp_sopt;
							$('#gs_' + target_grid_colname).attr('disabled', true);
						}
						break;
					case 'grid_search_bw':
					case 'grid_search_bn':
					case 'grid_search_cn':
					case 'grid_search_nc':
					case 'grid_search_date_ge':
					case 'grid_search_date_le':
					case 'grid_search_gt':
					case 'grid_search_ge':
					case 'grid_search_lt':
					case 'grid_search_le':
						var oper = options.commands[key].so;
						var tmp_sopt = $(this)[0].p.colModel[iCol].searchoptions.sopt;

						if (target_grid_value_pure.length === 0)
							$('#gs_' + target_grid_colname).val(null);
						else
							$('#gs_' + target_grid_colname).val(target_grid_value_pure);

						$(this)[0].p.colModel[iCol].searchoptions.sopt = [oper];
						$(this)[0].triggerToolbar();
						$(this)[0].p.colModel[iCol].searchoptions.sopt = tmp_sopt;
						$('#gs_' + target_grid_colname).attr('disabled', true);
						break;
					case 'grid_sort_asc':
						$(this).setGridParam({
							sortorder : 'ASC'
						});
						$(this).sortGrid(target_grid_colname, true);
						break;
					case 'grid_sort_desc':
						$(this).setGridParam({
							sortorder : 'DESC'
						});
						$(this).sortGrid(target_grid_colname, true);
						break;
					default:
						if (options.commands[key].hasOwnProperty('custom_callback'))
							options.commands[key].custom_callback.call(this, e, rowid, target_grid_value_pure, target_grid_colname, target_grid_col_options, raw_row_data,self,row_data)
						break;
					}
				},
				items : menu_items
			};
		}
	});
}
jqGrid$.prototype.plugin = function(source)
{
	if(!source.hasOwnProperty('url') && !source.hasOwnProperty('name') && !source.hasOwnProperty('frame_type'))
		return $.alert('Set url or name of plugin');
	var self = this;
	if(source.hasOwnProperty('frame_type'))
	{
		if(source.frame_type == 'email')
		{
			source.dialog_title = 'Отправка письма';
			source.url = '/templates/misc/mail_box/?reference=misc/mail_box';
			if(!source.hasOwnProperty('dialog_width'))
				source.dialog_width = 600;
			if(!source.hasOwnProperty('dialog_height'))
				source.dialog_height = 720;
			if(!source.hasOwnProperty('dialogClassName'))
                source.dialogClassName = 'email-dialog';
		}
	}
	if(!source.hasOwnProperty('id'))
		source.id = this.grid_p.selrow;
	if(!source.hasOwnProperty('data'))
		source.data = this.grid_element.getRowDataRaw(this.grid_p.selrow);
	if(!source.hasOwnProperty('url'))
		source.url = PLUGINS_URL + '?' + 'p_name=' + source.name + '&reference=' + this.location;
	var className;
	if(source.hasOwnProperty('dialogClassName'))
		className = source.dialogClassName;
	else if((/(\/[0-z]*){4,}/).test(source.url))
		className = source.url.match(/([0-z]*)$/)[0];
	else if(source.hasOwnProperty('dialog_title'))
		className = source.dialog_title.substring(0,5).replace(/ /g,'');
	else
		className = 'no-class';

	var last_position;
	var prev_dialog = $('.'+className).find('.ui-dialog-content');
	var state;
	if($('.'+className).length > 0)
	{
		state = prev_dialog.dialogExtend("state");
		if(state == 'minimized')
			prev_dialog.dialogExtend('restore');
		last_position = $('.' + className).position();
		prev_dialog.dialog('close');
	}
	var src_get = "rowid="+ encodeURIComponent(source.id) +"&row_data="+ encodeURIComponent(JSON.stringify(source.data));
	var default_dialog_opts = {
		customButtons:undefined,
		dialog_opts:{
			draggable:false,
			dialogClass:className
		},
		dialog_extend_opts:{
			closable:true
		},
		title:source.dialog_title,
		html:function()
		{
			var iframe_container = document.createElement('div');
			iframe_container.style.height = '100%';
			iframe_container.style.width = '100%';
			iframe_container.style.position = 'relative';
			var iframe = document.createElement('iframe');
			iframe.align = "top";
			iframe.height = "100%";
			iframe.width = "99%";
			iframe.style.borderWidth = 0;
			iframe.name = 'current-iframe';
			iframe.src = source.url+ "&"+ src_get;
			iframe.onload = function()
			{
				if(source.frame_type == 'email')
					$('.overlay-loading').remove();
			};
			iframe_container.appendChild(iframe);
			if(source.frame_type == 'email')
			{
				var loader = document.createElement('div');
				loader.className = 'overlay-loading';
				loader.style.position = 'absolute';
				iframe_container.appendChild(loader);
			}
			return iframe_container;
		},
		width:source.dialog_width || 'auto',
		height:source.dialog_height || 'auto',
	};

	if(typeof last_position !== typeof undefined)
		default_dialog_opts.dialog_opts.position = {my: "left top", at: "left+"+last_position.left+" top+"+last_position.top, of: document}
	if(source.hasOwnProperty('dialog_refresh_grid'))
	{
		if(source.dialog_refresh_grid === true)
		{
			$.extend(true,default_dialog_opts,{
				dialog_opts:{
					close:function(){
						self.grid_element.trigger("reloadGrid",{current:true});
						$(this).remove();
					}
				}
			})
		}
		else if($.isFunction(source.dialog_refresh_grid))
			source.dialog_refresh_grid.call(this);
	}

	if(source.dialog_custom_opts)
		$.extend(true,default_dialog_opts, source.dialog_custom_opts);
	$.confirmHTML(default_dialog_opts);
}
jqGrid$.prototype.excel_import = function() {
	var self = this;
	var colModel = this.grid_element.getGridParam("colModel");
	var colNames = this.grid_element.getGridParam("colNames");

	var wrapper = $('<div><select style="width:460px;height:385px" multiple="multiple"></select></div>');
	var select = $('select', wrapper);
	var selected,
	    ls_data,
	    ls_data_search,
	    filters,
	    excel_obj = new Object();

	var ls_data = this.local_storage.data;

	$.each(colModel, function(i, v) {
		selected = new String;
		if (this.hidedlg)
			return;
		if (ls_data && ls_data.hasOwnProperty('importState')) {
			ls_data_search = $.grep(ls_data.importState, function(val, ind) {
				return val === v.name
			})
			if (ls_data_search.length > 0)
				selected = 'selected="selected"'
		}

		select.append("<option value='" + this.name + "' " + selected + ">" + $.jgrid.stripHtml(colNames[i]) + "</option>");
	});
	wrapper.dialog({
		modal : true,
		width : 500,
		height : 500,
		title : 'Выбор полей для импорта',
		buttons : {
			'Экспортировать' : function() {
				var dialog_inst = $(this).data('uiDialog');
				var flds = [],
				    filters,
				    options,
				    sel_this,
				    sel_data,
				    selected = new Array;

				$('option:selected', select).each(function(i) {
					sel_this = this.value;
					selected.push(sel_this);
					options = $.grep(colModel, function(index, value) {
						return colModel[value].name === sel_this;
					})
					if (options[0].formatter === 'select') {
						sel_data = $('#gs_' + options[0].name).data('search');
						if ( typeof sel_data !== typeof undefined)
							flds.push({name : '(SELECT ' + sel_data.sfld + ' FROM ' + sel_data.tname + ' WHERE ' + sel_data.refid + '=' + sel_data.ref_fld + ') as [' + this.text + ']'});
						else
							flds.push({name : this.value});
					}
					else if (options[0].formatter === 'date')
						flds.push({name : '(CONVERT(VARCHAR(10),' + this.value + ',104))as [' + this.text + ']'});
					else
						flds.push({name : '[' + this.value + '] as [' + this.text + ']'});
				})
				if (flds.length > 0) {
					if (dialog_inst._saved === true && ls_data) {
						ls_data.importState = selected;
						self.local_storage.data = ls_data;
						self.local_storage.save_Object_in_local_storage();
					}
					if (self.grid_element_pure.p.postData.hasOwnProperty('perm_filters'))
						filters = JSON.parse(self.grid_element_pure.p.postData.perm_filters);

					if (self.grid_element_pure.p.postData.hasOwnProperty('filters')) {
						if (filters)
							filters.rules.push.apply(filters.rules, (JSON.parse(self.grid_element_pure.p.postData.filters)).rules);
						else
							filters = JSON.parse(self.grid_element_pure.p.postData.filters);
					}
					excel_obj.reference = self.location
					excel_obj.type = 'excel';
					excel_obj.qry = { type : 'SELECT', tname : self.grid_element_pure.p.postData.tname, fields : flds};
					if(typeof filters !== typeof undefined)
						excel_obj.qw = filters;
					if (self.grid_element_pure.p.postData.hasOwnProperty('mainIdname') && self.grid_element_pure.p.postData.hasOwnProperty('mainId')) {
						excel_obj.qry.mainIdname = self.grid_element_pure.p.postData.mainIdname;
						excel_obj.qry.mainId = self.grid_element_pure.p.postData.mainId;
					}
					get_file_url(excel_obj,self.grid_element_pure.p.postData.tname);
				}
				else
					$.alert('Ничего не выбрано.');
				$(this).dialog('destroy');
			},
			'Сохранить выбранные поля' : this.use_local_storage === true ? function(e) {
				if (self.useLs === false)
					$(e.target).attr('disabled', true);

				var dialog_inst = $(this).data('uiDialog'),
				    this_button = e.currentTarget;

				$(this_button).toggleClass('ui-state-highlight').blur();

				if ($(this_button).hasClass('ui-state-highlight'))
					dialog_inst._saved = true;
				else
					dialog_inst._saved = false;

				$(this).data('uiDialog', dialog_inst);
			} : undefined,
			'Закрыть' : function() {
				$(this).dialog('destroy');
			}
		}
	})

	$(select).multiselect({
		animated : 'fast',
		dividerLocation : 0.5,
		hide : 'slideUp',
		searchable : true,
		show : "slideDown",
		sortable : false,
		nodeComparator : function(node1, node2) {
			var text1 = node1.text(),
			    text2 = node2.text();
			return text1 == text2 ? 0 : (text1 < text2 ? -1 : 1);
		}
	});
}
jqGrid$.prototype.remove_sorting = function(column, order)
{

	var col = typeof column !== typeof undefined ? column : this.id;
	var ord = typeof order !== typeof undefined ? order : this.tableSort;

	if ($('span[sort="asc"]', this.headbox_element).length > 0 && $('span[sort="desc"]', this.headbox_element).length > 0) {
		$('span[sort="asc"]', this.headbox_element).parent().css('display', 'none');
		$('span[sort="asc"]', this.headbox_element).remove()
		$('span[sort="desc"]', this.headbox_element).remove()
	}

	this.grid_element.setGridParam({
		sortorder : ord
	})
	this.grid_element.sortGrid(col, true);
}
jqGrid$.prototype.sorting = function()
{
	var column_options = this.grid_element.getColProp(this.grid_element_pure.p.postData.sidx),data = new Object();
	if(column_options.stype == 'select')
	{
		var search_data = JSON.parse(column_options.searchoptions.attr['data-search']);
		data.join_fld = 'Код';
		data.ref_fld = column_options.index;
		data.order_fld = 'Название';
		data.join_tname = search_data.tname;
		this.grid_element_pure.p.postData.sortQue = JSON.stringify(data);
	}
}
jqGrid$.prototype.navigator_opts = function(renew)
{
	if(typeof renew === typeof undefined)
		renew = false;
	//typeof this.navGridOptions !== typeof undefined ? $.extend(this.nav_ops, this.navGridOptions) : undefined;
	// TODO maybe add delete form opts. So far deletion going through contextmenu
	var form_postData = Object(),self = this;
	form_postData.tname = this.tableQuery || this.table;
	form_postData.tid = this.id;
	if(this.main != true || this.subgrid == true)
	{
		form_postData.subgrid = true;
		form_postData.mainId = this.post_data.mainId;
		form_postData.mainIdname = this.post_data.mainIdname;
	}
	if(this.navGridOptions.add)
	{
		if(this.hasOwnProperty('addFormOptions') && !this.addFormOptions.hasOwnProperty('beforeSubmit'))
		{
			this.addFormOptions.beforeSubmit = function(postdata,formid)
			{
				if(form_postData.subgrid == true)
					postdata[form_postData.mainIdname] = form_postData.mainId;
				postdata.tname = form_postData.tname;
				if(self.hasOwnProperty('removeFromAddForm'))
				{
					for(var i = 0; i < self.removeFromAddForm.length;i++)
						delete postdata[self.removeFromAddForm[i]];
				}
				return[true];
			}
		}
		else if(renew == true && this.hasOwnProperty('addFormOptions'))
		{
			this.addFormOptions.beforeSubmit = function(postdata,formid)
			{
				if(form_postData.subgrid == true)
					postdata[form_postData.mainIdname] = form_postData.mainId;
				postdata.tname = form_postData.tname;
				if(self.hasOwnProperty('removeFromAddForm'))
				{
					for(var i = 0; i < self.removeFromAddForm.length;i++)
						delete postdata[self.removeFromAddForm[i]];
				}
				return[true];
			}
		}
		else if(!this.hasOwnProperty('addFormOptions'))
		{
			this.addFormOptions =
			{
				beforeSubmit:function(postdata,formid)
				{
					if(form_postData.subgrid == true)
						postdata[form_postData.mainIdname] = form_postData.mainId;
					postdata.tname = form_postData.tname;
					if(self.hasOwnProperty('removeFromAddForm'))
					{
						for(var i = 0; i < self.removeFromAddForm.length;i++)
							delete postdata[self.removeFromAddForm[i]];
					}
					return[true];
				},
				beforeShowForm:function(formid)
				{
					formid.css('max-width',$(this).width());
				}
			}
		}
	}
	if(this.navGridOptions.edit)
	{
		if(this.hasOwnProperty('editFormOptions') && this.editFormOptions.hasOwnProperty('beforeShowForm') && renew == false)
		{
			var hack = this.editFormOptions.beforeShowForm;
			delete this.editFormOptions.beforeShowForm;
			this.editFormOptions.beforeShowForm = function(formid)
			{
				$('.CaptionTD').before('<td class="verify"><span style="color:green;visibility:hidden;" class="fa fa-lg fa-check-circle-o"></span></td>');
				var grid = this;

				formid.find('.FormElement').change(function(e)
				{
					var name = this.name;
					var sel_row_id = grid.p.selrow;
					var old_value = $(grid).getCell(sel_row_id,name);
					if(old_value != this.value || old_value == false)
					{
						$(this).attr('changed',true);
						$('#tr_'+name).find('.verify').find('span').css('visibility','visible');
					};
				});
				/* adjust max width */
				formid.css('max-width',$(this).width());
				hack.call(this,formid);
			}
		}
		/* Abomination! */
		if(this.hasOwnProperty('editFormOptions') && !this.editFormOptions.hasOwnProperty('beforeSubmit'))
		{
			this.editFormOptions.beforeSubmit = function(postdata,formid)
			{
				$.each($('.FormElement',formid),function(){
					if(this.id !== 'id_g' && $(this).attr('changed') !== 'true')
						delete postdata[this.name]
				});
				postdata.tname = form_postData.tname;
				postdata.tid = form_postData.tid;
				return[true];
			}
		}
		else if(renew == false && this.hasOwnProperty('editFormOptions') && this.editFormOptions.hasOwnProperty('beforeSubmit'))
		{
			var hack = this.editFormOptions.beforeSubmit;
			delete this.editFormOptions.beforeSubmit;
			this.editFormOptions.beforeSubmit = function(postdata,formid)
			{
				$.each($('.FormElement',formid),function(){
					if(this.id !== 'id_g' && $(this).attr('changed') !== 'true')
						delete postdata[this.name]
				});
				var ret = hack.call(this,postdata,formid);
				if(typeof ret == 'object') ret = ret[0];
				postdata.tname = form_postData.tname;
				postdata.tid = form_postData.tid;
				return[ret];
			}
		}
		else if(renew == true && this.hasOwnProperty('editFormOptions'))
		{
			this.editFormOptions.beforeSubmit = function(postdata,formid)
			{
				$.each($('.FormElement',formid),function(){
					if(this.id !== 'id_g' && $(this).attr('changed') !== 'true')
						delete postdata[this.name]
				});
				postdata.tname = form_postData.tname;
				postdata.tid = form_postData.tid;
				return[true];
			}
		}
		else if(!this.hasOwnProperty('editFormOptions'))
		{
			this.editFormOptions =
			{
				beforeSubmit:function(postdata,formid)
				{
					$.each($('.FormElement',formid),function(){
						if(this.id !== 'id_g' && $(this).attr('changed') !== 'true')
							delete postdata[this.name]
					});
					postdata.tname = form_postData.tname;
					postdata.tid = form_postData.tid;
					return[true];
				},
				beforeShowForm:function(formid)
				{
					$('.CaptionTD').before('<td class="verify"><span style="color:green;visibility:hidden;" class="fa fa-lg fa-check-circle-o"></span></td>');
					var grid = this;
					formid.find('.FormElement').change(function(e){
						var name = this.name;
						var sel_row_id = grid.p.selrow;
						var old_value = $(grid).getCell(sel_row_id,name);
						if(old_value != this.value)
						{
							$(this).attr('changed',true);
							$('#tr_'+name).find('.verify').find('span').css('visibility','visible');
						}
					})
					if(self.hasOwnProperty('beforeShowForm'))
						self.beforeShowForm.call(this,formid)
					/* adjust max width */
					formid.css('max-width',$(this).width());
				}
			}
		}
	}
	if(this.navGridOptions.search)
	{
		if(!this.hasOwnProperty('searchFormOptions'))
		{
			this.searchFormOptions =
			{
				beforeShowSearch:function(formid)
				{
					formid.css('max-width',$(this).width());
					return true;
				}
			}
		}
	}
	if(this.filterToolbar == false || typeof this.filterToolbar === typeof undefined)
		this.navGridOptions.clearFilters = false;

	this.nav_ops = { refreshstate : 'current',refreshcurrent : true };
	$.extend(this.nav_ops,this.navGridOptions);
}
jqGrid$.prototype.cell_select = function() {
	var self = this;
	var table = this.onCellSelect instanceof Object ? this.onCellSelect.table : this.hasOwnProperty('tableQuery') ? this.tableQuery : this.table;
	var params = new Object();
	this.inline_params = new Object();
	this.inline_params.extraparam = new Object();
	this.inline_params.extraparam.oper = 'add';
	this.inline_params.extraparam.tname = this.onCellSelect instanceof Object ? this.onCellSelect.table : this.hasOwnProperty('tableQuery') ? this.tableQuery : this.table;
	if (this.subgrid === true)
		this.inline_params.extraparam[this.subgridpost.mainidName] = this.subgridpost.mainid;
	if(this.hasOwnProperty('inline_extra_param'))
		$.extend(this.inline_params.extraparam,this.inline_extra_param);
	var func = function(rowid, iCol, cellcontent, e) {
		if (rowid === 'blank') {
			if (this.p.prev_iRow && this.p.prev_iCol)
				$(this).saveCell(this.p.prev_iRow, this.p.prev_iCol);

			$(this).setGridParam({
				cellEdit : false
			});
			$(this).editRow('blank', self.inline_params);
		} else {
			if ($('input[class="editable"]').length > 0) {
				$(this).restoreRow('blank');
				$(this).setGridParam({
					cellEdit : true
				});
			}
		}
	};

	if (this.hasOwnProperty('events'))
		this.events.onCellSelect = func;
	else
		this.events = {
			onCellSelect : func
		};
}
jqGrid$.prototype.add_cell_params = function() {
	var table = this.beforeSubmitCell instanceof Object ? this.beforeSubmitCell.table : this.hasOwnProperty('tableQuery') ? this.tableQuery : this.table;
	var id = this.beforeSubmitCell instanceof Object ? this.beforeSubmitCell.hasOwnProperty('id') ? this.beforeSubmitCell.id : this.id : this.id;
	var params = $.extend({}, {
		tname : table,
		tid : id
	});

	var func = function(table, id) {
		return function(rowid, cellname, value, iRow, iCol) {
			var params = $.extend({}, {
				tname : table,
				tid : id
			});
			return params;
		}
	};
	if (this.hasOwnProperty('events'))
		this.events.beforeSubmitCell = func(table, id);
	else
		this.events = {
			beforeSubmitCell : func(table, id)
		};

}
jqGrid$.prototype.add_subgrids = function() {
	var self = this;
	this.options.subGrid = true;
	this.expandedGrid = new Array;
	var func = function(subs)
	{
		var subGrids = new Array;
		return function(subgrid_id, row_id)
		{
			self.subGrids = new Array();
			var this_g = this;
			if ($.inArray(row_id, self.expandedGrid) < 0)
				self.expandedGrid.push(row_id);
			var this_g_data = {
				formatted:$(this).getRowData(row_id),
				unformatted:$(this).getRowDataRaw(row_id)
			}
			var $main_grid = $('#' + subgrid_id),
			    table,
			    wrapper;
			var main_width = $main_grid.width();
			for (var i = 0; i < subs.length; i++)
			{
				if(subs[i].options.hasOwnProperty('width_whole') && subs[i].options.width_whole == true)
					subs[i].options.width = $main_grid.width() - 5;
				else if(subs[i].options.hasOwnProperty('width_whole_of_main'))
					subs[i].options.width = $main_grid.width() / 100 *  subs[i].options.width_whole_of_main  - 5;
				else if(!subs[i].options.hasOwnProperty('width'))
					subs[i].options.width = $main_grid.width() / subs.length - 5;
				if(!subs[i].options.hasOwnProperty('height'))
					subs[i].options.height = self.grid_p.height / 2;

				table = document.createElement('table');
				table.id = subs[i].name = subgrid_id + '_' + subs[i].name + '_subGrid';
				table.className = 'scroll';
				if (subs.length > 1) {
					wrapper = document.createElement('div');
					wrapper.style.display = 'inline-block';
					wrapper.style.verticalAlign = 'top';
					wrapper.style.marginRight = '5px';
					wrapper.style.width = (subs[i].options.width) + 'px';
					wrapper.appendChild(table);
					$main_grid.append(wrapper);
				} else
					$main_grid.append(table);

				if (!subs[i].hasOwnProperty('subgridpost')) {
					$.alert('Subgridpost prop is missing')
					break;
				}
				if(subs[i].hasOwnProperty('subgridBeforeCreate') && $.isFunction(subs[i].subgridBeforeCreate))
					subs[i] = subs[i].subgridBeforeCreate.call(this,subs[i],self,this_g_data.formatted,this_g_data.unformatted);
				if(!subs[i].subgridpost.hasOwnProperty('pseudo_mainid'))
					subs[i].subgridpost.mainid = row_id;
				else
					subs[i].subgridpost.mainid = subs[i].subgridpost.pseudo_mainid;

				subs[i].parent_row_id = row_id;
				subs[i].parent_grid = this_g;
				subs[i].parent_row_data = {formatted : this_g_data.formatted , unformatted: this_g_data.unformatted };
				subGrids[i] = new jqGrid$(subs[i]);
				self.subGrids.push(subGrids[i])
			}
		}
	}
	if (this.hasOwnProperty('events'))
	{
		this.events.subGridRowExpanded = func(this.subGridOps);
		this.events.subGridRowColapsed = function(subgrid_id, row_id)
		{
			self.expandedGrid.splice($.inArray(row_id, self.expandedGrid), 1);
			$("div[id ^=alertmod_][id $=_subGrid]").remove();
		}
	}
	else
	{
		this.events = {
			subGridRowExpanded : func(this.subGridOps)
		};
		this.events.subGridRowColapsed = function(subgrid_id, row_id)
		{
			self.expandedGrid.splice($.inArray(row_id, self.expandedGrid), 1);
			$("div[id ^=alertmod_][id $=_subGrid]").remove();
		}
	}
}
jqGrid$.prototype.init = function()
{
	if(this.prepare === true)
	{
		this.grid_element = $('#' + this.name);
		this.grid_element_pure = document.getElementById(this.name);
		this.grid_first_load = true;
		this.prepare = false;
		this.clear_nav();
	}
	this.prepare_post_data();
	this.prepare_col_model();
	this.grid_element_pure.jqGrid$ = this;
	if(this.hasOwnProperty('name_linked_grid'))
	{
		var linked_grid_selrow = $('#'+this.name_linked_grid).get(0).p.selrow;
		if(linked_grid_selrow && this.subgrid == true && typeof this.grid_element_pure.p !== typeof undefined)
			this.renew_external_sub(linked_grid_selrow);
	}
}
jqGrid$.prototype.clear_nav = function()
{
	delete this.editFormOptions;
	delete this.addFormOptions;
	delete this.delFormOptions;
	delete this.searchFormOptions;
}
jqGrid$.prototype.renew_external_sub = function(rowid)
{
	if(typeof rowid === typeof undefined)
		return;

	this.grid_element_pure.p.postData.mainId = rowid;
	this.post_data.mainId = rowid;
	this.subgridpost.mainid = rowid;
	if(this.navGrid === true)
	{
		this.navigator_opts(true);
		this.create_nav();
	}
	if(this.onCellSelect === true)
	{
		this.cell_select();
	}
	this.grid_element.trigger("reloadGrid",{current:true,overlay:false});
}
jqGrid$.prototype.remove_blank_row = function()
{
	delete this.events.onCellSelect;
	$('tr#blank',this.grid_element).remove();
}
jqGrid$.prototype.recreate_jqGrid = function() {
	this.grid_element = $('#' + this.name);
	this.grid_element_pure = document.getElementById(this.name);
	this.grid_first_load = true;

	this.prepare_post_data();
	this.prepare_col_model();
}
jqGrid$.prototype.adjust_height = function() {
	var grid_wrapper;
	var initial_height;
	var els = 0;
	var offset = false;
	if(this.grid_first_load === true)
	{
		// if grid created but not loaded
		if(this.grid_element.parents('.ui-jqgrid').length > 0)
			grid_wrapper = this.grid_element.parents('.ui-jqgrid')[0].parentNode;
		else
			grid_wrapper = this.grid_element_pure.parentNode;
		if(this.grid_element_pure.parentNode.id === 'gridcontainer')
			initial_height = window.innerHeight;
		else if(this.grid_element_pure.parentNode.nodeName === 'DIV' || this.grid_element_pure.parentNode.nodeName === 'FIELDSET')
		{
			if(this.grid_element_pure.parentNode.innerHeight > window.innerHeight)
				initial_height = window.innerHeight;
			else
			{
				if(grid_wrapper.innerHeight > 0)
					initial_height = grid_wrapper.innerHeight;
				else
				{
					if($(grid_wrapper).prev().attr('class') === 'menu-cont')
						offset = true;

					initial_height = window.innerHeight - grid_wrapper.offsetTop;
				}
			}
		}
	}
	else if (this.grid_first_load === false)
	{
		grid_wrapper = this.grid_element.parents('.ui-jqgrid')[0].parentNode;
		if($(grid_wrapper).prev().attr('class') === 'menu-cont')
			offset = true;
		initial_height = window.innerHeight - grid_wrapper.offsetTop;
	}
	// firefox need 1;
	if(get_agent() === 'Firefox')
		els += 1;
	// add top menu height;
	if($('.menu-cont').length > 0 && offset === false)
	{
		if(this.grid_element.parents('#gridcontainer').length > 0  && this.grid_element.parents().prev().attr('class') === 'menu-cont')
			els += 35;
		else if(this.grid_element.prev().attr('class') === 'menu-cont')
			els += 35;
		else
		{
			if(this.grid_first_load === false)
			{
				if(this.grid_element.parents('.ui-jqgrid').parents('#gridcontainer').length > 0 && this.grid_element.parents('.ui-jqgrid').parents().prev().attr('class') === 'menu-cont')
					els+= 35;
				else if(this.grid_element.parents('.ui-jqgrid').prev().attr('class') === 'menu-cont')
					els+= 35;
			}
		}
	}
	// add footer of grid;
	els += 28;
	// add column labels height;
	els += 22;
	// add column labels grid_wrapper bottom border
	els += 1;
	// add resize missing pixel...
	//if(this.grid_first_load === false)
		//els += 1;
	if (this.navGrid)
		els += 56;
	if (this.filterToolbar)
		els += 31;
		//els += 23;
	if (this.options.hasOwnProperty('caption'))
		els += 32;
	if (this.options.hasOwnProperty('footerrow'))
		els += 22;
	if(this.hasOwnProperty('adjust_height_els'))
		els += this.adjust_height_els;
	return initial_height - els;
}
jqGrid$.prototype.set_defaults = function() {
	this.jgrid_init_obj = {
		caption : "",
		loadtext : "",
		autowidth : true,
		height : this.options.hasOwnProperty('height') ? this.options.height : this.adjust_height(),
		cmTemplate : {
			align : "center",
			editable : true,
			celleditable : true,
			addformeditable : true,
			inlineeditable : true,
			search : true,
			searchoptions : {
				clearSearch : false,
				sopt : this.sop_ops
			}
		},
		datatype : 'json',
		mtype : 'POST',
		editurl : REQUEST_URL,
		cellurl : REQUEST_URL,
		url : REQUEST_URL,
		gridview : true,
		rowList : this.device ? [5,10,20] :[10, 20, 30, 40, 50, 100, 500, 1000],
		rowNum : this.rowNum ? this.rowNum : 500,
		shrinkToFit : false,
		toppager : true,
		viewrecords : true,
		hidegrid : false,
		rowattr : null,
		// grid defaults
		page : 1,
		rowTotal : null,
		records : 0,
		pager : "",
		pgbuttons : true,
		pginput : true,
		colModel : [],
		colNames : [],
		sortorder : "asc",
		sortname : "",
		altRows : false,
		selarrrow : [],
		savedRow : [],
		xmlReader : {},
		jsonReader : {},
		subGrid : false,
		subGridModel : [],
		reccount : 0,
		lastpage : 0,
		lastsort : 0,
		selrow : null,
		beforeSelectRow : null,
		onSelectRow : null,
		onSortCol : null,
		ondblClickRow : null,
		onRightClickRow : null,
		onPaging : null,
		onSelectAll : null,
		onInitGrid : null,
		onCellSelect : null,
		loadComplete : null,
		gridComplete : null,
		loadError : null,
		loadBeforeSend : null,
		afterInsertRow : null,
		beforeRequest : null,
		beforeProcessing : null,
		onHeaderClick : null,
		loadonce : false,
		multikey : false,
		search : false,
		hiddengrid : false,
		postData : {},
		userData : {},
		treeGrid : false,
		treeGridModel : 'nested',
		treeReader : {},
		treeANode : -1,
		ExpandColumn : null,
		tree_root_level : 0,
		prmNames : {
			page : "page",
			rows : "rows",
			sort : "sidx",
			order : "sord",
			search : "_search",
			nd : "nd",
			id : "id",
			oper : "oper",
			editoper : "edit",
			addoper : "add",
			deloper : "del",
			subgridid : "id",
			npage : null,
			totalrows : "totalrows"
		},
		forceFit : false,
		gridstate : "visible",
		cellEdit : false,
		cellsubmit : "remote",
		nv : 0,
		loadui : "enable",
		toolbar : [false, ""],
		scroll : false,
		deselectAfterSort : true,
		scrollrows : false,
		scrollOffset : 18,
		cellLayout : 5,
		subGridWidth : 20,
		multiselect:false,
		multiboxonly:false,
		multiselectWidth : 20,
		rownumWidth : 25,
		rownumbers : false,
		pagerpos : 'center',
		recordpos : 'right',
		footerrow : false,
		userDataOnFooter : false,
		hoverrows : true,
		altclass : 'ui-priority-secondary',
		viewsortcols : [false, 'vertical', true],
		resizeclass : '',
		autoencode : false,
		remapColumns : [],
		ajaxGridOptions : {},
		direction : "ltr",
		headertitles : false,
		scrollTimeout : 40,
		data : [],
		_index : {},
		grouping : false,
		groupingView : {
			groupField : [],
			groupOrder : [],
			groupText : [],
			groupColumnShow : [],
			groupSummary : [],
			showSummaryOnHide : false,
			sortitems : [],
			sortnames : [],
			summary : [],
			summaryval : [],
			plusicon : 'ui-icon-circlesmall-plus',
			minusicon : 'ui-icon-circlesmall-minus',
			displayField : [],
			groupSummaryPos : [],
			formatDisplayField : [],
			_locgr : false
		},
		ignoreCase : false,
		idPrefix : "",
		multiSort : false
	}
}
