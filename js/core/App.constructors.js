 function setter(source,debug)
{
	var object = new Object;
	for(var property in source)
	{
		object[property] = source[property];
	}
	$.extend(this,object);
	if(typeof debug !== typeof undefined && debug === true)
		console.log(object);
}
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
$.extend({ createOptions:function(data,valueName,textName)
	{
		valueName = typeof valueName === typeof undefined ? 'Код' : valueName;
		textName = typeof textName === typeof undefined ? 'Название' : textName;
		var response = Object();
		response.rawString = String();
		response.option = String();
		if($.isArray(data))
		{
			for(var i = 0;i < data.length;i++)
			{
				response.rawString += data[i][valueName]+':'+data[i][textName];
				if(i < data.length - 1)
					response += ';';
 				response.option += '<option role="option" value="'+data[i][valueName]+'">'+data[i][textName]+'</option>';
			}
		}
		else
		{

		}
		return response
	}
})
$.extend({ alert: function (message,title,source)
	{
		var self = this;
		this.dialog_opts = {
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
			width:'auto',
			show:'highlight',
		};
		this.dialog_extend_opts = {
			minimizable: false,
			titlebar:'transparent',
			collapsable: false,
			closable: true
		};
		if(typeof source != typeof undefined)
		{
			this.font_width = typeof source.font_width !== typeof undefined ? source.font_width : '12pt';
			this.classNames = typeof source.classNames !== typeof undefined ? ' '+source.classNames : '';
			this.min_wd = typeof source.min_wd !== typeof undefined ? source.min_wd : '150px';
			this.inline_css = { 'min-width':this.min_wd,'font-size':this.font_width };
			if(source.hasOwnProperty('dialog_opts'))
				$.extend(this.dialog_opts,source.dialog_opts);
			if(source.hasOwnProperty('dialog_extend_opts'))
				$.extend(this.dialog_extend_opts,source.dialog_extend_opts);
			if(source.hasOwnProperty('inline_css'))
				$.extend(this.inline_css,source.inline_css);
		}
		else
		{
			this.font_width = '12pt';
			this.classNames = '';
			this.min_wd = '150px';
			this.inline_css = {
				'min-width':this.min_wd,
				'font-size':this.font_width
			};
		}
		$("<div class='alert"+this.classNames+"'>")
			.css(this.inline_css)
			.append(message)
			.dialog(this.dialog_opts)
			.dialogExtend(this.dialog_extend_opts);
	}
});
$.extend({
	confirm: function(source)
	{
		var self = this;
		this.title = typeof source.title !== typeof undefined ? source.title : '';
		this.classNames = typeof source.classNames !== typeof undefined ? ' '+source.classNames : '';
		this.message = typeof source.message !== typeof undefined ? source.message : $.alert('No msg set.') ;
		this.done_func = $.isFunction(source.done_func) ? source.done_func : $.alert('No done function set.');
		this.cancel_func = $.isFunction(source.cancel_func) ? source.cancel_func : function() { $(this).dialog('close'); };
		this.width = typeof source.width !== typeof undefined ? source.width : 600;
		var yes_button_caption = typeof source.yes !== typeof undefined ? source.yes : 'Да';
		var no_button_caption = typeof source.no !== typeof undefined ? source.no : 'Нет';

		this.dialog_extend_opts = {
			minimizable: false,
			titlebar:'transparent',
			collapsable: false,
			closable: false
		};
		if(source.hasOwnProperty('dialog_extend_opts'))
			$.extend(this.dialog_extend_opts,source.dialog_extend_opts);

		this.buttons = new Object();
		this.buttons[yes_button_caption] = function()
		{
			self.done_func.call(this);
			$(this).dialog("close");
		}
		this.buttons[no_button_caption] = function()
		{
			self.cancel_func.call(this);
		}

		this.dialog_opts = {
			buttons: this.buttons,
			close: function(event, ui)
			{
				$(this).remove();
			},
			resizable: false,
			title: this.title,
			modal: true,
			width: typeof this.width !== typeof undefined ? this.width : 'auto',
			show:'highlight',
			open:function()
			{
				$(this).parent().find('button:nth-child(1)').focus();
			}
		}
		if(source.hasOwnProperty('dialog_opts'))
			$.extend(this.dialog_opts,source.dialog_opts);
		$("<div class='confirm"+this.classNames+"'>")
		.data('confirm',source)
		.css({'font-size':'12pt'})
		.text(this.message)
		.dialog(this.dialog_opts)
		.dialogExtend(this.dialog_extend_opts);
	}
});
$.extend({
	confirmHTML:function(source)
	{
		var self = this,buttons;
		this.title = typeof source.title !== typeof undefined ? source.title : '';

		if(typeof source.html !== typeof undefined)
		{
			if($.isFunction(source.html))
				this.html = source.html();
			else
				this.html = source.html;
		}
		else
			$.alert('No html set.');

		this.height = typeof source.height !== typeof undefined ? source.height : 'auto';
		this.width = typeof source.width !== typeof undefined ? source.width : 600;

		var yes_button_caption = typeof source.yes !== typeof undefined ? source.yes : 'Да';
		var no_button_caption = typeof source.no !== typeof undefined ? source.no : 'Нет';

		this.dialog_extend_opts = {
			minimizable: false,
			titlebar:'transparent',
			collapsable: false,
			closable: false
		};
		if(source.hasOwnProperty('dialog_extend_opts'))
			$.extend(this.dialog_extend_opts,source.dialog_extend_opts);

		if(source.hasOwnProperty('customButtons'))
			this.buttons = source.customButtons;
		else
		{
			this.done_func = $.isFunction(source.done_func) ? source.done_func : $.alert('No done function set.');
			this.cancel_func = $.isFunction(source.cancel_func) ? source.cancel_func : function() { $(this).dialog('close'); };
			this.buttons = new Object();
			this.buttons[yes_button_caption] = function()
			{
				self.done_func.call(this,self.html);
				if(source.preventOkClose != true)
					$(this).dialog("close");
			}
			this.buttons[no_button_caption] = function()
			{
				self.cancel_func.call(this);
			}
		}

		this.dialog_opts = {
			buttons:this.buttons,
			close: function(event, ui)
			{
				$(this).remove();
			},
			resizable: false,
			title: this.title,
			modal: true,
			height : typeof this.height !== typeof undefined ? this.height : 'auto',
			width : typeof this.width !== typeof undefined ? this.width : 600,
			show:'highlight',
			open:function()
			{
				$(this).parent().find('button:nth-child(1)').focus();
			}
		}

		if(source.hasOwnProperty('dialog_opts'))
			$.extend(this.dialog_opts,source.dialog_opts);

		$("<div class='confirm-html' style='z-index:950 !important'>")
			.css({'font-size':'12pt'})
			.append(this.html)
			.dialog(this.dialog_opts)
			.dialogExtend(this.dialog_extend_opts);
	}
});
$.extend({
	file_Tree:function(source)
	{
		if(!source.s_prm)
			return $.alert('Not all propertys set');
		this.dialog_opts = new Object();
		this.dialog_extend_opts = new Object();

		this.dialog_opts.close = function(){
			var tree_container = $(iframe).contents().find('.tree_container');
			var tree_object = tree_container[0].$jstree;
			if(source.hasOwnProperty('onClose'))
				source.onClose.call(this,tree_container,tree_object);
			$(this).remove();
		}

		this.dialog_opts.resizable = false;
		this.dialog_opts.title = source.title || '';
		this.dialog_opts.modal = true;
		this.dialog_opts.height = 500;
		this.dialog_opts.width = 600;


		if(source.hasOwnProperty('dialog_opts'))
			$.extend(this.dialog_opts,source.dialog_opts);

		this.dialog_extend_opts.minimizable = false;
		this.dialog_extend_opts.titlebar = 'transparent';
		this.dialog_extend_opts.collapsable = false;
		this.dialog_extend_opts.closable = true;

		if(source.hasOwnProperty('dialog_extend_opts'))
			$.extend(this.dialog_extend_opts,source.dialog_extend_opts);

		var stream_params = {};
		$.extend(stream_params,source.s_prm);
		var iframe = document.createElement('iframe');
		iframe.align = "top";
		iframe.width = iframe.height = "100%";
		iframe.style.borderWidth = 0;
		iframe.setAttribute('autofocus','autofocus');
		iframe.src = "/templates/misc/file_tree/?"+$.param(stream_params);

		$("<div>")
			.css({'font-size':'12pt'})
			.append(iframe)
			.dialog(this.dialog_opts)
			.dialogExtend(this.dialog_extend_opts);
	}
});
$.extend({
	genHTML : function(source){
		if(!source.type)
			return $.alert('No html type set');
		this.HTMLtype = source.type;
		this.options = source.hasOwnProperty('options') ? source.options : new Object();
		var html = new Object();
		var randomID = 'dummy_' + Math.floor(Math.random() * 100);
		switch(this.HTMLtype)
		{
			case 'input-top-label':
				html.wrapper = document.createElement('div');
				html.input = document.createElement('input');
				html.label = document.createElement('label');

				html.wrapper.className = 'float_label_wrapper';
				if(source.hasOwnProperty('wd'))
					html.wrapper.style.width = source.wd;
				else
					html.wrapper.style.width = 'calc(100% - 15px)';
				html.wrapper.style.minWidth = '0';

				html.input.className = 'float_input';
				html.input.id = randomID;
				this.options.hasOwnProperty('name') ? html.input.name = this.options.name : null;
				html.input.autocomplete = this.options.hasOwnProperty('autocomplete') ? this.options.autocomplete : 'off'
				this.options.hasOwnProperty('autofocus') ? html.input.autofocus = true : false;
				html.input.type = this.options.hasOwnProperty('input_type') ? this.options.input_type : 'text';
				html.input.required = this.options.hasOwnProperty('required') ? this.options.required : true;
				html.input.value = this.options.hasOwnProperty('input_value') ? this.options.input_value : '';
				if(this.options.hasOwnProperty('input_inline_style'))
					$(html.input).attr('style',this.options.input_inline_style);

				if(this.options.hasOwnProperty('pattern'))
					html.input.pattern = this.options.pattern;

				if(this.options.hasOwnProperty('disabled'))
					html.input.disabled = this.options.disabled;
				if(this.options.hasOwnProperty('readonly'))
					html.input.readOnly = this.options.readonly;

				if(this.options.hasOwnProperty('input_verification'))
				{
					switch(this.options.input_verification){
						case 'integer':
							html.input.onkeypress = function(event)
							{
								var key = (event.which) ? event.which : event.keyCode
								if (key > 31 && (key < 48 || key > 57))
							 		return false;
							}
						break;
						case 'numeric':
						html.input.onkeypress = function(event)
						{
							var key = (event.which) ? event.which : event.keyCode;
							if (key > 31 && (key < 48 || key > 57))
							{
								if(key != 46 && key != 44)
									return false;
							}
						}
						break;
						default: break;
					}
				}
				html.wrapper.appendChild(html.input);

				html.label.className = 'float_label';
				html.label.innerHTML = this.options.hasOwnProperty('label_text') ? this.options.label_text : '';
				html.label.htmlFor = randomID;

				html.wrapper.appendChild(html.label);

				html.ready = html.wrapper;
			break;
			case 'textarea-top-label':
				html.wrapper = document.createElement('div');
				html.input = document.createElement('textarea');
				html.label = document.createElement('label');

				html.wrapper.className = 'float_label_wrapper';
				html.wrapper.style.width = 'calc(100% - 15px)';
				html.wrapper.style.minWidth = '0';

				html.input.className = 'float_input';
				html.input.id = randomID;
				this.options.hasOwnProperty('name') ? html.input.name = this.options.name : null;
				html.input.autocomplete = this.options.hasOwnProperty('autocomplete') ? this.options.autocomplete : 'off'
				this.options.hasOwnProperty('autofocus') ? html.input.autofocus = true : false;
				html.input.required = this.options.hasOwnProperty('required') ? this.options.required : true;
				html.input.value = this.options.hasOwnProperty('input_value') ? this.options.input_value : '';
				if(this.options.hasOwnProperty('input_inline_style'))
					$(html.input).attr('style',this.options.input_inline_style);

				html.wrapper.appendChild(html.input);

				html.label.className = 'float_label';
				html.label.innerHTML = this.options.hasOwnProperty('label_text') ? this.options.label_text : '';
				html.label.htmlFor = randomID;

				html.wrapper.appendChild(html.label);

				html.ready = html.wrapper;
			break;
			case 'simple-input':
				html.input = document.createElement('input');
				html.input.className = 'niceInput_v1';
				html.input.style.width = 'calc(100% - 15px)';
				html.input.style.padding = '5px';
				html.input.type = this.options.hasOwnProperty('input_type') ? this.options.input_type : 'text';
				html.input.value = this.options.hasOwnProperty('input_value') ? this.options.input_value : '';
				this.options.hasOwnProperty('name') ? html.input.name = this.options.name : null;
				if(this.options.hasOwnProperty('input_inline_style'))
				{
					if(html.input.style.length > 0)
						$(html.input).attr('style',$(html.input).attr('style') + this.options.input_inline_style);
					else
						$(html.input).attr('style',this.options.input_inline_style);
				}

				html.ready = html.input;
			break;
			case 'hidden-input':
				html.input = document.createElement('input');
				html.input.type = 'hidden';
				html.ready = html.input;
			break;
			case 'simple-select':
				html.select = document.createElement('select');
				html.select.style.width = this.options.hasOwnProperty('width') ? this.options.width : "100%";
				this.options.hasOwnProperty('name') ? html.select.name = this.options.name : null;
				for(var iter in this.options.data)
				{
					if(!this.options.data[iter].value)
						continue;
					var option = document.createElement('option');
					option.value = this.options.data[iter].value;
					option.textContent = this.options.data[iter].text;
					html.select.appendChild(option);
				}
				html.ready = html.select;
			break;
		}
		return html.ready;
	}
});
