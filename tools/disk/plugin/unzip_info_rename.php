<?php
!defined('m') && header('location: /?r='.rand(0,999));

if ( $_url == '' || !is_file($__turl) ){
	echo '文件不存在！';
	echo hr;
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回文件</a>';
	$browser->template_foot();
}

if ( isset($_GET['yes']) ){
	$filename = isset($_POST['filename']) ? ubb_copy(strtolower(trim(fix_r_n_t($_POST['filename'])))) : null;
	if ( $filename === null || $filename == '' || strpos($filename,'..')!==false || strpos($filename,'/')!==false || strpos($filename,'\\')!==false || strlen($filename)>612){
		echo '请正确输入文件夹名！';
	}else{
		$newname = $__tup.'/'.FixSysUrlCode($filename);
		if ( !file_exists($newname) && @rename($__turl,$newname) ){
			echo '文件重命名成功<br/>';
			if ( $__up == ''){
				$__url = urlencode($filename);
			}else{
				$__url =$__up.'%2F'.urlencode($filename);
			}

		}else{
			echo '文件重命名失败，可能存在同名文件(夹)！<br/>';
		}
	}

}else{
	if ( $browser->template == 1 ){
		echo '
		重命名：<input name="filename'.$browser->rand.'" type="text" value="'.$_name.'"/><br/>
		<anchor>
		<go href="disk.php?cmd=info&amp;dir='.$__url.'&amp;go=unzip&amp;act=rename&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="filename" value="$(filename'.$browser->rand.')" />
		</go>确认</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;dir='.$__url.'&amp;go=rename&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		重命名：<input name="filename" type="text" value="'.$_name.'"/><br/>
		<input type="submit" value="确认"/></form>';
	}
}


