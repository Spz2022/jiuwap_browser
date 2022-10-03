<?php
!defined('m') && header('location: /?r='.rand(0,999));
$up_title = '根目录';
$up_id = isset($_GET['uid']) ? (float)$_GET['uid'] : 0;
$title = isset($_POST['title']) ? ubb_copy(strtolower(trim(fix_r_n_t($_POST['title'])))) : '';
$browser->template_top('新建目录');
$the_title = '';
if ( $up_id<>0 ){
	$dir = $browser->db->fetch_first('SELECT title,oid FROM `disk_dir` WHERE uid='.$disk['id'].' AND id='.$up_id);
	if ( !$dir ){
		echo '错误：文件夹不存在！<br/>';
		echo '<a href="disk.php?id=0'.$h.'">返回根目录</a>';
		$browser->template_foot();
	}else{
		$up_title = $dir['title'];
		$the_title = urltree($dir['oid']).'<a href="disk.php?id='.$up_id.$h.'">'.$dir['title'].'</a>\\';
	}
}

echo '<a href="disk.php?id=0'.$h.'">root</a>:\\'.$the_title.'<br/>';

if ( isset($_GET['yes']) ){
	if ( $title == '' ){
		echo '错误：文件夹名不得为空！';
	}elseif ( strlen($title) > 50 ){
		echo '错误：文件夹名不得多于50个字符！';
	}elseif( checktruestr($title) == false ){
		echo '错误：文件夹名不得使用标点符号！';
	}elseif( substr_count($the_title,'\\') >=5 ){
		echo '错误：子目录最多五个！';
	}else{
		$dir = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$up_id.' AND title="'.$title.'" AND uid='.$disk['id']);
		$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$up_id.' AND title="'.$title.'" AND uid='.$disk['id']);
		if ( $dir || $file ){
			echo '错误：文件(夹)已经存在！';
		}else{
			$arr = array(
				'oid'	=>	$up_id,
				'uid'	=>	$disk['id'],
				'title'	=>	$title,
			);
			$id = $browser->db->insert('disk_dir',$arr,true);
			if ( $id ){
				echo '新建文件夹成功！<br/>
				进入<a href="disk.php?id='.$id.'&amp;uid='.$up_id.$h.'">['.$title.']</a>';
				echo hr.'<a href=disk.php?cmd=newdir&amp;uid='.$up_id.$h.'">继续新建目录</a>';
				$browser->template_foot();
			}else{
				echo '错误：失败，未知错误。';
			}
		}
	}

	echo hr;
}else{
	echo '新建文件夹：';
}
if ( $title == ''){
	$title = '新文件夹';
}
if ( $browser->template == 1 ){
	echo '
	名称：<input name="title'.$browser->rand.'" type="text" value="'.$title.'"/><br/>
	<anchor>
	<go href="disk.php?cmd=newdir&amp;yes=yes&amp;uid='.$up_id.$h.'" method="post">
	<postfield name="title" value="$(title'.$browser->rand.')" />
	</go>新建</anchor><br/>
	';
}else{
	echo '<form action="disk.php?cmd=newdir&amp;yes=yes&amp;uid='.$up_id.$h.'" method="post">
	名称：<input name="title" type="text" value="'.$title.'"/><br/>
	<input type="submit" value="新建"/><br />
	</form>';
}
$browser->template_foot();
