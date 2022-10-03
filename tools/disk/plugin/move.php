<?php
!defined('m') && header('location: /?r='.rand(0,999));

if ( isset($_GET['yes'])){
	$oid = isset($_POST['oid']) ? (float)($_POST['oid']) : 0;
	if ( $oid == $dir['oid'] ){
		echo '移动完成。';
	}elseif ( $oid <>0 && !$browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE id='.$oid) ){
		echo '移动失败，目标目录不存在！';
	}else{
		$dirs = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$oid.' AND title="'.$dir['title'].'" AND uid='.$disk['id']);
		$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$oid.' AND title="'.$dir['title'].'" AND uid='.$disk['id']);
		if ( $dirs || $file ){
			$error = '移动失败，目标已存在同名文件(夹)！';
		}else{
			if ($browser->db->query('UPDATE `disk_file` SET oid="'.$oid.'" WHERE id='.$id)){
				echo '移动完成。';
			}else{
				echo '移动失败，原因未知！';
			}
		}
	}
	echo '<br/><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';

}else{
	if ( $browser->template == 1 ){
		echo '
		移动到：<select name="oid'.$browser->rand.'">
			<option value="0">根目录(root)</option>
			'.tree_select($dir['oid']).'
		</select><br />
		<anchor>
		<go href="disk.php?cmd=info&amp;do=move&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="oid" value="$(oid'.$browser->rand.')" />
		</go>移动</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;do=move&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		移动到：<select name="oid">
			<option value="0">根目录(root)</option>
			'.tree_select($dir['oid']).'
		</select><br />
		<input type="submit" value="移动"/><br />
		</form>';
	}
}
