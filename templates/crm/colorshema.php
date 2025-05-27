<script>
	function colorShema(){
		//alert('DDDDDDDDDDD');
		$.ajax({url: "/core/srv_01.php",async: true,type: 'get',data: {m: "get_prev_days"},success: function (data) {
			data = jQuery.parseJSON(data);
			pdoerrors();
			var dt0 = new Date(data['lin'][0]);
			$('table#list td').filter('.contdata').each(function(i,elem){ //Обходим таблицу, построенную jqGrid за исключением ячеек с классом noseldata
				var tdText = $(elem).text(); //Вытаскиваем содержимое тега td
				var tArr = tdText.split('-');
				var dtText =  new Date(tArr[2]+'/'+tArr[1]+'/'+tArr[0]);
				//alert(dtText +'  '+  dt0);
				if(dtText < dt0){
					//$(elem).css('color','<?=$Config['colors']['color1'];?>');//Окрашиваем любую из этих дат в соответствующий цвет
					$(elem).css('background-color','<?=$_SESSION['config']['colors']['coloroutfon'];?>');//Окрашиваем текст в цвет, предназначенный для просроченных дат
				}
				if(tdText == data['stamp'][1]) {
					$(elem).css('color','<?=$_SESSION['config']['colors']['color1'];?>');//Окрашиваем любую из этих дат в соответствующий цвет
				}
				if(tdText == data['stamp'][2]) {
					$(elem).css('color','<?=$_SESSION['config']['colors']['color2'];?>');//Окрашиваем любую из этих дат в соответствующий цвет
				}
				if(tdText == data['stamp'][3]) {
					$(elem).css('color','<?=$_SESSION['config']['colors']['color3'];?>');//Окрашиваем любую из этих дат в соответствующий цвет
				}
				if(tdText == data['stamp'][0]) {
					$(elem).css('color','<?=$_SESSION['config']['colors']['color0'];?>');//Окрашиваем любую из этих дат в соответствующий цвет
				}
			});
			
		}});
	
	}
</script>