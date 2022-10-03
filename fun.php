<?php
!defined('m') && header('location: /');

$h = isset($_GET['h']) ? $_GET['h'] : '';

if ( $h <> ''){
	$au = '&amp;h='.$h;
}else{
	$au = '';
}

$url = $browser->history_get($h);
if ( $url !== false ){
	$url = $url['url'];
}else{
	$url = 'http://';
}



load_template('menu',false);
