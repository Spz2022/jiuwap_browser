<?php
!defined('m') && header('location: /?r='.rand(0,999));

$do = isset($_GET['do']) ? $_GET['do'] : '';
$id = isset($_GET['id']) ? (float)$_GET['id'] : 0;
$the_title = '';

$browser->template_top('网盘');

if ( $id<>0 ){
	$dir = $browser->db->fetch_first('SELECT title,oid FROM `disk_dir` WHERE uid='.$disk['id'].' AND id='.$id);
	if ( !$dir ){
		echo '错误：文件夹不存在！<br/>';
		echo '<a href="disk.php?id=0'.$h.'">返回根目录</a>';
		$browser->template_foot();
	}
	$the_title = urltree($dir['oid']).$dir['title'].'\\';
}
if ( $id == 0 && $do == ''){
	echo $b_set['disktitle'].'<br />';
	echo '已用'.bitsize($disk['space_use']).'/剩余'.bitsize($disk['space_all']-$disk['space_use']).hr;
}
if ( $id == 0 ){
	echo 'root:\\'.$the_title;
}else{
	echo '<a href="disk.php?id=0'.$h.'">root</a>:\\'.$the_title;
}
echo hr;

if ( $do == 'delete'){
	echo '删除操作是不可逆的，确认删除本目录及本目录下所有子文件(夹)？<br/>';
	echo '<a href="disk.php?do=delete2&amp;id='.$id.$h.'">确认删除</a>';
}elseif ( $do == 'delete2'){
	deldirs($id);
	echo '删除成功！';
}elseif ( $do == 'rename2'){
	$title = isset($_POST['title']) ? strtolower($_POST['title']) : $the_title;
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
			$browser->db->query('UPDATE `disk_dir` SET title="'.$title.'" WHERE id='.$id);
		}
	}
	if ( $error ){
		echo '重命名失败：'.$error.'<br/>';
		echo '<a href="disk.php?do=rename&amp;id='.$id.$h.'">返回重命名</a><br/>';
	}else{
		echo '重命名['.$title.']成功！<br/>';
	}
	echo '<a href="disk.php?id='.$id.$h.'">返回文件夹</a>';
}elseif ( $do == 'rename'){
	if ( $browser->template == 1 ){
		echo '
		文件夹名：<input name="title'.$browser->rand.'" type="text" value="'.$dir['title'].'"/><br/>
		<anchor>
		<go href="disk.php?do=rename2&amp;id='.$id.$h.'" method="post">
		<postfield name="title" value="$(title'.$browser->rand.')" />
		</go>修改</anchor><br/>';
	}else{
		echo '<form action="disk.php?do=rename2&amp;id='.$id.$h.'" method="post">
		文件夹名：<input name="title" type="text" value="'.$dir['title'].'"/><br/>
		<input type="submit" value="修改"/></form><br/>';
	}
	echo '<a href="disk.php?id='.$id.$h.'">返回文件夹</a>';

}else{
	$dir_num=0;
	$file_num=0;
	$query = $browser->db->query('SELECT id,title FROM `disk_dir` WHERE uid='.$disk['id'].' AND `oid`="'.$id.'" ORDER BY title ASC');
	while ( $dir = $browser->db->fetch_array($query) ){
		$dir_num++;
		echo '<img src="'.getico().'" alt="dir"/><a href="disk.php?id='.$dir['id'].$h.'">'.$dir['title'].'</a><br/>';
	}

	$query = $browser->db->query('SELECT id,title,mime FROM `disk_file` WHERE uid='.$disk['id'].' AND `oid`="'.$id.'" ORDER BY title ASC');
	while ( $file = $browser->db->fetch_array($query) ){
		$file_num++;
		echo '<img src="'.getico($file['mime']).'" alt="file"/><a href="disk.php?cmd=info&amp;id='.$file['id'].$h.'">'.$file['title'].'</a><br/>';
	}

	if ( $dir_num == 0 && $file_num == 0 ){
		echo '没有文件';
	}
	echo hr;
	if ( $id<>0 ){
		echo '<a href="disk.php?do=delete&amp;id='.$id.$h.'">删除目录</a><br/>';
		echo '<a href="disk.php?do=rename&amp;id='.$id.$h.'">目录改名</a><br/>';
	}
	echo '<a href="disk.php?cmd=newdir&amp;uid='.$id.$h.'">新建目录</a><br/>
	<a href="disk.php?cmd=newtxt&amp;uid='.$id.$h.'">新建文本</a><br/>
	<a href="disk.php?cmd=newzip&amp;uid='.$id.$h.'">新建压缩包</a><br/>
	<a href="disk.php?cmd=upload&amp;uid='.$id.$h.'">上传文件</a><br/>';
	if ( $h ){
		echo '<a href="/?m='.$u.'">返回浏览器</a><br/>';
	}else{
		echo '<a href="/">返回浏览器</a><br/>';
	}
}


$browser->template_foot();