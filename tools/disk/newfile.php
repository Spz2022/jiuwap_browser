<?php
!defined('m') && header('location: /?r='.rand(0,999));
$up_title = '根目录';
$up_id = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
$title = isset($_POST['title']) ? FixCopyUBB(strtolower(trim(fix_r_n_t($_POST['title'])))) : '';
$code = isset($_POST['code']) ? $_POST['code'] : 0;
$content = isset($_POST['content']) ? FixCopyUBB($_POST['content']) : '';
$browser->template_top('新建文件');
$the_title = '';
if ( $up_id<>0 ){
	$dir = $browser->db->fetch_first('SELECT title,oid FROM `disk_dir` WHERE uid='.$disk['id'].' AND id='.$up_id);
	if ( !$dir ){
		echo '错误：文件夹不存在！';
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
		echo '错误：文件名不得为空！';
	}elseif ( strlen($title) > 50 ){
		echo '错误：文件名不得多于50个字符！';
	}elseif( checktruestr($title) == false ){
		echo '错误：文件名不得使用标点符号！';
	}else{
		$dir = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$up_id.' AND title="'.$title.'" AND uid='.$disk['id']);
		$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$up_id.' AND title="'.$title.'" AND uid='.$disk['id']);
		if ( $dir || $file ){
			echo '错误：文件(夹)已经存在！';
		}else{
			$the_save_file = time_().'_'.rand(10000,99999).'_txt';
			if ( $code == 1){
				$content = @iconv('utf-8','gb2312//TRANSLIT', $content);
			}
			$mime = get_short_file_mime($title);
			$arr = array(
				'oid'			=>	$up_id,
				'uid'			=>	$disk['id'],
				'mime'			=>	$mime,
				'title'			=>	$title,
				'size'			=>	strlen($content),
				'file'			=>	$the_save_file,
			);
			if ( $disk['space_all']-$disk['space_use']-$arr['size'] <= 0){
				echo '错误：空间已满！';
			}elseif ( $arr['size'] > $b_set['dlocal']) {
				echo '错误：文件不得大于'.bitsize($b_set['dlocal']).'！';
			}else{
				writefile($b_set['dfforever'].$the_save_file,$content);
				$id = $browser->db->insert('disk_file',$arr,true);
				if ( $id ){
					$browser->db->query('UPDATE `disk_config` SET space_use=space_use+'.$arr['size'].' WHERE id='.$disk['id']);
					echo '新建文件成功！<br/>查看<a href="disk.php?cmd=info&amp;id='.$id.'&amp;uid='.$up_id.$h.'">['.$title.']</a><br/>';
					$browser->template_foot();
				}else{
					echo '错误：失败，未知错误。';
				}
			}
		}
	}

	echo hr();
}else{
	echo '新建文件：<br/>';
}

if ( $title == ''){
	$title = '文本文档.txt';
}

if ( $browser->template == 1 ){
	echo '
	名称：<input name="title'.$browser->rand.'" type="text" value="'.$title.'"/><br/>
	内容：<input name="content'.$browser->rand.'" type="text" value=""/><br/>
	编码：<select name="code'.$browser->rand.'">
		<option value="1">GB2312</option>
		<option value="0" selected="selected">UTF-8</option>
	</select><br />
	<anchor>
	<go href="disk.php?cmd=newfile&amp;yes=yes&amp;uid='.$up_id.$h.'" method="post">
	<postfield name="title" value="$(title'.$browser->rand.')" />
	<postfield name="code" value="$(code'.$browser->rand.')" />
	<postfield name="content" value="$(content'.$browser->rand.')" />
	</go>新建</anchor><br/>
	';
}else{
	echo '<form action="disk.php?cmd=newfile&amp;yes=yes&amp;uid='.$up_id.$h.'" method="post">
	名称：<input name="title" type="text" value="'.$title.'"/><br/>
	内容：<input name="content" type="text" value=""/><br/>
	编码：<select name="code">
		<option value="1">GB2312</option>
		<option value="0" selected="selected">UTF-8</option>
	</select><br />
	<input type="submit" value="新建"/><br />
	</form>';
}
echo '提示:可以使用剪切板粘贴内容。';
$browser->template_foot();
