/**
 * @author
 */
(function ($, window, document, undefined) {
	"use strict";
	var pluginName = 'ddmenu',defaults = {};
	function Creator(el,options)
	{
		this.el = el;
		this.$el = $(el);
		this.$el_$lists = $(el).children('li');
		this.el_lists_width = 0;
		this.$lists = $(el).find('li');
		this.$subMenus = this.$el.find('ul');
		this.options = $.extend(defaults,options);
		this.init_elements();
		this.init();
	}
	Creator.prototype = {
		init_elements:function()
		{
			var self = this;
			this.caret = document.createElement('span');
			this.caret.className = 'sub-pointer';
			this.caret.innerHTML = '<i class="fa fa-caret-down"></i>';
			this.switcher = document.createElement('li');
			this.switcher.className = 'switcher';
			this.switcher.innerHTML = '<i class="fa fa-bars"></i>';
			this.switcher_width = 0;
			this.switcher.addEventListener("click", function(){
				$.each(self.$el_$lists,function(i,n){
					if(this.style.display == 'none')
						this.style.display = 'inline-block';
					else
						this.style.display = 'none';
				});
				self.el_lists_width = 0;
				self.calibrate();
			});
		},
		/* TODO separate this shit in pieces */
		init:function(){
			var self = this;
			this.calibrate();
			$.each(this.$lists,function(i,n)
			{
				if($(this).children('ul').length > 0)
					$(self.caret).clone().insertAfter($(this).children('a'));
				else
				{
					if($(this).parent('.menu-main-ul').length > 0)
						$(this).css('border-right','1px solid #10498D');
				}
			})
			$.each(this.$subMenus,function(i,n)
			{
				var width = 0;
				$.each($(this).find('li'),function(i,n)
				{
					if($(this).outerWidth() > width)
						width = $(this).outerWidth();
					else if($(this).outerWidth() < width)
						$(this).outerWidth(width);

				});
				$(this).width(width);
			})
			this.$lists.on('mouseenter',function(e)
			{
				if($(this).hasClass('disabled-li'))
					return false;
				var subMenu = $(this).children('ul');
				if(subMenu.length > 0)
				{
					$(this).children('.sub-pointer').children('i')
						.removeClass('fa-caret-down')
						.addClass('fa-caret-up');

					if($(this).parent('.menu-main-ul').length == 0)
					{
						subMenu.css({
							'left':$(this).outerWidth(),
							'top':0
						});
					}
					subMenu.show();
				}

			})
			.on('mouseleave',function(e){
				var until = $(this).parentsUntil('body');
				var target = e.relatedTarget,prevent = false;
				var subMenu = $(this).children('ul');
				$('.sub-pointer',this).find('i')
					.removeClass('fa-caret-up')
					.addClass('fa-caret-down');
				subMenu.hide();
			})
			.on('click',function(e)
			{
				e.preventDefault();
				e.stopPropagation();
				self.$subMenus.hide();
				if($(this).hasClass('disabled-li'))
					return false;
				if ($.isFunction( self.options.click ))
					self.options.click.call(this,e,self);
			})
			this.$el.find('a').on('click',function(e)
			{
				e.preventDefault();
				e.stopPropagation();
				$(this).parent('li').trigger('click');
			})
		},
		calibrate:function(){
			var self = this;
			$.each(this.$el_$lists,function(i,n){
				if(this.style.display == 'none')
					return false;
				self.el_lists_width += this.offsetWidth;
				if(self.el_lists_width > (window.innerWidth - 50))
					this.style.display = 'none';
				else
					self.switcher_width = window.innerWidth - self.el_lists_width - 55;
			})
			if(self.el_lists_width > window.innerWidth)
			{
				this.switcher_width += 'px';
				this.switcher.style.width = this.switcher_width;
				this.$el.append(this.switcher);
			}
		}
	};
	/* TODO usable for devices */
	/*
	 * $.fn reg
	 * */
	$.fn[pluginName] = function (options)
	{
		return this.each(function ()
		{
			if (!$.data(this, 'plugin_' + pluginName))
			{
				$.data(this, 'plugin_' + pluginName, new Creator(this, options));
			}
		});
	};
}(jQuery, window, document));
