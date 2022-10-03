<?php
/*
 *
 *	浏览器->网盘入口点
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

define('DEFINED_JIUWAP','jiuwap.cn');
define('m','true');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
include_once DIR.'tools/disk/inc.php';
if ( isset($_SERVER['PATH_INFO']) && substr($_SERVER['PATH_INFO'],0,3) == '/d/' ){
	$browser->user_login_check();
	$id = substr($_SERVER['PATH_INFO'],3);
	$id = substr($id,0,strpos($id,'/'));
	if ( $id ){
		$id = password2id($id,'4hr5h5da');
		if ( $id !== false){
			define('no_ob_gzip','true');
			include DIR.'tools/down_file.php';
		}else{
			include DIR.'tools/disk/_nofoundfile.php';
		}
	}else{
		include DIR.'tools/disk/_nofoundfile.php';
	}
	unset($id);
}elseif ( isset($_SERVER['PATH_INFO']) && substr($_SERVER['PATH_INFO'],0,3) == '/z/' ){
	$id = substr($_SERVER['PATH_INFO'],3);
	$id = substr($id,0,strpos($id,'/'));
	if ( $id ){
		$id = password2id($id,'4gsfghs');
		if ( $id  !== false){
			define('no_ob_gzip','true');
			include DIR.'tools/disk/down_file_zip.php';
		}else{
			include DIR.'tools/disk/_nofoundfile.php';
		}
	}else{
		include DIR.'tools/disk/_nofoundfile.php';
	}
	unset($id);
}elseif ( isset($_SERVER['PATH_INFO']) && substr($_SERVER['PATH_INFO'],0,1) == '/' ){
	$id = substr($_SERVER['PATH_INFO'],1);
	$id = substr($id,0,strpos($id,'/'));
	if ( $id ){
		$id = password2id($id);
		if ( $id !== false){
			define('no_ob_gzip','true');
			include DIR.'tools/disk/_downfile.php';
		}else{
			include DIR.'tools/disk/_nofoundfile.php';
		}
	}else{
		include DIR.'tools/disk/_nofoundfile.php';
	}
	unset($id);
}else{
	$browser->user_login_check();
}


$u = isset($_GET['h']) ? $_GET['h'] : '';
if ( $u <> ''){
	$h = '&amp;h='.$u;
}else{
	$h = '';
}

init_disk();

$fun = array('newzip','newdir','newtxt','info','upload');


if ( isset($_GET['cmd']) && in_array($_GET['cmd'],$fun) ){
	include DIR.'tools/disk/'.$_GET['cmd'].'.php';
}else{
	include DIR.'tools/disk/index.php';
}