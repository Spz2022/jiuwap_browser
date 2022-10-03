<?php
!defined('m') && header('location: /?r='.rand(0,999));
echo '目录:/'.$_url.'<br/>';
echo hr;

if ( isset($_GET['yes']) ){
	deldir($__turl);
	$__url = $__up;
	echo '删除完成';
}else{
	echo '确定要删除['.$_url.']吗？<br/>(目录里的文件也将被删除)<br/>
	<a href="disk.php?cmd=info&amp;dir='.$__url.'&amp;act=deldir&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'">确认删除</a>';
}
echo hr;
echo '<a href="disk.php?cmd=info&amp;dir='.$__url.'&amp;do=unzip&amp;id='.$id.$h.'">返回目录</a>';
$browser->template_foot();
