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
//inline edit function
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
//calculate grid height
function gridHeight(other)
{
	var height
	var mHeight = 136;
	return $(window).height() - mHeight - other;
}
