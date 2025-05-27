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
$.widget( "custom.combobox", {
	_create:function(){
		this.inForm = this.element.parents('form').length > 0 ? true : false;
		this.inLine = this.element.parents('tr[id="blank"]').length > 0 ? true : false;
		this.isRemote = typeof this.element.data('ac_data') !== typeof undefined ? true : false;
		this.grid_wrapper = this.element.closest('.ui-jqgrid-bdiv');
		this.grid = this.element.closest('.ui-jqgrid-btable');
		if(this.inForm == false)
			this.grid_is_sub = this.grid[0].p.gridProto.subgrid == true ? true : false;
		this.wrapper = $("<div>").insertAfter(this.element).addClass('ac-wrapper');
		this.element.attr('customCombobox','');
		this.element.hide();

		this._createAutocomplete();
		this._createShowAllButton();
		this.maxRepSize = 15;
		this.saved_search = String();
	},
	_createAutocomplete: function() {
		var self = this
		var selected = this.element.children(":selected");
		var value = selected.val() ? selected.text() : "";
		if(!value)
			value = this.element.parent('td').attr('title');

		this.input = $("<input>");
		this.input.addClass('autocompleteInput');
		this.input.attr('name',this.element[0].name)

		if(this.element[0].disabled === true)
		{
			this.input.attr('disabled','disabled');
			this.disabled = true;
		}
		if(this.inForm == true )
		{
			this.input
				.css({
					'border':'1px solid #dddddd',
					'border-radius':'2px 0px 0px 2px',
					'color':'#444444',
					'padding':'0.3em'
				})
		}
		this.input.appendTo( this.wrapper )
			.val( value )
			.attr( "title", value)
			.autocomplete({
				autoFocus:false,
				delay: this.isRemote == true ? 200 : 0,
				minLength: 0,
				appendTo: this.inForm == true ? null : this.wrapper,
				open:function(event, ui){
					var cell = $(this).parents('td');
					var menu = $(this).autocomplete('widget');
					var cell_position = cell.offset();
					//var wd = $(this).outerWidth();
					var wd = cell.innerWidth() - 2;
					var css_left,css_top,css_wd,css_maxHeight;

					wd = wd > 250 ? 250 : wd < 100 ? 'auto' : wd;
					if(self.inForm == true)
					{
						if($(window).height() - $(this).offset().top < 250)
							cell_position.top = cell_position.top -  menu.outerHeight() +  cell.outerHeight();
						else
							cell_position.top =	cell_position.top;
						css_top = cell_position.top + 2;
						css_left = cell_position.left + cell.outerWidth();
						css_wd = wd + 50 + 'px';
					}
					else
					{
						if(self.grid_is_sub == true)
						{
							if(self.grid_wrapper.height() <= 200)
							{
								css_maxHeight = self.grid_wrapper.height() - 100;
								menu.css({'max-height':css_maxHeight + 'px'});
							}
							if($(this).offset().top - self.grid.offset().top > 50)
								cell_position.top = cell_position.top - menu.outerHeight();
							else
								cell_position.top = cell_position.top + cell.outerHeight();
						}
						else
						{
							if($(window).height() - $(this).offset().top < 250)
								cell_position.top = cell_position.top - menu.outerHeight();
							else
								cell_position.top = 105 + cell_position.top + cell.outerHeight();
							cell_position.top = cell_position.top - self.grid.offset().top;
							cell_position.left = cell_position.left - self.grid.offset().left;
						}

						css_top = cell_position.top;
						css_left = cell_position.left;
						css_wd = wd + 'px';
					}
					menu.css({
						"top":css_top,
						"left":css_left,
						"z-index":10000,
						"width":css_wd,
						"border":"1px solid #10498D"
					});
				},
				close:function(event, ui)
				{
					self.wasOpen = Object();
					$('.ac-b-hover').removeClass('ac-b-hover');
					if(!self.element.val())
						$(this).trigger('change');
				},
				source: $.proxy( this, "_source" ),
				messages: {noResults: '',results: function() {}},
				select:$.proxy( this, "_select" ),
				focus:$.proxy( this, "_focus" ),
				response:$.proxy( this, "_response")
			})
			.blur(function(e){
				if(self.inForm == true && this.value.length > 0)
				{
					self.element.trigger('change');
				}
			})
			.keydown(function(e){
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
		this.wasOpen = Object();
		var self = this;
		var $input = $(this.input);
		var $button = $("<button>");
		if($input.width() < 50 && this.inForm == false)
			return;
		$button
			.attr('type','button')
			.attr('tabIndex',-1)
			.append('<i tabIndex="-1" class="fa fa-caret-down"></i>')
			.appendTo(this.wrapper)
			.mouseenter(function(e){
				$(this).addClass('ac-b-hover');
			})
			.mouseleave(function(e){
				if(Object.keys(self.wasOpen).length == 0)
					return $(this).removeClass('ac-b-hover');
				if(typeof self.wasOpen.state === typeof undefined || self.wasOpen.el !== this)
					$(this).removeClass('ac-b-hover');
			})
			.mousedown(function(e) {
				if(self.disabled == true)
					return;
				self.wasOpen.state = $input.autocomplete("widget").is(":visible");
				self.wasOpen.el = this;
			})
			.click(function(e){
				if(self.disabled == true)
					return;
				$(this).addClass('ac-b-hover');
				$input.focus();
				if (self.wasOpen.state && this.saved_search === 'isDummy')
					return;
				$input.autocomplete("search","isDummy");
			});
		if(this.inForm == true)
		{
			$button.outerWidth(30);
			$button.outerHeight(20.5);
			$input.css('width','calc(100% - 30px)');
		}
		else
		{
			$input.outerWidth($input.outerWidth() - $button.outerWidth() - 5);
			$button.outerHeight($input.outerHeight());
		}
	},
	_source:function(request,response) {
		this.saved_search = request.term;
		if(request.term === 'isDummy')
			request.term = '';
		var matcher = new RegExp('^'+$.ui.autocomplete.escapeRegex(request.term)+'$', "i");
		var mathcerBw = new RegExp('^'+$.ui.autocomplete.escapeRegex(request.term), "i");
		var matcherCn = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
		var select_el = this.bindings[0];
		var rep = new Array();
		var maxRepSize = this.maxRepSize;
		/*
		 * TODO When you will have a lot a time!
		 */
		if(this.isRemote == true)
		{
			this.ajax = this.element.data('ac_data');
			if(typeof this.ajax.tname == typeof undefined)
				return $.alert('No source table set');

			this.ajax.flds = typeof this.ajax.flds == typeof undefined ? ['Код','Название'] : this.ajax.flds;
			this.ajax.sfld = typeof this.ajax.sfls == typeof undefined ? this.ajax.flds[1] : this.ajax.sfls;
			this.ajax.order = typeof this.ajax.order == typeof undefined ? 2 : this.ajax.order;
			this.ajax.length = this.maxRepSize;
			$.ajax({
				url:"/php/main/request",
				dataType: 'json',
				type:"POST",
				data:{
					oper:'view_ac_selects',
					search:request.term,
					info:JSON.stringify(this.ajax)
				},
				success: function(data)
				{
					response(data);
				}
			})
		}
		else
		{
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

				if (rep.length >= this.maxRepSize) {
					rep.push({
						label: "...",
						value: "maxRepSizeReached",
						option: ""
					});
					break;
				}
			}
			this.last_responce = rep;
			response(rep);
		}
	},
	_response:function(event, ui){
		if(this.saved_search === 'isDummy')
			return;
		if(ui.content.length == 0)
			return;
		for(var i = 0; i < ui.content.length;i++)
		{
			if(ui.content[i].option != this.element[0].value)
			{
				if(this.element.find('option[value="'+ ui.content[i].option +'"]').length == 0)
				{
					var new_option = document.createElement('option');
					$(new_option).attr('role','option');
					new_option.value = ui.content[i].option;
					new_option.innerHTML = ui.content[i].value;
					this.element.append(new_option);
				}
			}
		}
		// select first empty value
		this.element.val(ui.content[0].option);
		if(ui.content.length > 0)
		{
			var caret = this.input[0].selectionStart;
			var sug = ui.content[0].value;
			this.input.val(sug);
			this.input[0].setSelectionRange(caret,sug.length);
		}
		$(this.input).trigger('change');
	},
	_select: function (event, ui) {
		var input = this.input,scroll = input.autocomplete( "widget" ).scrollTop();
		if (ui.item.value == "maxRepSizeReached") {
			$.extend(event.originalEvent,{keepOpen:true});
			this.maxRepSize += 10;
			input.autocomplete("search","");
			input.autocomplete("widget").scrollTop(scroll);
			return false;
		} else {
			var option = this.element.children('option[value="'+ui.item.option+'"]'),new_option
			if(option.length === 0)
			{
				new_option = document.createElement('option');
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
			if(this.inForm == false && this.inLine == false)
			{
				//this.grid.saveCell(this.grid[0].p.iRow,this.grid[0].p.iCol);
				this.input.autocomplete('close');
				this.input.trigger($.Event( 'keydown', { keyCode:13 } ));

			}
			else
			{
				this.input.trigger('change');
				this.element.trigger('change');
			}
		}

	},

	_focus: function (event, ui) {
		if (ui.item.value == "maxRepSizeReached")
			return false;
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

		if ( valid )
			return;
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
