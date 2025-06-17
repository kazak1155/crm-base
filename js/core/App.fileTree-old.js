function $fileTree(source)
{
	setter.call(this,source);
	if(!this.name)
		this.name = 'file_tree';
	if(!this.hasOwnProperty('controls'))
		this.controls = true;
	this.set_defaults();
	this.create_els();
}
/* jsTree defaults*/
$fileTree.prototype.set_defaults = function()
{
	if(this.controls === true)
	{
		var default_controls_ops = new Object();
		default_controls_ops.search = true;
		default_controls_ops.add = true;
		default_controls_ops.download = true;
		default_controls_ops.rename = true;
		default_controls_ops.del = true;
		default_controls_ops.refresh = true;
		if(this.hasOwnProperty('controls_ops'))
			$.extend(this.controls_ops,default_controls_ops)
		else
			this.controls_ops = default_controls_ops;
	}
	$(document).bind('dragover dragenter drop',function(e){ e.stopPropagation();	e.preventDefault(); })
}
/* jsTree navigation creator*/
$fileTree.prototype.create_els = function()
{
	this.tree_container = document.createElement('div');
	this.tree_container.id = this.name;
	this.tree_container.className = 'tree_container';
	this.$tree_container = $(this.tree_container);
	this.$tree_container.css({
		//'height':this.tname == 'global' ? 'calc(100% - 150px)':'245px',
		'height':'calc(100% - 150px)',
		'overflow-x':'hidden',
		'overflow-y':'auto'
	})
	if(this.controls === true)
	{
		var el,o = this.controls_ops,self = this;
		var controls_wrapper = document.createElement('div');
		controls_wrapper.id = 'controls_wrapper';

		if(o.search)
		{
			el = document.createElement('input');
			el.type = 'text';
			el.id = 'tree_search';
			el.className = 'tree_control';
			el.placeholder = 'Поиск...';
			$(el).on('keyup',function(e){
				if(self.search_timer)
					clearTimeout(self.search_timer)
				self.search_timer = setTimeout(function(){
					var v = $('#tree_search').val();
					self.$tree_container.jstree(true).search(v);
				},200);
			})
			controls_wrapper.appendChild(el);
		}
		if(o.add)
		{
			el = document.createElement('input');
			el.type ='file';
			el.multiple = "true";
			// el.style not working on IE >_<
			$(el).css('display','none');
			controls_wrapper.appendChild(el);

			el = document.createElement('button');
			el.id = 'tree_add_file';
			el.className = 'tree_button tree_control';
			el.innerHTML = '<i class="fa fa-file-o"></i><span>Добавить файл</span>';
			$(el).on('click',function(e){
				self.controls_actions('add_file',this);
			});
			controls_wrapper.appendChild(el);

			el = document.createElement('button');
			el.id = 'tree_add_folder';
			el.className = 'tree_button tree_control';
			el.innerHTML = '<i class="fa fa-folder-o"></i><span>Создать папку</span>';
			$(el).on('click',function(e){
				self.controls_actions('add_folder',this);
			})
			controls_wrapper.appendChild(el);
		}
		if(o.download)
		{
			el = document.createElement('button');
			el.id = 'tree_dowload_file';
			el.className = 'tree_button tree_control';
			el.innerHTML = '<i class="fa fa-download"></i><span>Скачать</span>';
			if(get_agent(true) == true)
			{
				$(el).css('opacity','0.5');
				el.disabled = true;
			}
			else
			{
				$(el).on('click',function(e){
					self.controls_actions('download',this);
				})
			}
			controls_wrapper.appendChild(el);
		}
		if(o.rename)
		{
			el = document.createElement('button');
			el.id = 'tree_rename';
			el.className = 'tree_button tree_control';
			el.innerHTML = '<i class="fa fa-pencil-square-o"></i><span>Переименовать</span>';
			$(el).on('click',function(e){
				self.controls_actions('rename',this);
			})
			controls_wrapper.appendChild(el);
		}
		if(o.del)
		{
			el = document.createElement('button');
			el.id = 'tree_delete';
			el.className = 'tree_button tree_control';
			el.innerHTML = '<i class="fa fa-trash-o"></i><span>Удалить файл</span>';
			$(el).on('click',function(e){
				self.controls_actions('delete',this);
			})
			controls_wrapper.appendChild(el);
		}
		if(o.refresh)
		{
			el = document.createElement('button');
			el.id = 'tree_refresh';
			el.className = 'tree_button tree_control';
			el.innerHTML = '<i class="fa fa-refresh"></i><span>Обновить</span>';
			$(el).on('click',function(e){
				self.refresh();
			})
			controls_wrapper.appendChild(el);
		}
		document.body.appendChild(controls_wrapper);
	}
	document.body.appendChild(this.tree_container);
	var progressBar = document.createElement('div');
	progressBar.className = 'jstree-progressbar';
	$(progressBar).progressbar({
		complete: function() {
			$(this).fadeOut("slow");
		}
	});
	document.body.appendChild(progressBar);
	this.create_tree();
}
/* jsTree creator */
$fileTree.prototype.create_tree = function()
{
	var self = this;
	this.$tree_container.jstree({
		core:{
			check_callback:function(operation, node, node_parent, node_position, more)
			{
				if(operation === 'move_node')
				{

					if(more.hasOwnProperty('ref'))
					{
						var target = more.ref;
						if(target.a_attr.directory == false)
							return false;
					}
				}
				return true;
			},
			data:{
				dataType:'json',
				url:'/core/file_system/file_stream/fs_request',
				type:'post',
				data:{
					oper:'view'
				},
				success:function(responce)
				{
					self.root = responce[0];
					self.define('node_names',responce);
					self.tree_container.$jstree = self;

				}
			},
			animation:false,
			multiple:false,
			themes:{
				dots:true,
				stripes:true
			}
		},
		plugins:['dnd','search','wholerow'],
		dnd:{
			copy:false,
			touch:true,
			is_draggable:function(data)
			{
				return true;
				/*
				if(data[0].a_attr.directory == true)
					return false;
				else
					return true;
				*/
			}
		}
	})
	.on({
		'move_node.jstree':function(e,data){
			var node_moved = data.node,msg;
			var node_old_parent = self.$tree_container.jstree('get_node',data.old_parent);
			var node_new_parent = self.$tree_container.jstree('get_node',data.parent);
			var check = self.check_names(node_moved.text,node_new_parent.id);
			self.postData = new Object();
			self.postData.oper = 'move_node';
			self.postData.oper = 'move_node';
			self.postData.node_moved = {stream_id:node_moved.a_attr.stream_id,directory:node_moved.a_attr.directory,name:node_moved.text};
			if(node_moved.a_attr.directory == true)
				return $.alert("Перемещение папок не поддерживается.");
			self.postData.node_old_parent = node_old_parent.a_attr.stream_id;
			self.postData.node_new_parent = node_new_parent.a_attr.stream_id;

			if(check[0] == true)
			{
				if(node_moved.a_attr.directory == true)
					return $.alert("Перемещение папок с совпадающими файлами или папками не поддерживается.");
					//msg = 'В папке назначения уже есть папка "'+ node_moved.text + '". Заменить совпадающие файлы ?';
				else
					msg = 'В папке назначения уже есть файл "'+ node_moved.text + '". Заменить файл ?';
				$.confirm({
					width:'400px',
					message:msg,
					done_func:function()
					{
						self.postData.replaceFile = true;
						self.postData.replaceId = self.node_list[check[1]].a_attr.stream_id;
						self.query(true,false,{block:true});
					}
				});
			}
			else
				self.query(true,false,{block:true});
		},
		'after_open.jstree after_close.jstree':function(e,data){
			var type = e.type
			if(type === 'after_open')
			{
				$(this).jstree(true).set_icon(data.node.id,'fa fa-lg fa-folder-open-o');
			}
			else if(type === 'after_close')
			{
				$(this).jstree(true).set_icon(data.node.id,'fa fa-lg fa-folder-o');
			}
		},
		'select_node.jstree': function(e,data){
			if(data.node.a_attr.directory == false)
			{
				if(self.selected_node === data.node.id) // double click workaround
				{
					window.open(data.node.a_attr.href);
					delete self.selected_node;
				}
				else
					self.selected_node = data.node.id
			}
		},
		'ready.jstree':function(e,data){
			self.$tree_container.jstree('show_dots');
		},
		'dragover':function(e){
			e.stopPropagation();
			e.preventDefault();
		},
		'dragenter': function(e){
			e.stopPropagation();
			e.preventDefault();
			var target = e.target;
			var target_node_object = self.$tree_container.jstree('get_node',$(target).closest('li'));
			var dataTransfer = e.dataTransfer || e.originalEvent.dataTransfer;
			if(this == target)
				return e.originalEvent.dataTransfer.dropEffect = 'none';
			if(e.type !== 'dragenter')
				return;

			self.$tree_container.jstree('deselect_all',true);
			self.define('deselect_all');

			if(target_node_object.a_attr.directory == true)
			{
				self.$tree_container.jstree('select_node',target_node_object);
				self.define('selection');
			}
			else
			{
				self.selected = document.getElementById(target_node_object.id);
				self.selected_obj = self.$tree_container.jstree('get_node',self.selected);
			}
			self.query_prepare('add_file');
		},
		'drop':function(e){
			e.stopPropagation();
			e.preventDefault();
			var target = e.target;
			var dataTransfer = e.dataTransfer || e.originalEvent.dataTransfer;
			self.query_prepare_add_file(dataTransfer.files);
		}
	});
	$(document).on('keyup',function(e){
		if(e.which == 46 && typeof self.action_button == typeof undefined)
		{
			if(self.hasOwnProperty('selected_obj') == true)
				self.controls_actions('delete');
		}
	})
}
$fileTree.prototype.controls_actions = function(oper,button)
{
	this.action_button = button;
	this.current_operation = oper;
	this.define('selection');
	if(this.query_prepare(oper) == false)
		return;
	switch (oper) {
		case 'add_folder':
			this.add_folder();
			break;
		case 'add_file' :
			this.add_file();
			break;
		case 'rename' :
			this.rename();
			break;
		case 'download':
			var download = document.createElement('a');
			download.setAttribute('href',this.selected_obj.a_attr.href);
			download.setAttribute('download', this.selected_obj.text);
			download.style.display = 'none';
			document.body.appendChild(download);
			download.click();
			document.body.removeChild(download);
			break;
		case 'delete' :
			this.delete_node();
			break;
		default :
			this.query(true);
	}
}
$fileTree.prototype.add_folder = function()
{
	/* disable button */
	this.action_button.disabled = true;
	var self = this;
	var folder = new Object();
	var names = this.check_names('Новая папка',this.selected_obj.id);
	folder.text = names[0] == false ? 'Новая папка' : 'Новая папка' + '('+ names[1] +')' ;
	folder.icon = 'fa fa-lg fa-folder-o';
	// console.log('node"=', node)
	var create_node_callback = function(node)
	{
		self.$tree_container.jstree('deselect_all');
		self.$tree_container.jstree('select_node',node.id);
		self.selected = document.getElementById(self.$tree_container.jstree('get_selected'));
		var set_node_data = function(data)
		{
			self.$tree_container.jstree(true).get_node(self.selected).a_attr.stream_id = data;
			self.$tree_container.jstree(true).get_node(self.selected).a_attr.parent = self.$tree_container.jstree(true).get_node(self.selected).parent;
			self.$tree_container.jstree(true).get_node(self.selected).a_attr.directory = 1;
			self.define('selection');
			/* enable button */
			self.node_names.push({text: self.$tree_container.jstree(true).get_node(self.selected).text,parent:self.$tree_container.jstree(true).get_node(self.selected).parent})
			self.action_button.disabled = false;
		}
		self.rename(set_node_data);
	}
	this.$tree_container.jstree('create_node',this.selected.id,folder,'first',create_node_callback)
}
$fileTree.prototype.add_file = function()
{
	var self = this;
	var $file_input = $(this.action_button).siblings('input[type="file"]');
	var file_name;
	$file_input.on('change',function(e){
		self.query_prepare_add_file(this.files);
		/* remove selected file */
		this.value = null;
		/* remove change event */
		$(this).unbind('change');
	})
	$file_input.trigger('click');
}
$fileTree.prototype.rename = function(callback)
{
	var self = this;
	var isFolder = this.selected_obj.a_attr.directory == true ? true : false;
	var $selected_anchor = $(this.selected).children('a');
	var editable_part = $selected_anchor.text().match(/^[^\.]*/)[0];
	var non_editable_part = new String();


	if(!isFolder)
		non_editable_part = $selected_anchor.text().match(/\..*/)[0];
	var rename_callback = function(node,status,esc){
		var check = self.check_names(node.text,node.parent);
		if(check[0] == true)
		{
			$.alert('Папка с таким именем уже существует в выбранном каталоге');
			node.text = editable_part;
			esc = true;
		}
		self.$tree_container.jstree('rename_node',self.selected,node.text + non_editable_part);
		if(esc == false)
		{
			self.postData.name = node.text;
			self.query(true,callback);
		}
		else
		{
			if(self.current_operation == 'add_folder')
			{
				self.$tree_container.jstree('delete_node',self.selected);
				$('.tree_control').attr('disabled',false);
			}
			delete self.action_button;
			delete self.current_operation;
		}

	}
	this.$tree_container.jstree('edit',this.selected,editable_part,rename_callback);
}
$fileTree.prototype.delete_node = function()
{
	if(this.selected_obj.a_attr.root == true)
		return;
	var msg,msg_text,self = this;
	if(this.selected_obj.text.length > 20)
		msg_text = this.selected_obj.text.substr(0,15) + '...';
	else
		msg_text = this.selected_obj.text
	if(this.selected_obj.a_attr.directory == true)
		msg = 'Вы уверены что хотите удалить папку "' + msg_text + '" и все её содержимое?';
	else
		msg = 'Вы уверены что хотите удалить файл "' + msg_text + '"?';
	$.confirm({
		message:msg,
		width:'500px',
		done_func:function()
		{
			self.$tree_container.jstree('delete_node',self.selected);
			self.query();
		},
		cancel_func:function()
		{
			$(this).dialog('close');
			delete self.action_button;
			delete self.current_operation;
		}
	});
}
$fileTree.prototype.refresh = function()
{
	this.$tree_container.jstree('deselect_all');
	this.$tree_container.jstree('refresh');
	$('.tree_control').attr('disabled',false);
}
$fileTree.prototype.define = function(type,data)
{
	if(typeof type === typeof undefined)
		return;
	switch (type)
	{
		case 'selection':
			this.selected = document.getElementById(this.$tree_container.jstree('get_selected'));
			this.selected_obj = this.$tree_container.jstree('get_node',this.selected);
		break;
		case 'deselect_all':
			delete this.selected;
			delete this.selected_obj;
		break;
		case 'node_names':
			this.node_list = data;
			if(typeof data == typeof undefined)
				return false;
			this.node_names = Array();
			for(var i = 0;i < data.length; i++)
			{
				this.node_names[i] = Object();
				if(data[i].text.length == 0)
				{
					this.node_names[i].text = 'root';
					this.node_names[i].parent = '#';
					this.node_names[i].type = 'folder';
				}
				else
				{
					this.node_names[i].text = data[i].text;
					this.node_names[i].parent = data[i].parent;
					this.node_names[i].type = data[i].type;
				}
			}
		default: break;

	}
}
$fileTree.prototype.check_names = function(text,parent,type)
{
	if(typeof text === typeof undefined || typeof parent === typeof undefined)
		return;
	var index = 1,exact = false,exact_index,pattern = new RegExp(/.*\([0-9]\)/);
	for(var i = 0; i < this.node_names.length; i++)
	{
		if(this.node_names[i].parent == parent)
		{
			if(this.node_names[i].text == text)
			{
				exact = true;
				exact_index = i;
			}
			else if(this.node_names[i].type == 'folder' && pattern.test(this.node_names[i].text) == true)
			{
				index++;
			}
		}
	}
	if(index > 1)
		return [true,index];
	else
	{
		if(exact == true)
			return [true,exact_index];
	}
	return [false];
}
$fileTree.prototype.query_prepare = function(oper)
{
	this.postData = new Object();
	if(oper != 'add_folder' && oper != 'add_file')
	{
		if(this.selected_obj == false)
			return false;
		this.postData.stream_id = this.selected_obj.a_attr.stream_id;
	}
	else
	{
		if(this.selected_obj == false)
		{
			/* select root folder */
			this.$tree_container.jstree('select_node',this.root,true,false);
			/* redefine selected */
			this.define('selection');
			this.postData.stream_id = this.selected_obj.a_attr.stream_id;
		}
		else
		{
			/* if file selected, find its folder and select it */
			if(this.selected_obj.a_attr.directory == false)
			{
				this.$tree_container.jstree('deselect_all');
				this.$tree_container.jstree('select_node',this.selected_obj.parent);
				this.define('selection');
				this.postData.stream_id = this.selected_obj.a_attr.stream_id;
			}
			/* make sure node is selected */
			else
			{
				this.postData.stream_id = this.selected_obj.a_attr.stream_id;
			}

		}
	}
	this.postData.directory = this.selected_obj.a_attr.directory;
	this.postData.oper = oper;
	return true;
}
$fileTree.prototype.query_prepare_add_file = function(file)
{
	var self = this;
	var ajax_opts = new Object();
	var fileData = new FormData();
	ajax_opts.processData = false;
	ajax_opts.contentType = false;
	ajax_opts.block = true;
	ajax_opts.xhr = function()
	{
		var $xhr = $.ajaxSettings.xhr();
		$xhr.upload.addEventListener('progress',function(e){
			$('.jstree-progressbar').show();
			$('.jstree-progressbar').progressbar("option", "value", e.loaded / e.total * 100);
		}, false);
		return $xhr;
	};
	if(file.length == 1)
	{
		var file = file[0];
		if(this.selected_obj.a_attr.directory == true)
		{
			var check = this.check_names(file.name,this.selected_obj.id);
			if(check[0] == true)
			{
				$.confirm({
					message:'В папке назначения уже есть файл "'+ file.name + '". Заменить файл ?',
					done_func:function()
					{
						fileData.append('uploaded_file',file);
						self.postData.replaceFile = true;
						self.postData.replaceId = self.node_list[check[1]].a_attr.stream_id;
						fileData.append('postData',JSON.stringify(self.postData));
						self.postData = fileData;
						self.query(true,false,ajax_opts);
					},
					cancel_func:function()
					{
						$(this).dialog('close');
						delete self.action_button;
						delete self.current_operation;
					}
				});
			}
			else
			{
				fileData.append('uploaded_file',file);
				fileData.append('postData',JSON.stringify(this.postData));
				this.postData = fileData;
				this.query(true,false,ajax_opts);
			}
		}
	}
	else if(file.length > 1)
	{
		this.postData.replaceId = [];
		var check;
		$.asyncloop({
			length:file.length,
			loop_action:function(loop,i){
				check = self.check_names(file[i].name,self.selected_obj.id);
				if(check[0] == true)
				{

					self.postData.replaceFile = true;
					$.confirm({
						classNames:'filecheck',
						file:file[i],
						checkIndex:check[1],
						loopIndex:i,
						message:'В папке назначения уже есть файл "'+ file[i].name + '". Заменить файл ?',
						done_func:function()
						{
							var dialog_file = $(this).data('confirm').file;
							var dialog_node_index = $(this).data('confirm').checkIndex;
							var dialog_loop_index = $(this).data('confirm').loopIndex;
							fileData.append('uploaded_file_'+dialog_loop_index,dialog_file);
							self.postData.replaceId.push(self.node_list[dialog_node_index].a_attr.stream_id);
						},
						cancel_func:function()
						{
							$(this).dialog('close');
							delete self.action_button;
							delete self.current_operation;
						}
					});
					loop();
				}
				else
				{
					fileData.append('uploaded_files_'+i,file[i]);
					loop();
				}
			},
			callback:function(){
				var wait_for_dialog = setInterval(function(){
					if($('.filecheck').length == 0)
					{
						fileData.append('postData',JSON.stringify(self.postData));
						self.postData = fileData;
						self.query(true,false,ajax_opts);
						clearInterval(wait_for_dialog);
					}
				},500);
			}
		})
	}
}
$fileTree.prototype.query = function(refresh,callback,ajaxOpts)
{
	var self = this;
	var ajaxParams = {
		url:'/core/file_system/file_stream/fs_request',
		data:this.postData,
		type:'post',
		beforeSend:function(data,opts)
		{
			if(opts.block == true)
				self.$tree_container.block({message: null,overlayCSS:{cursor:'default'},baseZ:100});
		},
		success:function(data)
		{
			self.$tree_container.unblock();
			if(callback)
				callback(data);
			if(refresh == true)
				self.refresh();
			self.define('node_names',self.$tree_container.jstree().get_json('#',{flat:true}));
			delete self.action_button;
			delete self.current_operation;
		},
		error:function(xhr,status,err)
		{
			self.$tree_container.unblock();
			$.alert(xhr.responseJSON.message);
		}
	};
	if(typeof ajaxOpts !== typeof undefined)
		$.extend(ajaxParams,ajaxOpts);
	$.ajax(ajaxParams);
}