<?php
/*
 *
 *	浏览器->图片
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

$_GET['p'] = trim($_GET['p']);
$arr = $browser->cache_get('pic',$_GET['p']);
if ( !isset($arr['url']) || empty($arr['url']) ){
	header('HTTP/1.0 404 Not Found');
	exit;
}else{
	$url = $arr['url'];
}

$referer = $arr['referer'];

$url_A = parse_url($url);

//读取数据库COOKIE
$cookies = $browser->cookieGet($url_A['host'],$url_A['path']);

$filename = $b_set['utemp'].'pics/'.$browser->uid.'/'.md5(serialize($cookies)).sha1(substr($url,-7));
if ( $referer ){
    $filename .= substr(md5($referer),5,10);
}

if ( file_exists($filename) ){
	//加载缓存图片
	$_time = filemtime($filename);
	!$_time && $_time = filectime($filename);
	$arr = GetImageSize($filename);
	if ( $arr !== false ){
		Header('Content-type: '.$arr['mime']);
		echo file_get_contents($filename);
		exit;
	}
}

if ( $browser->pic_wap == 0 && in_array($arr['mime'],array('text/vnd.wap.wml','application/vnd.wap.xhtml+xml','application/vnd.wap.wmlc','text/vnd.wap.wmlscript','application/vnd.wap.wmlscriptc')) ){
	$zip = false;
}else{
	$zip = true;
}
unset($arr);

//检测是否嵌套浏览
include DIR.'set_config/set_forbidhost.php';
if ( in_array(strtolower($url_A['host']),$b_set['forbid']) && !stripos($url,$b_set['host'].'/self') ){
	header('HTTP/1.0 404 Not Found');
    exit;
}

$http = new httplib();
$http->referer($referer);
unset($referer);

if ( $http->open($url,30,3) == false ){
	header('HTTP/1.0 404 Not Found');
    exit;
}
$url_A = $http->parse_url();

//发送COOKIE
foreach($cookies as $cookie_key=>$cookie_value){
	$http->cookie($cookie_key,$cookie_value);
}
unset($cookies,$cookie_key,$cookie_value);

$browser->selectBrowserUA();

//开始
$http->send();

//获取返回头信息
$header = $http->header();
$mime = array('image/gif'=>'gif','image/jpeg'=>'jpg','image/bmp'=>'bmp','image/jpg'=>'jpg','image/png'=>'png');
if( !isset($header['CONTENT-TYPE']) ){
	header('HTTP/1.0 404 Not Found');
    exit;
}

$i = strpos($header['CONTENT-TYPE'],';');
if ( $i !== false ){
	$header['CONTENT-TYPE'] = substr($header['CONTENT-TYPE'],0,$i);
}
unset($i);

$header['CONTENT-TYPE'] = strtolower($header['CONTENT-TYPE']);
if ( !isset($mime[$header['CONTENT-TYPE']]) || isset($header['CONTENT-LENGTH']) && $header['CONTENT-LENGTH'] > 2000000){
	header('HTTP/1.0 404 Not Found');
    exit;
}

//保存返回的COOKIE
foreach ( $header['COOKIE'] as $key => $value){
	$browser->cookieSave($url_A['host'],$value['domain'],$key,$value['value'],$value['path'],$value['expires']);
}


$mime = $mime[$header['CONTENT-TYPE']];

//保存文件
$pic_file_content = $http->response();
writefile($filename, $pic_file_content) ;
unset($http);

$arr = GetImageSize($filename);

if ( $arr === false ){
	exit;
}

$width = $arr[0];
$height = $arr[1];

//开始处理图片咯！
switch ($arr[2]) {
	case 1://gif
		$im = ImageCreateFromGIF($filename);
		break;
	case 2://jpg
		$im = imagecreatefromJPEG($filename);
		break;
	case 3://png
		$im = ImageCreateFromPNG($filename);
		break;
	case 6://bmp
		$im = ImageCreateFromBMP($filename);
		break;
	default://
		$zip = true;
		break;
}

if ( !$zip || $browser->pic == 6){
	Header('Content-type: '.$arr['mime']);
	echo $pic_file_content;
	exit;
}

//图片质量
switch ($browser->pic) {
case 8:
	$deep = 80;
	$N_height = 500;
	$N_width = 500;
	break;
case 3:
	$deep = 70;
	$N_height = 400;
	$N_width = 400;
	break;
case 7:
	$deep = 50;
	$N_height = 320;
	$N_width = 320;
	break;
case 2:
	$deep = 20;
	$N_height = 320;
	$N_width = 240;
	break;
default:
	$deep = 70;
	$N_height = 320;
	$N_width = 320;
	break;
}

if ( $height > $N_height ){
	$i = $height / $N_height;
	$width = $width / $i;
	$height = $N_height;
}
if ( $width > $N_width ){
	$i = $width / $N_width;
	$height = $height / $i;
	$width = $N_width;
}

$srcW = ImageSX($im);
$srcH = ImageSY($im);
$ni = imagecreatetruecolor($width,$height);
if ($arr[2]<>6){
	imagealphablending($ni,false);
}
if ($arr[2]==1){
	$black = ImageColorAllocate($ni, 0,0,0);
	imagecolortransparent($ni,$black);
}elseif ($arr[2]==3){
	imagesavealpha($ni,true);
}
ImageCopyResized($ni,$im,0,0,0,0,$width,$height,$srcW,$srcH);

switch ($arr[2]) {
	case 1://gif
		Header('Content-type: image/gif');
		ImageGif($ni,$filename, $deep);
		break;
	case 2://jpg
		Header('Content-type: image/jpeg');
		ImageJpeg($ni,$filename, $deep);
		break;
	case 3://png
		Header('Content-type: image/png');
		$deep = (int)($deep/10);
		if ( $deep == 10 ){
			$deep = 9;
		}
		ImagePng($ni,$filename,$deep);
		break;
	case 6://bmp
		Header('Content-type: image/jpeg');
		ImageJpeg($ni,$filename, $deep);
		break;
	default://
		Header('Content-type: image/jpeg');
		ImageJpeg($ni,$filename, $deep);
		break;
}
ImageDestroy($im);

$pic_file_content2 = file_get_contents($filename);

$old = strlen($pic_file_content);
$new = strlen($pic_file_content2);
if ( $new >= $old  ){
    writefile($filename, $pic_file_content2) ;
	unset($pic_file_content2);
	echo $pic_file_content;
}else{
	$new = $old - $new;
	//echo $new;
	$browser->num_add(0,$new);
	unset($pic_file_content);
	echo $pic_file_content2;
}
exit;