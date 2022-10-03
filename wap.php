<?php
define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
$back = isset($_GET['back']) ? $_GET['back'] : '';
$h = isset($_GET['h']) ? $_GET['h'] : '';

if ( isset($_GET['wap']) && $_GET['wap'] == 0 ){
	$browser->template_set('0');
}else{
	$browser->template_set('1');
}

if ( empty($back) ){
	$url = 'index.php';
}elseif( $back == 'login'){
	$url = 'login.php';
}elseif( $back == 'set'){
	$url = 'set.php?h='.$h;
}else{
	$url = 'index.php';
}

header('location: '.$url);