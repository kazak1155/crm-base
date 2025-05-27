////////////////////////////////////////////////////////////////////////////////
// inlineautocomplete.js
// http://urvanov.ru
//
////////////////////////////////////////////////////////////////////////////////
(function() {
	function onReady() {

		// Данные в выбранной строке.
		var selectedRowData = null;

		// Новые данные в выбранной строке
		var newRowData = null;

		// id выбранной строки
		var lastselId = null;

		// Инициализация jqGrid
		$("#staffListsGrid").jqGrid({
			regional : "ru",           // Русская локаль
			forceRegional : true,
			url : "data.php",          // Данные берём с data.php
			mtype : "POST",
			datatype : "json",
			editurl : 'save.php',      // URL для сохранения изменений.

			// Будет вызываться перед отправкой данных в строке на сервер.
			// Преобразуем объект в строку JSON
			serializeRowData : function(postData) {
				return JSON.stringify(postData);
			},


			ajaxRowOptions : {
				contentType : 'application/json'
			},

			// При смене выделенной строки сохраняем изменения.
			onSelectRow: function(id){
				if(lastselId && id!==lastselId){
					saveCurrentRow();
				}
				calcId = $("#calcSelect").val();
				var grid = $("#staffListsGrid");
				selectedRowData = grid.jqGrid("getRowData", id);
				newRowData = grid.jqGrid("getRowData", id);
				grid.editRow(id, false);
				lastselId=id;

			},
			colModel : [ {
				label : 'ID',
				name : 'id',
				key : true,
				editable : false,
				hidden : true,
				sortable : false
			}, {
				label : 'Описание',
				name : 'descr',
				width : 200,
				editable : false,
				sortable : true,
				sorttype : 'text'
			},{
				label : 'personId',
				name : 'personId',
				editable : false,
				hidden : true,
				sortable : false
			},
				{
					label : 'ФИО',
					name : 'personName',
					width : 300,
					editable : true,
					editoptions : {

						dataInit : function (el) {
							// Основная магия здесь.
							// Инициализируем jQuery autocomplete.
							$(el).autocomplete( {
								source : function (request, response) {
									$.ajax({

										type : "POST",

										// Адрес для запроса на список подходящих
										// значений для автодополнения
										url :  'autocomplete.php',

										// Параметры.
										data : {
											term : request.term,
											calcId : calcId,
											personId : selectedRowData.personId
										},

										success : function(ret, textStatus, jqXhr) {
											// Возвращаем полученные данные.
											response(ret);
										},
										error : function (jqXhr, textStatus, errorThrown) {
											if (console)
												console.log("Autocomplete failed.");
										}
									});
								},

								select : function (event, ui) {

									// Сохраняем ID выбранной записи.
									newRowData.personId =  ui.item.value;

									// Подставляем надпись в сам элемент редактирования.
									$(el).val(ui.item.label);

									// Останавливаем дальнейшую обработку события.
									return false;
								},

								open : function ( event, ui ) {
									$(".ui-autocomplete").css("zIndex", 10000);
								}
							});
							$(el).on("keydown", function (event) {
								// Сохраняем строку при нажатии ВВОД.
								var code = event.which;
								if(code==13) event.preventDefault();
								if(code==32||code==13||code==188||code==186) {
									saveCurrentRow();
								}
							});
						}
					},
					sortable : true,
					sorttype : 'text'
				} ],
			viewrecords : true,
			width : 600,
			height : 'auto',
			rowNum : 20,
			pager : "#staffListsGridPager"
		});

		// Сохранение текущей редактируемой строки.
		function saveCurrentRow() {
			var grid = $("#staffListsGrid");

			// jqGrid при editRow создаёт элементы редактирования по схеме
			// rowid_columnName, где rowid - id строки,
			// columnName - name из colModel.
			var personNameEditElId='#'+selectedRowData.id+"_personName";

			if (!$(personNameEditElId).val()) {
				// Если поле ввода ФИО очищено, то очищаем первичный ключ.
				newRowData.personId = null;
			} else if (newRowData.personId == selectedRowData.personId) {
				// Восстанавливаем прежнее ФИО, если ничего не выбрано.
				$(personNameEditElId).val(selectedRowData.personName);
			}
			grid.jqGrid("setRowData", lastselId, {personId : newRowData.personId});
			grid.saveRow(lastselId, null, null, {
				personId : newRowData.personId
			});
		}

		var grid = $("#staffListsGrid");
		grid.trigger("reloadGrid");
	}
	$(document).ready(onReady);
})();