<?php
error_reporting(E_ERROR);//(E_ALL);
?>
<link href="/css/js/chosen2.css" rel="stylesheet" type="text/css" />
<style>
	.ui-search-input .ui-widget-content{width:100%;padding:0px;margin:0px;height:100%;}
	.ui-search-input{padding:0px;margin:0px;border-spacing:0px;text-align: top;}
	/*
	.ui-th-div, .ui-jqgrid, .ui-jqgrid-resize,.ui-th-column, .ui-th-ltr, .ui-state-default, .ui-jqgrid-sortable {overflow:visible; font-size: 12px;}
	#jqgh_list_Тип_Контакта, #jqgh_list_Контрагент, #jqgh_list_Тип_Обращения, #jqgh_list_Тема, #jqgh_list_Описание_Задачи, #jqgh_list_Кто_Поставил_Код, #jqgh_list_Кому_Поставили_Группа, #jqgh_list_Дата_Постановки, #jqgh_list_Дата_Контрольная, #jqgh_list_Дата_Комментарий_Исполнителя {overflow:visible; font-size: 12px;}
	*/
</style>
<input type="hidden" id="fieldsListC"/>
<input type="hidden" id="gridsListC"/>
<input type="hidden" id="exelListC"/>
<input type="hidden" id="postDataC"/>
<?php
unset($epiLinesArr);
$maxlines = ($_SESSION['config']['main']['maxlinesCurrWin_crm'] + $_SESSION['config']['main']['maxlinesCurrFrames']);
$epi = $_SESSION['config']['main']['maxlinesCurrWin_crm'];
while($epi <= $maxlines){
	$epiLinesArr[] = $epi;
	$epi++;
}
$epiLines = implode(',',$epiLinesArr);
$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".$_SESSION['usr']['user_id']."' AND [Проект] = 'crm' AND ([Таблица] = '00' OR [Таблица] = '0')";
$sizArray = explode('|',$row['Высота_Таблиц']);
$uFlgs = json_decode($row['Флаги'],1);
$_SESSION['usr']['UserFlags']['crm'] = $uFlgs['Задачи']['00'];
//$r = print_r($uFlgs,1);$r1 = print_r($_SESSION,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2_griid.txt", "w");fputs($des, $r.' '.$r1); fclose($des);
if($sizArray[1] > 1){
	$_SESSION['config']['main']['maxlinesCurrWin_crm'] = $sizArray[0];
	$epiSise =  $sizArray[1];
}else{
	$epiSise = 284;
}
require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/colorshema.php';
?>
<script>
	$(function () {
		var lastsel;
		var otherparamss = {table:'Сотрудники_Задачи',tprefix:'dbo',base:'tig50',m:'save_total'};
		var otherparamln = 'table=Сотрудники_Задачи&tprefix=dbo&base=tig50&m=save_total';
		jQuery("#list").jqGrid({
			url: "/core/reqsave?m=load_total",
			datatype: "json",
			mtype: "get",
			height: <?=$epiSise;?>,
			rowNum: <?=$_SESSION['config']['main']['maxlinesCurrWin_crm'];?>,
			colNames: ["Код","Контрагент","Тип Контакта","Тип Обращения","Тема","Описание Задачи","Автор","Группа","Исполнитель","Дата Постановки","Дата Контрольная","Комментарий Исполнителя","Контрагент_Код"],
			colModel: [
				{ name: "id", index: "id",search:true,width: <?php $tWei = current($wDat); if($tWei > 0){print $tWei;}else{print '55';} ?>, align: "left",hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>,sortable:true},
				{ name: "Контрагент", index: "Контрагент",search:true,width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '90';} ?>, align: "left", editable:false,edittype: 'select', classes:"contragent",sortable:true, hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>
				},
				{ name: "Тип_Контакта", index: "Тип_Контакта",search:true,width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '80';} ?>, align: "left", editable:false,classes:"codemain",sortable:true,  hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>
				},
				{ name: "Тип_Обращения", index: "Тип_Обращения",search:true, align: "left",width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '100';} ?>, classes:"countryHolder", editable:false, classes:"reqtype",sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>},
				{ name: "Тема", index: "Тема",search:true,width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '200';} ?>, align: "left", editable:false, classes:"tasksubj",sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?> },
				{ name: "Описание_Задачи", index: "Описание_Задачи",search:true,width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '290';} ?>, sortable: false,align: "left", editable:false, classes:"taskdescription",sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?> },
				{name:'Кто_Поставил_Код', index:'Кто_Поставил_Код',search:true, width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '80';} ?>, align: "left", editable:false, sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>,
					classes:"codemain"
				},
				{ name: "Кому_Поставили_Группа", index: "Кому_Поставили_Группа",search:true,width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '80';} ?>, align: "left", editable:false, sortable:true,
					edittype: 'select',
					editoptions:{dataUrl:'/core/reqsave.php?m=load_group'},
					classes:"codegroup",hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>
				},
				{name:'Кому_Поставили_Пользователь_Код', index:'Кому_Поставили_Пользователь_Код',search:true, width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '80';} ?>, align: "left", editable:false, sortable:true,
					edittype: 'select',
					editoptions:{dataUrl:'/core/reqsave.php?m=load_user'},
					classes:"codeuser",hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>
				},
				{ name: "Дата_Постановки", index: "Дата_Постановки",search:true,width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '80';} ?>, align: "center", editable:false, classes:"strtdata centrr",sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>},
				{ name: "Дата_Контрольная", index: "Дата_Контрольная",search:true, width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '80';} ?>, align: "center", editable:false,classes:"contdata centrr",sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>},
				{ name: "Комментарий_Исполнителя", index: "Комментарий_Исполнителя",search:true, label:"Комментарий Исполнителя",width: <?php $tWei = next($wDat);  if($tWei > 0){print $tWei;}else{print '620';} ?>, align: "left",cellEdit:false, editable:false, classes:"comment",sortable:true,hidden:<?php if($viewsArr[key($wDat)] == 0){print "true";}else{print "false";}?>	},
				{ name: "Контрагент_Код",search:false, editable:false, classes:"cagent",hidden:true}
			],
			cellEdit:true,
			cellsubmit:'remote',
			cellurl: '/core/reqsave.php?' + otherparamln,
			pager: "#pager",
			rowList: [<?=$epiLines;?>],
			sortname: "Код",
			sortorder: "desc",
			resizeStop:function(newwidth, index) { //Отрабатывает на момент окончания изменения размера, отправляет измененные размеры колонки аяксом в базу.
				$.get("/core/srv_01.php?", {m: "set_user_tablesetting",j:'<?=$projectName;?>',t:'<?=$jqGridIdent;?>', p:index,s:newwidth}, function (data) { //alert(data);
				});
			},
			onHeaderClick:function(gridstate){
				if(gridstate == 'visible'){ //console.log('11111111111');
					$.get("/core/srv_01.php?", {m: "getSignalReload",mod:1}, function (data){ //console.log('2222222222222');
						if(data == 1){ //console.log('333333333');
							$('#list').trigger('reloadGrid');
							$('#nMessa').removeClass('nMessArrived');
							document.cookie = "soun=0; path=/;";
						}
					});
				}
			},
			gridComplete:function () {pdoerrors();},
			loadError:function () {	pdoerrors();},
			editurl: '/core/reqsave.php?' + otherparamln,
			viewrecords: true,
			gridview: true,
			autoencode: true,
			multiselect: false,
			autowidth: true,
			shrinkToFit: false,
			gridComplete:function (){
				chArchiveFlag();
				if ($('#ifchstr').val() > 1) {
					location.reload();
				}
				colorShema();
			},
			caption: "Задачи пользователя"
		}).jqGrid('filterToolbar', {autosearch: true, stringResult: true, searchOnEnter: false, defaultSearch: "cn" });
		function getVrsC(){//Функция раскладывает настройки грида по скрытым полям
			document.getElementById('fieldsListC').value = $("#list").jqGrid('getGridParam','colNames');
			document.getElementById('gridsListC').value = JSON.stringify($("#list").jqGrid('getGridParam','colModel'));
			//var pData = $("#list<?=$jqGridIdent;?>").jqGrid('getGridParam','postData');//Сюда кладем установленные фильтры, чтобы по ним создавать эеселевскую картинку
			//document.getElementById('postDataC').value = JSON.stringify(pData);
			////document.getElementById('filters').value = JSON.stringify($("#list").jqGrid('getGridParam','filters'));
		}
		getVrsC();
		jQuery("#list").jqGrid('navGrid','#pager',  // Управление тулбаром таблицы
			{edit:false,add:false,del:true, search: false, refresh: true, view: false, position: "left"}, // Отключаем от тулбара редактирование, добавление и удаление записей. На тулбаре останутся только две кнопки: "Поиск" и "Обновить"
			{
			}, // Опции окон редактирования
			{
				url:'/core/reqsave.php?add=1&' + otherparamln,
				beforeShowForm: function(form) { $('#tr_Комментарий_Исполнителя', form).hide(); },
				beforeShowForm: function(form) { $('#tr_Завершена', form).hide(); }
			}, // Опции окон добавления
			{
				url:'/core/reqsave.php?del=1&' + otherparamln
			}, // Опции окон удаления
			{
				multipleSearch:true, // Поиск по нескольким полям
				multipleGroup:true, // Сложный поиск с подгруппами условий
				showQuery: true // Показывать превью условия
			}
		)
			.navButtonAdd('#pager',{
				caption:"",
				title:"Добавить запись",
				buttonicon:"ui-icon-plus",
				onClickButton: function(){
					openMod2();
					$('.chosen-single').css('width','296px');
					$('.backfield').css('background','');
				},
				position:"first"
			})
			.navSeparatorAdd("#pager")
			.navButtonAdd('#pager',{
				caption:"Шаблоны заполнения",
				buttonicon:"ui-icon-document-b",
				id:"Шаблоны_Заполнения",
				title:"Шаблоны_Заполнения",
				onClickButton: function(){
					openEditorWin();
				},
				position:"last"
			})
			.navSeparatorAdd("#pager")
			.navButtonAdd('#pager',{
				caption:"Архив",
				buttonicon:"ui-icon-folder-collapsed",
				id:"Архив",
				title:"Архив",
				onClickButton: function(){
					chArchiveSet();
				},
				position:"last"
			})
			.navButtonAdd('#pager',{
				caption:"",
				buttonicon:"ui-icon-wrench",
				id:"fields",
				title:"Поля таблицы",
				onClickButton: function(){
					$('#keyBlock').val('10');
					document.getElementById('horizont15').style.display = 'block';
					showFieldsListC('crm');
				},
				position:"last"
			});

		//	ui-icon-document-b ui-icon-folder-collapsed
	});
	function testArchive(){
		$.ajax({url: "/core/srv_01.php",async: false,type: 'get',data: {m: "countArchive"},success: function (data) {
			$('#countarchive').val(data);
		}});

	}
	function chArchiveSet() {
		testArchive();
		if($('#countarchive').val() == 0) {
			var strinn = "Архив&#160;пуст";
			$('#Архив').html(strinn);
			$('#list').trigger('reloadGrid');
		}else{
			$.get("/core/srv_01.php", {m: "flag_set", pr:"crm", gr:"0",us: <?=$_SESSION['usr']['user_id'];?>, f: "arch00",o:"998"}, function(data){
				if(data == 1){
					if($('#countarchive').val() == 0){
						var strinn = "Архив&#160;пуст";
					}else{
						var strinn = "В архив";
					}
				}else{
					var strinn = "В рабочие";
				}
				$('#Архив').html(strinn);
				$('#list').trigger('reloadGrid');
				$('#Архив').html(strinn);
			});
		}
	}
	function chArchiveFlag(){
		$.get("/core/srv_01.php", {m: "flag_get", pr:"crm", gr:"0",us: <?=$_SESSION['usr']['user_id'];?>, f: "arch00"}, function(data){
			if(data == 1){
				testArchive();
				if($('#countarchive').val() == 0){
					var strinn = "Архив&#160;пуст";
				}else{
					var strinn = "В архив";
				}
			}else{
				var strinn = "В рабочие";
			}
			$('#Архив').html(strinn);
		});
	}


	function myelem(value,options){
		var $ret = $('<div id="Тип_Обращения"></div>');
		$ret.flexbox(countries, {
			initialValue: value,
			paging: {
				pageSize: 5
			}
		});
		return $ret;
	}

	function myval(elem){
		return  $('#Тип_Обращения_input').val();
	}
	function testTempl(){
		$('#Шаблоны_Заполнения').html('Шаблоны&#160;Заполнения');
		$('#Шаблоны_Заполнения').css('font-size','10px');
	}
	$( document ).ready(function() {
		chArchiveFlag();
		testTempl();
		$('#list').click(function(){
			$.get("/core/srv_01.php?", {m: "getSignalReload",mod:1}, function (data){
				if(data == 1){
					$('#nMessa').removeClass('nMessArrived');
					$('#list').trigger('reloadGrid');
					document.cookie = "soun=0; path=/;";
				}
			});
			
			//$('#nMessa').removeClass('nMessArrived');
		});
	});
</script>
