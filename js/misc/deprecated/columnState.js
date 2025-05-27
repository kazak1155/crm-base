globalScope.firstLoad = true;

function saveObjectInLocalStorage(storageItemName, object) 
{
	if (typeof window.localStorage !== typeof undefined)
		window.localStorage.setItem(storageItemName, JSON.stringify(object));
}

function removeObjectFromLocalStorage(storageItemName,reload) 
{
	if(typeof window.localStorage !== typeof undefined)
		window.localStorage.removeItem(storageItemName);
	if(typeof reload !== typeof undefined && reload === true)
		location.reload();	
}
function getObjectFromLocalStorage(storageItemName) 
{
	if (typeof window.localStorage !== typeof undefined)
		return $.parseJSON(window.localStorage.getItem(storageItemName));
}
function restoreColumnState (colModel,storageItemName) 
{
	var colItem,l = colModel.length,colStates, cmName,columnsState = getObjectFromLocalStorage(storageItemName);
	
	if (columnsState)
	{
		colStates = columnsState.colStates;
		for (var i = 0; i < l; i++)
		{
			colItem = colModel[i];
			cmName = colItem.name;
			if (cmName !== 'rn' && cmName !== 'cb' && cmName !== 'subgrid')
				colModel[i] = $.extend(true, {}, colModel[i], colStates[cmName]);
		}
	}
	return columnsState;
}
function saveColumnState (perm,storageItemName) 
{
	var ls = getObjectFromLocalStorage(storageItemName);
	var colModel = this.getGridParam('colModel'),
	l = colModel.length,colItem, cmName,
	postData = this.getGridParam('postData'),
	columnsState =
	{
		page: this.getGridParam('page'),
		sortname: this.getGridParam('sortname'),
		sortorder: this.getGridParam('sortorder'),
		permutation: perm,
		colStates: {}
	},
	colStates = columnsState.colStates;
	
	
	if(ls && ls.hasOwnProperty('importState'))
		columnsState.importState = ls.importState;
	
	for (var i = 0; i < l; i++)
	{
		colItem = colModel[i];
 		cmName = colItem.name;
		if (cmName !== 'rn' && cmName !== 'cb' && cmName !== 'subgrid')
		{
			colStates[cmName] =
			{
				width: colItem.width,
				hidden: colItem.hidden
			};
			if(colItem.searchoptions.hasOwnProperty('width'))
				colStates[cmName].searchoptions = {width:colItem.searchoptions.width};
			
		}
	}
	saveObjectInLocalStorage(storageItemName, columnsState);
}
function columnState(data,colModel,storageItemName)
{
	var cm = restoreColumnState(colModel,storageItemName),isColState = typeof (cm) !== 'undefined' && cm !== null;
	
	for (var i = 0; i < mainGridRowId.length; i = i + 1)
	{
		$(this).jqGrid('expandSubGridRow', mainGridRowId[i]);
	}
	
	if (globalScope.firstLoad)
	{
		globalScope.firstLoad = false;
		if (isColState) 
			$(this).jqGrid("remapColumns", cm.permutation, true);
	}
	saveColumnState.call($(this), this.p.remapColumns,storageItemName);   
}
function columnSize(newwidth,index,storageItemName)
{
	var cm = $(this).getGridParam('colModel'),colName = cm[index]['index'],columnsState = getObjectFromLocalStorage(storageItemName);
	if(columnsState)
		if(columnsState.colStates.hasOwnProperty(colName))
		{
			columnsState.colStates[colName].width = newwidth;
			if(cm[index].stype === 'select')
			{
				newwidth = 5*(Math.floor(Math.abs(newwidth/5)))
				if(!columnsState.colStates[colName].hasOwnProperty('searchoptions'))
					columnsState.colStates[colName].searchoptions = {width:newwidth}
				else
					columnsState.colStates[colName].searchoptions.width = newwidth;
			}
				
		}
			
	saveObjectInLocalStorage(storageItemName, columnsState);
}
	
function gridLocalStorage(colModel,storageItemName){
	var cm = restoreColumnState(colModel,storageItemName),
	isColState = typeof (cm) !== 'undefined' && cm !== null;
	return isColState;
}
