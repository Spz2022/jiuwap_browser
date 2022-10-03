<?php
/*
 *
 *	浏览器->处理CSS
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}


!defined('m') && header('location: /?r='.rand(0,999));

$html = $http->response();
if ( !$html ){
	exit;
}

$code = 'utf-8';
$mime = 'text/vnd.wap.wml';

$html = str_ireplace('@','&at;at;',$html);
$html = str_replace("\n",'',$html);
$html = str_replace("\r",'',$html);
$html = preg_replace('@<!--(.*?)-->@','', $html);
$html = str_replace("\t",' ', $html);

$html = fix_css($html,false);

$html = str_ireplace('&at;at;','@',$html);

echo $html;