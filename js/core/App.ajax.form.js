function $ajaxForm(source)
{
	this.set_defaults();
	setter.call(this,source);
	this.set_form_defaults();
	this.set_form_data();
	if(this.float_label === true)
		this.add_labels();
	if(this.view_only === true)
	{
		$('#'+this.name+' :input').not('button').attr('disabled',true).on('keydown',function(event)
		{
			return false;
		})
		return;
	}
	if(this.add_nav === true)
		this.create_navigation();
	if(!this.hasOwnProperty('name') || $('#'+this.name).length === 0)
		return $.alert('Element name not set or there is no such element');
	this.bind_events();
}
$ajaxForm.prototype.add_labels = function()
{
	var self = this;
	if(this.table_form == true)
	{
		/* TODO */
		if(this.hasOwnProperty('t_head'))
		{
			if(this.hasOwnProperty('form_element_header'))
				return;
			$.each($('input',this.form_element),function(i,n)
			{
				$(this).closest('div').find('label').remove();
			});
			var thead = document.createElement('thead');
			var tr = document.createElement('tr');
			tr.className = 'form-table-header';
			for(var i = 0;i < this.t_head.length;i++)
			{
				var th = document.createElement('th');
				if(this.t_head[i].length == 0)
					th.style.display = 'none';
				th.innerHTML = '<label>'+this.t_head[i]+'</label>';
				tr.appendChild(th);
			}
			thead.appendChild(tr);
			this.form_element.find('table').prepend(thead);
			this.form_element_header = $('form-table-header',this.form_element);
		}
		else
		{
			$.each(this.form_element.find('input[type="text"]:visible,input[type="checkbox"]'),function(i,n)
			{
				this.id = this.name;
				$(this).siblings('label').attr('for',this.id);
				if($(this).hasClass('dp'))
					$(this).datepicker();
			});
			$.each(this.form_element.find('select:visible'),function(i,n)
			{
				if($(this).siblings('label').length > 0)
					return;
				$(this).parents('div').css({'position':'relative'})
				this.id = this.name;
				var ph = $(this).attr('data-placeholder');
				$(this).after('<label for="'+ this.id +'" class="float_label_select">'+ ph +'</label>')
			});
		}
	}
	else
	{
		$.each(this.form_element.find('input[type="text"]:visible,input[type="checkbox"]'),function(i,n)
		{
			this.id = this.name;
			$(this).siblings('label').attr('for',this.id);
			if($(this).hasClass('dp'))
				$(this).datepicker();
		});
		$.each(this.form_element.find('select:visible'),function(i,n)
		{
			if($(this).siblings('label').length > 0)
				return;
			$(this).parents('div').css({'position':'relative'})
			this.id = this.name;
			var ph = $(this).attr('data-placeholder');
			$(this).after('<label for="'+ this.id +'" class="float_label_select">'+ ph +'</label>')
		});
	}
}
$ajaxForm.prototype.set_defaults = function()
{
	this.oper = 'edit';
	this.float_label = true;
	this.sub_form = false;
	this.table_form = false;
	this.add_nav = false;
	this.id = 'Код';
	this.data_type = 'local';
	this.eventHandlers = ['keyup','change'];
}
$ajaxForm.prototype.set_form_defaults = function()
{
	var self = this;
	this.form_element = $('#'+this.name);
	this.form_element_pure = this.form_element[0];
	if(this.table_form === true)
	{
		this.table_element = this.form_element.find('table');
		this.table_element_pure = this.table_element[0];
	}
	this.form_element.bind('reset',function(e){
		self.set_form_data();
	});
	this.form_element.submit(function(e){
		return false;
	});
}
$ajaxForm.prototype.set_form_data = function()
{
	var self = this;
	this.form_data = new Object();
	this.columns = new Object();
	$.each($('#'+this.name+' :input').not('[type="submit"]'),function(i,n)
	{
		if(self.data_type === 'local')
		{
			if(self.table_form === true)
			{
				var $row = $(this).parents('tr');
				var id = $row.find('input[name="'+self.id+'"]').val();
				if($row.hasClass('blank-row'))
					return;
				if(self.columns.hasOwnProperty(this.name) === false)
				{
					self.columns[this.name] = new Object();
					self.columns[this.name].visible = $(this).is(':visible');
					self.columns[this.name].type = this.type;
				}
				if(!self.form_data.hasOwnProperty(id))
				{
					self.form_data[this.value] = new Object();
					$row.attr('id',this.value);
					self.last_row_id = this.value;
				}
				if(this.name === self.id)
					return true;
				self.form_data[id][this.name] = this.value;
			}
			else
			{
				if(this.disabled == false)
				{
					if(this.type === 'checkbox')
					{
						if(this.value || this.checked)
							self.form_data[this.name] = Number(this.checked);
					}
					else
					{
						if(this.value)
							self.form_data[this.name] = Number(this.checked) || this.value;
					}
				}
			}
		}
		else
		{
			/* TODO */
		}
	});
}
$ajaxForm.prototype.append_hidden_submit = function()
{
	if($('.hidden-submit',this.form_element).length === 0)
	{
		var hidden_submit = document.createElement('input');
		hidden_submit.type = 'submit';
		hidden_submit.className = 'hidden-submit';
		hidden_submit.style.display = 'none';
		this.form_element_pure.appendChild(hidden_submit);
	}
}
$ajaxForm.prototype.create_navigation = function()
{
	var self = this;
	if(this.last_row_id)
	{
		this.last_row = $('tr#'+this.last_row_id,this.form_element);
		this.last_row_pure = this.last_row[0];
	}
	else
	{
		this.last_row = $('tr',this.form_element).not('.form-table-header').first();
		this.last_row_pure = this.last_row[0];
	}
	this.append_hidden_submit();
	if(this.add === true)
		this.add_blank_row();
	if(this.del === true)
		this.add_del();
}
$ajaxForm.prototype.add_blank_row = function()
{
	var self = this;
	var selects_preset = false;
	if($('select',this.last_row).length > 0)
		selects_preset = true;
	if(selects_preset === true)
		$('select',this.last_row).select2('destroy');
	var cloned_row;
	if(this.last_row_id)
		cloned_row = this.last_row.clone(true,true);
	else
		cloned_row = this.last_row;
	var cell_save = document.createElement('td');
	var cell_save_icon = document.createElement('i');
	cell_save.className = 'save-row';
	cell_save.title = 'Сохранить строку';
	cell_save.style.cursor = 'pointer';
	cell_save_icon.className = 'fa fa-lg fa-floppy-o';
	Object.assign(cell_save_icon.style,{cursor:'pointer',color:'#10498D',fontSize:'2em',marginTop:'15px',marginLeft:'5px'});
	cell_save.appendChild(cell_save_icon);
	// recreate selects of last row
	if(selects_preset === true)
		dataSelect2.call(this.last_row,$('.select2me',this.last_row));

	// remove cloned row id
	cloned_row.addClass('blank-row');
	// reset values of cloned row
	if(self.table_form === true && self.sub_form === true && self.subid)
		cloned_row.find('input,select').not('[name="'+this.subid+'"]').val('');
	else
		cloned_row.find('input,select').val('');

	// add save-row in cloned row;
	cloned_row.append(cell_save);
	// append cloned row
	this.table_element.append(cloned_row);
	// reinit labes;
	this.add_labels();
	// recreate selects of cloned appended row
	if(selects_preset === true)
		dataSelect2.call(cloned_row,$('.select2me',cloned_row));
	// set new last row
	this.last_row = cloned_row;
	this.last_row_pure = this.last_row[0];
	// set add_mode
	this.add_mode = true;
	cell_save.onclick = function(event)
	{
		var post_data = new Object();
		var save = this;
		var save_row = $(this).parents('tr')
		post_data.oper = 'add';
		post_data.tname = self.table;
		$.each(self.last_row.find(':input'),function(i,el)
		{
			if(this.name !== self.id)
				post_data[this.name] = this.value;
			if(this.required === true && !this.value)
				post_data.required_error = true;
		});
		post_data.getid = true;
		if(post_data.required_error === true)
		{
			$(':submit',self.form_element_pure).click();
			return;
		}
		self.xhr(post_data,function(rowid){
			save.remove();
			self.add_mode = false;
			self.last_row = save_row;
			self.last_row_pure = self.last_row[0];
			self.last_row.removeClass('blank-row');
			$('input[name="'+self.id+'"]',self.last_row).val(rowid);
			self.set_form_data();
			self.create_navigation();
		});
	};
}
$ajaxForm.prototype.add_del = function()
{
	var self = this;
	var cell_del = document.createElement('td');
	var cell_del_icon = document.createElement('i');
	var cell_del_clone;
	var cell_del_click = function(event)
	{
		var post_data = new Object();
		var row = $(this).parents('tr');
		post_data.oper = 'del';
		post_data.tname = self.table;
		post_data.tid = self.id;
		post_data.id = row[0].id;
		self.xhr(post_data,function(rowid){
			row.remove();
		});
	};
	cell_del.className = 'del-row';
	cell_del.title = 'Удалить строку';
	cell_del.style.cursor = 'pointer';
	cell_del_icon.className = 'fa fa-lg fa-trash';
	Object.assign(cell_del_icon.style,{color:'#10498D',fontSize:'2em',marginTop:'15px',marginLeft:'5px'});
	cell_del.appendChild(cell_del_icon);
	$.each($('tr',this.form_element).not('.blank-row,.form-table-header'),function(i,el)
	{
		if($(this).children('td.del-row').length === 0)
		{
			cell_del_clone = cell_del.cloneNode(true);
			cell_del_clone.onclick = cell_del_click;
			this.appendChild(cell_del_clone);
			self.set_form_data();
		}
	});
}
$ajaxForm.prototype.bind_events = function()
{
	var self = this;
	for(var i = 0;i < this.eventHandlers.length;i++)
	{
		// Я совершенно забыл откуда вылезает значение prevented, однако если речь идет о change оно равно true...потратил пол часа на поиски не смог найти.
		// Вспомнил, $('select','.submitForm').trigger("change",true); <-- отсюда.
		$('#'+this.name+' :input').bind(this.eventHandlers[i],function(event,prevented)
		{
			if(this.checkValidity() === false)
			{
				if($(':submit',self.form_element_pure).length === 0)
					self.append_hidden_submit();
				$(':submit',self.form_element_pure).click();
				return;
			}
			if($(this).parents('tr')[0] === self.last_row_pure && self.add_mode === true)
				return false;
			if(this.disabled)
				return false;
			if(self.table_form == true)
			{
				self.cur_id = $(this).closest('td').siblings().find('input[name="'+self.id+'"]').val();
			}
			else
			{
				if(self.hasOwnProperty('eventExtend'))
					self.eventExtend.call(this,event,self.form_element,self);

				if(self.sub_form == true)
					self.cur_id = self.form_element.find('input[subname="'+self.id+'"]').val();
				else
					self.cur_id = self.form_element.find('input[name="'+self.id+'"]').val();
			}
			if(prevented)
				return;
			if (event.type === 'change' && this.type === 'checkbox')
				self.input_value = this.value = Number(this.checked);
			else
			{
				if(self.input_name == this.name && self.input_value == this.value && self.cur_id == self.prev_id)
					return;

				if(this.hasAttribute('min-value') && this.value.length == 0)
				{
					self.input_value = this.getAttribute('min-value');
					this.value = this.getAttribute('min-value');
				}
				else
					self.input_value = this.value;
				self.input_name = this.name;
			}
			self.prev_id = self.cur_id;
			self.current_input = this;
			if(this.required === true && this.value.length === 0)
			{
				this.value = self.input_value = this.defaultValue;
				$(this).trigger('keyup');
			}
			else
				self.prepare_and_send();
		})
	}
}
$ajaxForm.prototype.prepare_and_send = function()
{
	var self = this
	var els;
	if(!this.cur_id)
		return $.alert('Не указан id.');
	if(this.add === true && this.oper == 'edit')
	{
		if(this.sub_form == true)
		{
			els = $('#'+this.form_element_pure.id+' input[subname!="'+this.id+'"]').not(this.current_input).filter(function(){ return this.value || this.checked; });
			if(els.length == 0 && Object.keys(this.form_data).length == 0)
				this.oper = 'add';
		}
		else
		{
			els = $('#'+this.form_element_pure.id+' input[name!="'+this.id+'"],#'+this.form_element_pure.id+' select').not(this.current_input).filter(function(){ return this.value || this.checked; });
			if(els.length == 0 && Object.keys(this.form_data).length == 1)
				this.oper = 'add';
		}
	}
	clearTimeout(this.timeout);
	var timer = setTimeout(function(){
		var post_data = new Object();
		post_data.oper = self.oper;
		post_data.tname = self.table;
		post_data.tid = self.id;
		post_data.id = self.cur_id;
		if(self.oper === 'add')
		{
			delete post_data.tid;
			delete post_data.id;
			post_data[self.id] = parseInt(self.cur_id);
		}
		post_data[self.input_name] = self.input_value;
		self.xhr(post_data);
	},300);
	this.timeout = timer;
}
$ajaxForm.prototype.xhr = function(post_data,afterRequest)
{
	var self = this;
	$.ajaxShort({
		data:post_data,
		success:function(data)
		{
			if(self.oper == 'add')
				self.oper = 'edit';
			if(data.length > 10)
				return $.alert(data);
			if(self.hasOwnProperty('afterRequest'))
				self.afterRequest.call(self,data);
			if($.isFunction(afterRequest))
				afterRequest.call(this,data);
			else
				self.set_form_data();
			if(!self.hasOwnProperty('gridLink'))
				return;
			$('#'+self.gridLink).FormToGrid(self.cur_id,'#'+self.name);
		}
	});
}
$ajaxForm.prototype.destroy = function()
{
	for(var i = 0;i < this.eventHandlers.length;i++)
	{
		$('#'+this.name+' :input').unbind(this.eventHandlers[i]);
	}
	delete self;
}
