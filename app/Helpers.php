<?php

function is_day($value){
	return  preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])$/", $value);
}

function is_month($value){
	return  preg_match("/^(0[1-9]|1[0-2])$/", $value);
}

function is_year($value){
	return preg_match('/(20[0-9][0-9])/', $value);
}

function is_iso_date($date){
	return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date);
}

function now($val=null){
	switch ($val) {
		case 'year':
			return date('Y', strtotime('now'));
			break;
		case 'month':
			return date('m', strtotime('now'));
			break;
		case 'day':
			return date('d', strtotime('now'));
			break;
		default:
			return date('Y-m-d', strtotime('now'));
			break;
	}
	
}

function pad($val, $len=2, $char='0', $direction=STR_PAD_LEFT){
	return str_pad($val, $len, $char, $direction);
}


