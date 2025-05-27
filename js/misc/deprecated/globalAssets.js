// globals
var globalScope = {}, mainGridRowId = [];
// Коды поиска cn=contains;ne=notEqual;eq=equal;bw=beginsWith
var sopCodes = ['cn','ne','eq'];
// datepicker russian lang
// depricated
//$.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
$.datepicker._gotoToday = function(id) 
{
	$(id).val(getDateRu());
	this._hideDatepicker($(id)[0]);
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
 *  alert pseudo-widget
 *	message - Текст сообщения
 *	title - Текст названия
 */
$.extend({ alert: function (message,title) 
	{		
		$("<div style='min-width:80px;font-size:13pt'></div>")
			.append(message).dialog
			({
				buttons: 
				{ 
					"Ok": function () 
					{ 
						$(this).dialog("close"); 
					}
				},
				close: function (event, ui) 
				{ 
					$(this).remove(); 
				},
				resizable: false,
				title: (typeof title !== typeof undefined) ? title : 'Внимание!',
				modal: true,
				width:'auto'
			});
	}
});
/*
 *  confirm pseudo-widget
 *  title - Текст названия
 *	message - Текст сообщения
 *	okAction - function(){}
 *	noAction - function(){}
 */
$.extend({
	confirm: function(title,message,okAction,noAction)
	{
		$("<div style='font-size:13pt'></div>")
		.text(message)
		.dialog
		({
			open: function(event, ui) 
			{ 
				$(this).parent().find(".ui-dialog-titlebar-close").hide(); 
			}, 
			buttons: 
			{
				'Да': function() 
				{
					if(typeof okAction !== typeof undefined) 
						okAction.call(this);
					$(this).dialog("close");
				},
				'Нет': function()
				{
					if(typeof noAction !== typeof undefined)
						noAction.call(this);
					else 
						$(this).dialog("close");
				}
			},
			close: function(event, ui)
			{ 
				$(this).remove();
			},
			resizable: false,
			title: title,
			modal: true,
			width: 'auto'
		});
	}
});
/*
 * inputBox pseudo-widget
 * title - Текст названия
 * okAction - function(){}
 * html - appended html
 * noAction - function(){}
 */
$.extend({
	inputBox:function(title,okAction,html,noAction)
	{
		$("<div style='font-size:13pt'></div>")
		.append(html)
		.dialog
		({
			open: function(event, ui)
			{ 
				$(this).parent().find(".ui-dialog-titlebar-close").hide();
			}, 
			buttons:
			{
				'Да': function() 
				{
					if(typeof okAction !== typeof undefined) 
						okAction.call(this); 	
				},
				'Нет': function()
				{
					if(typeof noAction !== typeof undefined)
						noAction.call(this);
					else
						$(this).dialog("close");
				}
			},
			close: function(event, ui)
			{ 
				$(this).remove();
			},
			resizable: false,
			title: title,
			modal: true,
			width: '600px'
		});
	}
})
/*
 * mailBox pseudo-widget
 * mailid = шаблон письма
 * grid = id таблицы грида
 */
$.extend({
	mailBox:function(mailid,grid)
	{
		if(typeof grid !== typeof undefined)
		{
			var selrow_id = $(grid).jqGrid ('getGridParam', 'selrow'),
			selrow_data = $(grid).getRowData(selrow_id);
		}
		var mailOptions = optionCreate(ajaxSelectSingle({type:'SELECT',order:'2 DESC',id:true},{tName:'dbo.[Письма - Шаблоны]',tfields:[{'field':'Код'},{'field':'Название'}]})),
		style = '<style>'+
		'.mailWrapper {	margin-top:5px; width:500px; float:left; }'+
		'.mailWrapper label{ display: inline-block;	border-radius: 5px 0px 0px 5px; padding:5px; width:150px; }'+
		'.mailWrapper input{ outline:none; width:320px; border-radius: 0px 5px 5px 0px; padding:5px; }'+
		'.mailWrapper textarea[name="msg"]{ border-radius: 5px;width:490px;height:250px;resize:vertical }'+
		'</style>',
		html = '<div class="mailWrapper"><span style="width:180px;display:inline-block">Шаблон письма: </span><select id="mailId">'+mailOptions+'</select></div>'+
					'<div class="mailWrapper"><label class="niceLabel_v1">Кому :</label><input class="niceInput_v1 required" type="email" name="tomail"/></div>'+
					'<div class="mailWrapper"><label class="niceLabel_v1">От кого :</label><input class="niceInput_v1 required" type="email" name="frommail"/></div>'+
					'<div class="mailWrapper"><label class="niceLabel_v1">Тема :</label><input class="niceInput_v1" type="text" name="subject"/></div>'+
					'<div class="mailWrapper"><label style="width:100%;display: inline-block;">Текст письма :</label><textarea name="msg"></textarea></div>'+
					'<div class="mailWrapper"></label><input type="file" name="file"/></div>',
		responce,mail
				
		$("<div style='font-size:13pt' id='mailForm'></div>")
		.append(style+html)
		.dialog
		({
			open: function(event, ui)
			{ 
				$(".ui-dialog-titlebar-close").hide();
			}, 
			buttons:
			{
				'Отправить': function() 
				{
					var proceed = true;
					$('.mailWrapper input[class="required"]').each(function(e)
					{
						if(!$.trim($(this).val()))
						{
							$(this).css('border-color','red');
							proceed = false;
						}
						if($(this).attr("type")=="email" && !email_reg.test($.trim($(this).val())))
						{
							$(this).css('border-color','red');
							proceed = false;
						}
					})
					if(proceed === true)
					{
						//data to be sent to server         
						var mail_data = new FormData();    
						mail_data.append( 'tomail', $('input[name=tomail]').val());
						mail_data.append( 'frommail', $('input[name=frommail]').val());
						mail_data.append( 'subject', $('input[name=subject]').val());
						mail_data.append( 'msg', $('textarea[name=msg]').val());
						mail_data.append( 'file_attach', $('input[name=file]')[0].files[0]);
						$.ajax({
							url: '/php/sendmail',
							data: mail_data,
							contentType: false,
							processData: false,
							type: 'POST',
							dataType:'json',
							success: function(response){
								$.alert(response.text,'');
							}
						});
					}
				},
				'Закрыть': function()
				{
					$(this).dialog("close");				
				}
			},
			close: function(event, ui)
			{ 
				$(this).remove();
			},
			resizable: false,
			title: 'Отправка письма',
			modal: true,
			width: '530px'
		})
		$('#mailId').change(function(e)
		{
			shortAjax(
				false,
				{print:true,array:false},
				'SELECT Тема,Текст FROM dbo.[Письма - Шаблоны] WHERE Код = '+ $(this).val(),
				true,
				function(data)
				{
					responce = JSON.parse(data);
					$('input[name="subject"]').val(responce['Тема']);
					$('textarea[name="msg"]').val(responce['Текст']);
				}
			)
		})
		if(typeof mailid !== typeof undefined)
		{
			$('#mailId').val(mailid);
			shortAjax(
				false,
				{print:true,array:false},
				'SELECT Тема,Текст FROM dbo.[Письма - Шаблоны] WHERE Код = '+ mailid,
				true,
				function(data)
				{
					responce = JSON.parse(data);
					if (typeof selrow_data !== typeof undefined)
					{
						if(mailid === 38 && selrow_data['Фабрики_Код'] !== '')
						{
							shortAjax(
								false,
								{print:true,array:false},
								'SELECT Email FROM dbo.Контрагенты WHERE Код = '+ selrow_data['Фабрики_Код'],
								true,
								function(data)
								{
									var mail = JSON.parse(data);
									if(mail['Email'] !== 'undefined' && mail['Email'] !== null)
									{
										mail['Email'] = mail['Email'].replace("\n",";");
										$('input[name="tomail"]').val(mail['Email']);
									} 
									else
									{
										$('input[name="tomail"]').val('Почта не указана!');
									}
								}
							)
							for(var prop in selrow_data)
							{
								if(responce['Тема'].indexOf('%'+prop+'%') > -1) responce['Тема'] = responce['Тема'].replace('%'+prop+'%',selrow_data[prop]);
								if(responce['Текст'].indexOf('%'+prop+'%') > -1) responce['Текст'] = responce['Текст'].replace('%'+prop+'%',selrow_data[prop]);
							}
							shortAjax(
								false,
								{print:true,array:false},
								'SELECT TOP 1 Код FROM dbo.Пользователи WHERE (SID = SUSER_SID())',
								true,
								function(data)
								{
									var user = JSON.parse(data);
									switch(user['Код'])
									{
										case '3':
											responce['Текст'] = responce['Текст'].replace('%Name%','Dudko Yana');
											responce['Текст'] = responce['Текст'].replace('%Skype%','');
											responce['Текст'] = responce['Текст'].replace('%Mob%','');
											$('input[name="frommail"]').val('y.dudko@tinvest-group.ru');
											break;
										case '6':
											if(selrow_data['Клиенты_Код'] === '150'){
												responce['Текст'] = responce['Текст'].replace('%Name%','Dudko Yana');
												responce['Текст'] = responce['Текст'].replace('%Skype%','');
												responce['Текст'] = responce['Текст'].replace('%Mob%','');
												$('input[name="frommail"]').val('y.dudko@tinvest-group.ru');
											} else {
												responce['Текст'] = responce['Текст'].replace('%Name%','Darya Navilnikova');
												responce['Текст'] = responce['Текст'].replace('%Skype%','Mob: 007-916-199-2373');
												responce['Текст'] = responce['Текст'].replace('%Mob%','Skype: navilnichek');
												$('input[name="frommail"]').val('via@tinvest-group.ru');
											}
											break;
										default:
											responce['Текст'] = responce['Текст'].replace('%Name%','Darya Navilnikova');
											responce['Текст'] = responce['Текст'].replace('%Skype%','Mob: 007-916-199-2373');
											responce['Текст'] = responce['Текст'].replace('%Mob%','Skype: navilnichek');
											$('input[name="frommail"]').val('via@tinvest-group.ru');
											break;
									}
								}
							)
						}
						else
						{
							$('#mailForm').dialog( "destroy" );
							$.alert('Не указана фабрика!');
						}
					}
					$('input[name="subject"]').val(responce['Тема'])
					$('textarea[name="msg"]').val(responce['Текст'])
				}
			);
		}
		$('#mailId').chosen({disable_search: true});
	}
})
/*
 * close rewrite - unknown shit
 */
$.widget("ui.autocomplete", $.ui.autocomplete,
	{
		_close: function (event) 
		{
			if (event !== undefined && event.keepOpen === true) 
				return true;
				
			return this._super(event);
		}
	}
);
/*
 * autocomplete select
 * $(element).combobox();
 */

$.widget( "custom.combobox", {
	_create:function(){
		this.wrapper = $("<div>")
			.css({"display":"flex"})
			.insertAfter(this.element);
			this.element.hide();
			this._createAutocomplete();
			this._createShowAllButton();
	},
	_createAutocomplete: function() {
		var selected = this.element.children( ":selected" );
		var value = selected.val() ? selected.text() : "";
		
		this.maxRepSize = 15;
		this.input = $( "<input class='autocompleteInput' name='"+this.element[0].name+"'>" );
		
		if(this.element[0].disabled === true)
			this.input.attr('disabled','disabled');
		
		this.input.appendTo( this.wrapper )
			.val( value )
			.attr( "title", value)
			.autocomplete({
				autoFocus:true,
				delay: 0,
				minLength: 0,
				open:function(event, ui){
					var wd =  $(this).parent().outerWidth() - 3,
					pos = parseInt($(window).height()) - parseInt($(this).offset().top),
					inputPos = $(this).offset(),
					top = inputPos.top,
					liCount = $(this).autocomplete('widget').find('li').length;
					
					for(var i = 0;i < liCount; i ++)
					{
						if(i <= 11)	
							top = top - 22;
					}
					if(pos < 300) 
						$(this).autocomplete('widget').css('top',top +'px');
						
					$(this).autocomplete('widget').css({
						'z-index':10000,
						"width":wd+"px",
						"min-height":"10px",
						"max-height":"250px",
						"height":"auto"
					});
					return false;
				},
				create:function(event, ui){
					if(this.disabled !== true)
						$(this).select().focus();	
				},
				source: $.proxy( this, "_source" ),
				messages: {noResults: '',results: function() {}},
				select:$.proxy( this, "_select" ),
				focus:$.proxy( this, "_focus" ),
				response:$.proxy( this, "_response")
			}).keydown(function(e){
				var caret = this.selectionStart,vl = this.value.length
				if(e.keyCode === 8)
					this.setSelectionRange(caret - 1,vl )
			}); 
		
		
		this._on( this.input, {
			autocompleteselect: function( event, ui ) {
				ui.item.option.selected = true;
				this._trigger( "select", event, {
					item: ui.item.option
				});
			},
			autocompletechange: "_removeIfInvalid"
		});
		
		this.input.data("uiAutocomplete").menu._isDivider = function( item ) {
			if ( !/[^\-\u2014\u2013\s]/.test( item.text() ) ) {
				item.css("height","15px")
			}
			return false;
		}
	},
		
	_createShowAllButton: function() {
		var input = this.input,
		wasOpen = false,
		t = this;
		$( "<span>" )
			.attr( "tabIndex", -1 )
			.attr( "title", "Отобразить список" )
			.appendTo( this.wrapper )
			.button({ icons: { primary: "ui-icon-triangle-1-s alwaysBlack" }, text: false })
			.removeClass( "ui-corner-all" )
			.addClass( "custom-combobox-toggle ui-corner-right" )
			.mousedown(function() { wasOpen = input.autocomplete( "widget" ).is( ":visible" ); })
			.click(function() {
				if(input.attr('disabled') === 'disabled')
					return false;
				input.focus();
				
				if ( wasOpen ) 
					return;
					
				input.autocomplete("search","");
			});
	},
	_source: function( request, response ) {
		var matcher = new RegExp('^'+$.ui.autocomplete.escapeRegex(request.term)+'$', "i");
		var mathcerBw = new RegExp('^'+$.ui.autocomplete.escapeRegex(request.term), "i");
		var matcherCn = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
		var select_el = this.bindings[0];
		var rep = new Array();
		var maxRepSize = this.maxRepSize;
		
		
		var grid = $(this.options.grid);
		var grid_sel_options = grid.getColProp(select_el.name);
		var grid_sel_value = (grid_sel_options.editoptions.value).split(';');
		
		for (var i = 0; i < grid_sel_value.length; i++)
		{
			var pseudo_option = grid_sel_value[i].split(':')
			
			var text = pseudo_option[1];
			
			if (request.term && matcher.test(text)){
				rep.unshift({
					label: text,
					value: text,
					option: pseudo_option[0]
				});
			} else if(!request.term || mathcerBw.test(text)){
				rep.push({
					label: text,
					value: text,
					option: pseudo_option[0]
				});
			}			
			if (rep.length > maxRepSize) {
				rep.push({
					label: "... больше доступно",
					value: "maxRepSizeReached",
					option: ""
				});
				break;
			}
		}
		response(rep);
	},
	_response:function(event, ui){
		// select first empty value
		if(event.target.value === '') this.bindings[0].selectedIndex = 0;
		else {
			if(ui.content.length > 0){
				var $t = this,caret = $t.input[0].selectionStart,sug = ui.content[0].value;
				$t.input.val(sug);
				$t.input[0].setSelectionRange(caret,sug.length);
				$t.bindings[0].selectedIndex = ui.content[0].option.index;
			}
		}
	},
	_select: function (event, ui) {
		var input = this.input,scroll = input.autocomplete( "widget" ).scrollTop();
		
		if (ui.item.value == "maxRepSizeReached") {
			$.extend(event.originalEvent,{keepOpen:true});
			this.maxRepSize += 15;
			input.autocomplete("search","");
			input.autocomplete( "widget" ).scrollTop(scroll);
			return false;
		} else {
			var option = this.element.children('option[value="'+ui.item.option+'"]'),new_option
			if(option.length === 0)
			{
				new_option =  document.createElement('option');
				new_option.value = ui.item.option;
				new_option.innerHTML = ui.item.value;
				
				this.element.append(new_option);
			}
			else
			{
				new_option = this.element.children('option[value="'+ui.item.option+'"]')[0]
			}

			ui.item.option = new_option;
			ui.item.option.selected = true;
			this._trigger("selected", event, {
				item: new_option
			});

			// if event invoked by click on li element, press enter
			var inputClass = $(event.currentTarget).parents('td').attr("class")
			if(typeof event.originalEvent !== typeof undefined && typeof inputClass !== typeof undefined && inputClass.indexOf('edit-cell') >= 0){
				input.autocomplete("close");
				input.trigger($.Event( 'keydown', { keyCode:13 } ))
			}
		}
	},
	
	_focus: function (event, ui) {
		if (ui.item.value == "maxRepSizeReached") {
			return false;
		}
	},
	
	_removeIfInvalid: function( event, ui ) {
		if ( ui.item ) return;
		var value = this.input.val(),
		valueLowerCase = value.toLowerCase(),
		valid = false;
		this.element.children( "option" ).each(function() {
			if ( $( this ).text().toLowerCase() === valueLowerCase ) {
				this.selected = valid = true;
				return false;
			}
		});
		
		if ( valid ) return;
	},
	_destroy: function() {
		this.wrapper.remove();
		this.element.show();
	},
	refresh:function(){ 
		selected = this.element.children( ":selected" );
		this.input.val(selected.text());
	}
});
/*
 * Submit form
 */
$(document).on('keyup change','form.submitForm :input',function(e,prevent)
{
	var $t = $(this).parents('form.submitForm');
	var grid,table,tid = $t.attr('nameid'),id,name = this.name,value = this.value,data = {};
	
	$t.attr('name').length > 0 ? table = $t.attr('name') : table = undefined;
	$t.attr('gridlink').length > 0 ? grid = $t.attr('gridlink') : grid = undefined;
	
	tid !== null ? id = $('[name='+tid+']',$t).val() : id = undefined;
	// only for inputs
	
	var sendData = function(){
		clearTimeout($.data(this, 'timer'));
		var wait = setTimeout(function(){
			data = {oper:'edit',tname:table,tid:tid,id:id};
			data[name] = value;
			$.ajax({
				type:'post',
				url:'/php/rowedit',
				data:data,
				success:function(data){
					if(typeof grid !== typeof undefined)
					{
						var gridData = $('#'+grid).jqGrid('getRowData',id);
						for (items in gridData){
							if(items === name)
								gridData[items] = value
						}
						$('#'+grid).jqGrid('setRowData',id,gridData);
					}
					else 
					{
						$.alert('No grid specified!');
					}
					if(data.length > 0){
						$.alert(data);
					}
				}
			})
		}, 500);
		$(this).data('timer', wait);
	}
	
	if(e.type === 'keyup')
	{
		if(typeof table !== typeof undefined && id !== typeof undefined)
		{
			if(id.length > 0)
			{
				sendData();
			}
			else 
			{
				$.alert(app.noRowSelected);
			}
		}
		else 
		{
			$.alert('Table or id name not defined');
		}
	}
	// filter SELECT Change
	else if (e.type === 'change' && (this.tagName === 'SELECT' || this.type === 'checkbox'))
	{
		if(this.type === 'checkbox')
		{
			var formCheckBoxes = $t.find('[type="checkbox"]').not(this);
			var cbClass = this.className,cbClassArr = cbClass.split(' ');
			
			if(cbClass.length > 0 )
			{
				if(cbClassArr.length > 1)
				{
					for(var i = 0 ;i < cbClassArr.length;i++)
					{
						for(var y = 0 ;y < formCheckBoxes.length;y++)
						{
							if(formCheckBoxes[y].className.length > 0 && $(formCheckBoxes[y]).hasClass(cbClassArr[i])){
								if(formCheckBoxes[y].checked === true)
								{
									$(this).attr('checked', false);
									return false;
								}	
							}
						}
					}
				}
				else 
				{
					for(var i = 0 ;i < formCheckBoxes.length;i++)
					{
						if(formCheckBoxes[i].className.length > 0 && $(formCheckBoxes[i]).hasClass(cbClass)){
							if(formCheckBoxes[i].checked === true)
							{
								$(this).attr('checked', false);
								return false;
							}	
						}
					}
				}
				
			}
			value = Number(this.checked);
		}
		if(prevent !== true){
			sendData();
		}
	}
})
/* 
 * array.equals
 * Сравнение массивов
 */
Array.prototype.equals = function (array) 
{
	if (!array) 
		return false;
	if (this.length != array.length)
		return false;
	
	for (var i = 0, l=this.length; i < l; i++)
	{
		if (this[i] instanceof Array && array[i] instanceof Array)
		{
			if (!this[i].equals(array[i]))
				return false;
		} 
		else if (this[i] != array[i])
			return false;   
	}
	return true;
}
function lang(param)
{
	return app[param];
}

/*
 * 
 * 
 * 
 */
function ClearSomeLocalStorage(startsWith)
{
	var myLength = startsWith.length;
	Object.keys(localStorage).forEach(function (key)
	{
		if (key.substring(0, myLength) == startsWith)
			localStorage.removeItem(key);
	});
}
/*
 * 
 * 
 * 
 * 
 */
function gridTools(pasteRow,delRow,disableCutCopy,customOptions)
{
	var $t = $(this), // jquery grid obj;
	t = this, // grid obj
	rows = $t.find('tr[role="row"].jqgrow'),
	colmodel = $t.jqGrid("getGridParam", "colModel"),
	rowid, // selected rowId
	val, // selected value
	// val for copy/paste globalScope.copyVal 
	column, // selected column
	columnOptions, // grid column object
	iCol,iRow, // cell iCol|iRow
	rowdata; // selected row object
	
	var isFirefox = typeof InstallTrigger !== 'undefined';
	var custom = typeof customOptions !== 'undefined';
	
	$(this).contextmenu({
		autoFocus:true,
		delegate:rows,
		preventSelect:false,
		taphold:true,
		ignoreParentSelect:true,
		preventContextMenuForPopup:true,
		show:{ effect: "slideDown", duration: 50},
		hide:{ effect: "fadeOut", duration: 50},
		menu:[
			{title:"<span style='color:red;font-weight:bolder'>Ваш браузер не поддерживает копирование в буфер обмена!</span>",cmd:"client_info",uiIcon:"ui-icon-notice",noHover:true},
			{title: "Вырезать", cmd: "cut", uiIcon: "ui-icon-scissors"},
			/*{title: "Копировать в буфер", cmd: "copyReal", uiIcon: "ui-icon-copy"},*/
			{title: "Копировать значение", cmd: "copySingle", uiIcon: "ui-icon-copy"},
			{title: "Копировать строку", cmd: "copyRow", uiIcon: "ui-icon-copy"},
			{title: "Вставить", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: true },
			{title: "Удалить", cmd: "del", uiIcon: "ui-icon-trash"},
			{title: "----"},
			{title: "Дополнительно",cmd:'custom',children : custom ? customOptions:undefined},
			{title: "Прочее", children: [
				{title: "Сортировка от А до Я", cmd: "sort_asc",uiIcon:"ui-icon-triangle-1-s"},
				{title: "Сортировка от Я до А", cmd: "sort_desc",uiIcon:"ui-icon-triangle-1-n"}
			]},
			{title: "Равно", cmd: "filter_eq"},
			{title: "Не равно", cmd: "filter_ne"},
			{title: "Содержит", cmd: "filter_cn"},
		],
		select: function(event, ui) {
			var oper = ui.cmd; // context menu cmd
			
			rowid = ui.target.parents('tr')[0].id; // id redef just to be sure...
			rowdata = $t.jqGrid('getRowData',rowid); // row data def
			
			// define value of clicked element
			// if target is TD
			if(ui.target.context.tagName === 'TD') {
				// if target TD have checkbox get checkbox value, instead of TD-s text
				if(ui.target.find('input[type="checkbox"]').length > 0)	{
					val = ui.target.find('input[type="checkbox"]').val();
				} else {
					val = ui.target.text();
				}
				// column name defined as TD-s attr 'aria-describedby'
				column = ui.target.attr('aria-describedby').replace($t[0].id+'_','');
			} else {
				val = ui.target.val();
				column = ui.target.attr('name');
			}
			
			// run loop to get column object
			for(var i = 0;i < colmodel.length;i++){
				// check if this column exist in grid
				if(colmodel[i].name === column){
					// get options of target column
					columnOptions = colmodel[i];
				}
			}
			if(oper.indexOf('filter') >= 0){
				// if search type is select
				if(columnOptions.stype == 'select'){
					/* 
					// depricated: select plugin changed to select2 and data changed from local to remote
					// search value of select option by text
					var selval = $('#gs_'+column+' option').filter(function() {
						return $(this).text() === val;
					}).first().attr("value");
					// if found re-define val variable
					if(typeof selval !== typeof undefined) val = selval
					// if value is only white spaces
					if(!val.trim()) {
						val = null;
						// set toolbar value
						$('#gs_'+column).val(val);
					} else {
						// set toolbar value
						$('#gs_'+column).val(val);
						// $.selectmenu refresh
						$('#gs_'+column).selectmenu( "refresh" );
					}
					*/
					$.ajax({
						url: '/php/libs',
						type: 'POST',
						dataType: 'json',
						async:false,
						cache:false,
						data:
						{
							search:val,
							info:JSON.stringify({
								tname : $('#gs_'+column).attr('tname'),
								flds : ($('#gs_'+column).attr('flds')).split(','),
								sfld : $('#gs_'+column).attr('sfld'),
								order : $('#gs_'+column).attr('order'),
								idz : $('#gs_'+column).attr('idz')
							})
						},
						success: function (data) 
						{
							$('#gs_'+column).append(optionCreate(data.results[0].id + ':'+data.results[0].text))
							$('#gs_'+column).val(data.results[0].id).trigger("change");
							$('#gs_'+column).select2('val',data.results[0].id);
						}
					})
				} else {
					// set toolbar value
					if(!val.trim()){
						val = null;
						$('#gs_'+column).val(null);
					} else
						$('#gs_'+column).val(val);
				}
				// define current oper for filters
				var operand = oper.replace('filter_','');
				// check if column is data type and re-define operand if so
				if(column.indexOf('data') >= 0 || column.indexOf('Дата') >= 0 || column.indexOf('дата') >= 0){
					switch(operand){
						case 'eq': operand = 'dateEq'; break;
						case 'ne': operand = 'dateNe'; break;
						case 'cn': operand = 'dateEq'; break;
						default: break;
					}
				}
				// define grid post data
				var gridData = $t.jqGrid('getGridParam','postData');
				if(gridData.hasOwnProperty('filters')){
					// convert string of grid filters to object
					var filters = JSON.parse(gridData['filters']);
				} else
					var filters = {groupOp:globalScope.groupOp,rules:[]};
				// add new filter to array of filter objects
				filters['rules'].push({"field":column,"op":operand,"data":val});
				// replace grid filters with new
				gridData['filters'] = JSON.stringify(filters);
				// set new grid postData + set search true for clearing that filter
				$t.jqGrid('setGridParam',{
					postData:gridData,
					search:true
				})
				// call refresh grid
				refreshGrid.call($t);
			// if cell is editable and grid got cellEdit
			} else {
				iRow = ui.target.parents('tr')[0].rowIndex; // define iRow by rowIndex
				iCol = $.jgrid.getCellIndex(ui.target); // define iCol by getCellIndex method
				var cellId = '#'+iRow+'_'+column; // get cell id
				switch(oper){
					case 'cut':
						$t.editCell(iRow,iCol,true);
						document.execCommand('Copy');
						$t.restoreCell(iRow,iCol);
						// cut value from cell
						// dont cut checkboxes
						if(columnOptions.edittype !== 'checkbox'){
							// define copyVal
							globalScope.copyVal = val;
							// open cellEdit
							$t.jqGrid('editCell',iRow,iCol,true);
							// remove value from cell
							if($(cellId).length > 0)
							{
								$(cellId).val('NULL');
							}
							// saveCell
							$t.jqGrid('saveCell',iRow,iCol);
							// enable paste
							$t.contextmenu('enableEntry', 'paste', true);	
						} else {
							$.alert('Действие запрещено!');
						}		
						break;
					case 'paste':
						// single value copied
						if(typeof globalScope.copyVal === 'string'){
							if(rowid !== 'blank'){
								$t.jqGrid('editCell',iRow,iCol,true);
								if($(cellId).length > 0){
									// if select presented
									if(columnOptions.edittype == 'select'){
										globalScope.copyVal = $(cellId+' option').filter(function() {
											return $(this).text() === globalScope.copyVal;
										}).first().attr("value");
										// if user want to paste some lame shit
										if(typeof globalScope.copyVal === typeof undefined){
											$t.jqGrid('restoreCell',iRow,iCol)
											$.alert('Невозможно вставить!');
											break;
										}
									}
									// set cell value
									$(cellId).val(globalScope.copyVal);
									// saveCell
									$t.jqGrid('saveCell',iRow,iCol);
									// disable paste
									$t.contextmenu('enableEntry', 'paste', false);
								}
							// paste in blank row 
							} else {
								if($('.editable').length === 0)
									$('#'+rowid).children(':visible:first').trigger('click');
								$('#'+rowid+'_'+column).val(globalScope.copyVal);
								$t.contextmenu('enableEntry', 'paste', false);
							}
							// delete copyVal;
							delete globalScope.copyVal;
							break;
						// row copied
						} else if(typeof globalScope.copyVal === 'object'){
							if(rowid === 'blank'){
								// set blank row values
								$t.jqGrid('setRowData','blank',globalScope.copyVal);
								$t.jqGrid('editRow','blank');
								pasteRow.call(this,rowid);
							} else {
								$.alert('Невозможно вставить строку!');
							}
							// delete copyVal;
							delete globalScope.copyVal;
							break;
						}
					case 'copySingle':
						if(!isFirefox)
						{
							$t.editCell(iRow,iCol,true);
							document.execCommand('Copy');
							$t.restoreCell(iRow,iCol);
						}
						
						// save copyVal
						globalScope.copyVal = val;
						// enable paste
						$t.contextmenu('enableEntry', 'paste', true);
						break;
					case 'copyRow':
						if(!isFirefox)
						{
							selectInnerText(rowid)
							document.execCommand('Copy');
							window.getSelection().removeAllRanges();
						}
						// save copyVal as object
						globalScope.copyVal = rowdata;
						// enable paste
						$t.contextmenu('enableEntry', 'paste', true);
						break;
					case 'del':
						delRow.call(this,rowid);
						break;
					case 'sort_asc':
						$t.jqGrid('setGridParam', {sortorder: 'ASC'});
						$t.jqGrid('sortGrid',column,true);
						break;
					case 'sort_desc':
						$t.jqGrid('setGridParam', {sortorder: 'DESC'});
						$t.jqGrid('sortGrid',column,true);
						break;
					default: break;
				}
			}
			$t.jqGrid('restoreCell', globalScope.gRow, globalScope.gCol); // close edit
		},
		beforeOpen:function(event, ui){
			// FireFox cant work with clipboard
			if(!isFirefox)
				$t.contextmenu("showEntry", "client_info", false);
			// if there is contextmenu opened
			if($('.normalizeContext').is(':visible') === true)	return false;
			
			
			var localval,localcolumn,localcolumn_options
			
			if(ui.target.parents('tr')[0].id !== 'blank')
				ui.target.parents('tr').addClass('gridtools_hl'); // add class
				
			if((ui.target.parents('tr')[0].id).length > 0) 
				rowid = ui.target.parents('tr')[0].id // define rowid
			
			// define value of clicked element
			// if target is TD
			if(ui.target.context.tagName === 'TD') {
				// if target TD have checkbox get checkbox value, instead of TD-s text
				if(ui.target.find('input[type="checkbox"]').length > 0) localval = ui.target.find('input[type="checkbox"]').val();
				else localval = ui.target.text();
				// column name defined as TD-s attr 'aria-describedby'
				localcolumn = ui.target.attr('aria-describedby').replace($t[0].id+'_','');
			} else {
				localval = ui.target.val();
				localcolumn = ui.target.attr('name');
			}

			// run loop to get column object
			for(var i = 0;i < colmodel.length;i++){
				// check if this column exist in grid
				if(colmodel[i].name === localcolumn){
					// get options of target column
					localcolumn_options = colmodel[i];
				}
			}
			ui.menu.addClass('normalizeContext')
			ui.menu.find('ul').css('width','200px');
			
			// add dots and cut if length > 40
			if(localval.length > 40) localval = localval.substring(0,37)+'...';
			// replace empty value with "Пустые" or add double quotes
			if(localval.length === 0 || (localval.trim()).length === 0) localval = 'Пустые';
			else localval = '"'+localval+'"';
			
			// add value to filter entrys
			$t
				.contextmenu("setEntry", "filter_eq", {title: "Равно "+localval, cmd: "filter_eq"})
				.contextmenu("setEntry", "filter_ne", {title: "Не равно "+localval, cmd: "filter_ne"})
				.contextmenu("setEntry", "filter_cn", {title: "Содержит "+localval, cmd: "filter_cn"})
			// enable paste if copyVal defined
			if(typeof globalScope.copyVal !== typeof undefined) $t.contextmenu('enableEntry', 'paste', true);
			// enable/disable entys base on colmodel options
			if(localcolumn_options.search !== true || $('#gs_'+localcolumn).length === 0)
				$t.contextmenu('enableEntry', 'filter_eq', false).contextmenu('enableEntry', 'filter_ne', false).contextmenu('enableEntry', 'filter_cn', false);
			else 
				$t.contextmenu('enableEntry', 'filter_eq', true).contextmenu('enableEntry', 'filter_ne', true).contextmenu('enableEntry', 'filter_cn', true);
				
			if(localcolumn_options.editable !== true || t.p.cellEdit !== true)
				$t.contextmenu('enableEntry', 'cut', false).contextmenu('enableEntry', 'copySingle', false);
			else
				$t.contextmenu('enableEntry', 'cut', true).contextmenu('enableEntry', 'copySingle', true);
			
			if($.isFunction(delRow) === false)
				$t.contextmenu('enableEntry', 'del', false);
			else
				$t.contextmenu('enableEntry', 'del', true);
			if($.isFunction(pasteRow) === false)
				$t.contextmenu('enableEntry', 'copyRow', false);
			else
				$t.contextmenu('enableEntry', 'copyRow', true);
			
			if(rowid === 'blank'){
				$t
					.contextmenu('enableEntry', 'filter_eq', false).contextmenu('enableEntry', 'filter_ne', false).contextmenu('enableEntry', 'filter_cn', false)
					.contextmenu('enableEntry', 'cut', false).contextmenu('enableEntry', 'copySingle', false)
					.contextmenu('enableEntry', 'copyRow', false)
					.contextmenu('enableEntry', 'del', false);
			}
			if($t.contextmenu('getMenuEntry','custom').children().length === 0){
				$t.contextmenu('enableEntry', 'custom', false);
			}
			if($('#clearSort'+t.id).length === 0)
				$t.contextmenu('enableEntry', 'sort_asc', false).contextmenu('enableEntry', 'sort_desc', false);
			if(typeof disableCutCopy !== typeof undefined && disableCutCopy === true)
				$t.contextmenu('enableEntry', 'cut', false).contextmenu('enableEntry', 'copySingle', false);
		},
		close:function(event){
			$('#'+rowid).removeClass('gridtools_hl');
		}
	})
}
/*
 * Set Selection range
 */
function selectInnerText(element) 
{
    var doc = document,text = doc.getElementById(element),range,selection;    
	if (doc.body.createTextRange)
	{
		range = document.body.createTextRange();
		range.moveToElementText(text);
		range.select();
	}
	else if (window.getSelection) 
	{
		selection = window.getSelection();        
		range = document.createRange();
		range.selectNodeContents(text);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}
/* 
 * redirect to php excel
 */
function getExcel(statment,filters,filename,url)
{
	typeof statment !== typeof undefined ? statment = JSON.stringify(statment): $.alert('Smth Wrong!');
	typeof filters !== typeof undefined ? filters = JSON.stringify(filters): filters = null;
	typeof url === typeof undefined ? url = '/php/docs/excel/excel_tmpl': null;
	
	url = url + '?fileName='+filename+'&qry='+statment+'&filters='+filters;
	window.location = url;
}
/*
 * current Date yyyy-mm-dd
 */
function getDate()
{
	var currentTime = new Date(),
	month = currentTime.getMonth() + 1,
	day = currentTime.getDate(),
	year = currentTime.getFullYear();
	if (day < 10) 
		day ='0'+day;
	if (month < 10) 
		month = '0'+month;
		
	return year + "-" + month + "-" + day;
}
/*
 * current Date dd.mm.yyyy
 */
function getDateRu()
{
	var currentTime = new Date(),
	month = currentTime.getMonth() + 1,
	day = currentTime.getDate(),
	year = currentTime.getFullYear();
	if (day < 10) 
		day ='0'+day;
	if (month < 10) 
		month = '0'+month;
		
	return day + "." + month + "." + year;
}
/*
 *	destroy grid/clear container hmtl
 */
function destroy()
{
	if($('#gridcontainer').children().length > 0){
		$('.gridclass').jqGrid('GridDestroy');
		$('#container').find('table').remove();
		$('.ui-widget').remove();
		$('.ui-datepicker-div').remove();
		$('.ui-multiselect-menu').remove();
		document.getElementById('gridcontainer').innerHTML = "";
	}
}
/*
 *	destroy grid by passed id
 */
function destroyLocal(id)
{
	if(typeof(id)!== 'undefined'){
		$("table[id*='"+ id +"']").jqGrid('GridDestroy');
	} else {
		$('.gridclass').jqGrid('GridDestroy');
		$('.ui-widget').remove();
		$('.ui-datepicker-div').remove();
		$('.ui-multiselect-menu').remove();
	}
}
/*
 *	logout - need url
 */
function logout(to_url) 
{
	$.ajax({
		url:'/php/misc',
		type:'post',
		data:{oper:'logout'},
		success:function(data){ localStorage.clear(); }
	})
}
/*
 * short ajax. self explanatory.
 * 
 */
function shortAjax(sync,responce,query,getData,successFunc,getid)
{
	$.ajax({
		type:'POST',
		url:'/php/misc',
		async:sync,
		data:{oper:'directQuery',responce:JSON.stringify(responce),query:query,getid:getid},
		success:function(data){
			if(typeof(getData) !== typeof undefined && getData === true) successFunc(data);
		}
	})
}
/* 
 * Ф-ия поиска обькта в массивах 
 */
function containsObject(obj, list,fieldName,op,fieldValue)
{
    for (var i = 0; i < list.length; i++)
    {
		if (typeof list[i][fieldName] === 'object' && typeof obj[fieldName] === 'object' )
		{
			if((JSON.stringify(list[i][fieldName]) === JSON.stringify(obj[fieldName])))
				return true;
			else
				return false;
			break;
		} 
		else
		{
			if (list[i][fieldName] === obj[fieldName] && list[i][op] === obj[op] && list[i][fieldValue] === obj[fieldValue])
	            return true;
			break;
	    }
    }
    return false;
}
/*
 * Check if element is overflowed
 */
function checkOverflow(el,of_el,direction)
{
	var curOverflow,isOverflowing,prev_els,prev_els_wh = {width:0,height:0};
	
	if(typeof of_el === typeof undefined)
	{
		curOverflow = el.style.overflow;
		if ( !curOverflow || curOverflow === "visible" )
			el.style.overflow = "hidden";
			
		isOverflowing = el.clientWidth < el.scrollWidth || el.clientHeight < el.scrollHeight;
		el.style.overflow = curOverflow;
	}
	else
	{
		curOverflow = of_el.style.overflow;
		if ( !curOverflow || curOverflow === "visible" )
			of_el.style.overflow = "hidden";
		
		
		prev_els = $(el).prevAll('td:visible').andSelf();
		if(prev_els.length > 0)
		{
			$.each(prev_els,function(i,v)
			{
				prev_els_wh.width += v.clientWidth;
				prev_els_wh.height += v.clientHeight;
			})
		}
		if(typeof direction !== typeof undefined)
		{
			if(direction === 'horizontal')
				isOverflowing = prev_els_wh.width > of_el.clientWidth;
			else if(direction === 'vertical')
				isOverflowing = prev_els_wh.height > of_el.clientHeight;
		}
		else
			isOverflowing = prev_els_wh.width > of_el.clientWidth || prev_els_wh.height > of_el.clientHeight;
		
		of_el.style.overflow = curOverflow;
	}
	return isOverflowing;
}
/*
 * Ф-ия проверки нахождения элемента в пределах окна браузера
 */
function isElementInViewport (el)
{
	var bol = true;
    if (el instanceof jQuery)
    	el = el[0];

    var rect = el.getBoundingClientRect();

    bol =  
    (
        rect.top >= 0 && rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && 
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
    return bol;
}
/*
 * Вывод пдф-а в фрейм - getPDF('юрл с готовым пдф-ом')
 */
function getPDF(phpsource)
{
	window.open((document.URL)+phpsource,'pdf');
}
/*
 * 
 */
function setSess(sessData,reload,sync)
{
	$.ajax({
		url:'/php/misc',
		async:typeof sync != typeof undefined ? sync :false,
		type:'post',
		data:sessData,
		success:function()
		{
			if(reload === true)
			{
				location.reload();
			}
		}
	})
}
function setUserPref(prefData,reload)
{
	$.ajax({
		url:'/php/misc',
		async:false,
		type:'post',
		data:prefData,
		success:function()
		{
			if(reload === true)
			{
				location.reload();
			}
		}
	})
}
function getUserPref(val)
{
	var res;
	$.ajax({
		url:'/php/misc',
		async:false,
		type:'post',
		data:{oper:'get_user_pref',pref:val},
		success:function(data)
		{
			res = data;
		}
	})
	return res;
}
/*
 * Уничтожение сессии и удаление всего локала
 */
function AjaxKillSession(reload,clearStorage)
{
	$.ajax({
		url:'/php/misc',
		type:'post',
		data:{oper:'destroySession'},
		success:function(data){
			clearStorage ? ClearSomeLocalStorage('tig_') : '';
			if(reload  === true) location.reload();
		}
	})
}