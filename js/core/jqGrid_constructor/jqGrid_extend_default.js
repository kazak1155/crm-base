$.extend($.fn.fmatter, {
	button:function(cellValue, options, rowObject)
	{
		if(Object.keys(rowObject).length == 0)
			return '';

		var bindEv = function(callback)
		{
			if ($.fn.fmatter.invokers === undefined)
				$.fn.fmatter.invokers = {};
			var name = 'func_'+this.gid+'_'+this.rowId+'_'+this.colModel.index;
			var t = this
			var handler = callback;
			$.fn.fmatter.invokers[name] = function ($control, rowId,gid)
			{
				callback.call(t.colModel.formatoptions.eventContext, $control, rowId, gid);
			};
			handler = "$.fn.fmatter.invokers." + name;
			return handler + "($(this)," + this.rowId + ","+this.gid+")";
		}
		var onClick = '',value;
		var style_raw = 'width:100%;height:100%;cursor:pointer;outline:none';
		var style = 'style="'+ style_raw +'"';
		if(options.colModel.hasOwnProperty('formatoptions'))
		{
			value = options.colModel.formatoptions.hasOwnProperty('value') ? options.colModel.formatoptions.value : '';

			if(options.colModel.formatoptions.hasOwnProperty('onButtonClick') && $.isFunction(options.colModel.formatoptions.onButtonClick))
			{
				onClick = "onclick='"+bindEv.call(options,options.colModel.formatoptions.onButtonClick)+"'";
			}
			else
				return 'onButtonClick undefined or not a function';
			if(options.colModel.formatoptions.hasOwnProperty('beforeButtonCreate'))
			{
				var splitted_style_raw = style_raw.split(';'),style_obj = new Object(),style_p,ret;
				for(var i = 0; i < splitted_style_raw.length; i++)
				{
					style_p = splitted_style_raw[i].split(/:(.+)/);
					style_p = style_p.filter(Boolean);
					style_obj[style_p[0]] = style_p[1];
				}
				ret = options.colModel.formatoptions.beforeButtonCreate.call(document.getElementById(options.gid),options.rowId,style_obj,options);
				if(ret === undefined)
					ret = [options,style_raw];
				else
				{
					style_p = '';
					for(style in ret[1])
					{
						style_p += style +':'+ ret[1][style];
						if(style != Object.keys(ret[1]).pop())
							style_p += ';';
					}
				}
				options = ret[0];
				style = 'style="'+ style_p +'"';
			}
		}

		var raw_element = '<button class="button-flat-blue" '+style+' '+onClick+' >'+ value +'</button';
		return raw_element;
	},
	percentage:function(cellValue, options, rowObject)
	{
		if(Object.keys(rowObject).length == 0)
			return '';
		var pers = '';
		if(typeof cellValue !== typeof undefined && cellValue === null)
			pers = floatFormatter.call(this,cellValue,options, rowObject) + '%';
		else if(typeof cellValue !== typeof undefined && (cellValue.length > 0 || cellValue === null))
			pers = floatFormatter.call(this,cellValue,options, rowObject) + '%';

		return pers;
	},
	nullable_percentage:function(cellValue, options, rowObject)
	{
		if(Object.keys(rowObject).length == 0)
			return '';
		var pers;
		if(cellValue === null)
			pers = '&nbsp';
		else if(typeof cellValue !== typeof undefined && (cellValue.length > 0 || cellValue === null))
			pers = floatFormatter.call(this,cellValue,options, rowObject) + '%';
		return pers;
	},
	custom_checkbox:function(cellValue, options, rowObject,action)
	{
		var disabled = options.colModel.editable == false ? true : false,style = 'font-size:1.4em;display:table;height:100%;width:100%;',className;
		var bindEv = function(callback)
		{
			if ($.fn.fmatter.invokers === undefined)
				$.fn.fmatter.invokers = {};
			var name = 'func_'+this.gid+'_'+this.rowId+'_'+this.colModel.index,grid = this;
			var handler = callback;
			$.fn.fmatter.invokers[name] = function ($control, rowId,gid)
			{
				callback.call(grid.colModel.formatoptions.eventContext, $control, rowId, gid);
			};
			handler = "$.fn.fmatter.invokers." + name;
			return handler + "($(this),\'" + this.rowId + "\',"+this.gid+")";
		}
		if(typeof action === typeof undefined && options.rowId !== 'blank')
			cellValue = 0;
		if(typeof cellValue !== typeof undefined && options.rowId !== 'blank')
		{
			options.colModel.editable == false ? style += 'cursor:no-drop;' : style += 'cursor:cell;';
			cellValue == null ? cellValue = 0 : false;
			cellValue == 1 ? className = 'fa fa-check fa-display-table' : className = 'fa fa-times fa-display-table';
			cellValue == 1 ? style += 'color:green;' : style += 'color:red;';
			disabled == true ? style += 'opacity:0.3;': false;

			return '<i id="'+ options.rowId +'_'+ options.colModel.name +'_t" name="'+ options.colModel.name +'" value="'+ cellValue +'" class="'+ className +'" style="'+ style +'" onclick="'+ bindEv.call(options,options.colModel.formatoptions.onCheckBoxClick) +'"></i>';
		}
		else
			return '';
	}
});
$.extend($.fn.fmatter.custom_checkbox, {
	unformat:function(cellValue, options, cellObject)
	{
		var value = cellObject.find('i').attr('value');
		if(typeof value !== typeof undefined)
			return value;
		else
			return '';
	}
});
$.extend($.fn.fmatter.percentage, {
	unformat:function(cellValue, options, cellObject)
	{
		//if(cellValue.length > 0)
			//return cellValue.replace(/%/g,'')
	}
});
$.extend($.fn.fmatter.button, {
	unformat:function(cellValue, options, cellObject)
	{
		return '';
	}
});
// almost same as getRowData, except its ignoring formatter
$.jgrid.extend({
	getRowDataRaw: function(rowid)
	{
		var res = {}, resall, getall=false, len, j=0;
		this.each(function(){
			var $t = this,nm,ind;
			if(rowid === undefined)
			{
				getall = true;
				resall = [];
				len = $t.rows.length;
			}
			else
			{
				ind = $($t).jqGrid('getGridRowById', rowid);
				if(!ind)
					return res;
				len = 1;
			}
			while(j<len)
			{
				if(getall)
					ind = $t.rows[j];
				if($(ind).hasClass('jqgrow'))
				{
					$('td[role="gridcell"]',ind).each( function(i)
					{
						nm = $t.p.colModel[i].name;
						if ( nm !== 'cb' && nm !== 'subgrid' && nm !== 'rn' &&  $t.p.colModel[i].gtype !== 'checkbox' && $t.p.colModel[i].edittype !== 'checkbox' && $t.p.colModel[i].formatter !== 'button')
						{
							if($t.p.treeGrid===true && nm === $t.p.ExpandColumn)
							{
								res[nm] = $.jgrid.htmlDecode($("span:first",this).html());
							}
							else
							{
								res[nm] = $.jgrid.htmlDecode($(this).html());
							}
						}
						else if($t.p.colModel[i].gtype === 'checkbox' || $t.p.colModel[i].edittype === 'checkbox')
						{
							res[nm] = $(this).find('i').attr('value');
						}
					});
					if(getall)
					{
						resall.push(res);
						res={};
					}
				}
				j++;
			}
		});
		return res;
	}
})
$.jgrid.extend({
	getRowData : function( rowid ) {
		var res = {}, resall, getall=false, len, j=0;
		this.each(function(){
			var $t = this,nm,ind;
			if(rowid === undefined) {
				getall = true;
				resall = [];
				len = $t.rows.length;
			} else {
				ind = $($t).jqGrid('getGridRowById', rowid);
				if(!ind) { return res; }
				len = 2;
			}
			while(j<len){
				if(getall) { ind = $t.rows[j]; }
				if( $(ind).hasClass('jqgrow') ) {
					$('td[role="gridcell"]',ind).each( function(i)
					{
						nm = $t.p.colModel[i].name;
						if ( nm !== 'cb' && nm !== 'subgrid' && nm !== 'rn' &&  $t.p.colModel[i].gtype !== 'checkbox' && $t.p.colModel[i].edittype !== 'checkbox' && $t.p.colModel[i].formatter !== 'button'){
							if($t.p.treeGrid===true && nm === $t.p.ExpandColumn) {
								res[nm] = $.jgrid.htmlDecode($("span:first",this).html());
							} else {
								try {
									res[nm] = $.unformat.call($t,this,{rowId:ind.id, colModel:$t.p.colModel[i]},i);
								} catch (e){
									res[nm] = $.jgrid.htmlDecode($(this).html());
								}
							}
						}
						else if($t.p.colModel[i].gtype === 'checkbox' || $t.p.colModel[i].edittype === 'checkbox')
						{
							res[nm] = $(this).find('i').attr('value');
						}
					});
					if(getall) { resall.push(res); res={}; }
				}
				j++;
			}
		});
		return resall || res;
	}
})
$.extend($.jgrid.nav,
	{
		refresh:true,
		undo:false,
		clearFilters: true,
		edit:false,
		search:false,
		add:false,
		del:false,
		view:false,
		edittext:app.edittext,
		addtext:app.addtext,
		deltext:app.deltext,
		searchtext:app.searchtext,
		refreshtext:app.refreshtext,
		clearfilterstext:app.clearfilterstext,
		undotext:""
	}
);
$.extend($.jgrid.search,
	{
		formtype:'vertical',
		closeAfterSearch:true,
		recreateForm: true,
		closeOnEscape:true,
	   height:'auto',
	   width:'auto',
	   afterShowSearch:positionCenter,
	   multipleSearch:true,
	   drag:true,
	   viewPagerButtons: false
	}
);
$.extend($.jgrid.edit,
	{
		resize:false,
		bCancel: "Закрыть",
		recreateForm: true,
		closeOnEscape:true,
		drag:true,
		closeAfterEdit:true,
		reloadAfterSubmit:true,
		height:'auto',
		width:'auto',
		closeAfterAdd:true,
		afterShowForm:positionCenter,
		viewPagerButtons: false,
		beforeInitData:function(formid,type)
		{
			if(this.p.cellEdit === true && this.p.prev_iRow && this.p.prev_iCol)
				$(this).saveCell(this.p.prev_iRow, this.p.prev_iCol);
		},
		afterSubmit:function(response, postdata)
		{
			if((response.responseText).length > 0 && (response.responseText).length > 10)
				return [false,response.responseText];
			else
				return [true];
		}
	}
);
$.extend($.jgrid.del,
	{
		resize:false,
		closeOnEscape:true,
		reloadAfterSubmit:false,
		height:'auto',
		width:301,
		afterShowForm:positionCenter,
		drag:true,
		beforeInitData:function(formid,type)
		{
			if(this.p.cellEdit === true && this.p.prev_iRow && this.p.prev_iCol)
				$(this).saveCell(this.p.prev_iRow, this.p.prev_iCol);
		},
		afterSubmit:function(response, postdata)
		{
			if(this.p.hasOwnProperty('gridProto') && this.p.gridProto.hasOwnProperty('subGridOps'))
			{
				if(postdata.id.indexOf(',') > 0)
				{
					var ids = postdata.id.split(',');
					for(var i = 0;i < ids.length;i++)
					{
						$(this).collapseSubGridRow(ids[i]);
					}
				}
				else
					$(this).collapseSubGridRow(postdata.id);
			}
			if((response.responseText).length > 10)
				return [false,response.responseText];
			else
				return [true];
		}
	}
)
$.extend($.jgrid.inlineEdit,
	{
		keys:false, // very important to set false here!
		url:REQUEST_URL,
		successfunc:function(responce){
			if(responce.responseText.length > 10)
			{
				var tr = $(this).jqGrid('getGridRowById', 'blank'), positions = $.jgrid.findPos(tr);
				if(this.p.subGrid === true )
					positions[0] = positions[0] + 25;
				//$.jgrid.info_dialog($.jgrid.errors.errcap,'<div class="ui-state-error">' + responce.responseText + '</div>',$.jgrid.edit.bClose,{width:'auto',left:positions[0],top:positions[1]+$(tr).outerHeight()});
				$.jgrid.info_dialog($.jgrid.errors.errcap,'<div class="ui-state-error">' + responce.responseText + '</div>',$.jgrid.edit.bClose,{width:'auto'});
			}
			else if(responce.responseText.length <= 10 && responce.responseText.length !== 0)
			{
				if($('#info_content').length > 0)
					$('#closedialog').trigger('click');

				inlineSucFunc.call(this,responce);
				$(this).triggerHandler("jqGridInlineAfterSaveRow", [responce.responseText, responce]);
			}
			return false;
		},
		afterrestorefunc:function(rowid){
			$(this).setGridParam({cellEdit:true});
		}
	}
);
