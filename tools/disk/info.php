<?php
!defined('m') && header('location: /?r='.rand(0,999));
$do = isset($_GET['do']) ? strtolower(trim($_GET['do'])) : null;
$id = isset($_GET['id']) ? (float)$_GET['id'] : 0;

if ( $do == 'picread' && isset($_GET['yes']) ){
	define('no_ob_gzip','true');
}

$dir = $browser->db->fetch_first('SELECT * FROM `disk_file` WHERE uid='.$disk['id'].' AND id='.$id);
if ( !$dir ){
	$browser->template_top('网盘');
	echo '错误：文件不存在！<br/>';
	echo '<a href="disk.php?id=0'.$h.'">返回根目录</a>';
	$browser->template_foot();
}

$browser->template_top($dir['title']);
$the_title = urltree($dir['oid']);

echo '<a href="disk.php?id=0'.$h.'">root</a>:\\'.$the_title.'<br/>';
echo '<img src="'.getico($dir['mime']).'" alt="'.$dir['mime'].'"/>'.$dir['title'].'('.bitsize($dir['size']).')<br/>';

$plugin = array('move','copy','mrpchange','unzip','docread','docread','txtread','phpread','picread','delete','toemail','rename','down');
if ( in_array($do,$plugin) ){
	include 'plugin/'.$do.'.php';
}else{
	include 'plugin/default.php';
}

$browser->template_foot();