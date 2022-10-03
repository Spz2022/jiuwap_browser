<?php
/*
 *
 *
 *
 *	2011-3-11 @ jiuwap.cn
 *
 */

!defined('m') && header('location: /?r='.rand(0,999));

include_once DIR.'tools/disk/inc.php';

$a = strpos($id,'|');
if ( !$a ){
	error_show('文件信息丢失');
}

$file = substr($id,$a+1);
$id = substr($id,0,$a);

$content = $browser->db->fetch_first('SELECT * FROM `disk_file` WHERE id='.$id);
if ( !$content ){
	error_show('文件不存在(1)');
}

$_dir = $b_set['dftemp'].md5($id.'_u').'/'.$file;
@$_dir = iconv('UTF-8',SYSFILECODE, $_dir);
if ( file_exists($_dir) ){
    @ob_end_clean();
    @ob_start();
    @set_time_limit(0);
	$content['title'] = basename($file);
	$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	$encoded_filename = urlencode($content['title']);
	$encoded_filename = str_replace('+', '%20', $encoded_filename);

	if (preg_match("/MSIE/", $ua)) {
		header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
	}elseif (preg_match("/Firefox/", $ua)) {
		header('Content-Disposition: attachment; filename*="utf8\'\'' . $content['title'] . '"');
	} else {
		header('Content-Disposition: attachment; filename="' . $content['title'] . '"');
	}
	header('Content-Encoding: none');
	Header('Content-Length: '.filesize($_dir));
	Header('Content-Type: '.get_file_mime(get_short_file_mime($_dir)));
	ob_flush();
	flush();
	if (!$fp = @fopen($_dir,'rb')){
		exit;
	}
	while(!feof($fp)){
		echo fread($fp,1024);
		ob_flush();
		flush();
	}
	fclose($fp);
	exit;
}else{
	error_show('文件不存在(2)');
}