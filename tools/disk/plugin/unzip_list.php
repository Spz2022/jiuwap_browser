<?php
!defined('m') && header('location: /?r='.rand(0,999));
$act = isset($_GET['act']) ? $_GET['act'] : null;
if ( $act == 'pack' ){
	include 'unzip_pack.php';
}elseif( $act == 'add' ){
	include 'unzip_add.php';
}elseif( $act == 'newdir' ){
	include 'unzip_newdir.php';
}elseif( $act == 'deldir' ){
	include 'unzip_deldir.php';
}elseif( $act == 'rename' ){
	include 'unzip_rename.php';
}elseif( $act == 'unpack' ){
	include 'unzip_unpack.php';
}
echo '<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a><br/>';
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;act=pack&amp;id='.$id.$h.'">重新打包</a>,';
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;act=unpack&amp;id='.$id.$h.'">重新解包</a><br/>';
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;act=add&amp;dir='.$__url.'&amp;id='.$id.$h.'">添加文件</a>,';
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;act=newdir&amp;dir='.$__url.'&amp;id='.$id.$h.'">新建目录</a>';
if ( $_url != '' ){
	echo '<br/><a href="disk.php?cmd=info&amp;do=unzip&amp;act=deldir&amp;dir='.$__url.'&amp;id='.$id.$h.'">删除目录</a>,';
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;act=rename&amp;dir='.$__url.'&amp;id='.$id.$h.'">目录改名</a>';
}
echo hr;
echo '目录:/'.$_url.'<br/>';
if ( $_url <> ''){
	echo '<img src="'.getico().'" alt="dir"/><a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.urlencode($_up).'&amp;id='.$id.$h.'">返回上级</a><br/>';
}

$list_dir = '';
$list_file = '';
$__url<>'' && $__url .= '%2F';
$dir = opendir($__turl);
while( $file = readdir($dir) ){
	if ( $file == '.' || $file == '..' ){
		continue;
	}
	if ( is_dir($__turl.'/'.$file) ){
		@$file = iconv(SYSFILECODE, 'UTF-8', $file);
		$list_dir .= '<img src="'.getico().'" alt="dir"/><a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.urlencode($file).'&amp;id='.$id.$h.'">'.$file.'</a><br/>';
	}else{
		@$file = iconv(SYSFILECODE, 'UTF-8', $file);
		$mime = get_short_file_mime($file);
		$list_file .= '<img src="'.getico($mime).'" alt="'.$mime.'"/><a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.urlencode($file).'&amp;id='.$id.$h.'">'.$file.'</a><br/>';
	}
}
if ( !$list_dir && !$list_file){
	echo '文件列表为空';
}else{
	echo $list_dir.$list_file;
}
