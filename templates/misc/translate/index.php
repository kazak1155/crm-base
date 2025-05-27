<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/php/main/core.php');

$CRM = new Core;
$CRM->con_database('srv');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="robots" content="noindex, nofollow"/>
<meta name="robots" content="noarchive"/>
<title>Перевод</title>
<?php $CRM->get_files(); ?>
<script>
$(function() {
	createGrid({
		main:true,
		name:'vars',
		table:'З_Б_Перевод',
		id:'Код',
		tableSort:'ASC',
		filterToolbar:true,
		navGrid:true,
		cn:[
			"<?php echo $CRM->get_trns('code')?>",
			"<?php echo $CRM->get_trns('variable')?>",
			"<?php echo $CRM->get_trns('language')?>",
			"<?php echo $CRM->get_trns('name')?>"
		],
		cm:[
			{name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true},
			
			{name: "Переменная",index:"Переменная",width:300},
			
			{name: "Язык",index:"Язык",width:500,formatter:'select',
				stype:'select',searchoptions:{value:'ru:Русский;en:Английский;it:Итальянский',dataInit:dataSelect2,attr: {multiple: 'multiple'}},
				edittype:'select',editoptions:{value:':&#27;ru:Русский;en:Английский;it:Итальянский',dataInit:dataInitAcComboBox}
			},
			{name: "Наименование",index:"Наименование",width:500}
		],
		options:{
			cellEdit:true,
			autowidth: true,
			rowNum: 50,
			shrinkToFit:true
		},
		events:{
			beforeSubmitCell:function(rowid, cellname, value, iRow, iCol)
			{ 	
				return {tid:'Код',tname:'З_Б_Перевод'}; 
			},
			afterEditCell:function(rowid,cellname,value,iRow,iCol)
			{ 
				focusInputHTML.apply(this,[rowid,cellname,value,iRow,iCol]);
			},
			onCellSelect:function(rowid,iCol,cellcontent,e)
			{
				if(rowid === 'blank'){
					$(this).jqGrid('saveCell', globalScope.gRow, globalScope.gCol);
					$(this).setGridParam({cellEdit:false});			
					$(this).jqGrid('editRow','blank',{extraparam:{oper:'add',tname:'З_Б_Перевод'}});
				} else {
					if($('input[class="editable"]').length > 0){
						$(this).jqGrid('restoreRow','blank');
						$(this).setGridParam({cellEdit:true});
					}
				}
			},
			gridComplete:function()
			{
				gridTools.call(this,
					function(rowid){  $(this).jqGrid('saveRow','blank',{extraparam:{oper:'add',tname:'З_Б_Перевод'}}); },
					function(rowid){ $(this).jqGrid('delGridRow', rowid,{delData:{tid:'Код',tname:'З_Б_Перевод'}}); });
			}
		}
	});
})
</script>
</head>
<body>
<div id="gridcontainer" >
	<table class='gridclass' id='vars'></table>
</div>
</body>
</html>




