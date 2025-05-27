<?php
unset($wDat);
$maX = 24;
$Cache = new WinCache();
$SQl = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Колонки] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = '".$projectName."' AND [Таблица] = '".$jqGridIdent."' ORDER BY [Поле]";
$Core->query = $SQl;
$Core->con_database('srv');
$stm = $Core->PDO();
while($row = $stm->fetch()){ //Выбираем сохраненные данные ширины ячеек таблицы
	$wDat[$row['Поле']] = $row['Ширина'];
	$lass = $row['Поле']; //Ищем самое большое значение поля, чтобы стандартизовать массив
}
$wCount = 0;
while($wCount <= $lass){ //бходим массив
	if($wDat[$wCount] < 1){ //И, если какого-то элемента в сохранении не было
		$wDat[$wCount] = 0; //создаем его - для правильной работы команды next
	}
	$wCount++;
}
ksort($wDat,SORT_NUMERIC);
$c = 0;
while($c < $maX){
	if($wDat[$c] < 1){$wDat[$c] = 0;}
	$c++;
}
reset($wDat);//Сбрасываем указатель массива
?>
<script type="text/javascript">
	var fieldChArr = [];//Создаем массивы для отметок о выводимости колонок
	var fieldExel = [];
</script>
<?php
$c = 0;
while($c < $maX){ //Делаем заготовку - массив со всеми выставленными полями
	$clrString[$c] = 1;
	$c++;
}
if(isset($_REQUEST['flistsubmC'])){//Если сохраняем измененную видимость полей
	unset($outString);
	$c = 0;
	while($c < $maX){
		if($_REQUEST['fieldC_'.$c] == 1){
			$outString[$c] = 1;
		}else{
			$outString[$c] = 0;
		}
		$c++;
	}
	$c = 0;
	//print "<pre>"; print_r($clrString); print "</pre>"; die;
		setfieldRecord();
		$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Видимость] = '".json_encode($outString)."' WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = '".$projectName."' AND [Таблица] = '".$jqGridIdent."'";
		$Core->query = $sql;
		$Core->PDO(array("exec" => true));//Сохраняем полученный массив полей
		$Cache->flush();
}
$sett = getfieldArray();
$viewsArr = json_decode($sett,1);
//print $sett."<pre>"; print_r($viewsArr); print "</pre>"; die;
if(isset($_REQUEST['defaultFieldsC']) || !count($viewsArr)){
	setfieldRecord();
	$sql = "UPDATE [srv].[dbo].[црм_Пользователь_Установки] SET [Видимость] = '".json_encode($clrString)."' WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = '".$projectName."' AND [Таблица] = '".$jqGridIdent."'";
	//print $sql.'';die;
	$Core->query = $sql;
	$Core->PDO(array("exec" => true));//Сохраняем полученный массив полей
	$Cache->flush();
	//if(!count($viewsArr)){
		$viewsArr = json_decode(getfieldArray(),1);
	//}
}
if(count($viewsArr)){
	foreach($viewsArr as $k => $v){ //ерекодируем видимость полей грида в скриптовые переменные
		if($v == 1){$o = 0;}else{$o = 1;}
		?>
		<script>
			fieldChArr[<?=(int)$k;?>] = '<?=$o;?>';
		</script>
		<?php
	}
}

function setfieldRecord(){
	global $projectName, $jqGridIdent, $Cache,$Core;
	$sql = "SELECT count(*) AS [cnt] FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = '".$projectName."' AND [Таблица] = '".$jqGridIdent."'";
	$Core->query = $sql; //Проверяем, есть ли хоть какая-то запись на этого пользователя, проект и таблицу.
	$Core->con_database('srv');
	$stm = $Core->PDO();
	$row = $stm->fetch();
	if($row['cnt'] == 0){//Если записи нет
		$sql = "INSERT INTO [srv].[dbo].[црм_Пользователь_Установки]  ([Пользователь],[Проект],[Таблица]) VALUES ('".(int)$_SESSION['usr']['user_id']."', '".$projectName."', '".$jqGridIdent."')";
		$Core->query = $sql;
		$Core->PDO(array("exec" => true));//Создаем ее
		$Cache->flush();
	}
}
function getfieldArray(){
	global $projectName, $jqGridIdent, $Cache,$Core;
	$sql = "SELECT * FROM [srv].[dbo].[црм_Пользователь_Установки] WHERE [Пользователь] = '".(int)$_SESSION['usr']['user_id']."' AND [Проект] = '".$projectName."' AND [Таблица] = '".$jqGridIdent."'";
	$Core->query = $sql; //Читаем сохраненную видимость путей
	$Core->con_database('srv');
	$stm = $Core->PDO();
	$row = $stm->fetch();
	return $row['Видимость'];
}
?>