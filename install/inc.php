<?php
@error_reporting(4095);
@date_default_timezone_set('PRC');
@set_time_limit(1000);
@ob_start('ob_gzip');
@ini_set('register_globals', false);
@ini_set("magic_quotes_runtime", 0);
@ini_set("magic_quotes_sybase", 0);

if ( !defined('DEFINED_JIUWAP') || DEFINED_JIUWAP <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

define('DEFINED_TIANYIW','jiuwap.cn');

define('DIR',$_SERVER['DOCUMENT_ROOT'].'/');

/*$install_version = '20110831';
$install_password = 'jiuwap';
$install_full = true;
$version = 0;

@include DIR.'set_config/version.php';
@include DIR.'set_config/install_password.php';

if ( !$version || $version < $install_version ){
	$install_full = true;
}*/

function ob_gzip($content){
	if( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && !headers_sent() && extension_loaded('zlib') && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') ){
		$content = gzencode($content,9);
		header("Content-Encoding: gzip");
		header("Vary: Accept-Encoding");
		header("Content-Length: ".strlen($content));
	}
	return $content;
}

$iswml = isset($_SERVER['HTTP_ACCEPT']) ? '.'.strtolower($_SERVER['HTTP_ACCEPT']) : false;
if ( $iswml && (
	strpos($iswml,'text/html') ||
	strpos($iswml,'application/xhtml+xml')
	)){
	$iswml= false;
	define('hr','<hr/>');
}else{
	$iswml = isset($_SERVER['HTTP_USER_AGENT']) ? '.'.$_SERVER['HTTP_USER_AGENT'] : false;
	if (
		$iswml && (
		stripos($iswml,'MSIE') ||
		stripos($iswml,'Windows') ||
		stripos($iswml,'Mozilla') ||
		stripos($iswml,'Symbian') ||
		stripos($iswml,'iPhone') ||
		stripos($iswml,'KHTML') ||
		stripos($iswml,'Chrome') ||
		stripos($iswml,'ucweb') ||
		stripos($iswml,'smartphone') ||
		stripos($iswml,'blackberry') ||
		stripos($iswml,'opera') ||
		stripos($iswml,'AppleWebKit')
		)){
		$iswml= false;
		define('hr','<hr/>');
	}else{
		$iswml= true;
		define('hr','<br/>------------<br/>');
	}
}

function top($title){
	global $iswml;
	if ( $iswml ){
		header('Content-Type: text/vnd.wap.wml; charset=utf-8');
		echo '<?xml version="1.0" encoding="utf-8"?>
		<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
		<wml><head><meta http-equiv="Cache-Control" content="max-age=0"/>
		<meta http-equiv="Cache-Control" content="no-cache"/></head>
		<card id="main" title="'.$title.'">
		<p>';
	}else{
		echo '<?xml version="1.0" encoding="utf-8"?>
		<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml; charset=utf-8"/>
		<meta http-equiv="Cache-Control" content="must-revalidate,no-cache"/>
		<title>'.$title.'</title><style>body{font-size:15px;color:#000;font-family:Arial,Helvetica,sans-serif;}a{color:#039;text-decoration:none;}</style>
		</head><body>';
	}
}

function foot(){
	global $iswml;
	echo hr;
	echo date('Y-n-j H:i:s').'<br/><b>Powered By <a href="http://jiuwap.cn">jiuwap.cn</a></b>';
	if ( $iswml ){
		echo '</p></card></wml>';
	}else{
		echo '</body></html>';
	}
	exit;
}

function GET_LOGIN_KEY_LONG() {
    return str_shuffle('lDEFABstCNOPyzghijQRSTUwxkVWXYZabcdef+IJK6/7nopqr89LMmGH012345uv=') ;
}

function str_rand($len=5) {
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ' ;
    for ($i = 0,$key = ''; $i < $len; $i++) {
        $key .= $pattern{mt_rand(0, 62)} ;
    }
    return $key ;
}

function mkdirs($path, $mode = 0777){
	$dirs = explode('/',$path);
	$pos = strrpos($path, '.');
	if ($pos === false) {
		$subamount=0;
	}else{
		$subamount=1;
	}
	for ($c=0;$c < count($dirs) - $subamount; $c++) {
		$thispath='';
		for ($cc=0; $cc <= $c; $cc++) {
			$thispath.=$dirs[$cc].'/';
		}
		if (!file_exists($thispath)) {
			@mkdir($thispath);
			@chmod($thispath,$mode);
		}
	}
}


function fixPath($path){
	return str_replace('\\','/',$path);
}

function post($title,$value=''){
	if ( isset($_POST[$title]) ){
		return trim($_POST[$title]);
	}else{
		return $value;
	}
}

function quick_connect($url){
	$fp = @fsockopen('localhost',80);
	if ( !$fp ){
		break;
	}
	$out = "GET /{$url} HTTP/1.1\r\n";
	$out .= "Host: {$_SERVER['SERVER_NAME']}\r\n";
	$out .= "Connection: Close\r\n\r\n";
	@fwrite($fp, $out);
	@fclose($fp);
}

