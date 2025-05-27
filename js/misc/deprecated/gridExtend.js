/*
 * Шаблон навигации грида
 */
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
/*
 * Шаблон поиска грида
 */
$.extend($.jgrid.search,
	{
		closeAfterSearch:true,
	    recreateForm: true,
	    closeOnEscape:true,
	    height:'auto',
	    width:'auto',
	    afterShowSearch:positionCenter,
	    multipleSearch:true,
	    onReset:clearFilters,
	    drag:true,
	    viewPagerButtons: false
	}
);
/*
 * Шаблон редактирования грида
 */
$.extend($.jgrid.edit,
	{
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
		afterSubmit:function(response, postdata)
		{
			if((response.responseText).length > 0 && (response.responseText).length > 10) 
				return [false,response.responseText];
			else
				return [true];
		}
	}
);
/*
 * Шаблон удаления в гриде
 */
$.extend($.jgrid.del,
	{
		closeOnEscape:true,
		reloadAfterSubmit:false,
		height:'auto',
		width:'auto',
		afterShowForm:positionCenter,
		drag:true,
		afterSubmit:function(response, postdata)
		{
			if((response.responseText).length > 10)
				return [false,response.responseText];
			else
				return [true];
		}
	}
)

$.extend($.jgrid.inlineEdit,
	{
		keys:true,
		url:'/php/rowedit',
		successfunc:function(responce){
			if(responce.responseText.length > 10)
			{
				var tr = $(this).jqGrid('getGridRowById', 'blank'), positions = $.jgrid.findPos(tr);
				if(this.p.subGrid === true )
					positions[0] = positions[0] + 25;
					
				$.jgrid.info_dialog($.jgrid.errors.errcap,'<div class="ui-state-error">' + responce.responseText + '</div>',$.jgrid.edit.bClose,{left:positions[0],top:positions[1]+$(tr).outerHeight()});
			}
			else 
			{
				if($('#info_content').length > 0)
					$('#closedialog').trigger('click');
					
				inlineSucFunc.call(this,responce);
			}
				
			return false;
		},
		afterrestorefunc:function(rowid){
			$(this).setGridParam({cellEdit:true});
		}
	}
);
/*
 * Ф-ия настроек по умолчанию
 */
function resetDefaults()
{
	$.extend($.jgrid.defaults, 
		{
			/* DarkRay defualts */
			caption:"",
			loadtext:"",
			autowidth:true,
			height:gridHeight(0),
			cmTemplate: {
		    	align:"center",
		    	editable:true,
		    	celleditable:true,
		    	addformeditable:true,
		    	inlineeditable:true,
		    	search:true,
		    	searchoptions:{clearSearch:false,sopt:sopCodes},
		    	editoptions: {size:'100%'} 	
		    },
		    datatype:'json',
		    mtype: 'POST',
		    editurl:'/php/rowedit',
		    cellurl:'/php/rowedit',
		    url:'/php/getdata',
		    gridview: true,
		    multiselect:false,
			multiboxonly:false,
			rowList: [10,20,30,40,50,100,500,1000,5000],
			rowNum: 500,
			shrinkToFit:false,
			toppager:true,
			viewrecords: true,
			hidegrid: false,
			/* cellEdit error reporting */
			afterSubmitCell:function(serverresponse, rowid, cellname, value, iRow, iCol)
			{
				if((serverresponse.responseText).length > 0)
				{
					return[false,serverresponse.responseText];
				}
				else 
					return[true,''];
			},
			/* save iRow and iCol in global scope vars */
			beforeEditCell:function(rowid, cellname, value, iRow, iCol)
			{
				if($('#info_dialog').length > 0){
					$(this).restoreCell(iRow,iCol)
				}
				beforeEditCellActions.apply(this,[rowid, cellname, value, iRow, iCol]); 
			},
			/* enable right click tools */
			gridComplete: function() 
			{
				gridTools.call(this); 
			},
			/* Jqgrid defaults */
			page: 1,
			rowTotal : null,
			records: 0,
			pager: "",
			pgbuttons: true,
			pginput: true,
			colModel: [],
			colNames: [],
			sortorder: "asc",
			sortname: "",
			altRows: false,
			selarrrow: [],
			savedRow: [],
			xmlReader: {},
			jsonReader: {},
			subGrid: false,
			subGridModel :[],
			reccount: 0,
			lastpage: 0,
			lastsort: 0,
			selrow: null,
			beforeSelectRow: null,
			onSelectRow: null,
			onSortCol: null,
			ondblClickRow: null,
			onRightClickRow: null,
			onPaging: null,
			onSelectAll: null,
			onInitGrid : null,
			loadComplete: null,
			gridComplete: null,
			loadError: null,
			loadBeforeSend: null,
			afterInsertRow: null,
			beforeRequest: null,
			beforeProcessing : null,
			onHeaderClick: null,
			loadonce: false,
			multikey: false,
			search: false,
			hiddengrid: false,
			postData: {},
			userData: {},
			treeGrid : false,
			treeGridModel : 'nested',
			treeReader : {},
			treeANode : -1,
			ExpandColumn: null,
			tree_root_level : 0,
			prmNames: {page:"page",rows:"rows", sort: "sidx",order: "sord", search:"_search", nd:"nd", id:"id",oper:"oper",editoper:"edit",addoper:"add",deloper:"del", subgridid:"id", npage: null, totalrows:"totalrows"},
			forceFit : false,
			gridstate : "visible",
			cellEdit: false,
			cellsubmit: "remote",
			nv:0,
			loadui: "enable",
			toolbar: [false,""],
			scroll: false,
			deselectAfterSort : true,
			scrollrows : false,
			scrollOffset :18,
			cellLayout: 5,
			subGridWidth: 20,
			multiselectWidth: 20,
			rownumWidth: 25,
			rownumbers : false,
			pagerpos: 'center',
			recordpos: 'right',
			footerrow : false,
			userDataOnFooter : false,
			hoverrows : true,
			altclass : 'ui-priority-secondary',
			viewsortcols : [false,'vertical',true],
			resizeclass : '',
			autoencode : false,
			remapColumns : [],
			ajaxGridOptions :{},
			direction : "ltr",
			headertitles: false,
			scrollTimeout: 40,
			data : [],
			_index : {},
			grouping : false,
			groupingView : {groupField:[],groupOrder:[], groupText:[],groupColumnShow:[],groupSummary:[], showSummaryOnHide: false, sortitems:[], sortnames:[], summary:[],summaryval:[], plusicon: 'ui-icon-circlesmall-plus', minusicon: 'ui-icon-circlesmall-minus', displayField: [], groupSummaryPos:[], formatDisplayField : [], _locgr : false},
			ignoreCase : false,
			idPrefix : "",
			multiSort :  false
		}
	)
}