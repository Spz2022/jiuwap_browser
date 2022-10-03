<?php
!defined('m') && header('location: /?r='.rand(0,999));


if ( isset($_GET['yes'])){
	$oid = isset($_POST['oid']) ? (float)($_POST['oid']) : 0;
	if ( $oid <>0 && !$browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE id='.$oid) ){
		echo '复制失败，目标目录不存在！';
	}else{
		$dirs = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$oid.' AND title="'.$dir['title'].'" AND uid='.$disk['id']);
		$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$oid.' AND title="'.$dir['title'].'" AND uid='.$disk['id']);
		if ( $dirs || $file ){
			$dir['title'] = rand(1,999999).'_'.$dir['title'];
		}
		$mime = get_short_file_mime($dir['title']);
		$the_save_file = time_().'_'.rand(10000,99999);
		if ( $mime <> ''){
			$the_save_file .= '_'.$mime;
		}
		copy($b_set['dfforever'].$dir['file'],$b_set['dfforever'].$the_save_file);
		$arr = array(
				'oid'	=>	$oid,
				'uid'	=>	$disk['id'],
				'mime'	=>	$mime,
				'title'	=>	$dir['title'],
				'size'	=>	$dir['size'],
				'file'	=>	$the_save_file,
				);
		if ( $id = $browser->db->insert('disk_file',$arr,true) ){
			echo '复制完成。<br/>';
			echo '查看<a href="disk.php?cmd=info&amp;id='.$id.$h.'">['.$dir['title'].']</a>';
		}else{
			echo '复制失败，原因未知！';
		}
	}
	echo '<br/><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';

}else{
	if ( $browser->template == 1 ){
		echo '
		复制到：<select name="oid'.$browser->rand.'">
			<option value="0">根目录(root)</option>
			'.tree_select($dir['oid']).'
		</select><br />
		<anchor>
		<go href="disk.php?cmd=info&amp;do=copy&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="oid" value="$(oid'.$browser->rand.')" />
		</go>复制</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;do=copy&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		复制到：<select name="oid">
			<option value="0">根目录(root)</option>
			'.tree_select($dir['oid']).'
		</select><br />
		<input type="submit" value="复制"/><br />
		</form>';
	}
}
