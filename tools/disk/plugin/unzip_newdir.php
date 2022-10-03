<?php
!defined('m') && header('location: /?r='.rand(0,999));

echo '目录:/'.$_url.'<br/>';
if ( isset($_GET['yes']) ){
	$dirname = isset($_POST['dirname']) ? ubb_copy(strtolower(trim(fix_r_n_t($_POST['dirname'])))) : null;
	if ( $dirname === null || $dirname == '' || strpos($dirname,'/')!==false || strpos($dirname,'\\')!==false || strlen($dirname)>612){
		echo '请正确输入文件夹名！';
	}else{
		$new_file = $__turl.'/'.FixSysUrlCode($dirname);
		if ( !file_exists($new_file) && @mkdir($new_file) ){
			echo '新建文件夹成功！<br/>';
		}else{
			echo '新建文件夹失败，可能存在同名文件(夹)！<br/>';
		}
		if ( $__url != '' ){
			$__url .= '%2F';
		}
		echo '查看[<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.urlencode($dirname).'&amp;id='.$id.$h.'">'.$dirname.'</a>]';
	}

}else{
	if ( $browser->template == 1 ){
		echo '
		文件夹名：<input name="dirname'.$browser->rand.'" type="text" value=""/><br/>
		<anchor>
		<go href="disk.php?cmd=info&amp;dir='.$__url.'&amp;do=unzip&amp;act=newdir&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="dirname" value="$(dirname'.$browser->rand.')" />
		</go>添加</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;dir='.$__url.'&amp;act=newdir&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		文件夹名：<input name="dirname" type="text" value=""/><br/>
		<input type="submit" value="添加"/></form>';
	}
}


echo hr;
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回目录</a>';
$browser->template_foot();
