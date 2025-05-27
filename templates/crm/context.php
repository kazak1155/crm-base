<script>
	$(function() { //Запускаем обработку контекстного меню
		$('#list').contextMenu({
			selector: '.comment',
			callback: function(key, options) {
				var parentData = $(this).parent("tr").attr('id');
				document.getElementById('rw').value = parentData;
				document.getElementById('win').value = 1;
				//alert('LLLLLLLLLLLL');
				//$('#nMessa').removeClass('nMessArrived');
				if(key == 'Изменение_даты') { //Смотрим что выбрано из контекстного меню
					win4 = 1;
					$('#horizont4').show();
					getData2(parentData, key);
				}
				if(key == 'Смотреть' || key == 'Добавить'){
					document.getElementById('pKey').value = key;
					getData(parentData,key);
					$('#horizont3').show();
				}
				if(key == 'Делегировать'){
					win = 1;
					document.getElementById('horizont').style.display = 'block';//Открываем модальное окно
					delegate(parentData);
				}
				if(key == 'Удалить'){
					win = 1;
					alert(parentData);
					jQuery('#list').jqGrid('delGridRow',parentData);
				}
				if(key == 'Завершить'){
					$.post("/core/reqsave?m=crm_toarchive", { m: "crm_toarchive", rowID: parentData},
						function(){
							location.reload();
						}
					);
				}
			},
			items: {
				"Смотреть": {name: "Смотреть комментарии", icon: function(opt, $itemElement, itemKey, item)	{return 'context-menu-icon-fa fa-binoculars';}},
				"Добавить": {name: "Добавить комментарий", icon: "edit"},
				"Делегировать": {name: "Делегировать", icon: function(opt, $itemElement, itemKey, item)	{return 'context-menu-icon-fa fa-users';	}},
				"Завершить": {name: "Завершить/Восстановить", icon: "quit"},
				"Удалить": {name: "Удалить",icon: function(opt, $itemElement, itemKey, item){return 'context-menu-icon-fa fa-trash-o';	}}
			}
		});
	});
	$(function() { //Запускаем обработку контекстного меню
		$('#list').contextMenu({
			selector: '.finaldata, .contragent, .codemain, .reqtype, .tasksubj, .taskdescription, .codemain, .codegroup, .codeuser, .strtdata, .contdata',
			callback: function(key, options) {
				var parentData = $(this).parent("tr").attr('id'); //Выбираем ID строки из первой ячейки
				document.getElementById('rw').value = parentData; //Кладем его в долгий ящик (потом пригодится)
				document.getElementById('win').value = 1; //Устанавливам флаг, чтобы не закрыть окно в момент его открытия
				$('table#list td.cagent').each(function (i, elem){
					var lne = $(elem).parent("tr").attr('id');
					var tdText = $(elem).text();
					if(lne == parentData){
						callAgent = tdText;
						//alert(callAgent);
					}
				});
				
				//$('#nMessa').removeClass('nMessArrived');
				if(key == 'Изменение_даты') { //Смотрим что выбрано из контекстного меню
					win4 = 1;
					//alert(document.getElementById('win').value);
					$('#horizont4').show();
					getData2(parentData, key);
				}
				if(key == 'Делегировать'){
					win = 1;
					document.getElementById('horizont').style.display = 'block';//Открываем модальное окно
					delegate(parentData);
				}
				if(key == 'Удалить'){
					win = 1;
					jQuery('#list').jqGrid('delGridRow',parentData,{url:'/core/reqsave.php?del=1&table=Сотрудники_Задачи&tprefix=dbo&base=tig50&m=save_total'});
				}
				if(key == 'Документы') { //Смотрим что выбрано из контекстного меню
					$('#keyBlock').val('10');

					//alert(callAgent);
					
					
					$('#horizont19').show();
					filesProcW(parentData,callAgent);
					//alert(parentData+'  '+callAgent);
				}
				if(key == 'Контрагент'){
					win = 1;
					$.get("/core/srv_01.php", { m: "get_cagent_crm", l:parentData}, function(data){
						pdoerrors();
						$('#контрагент').val(Number(data)).trigger("chosen:updated");
						cag_w_on();
						cag_content(1);
					});
				}
				if(key == 'Завершить'){ //Делаем запись в базе: выбранный ID переносим в архив
					finishProcX(parentData);
					/*
					$.post("/core/reqsave?m=crm_toarchive", { m: "crm_toarchive", rowID: parentData},
						function(data){
						if(data == 0){
							location.reload();
						}else{
							jQuery('#list').trigger('reloadGrid');
						}
							//var aa = jQuery('#list').trigger('reloadGrid');
							//alert(JSON.stringify(aa));
						}
					);
					*/
				}
			},
			items: {
				"Изменение_даты": {name: "Изменить дату", icon: function(opt, $itemElement, itemKey, item){return 'context-menu-icon-fa fa-clock-o';}},
				"Делегировать": {name: "Делегировать", icon: function(opt, $itemElement, itemKey, item){return 'context-menu-icon-fa fa-users';	}},
				"Документы": {name: "Документы",   icon: function(opt, $itemElement, itemKey, item)	{return 'context-menu-icon-fa fa-file-word-o';}},
				"Контрагент": {name: "Контрагент", icon: function(opt, $itemElement, itemKey, item){return 'context-menu-icon-fa fa-user-circle-o';}},
				"Завершить": {name: "Завершить/Восстановить", icon: "quit"},
				"Удалить": {name: "Удалить",icon: function(opt, $itemElement, itemKey, item){return 'context-menu-icon-fa fa-trash-o';	}}
			}
		});
	});
	$(function() { //Запускаем обработку контекстного меню делегирования
		$('#list').contextMenu({
			selector: '.finaldata',
			callback: function(key, options) {
				var parentData = $(this).parent("tr").attr('id');
				document.getElementById('win').value = 1;
				getData2(parentData,key);
				$('#horizont4').show();
			},
			items: {
				"Изменение даты": {name: "View", icon: "edit"}
			}
		});
		$('.context-menu-one').on('click', function(e){
			console.log('clicked', this);
		})
	});

</script>