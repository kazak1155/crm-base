
if(window != window.top)
 {
	location = (parent.window.location.search).replace(/(\?reference=)|(\&.*)/gi,'');
	parent.document.title = $.capitalize(this.title || this.table.replace(/_/g,' '));
	if(document.title.length > 0)
		parent.document.title = document.title + ' - ' +parent.document.title;
 }
 else
{
	document.title = $.capitalize(this.title || this.table.replace(/_/g,' '));
	location = (window.location.href).match('\/([^.][A-Za-z0-9 _]+)\/');
	console.log(window.location,location);
	return;
	location = location[1];
}


var iframe_form = document.createElement('form'),iframe_form_rowid = document.createElement('input'), iframe_form_data = document.createElement('input');
iframe_form.action = '/misc/mail_box/';
iframe_form.method = 'post';
iframe_form.target = 'current-iframe';
iframe_form_rowid.name = 'rowid';
iframe_form_rowid.value = encodeURIComponent(source.id);
iframe_form_data.name = 'row_data';
iframe_form_data.value = encodeURIComponent(JSON.stringify(source.data));
iframe_form.appendChild(iframe_form_rowid);
iframe_form.appendChild(iframe_form_data);
/*
$('div').mousemove(function(e){
	var target = this;

	var overflowed = checkOverflow(this);
	var screen_size = {
		x:window.screen.availWidth,
		y:window.screen.availHeight
	};
	var mouse_pos = {
		x:e.clientX,
		y:e.clientY
	};
	var left_edge = screen_size.x - (screen_size.x - mouse_pos.x ),right_edge = screen_size.x - mouse_pos.x;
	var x = e.clientX,y = e.clientY;
	var pseudo_edge = 25;
	var scroll_left = $(this).scrollLeft();
	var interval;

	if($('.side-scroll-bar').length > 0 || overflowed == false)
		return;

	var bar = document.createElement('div');
	bar.style.width = pseudo_edge + 'px';
	bar.className = 'side-scroll-bar';
	$(bar)
		.mouseenter(function(e){
			var direction = $(this).attr('direction');
			interval = setInterval(function()
			{

				if(direction == 'right' && (target.scrollWidth > (target.scrollLeft + target.offsetWidth)))
				{
					bar.style.right = 0 - (scroll_left + 2) + 'px';
					target.scrollLeft = scroll_left + 2;
					scroll_left += 2;
				}
				else if(direction == 'left' && scroll_left > 0)
				{
					bar.style.left = 0 + (scroll_left - 2) + 'px';
					target.scrollLeft = scroll_left - 2;
					scroll_left -= 2;
				}
			},0);
		})
		.mouseleave(function(e){
			clearInterval(interval);
			$('.side-scroll-bar').remove();
		});



	if(overflowed == true)
	{
		if(right_edge <= pseudo_edge) // right edge
		{
			bar.style.right = 0 - scroll_left + 'px';
			$(bar).attr('direction','right');
			this.appendChild(bar);
		}
		else if(left_edge <= pseudo_edge) // left edge
		{
			bar.style.left = 0 + scroll_left + 'px';
			$(bar).attr('direction','left');
			this.appendChild(bar);
		}
	}
})
*/
/*fileTreeCallback:function(elem,$jstree,rowid,rowObject,gridPseudo,rowObjectFormatted){
			var value = $jstree.node_list.length > 1 ? '1' : '0';
			if(value == rowObjectFormatted['Документы'])
				return;
			var grid = this;
			$.ajaxShort({
				data:{
					oper:'edit',
					tname:'Заказы',
					id:rowid,
					tid:'Код',
					'Документы':value
				},
				success:function(){
					$(grid).setCell(rowid,'Документы',value);
				}
			});
		},*/
/*Autocomplete*/
afterEditCell:getAutocomplete
getAutocomplete = function(rowid,cellname,value,iRow,iCol){
	setTimeout(function() { $("#"+iRow+"_"+cellname).select().focus();},10);
	if (cellname !== 'status' && cellname !== 'currency') {
		$("#"+iRow+"_"+cellname).autocomplete({
			source:"php/autocomplete?fname="+cellname+"&tname="+table,
			delay:250,
			minLength: 2
		});
	}
}
/* Селект всего текста */
afterEditCell:selectAll,

selectAll = function(rowid,cellname,value,iRow,iCol){
setTimeout(function() {
	$("#"+iRow+"_"+cellname).select().focus();
	},10)
},
/* Калибровка размеров при изменении размера окна браузера */
$(document).ready(function() {
    prevHeight = $(window).innerHeight();
    prevWidth = $(window).innerWidth();
});

// единичный селект
beforeSelectRow: selectRowActions,
selectRowActions = function (rowid,e){
	if (globalScope.gRow == null && globalScope.gCol == null){
		if($(e.target).is("input:checkbox[id^=jqg_]")){
			$(this).jqGrid("resetSelection");
			return true;
		} else {
			return true;
		}
	} else {
		$(this).jqGrid("saveCell",globalScope.gRow,globalScope.gCol);
		$(this).jqGrid("resetSelection");
		return true;
	}
},


$(window).resize(function(){
	$(gridname).jqGrid('setGridHeight',$(window).innerHeight()-140);
	if ($('#gridcontainer').has('#ajaxtable')){
		$(gridname).jqGrid('setGridWidth',$('#ajaxtable').width());
	} else {
		$(gridname).jqGrid('setGridWidth',$('#gridcontainer').width());
	}

});

function toHex(str) {
	var hex = '';
	for(var i=0;i<str.length;i++) {
		hex += ''+str.charCodeAt(i).toString(16);
	}
	return hex;
}

function getOptions(fname,tname,resp){
	$.ajax({
		type:'post',
		async:false,
		cache:false,
		url:'php/selects',
		data:{fname:fname,tname:tname},
		success:function(data){
			resp = data;
		}
	})
	return resp;
}

function RefreshGridData() {
    var num;
    ids = new Array();
    $(".sgcollapsed").each(function () {
        num = $(this).attr('id');
        ids.push(num);
    });
    $(gridname).trigger("reloadGrid");
}
/* Открыть сабгрид обновленного грида */
var expandSubGrid = function(){
	if (globalScope.row_id !== null) {
		$(gridname).jqGrid('expandSubGridRow', globalScope.row_id)
	}
},

$beforeexpand = <<<BEFORE
function( divid, rowid)
{
  // #grid is the id of the grid
  var expanded = jQuery("td.sgexpanded", "#grid")[0];
  if(expanded) {
    setTimeout(function(){
        $(expanded).trigger("click");
    }, 200)
  }
}
BEFORE;
$grid->setGridEvent('subGridBeforeExpand', $beforeexpand);[/quote]





(function(safeLocation){
		var outcome, u, m = "You should be logged out now.";
		// IE has a simple solution for it - API:
		try {
			outcome = document.execCommand("ClearAuthenticationCache")
		}
		catch(e){

		}
		// Other browsers need a larger solution - AJAX call with special user name - 'logout'.
		if (!outcome) {
		// Let's create an xmlhttp object
			outcome = (function(x){
				if (x) {
					// the reason we use "random" value for password is
					// that browsers cache requests. changing
					// password effectively behaves like cache-busing.
					x.open("HEAD", safeLocation || location.href, true, "logout", (new Date()).getTime().toString())
					x.send("")
					// x.abort()
					return 1 // this is **speculative** "We are done."
				} else {
					return
				}
			})(window.XMLHttpRequest ? new window.XMLHttpRequest() : ( window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : u ))
		}
		if (!outcome) {
			m = "Your browser is too old or too weird to support log out functionality. Close all windows and restart the browser."
		}
		alert(m)
		// return !!outcome
	})

		pgbuttons: false,
		pgtext: null,
/*
if (localStorage.getItem('timeStamp') !== null) {
	var localTime = new Date(localStorage.getItem('timeStamp'));
	var currentTime = new Date();
	if ( currentTime > localTime ) {
		localStorage.clear();
		localStorage.setItem('timeStamp',new Date());
	}
} else if (localStorage.getItem('timeStamp') === null) {
	localStorage.setItem('timeStamp',new Date());
}
*/

/* recreate LsAfterSaveCell */
afterSaveCell:reloadGrid
reloadGrid = function(rowid, cellname, value, iRow, iCol) {
	if ( cellname == 'name' || cellname == 'sku') {
		afterCellEditStorageActions(this.id);
	}
}
function afterCellEditStorageActions (id){
	switch (id){
		case 'gridnomen':
			removeLocalStorageObject('tovarOil');
			tovar = AjaxSelect('name','dbo.tovar','tovarOil','oil','1');
			break;
		default: break;
	}
}
/* recreate LsAfterAddForm */
afterComplete:afterAddCompleteStorageActions
afterAddCompleteStorageActions = function (response, postdata, formid){
	if (postdata.tname == 'dbo.nomenclature_oil') {
		removeLocalStorageObject('tovarOil');
		tovar = AjaxSelect('name','dbo.tovar','tovarOil','oil','1');
	}
}
/* recreate LsAfterDelForm */
afterSubmit:afterSubmitDeleteCompleteStorageActions
afterSubmitDeleteCompleteStorageActions = function (response, postdata){
	if (postdata.tname == 'dbo.nomenclature_oil') {
		removeLocalStorageObject('tovarOil');
		tovar = AjaxSelect('name','dbo.tovar','tovarOil','oil','1');
	}
	return [true,"",""]
}

/* on cell select*/
onCellSelect:function(rowid,iCol){
			var cm = $('#'+this.id).jqGrid("getGridParam", "colModel");
			colName = cm[iCol];
			if (colName.name === 'tov_name'){
				tovarCard();
			}
		},
/* filterbar*/
beforeSearch:function(){
		fullPostData = $(this).jqGrid('getGridParam','postData');
		filtersPostdata = JSON.parse(fullPostData.filters);
		for (i=0;i<(filtersPostdata.rules).length;i++){
			result = $.grep(globalScope.gridAddedFilters, function (e) {  return e.field === filtersPostdata.rules[i].field; });
			if (result.length > 0){
				break;
			} else {
				globalScope.gridAddedFilters.push(filtersPostdata.rules[i]);
			}
		}
	}
/*
 change cell postdata, depricated >> added postIndex prop for colmodel
 */

 function changeGridPostObject(obj,prop,propChange){
	var objValue = obj[prop];
	delete obj[prop];
	obj[propChange] = objValue;
		deletedId = obj['id'];
		delete obj['id'];
		obj['id'] = deletedId
	return obj
}
	serializeCellData:function(postdata){
	if (postdata.hasOwnProperty('Статус')){
		return changeGridPostObject(postdata,'Статус','Статус_Код');
	} else if (postdata.hasOwnProperty('Клиент')) {
		return changeGridPostObject(postdata,'Клиент','Клиенты_Код');
	} else if (postdata.hasOwnProperty('Подклиент')) {
		return changeGridPostObject(postdata,'Подклиент','Подклиенты_Код');
	} else if (postdata.hasOwnProperty('Фабрика')) {
		return changeGridPostObject(postdata,'Фабрика','Фабрики_Код');
	} else if (postdata.hasOwnProperty('ИнСклад')) {
		return changeGridPostObject(postdata,'ИнСклад','ИнСклад_Код');
	} else if (postdata.hasOwnProperty('Пользователь')) {
		return changeGridPostObject(postdata,'Пользователь','Пользователи_Код');
	} else {
		return postdata;
	}

},























	<!--<?php require_once ("../languages/".substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2)."_language.php"); ?>-->


















	/*
<div id="buttonsWrapper">
			<button id="removeSelected" class="bFilterState"><i class="fa fa-filter" style="float:left;margin-top:1px"></i>Снять фильтры</button>
</div>
#buttonsWrapper button {
	height:25px;
	width:100%;
	vertical-align: middle;
}
.bFilterState {
	background: -moz-linear-gradient(center top , #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%) repeat scroll 0 0 padding-box rgba(0, 0, 0, 0);
	border: 1px solid #aaa;
	box-shadow: 0 0 3px white inset, 0 1px 1px rgba(0, 0, 0, 0.1);
}
.bFilterStatFiltered {
	color:#ffffff;
	background: #0073ea;
	border: 1px solid blue;
	box-shadow: 0 0 3px blue inset, 0 1px 1px rgba(0, 0, 0, 0.1);
}
*/
/* remove select all filters */
$("#removeSelected").click(function(e){
	$("#removeSelected").removeClass('bFilterStatFiltered');
	$("#removeSelected").addClass('bFilterState');
	removeSelectsFromFilters();
	$('.filterselection option:selected').removeAttr('selected');
	SelectsArray = $(".filterselection");
	for (i=0;i < SelectsArray.length;i++){ $(SelectsArray[i]).children(':not(:first)').remove(); }
	$("#hauls").append(hauls);$("#agents").append(agents);$("#subagents").append(subagents);$("#fabric").append(fabric);
	$('.filterselection').trigger('chosen:updated');
	globalScope.selectsFilters = [];
	$(gridname)[0].triggerToolbar();
})





/*
 * Мультиселект для всех форм редактирования
 */
dataInitMultiselectEdit = function (elem) {
	gridId = this.id;
	var $elem = $(elem);
	$elem.attr({'multiple':'multiple','size':'1'}); /* grid Attr fix */

	if ( ($('#'+elem.id).parent()).length !== 0) {
		parentId = ((($('#'+elem.id).parent()))[0].className).substr(0,9);
	} else {
		parentId = 'form';
	}
	inCellEdit = typeof parentId === "string" && parentId === "edit-cell" || parentId === "dirty-cell";


	inCellEdit ? $elem.width('95%'):$elem.width('459px');
	elemWidths = $elem.width();

	options = {
		appendTo: inCellEdit ? $elem.parent() : 'body',
		multiple: false,
		selectedList: 1,
		height: 150,
		minWidth:10,
		autoOpen:inCellEdit ? true :false,
		noneSelectedText: "Выберите",
		open: function () {
			inCellEdit ? $(".ui-multiselect-menu:visible").width('200px') : $(".ui-multiselect-menu:visible").width(elemWidths);
			inCellEdit ? $('.ui-multiselect-menu:visible').css({marginLeft:'2px',marginTop:'2px',top:'auto',fontSize:'1.1em',whiteSpace:'normal'}) : $('.ui-multiselect-menu:visible').css({ marginTop:'2px' });
		},
		close: function (){
			if (inCellEdit === true){
				if (($elem.multiselects("getChecked")).length == 0) {
					$elem.multiselects("destroy");
					$elem.multiselectfilter("destroy");
					$('#'+gridId).jqGrid("restoreCell",globalScope.gRow,globalScope.gCol);
					$('#'+gridId).jqGrid("resetSelection");
				} else {
					$elem.multiselects("destroy");
					$elem.multiselectfilter("destroy");
					$('#'+gridId).jqGrid("saveCell",globalScope.gRow,globalScope.gCol);
					$('#'+gridId).jqGrid("resetSelection");
				}

			}
		}
	};
	optionsFilter = {
		label:'',
		placeholder:'Введите текст',
		autoReset:true
	};
	setTimeout(function () {
	$elem.multiselects(options).multiselectfilter(optionsFilter);

	if (inCellEdit === false && globalScope.formEdit === false){
		$elem.multiselects("uncheckAll");
	}

	$elem.siblings('button.ui-multiselect').css({
		width: elemWidths,
		textAlign: "center",
		margin:'0px'
	});
	},1)
},
/*
 * Мультиселект для форм поиска без мультиселекта
 */
dataInitMultiselectNone = function (elem) {
	setTimeout(function () {
		var t = ($(elem).parents('th'));
		for(i=0;i < t.length;i++){
			$(t[i]).css({"padding":"0"});
			$(t[i]).children('div').css({"padding":"0"});
		}
		var $elem = $(elem), id = elem.id,
		inToolbar = typeof id === "string" && id.substr(0, 3) === "gs_",
		options = {
		    multiple: false ,
			selectedList: 1,
			height: 150,
			minWidth:150,
			checkAllText: "Выбрать все",
			uncheckAllText: "Снять все",
			noneSelectedText: "!",
			selectedText:'# выбрано',
			open: function () {
				var $menu = $(".ui-multiselect-menu:visible");
				inToolbar ? $menu.width("200px") : $menu.width("350px")
				return;
			}
		},
		//optionsFilter = { label:'',placeholder:'Введите текст',autoReset:true,width:150 };
		//$elem.multiselects(options).multiselectfilter(optionsFilter);

		$options = $elem.find("option");
		if ($options.length > 0 && $options[0].selected) $options[0].selected = false;
		if (inToolbar) options.minWidth = 'auto';
		$elem.multiselects(options);
		$elem.siblings('button.ui-multiselect').css({
			width: inToolbar ? "100%" : "100%",
			height: inToolbar ? "auto" : "31px",
			fontSize: "9px",
			marginTop: inToolbar ? "-3px":"0px",
			marginBottom: "1px",
			paddingTop: "3px",
			textAlign: "center"
		});
}, 1);
},
/*
 * Мультиселект для форм поиска с мультиселектом
 */
dataInitMultiselect = function (elem) {
	setTimeout(function () {
		var t = ($(elem).parents('th'));
		for(i=0;i < t.length;i++){
			$(t[i]).css({"padding":"0"});
			$(t[i]).children('div').css({"padding":"0"})
		}
		var $elem = $(elem), id = elem.id,
		inToolbar = typeof id === "string" && id.substr(0, 3) === "gs_",
		options = {
		    multiple: inToolbar ? true : false ,
			selectedList: 1,
			height: 150,
			minWidth:10,
			checkAllText: "Выбрать все",
			uncheckAllText: "Снять все",
			noneSelectedText: "!",
			selectedText:'# выбрано',
			open: function () {
				var $menu = $(".ui-multiselect-menu:visible");
				inToolbar ? $menu.width("auto") : $menu.width("350px")
				return;
			}
		},
	$options = $elem.find("option");

	inToolbar ? false : $elem.attr({'multiple':'multiple','size':'1'});

	if ($options.length > 0 && $options[0].selected) {
		$options[0].selected = false;
	}
	if (inToolbar) {
		options.minWidth = 'auto';
	}
	$elem.multiselects(options);
	$elem.siblings('button.ui-multiselect').css({
	width: inToolbar ? "100%" : "100%",
	height: inToolbar ? "auto" : "31px",
	fontSize: "9px",
	marginTop: inToolbar ? "-3px":"0px",
	marginBottom: "1px",
	paddingTop: "3px",
	textAlign: "center"
	});
}, 1);
},





/*
 * Модифицирование фильтра при мульти-фильтре
 */
modifyFilers = function () {
	modifySearchingFilter.call(this, ',');
},
var myDefaultSearch = "bw",

getColumnIndexByName = function (columnName) {
	var cms = $(this).jqGrid('getGridParam', 'colModel'), i, l = gridColModel.length;
	for (i = 0; i < l; i += 1) {
		if (cms[i].name === columnName) {
		return i; // return the index
		}
	}
	return -1;
},

modifySearchingFilter = function (separator) {
	var i, l, rules, rule, parts, j, group, str, iCol, cmi, gridColModel = this.p.colModel,
    filters = eval("(" + this.p.postData.filters + ")");
	if (filters && filters.rules !== undefined && filters.rules.length > 0) {
		for (i = 0; i < filters.rules.length; i++) {
			rule = filters.rules[i];
			iCol = getColumnIndexByName.call(this, rule.field);
			cmi = gridColModel[iCol];
			parts = rule.data.split(separator);
			if (parts.length > 1) {
				if (filters.groups === undefined) {
				filters.groups = [];
				}
				group = {
					groupOp: 'OR',
					rules: [],
					groups: []
				};
				filters.groups.push(group);
				for (j = 0, l = parts.length; j < l; j++) {
					str = parts[j];
					if (str) {
						group.rules.push({
						field: rule.field,
						op: rule.op,
						data: parts[j]
						});
					}
				}
			filters.rules.splice(i, 1);
			i--;
			}
		}
		this.p.postData.filters = JSON.stringify(filters);
	}
};
/*
 * Ф-ия синхронизации - (иморт / экспорт,группа товара)
 */
syncDataBases = function(type,group) {
	$.ajax({
		url:'php/misc',
		type:'post',
		async:false,
		data:{oper:type,group:group},
		success:function() {
			destroy();
			if(typeof menuItemName !== 'undefined'){  menuItemName.trigger('click'); }
			$.alert('Синхронизация успешно завершена','')
		},
		error:function(){ $.alert('Синхронизация не удалась! Обратитесь к системному администратору.') }

	})
}
/* Карточка товара */
tovarCard = function(e){
	var offset = $('html').offset();
	selrs = $('#'+this.id).jqGrid('getGridParam','selarrrow');
	if ((selrs.length) > 1){
		$.alert('Выделено несколько записей. Добавление невозможно.','Внимание!')
	} else {
		selr = $('#'+this.id).jqGrid('getGridParam','selrow');
		if (selr == null ) {
				var alertIDs = {themodal: 'alertmod_' + this.p.id, modalhead: 'alerthd_' + this.p.id,modalcontent: 'alertcnt_' + this.p.id};
				$.jgrid.viewModal("#"+alertIDs.themodal,{gbox:"#gbox_"+$.jgrid.jqID(this.p.id),jqm:true});
		} else {
			var id = $('#'+this.id).getCell(selr, 'id_tovar');
			$('<div id="tovarCardDialog"></div>').dialog({
				buttons: { "Закрыть": function () {
					$(this).dialog("close");
				}},
				open: function( event, ui ) {
					$.ajax({
						type:'post',
						url:'php/misc',
						async:false,
						data:{oper:'getTovarCard',tovarId:id},
						success:function(data){
							var arrayData = $.parseJSON(data);
							var table = $('<table style="width:100%;text-align:center" class="ui-widget-content"><tr></tr></table>');
							for (i=0;i < arrayData.length;i++){
								if (i > 0) {
									table.append('<td style="border-left:1px solid #dddddd;">'+ arrayData[i] +'</td>');
								} else {
									table.append('<td>'+ arrayData[i] +'</td>');
								}

							}
							$('#tovarCardDialog').append(table);
						}
					})
				},
				close: function (event, ui) { $(this).remove(); },
				position:[offset.center, offset.center],
				resizable: false,
				title:'Карточка товара',
				width:'600',
				modal:true
			})
		}
	}
}

/*
 * Все плохо.
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
$.extend({
	uploadFile : function(form,target) {
		if(jQuery().ajaxSubmit)
		{
			var options =
			{
				resetForm:true,
				target: target,
				beforeSubmit : function(arr, $form, options)
				{
					if (window.File && window.FileReader && window.FileList && window.Blob)
					{
						if(!$form.find('input[type="file"]').val())
						{
							target.html('No file selected');
							return false;
						}
						if($form.find('input[type="file"]')[0].files[0].size > 10000000) // ~10mb
						{
							target.html('Too big file.');
							return false;
						}
						target.html('');
					}
					else
					{
						$.alert('Please upgrade your browser!');
						return false;
					}
				},
				success:function(responseText,statusText,xhr,$form)
				{
					$form.trigger('reset');
					$form.removeAttr('action');
					$form.find('input[type="file"]').trigger('change');
				}
			}
			$(form).ajaxSubmit(options);
		}
		else $.alert('Form plugin not loaded!');
	}
});
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
var disabled = !options.colModel.editable || options.colModel.formatoptions.disabled ? true : false;
	var inputControl = '';
	var fa_class,checked;

	if(typeof action === typeof undefined && options.rowId !== 'blank')
		cellvalue = '0';

	if(typeof cellvalue !== typeof undefined && options.rowId !== 'blank')
	{

		checked = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? true: false;
		fa_class = disabled ? checked ? 'fa-check-square' : 'fa-square' : checked ? 'fa-check-square-o' : 'fa-square-o';
		inputControl = '<i class="fa '+ fa_class +' fa-lg" onclick="faCheckBoxEdit.call(this,&apos;'+ options.rowId + '&apos;,&apos;'+options.colModel.name+'&apos;,&apos;'+'#'+this.id+'&apos;)"></i> ';
		/*var inputControl = '<input '+disabled+' id="'+ options.rowId +'_'+ options.colModel.name +'_t" class="view" name="' +options.colModel.name +'" style="width:100%" type="checkbox" ' + checked + ' value="' + cellvalue + '" onclick="MakeCellEditable.call(this,&apos;'+ options.rowId + '&apos;,&apos;'+options.colModel.name+'&apos;,&apos;'+'#'+this.id+'&apos;)" />'*/

	}
