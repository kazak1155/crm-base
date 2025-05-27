<?php
function filterZeros($value)
{
	return ($value || is_numeric($value));
}
function isMobile()
{
	 return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
function mb_ucfirst($str)
{
	if (preg_match('/[a-z]/ui', $str[0]))
		return ucfirst($str);

	$first = mb_strtoupper($str[0].$str[1], 'utf8');
	$str[0] = $first[0];
	$str[1] = $first[1];
	return $str;
}
// Название месяца по метке UNIX
function getMonthName($unixTimeStamp = false,$short = false,int $month = 0 ) {
	if (!$unixTimeStamp)
		$mN = date('m');
	else
		$mN = date('m', (int)$unixTimeStamp);
	if($month > 0)
		$mN = $month;
	$monthAr = array(
		1 => array('Январь', 'Января'),
		2 => array('Февраль', 'Февраля'),
		3 => array('Март', 'Марта'),
		4 => array('Апрель', 'Апреля'),
		5 => array('Май', 'Мая'),
		6 => array('Июнь', 'Июня'),
		7 => array('Июль', 'Июля'),
		8 => array('Август', 'Августа'),
		9 => array('Сентябрь', 'Сентября'),
		10=> array('Октябрь', 'Октября'),
		11=> array('Ноябрь', 'Ноября'),
		12=> array('Декабрь', 'Декабря')
	);
	$monthArShort = array(
		1 => 'Янв.',
		2 => 'Фев.',
		3 => 'Мар',
		4 => 'Апр',
		5 => 'Май',
		6 => 'Июн.',
		7 => 'Июл.',
		8 => 'Авг.',
		9 => 'Сент.',
		10=> 'Окт.',
		11=> 'Ноя.',
		12=> 'Дек.'
	);
	if($short === false)
		return $monthAr[(int)$mN];
	else
		return $monthArShort[(int)$mN];
}
/*
* Function used to simply verify if given value is set. This technique just makes code more elegant.
*/
function v(&$var, $default=null, $if_empty='') {
	if (isset($var)) {
		if ($if_empty === '') return $var;
		elseif (empty($var)) return $if_empty;
		else return $var;
	}
	else return $default;
}
function print_pre ($expression, $return = false, $wrap = false)
{
  $css = 'border:1px dashed #000;background:#69f;padding:1em;text-align:left;';
  if ($wrap) {
    $str = '<p style="' . $css . '"><tt>' . str_replace(
      array('  ', "\n"), array('&nbsp; ', '<br />'),
      htmlspecialchars(print_r($expression, true))
    ) . '</tt></p>';
  } else {
    $str = '<pre style="' . $css . '">'
    . htmlspecialchars(print_r($expression, true)) . '</pre>';
  }
  if ($return) {
    if (is_string($return) && $fh = fopen($return, 'a')) {
      fwrite($fh, $str);
      fclose($fh);
    }
    return $str;
  } else
    echo $str;
}
?>
