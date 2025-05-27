/**
 * @author 106
 */
/*
 * TODO optimize
 * */
(function ($, window, document, undefined) {
	"use strict";
	var pluginName = 'sidescroll',
		defaults = {
			barsWidth:25,
			scrollOffset:2,
			delay:0
		};
	function Creator(el,options)
	{
		this.el = el;
		this.$el = $(el);
		this.options = $.extend(defaults,options);
		this.init();
	}
	Creator.prototype = {
		init:function(){
			var self = this;
			this.$el.mousemove(function(e){
				if($('.side-scroll-bar').length > 0)
					return;
				
				var target = this;
				var overflowed = checkOverflow(this,undefined,'horizontal');
				if(overflowed == false)
					return;
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
				var pseudo_edge = self.options.barsWidth;
				var scroll_left = $(this).scrollLeft();
				var interval;
				
				
				if(overflowed == false)
					return;
				
				var hasVScroll = $(this).css('overflow') == "auto" || $(this).css('overflow-y') == "auto" || $(this).css('overflow') == 'scroll' || $(this).css('overflow-y') == 'scroll';
				if(hasVScroll == true)
					right_edge -= 17;
				//$(this).css('overflow','')
				
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
								bar.style.right = 0 - (scroll_left + self.options.scrollOffset) + 'px';
								target.scrollLeft = scroll_left + self.options.scrollOffset;
								scroll_left += self.options.scrollOffset;
							}
							else if(direction == 'left' && scroll_left > 0)
							{
								bar.style.left = 0 + (scroll_left - self.options.scrollOffset) + 'px';
								target.scrollLeft = scroll_left - self.options.scrollOffset;
								scroll_left -= self.options.scrollOffset;
							}
						},self.options.delay);
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
			
		}
	}
	/*
	 * $.fn reg
	 * */
	$.fn[pluginName] = function (options)
	{
		return this.each(function ()
		{
			if (!$.data(this, 'plugin_' + pluginName))
			{
				$.data(this, 'plugin_' + pluginName,
				new Creator(this, options));
			}
		});
	};
}(jQuery, window, document));