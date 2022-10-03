<?php
!defined('m') && header('location: /?r='.rand(0,999));
//解压缩临时目录
$_dir = $b_set['dftemp'].md5($id.'_u');
$__dir = FixSysUrlCode($_dir);

//压缩包文件
$_file = $b_set['dfforever'].$dir['file'];
$__file = FixSysUrlCode($_file);

//当前相对目录
$_url = isset($_GET['dir']) ? $_GET['dir'] : '';
$_url && $_url = str_replace(array('\\','//'),'/',$_url);
$_url && $_url = str_replace(array(chr(0),"\t","..","./","\n","\r"),'',$_url);
$__url = urlencode($_url);

//上级相对目录
$_up = strrpos(substr($_url,0,strlen($_url)),'/');
if ( $_up ){
	$_up = substr($_url,0,$_up);
	$__up = urlencode($_up);
}else{
	$__up = $_up = '';
}

//上级绝对目录
if ( $_up == '' ){
	$__tup = $_tup = $_dir;
}else{
	$_tup = $_dir . '/'.$_up;
	$__tup = FixSysUrlCode($_tup);
}

//当前目录文件名
if ( $_url <>'' ){
	if ( $_up == '' ){
		$_name = $_url;
	}else{
		$_name = substr($_url,strlen($_up)+1);
	}
}else{
	$_name = $_url;
}

//当前目录绝对目录
if ( $_url == '' ){
	$__turl = $_turl = $_dir;
}else{
	$_turl = $_dir . '/'.$_url;
	$__turl = FixSysUrlCode($_turl);
}

if ( !is_dir($_dir) ){
	set_time_limit(300);
	ignore_user_abort(true);
	include_once DIR.'inc/class/runtime.lib.php';
	$rtime = new runtime();
	$rtime->start();
	if ( $dir['mime'] == 'mrp' ){
		include DIR. 'inc/class/mrp.lib.php';
		if ( !$list = mrp::unpack($__file,$__dir) ){
			echo '错误:损坏的或者不是MRP文件';
			echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
			$browser->template_foot();
		}
		$arr = array();
		foreach ( $list as $val){
			$arr[] = $val['n'];
		}
		@file_put_contents($__dir.'_list',serialize($arr));
	}elseif( $dir['mime'] == 'zip' || $dir['mime'] == 'jar' ){
		include DIR. 'inc/class/pclzip.lib.php';
		$archive = new PclZip($__file);
		if ( $err = $archive->extract(PCLZIP_OPT_PATH, $__dir) == 0) {
			echo '错误:'.pclzip_error_code($archive->errorCode());
			echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
			$browser->template_foot();
		}
	}else{
		echo '不支持或损坏的压缩包！';
		echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
		$browser->template_foot();
	}
	$rtime->stop();
	echo '解包耗时：'.$rtime->spent().'毫秒<br/>';
}elseif ( !file_exists($__dir) ) {
	echo '解压缩失败，请刷新本页面。';
	echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
	$browser->template_foot();
}

$is_dir = is_dir($__turl);
$is_file = is_file($__turl);

echo hr;

if ( !$is_dir && !$is_file ) {
	echo '文件(夹)不存在<br/><img src="'.getico().'" alt="dir"/><a href="disk.php?cmd=info&amp;do=unzip&amp;dir=&amp;id='.$id.$h.'">返回根目录</a><br/>';
}elseif ( $is_dir ){
	include 'unzip_list.php';
}elseif ( $is_file ){
	include 'unzip_info.php';
}else{
	echo '系统错误<br/><img src="'.getico().'" alt="dir"/><a href="disk.php?cmd=info&amp;do=unzip&amp;dir=&amp;id='.$id.$h.'">返回根目录</a><br/>';
}
