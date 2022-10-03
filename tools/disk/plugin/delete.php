<?php
!defined('m') && header('location: /?r='.rand(0,999));
if ( !isset($_GET['yes']) ){
	echo '确认要删除吗？<br/>';
	echo '<a href="disk.php?cmd=info&amp;do=delete&amp;yes=yes&amp;id='.$id.$h.'">确认删除</a><br/>';
	echo '<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
}else{
    if ( $dir['mime'] == 'zip' || $dir['mime'] == 'jar' || $dir['mime'] == 'mrp'){
		$_dir = $b_set['dftemp'].md5($id.'_u');
        deldir($_dir);
        $dir['mime'] == 'mrp' && @unlink($_dir.'_list');
    }
	if ( !file_exists($b_set['dfforever'].$dir['file']) || @unlink($b_set['dfforever'].$dir['file']) ){
		$browser->db->query('UPDATE `disk_config` SET space_use=space_use-'.$dir['size'].' WHERE id='.$disk['id']);
		$browser->db->delete('disk_file','id='.$id,1);
		echo '删除成功！';
	}else{
		echo '删除失败！';
	}
}