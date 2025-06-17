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
	// var rootNode = this.$tree_container.jstree(true).get_node('#');
	// this.selected_obj = rootNode.children[0]
	// console.log('111', )

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
			multiple:true,
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
			if (self.root && self.root.id) {
            self.$tree_container.jstree('select_node', self.root.id);
            self.selected_obj = self.$tree_container.jstree('get_node', self.root.id);
        	}
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
	// console.log('button: ' + button)
	// console.log('oper:' + oper)
	this.action_button = button;
	this.current_operation = oper;
	this.define('selection');
	if(this.query_prepare(oper) == false)
		return;
	switch (oper) {
		case 'add_folder':
			console.log('push the button add folder')
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
$fileTree.prototype.add_folder = function() {
    this.action_button.disabled = true;
    var self = this;

    // Получаем id текущего выбранного узла, куда добавляем новую папку
    var selectedId = this.$tree_container.jstree('get_selected')[0];
    if (selectedId == undefined) {
        // Если ничего не выделено, можно добавить в корень
		var rootNode = this.$tree_container.jstree(true).get_node('#');
		selectedId = rootNode.children[0];
    }
	console.log('selectedId:=', selectedId)
    var names = this.check_names('Новая папка', selectedId);
	var folder = new Object();
    var folder = {
        text: names[0] === false ? 'Новая папка' : 'Новая папка' + '(' + names[1] + ')',
        icon: 'fa fa-lg fa-folder-o'
    };
    this.selected_obj = this.$tree_container.jstree('get_node', selectedId);
    var create_node_callback = function(selectedId) {
        var tree = self.$tree_container.jstree(true);

        tree.deselect_all();
        tree.select_node(selectedId);

        self.selected = selectedId;
		// console.log('self.selected:=', self.selected)
		var guidPattern = /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/;
        var set_node_data = function(data) {
            var node = tree.get_node(self.selected);
			console.log('data:=', data)	

		// Проверяем, что data — строка и похожа на GUID		
		if (typeof data !== 'string' || !guidPattern.test(data)) {
			console.error('Некорректный stream_id:', data);
			self.action_button.disabled = false;
			return;
		}    

            // Обновляем пользовательские данные узла
            node.data = node.data || {};
            node.data.stream_id = data;

            self.define('selection');

            self.node_names.push({
                text: node.text,
                parent: node.parent
            });

            self.action_button.disabled = false;
        };

        self.rename(set_node_data);
    };

    this.$tree_container.jstree('create_node', selectedId, folder, 'first', create_node_callback);
};

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
$fileTree.prototype.rename = function(callback) {
    var self = this;
    var selectedIds = this.$tree_container.jstree('get_selected');
    if (selectedIds.length > 1) {
		$.alert('Выбрано более одного элемента элемента');
		domElement.text = editable_part;
		esc = true;
	} 

    var tree = this.$tree_container.jstree(true);
    var SelectNode = tree.get_node(selectedIds[0]);

    var isFolder = SelectNode.a_attr.directory == 1 || SelectNode.a_attr.directory === true ? true : false;

    var fullName = SelectNode.text;

    var editable_part = '';
    var non_editable_part = '';

    if (isFolder) {
        // Для папок редактируем всё имя
        editable_part = fullName;
        non_editable_part = '';
    } else {
        // Для файлов отделяем имя и расширение
        var matchName = fullName.match(/^[^\.]+/); // имя до первой точки
        var matchExt = fullName.match(/(\.[^\.]+)$/); // расширение (последняя точка и все после неё)

        editable_part = matchName ? matchName[0] : fullName;
        non_editable_part = matchExt ? matchExt[0] : '';
    }

    var rename_callback = function(domElement, status, esc) {
        var newName = domElement.text;

        // Проверяем новые имена на валидность
        var check = self.check_names(newName, SelectNode.parent);
        if (check[0] === true) {
            $.alert('Папка с таким именем уже существует в выбранном каталоге');
            domElement.text = editable_part;
            esc = true;
        }
        // Переименовываем узел, добавляя расширение обратно
		self.$tree_container.jstree('rename_node', SelectNode.id, newName + non_editable_part);

        if (esc === false) {
            // self.postData.name = newName;
			self.postData.name = newName + non_editable_part; // сохраняем полное имя с расширением
            self.query(true, callback);
        } else {
            if (self.current_operation === 'add_folder') {
                self.$tree_container.jstree('delete_node', self.selected);
                $('.tree_control').attr('disabled', false);
            }
            delete self.action_button;
            delete self.current_operation;
        }
    };

    this.$tree_container.jstree('edit', SelectNode, editable_part, rename_callback);
};



$fileTree.prototype.delete_node = function() {
    const self = this;
    const selectedIds = this.$tree_container.jstree('get_selected');
    
    console.log("выбранные файлы с IDs:", selectedIds);

    // Формируем сообщение подтверждения
    let msg;
    if (selectedIds.length === 1) {
        const node = this.$tree_container.jstree('get_node', selectedIds[0]);
        const msgText = node.text.length > 20 ? node.text.substr(0, 15) + '...' : node.text;
        msg = (node.a_attr?.directory == 1)
            ? `Вы уверены, что хотите удалить папку "${msgText}" и все её содержимое?`
            : `Вы уверены, что хотите удалить файл "${msgText}"?`;
    } else {
        msg = `Вы уверены, что хотите удалить ${selectedIds.length} выбранных элементов?`;
    }

    $.confirm({
        message: msg,
        width: '500px',
        done_func: function() {
            // Функция для последовательного удаления с задержкой
            const deleteNext = (index) => {
                if (index >= selectedIds.length) return; // Все элементы удалены

                const id = selectedIds[index];
                const node = self.$tree_container.jstree('get_node', id);
                console.log("Удаление элемента с ID:", id);
                
                // Формируем данные для отправки
                self.postData = {
                    directory: node.a_attr?.directory,
                    oper: "delete",
                    stream_id: id,
                };                
                // console.log("Отправляемые данные:", self.postData);
                
                // Удаляем текущий элемент
                self.$tree_container.jstree('delete_node', id);
                
                // Обновляем дерево и переходим к следующему элементу
                setTimeout(() => {
                    self.query(); // Обновляем состояние дерева
                    deleteNext(index + 1); // Рекурсивно удаляем следующий
                }, 300); // удаляем следующий файл через 300 милисекунд
            };
            deleteNext(0); // Начинаем с первого элемента
        },
        cancel_func: function() {
            $(this).dialog('close');
            delete self.action_button;
            delete self.current_operation;
        }
    });
};

$fileTree.prototype.refresh = function()
{
	this.$tree_container.jstree('deselect_all');
	this.$tree_container.jstree('refresh');
	$('.tree_control').attr('disabled',false);
}
$fileTree.prototype.define = function(type,data)
{
	// console.log('type:=', type)
	if(typeof type === typeof undefined)
		return;
	switch (type)
	{
		case 'selection':			
			// Получаем массив ID выбранных элементов
			const selectedIds = this.$tree_container.jstree('get_selected');
			// console.log(selectedIds)
			if(selectedIds.length === 1) {
				this.selected_obj = this.$tree_container.jstree('get_node',selectedIds[0]);
			}
			// Получаем массив объектов выбранных узлов
			this.selected_objects = selectedIds.map(id => {
				return this.$tree_container.jstree('get_node', id);
			});

		break;
		case 'deselect_all':
			delete this.selected;
			delete this.selected_obj;
		break;
		case 'node_names':
			this.node_list = data;
			// console.log('this.node_list:=', this.node_list)		
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
    this.postData = {};

    // Для операций добавления папки/файла логика своя
    if(oper != 'add_folder' && oper != 'add_file')
    {
        // Получаем выбранные узлы с объектами
        const selectedNodes = this.$tree_container.jstree('get_selected', true);

        if(selectedNodes.length === 0)
            return false;

        // Фильтруем, чтобы оставить только корневые выбранные узлы (без выбранных родителей)
        const roots = selectedNodes.filter(node => {
            let parent = node.parent;
            while(parent && parent !== '#') {
                if(selectedNodes.find(n => n.id === parent)) {
                    return false; // у узла выбран родитель — значит он не корень
                }
                parent = this.$tree_container.jstree('get_node', parent).parent;
            }
            return true;
        });

        if(roots.length === 0)
            return false;

        if(roots.length === 1) {
            const node = roots[0];
            if(!node.a_attr || !node.a_attr.stream_id)
                return false;

            this.postData.stream_id = node.a_attr.stream_id;
            this.selected_obj = node;
        } else {
            // Собираем массив stream_id, пропуская отсутствующие
            const streamIds = roots.map(node => node.a_attr && node.a_attr.stream_id)
                                   .filter(id => id != null);

            if(streamIds.length === 0)
                return false;			
            this.postData.stream_id = streamIds;
            this.selected_obj = roots[0]; // для удобства можно взять первый корневой узел

        }
    }
    else
    {
        if(this.selected_obj == false)
        {
            /* select root folder */
            this.$tree_container.jstree('select_node',this.root,true,false);
            /* redefine selected */
            this.define('selection');
            this.postData.stream_id = this.selected_obj.id;
        }
        else
        {
            /* if file selected, find its folder and select it */
            if(this.selected_obj.a_attr.directory == false)
            {
                this.$tree_container.jstree('deselect_all');
                this.$tree_container.jstree('select_node',this.selected_obj.parent);
                this.define('selection');
                this.postData.stream_id = this.selected_obj.id;
            }
            /* make sure node is selected */
            else
            {
                this.postData.stream_id = this.selected_obj.id;
            }
        }
    }

    // this.postData.directory = this.selected_obj.a_attr.directory;
	this.postData.directory = this.selected_obj.id;
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
		console.log(this.selected_obj)
		console.log('this.selected_obj.a_attr.directory == true', this.selected_obj.a_attr.directory == true)
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
