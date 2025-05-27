$.extend({ ajaxShort: function (source)
	{
		var self = this;
		this.options = new Object();
		this.options.type = typeof source.type !== typeof undefined ? source.type : 'POST';
		this.options.dataType  = typeof source.dataType  !== typeof undefined ? source.dataType : 'html';
		this.options.async = typeof source.async !== typeof undefined ? source.async : true;
		this.options.url = typeof source.url !== typeof undefined ? source.url : REQUEST_URL;
		this.reload = typeof source.reload !== typeof undefined ? source.reload : false;
		if(source.progress_bar == true)
		{
			let progress_bar_container = document.createElement('div');
			let progress_bar = document.createElement('div');
			progress_bar_container.className = 'ajax-progress-bar';
			progress_bar_container.appendChild(progress_bar);
			document.body.appendChild(progress_bar_container);

			this.options.contentType = false;
			this.options.processData = false;
			this.options.xhr = function()
			{
				let xhr = $.ajaxSettings.xhr();
				xhr.upload.addEventListener('progress', function(event)
				{
					if(event.total <= 2000)
						return;
					progress_bar_container.style.visibility = 'visible';
					progress_bar.style.width = Math.ceil(event.loaded / event.total * 100) + '%';
				}, false);
				return xhr;
			}
		}
		this.parseJson = typeof source.parseJson !== typeof undefined ? source.parseJson : true;
		if(typeof source.data !== typeof undefined)
		{
			if(source.data instanceof FormData === true)
			{
				this.options.data = source.data;
			}
			else
			{
				this.options.data = new Object();
				if(source.data instanceof Object)
				{
					this.options.data.oper = source.data.hasOwnProperty('oper') ?  source.data.oper : 'custom';
					if(source.data.hasOwnProperty('query'))
						source.data.query = encodeURIComponent(source.data.query);
					$.extend(this.options.data,source.data);
				}
				else
				{
					this.options.data.oper = 'custom';
					this.options.data.action = 'view';
					if(source.data.hasOwnProperty('query'))
						this.options.data.query = encodeURIComponent(source.data.query);
					this.options.data.responseType = typeof source.responseType !== typeof undefined ? source.responseType : 'single';
				}
			}
		}
		else
			return $.alert('No ajax data set.');

		if(typeof source.success === typeof undefined)
		{
			this.options.success = function(data)
			{
				self.response = data;
				if(source.progress_bar == true)
					document.getElementsByClassName('ajax-progress-bar')[0].remove()
				if(self.reload === true)
					location.reload();
			}
		}
		else
		{
			this.options.success = function(data)
			{
				if(source.progress_bar == true)
					document.getElementsByClassName('ajax-progress-bar')[0].remove()
				source.success(data);
			};
		}
		if(typeof source.ajax_opts !== typeof undefined)
			$.extend(this.options,source.ajax_opts);
		$.ajax(this.options);
		if(this.hasOwnProperty('response') && this.options.async === false)
			return this.parseJson ? JSON.parse(this.response) : this.response;
		if(source.hasOwnProperty('callback'))
			return source.callback();
	}
});
