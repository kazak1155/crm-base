<style>
	* {
		font-family: Verdana;
	}
	.llong{ margin: 0; padding: 0; border: 0; width: 200px;}
	.query{display:none;}
	.ui-jqgrid .ui-pg-input {height:20px;}
	.ui-jqgridssssss .ui-jqgrid-pager {height:36px;}
	.ui-jqgrid, .ui-jqgrid-pager{font-size:12px;}
	.ui-icon, .ui-pg-div, #Архив {font-family: Verdana; font-size:10px;display: inline-block;white-space:nowrap;}
	.ui-paging-info, td #input_pager, .ui-pg-input, .ui-pg-selbox option, .ui-paging-pager tbody tr td, .ui-paging-pager tbody tr td select, .ui-jqgrid .ui-pg-selbox{font-size:14px;}
	input.w_100, select.w_100, textarea.w_100{
		width:100%;font-size:100%;
		box-sizing:content-box;
		-ms-box-sizing:content-box;
		-moz-box-sizing:content-box;
		-webkit-box-sizing:content-box;
		margin: 3px 0;
		padding: 0;
		border: 0;
	}
	td#pager-left, .ui-jqgrid .ui-pg-table td {vertical-align: top;}
	#gbox_list5, #gview_list5, .ui-jqgrid-bdivsssss{width:1250px;}
	#shader, #shader2, #shader3, #shader4, #shader5 {position: absolute;top:0;left:0;height: 100%;min-height: 100%;width: 100%;background: #000;filter:alpha(opacity=40, style=0);opacity: 0.4;z-index:300;}
	.chosen-container, .chosen-container-single, .chosen-container-active, .chosen-with-drop{margin:4px 0px;}
	.chosen-container {font-size:11px;}
	.chosen-single span{height:18px;}
	/* chosen-default
	.chosen-single, .chosen-default, .chosen-default span, .chosen-single span{font-size:11px;};
	.chosen-container {font-size:11px;}
	#Тип_Обращения, .chosen-select{padding:4px 0px;margin:4px 0px; height:20px;}
	*/
</style>
<script>
	var mod_h = 200;//Высота модального окна 1
	var mod_w = 500;//ирина модального окна 1
	var mod_h2 = 430;//Высота модального окна 2
	var mod_w2 = 540;//Ширина модального окна 2
	var mod_h3 = 730;//Высота модального окна 3
	var mod_w3 = 850;//Ширина модального окна 3
	var mod_h4 = 300;//Высота модального окна 4
	var mod_w4 = 850;//Ширина модального окна 4
	var mod_h5 = 594;//Высота модального окна 5
	var mod_w5 = 1250;//Ширина модального окна 5
	var mod_h6 = 350;//Высота модального окна 5
	var mod_w6 = 600;//Ширина модального окна 5
	var mod_h15 = 450;//Высота модального окна 0
	var mod_w15 = 600;//ирина модального окна 0
	var mod_h16 = 450;//Высота модального окна 0
	var mod_w16 = 600;//ирина модального окна 0
	var mod_h17 = 450;//Высота модального окна 0
	var mod_w17 = 600;//ирина модального окна 0
	var mod_h18 = 200;//Высота модального окна 8
	var mod_w18 = 400;//Ширина модального окна 8
	var mod_h19 = 400;//Высота модального окна 9
	var mod_w19 = 800;//Ширина модального окна 9
	var mod_h20 = 600;//Высота модального окна 9
	var mod_w20 = 1000;//Ширина модального окна 9
	var mod_h21 = 300;//Высота модального окна 9
	var mod_w21 = 500;//Ширина модального окна 9
	var mod_h39 = 200;//Высота модального окна 9
	var mod_w39 = 400;//Ширина модального окна 9
	
	var win = 0;//лаг, показывающий что мы открываем окно
	var win4 = 0;//лаг, показывающий что мы открываем окно
	var bw = $('body').width();//Вычисляем данные для открытия модального окна
	var bh = $(window).height();
	var dh = $(window).scrollTop();
	var X; //Координаты грызуна
	var Y;
	$(document).mousemove(function(e){//Считываем координаты грызуна из системы
		X = e.pageX; // положения по оси X
		Y = e.pageY; // положения по оси Y
	});
	//$('#horizont5').show();
</script>
<div id="horizont" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader"></div>
	<div id="centered_box" style="position:absolute;top:0px;left:0px;z-index:300;">
		<div class="modal_container" id="modal_container" style="z-index:300;background-color: #d0d0d0;height:0px;width:0px;">
			<?php
			require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/delegate.php';
			?>
		</div>
	</div>
</div>
<script>
	//обработчик окна делегирования
	//var t_ot = (bh/2 - (mod_w*0.8) + dh);//Вычисляем реальный отступ верхнего левого угла модального окна от верхней части экрана
	var t_ot = (100);
	var l_ot = (bw/2 - (mod_w/2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box').css('top', t_ot +'px');
	$('#centered_box').css('left', l_ot +'px');
	$('#modal_container').css('height', mod_h +'px');
	$('#modal_container').css('width', mod_w +'px');
	$('#shader').css('height', bh +'px');
	$('#shader').css('width', bw +'px');
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot || Y > (t_ot + mod_h))||(X < l_ot || X > (l_ot + mod_w))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont').style.display != 'none' && win != 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>

<div id="horizont2" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader2"></div>
	<div id="centered_box2" style="position:absolute;top:0px;left:0px;z-index:30000000;">
		<div class="modal_container2" id="modal_container2" style="z-index:30000000;height:0px;width:0px;">
			<?php
				require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/addform.php';
			?>
			<div id="addroom"></div>
		</div>
	</div>
</div>
<script>
	//обработчик окна добавления
	var t_ot2 = (100);
	var l_ot2 = (bw / 2 - (mod_w2 / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box2').css('top', t_ot2 +'px');
	$('#centered_box2').css('left', l_ot2 +'px');
	$('#modal_container2').css('height', mod_h2 +'px');
	$('#modal_container2').css('width', mod_w2 +'px');
	$('#shader2').css('height', bh +'px');
	$('#shader2').css('width', bw +'px');
	function openMod2(rowid){//Функция, запускаемая по нажатию кнопки в пейджере
		document.getElementById('horizont2').style.display = 'block';//Открываем модальное окно
	}
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot2 || Y > (t_ot2 + (mod_h2 * 1.1)))||(X < l_ot2 || X > (l_ot2 + mod_w2))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont2').style.display != 'none' && win != 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				//document.getElementById('horizont2').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>
<?php //print "222222222222222222";
?>

<div id="horizont3" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader3"></div>
	<div id="centered_box3" style="position:absolute;top:0px;left:0px;z-index:300;">
		<div class="modal_container3" id="modal_container3" style="z-index:30000000;background-color: #d0d0d0;height:0px;width:0px;">
			<?php
			require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/editcomment.php';
			?>
			<div id="addroom"></div>
		</div>
	</div>
</div>
<script>
	//обработчик окна редактирования
	var t_ot3 = (100);
	var l_ot3 = (bw / 2 - (mod_w3 / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box3').css('top', t_ot3 +'px');
	$('#centered_box3').css('left', l_ot3 +'px');
	$('#modal_container3').css('height', mod_h3 +'px');
	$('#modal_container3').css('width', mod_w3 +'px');
	$('#shader3').css('height', bh +'px');
	$('#shader3').css('width', bw +'px');
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot3 || Y > (t_ot3 + (mod_h3 * 1.1)))||(X < l_ot3 || X > (l_ot3 + mod_w3))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont3').style.display != 'none' && document.getElementById('win').value != 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont3').style.display = 'none'; //Тогда закрываем окно
			}
			document.getElementById('win').value = 0;
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>

<div id="horizont4" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader4"></div>
	<div id="centered_box4" style="position:absolute;top:0px;left:0px;z-index:300;">
		<div class="modal_container4" id="modal_container4" style="z-index:300;background-color: #d0d0d0;height:0px;width:0px;">
			<?php
			require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/newdata.php';
			?>
			<div id="addroom"></div>
		</div>
	</div>
</div>
<script>
	var t_ot4 = (100);
	var l_ot4 = (bw / 2 - (mod_w4 / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box4').css('top', t_ot4 +'px');
	$('#centered_box4').css('left', l_ot4 +'px');
	$('#modal_container4').css('height', mod_h4 +'px');
	$('#modal_container4').css('width', mod_w4 +'px');
	$('#shader4').css('height', bh +'px');
	$('#shader4').css('width', bw +'px');
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot4 || Y > (t_ot4 + mod_h4))||(X < l_ot4 || X > (l_ot4 + mod_w4))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont4').style.display != 'none' && win4 != 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont4').style.display = 'none'; //Тогда закрываем окно
			}
			win4 = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>
<div id="horizont6" style="position:absolute;z-index:309;display:none;background: yellow;background-color: #808080;">
	<div id="shader6"></div>
	<div id="centered_box6" style="position:absolute;top:0px;left:0px;z-index:305;">
		<div class="modal_container6" id="modal_container6" style="z-index:305;background-color: #d0d0d0;height:0px;width:0px;">
			<?php
			require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/templadd.php';
			?>
			<div id="addroom6"></div>
		</div>
	</div>
</div>
<script>
	var t_ot6 = (100);
	var l_ot6 = (bw / 2 - (mod_w6 / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box6').css('top', t_ot6 +'px');
	$('#centered_box6').css('left', l_ot6 +'px');
	$('#modal_container6').css('height', mod_h6 +'px');
	$('#modal_container6').css('width', mod_w6 +'px');
	$('#shader6').css('height', bh +'px');
	$('#shader6').css('width', bw +'px');
	$(document).click(function(e) {//по событию клика грызуном
		if(Y < 100){
		}else if((Y < t_ot6 || Y > (t_ot6 + (mod_h6 * 1.1)))||(X < l_ot6 || X > (l_ot6 + mod_w6))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont6').style.display != 'none' && document.getElementById('win').value != 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont6').style.display = 'none'; //Тогда закрываем окно
			}
			document.getElementById('win').value = 0;
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>



<div id="horizont5" style="position:absolute;z-index:305;display:none;background: yellow;background-color: #808080;">
	<div id="shader5"></div>
	<div id="centered_box5" style="position:absolute;top:0px;left:0px;z-index:305;">
		<div class="modal_container5" id="modal_container5" style="z-index:305;background-color: #d0d0d0;height:0px;width:0px;">
			<?php
			require_once $_SERVER['DOCUMENT_ROOT'].'/templates/crm/modalwin.php';
			?>
			<div id="addroom5"></div>
		</div>
	</div>
</div>
<script>
	var t_ot5 = (100);
	var l_ot5 = (bw / 2 - (mod_w5 / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box5').css('top', t_ot5 +'px');
	$('#centered_box5').css('left', l_ot5 +'px');
	$('#modal_container5').css('height', mod_h5 +'px');
	$('#modal_container5').css('width', mod_w5 +'px');
	$('#shader5').css('height', bh +'px');
	$('#shader5').css('width', bw +'px');
	$(document).click(function(e) {//по событию клика грызуном
		if(Y < 100){
		}else if((Y < t_ot5 || Y > (t_ot5 + (mod_h5 * 1.1)))||(X < l_ot5 || X > (l_ot5 + mod_w5))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont5').style.display != 'none' && document.getElementById('win').value != 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont5').style.display = 'none'; //Тогда закрываем окно
			}
			document.getElementById('win').value = 0;
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
	<?php
	//$r = print_r($_SESSION,1);$des = fopen($_SERVER['DOCUMENT_ROOT']."/dddd2s.txt", "w");fputs($des, $r); fclose($des);
	if($_COOKIE['prime_modus'] == 'horizont5'){
	$_COOKIE['prime_modus'] = '';
	?>
	document.cookie = "prime_modus=horizont0; path=/";
	document.getElementById('horizont5').style.display = 'block';
	<?php
	}
	?>
	function openEditorWin(){
		//alert(Y + '  ' + X);
		if(document.getElementById('horizont5').style.display != 'none'){
			$('#horizont5').hide();
		}else{
			$('#horizont5').show();
		}
	}
</script>

<?php $winPosition = '15'; ?>
<div id="horizont<?=$winPosition;?>" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader<?=$winPosition;?>"></div>
	<div id="centered_box<?=$winPosition;?>" style="position:absolute;top:0px;left:0px;z-index:30000000;">
		<div class="modal_container<?=$winPosition;?>" id="modal_container<?=$winPosition;?>" style="z-index:30000000;background-color: #98baee;height:0px;width:0px;">
			<?php
			//print $baseDir.'||||||||||||||||||||';
			require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/grid_fields.php';
			?>
		</div>
	</div>
</div>
<script>
	var t_ot<?=$winPosition;?> = (40);
	var l_ot<?=$winPosition;?> = (bw / 2 - (mod_w<?=$winPosition;?> / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box<?=$winPosition;?>').css('top', t_ot<?=$winPosition;?> +'px');
	$('#centered_box<?=$winPosition;?>').css('left', l_ot<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('height', mod_h<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('width', mod_w<?=$winPosition;?> +'px');
	$('#shader<?=$winPosition;?>').css('height', bh +'px');
	$('#shader<?=$winPosition;?>').css('width', bw +'px');
	function openMod<?=$winPosition;?>(rowid){//Функция, запускаемая по нажатию кнопки в пейджере
		document.getElementById('horizont<?=$winPosition;?>').style.display = 'block';//Открываем модальное окно
	}
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot<?=$winPosition;?> || Y > (t_ot<?=$winPosition;?> + (mod_h<?=$winPosition;?> * 1.1)))||(X < l_ot<?=$winPosition;?> || X > (l_ot<?=$winPosition;?> + mod_w<?=$winPosition;?>))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont<?=$winPosition;?>').style.display != 'none' && win != 1 && $('#keyBlock').val() < 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont<?=$winPosition;?>').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>
<?php $winPosition = '19'; ?>
<div id="horizont<?=$winPosition;?>" style="position:absolute;z-index:3000000000;display:none;background: yellow;background-color: #808080;">
	<div id="shader<?=$winPosition;?>"></div>
	<div id="centered_box<?=$winPosition;?>" style="position:absolute;top:0px;left:0px;z-index:30000000;">
		<div class="modal_container<?=$winPosition;?>" id="modal_container<?=$winPosition;?>" style="z-index:30000000;background-color: #98baee;height:0px;width:0px;">
			<div id="addroom"></div>
		</div>
	</div>
</div>
<script>
	function filesProcW(ordId,agt){
		var uril = '/templates/calls/files/editor.php?oid='+ordId+'&h='+mod_h<?=$winPosition;?>+'&w='+mod_w<?=$winPosition;?>+'&a='+agt+'&mo=<?=$winPosition;?>';
		$('#modal_container<?=$winPosition;?>').empty();
		$('<iframe>', { src:uril , height:mod_h<?=$winPosition;?>+'px', width:mod_w<?=$winPosition;?>+'px', id: 'ordDet', frameborder: 0, scrolling: 'no' }).appendTo('#modal_container<?=$winPosition;?>');
		$('horizont<?=$winPosition;?>').show();
	}
	var t_ot<?=$winPosition;?> = (40);
	var l_ot<?=$winPosition;?> = (bw / 2 - (mod_w<?=$winPosition;?> / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box<?=$winPosition;?>').css('top', t_ot<?=$winPosition;?> +'px');
	$('#centered_box<?=$winPosition;?>').css('left', l_ot<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('height', mod_h<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('width', mod_w<?=$winPosition;?> +'px');
	$('#shader<?=$winPosition;?>').css('height', bh +'px');
	$('#shader<?=$winPosition;?>').css('width', bw +'px');
	function openMod<?=$winPosition;?>(rowid){//Функция, запускаемая по нажатию кнопки в пейджере
		document.getElementById('horizont<?=$winPosition;?>').style.display = 'block';//Открываем модальное окно
	}
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot<?=$winPosition;?> || Y > (t_ot<?=$winPosition;?> + (mod_h<?=$winPosition;?> * 1.1)))||(X < l_ot<?=$winPosition;?> || X > (l_ot<?=$winPosition;?> + mod_w<?=$winPosition;?>))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont<?=$winPosition;?>').style.display != 'none' && win != 1 && $('#keyBlock').val() < 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont<?=$winPosition;?>').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>
<?php $winPosition = '20'; ?>
<div id="horizont<?=$winPosition;?>" style="position:absolute;z-index:30000000000;display:none;background: yellow;background-color: #808080;">
	<div id="shader<?=$winPosition;?>"></div>
	<div id="centered_box<?=$winPosition;?>" style="position:absolute;top:0px;left:0px;z-index:300000000;">
		<div class="modal_container<?=$winPosition;?>" id="modal_container<?=$winPosition;?>" style="z-index:300000000;background-color: #98baee;height:0px;width:0px;">
			<div id="addroom"></div>
		</div>
	</div>
</div>
<script>
	function usrProcX(){
		var ordId = 1;
		var uril = '/templates/crm/userselect.php?oid='+ordId+'&h='+mod_h<?=$winPosition;?>+'&w='+mod_w<?=$winPosition;?>;
		$('#modal_container<?=$winPosition;?>').empty();
		$('<iframe>', { src:uril , height:mod_h<?=$winPosition;?>+'px', width:mod_w<?=$winPosition;?>+'px', id: 'ordDeth', frameborder: 0, scrolling: 'no' }).appendTo('#modal_container<?=$winPosition;?>');
		$('#horizont<?=$winPosition;?>').show();
	}
	var t_ot<?=$winPosition;?> = (40);
	var l_ot<?=$winPosition;?> = (bw / 2 - (mod_w<?=$winPosition;?> / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box<?=$winPosition;?>').css('top', t_ot<?=$winPosition;?> +'px');
	$('#centered_box<?=$winPosition;?>').css('left', l_ot<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('height', mod_h<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('width', mod_w<?=$winPosition;?> +'px');
	$('#shader<?=$winPosition;?>').css('height', bh +'px');
	$('#shader<?=$winPosition;?>').css('width', bw +'px');
	function openMod<?=$winPosition;?>(rowid){//Функция, запускаемая по нажатию кнопки в пейджере
		document.getElementById('horizont<?=$winPosition;?>').style.display = 'block';//Открываем модальное окно
	}
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot<?=$winPosition;?> || Y > (t_ot<?=$winPosition;?> + (mod_h<?=$winPosition;?> * 1.1)))||(X < l_ot<?=$winPosition;?> || X > (l_ot<?=$winPosition;?> + mod_w<?=$winPosition;?>))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont<?=$winPosition;?>').style.display != 'none' && win != 1 && $('#keyBlock').val() < 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				document.getElementById('horizont<?=$winPosition;?>').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
</script>
<?php $winPosition = '21'; ?>
<div id="horizont<?=$winPosition;?>" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader<?=$winPosition;?>"></div>
	<div id="centered_box<?=$winPosition;?>" style="position:absolute;top:0px;left:0px;z-index:300;">
		<div class="modal_container<?=$winPosition;?>" id="modal_container<?=$winPosition;?>" style="z-index:300000000;background-color: #4479BA;height:0px;width:0px;">
			<div id="addroom"></div>
		</div>
	</div>
</div>
<script>
	function finishProcX(ordId){
		//var ordId = 1;
		var uril = '/templates/crm/finish.php?oid='+ordId+'&h='+mod_h<?=$winPosition;?>+'&w='+mod_w<?=$winPosition;?>;
		$('#modal_container<?=$winPosition;?>').empty();
		$('<iframe>', { src:uril , height:mod_h<?=$winPosition;?>+'px', width:mod_w<?=$winPosition;?>+'px', id: 'ordDeth', frameborder: 0, scrolling: 'no' }).appendTo('#modal_container<?=$winPosition;?>');
		$('#horizont<?=$winPosition;?>').show();
	}
	var t_ot<?=$winPosition;?> = (40);
	var l_ot<?=$winPosition;?> = (bw / 2 - (mod_w<?=$winPosition;?> / 2));//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box<?=$winPosition;?>').css('top', t_ot<?=$winPosition;?> +'px');
	$('#centered_box<?=$winPosition;?>').css('left', l_ot<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('height', mod_h<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('width', mod_w<?=$winPosition;?> +'px');
	$('#shader<?=$winPosition;?>').css('height', bh +'px');
	$('#shader<?=$winPosition;?>').css('width', bw +'px');
	function openMod<?=$winPosition;?>(rowid){//Функция, запускаемая по нажатию кнопки в пейджере
		document.getElementById('horizont<?=$winPosition;?>').style.display = 'block';//Открываем модальное окно
	}
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot<?=$winPosition;?> || Y > (t_ot<?=$winPosition;?> + (mod_h<?=$winPosition;?> * 1.1)))||(X < l_ot<?=$winPosition;?> || X > (l_ot<?=$winPosition;?> + mod_w<?=$winPosition;?>))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont<?=$winPosition;?>').style.display != 'none' && win != 1 && $('#keyBlock').val() < 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				//document.getElementById('horizont<?=$winPosition;?>').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
	//window.runoutuserParent = function(){
//		$.ajax({url: "/core/srv_01.php",async: false,type: 'get',data: {m: "getSessionUsers"},success: function (data) {
			//alert(data);
			//alert(document.getElementById('out__user').innerHTML);
			//alert($('#Тип_Контакта').val());
			//window.
			//$('#out__user').html('ddddd');
//		}});
	//}
	
</script>
<input type="hidden" name="urefr" id="urefr" value=""/>