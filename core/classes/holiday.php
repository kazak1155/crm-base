<?php
class holidaysCount{//Класс, вычисляющий разницу между датами с учетом выходных и праздников
	function __construct ($Core) {
		$this->Core = $Core;
	}
	function dcProcess ($days, $start = 0) {
		if($start == 0){$start = date('Y-m-d');}
		return $this->daysCount($start,$days);
	}
	function daysCount($prDays, $plusDays){ //Прибавляем (вычитаем) к первичной дате $plusDays рабочих дней
		unset($out); //Здесь будет дата, сдвинутая на указанное число дней
		$cntDat = $prDays; // дата начала отсчета, к которой мы прибавляем (или вычитаем) дни
		$tmpDat = explode(' ',$prDays);
		$prDays = $tmpDat[0];//На всякий случай, отсекаем часы, минуты и секунды
		$datArray = explode('-',$prDays);//Раскладываем дату в массив
		$yeHdays[$datArray[0]] = $this->getHolidays($prDays);//Выбираем выходные текущего года
		if($plusDays > 0){
			$yeHdays[((int)$datArray[0]+1)] = $this->getHolidays(((int)$datArray[0]+1).'-'.$datArray[1].'-'.$datArray[2]);//Если сдвиг вперед, выбираем следующий год
		}elseif($plusDays < 0){
			$yeHdays[((int)$datArray[0]-1)] = $this->getHolidays(((int)$datArray[0]-1).'-'.$datArray[1].'-'.$datArray[2]);//Если сдвиг назад, выбираем предыдущий год
		}
		$countStart = (abs($plusDays)); // Счетчик прибавляемых дней
		while($countStart){
			if($plusDays > 0){
				$cntDat = date('Y-m-d', strtotime('+1 day', strtotime($cntDat)));
			}elseif($plusDays < 0){
				$cntDat = date('Y-m-d', strtotime('-1 day', strtotime($cntDat)));
			}
			if($this->workDayTest($cntDat,$yeHdays) > 0){
				$countStart--;
			}
		}
		return $cntDat;
	}
	function workDayTest($cntDat,$yArr){ //Функция проверяет, является ли день рабочим. На вход подается тестируемая дата в формате yyyy-mm-dd и массив праздников
		$datArr = explode('-',$cntDat);
		$tDtMonth = $yArr[$datArr[0]]['month'][(int)$datArr[1]]; //Выбираем массив месяца, в котором будем искать, рабочий ли день.
		if($tDtMonth[(int)$datArr[2]] < 0) { //Если в карте внеочередных выходных с этом месте стоит отрицательное число, потому что рабочий день, хотя по календарю выходной
			$out = 1;// Это рабочий день
			$d=1;
		}elseif($tDtMonth[(int)$datArr[2]] == 0){
			$nDay = date('w', strtotime($cntDat)); if($nDay == 0){$nDay = 7;}//Получаем день недели предыущего дня
			if((int)$nDay == 6 || (int)$nDay == 7){ //Если день недели больше пятницы
				$out = 0; // Это выходной
				$d=2;
			}else{
				$out = 1; // Это рабочий день
				$d=3;
			}
		}else{
			$out = 0; // Это выходной
			$d=4;
		}
		//		print '('.$cntDat.') day:'.$nDay.' % '.$d.' __ '.$out." |||<br/>";
		return $out;
	}
	
	function getHolidays($dt){ //Выбираем праздники из базы
		$datArr = explode('-',$dt);
		$sql = "SELECT * FROM [srv].[dbo].[црм_Праздники] WHERE [Год] = '".$datArr[0]."'";
		$this->Core->query = $sql;
		$this->Core->con_database('srv');
		$stm = $this->Core->PDO();
		$row = $stm->fetch();
		$ye[0] = $row['Год'];
		$ye[1] = $this->dExplode($row['Январь']);
		$ye[2] = $this->dExplode($row['Февраль']);
		$ye[3] = $this->dExplode($row['Март']);
		$ye[4] = $this->dExplode($row['Апрель']);
		$ye[5] = $this->dExplode($row['Май']);
		$ye[6] = $this->dExplode($row['Июнь']);
		$ye[7] = $this->dExplode($row['Июль']);
		$ye[8] = $this->dExplode($row['Август']);
		$ye[9] = $this->dExplode($row['Сентябрь']);
		$ye[10] = $this->dExplode($row['Октябрь']);
		$ye[11] = $this->dExplode($row['Ноябрь']);
		$ye[12] = $this->dExplode($row['Декабрь']);
		return array('month'=>$ye,'nams'=>$row);
	}
	function dExplode($dtt){
		$out = explode(',',$dtt);
		foreach($out as $k => $v){
			if($v != '' && $v > 0){$Out[abs($v)] = 1;}
			if($v != '' && $v < 0){$Out[abs($v)] = -1;}
		}
		return $Out;
	}
	
}
?>