<?php
class Base extends Connection
{
	public function set_sess($sub_k,$v,$k = null)
	{
		if(is_null($k))
			$_SESSION[$sub_k] = $v;
		else
			$_SESSION[$k][$sub_k] = $v;
	}
	public function get_sess($sub_k,$k = null)
	{
		if(is_null($k))
			return $_SESSION[$sub_k];
		else
			return $_SESSION[$k][$sub_k];
	}
	public function remove_sess($sub_k,$k = null)
	{
		if(is_null($k))
			unset($_SESSION[$sub_k]);
		else
			unset($_SESSION[$k][$sub_k]);
	}
	public function iconv_keys(array &$rgData, $sIn, $sOut)
	{
		$rgData = array_combine(
			array_map(
				function($sKey) use ($sIn, $sOut)
				{
	      			return iconv($sIn, $sOut, $sKey);
				},
				array_keys($rgData)
			),
			array_values($rgData)
		);
		foreach($rgData as &$mValue)
		{
			if(is_array($mValue))
				$mValue = $this->iconv_keys($mValue, $sIn, $sOut);
		}
		return $rgData;
	}

	public function full_date_to_server($string)
	{
		$string = explode('.', $string);
		$string = $string[2].$string[1].$string[0].' '.date('H:i:s');
		return ($string);
	}
	public function full_date_from_server($string)
	{
		$string = explode(' ',$string);
		$string = explode('-',$string[0]);
		$string = $string[2].'.'.$string[1].'.'.$string[0];
		return $string;
	}
	public function small_date_to_server($string)
	{
		$string = explode('.', $string);
		$string = $string[2].$string[1].$string[0];
		return ($string);
	}
}
