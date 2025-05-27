<?php
//session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/tload.php";
require_once $_SERVER['DOCUMENT_ROOT']."/templates/crm/modalwin_main.php";
?>

<style>
	.key_s5{height:34px;width:123px;background-image: url(/css/images/key_s_b.png)}
	.key_s5:hover{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_s5:active{height:34px;width:123px;background-image: url(/css/images/key_s_h.png)}
	.key_r5{height:34px;width:98px;background-image: url(/css/images/key_r_b.png)}
	.key_r5:hover{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
	.key_r5:active{height:34px;width:98px;background-image: url(/css/images/key_r_h.png)}
</style>
<a style="position:absolute;top:8px;right:24px;text-decoration: none;z-index: 301;color:#ffffff;" href="javascript:void(0);" onclick="javascript:jQuery('#list5').jqGrid('saveCell', $('#templ_editrow').val(), $('#templ_editcol').val());document.getElementById('horizont5').style.display = 'none';"><img src="/css/images/crus004.png" border="0"/></a>
<script type="text/javascript">
	$(function () {
		var lastsel;
		var pTable = 'Шаблоны_Заданий';
		var pBase = 'tig50';
		var pPrefix = 'dbo';
		var otherparamss = {table:pTable,tprefix: pPrefix,base:pBase};
		var otherparamln = 'table='+pTable+'&tprefix='+pPrefix+'&base='+pBase+'';
		$("#list5").jqGrid({
			url: "/core/reqsave?m=crm_templates",
			datatype: "json",
			mtype: "get",
			height: "505px",
			colNames: ["Код","Шаблон","Тип Контакта","Тип Обращения","Тема","Описание задачи","Группе","Пользователю","Пользователям","Дней на выполнение"],
			colModel: [
				{ name: "id", width: 85, editable:false,hidden:true},
				{ name: "Шаблон", width: 125, editable:true, edittype:'text'},
				{ name: "Тип_Контакта", width: 120, editable:true, edittype:'select',
					editoptions:{value:"Исходящий:Исходящий;Входящий:Входящий"}
				},
				{ name: "Тип_Обращения", width: 120, align: "right", editable:true, edittype:'text'},
				{ name: "Тема", width: 190, align: "right", editable:true, edittype:'text'},
				{ name: "Описание_задачи", width: 345, align: "right", editable:true, edittype:'text'},
				{ name: "Группе", width: 110, align: "left", editable:true,
					edittype: 'select',
					editoptions:{dataUrl:'/core/reqsave.php?m=load_group'}
				},
				{name:'Пользователю', width:100, align: "left", editable:true,
					edittype: 'select',
					editoptions: {value: function (elem) {
						return document.getElementById('grtext').value
						//"45:Anna Braginskaya;5:Дарья Навильникова;12:Оксана Кулакова";
					}
					}
				},
				{name:'Пользователям', width:100, align: "left", editable:false, classes:"selusers"
				},
				{ name: "Дата", width: 80, align: "right", editable:true, edittype:'text'
				}
			],
			cellEdit:true,
			cellsubmit:'remote',
			cellurl: '/core/reqsave?m=crm_templates_add&' + otherparamln,
			rowNum: 14,
			rowList: [100, 200, 300],
			sortname: "Код",
			sortorder: "asc",
			ondblClickRow:function(rowid, iRow, iCol, e){
				sUsers(rowid);
			},
			onSelectRow: function(id){ //alert('Ttttttttttttttttttttttt');
				//jQuery('#list').jqGrid('editRow',Ключ,true,false,false,'/php/main/reqsave?m=calk_reference_edit&',otherparamss);
			},

			afterSubmitCell: function(serverresponse, rowid, cellname, value, iRow, iCol){
				if(cellname == 'Группе'){
					$.ajax({url:"/core/reqsave?m=crm_test_user", async:false, type:'POST', data:{m: "crm_test_user",r:rowid},success:function(data) {
						$("#list5").trigger("reloadGrid");
					}});
				}
				$("#list5").trigger("reloadGrid");
			},
			beforeEditCell: function (rowid, cellname, value, iRow, iCol){ //alert('MMMMMMMMMMMMM');
				if(cellname == 'Пользователю'){
					$.ajax({url:"/core/reqsave?m=load_user", async:false, type:'POST', data:{m: "load_user",modus:2,r:rowid},success:function(data) {
						document.getElementById('grtext').value = data;
					}});
				}else if(cellname == 'Группе'){

				}
				//alert(cellname + "before edit here " + rowid);
				// set editoptions here
				$('#edt_col').val(iCol);
				$('#edt_row').val(iRow);
				$('#templ_editrow').val(iRow);
				$('#templ_editcol').val(iCol);
			},
			gridComplete:function () {pdoerrors();},
			loadError:function () {	pdoerrors();},
			editurl: '/core/reqsave?m=crm_templates_add&' + otherparamln,
			viewrecords: true,
			gridview: true,
			autoencode: true,
			multiselect: false,
			autowidth: true,
			shrinkToFit: false,
			pager: '#pager5',
			caption: "Шаблоны задания"
		});
		jQuery("#list5").jqGrid('navGrid','#pager5',  // Управление тулбаром таблицы
			{edit:false,add:false,del:true}, // Отключаем от тулбара редактирование, добавление и удаление записей. На тулбаре останутся только две кнопки: "Поиск" и "Обновить"
			{
			}, // Опции окон редактирования
			{
				url:'/core/reqsave?m=crm_templates_add&add=1&' + otherparamln
			}, // Опции окон добавления
			{
				url:'/core/reqsave?m=crm_templates_add&del=1&' + otherparamln
			}, // Опции окон удаления
			{

				multipleSearch:true, // Поиск по нескольким полям
				multipleGroup:true, // Сложный поиск с подгруппами условий
				showQuery: true // Показывать превью условия
			}
		)
			.navButtonAdd('#pager5',{
				caption:"",
				title:"Добавить запись",
				buttonicon:"ui-icon-plus",
				onClickButton: function(){
					openMod6();
				},
				position:"first"
			});
	});
	function openMod6(){
		$('#horizont6').show();
	}
	function getUsersObg(){ //'/core/reqsave.php?m=load_user'
		var out;
		$.post("/core/reqsave?m=load_user", {m: "load_user",modus:2},function(data){ //alert(data);
			return data;
			//out = data;
		});
		//return out;
	}
	//$('#horizont5').hide();
</script>
<table id="list5"><tr><td></td></tr></table>
<div id="pager5"></div>
<input type="hidden" id="grtext" size="100"/><input type="hidden" id="edt_col" ssze="100"/><input type="hidden" id="edt_row" ssze="100"/>
<input type="hidden" id="templ_editrow"/>
<input type="hidden" id="templ_editcol"/>
<!--
<p style="position:absolute;bottom:-10px;right:105px;z-index: 300000000;"><input type="button" class="key_s5" value="" onclick="javascript:jQuery('#list5').jqGrid('saveCell', $('#templ_editrow').val(), $('#templ_editcol').val());document.getElementById('horizont5').style.display = 'none';"></p>
<p style="position:absolute;bottom:-10px;right:5px;z-index: 300000000;"><input type="button" class="key_r5" value="" onclick="javascript:jQuery('#list5').jqGrid('restoreCell', $('#templ_editrow').val(), $('#templ_editcol').val());document.getElementById('horizont5').style.display = 'none';"></p>
-->
<!--
<table style="float:right;">
	<tr>
		<td id="key_s" onClick="javascript:formCheck();"></td>
		<td id="key_r" onClick="javascript:$('#list5').jqGrid('saveCell',$('#edt_row').val(),$('#edt_col').val());"></td>
	</tr>
</table>
-->