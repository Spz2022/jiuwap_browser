<?php
!defined('m') && header('location: /?r='.rand(0,999));

if ( isset($_GET['yes']) ){
	set_time_limit(100);
	ignore_user_abort(true);
	deldir($__dir);
	echo '删除已解包文件完毕,进入目录后将重新解包。<br/>';
}else{
	echo '确认要重新解包吗？<br/>(确认后将删除已解包文件,重新解包,如果文件过多,手机浏览器可能会显示网页超时,超时的话请直接返回.)<br/>
	<a href="disk.php?cmd=info&amp;dir='.$__url.'&amp;act=unpack&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'">确认解包</a>';
}
echo hr;
echo '<a href="disk.php?cmd=info&amp;dir='.$__url.'&amp;do=unzip&amp;id='.$id.$h.'">返回目录</a>';
$browser->template_foot();
