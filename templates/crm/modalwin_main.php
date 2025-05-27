<script>
	var mod_h1 = 700;//Высота модального окна 0
	var mod_w1 = 1000;//ирина модального окна 0
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
	<?php $winPosition = '1'; ?>
</script>
<div id="horizont<?=$winPosition;?>" style="position:absolute;z-index:300;display:none;background: yellow;background-color: #808080;">
	<div id="shader<?=$winPosition;?>"></div>
	<div id="centered_box<?=$winPosition;?>" style="position:absolute;top:0px;left:0px;z-index:30000000;">
		<div class="modal_container<?=$winPosition;?>" id="modal_container<?=$winPosition;?>" style="z-index:30000000;background-color: #98baee;height:0px;width:0px;">
			<?php require $_SERVER['DOCUMENT_ROOT'].'/templates/crm/userselect_adm.php'; ?>
		</div>
	</div>
</div>
<input type="hidden" id="data_01_<?=$winPosition;?>"/>
<script>
	function sUsers(rid){
		//$(".testchbox[type=checkbox]").removeAttr('checked');
		$('.testchbox').prop("checked",false);
		//$.ajax({url: "/core/srv_01.php",async: true,type: 'get',data: {m: "dropSessionArray", s: 'seluser'},success: function (data) {
		//}});
		$('#data_01_<?=$winPosition;?>').val(rid);
		//alert('AAAAAAAAAAAA');
		$.get("/core/srv_01.php", { m: "get_table_line", v:rid ,base: 'tig50',tprefix: 'dbo',table:'Шаблоны_Заданий'}, function(data){
			//alert('BBBBBBBBBBB');
			var fullDat = jQuery.parseJSON(data);
			pdoerrors();
			var checkDat = jQuery.parseJSON(fullDat['Пользователи']);
			console.log(checkDat);
			for (key in checkDat) {
				$('#fld_'+checkDat[key]).prop("checked",true);
				//console.log(checkDat[key]);
			}
		});
		
		document.getElementById('horizont<?=$winPosition;?>').style.display = 'block';
		
	}
	var t_ot<?=$winPosition;?> = (20);
	var l_ot<?=$winPosition;?> = (400);//Вычисляем реальный отступ верхнего левого угла модального окна от левого края экрана
	$('#centered_box<?=$winPosition;?>').css('top', t_ot<?=$winPosition;?> +'px');
	$('#centered_box<?=$winPosition;?>').css('left', l_ot<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('height', mod_h<?=$winPosition;?> +'px');
	$('#modal_container<?=$winPosition;?>').css('width', mod_w<?=$winPosition;?> +'px');
	$('#shader<?=$winPosition;?>').css('height', bh +'px');
	$('#shader<?=$winPosition;?>').css('width', bw +'px');
	$(document).click(function(e) {//по событию клика грызуном
		if((Y < t_ot<?=$winPosition;?> || Y > (t_ot<?=$winPosition;?> + 1200))||(X < l_ot<?=$winPosition;?> || X > ((l_ot<?=$winPosition;?> * 2) + mod_w<?=$winPosition;?>))){ //Если координаты грызуна вне окна
			if(document.getElementById('horizont<?=$winPosition;?>').style.display != 'none' && win != 1 && $('#keyBlock').val() < 1){ //Если окно открыто и если окно не было открыто этим самым нажатием (отслеживаем флаг win)
				//document.getElementById('horizont<?=$winPosition;?>').style.display = 'none'; //Тогда закрываем окно
			}
			win = 0;//Сбрасываем флаг открытия окна этим нажатием
		}
	});
	//wrRights(); //Выполняем функцию в окне
	//document.getElementById('horizont<?=$winPosition;?>').style.display = 'none';
</script>
