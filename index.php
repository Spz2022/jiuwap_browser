<?php
/*
 *
 *	浏览器->index入口点
 *
 *	2011-3-12 @ jiuwap.cn
 *

 */
$version = '0';
@include 'set_config/version.php';
if ( $version == 0 ){
	header('LOCATION: /install/index.php');
    exit;
}
header('Pragma: no-cache');
header('Expires: Mon, 26 Jul 2010 05:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');

define('DEFINED_JIUWAP','jiuwap.cn');
define('m','true');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';


$browser->user_login_check();

$cmd = trim($_SERVER['QUERY_STRING']);

if ( $cmd=='' || $cmd == '_main' || substr($cmd,0,6)=='_main=' || isset($_GET['r']) && $_GET['r']<>'' &&  !isset($_POST['url']) ){
	//include DIR.'parse/function.php';
	load_template('default');
	exit;
}

init_ad_index();

//p o n m h b s v po d z q dl dh dn fi
if ( isset($_GET['p']) && $_GET['p']<>'') {
	include DIR.'parse/pic.php';
	exit;
}elseif( isset($_GET['o']) && $_GET['o']<>'') {
	$url = $browser->history_get($_GET['o']);
	if ( $url !== false ){
		header('Location: '.$url['url']);
	}else{
		header('Location: /');
	}
	exit;
}elseif( isset($_GET['z']) && $_GET['z']<>'') {
	include DIR.'parse/parse_down_file.php';
	exit;
}elseif( isset($_GET['q']) && $_GET['q']<>'') {
	include DIR.'tools/disk/upload_browser.php';
	exit;

}elseif( isset($_GET['d']) && $_GET['d']<>'') {
	$arr = $browser->cache_get('pic',$_GET['d']);
	if ( isset($arr['url']) && !empty($arr['url']) ){
		header('Location: '.$arr['url']);
	}else{
		header('Location: /');
	}
	exit;
}elseif( isset($_GET['n']) && $_GET['n']<>'') {
	$_GET['h'] = $_GET['n'];
	unset($_GET['n']);
	include DIR.'book.php';
	exit;
}elseif( isset($_GET['m']) && $_GET['m']<>'' || $cmd == 'm=') {
	$_GET['h'] = $_GET['m'];
	unset($_GET['m']);
	include DIR.'fun.php';
	exit;
}elseif ( (isset($_GET['h']) && $_GET['h']<>'') || (isset($_GET['dl']) && $_GET['dl']<>'')  || (isset($_GET['dh']) && $_GET['dh']<>'') ) {
    if ( isset($_GET['h']) ){
        $id = $_GET['h'];
    }elseif ( isset($_GET['dh']) ){
        $id = $_GET['dh'];
        $form_diskupload = false;
    }else{
        $id = $_GET['dl'];
        $form_diskupload = true;
    }
	$url = $browser->history_get($id);
	if ( $url === false ){
        error_show('历史记录不存在','错误：历史记录不存在#1['.$id.']');
	}
	include DIR.'parse/parse_history.php';
	exit;
}elseif ( isset($_GET['b']) && $_GET['b']<>'') {
	$id = (int)$_GET['b'];
	$url = $browser->book_get($id,true);
	if ( $url == false ){
        error_show('书签未找到','错误：书签未找到#1');
	}
	$url = $url['url'];
}elseif ( isset($_GET['s']) && $_GET['s']<>'') {
	$id = (int)$_GET['s'];
	$url = $browser->site_get($id);
	if ( $url == false ){
        error_show('导航站未找到','错误：导航站未找到#1');
	}
	$url = $url['url'];
}elseif ( isset($_GET['v']) && $_GET['v']<>'') {
	//css
	$url = $browser->cache_get('url',$_GET['v']);
	if ( $url == false ){
		exit;
	}else{
		$is_css = true;
		include DIR.'parse/init.php';
	}
}elseif ( isset($_GET['url']) ) {
	$url = ubb_copy(fix_r_n_t($_GET['url']));
}else{
    if ( isset($_GET['dn']) && $_GET['dn']<>''){
        //网盘上传
		$cmd = $_GET['dn'];
		$form_2diskup = true;
	}elseif ( isset($_GET['po']) && $_GET['po']<>''){
        //GET变为POST
		$cmd = $_GET['po'];
		$form_post2get = true;
	}else{
		$i = strpos($cmd,'&');
		if ($i!==false){
			$cmd = substr($cmd,0,$i);
		}
		unset($i);
	}
	$url = $browser->cache_get('url',$cmd);
	if ( $url == false ){
        error_show('URL缓存未找到','错误：URL缓存未找到#1');
	}
}

include DIR.'parse/init.php';
