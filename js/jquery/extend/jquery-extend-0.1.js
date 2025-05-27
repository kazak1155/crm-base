/**
 * @author darkr_000
 */
$.datepicker._gotoToday = function(id)
{
	var inst = $(id).data('datepicker');
	if(inst.range === true || inst.inline === true)
	{
		var selectedDate = getDate(true);
		if(!inst.first)
		{
			$('.dp_alert').html('Выберите <strong style="color:#FF0000">вторую</strong> дату из интервала');
			inst.inline = true;
			inst.first = selectedDate;
			$(id).val(selectedDate);
		}
		else
		{
			if(selectedDate > inst.first)
				$(id).val(inst.first+":"+selectedDate);
			else if (selectedDate < inst.first)
				$(id).val(selectedDate+":"+inst.first);
			$('.dp_alert').dialog('destroy');
			inst.inline = false;
			inst.range = false;
			this._hideDatepicker($(id)[0]);
		}
	}
	else
	{
		$(id).val(getDate(true));
		this._hideDatepicker($(id)[0]);
	}
}
$.expr[':'].containsIgnoreCase = function (n, i, m)
{
	return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};
$.fn.serializeObject = function()
{
	var data = {};
	$.each($(this).serializeArray(), function(_, kv) {
		data[kv.name] = kv.value;
	});
	return data;
};
$.fn.center = function (top,left)
{
	top = typeof top === typeof undefined ? 0 : top;
	left = typeof left === typeof undefined ? 0 : left;

	this.css("position","absolute");
	this.css("top", Math.max(0, (top + ($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
	this.css("left", Math.max(0, (left + ($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	return this;
};
$.fn.taphold = function (callback,timeout) {
	if( typeof timeout == typeof undefined ) timeout = 1000;
   $(this).bind("touchstart", function (event) {
		event.preventDefault();
		event.stopImmediatePropagation();
		event.stopPropagation();
   	var initialEvent = event;
		var self = this
   	var timer = window.setTimeout(function ()
		{
			callback.call(self,initialEvent);
		}, timeout);
		$(document).bind("touchend touchcancel", function () {
   		window.clearTimeout(timer);
      	$(document).unbind("touchend touchcancel");
			return true;
		});
   	return true;
	});
};
$.event.special.taphold = {
	delegateType: "touchstart",
	bindType: "touchstart",
	timeout:500,
	handle:function(event){
		/* Pull X/Y */
		event.pageX = event.originalEvent.touches[0].pageX;
		event.pageY = event.originalEvent.touches[0].pageY;
		var initialEvent = event;
		var self = this;
   	var timer = setTimeout(function ()
		{
			initialEvent.handleObj.handler.call(self,initialEvent);
		}, $.event.special.taphold.timeout);
		$(this).bind("touchend touchcancel", function () {
   		clearTimeout(timer);
      	$(this).unbind("touchend touchcancel");
			return true;
		});
   	return true;
	}
};
$.fn.copyEventTo = function(eventType, destination, clearCurrent)
{
	var events = [];
	this.each(function()
	{
	    var allEvents = jQuery._data(this, "events");
		if (typeof allEvents === "object")
		{
			var thoseEvents = allEvents[eventType];
			if (typeof thoseEvents === "object")
			{
	    		for (var i = 0; i<thoseEvents.length; i++)
	    		{
					events.push(allEvents[eventType][i].handler);
	   			}
			}
	    }
	});
	if (typeof destination === "string")
	    destination = $(destination);
	else if (typeof destination === "object")
	{
	    if (typeof destination.tagName === "string")
			destination = $(destination);
	}
	if (clearCurrent === true)
		destination.off(eventType);

	destination.each(function()
	{
	    for(var i = 0; i<events.length; i++)
	    {
			destination.bind(eventType, events[i]);
	    }
	});
	return this;
}
$.extend({
	capitalize: function(string){
		var capString = string.toLowerCase(),firstChar;
		firstChar = (capString.charAt(0)).toUpperCase();

		capString = capString.substring(1)

		capString = firstChar.concat(capString)

		return capString;
	}
})
$.extend({
	normalize: function(string){
		var capString = string.replace(/([a-zа-я])([A-ZА-Я])/g, "$1 $2");

		return capString;
	}
})
$.extend({
	grepStop : function(value,array,prop,prop_search,index)
	{
		for (var i = 0;i < array.length; i++)
		{
			if(typeof prop_search !== typeof undefined && array[i][prop] === value)
			{
				if(array[i][prop_search[0]] === prop_search[1])
				{
					if(typeof index !== typeof undefined)
					{
						return {val:true,index:i};
					}
					else
						return true;
					}
			}
			else if (typeof prop_search === typeof undefined && array[i][prop] === value)
			{
				if(typeof index !== typeof undefined)
				{
					return {val:true,index:i}
				}
				else
					return true;
			}

		}
		return false;
	}
});
$.extend({
	asyncloop:function(source)
	{
		var i = -1,length = source.length;
		if(!length)
			return $.alert('Loop length not set');
		if(!source.hasOwnProperty('loop_action'))
			return $.alert('Loop loop_action not set');
		var loop = function()
		{
			i++;
			if(i == length)
			{
				if(source.hasOwnProperty('callback'))
					source.callback();
				return;
			}
			source.loop_action(loop,i);
		};
		loop();
	}
});
