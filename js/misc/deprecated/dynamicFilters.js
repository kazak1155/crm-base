/*grid part*/
/* reforge post data */
function modifyfilters (){
	var result;
	if(globalScope.dfLength > 0 || globalScope.defaultFilters.length > 0 || globalScope.dynamicFilters.length > 0){
		var fullPostData = $(this).jqGrid('getGridParam','postData');
		if (typeof(fullPostData.filters) !== 'undefined' && globalScope.dynamicFilters.length > 0){
			var gridFilters = JSON.parse(fullPostData.filters);
			for (var i=0;i<(globalScope.dynamicFilters).length;i++){
				var result = containsObject(globalScope.dynamicFilters[i],gridFilters.rules,'field','op','data');
				if (result === false) {
					gridFilters.rules.push(globalScope.dynamicFilters[i]);
				}
			}
			$(this).jqGrid('setGridParam',{postData:{_search:true,filters:'{"groupOp":"'+globalScope.groupOp+'","rules":' + JSON.stringify(gridFilters.rules) + '}'}});
		} else {
			globalScope.dynamicFilters = globalScope.defaultFilters.slice();
			$(this).jqGrid('setGridParam',{postData:{_search:true,filters:'{"groupOp":"'+globalScope.groupOp+'","rules":' + JSON.stringify(globalScope.defaultFilters) + '}'}});
		}
	}
}
/* page part */
/* push object into default filters from select ++ check if not repeated */
function addToDefaultFiltersSelects(field,rule,value){
	for(var i=0; i < (globalScope.dynamicFilters).length ; i++){
		if(globalScope.dynamicFilters[i].field == field && globalScope.dynamicFilters[i].op == rule){
			globalScope.dynamicFilters.splice(i,1);
		}
	}
	globalScope.dynamicFilters.push({"field":field,"op":rule,"data":value});
	$('#'+globalScope.gridName)[0].triggerToolbar();
}
/* clear all filters added from selects ++ can handle single remove if element setted */
function removeSelectsFromFilters(element){
	if (typeof(element) === 'undefined'){
		var selectedOptions = $(".filterselection option:selected").not(':empty');
	} else {
		var selectedOptions = element;
	}
	for(i=0;i<selectedOptions.length;i++){
		var select = $(selectedOptions[i]).parent();
		var selectedNameArray = (select[0].name).split(',');
		globalScope.dynamicFilters = $.grep(globalScope.dynamicFilters,function(e,i){ return e.field === selectedNameArray[1] },true);
	}
}
/* push object into dynamic filters from checkboxes/radio */
function addCbFilter(field,rule,value,ops){
	var globFils = globalScope.dynamicFilters, result,newFields = oldFields = newValues = oldValues = '';
	if(typeof(field) === 'object'){
		for(var i=0;i < globFils.length;i++){
			if(typeof(globFils[i]['field']) === 'object' && 
				( globFils[i]['field'].equals(field) === true && globFils[i]['op'].equals(rule) === true)
			) {
				globalScope.dynamicFilters.splice(i,1);
				result = false;
			} else {
				result = false;
			}
		}
		if (result === false){
			globalScope.dynamicFilters.push({"field":field,"op":rule,"data":value,"groupOps":ops});
			$('#'+globalScope.gridName)[0].triggerToolbar();
		}
	} else {
		result = containsObject([{'field':field,'op':rule,'data':value}],globFils,'field','op','data');
		if (result === false){
			globalScope.dynamicFilters.push({"field":field,"op":rule,"data":value});
			$('#'+globalScope.gridName)[0].triggerToolbar();
		}
	}
}
/*	Selects option function (yet selects need filterselection class to work, dont wanna change it) v.2
 * if processGrid set true -> selected value added to gridfilters, used if called with select.
 * with this option changed select options wont be changed!
 * if change set true -> all other selects with filterselection class will reconstruct to selected filter
 * 
 * if processGrid set false -> all selects will change their value according to setted filter, used if called by non-select element.
 * 
 * libId = ключ библиотеки
 * sQId = ключ таблицы
 * sQTable = таблица
 * globalOrder = number of field that need to be ordered
 * name[0] = source table name
 * name[1] = select column name in grid table
 * name[2] = source table column values
 * name[3] = oper for selects, should be always eq, but why not?
 */
function constructDynamicSelects(processGrid,change,libId,sQId,sQTable,globalOrder){
	if (processGrid === true){
		var indexToSplice;
		var thisVal = $(this).val();

		var thisNameArray = (this.name).split(',');
		var SelectsArray = $('.filterselection');

		if(thisVal !== ''){
			addToDefaultFiltersSelects(thisNameArray[1],thisNameArray[3],thisVal)
		} else {
			for(var y=0;y<(globalScope.dynamicFilters).length;y++){
				if(globalScope.dynamicFilters[y].field === thisNameArray[1] && globalScope.dynamicFilters[y].op === thisNameArray[3]){
					indexToSplice = y
				}
			}
			if(typeof(indexToSplice) !== 'undefined'){
				(globalScope.dynamicFilters).splice(indexToSplice,1);
				$('#'+globalScope.gridName)[0].triggerToolbar();
			}
		}
		if(change === true){
			SelectsArray.prop("disabled",true);
			SelectsArray.trigger("chosen:updated");
			for (i=0; i < SelectsArray.length; i++) {
				(function(i){
					if ($(SelectsArray[i]).val() == ''){
						var loopId = SelectsArray[i].id;
						var loopNameArray = (SelectsArray[i].name).split(',');
						$.ajax({
							type:'post',
							url:'php/selects',
							data:{qObj:'{"type":"SELECT","order":"'+globalOrder+'","id":"true"}',
								qValuesObj:'{"tName":"'+ loopNameArray[0] +'","tfields":[{"field":"'+libId+'"},{"field":"'+loopNameArray[2]+'"}]}',
								qFiltersObj:'{"rules":[{"field":"'+sQId+'","op":"in"}]}',
								sqObj:'{"tName":"'+sQTable+'","tfields":[{"field":"'+loopNameArray[1]+'"}]}',
								sqFiltersObj:'{"rules":'+JSON.stringify(globalScope.dynamicFilters)+'}'
							},
							success:function(data){
								$("#"+loopId).children(':not(:first)').remove();
								if (data !== '') $("#"+loopId).append(optionCreate(data));
								$("#"+loopId).prop("disabled",false);
								$("#"+loopId).trigger("chosen:updated");
							}
						})
					} else {
						$("#"+SelectsArray[i].id).prop("disabled",false);
						$("#"+SelectsArray[i].id).trigger("chosen:updated");
					}
				})(i);
			}
		}
	} else if(processGrid === false && change === true){
		var SelectsArray = $('.filterselection');
		SelectsArray.prop("disabled",true);
		SelectsArray.trigger("chosen:updated");
		for (i=0; i < SelectsArray.length; i++) {
			(function(i){
				if ($(SelectsArray[i]).val() == ''){
					var loopId = SelectsArray[i].id;
					var loopNameArray = (SelectsArray[i].name).split(',');
					$.ajax({
						type:'post',
						url:'php/selects',
						data:{qObj:'{"type":"SELECT","order":"'+globalOrder+'","id":"true"}',
							qValuesObj:'{"tName":"'+ loopNameArray[0] +'","tfields":[{"field":"'+libId+'"},{"field":"'+loopNameArray[2]+'"}]}',
							qFiltersObj:'{"rules":[{"field":"'+sQId+'","op":"in"}]}',
							sqObj:'{"tName":"'+sQTable+'","tfields":[{"field":"'+loopNameArray[1]+'"}]}',
							sqFiltersObj:'{"rules":'+JSON.stringify(globalScope.dynamicFilters)+'}'
						},
						success:function(data){
							$("#"+loopId).children(':not(:first)').remove();
							if (data !== '') { $("#"+loopId).append(optionCreate(data)) }
							$("#"+loopId).prop("disabled",false);
							$("#"+loopId).trigger("chosen:updated");
						}
					})
				} else {
					$("#"+SelectsArray[i].id).prop("disabled",false);
					$("#"+SelectsArray[i].id).trigger("chosen:updated");	
				} 
			})(i);
		}
	}
}
/*
 * chBx/radio actions function
 * 
 * 
 * 
 */
function gridFilterCheckBox(consDynSelArray){
	if($.active > 0) return; // jQuery yet unknown stuff to prevent clicks when smth already running.
	var getIndex,fields,vals,ops,indexToSplice,checkBoxObject = $(this).prev('input[type=checkbox]');
	checkBoxObject.prop('checked',checkBoxObject.is(':checked') ? false : true);
	if((checkBoxObject.attr('field')).indexOf(',') > 0){
		fields = checkBoxObject.attr('field').split(',');
		vals = checkBoxObject.attr('vals').split(',');
		oper = checkBoxObject.attr('op').split(',');
		ops = checkBoxObject.attr('ops');
	} else {
		fields = checkBoxObject.attr('field');
		vals = checkBoxObject.attr('vals');
		oper = checkBoxObject.attr('op');
	}
	if(checkBoxObject[0].checked === true){
		if (typeof(fields) === 'object' && fields.length > 0) {
			addCbFilter(fields,oper,vals,ops);
			constructDynamicSelects.apply(null,consDynSelArray);
		} else {
			if(fields === 'Удалено') {
				$.grep(globalScope.dynamicFilters,function(el,z){ 
					if (el.field === fields) getIndex = z;
				})
			} else if (fields === 'Статус_Код' && oper === 'ge' && vals === '0') {
				$.grep(globalScope.dynamicFilters,function(el,z){ 
					if (el.field === fields && el.op === 'lt' && el.data === '100') getIndex = z; 
				})
			}
			if (typeof(getIndex) !== 'undefined'){ (globalScope.dynamicFilters).splice(getIndex,1); }
			addCbFilter(fields,oper,vals);
			constructDynamicSelects.apply(null,consDynSelArray);
		}
	} else if (checkBoxObject[0].checked === false){
		for(i=0;i<(globalScope.dynamicFilters).length;i++){
			if(globalScope.dynamicFilters[i].field === fields && fields === 'Удалено'){
				globalScope.dynamicFilters.push({"field":"Удалено","op":"eq","data":"0"})
				indexToSplice = i;
				break;
			} else if(fields === 'Статус_Код' && oper === 'ge' && vals === '0' && globalScope.dynamicFilters[i].field === fields && globalScope.dynamicFilters[i].op === 'ge' && globalScope.dynamicFilters[i].data === '0') {
				globalScope.dynamicFilters.push({"field":"Статус_Код","op":"lt","data":"100"});
				indexToSplice = i;
				break;
			} else if(globalScope.dynamicFilters[i].field === fields && globalScope.dynamicFilters[i].op === oper && globalScope.dynamicFilters[i].data === vals){
				indexToSplice = i;
				break;
			} else if(typeof(globalScope.dynamicFilters[i].field) === 'object' && typeof(fields) === 'object' && globalScope.dynamicFilters[i].field.equals(fields) === true ){
				indexToSplice = i;
				globalScope.dynamicFilters = globalScope.dynamicFilters.concat(globalScope.defaultFilters)
				break;
			}
		}
		if(typeof(indexToSplice) !== 'undefined'){
			globalScope.dynamicFilters.splice(indexToSplice,1);
			$('#'+globalScope.gridName)[0].triggerToolbar();
			constructDynamicSelects.apply(null,consDynSelArray);
		}
	}
}
