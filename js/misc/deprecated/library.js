/*
 * <option> Creator
 */
function optionCreate(value){
	var delimArray = value.split(';'),summary = new String,sepArray
	for (var i=0; i < delimArray.length; i++) {
		sepArray = delimArray[i].split(':');
		option = "<option value='"+sepArray[0]+"'>"+sepArray[1]+"</option>";
		summary += option;
	}
	return summary;
}
/*
 * Единовременное получение данных из библиотек
 */
function ajaxSelectSingle(qObj,qValuesObj,qFiltersObj,sqObj,sqFiltersObj,createObj){
	var ajaxResponce;
	$.ajax({
		url:'/php/selects',
		type:'post',
		async:false,
		cache:false,
		datatype: "json",
		data: {qObj:JSON.stringify(qObj),qValuesObj:JSON.stringify(qValuesObj),qFiltersObj:JSON.stringify(qFiltersObj),sqObj:JSON.stringify(sqObj),sqFiltersObj:JSON.stringify(sqFiltersObj)},
		success:function(data){
			ajaxResponce = data;
		}
	})
	if (typeof(createObj)!== 'undefined' && createObj.create === true){
		return optionCreate(ajaxResponce);
	} else {
		return ajaxResponce;
	}
}
/*
 * Получение данных из библиотек
 * Если уже существует в локале - запрос не выполняется
 * AjaxSelect(поле,таблица,idтаблицы,имя обьекта в локале,категория (категории товаров),добавленное значение)
 */
function AjaxSelect(storageItemName,create,qObj,qValuesObj,qFiltersObj,sqObj,sqFiltersObj){
	if (localStorage.getItem(storageItemName) === null) {
		var ajaxResponce;
		$.ajax({
			url:'php/selects',
			type:'post',
			async:false,
			cache:false,
			data: {
				qObj:JSON.stringify(qObj),
				qValuesObj:JSON.stringify(qValuesObj),
				qFiltersObj:JSON.stringify(qFiltersObj),
				sqObj:JSON.stringify(sqObj),
				sqFiltersObj:JSON.stringify(sqFiltersObj)
			},
			success:function (data) {
				localStorage.setItem(storageItemName, JSON.stringify(data));
				ajaxResponce = data;
			}
		})
		if (typeof ajaxResponce !== 'undefined') return ajaxResponce;
	} else {
		if(create === true) {
			return $.parseJSON(optionCreate(localStorage.getItem(storageItemName))); 
		} else {
			return $.parseJSON(localStorage.getItem(storageItemName)); 
		}
	}
}
/*
 * Удаление локала - название обьект в локале
 */
function removeLocalStorageObject(storageItemName) {
	if (localStorage.getItem(storageItemName) !== null) {
		localStorage.removeItem(storageItemName);
	}
}
/*
 * Тут все очень плохо. Диалог добавление в библиотеки + пересоздание локалов + пересоздание селектов в гриде.
 * Очень много черной магии, не трогать!
 * 
 */
function addDialog(local,construct,reload,gridName,ajaxArray,object){
	var offest = $(gridName).offset();
	$("<div></div>").dialog( {
    	buttons: { "Добавить": function () {
    		$.ajax({
    			type:'post',
				async: false,
				url:'php/misc',
				data:{oper:object.action,value:$('#valueToAdd').val(),addedValue:object.addedValue},
				success: function(){
					if (local === true){
						if (construct === true) {
							if($.isArray(ajaxArray)){
								$('#gs_'+ object.fieldName).html('');
								ajaxSelectSingle.apply(window[object.varName],ajaxArray);
								$(object.gridName).setColProp(object.fieldName,{searchoptions:{value:window[object.varName]},editoptions:{value:window[object.varName]}});
								$('#gs_'+ object.fieldName).multiselects('refresh');
							}
						} else {
							ajaxSelectSingle.apply(window[object.varName],ajaxArray);
						}
					} else {
						if($.isArray(ajaxArray)){
							removeLocalStorageObject(object.removeLsObject);
							window[object.varName] = AjaxSelect.apply(null,ajaxArray);
						}
					}
					reload ? $(gridName).trigger("reloadGrid") : null;
				}
    		})
    		$(this).dialog("close");  
    	}},
    	close: function (event, ui) { $(this).remove(); },
    	position:[offest.center, offest.center],
    	resizable: false,
    	title: object.titleName,
    	modal: true
  	}).append('<label>'+ object.labelName +' : </label><input id="valueToAdd" type="text">')
}