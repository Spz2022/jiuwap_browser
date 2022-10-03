<?php
!defined('m') && header('location: /?r='.rand(0,999));

echo '目录:/'.$_url.'<br/>';
if ( $_url == '' || !is_dir($__turl) ){
	echo '目录不存在！';
	echo hr;
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回目录</a>';
	$browser->template_foot();

}

if ( isset($_GET['yes']) ){
	$dirname = isset($_POST['dirname']) ? ubb_copy(strtolower(trim(fix_r_n_t($_POST['dirname'])))) : null;
	if ( $dirname === null || $dirname == '' || strpos($dirname,'..')!==false || strpos($dirname,'/')!==false || strpos($dirname,'\\')!==false || strlen($dirname)>612){
		echo '请正确输入文件夹名！';
	}else{
		$newname = $__tup.'/'.FixSysUrlCode($dirname);
		if ( !file_exists($newname) && @rename($__turl,$newname) ){
			echo '文件夹重命名成功<br/>';
			if ( $__up == ''){
				$__url = urlencode($dirname);
			}else{
				$__url =$__up.'%2F'.urlencode($dirname);
			}
		}else{
			echo '文件夹重命名失败，可能存在同名文件(夹)！<br/>';
		}
	}

}else{
	if ( $browser->template == 1 ){
		echo '
		重命名：<input name="dirname'.$browser->rand.'" type="text" value="'.$_name.'"/><br/>
		<anchor>
		<go href="disk.php?cmd=info&amp;dir='.$__url.'&amp;do=unzip&amp;act=rename&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="dirname" value="$(dirname'.$browser->rand.')" />
		</go>确认</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;dir='.$__url.'&amp;act=rename&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		重命名：<input name="dirname" type="text" value="'.$_name.'"/><br/>
		<input type="submit" value="确认"/></form>';
	}
}


echo hr;
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回目录</a>';
$browser->template_foot();
