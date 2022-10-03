<?php
/*
 *
 *	浏览器->清理
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
$browser->user_login_check();

$h = isset($_GET['h']) ? $_GET['h'] : '';
if ( $h <> ''){
	$au = '&amp;h='.$h;
}else{
	$au = '';
}

if ( isset($_GET['cookie']) ){
	$browser->template_top('清理COOKIE');
	echo '清理COOKIES：<br/>';
	if ( $_GET['cookie'] == 'yes' ){
		$browser->cookie_del();
		echo '操作成功。<br/><a href="clear.php?h='.$h.'">返回</a>';
	}else{
		echo '此操作将提高浏览器处理速度，但会造成某些网站的登陆信息丢失。<br/>
		<a href="clear.php?cookie=yes&amp;h='.$h.'">确认</a>.<a href="clear.php?h='.$h.'">返回</a>';
	}
}elseif ( isset($_GET['urls']) ){
	$browser->template_top('清理网址缓存');
	echo '清理网址缓存：<br/>';
	if ( $_GET['urls'] == 'yes' ){
		$browser->cacheurl_del('url');
		echo '操作成功。<br/><a href="clear.php?h='.$h.'">返回</a>';
	}else{
		echo '(提示：您无需清理网址缓存，系统会自动覆盖旧缓存！当然，您也可以手工清理！)<br/>
		<a href="clear.php?urls=yes&amp;h='.$h.'">确认</a>.<a href="clear.php?h='.$h.'">返回</a>';
	}
}elseif ( isset($_GET['pics']) ){
	$browser->template_top('清理图片缓存');
	echo '清理图片缓存：<br/>';
	if ( $_GET['pics'] == 'yes' ){
		$browser->cacheurl_del('pic');
		echo '操作成功。<br/><a href="clear.php?h='.$h.'">返回</a>';
	}else{
		echo '提示：您无需清理图片缓存，系统会自动覆盖旧缓存！当然，您也可以手工清理！<br/>
		<a href="clear.php?pics=yes&amp;h='.$h.'">确认</a>.<a href="clear.php?h='.$h.'">返回</a>';
	}
}elseif ( isset($_GET['history']) ){
	$browser->template_top('清理历史记录');
	echo '清理历史记录：<br/>';
	if ( $_GET['history'] == 'yes' ){
		$browser->history_del();
		echo '操作成功。<br/><a href="clear.php?h='.$h.'">返回</a>';
	}else{
		echo '您确认要清空历史记录？<br/>
		<a href="clear.php?history=yes&amp;h='.$h.'">确认</a>.<a href="clear.php?h='.$h.'">返回</a>';
	}
}elseif ( isset($_GET['num']) ){
	$browser->template_top('清理流量统计');
	echo '清理流量统计：<br/>';
	if ( $_GET['num'] == 'yes' ){
		$browser->num_del();
		echo '操作成功。<br/><a href="clear.php?h='.$h.'">返回</a>';
	}else{
		echo '此操作将重置流量统计计数器(累计的图片、页面压缩、浏览次数)。<br/>
		<a href="clear.php?num=yes&amp;h='.$h.'">确认</a>.<a href="clear.php?h='.$h.'">返回</a>';
	}
}else{
	$browser->template_top('清理');
	echo $b_set['webtitle'].'-清理<br/>返回:';
	if ( $h<>'' ){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>.<a href="/">首页</a><br/>
	-------------<br />
	<a href="clear.php?cookie=check&amp;h='.$h.'">清理COOKIES</a><br/>
	<a href="clear.php?urls=check&amp;h='.$h.'">清理网址缓存</a><br/>
	<a href="clear.php?pics=check&amp;h='.$h.'">清理图片缓存</a><br/>
	<a href="clear.php?history=check&amp;h='.$h.'">清理历史记录</a><br/>
	<a href="clear.php?num=check&amp;h='.$h.'">清理流量统计</a><br/>
	';
}

$browser->template_foot();