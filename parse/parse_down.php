<?php
/*
 *
 *	浏览器->下载提示
 *
 *	2011-3-11 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

!defined('m') && header('location: /?r='.rand(0,999));
$filename = $b_set['rini'].$browser->uid.'_'.sha1($url);
$size = isset($header['CONTENT-LENGTH']) ? $header['CONTENT-LENGTH'] : -1;

if ( isset($header['CONTENT-DISPOSITION']) ){
	$file_name = fix_disposition($header['CONTENT-DISPOSITION']);
}else{
	$file_name = fix_basename($url);
}

$file_name = getUTFString($file_name);

$file_type = isset($header['CONTENT-TYPE']) ? $header['CONTENT-TYPE'] : 'application/octet-stream';
$file = false;
if ( false !== ($var = @file_get_contents($filename) ) ){
	if ( $var = @unserialize($var) ){
		$arr = $browser->cache_get('pic',$var['id']);
		if ( isset($arr['url']) && !empty($arr['url']) ){
			$file =  $var['id'];
		}
		unset($arr);
	}
	unset($var);
}

if ( !$file ) {
	$file = $browser->cache_add('pic',$url,'','');
	$browser->cacheurl_set();
	$content = array();
	$content['size']= $size;
	$content['name']= $file_name;
	$content['id']= $file;
	$content['type']= $file_type;
	$content['referer']= $http->referer();
	$content['post'] = $http->private_post();
	$content['header'] = $http->private_header();
	$content['cookie'] = $http->private_cookie();
	writefile($filename,serialize($content));
	unset($content);
}

$html_title = '下载提示';

$browser->template_top($html_title);
echo $b_set['webtitle'].'下载提示<br/>';
echo '文件：'.$file_name.'<br />';
echo '大小：'.bitsize($size).'<br />';
echo '类型：'.$file_type.'<br />';
echo '地址：'.htmlspecialchars($url).'<br />';
if ( $size <= $b_set['tupload'] ){
	echo '<a href="?d='.$file.'">直接下载</a><br/>';
	echo '<a href="?z='.$file.'">中转下载</a><br/>';
	echo '<a href="?q='.$file.'">存到网盘</a><br/>';
}else{
	$strtps = bitsize($b_set['tupload']);
	echo '<a href="?d='.$file.'">直接下载</a><br/>';
	echo '中转下载(当前不允许中转大于'.$strtps.'的文件)<br/>';
	echo '存到网盘(当前不允许转存大于'.$strtps.'的文件)<br/>';
}
echo '推荐直接下载，如果下载失败请使用中转下载！<br/>';
echo '更多功能(如阅读doc,解压缩,java服务)请到网盘操作！<br/>';
$browser->template_foot(false);
if ( $browser->template == '1' ){
    $mime = 'text/vnd.wap.wml';
}else{
    $mime = 'application/vnd.wap.xhtml+xml';
}
$code = 'utf-8';
$html_size_old = -1;
$html = ob_get_contents();
$html_size_new = strlen($html);
$the_history_key = $browser->history_add($html_title,$url,$html,$mime,$code,$html_size_new);
ob_clean();

include DIR. 'parse/parse_foot.php';