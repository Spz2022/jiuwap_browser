<?php
!defined('m') && header('location: /?r='.rand(0,999));
echo hr;
echo '<a href="disk.php?cmd=info&amp;do=down&amp;id='.$id.$h.'">下载文件</a><br/>';
echo '<a href="disk.php?cmd=info&amp;do=toemail&amp;id='.$id.$h.'">发至邮箱</a><br/>';
echo hr;
if ( $dir['mime'] == 'mrp'){
	echo '<a href="disk.php?cmd=info&amp;do=mrpchange&amp;id='.$id.$h.'">修改MRP</a><br/>';
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;id='.$id.$h.'">解包MRP</a><br/>';
}elseif ( $dir['mime'] == 'doc'){
	echo '<a href="disk.php?cmd=info&amp;do=docread&amp;id='.$id.$h.'">在线阅读</a><br/>';
}elseif ( $dir['mime'] == 'zip' || $dir['mime'] == 'jar' || $dir['mime'] == 'gz' ){
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;id='.$id.$h.'">在线解压</a><br/>';
}elseif ( mime_istxt($dir['mime']) ){
	echo '<a href="disk.php?cmd=info&amp;do=txtread&amp;id='.$id.$h.'">查看内容</a><br/>';
	echo '<a href="disk.php?cmd=info&amp;do=phpread&amp;id='.$id.$h.'">代码高亮</a><br/>';
}elseif ( mime_ispic($dir['mime']) ){
	echo '<a href="disk.php?cmd=info&amp;do=picread&amp;id='.$id.$h.'">查看图片</a><br/>';
}else{
	$no_hr = false;
}
if( !isset($no_hr) ){ echo hr;}
echo '<a href="disk.php?cmd=info&amp;do=move&amp;id='.$id.$h.'">移动文件</a><br/>';
echo '<a href="disk.php?cmd=info&amp;do=copy&amp;id='.$id.$h.'">复制文件</a><br/>';
echo '<a href="disk.php?cmd=info&amp;do=delete&amp;id='.$id.$h.'">删除文件</a><br/>';
echo '<a href="disk.php?cmd=info&amp;do=rename&amp;id='.$id.$h.'">文件改名</a>';
echo hr.'网盘上传模式代码：<a>[disk='.$id.']</a>，使用网盘上传模式可以轻松将网盘文件上传到网页！<br/>';
