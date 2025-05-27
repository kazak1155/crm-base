function $ajaxForm_(source)
{
	setter.call(this,source);
	this.create_markup();
	this.form_element = $('#'+this.name);
	this.form_element_pure = this.form_element[0];

	//this.set_defaults();
	//this.set_form_values();

	/*
	if(this.floatLabel === true)
		this.bind_ident();
	if(this.view_only === true)
	{

		$('#'+this.name+' :input').attr('disabled',true).on('keydown',function(event)
		{
			return false;
		})
		return;
	}


	if(!this.hasOwnProperty('eventHandlers') || !$.isArray(this.eventHandlers))
		return $.alert('Handlers not set or set not as array');
	if(!this.hasOwnProperty('name') || $('#'+this.name).length === 0)
		return $.alert('Element name not set or there is no such element');

	this.bind_events();
	*/
}
$ajaxForm_.prototype.create_markup = function()
{
	var wrapper = document.createElement('div');
	wrapper.className = 'aj-form-wrapper';
	this.form_element = document.createElement('form');
	this.form_element.id = this.name;
	this.form_element.className = 'aj-form';
	this.$form_element = this.form_element;

	this.create_form_markup();
}
$ajaxForm_.prototype.create_form_markup = function()
{
	if(!this.formlabels)
		return alert('Labels missing');
	if(!this.formelements)
		return alert('Elements missing');
	if(this.formlabels.length != this.formelements.length)
		return alert('Formlabels length not equals formelements');
	var default_el = {
		eltype:'input',
		width:'100%',
		contentType:'text'
	};
	this.fe = new Array();
	for(var i = 0; i < this.formelements.length; i++)
	{
		this.fe[i] = $.extend({},default_el,this.formelements[i])
	}
	console.log(this.fe)
}
$ajaxForm_.prototype.test = function()
{
	console.log('kek');
}
/*
$ajaxForm.prototype.set_defaults = function()
{
	var self = this;
	this.form_element.bind('reset',function(e){
		self.set_form_values();
	})
}
$ajaxForm.prototype.set_form_values = function()
{
	var self = this;
	this.form_values = new Object();
	$.each($('#'+this.name+' :input'),function(i,n){
		if(this.disabled == false)
		{
			if(this.type === 'checkbox')
			{
				if(this.value || this.checked)
					self.form_values[this.name] = Number(this.checked);
			}
			else
			{
				if(this.value)
					self.form_values[this.name] = Number(this.checked) || this.value;
			}
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
			if(self.hasOwnProperty('eventExtend'))
				self.eventExtend.call(this,event,self.form_element,self);
			if(self.subform == true)
				self.cur_id = self.form_element.find('input[subname="'+self.id+'"]').val();
			else
				self.cur_id = self.form_element.find('input[name="'+self.id+'"]').val();

			if(prevented)
				return;

			if (event.type === 'change' && this.type === 'checkbox')
				self.input_value = this.value = Number(this.checked);
			else
			{
				if(self.input_name == this.name && self.input_value == this.value)
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
			self.current_input = this;
			self.send_data();
		})
	}

}
$ajaxForm.prototype.send_data = function()
{
	var oper = 'edit',self = this,els; // fucking scope...
	if(!this.cur_id)
		return $.alert('Не указан id.');
	if(this.add === true)
	{
		if(this.subform == true)
		{

			els = $('#'+this.form_element_pure.id+' input[subname!="'+this.id+'"]').not(this.current_input).filter(function(){ return this.value || this.checked; });
			if(els.length == 0 && Object.keys(this.form_values).length == 0)
				oper = 'add';
		}

		else
		{
			els = $('#'+this.form_element_pure.id+' input[name!="'+this.id+'"]').not(this.current_input).filter(function(){ return this.value || this.checked; });
			if(els.length == 0 && Object.keys(this.form_values).length == 1)
				oper = 'add';
		}
	}
	clearTimeout(this.timeout);
	var timer = setTimeout(function(){
		var data = {
			oper:oper,
			tid:self.id,
			tname:self.table,
			id:self.cur_id
		};
		if(oper ==='add')
		{
			delete data.tid;
			delete data.id;
			data[self.id] = parseInt(self.cur_id);
		}

		data[self.input_name] = self.input_value
		$.ajaxShort({
			data:data,
			success:function(data)
			{
				if(data.length > 10)
					return $.alert(data);

				if(!self.hasOwnProperty('gridLink'))
					return;
				$('#'+self.gridLink).FormToGrid(self.cur_id,'#'+self.name);
				self.set_form_values();
			}
		})
	},300);
	this.timeout = timer;
}
$ajaxForm.prototype.bind_ident = function()
{
	$.each(this.form_element.find('input[type="text"]:visible'),function(i,n)
	{
		this.id = this.name;
		$(this).siblings('label').attr('for',this.name);
		if($(this).hasClass('dp'))
			$(this).datepicker();
	})
}
*/
