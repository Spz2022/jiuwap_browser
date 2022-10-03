<?php
!defined('m') && header('location: /?r='.rand(0,999));
$mime = get_short_file_mime($_url);
echo '文件:/'.$_url.'('.bitsize(filesize($__turl)).')<br/>';
$go = isset($_GET['go']) ? $_GET['go'] : '';

if ( in_array($go,array('save','rename','delete')) ){
	include 'unzip_info_'.$go.'.php';
	echo hr;
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回文件</a>';
	$browser->template_foot();
}else{
	$str = id2password($id.'|'.$_url,'4gsfghs').'/'.urlencode($_name);
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;go=save&amp;dir='.$__url.'&amp;id='.$id.$h.'">存至网盘</a><br/>';
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;go=rename&amp;dir='.$__url.'&amp;id='.$id.$h.'">文件改名</a><br/>';
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;go=delete&amp;dir='.$__url.'&amp;id='.$id.$h.'">删除文件</a><br/>';
	echo '<a href="disk.php/z/'.$str.'">下载文件</a><br/>';
	echo '(操作文件请先将文件存至网盘)<br/>';
	echo hr;
	echo '下载链接:<br/>';
	echo '<input type="text" value="http://'.$b_set['host'].'/disk.php/z/'.$str.'"/><br/>
	(允许外链,链接有效期为十分钟,本链接采用伪静态绝对地址,十分钟内任何地方任何工具都能使用本链接下载此文件。)';
}
echo hr.'<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.urlencode($_up).'&amp;id='.$id.$h.'">返回目录</a><br/>';
