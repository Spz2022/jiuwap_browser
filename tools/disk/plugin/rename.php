<?php
!defined('m') && header('location: /?r='.rand(0,999));
if ( isset($_GET['yes'])){
	$title = isset($_POST['title']) ? strtolower($_POST['title']) : '';
	$error = false;
	if ( $title == '' ){
		$error = '文件名不得为空！';
	}elseif ( strlen($title) > 50 ){
		$error = '文件名不得多于50个字符！';
	}elseif( checktruestr($title) == false ){
		$error = '文件名不得使用标点符号！';
	}else{
		$dirs = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$dir['oid'].' AND title="'.$title.'" AND uid='.$disk['id'].' AND id<>'.$id);
		$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$dir['oid'].' AND title="'.$title.'" AND uid='.$disk['id'].' AND id<>'.$id);
		if ( $dirs || $file ){
			$error = '文件(夹)已经存在！';
		}else{
			$mime = get_short_file_mime($title);
			$browser->db->query('UPDATE `disk_file` SET title="'.$title.'",mime="'.$mime.'" WHERE id='.$id);
		}
	}
	if ( $error ){
		echo '重命名失败：'.$error.'<br/>';
		echo '<a href="disk.php?cmd=info&amp;do=rename&amp;id='.$id.$h.'">重命名</a><br/>';
	}else{
		echo '重命名['.$title.']成功！<br/>';
	}
	echo '<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
}else{
	if ( $browser->template == 1 ){
		echo '
		文件名：<input name="title'.$browser->rand.'" type="text" value="'.$dir['title'].'"/><br/>
		<anchor>
		<go href="disk.php?cmd=info&amp;do=rename&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="title" value="$(title'.$browser->rand.')" />
		</go>修改</anchor><br/>
		<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;do=rename&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		文件名：<input name="title" type="text" value="'.$dir['title'].'"/><br/>
		<input type="submit" value="修改"/>
		<br /><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>
		</form>';
	}

}