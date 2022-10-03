<?php
/*
 *
 *	浏览器->历史
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
$browser->user_login_check();
$h = isset($_GET['h']) ? $_GET['h'] : '';
$browser->template_top('历史浏览');
$history = $browser->history_get();
echo ''.$b_set['webtitle'].'-历史<br/>
返回:<a href="/?h='.$h.'">网页</a>.
	<a href="/?m='.$h.'">菜单</a>.
	<a href="/">首页</a>'.hr;
if ( $history == array() ){
	echo '无';
}else{
	$echo = '';
	foreach($history as $id=>$val){
		$echo = '<a href="/?h='.$id.'">'.urldecode($val['title']).'</a><br/>'.$echo ;
	}
	echo $echo;
}
echo hr.'<a href="clear.php?history=check&amp;h='.$h.'">清理历史记录</a>';
$browser->template_foot();