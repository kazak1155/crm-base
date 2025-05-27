function jqGrid_aw_combobox$(element,data,source,grid,init_opts)
{
	if(typeof element === typeof undefined)
		return $.alert('No element set');
	if(typeof source !== typeof undefined)
		setter.call(this,source);
	this.init_opts = init_opts || {};
	this.valid = this.opened = this.changed = false;
	this.element = element;
	this.data = data;
	this.grid = grid;
	this.$grid = $(grid);
	this.$element = $(this.element);
	this.$legacy_parent = this.$element.parent();
	this.$legacy_element = this.$element.clone();
	self.isOpened = false;
	this.predefined = false;
	if(typeof grid !== typeof undefined)
		this.grid_related_prepare();
	this.prepare();
}
jqGrid_aw_combobox$.prototype.grid_related_prepare = function()
{
	this.inForm = this.$element.parents('form').length > 0 ? true : false;
	this.inLine = this.$element.parents('tr[id="blank"]').length > 0 ? true : false;
	this.$cell = this.$element.closest('td');
	this.$cell_row = this.$cell.closest('tr');
	this.rowid = this.$cell_row[0].id;
}
jqGrid_aw_combobox$.prototype.prepare = function()
{
	this.init_value = this.element.value;
	if(this.init_opts.hasOwnProperty('list'))
		this.predefined = true;
	this.init_element_value();
	this.element_wd = this.$element.outerWidth();
	this.element_pos = this.$element.offset();
	if(this.element_wd <= 50)
		this.small = true;
	if(this.inForm == true || this.inLine == true)
		this.create_select_helper();
	this.set_default_data();
	if(this.small != true || this.inForm == true)
		this.show_all_button();
	this.assign_aw();
	this.element.jqGrid_aw_combobox_proto = this;
	if(this.hasOwnProperty('select_helper'))
		this.select_helper.jqGrid_aw_combobox_proto = this;
}
jqGrid_aw_combobox$.prototype.init_element_value = function()
{
	var self = this;
	if(this.hasOwnProperty('init_element_value_fu'))
	{
		if($.isFunction(this.init_element_value_fu))
			this.init_element_value_fu.call(element);
	}
	else
	{
		if(this.predefined == true)
		{
			for(var i = 0;i < this.init_opts.list.length;i++)
			{
				if(Array.isArray(this.init_opts.list[i]) == true)
				{
					if(this.init_opts.list[i][1] == this.init_value)
						this.element.value = this.init_opts.list[i][0];
				}
				else
				{
					if(this.init_opts.list[i].value = this.init_value)
						this.element.value = this.init_opts.list[i].label;
				}
			}
		}
		else
		{
			if(this.$grid.getColProp(this.name).hasOwnProperty('formatoptions') && this.$grid.getColProp(this.name).formatoptions.hasOwnProperty('value'))
			{
				this.formattedValue = this.$grid.getColProp(this.name).formatoptions.value[this.init_value];
				if(this.formattedValue)
					this.element.value = this.formattedValue;
			}
		}
	}
}
jqGrid_aw_combobox$.prototype.create_select_helper = function()
{
	var self = this,option;

	this.element.removeAttribute('id');
	this.element.removeAttribute('name');

	this.select_helper = document.createElement('select');
	if(this.inForm == true)
	{
		this.select_helper.className = "FormElement";
		this.select_helper.name = this.select_helper.id = this.name;
	}
	else
	{
		this.select_helper.className = "editable";
		this.select_helper.name = this.name;
		this.select_helper.id = this.$cell_row[0].id + '_' + this.name;
	}

	this.select_helper.style.display = "none";
	option = document.createElement('option');
	option.selected = true;
	if(this.predefined == true)
	{
		for(var i = 0;i < this.init_opts.list.length;i++)
		{
			if(Array.isArray(this.init_opts.list[i]) == true)
			{
				if(this.init_opts.list[i][1] == this.init_value)
				{
					option.value = this.init_opts.list[i][1];
					option.innerHTML = this.init_opts.list[i][0];
				}
			}
			else
			{
				if(this.init_opts.list[i].value = this.init_value)
				{
					option.value = this.init_opts.list[i].value;
					option.innerHTML = this.init_opts.list[i].label;
				}
			}
		}
	}
	else
	{
		if(this.init_value)
		{
			option.value = this.init_value;
			option.innerHTML = this.formattedValue;
		}

	}
	if(this.inForm == true)
		this.$element.removeClass("FormElement");
	else
		this.$grid.setColProp(this.name,{edittype_temp:'select'});

	this.select_helper.appendChild(option);
	this.$cell.prepend(this.select_helper);
}
jqGrid_aw_combobox$.prototype.set_default_data = function()
{
	var self = this;
	this.cc_opts = new Object();
	this.cc_opts.minChars = 0;
	this.cc_opts.maxItems = 20;
	this.cc_opts.autoFirst = true;
	if(this.inForm == true || this.inLine == true)
	{
		this.cc_opts.replace = function (text) {
			var option = $('<option selected="selected" value="'+ text.value +'">'+ text.label +'</option>');
			self.$cell.find('select').find('option').remove();
			$(self.select_helper).append(option);
			this.input.value = text;
		};
	}
	$.extend(this.cc_opts,this.init_opts);
	delete this.init_opt;
	this.aj_data = new Object();
	this.aj_data.flds = ['Код','Название'];
	this.aj_data.sfld = this.aj_data.flds[1];
	this.aj_data.op = 'cn';
	this.aj_data.order = 2;
	this.aj_data.getNull = true;
	this.aj_data.top = this.cc_opts.maxItems;
	$.extend(this.aj_data,this.data);
	delete this.data;
}
jqGrid_aw_combobox$.prototype.assign_aw = function()
{
	this.cc = new Awesomplete(Awesomplete.$(this.element),this.cc_opts);
	this.assign_aw_render();
	this.assign_aw_events();
	if(this.inForm == true)
		this.assign_grid_form_related_events();
	else
		this.assign_grid_cell_related_events();

	this.assign_keyup();
}
jqGrid_aw_combobox$.prototype.assign_aw_render = function()
{
	var self = this;
	if(this.disabled == true || this.disabled == 'disabled')
		this.$element.css('background','#EBEBE4')
	if(this.inForm == true)
	{
		$(this.cc.status).css('display','none'); // remove span render,if rendered cause form overflow
		this.$element.css({'padding':'.3em','display':'inline-block','width':'275px','border-radius':'2px 0px 0px 2px'});
		$(this.show_all).css({'border':'1px solid #10498D','padding':'.3em 8px'})
		setTimeout(function(){
			self.element_wd = self.$element.outerWidth();
			self.element_pos = self.$element.offset();
			$(self.cc.ul).css({'min-width':self.element_wd,'left':self.element_pos.left});
		},0);
	}
	else
	{
		$(this.cc.ul).css({'min-width':this.element_wd,'left':this.element_pos.left});
		if(this.small != true)
			$(this.cc.container).css('width','calc(100% - 25px)');
	}
}
jqGrid_aw_combobox$.prototype.assign_aw_events = function()
{
	var self = this;
	this.element.addEventListener('awesomplete-open',function(event){
		if(self.inForm == false)
		{
			var top = self.element_pos.top + self.element.offsetHeight;
			if(window.innerHeight - self.element_pos.top + self.element.offsetHeight < 300 && (window.innerHeight - self.cc.ul.offsetHeight) > 300)
			{
				top -= self.cc.ul.offsetHeight + self.element.offsetHeight + 2;
				self.cc.ul.className = 'bottom-before';
			}
			$(self.cc.ul).css({'top':top});
		}
		self.opened = true;
	});
	this.element.addEventListener('awesomplete-close',function(event)
	{
		self.opened = false;
	});
	this.element.addEventListener('awesomplete-select',function(event)
	{
		self.valid = true;
	});
	this.element.addEventListener('awesomplete-selectcomplete',function(event)
	{
		if(self.inForm == true)
		{
			self.hook_new(self.select_helper.value);
			self.$element.trigger('change');
			$(self.select_helper).trigger('change');
		}
		else
		{
			if(self.predefined == false)
			{
				if(self.rowid != 'blank')
					self.hook_new(this.value);
				else
					self.hook_new(self.select_helper.value);
			}
			if(self.valid == true)
				self.$element.trigger($.Event( 'keydown', { keyCode:13,forced:true} ));
		}
	});
}
jqGrid_aw_combobox$.prototype.assign_grid_form_related_events = function()
{
	var self = this;
	this.$element.bind('keydown',function(event){
		if(event.keyCode == 9)
			self.cc.select();
		if(self.cc.ul.childNodes.length == 0)
			self.valid = false;
	});
	this.$element.change(function(event){
		if(self.valid == false)
			$(this).addClass('invalid-input');
		else
			$(this).removeClass('invalid-input');
	});
}
jqGrid_aw_combobox$.prototype.assign_grid_cell_related_events = function()
{
	var self = this;
	function kd(event,forced)
	{
		if(event.forced == true)
			return false;
		var prev = self.$cell_row.prev().not(".jqgfirstrow");
		var next = self.$cell_row.next();
		if(event.keyCode == 38 && prev.length == 0)
			return false;
		else if(event.keyCode == 40 && next.length == 0)
			return false;
		if([13, 27, 37, 38, 39, 40].indexOf(event.keyCode) > -1 && self.changed == false)
		{
			self.element.value = self.init_value;
			event.preventDefault();
			if(self.inLine == false)
				return false;
		}
		if(event.keyCode == 9)
			self.cc.select();
		// select box opened
		if(self.opened == true)
		{
			if([37,39].indexOf(event.keyCode) > -1)
				event.preventDefault();
			event.stopImmediatePropagation();
		}
		// ajax processing
		else if(event.keyCode != 27 && (typeof self.processing == typeof undefined || self.processing == true))
		{
			if(self.inLine == false)
				event.stopImmediatePropagation();
		}
		// invalid entry
		else if(event.keyCode != 27 && self.valid == false && self.changed == true)
			event.stopImmediatePropagation();
	}
	function kd_new(event)
	{
		var len = this.value.length,caret = this.selectionStart;
		if(event.keyCode === 27 || event.keyCode === 13)
			this.value = self.init_value;
		else
		{
			if(len === caret)
				$(this).bind("keydown",kd);
			else if (caret === 0)
				$(this).bind("keydown",kd);
		}
	}
	this.$element.bind('click',function(event){
		$(this).unbind("keydown",kd);
		$(this).unbind("keyup",kd_new);
		$(this).bind("keyup",kd_new);
	});
	this.$element.bind('keydown',kd);
	if(this.inForm == false)
	{
		this.$element.bind('blur',function(event){
			if(self.rowid == 'blank')
				return false;
			var $target = $(event.target);
			setTimeout(function(){
				var new_target = document.activeElement;
				// if clicked on same element
				if($target[0] == new_target)
					return;
				// if clicked on same table cell
				if($(new_target).closest('td')[0] === self.$legacy_parent[0])
					return $target.focus();
				if((($target.closest('td').find(new_target).length == 0) && ($target.closest('td')[0] != new_target)) && (self.changed == false))
					self.element.value = self.init_value;
				else if(self.valid == false)
					self.element.value = self.init_value;
			},0);
		});
	}
}
jqGrid_aw_combobox$.prototype.assign_keyup = function()
{
	var self = this,timeout;
	if(this.predefined == true)
	{
		this.$element.bind('keyup',function(event){
			event.stopPropagation();
			//13 enter,27 esc,rest navigation keys
			if([13, 27, 37, 38, 39, 40].indexOf(event.keyCode) > -1)
				return false;
			self.changed = true;
			self.processing = false;
		});
	}
	else
	{
		this.$element.bind('keyup',function(event){
			event.stopPropagation();
			//13 enter,27 esc,rest navigation keys
			if([13, 27, 37, 38, 39, 40].indexOf(event.keyCode) > -1)
				return false;
			self.changed = true;
			self.search = this.value;
			self.processing = true;
			clearTimeout(timeout);
			timeout = setTimeout(function(){
				self.xhr();
			},300);
		});
	}
}
jqGrid_aw_combobox$.prototype.show_all_button = function()
{
	var self = this;
	this.show_all = document.createElement('div');
	this.show_all.className = 'dropdown-btn';
	this.show_all_pointer = document.createElement('i');
	this.show_all_pointer.tabIndex = -1;
	this.show_all_pointer.className = 'fa fa-caret-down';
	this.show_all.appendChild(this.show_all_pointer);
	this.$element.after(this.show_all);
	this.show_all.addEventListener('click',function(event){
		//if(self.inForm == true)
		self.element_pos = self.$element.offset();
		self.assign_aw_render();
		if(self.element.disabled == true || self.element.disabled == 'disabled')
			return false;
		event.stopPropagation();
		if(self.cc.ul.childNodes.length === 0)
		{
			if(self.valid == false)
				self.search = '';
			self.element.value = '';
			if(self.predefined == false)
				self.xhr();
			else
				self.cc.evaluate();
			self.cc.input.focus();
			self.isOpened = true;
			return;
		}
		if(self.isOpened === false)
		{
			self.isOpened = true;
			self.cc.open();
			self.cc.input.focus();
		}
		else if(self.isOpened === true)
		{
			self.isOpened = false;
			self.cc.close();
			if(self.changed === false)
			{
				if(typeof self.formattedValue !== typeof undefined)
					self.element.value = self.formattedValue;
				else
					self.element.value = '';
			}

		}
	});
}
jqGrid_aw_combobox$.prototype.hook_new = function(scope)
{
	if(scope == 'NULL')
		return;
	var self = this;
	if(!this.$grid.getColProp(this.name).hasOwnProperty('formatoptions'))
		return;
	var found_scope = this.$grid.getColProp(this.name).formatoptions.value[scope];
	var guess_obj,guess_val,last_sugg_label,ss_name;
	var new_val = new Object();
	if(!found_scope)
	{
		guess_obj = $.grep(this.cc._list,function(n,i){
			if(self.inForm == true)
			{
				if(n.value == self.select_helper.value)
						return n;
			}
			else
			{
				if(n.value == self.element.value)
						return n;
			}
		});
		guess_val = guess_obj[0].value;
		for(var vals in this.cc.suggestions)
		{
			if(this.cc.suggestions[vals].value === scope)
			{
				last_sugg_label = this.cc.suggestions[vals].label;
				break;
			}
		}
		new_val[guess_val] = last_sugg_label;
		if(this.grid.p.gridProto.subgrid == true)
		{
			var parent = this.grid.p.gridProto.parent_grid;
			for(var i=0;i < parent.p.gridProto.subGridOps.length;i++)
			{
				var subgrid_obj = parent.p.gridProto.subGridOps[i];
				if(subgrid_obj.name == this.grid.id)
				{
					for(var cm_i = 0; cm_i < subgrid_obj.cm.length;cm_i ++)
					{
						if(subgrid_obj.cm[cm_i].name == this.name)
						{
							$.extend(subgrid_obj.cm[cm_i].formatoptions.value,new_val);
							this.$grid.setColProp(this.name,{formatoptions:{value:$.extend(this.$grid.getColProp(this.name).formatoptions.value,new_val)}});
						}
					}
				}
			}
		}
		else
		{
			this.$grid.setColProp(this.name,{formatoptions:{value:$.extend(this.$grid.getColProp(this.name).formatoptions.value,new_val)}});
		}
		if(window.location.search.indexOf('reference') > -1)
			$.ajaxShort({ data : { action : 'flush_cache_specify', tname: this.aj_data.tname } });
	}
}
jqGrid_aw_combobox$.prototype.destroy = function()
{
	this.$legacy_parent.children().remove();
	this.$legacy_element.removeAttr('style');
	this.$legacy_element.appendTo(this.$legacy_parent);
	delete(this);
}
jqGrid_aw_combobox$.prototype.clear_results = function()
{
	$(this.cc.ul).children().remove();
}
jqGrid_aw_combobox$.prototype.disable = function()
{
	this.disabled = true;
	this.element.disabled = true;
	this.element.style.background = '#EBEBE4';
}
jqGrid_aw_combobox$.prototype.enable = function()
{
	this.disabled = false;
	this.element.disabled = false;
	this.element.style.background = '#FFF';
}
jqGrid_aw_combobox$.prototype.focusself = function()
{
	this.$element.focus().select();
}
jqGrid_aw_combobox$.prototype.openlist = function()
{
	if(this.cc.ul.childNodes.length === 0)
	{
		if(this.valid == false)
			this.search = '';
		this.element.value = '';
		if(this.predefined == false)
			this.xhr();
		else
			this.cc.evaluate();
		this.cc.input.focus();
	}
	else
	{
		this.cc.open();
		this.cc.input.focus();
	}
}

jqGrid_aw_combobox$.prototype.xhr = function(callback)
{
	var self = this;
	$.ajax({
		type:'POST',
		url:REQUEST_URL,
		data:{
			oper:'view_ac_selects',
			search:self.search,
			info:JSON.stringify(self.aj_data)
		},
		complete:function(jqXHR,textStatus){
			self.processing = false;
			var res = JSON.parse(jqXHR.responseText);
			if(res.length > 1 || self.aj_data.getNull == false)
				self.cc.list = res;
			if($.isFunction(callback))
				callback();
		}
	})
}
