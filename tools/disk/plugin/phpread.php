<?php
!defined('m') && header('location: /?r='.rand(0,999));
echo '代码高亮'.hr;
if ( mime_istxt($dir['mime']) ){
	include DIR. 'inc/class/hightlight/class.stx.php';
	include DIR. 'inc/class/runtime.lib.php';
	$runtime = new runtime();
	$runtime->start();
	echo highlight_stx($b_set['dfforever'].$dir['file'],$dir['mime'],true);
	$runtime->stop();
	echo hr.'解析耗时: '.$runtime->spent().'毫秒<br/>源码开发: Editplus&amp;Tianyiw<br/><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
}else{
	echo '不支持此文件'.hr;
	echo '(高亮技术由Editplus提供)<br/><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
}
