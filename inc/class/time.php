<?php
/*
 *
 *	时间校正
 *
 *	2011/7/10 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}
if ( !defined('JIUWAP_TIME_CHA') ){
	date_default_timezone_set('PRC');
	function NTP_time($i=0){
		if ( $i >= 3 ){
			trigger_error('NTP_time ERROR!!#2', E_USER_ERROR);
		}
		$ini = DIR. 'temp/NTP_time.ini';
		$local = $net = $cha1 = 0;
		if ( !file_exists($ini) ){
			if ( !$fp = @fsockopen('203.129.68.14',13,$errno,$errstr,90) ){
				trigger_error('NTP_time ERROR!!#1', E_USER_ERROR);
			}
			$net = @fread($fp,2096);
			if ( empty($net) ){
				NTP_time(++$i);
			}else{
				$net = strtotime($net);
				$local = time();

				$cha1 = $net - $local;
				file_put_contents($ini,$cha1);
			}

		}else{
			if ( ($cha1 = @file_get_contents($ini)) === false ){
				trigger_error('NTP_time ERROR!!#3',E_USER_ERROR);
			}
		}
		$net = $local + $cha1;
		define('JIUWAP_TIME_CHA',$cha1);
	}
	NTP_time();


	function time_(){
		return time() + JIUWAP_TIME_CHA;
	}

	function microtime_(){
		list($usec, $sec) = explode(' ', microtime());
		$sec = $sec + JIUWAP_TIME_CHA;
		return "$usec $sec";
	}

	function strtotime_($time,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return strtotime($time,$timestamp);
	}

	function date_($format,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return date($format,$timestamp);
	}

	function getdate_($timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return getdate($timestamp);
	}

	function gmdate_($format,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return gmdate($format,$timestamp);
	}

	function strftime_($format,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return strftime($format,$timestamp);
	}

	function localtime_($timestamp=false,$is_associative=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return localtime($timestamp,$is_associative);
	}
}