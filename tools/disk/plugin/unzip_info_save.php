<?php
!defined('m') && header('location: /?r='.rand(0,999));
$size = filesize($__turl);
echo '大小：'.bitsize($size).'<br />';
echo '类型：'.get_file_mime($mime).'<br />';
if ( isset($_GET['yes']) ){
	$up_id = isset($_POST['oid']) ? (float)$_POST['oid'] : 0;
	$dir = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE id='.$up_id.' AND uid='.$disk['id']);
	if ( $up_id<0 && !$dir ){
		echo '网盘目录不存在<br/>';
		echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;go=save&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回上级</a><br/>';
	}elseif ( $disk['space_all']-$disk['space_use']-$size <= 0){
		echo '网盘空间已满';
	}else{
		$dir = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$up_id.' AND title="'.$_name.'" AND uid='.$disk['id']);
		$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$up_id.' AND title="'.$_name.'" AND uid='.$disk['id']);
		if ( $dir || $file || $_name==''){
			$_name = rand(1,999999).'_'.$_name;
		}
		$the_save_file = time_().'_'.rand(10000,99999);
		if ( $mime <> ''){
			$the_save_file .= '_'.$mime;
		}
		copy($__file,FixSysUrlCode($b_set['dfforever'].$the_save_file));
		$arr = array(
				'oid'	=>	$up_id,
				'uid'	=>	$disk['id'],
				'mime'	=>	$mime,
				'title'	=>	$_name,
				'size'	=>	$size,
				'file'	=>	$the_save_file,
				);
		$ida = $browser->db->insert('disk_file',$arr,true);
		if ( $ida ){
			$browser->db->query('UPDATE `disk_config` SET space_use=space_use+'.$size.' WHERE id='.$disk['id']);
			echo '上传文件成功！<br/>查看<a href="disk.php?cmd=info&amp;id='.$ida.'&amp;uid='.$up_id.$h.'">['.$_name.']</a><br/>';
		}else{
			echo '未知错误<br/>';
		}
	}
}else{
	if ( $browser->template == 1 ){
		echo '目录：<select name="oid"><option value="0" selected="selected">根目录(root)</option>
		'.tree_select().'</select><br />
		<anchor>
		<go href="disk.php?cmd=info&amp;do=unzip&amp;go=save&amp;yes=yes&amp;dir='.$__url.'&amp;id='.$id.$h.'" method="post">
		<postfield name="oid" value="$(oid'.$browser->rand.')" />
		</go>存至网盘</anchor><br/>';
	}else{
		echo '<form action="disk.php?cmd=info&amp;do=unzip&amp;go=save&amp;yes=yes&amp;dir='.$__url.'&amp;id='.$id.$h.'" method="post">
		目录：<select name="oid">
		<option value="0" selected="selected">根目录(root)</option>
		'.tree_select().'
		</select><br />
		<input type="submit" value="存至网盘"/><br />
		</form>';
	}
}
