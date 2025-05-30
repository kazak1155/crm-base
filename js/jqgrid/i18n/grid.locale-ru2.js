;(function($){
	/**
	 * jqGrid Russian Translation v1.0 02.07.2009 (based on translation by Alexey Kanaev v1.1 21.01.2009, http://softcore.com.ru)
	 * Sergey Dyagovchenko
	 * http://d.sumy.ua
	 * Dual licensed under the MIT and GPL licenses:
	 * http://www.opensource.org/licenses/mit-license.php
	 * http://www.gnu.org/licenses/gpl.html
	 **/
	$.jgrid = $.jgrid || {};
	$.extend($.jgrid,{
		defaults : {
			recordtext: "Просмотр {0} - {1} из {2}",
			emptyrecords: "Нет записей для просмотра",
			loadtext: "Загрузка...",
			pgtext : "Стр. {0} из {1}"
		},
		search : {
			caption: "Поиск...",
			Find: "Найти",
			Reset: "Сброс",
			odata: [{ oper:'eq', text:"равно"},{ oper:'ne', text:"не равно"},{ oper:'lt', text:"меньше"},{ oper:'le', text:"меньше или равно"},
				{ oper:'gt', text:"больше"},{ oper:'ge', text:"больше или равно"},{ oper:'bw', text:"начинается с"},{ oper:'bn', text:"не начинается с"},
				{ oper:'in', text:"находится в"},{ oper:'ni', text:"не находится в"},{ oper:'ew', text:"заканчивается на"},{ oper:'en', text:"не заканчивается на"},
				{ oper:'cn', text:"содержит"},{ oper:'nc', text:"не содержит"},{ oper:'nu', text:"равно NULL"},{ oper:'nn', text:"не равно NULL"},
				{ oper:'dateEq',text:"равно"},{oper:'dateNe',text:"не равно"},{oper:'dateLe',text:"меньше или равно"},{oper:'dateGe',text:"больше или равно"}],
			groupOps: [	{ op: "AND", text: "Все из ( - И - )" }, { op: "OR", text: "Любой из (- Или - )" }],
			operandTitle : "Click to select search operation.",
			resetTitle : "Reset Search Value"
		},
		edit : {
			addCaption: "Добавить запись",
			editCaption: "Редактировать запись",
			bSubmit: "Сохранить",
			bCancel: "Отмена",
			bClose: "Закрыть",
			saveData: "Данные были измененны! Сохранить изменения?",
			bYes : "Да",
			bNo : "Нет",
			bExit : "Отмена",
			msg: {
				required:"Поле является обязательным",
				number:"Пожалуйста, введите правильное число",
				minValue:"значение должно быть больше либо равно",
				maxValue:"значение должно быть меньше либо равно",
				email: "некорректное значение e-mail",
				integer: "Пожалуйста, введите целое число",
				date: "Пожалуйста, введите правильную дату",
				url: "неверная ссылка. Необходимо ввести префикс ('http://' или 'https://')",
				nodefined : " не определено!",
				novalue : " возвращаемое значение обязательно!",
				customarray : "Пользовательская функция должна возвращать массив!",
				customfcheck : "Пользовательская функция должна присутствовать в случаи пользовательской проверки!"
			}
		},
		view : {
			caption: "Просмотр записи",
			bClose: "Закрыть"
		},
		del : {
			caption: "Удалить",
			msg: "Удалить выбранную запись(и)?",
			bSubmit: "Удалить",
			bCancel: "Отмена"
		},
		nav : {
			edittext: " ",
			edittitle: "Редактировать выбранную запись",
			addtext:" ",
			addtitle: "Добавить новую запись",
			//delicon:"",
			deltext: " ",
			deltitle: "Удалить выбранную запись",
			searchtext: " ",
			searchtitle: "Найти записи",
			refreshtext: "",
			refreshtitle: "Обновить таблицу",
			alertcap: "Внимание",
			alerttext: "Пожалуйста, выберите запись",
			viewtext: "",
			viewtitle: "Просмотреть выбранную запись"
		},
		col : {
			caption: "Показать/скрыть столбцы",
			bSubmit: "Сохранить",
			bCancel: "Отмена"
		},
		errors : {
			errcap : "Ошибка",
			nourl : "URL не установлен",
			norecords: "Нет записей для обработки",
			model : "Число полей не соответствует числу столбцов таблицы!"
		},
		formatter : {
			integer : {thousandsSeparator: " ", defaultValue: '0'},
			number : {decimalSeparator:",", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0,00'},
			currency : {decimalSeparator:",", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0,00'},
			date : {
				dayNames:   [
					"Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб",
					"Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"
				],
				monthNames: [
					"Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек",
					"Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
				],
				AmPm : ["am","pm","AM","PM"],
				S: function (j) {return j < 11 || j > 13 ? ['st', 'nd', 'rd', 'th'][Math.min((j - 1) % 10, 3)] : 'th';},
				srcformat: 'Y-m-d',
				newformat: 'd.m.Y',
				parseRe : /[#%\\\/:_;.,\t\s-]/,
				masks : {
					ISO8601Long:"Y-m-d H:i:s",
					ISO8601Short:"Y-m-d",
					ShortDate: "n.j.Y",
					LongDate: "l, F d, Y",
					FullDateTime: "l, F d, Y G:i:s",
					MonthDay: "F d",
					ShortTime: "G:i",
					LongTime: "G:i:s",
					SortableDateTime: "Y-m-d\\TH:i:s",
					UniversalSortableDateTime: "Y-m-d H:i:sO",
					YearMonth: "F, Y"
				},
				reformatAfterEdit : false
			},
			baseLinkUrl: '',
			showAction: '',
			target: '',
			checkbox : {disabled:true},
			idName : 'id'
		}
	});
})(jQuery);
