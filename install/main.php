<?php
if ( !defined('DIR') ){
	die('hello world');
}
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

top('安装玖玩浏览器');
echo '<b>安装玖玩浏览器</b>';
echo hr;

echo '感谢您选择并安装玖玩浏览器！<br/>';
echo '当前版本:'.$version.'(<a href="index.php?do=update">检测升级</a>)<br/>';
echo hr;


echo '<a href="index.php?do=install_full">重新安装</a>,<a href="index.php?do=clear">清理缓存</a><br/>';

echo '提示：您无需删除本页面哦。本页无任何安全隐患。<br/>';
echo '<a href="/index.php">返回浏览器</a><br/>';
foot();