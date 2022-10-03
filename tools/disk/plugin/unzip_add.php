<?php
!defined('m') && header('location: /?r='.rand(0,999));

echo '目录:/'.$_url.'<br/>';
if ( isset($_GET['yes']) ){
	$filecode = isset($_POST['filecode']) ? strtolower(trim($_POST['filecode'])) : null;
	$filename = isset($_POST['filename']) ? ubb_copy(strtolower(trim(fix_r_n_t($_POST['filename'])))) : null;
	$filecode = (float)str_pos($filecode,'[disk=',']');
	if ( !$filecode || !($file = $browser->db->fetch_first('SELECT title,file FROM `disk_file` WHERE uid='.$disk['id'].' AND id='.$filecode)) ){
		echo '添加文件失败,[disk='.$filecode.']网盘文件不存在.';
	}else{
		if ( strpos($filename,'/')!==false || strpos($filename,'\\')!==false || strlen($filename)>612){
			echo '请正确输入文件名！';
		}else{
			if ( $filename === null || $filename == '' ){
				$filename = $file['title'];
			}
			$new_file = $__turl.'/'.FixSysUrlCode($filename);
			$isadd = !@unlink($new_file);
			@copy($b_set['dfforever'].$file['file'],$new_file);
			if ( $__url != '' ){
				$__url .= '%2F';
			}
			echo '文件'.($isadd ? '添加' : '替换' ).'成功<br/>
			查看[<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.urlencode($filename).'&amp;id='.$id.$h.'">'.$filename.'</a>]';
		}
	}

}else{
	if ( $browser->template == 1 ){
		echo '
		网盘代码：<input name="filecode'.$browser->rand.'" type="text" value="[disk=0]"/><br/>
		新的名字：<input name="filename'.$browser->rand.'" type="text" value=""/><br/>
		(提示:当新的名字为空时,则为网盘文件的名字;如果文件已经存在则替换文件)<br/>
		<anchor>
		<go href="disk.php?cmd=info&amp;dir='.$__url.'&amp;do=unzip&amp;act=add&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="filecode" value="$(filecode'.$browser->rand.')" />
		<postfield name="filename" value="$(filename'.$browser->rand.')" />
		</go>添加</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;dir='.$__url.'&amp;act=add&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		网盘代码：<input name="filecode" type="text" value="[disk=0]"/><br/>
		新的名字：<input name="filename" type="text" value=""/><br/>
		(提示:当新的名字为空时,则为网盘文件的名字;如果文件已经存在则替换文件)<br/>
		<input type="submit" value="添加"/></form>';
	}
}
echo hr;
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回目录</a>';
$browser->template_foot();
