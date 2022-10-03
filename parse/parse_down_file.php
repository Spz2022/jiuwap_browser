<?php
/*
 *
 *	浏览器->中转下载提示
 *
 *	2011-4-3 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

!defined('m') && header('location: /?r='.rand(0,999));
include_once DIR.'tools/disk/inc.php';

$_GET['z']= trim($_GET['z']);

$arr = $browser->cache_get('pic',$_GET['z']);
if ( !isset($arr['url']) || empty($arr['url']) ){
	error_show('文件信息丢失(1),请重新下载。('.$_GET['z'].')');
}
$filename = $browser->uid.'_'.sha1($arr['url']);
if ( false !== ($content = @file_get_contents($b_set['rini'].$filename) ) ){
	if ( !$content = @unserialize($content) ){
		error_show('文件信息损坏(1),请重新下载。('.$_GET['z'].')');
	}
}else{
	error_show('文件信息丢失(2),请重新下载。('.$_GET['z'].')');
}
if ( $content['size'] > $b_set['tdown'] ){
	error_show('当前系统不允许中转下载大于'.bitsize($b_set['tdown']).'的文件。('.$_GET['z'].')');
}
$str = 'disk.php/d/'.id2password($_GET['z'],'4hr5h5da').'/'.urlencode($content['name']);

header('location: '.$str);
exit;

//$browser->template_top('网络硬盘');
//echo '本链接不支持外链，链接将在十分钟后失效，仅限本地下载，如需外链请转至网盘。<br/>';
//echo '<a href="'.$str.'">下载文件</a><br/>';
//$browser->template_foot();
