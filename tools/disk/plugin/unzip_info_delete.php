<?php
!defined('m') && header('location: /?r='.rand(0,999));

if ( isset($_GET['yes']) ){
	@unlink($__turl);
	echo '删除完成<br/>';
	echo hr;
	echo '<a href="disk.php?cmd=info&amp;dir='.$__up.'&amp;do=unzip&amp;id='.$id.$h.'">返回目录</a>';
	$browser->template_foot();
}else{
	echo '确定要删除['.$_name.']吗？<br/>
	<a href="disk.php?cmd=info&amp;dir='.$__url.'&amp;go=delete&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'">确认删除</a>';
}
