<?php
!defined('m') && header('location: /?r='.rand(0,999));
if (!defined('SYSFILECODE')){
	//设置服务器文件名编码,小写编码
	define('SYSFILECODE','gb2312');
}

function FixSysUrlCode($url){
	if ( SYSFILECODE != 'utf-8' ){
		return @iconv('utf-8',SYSFILECODE, $url);
	}else{
		return $url;
	}
}

function FixPhpUrlCode($url){
	if ( SYSFILECODE != 'utf-8' ){
		return @iconv( SYSFILECODE,'utf-8', $url);
	}else{
		return $url;
	}
}

function ListDirFiles($dir){
	//返回数组,0文件个数,1文件夹个数,2总大小,3文件列表
	$arr = array(0,0,0,array());
	if ( !file_exists($dir) || !is_dir($dir) ){
		return $arr;
	}
	$opendir = opendir($dir);
	while( $file = readdir($opendir) ){
		if ( $file == '.' || $file == '..'){
			continue;
		}
		$file = $dir . '/' . $file;
		$file = str_replace('//','/',$file);
		if ( is_dir($file) ){
			$temp = ListDirFiles($file);
			if ( array(0,0,0,array()) == $temp ){
				$arr[0] ++;
				$arr[3][] = $file;
			}else{
				$arr[0] += $temp[0]+1;
				$arr[1] += $temp[1];
				$arr[2] += $temp[2];
				$arr[3] = array_merge($temp[3],$arr[3]);
			}
		}else{
			$arr[1]++;
			$arr[2] += filesize($file);
			$arr[3][] = $file;
		}
	}
	return $arr;

}

function checktruestr($str){
	if ( substr($str,0,1) == '.' || substr($str,strlen($str)-1) == '.' ){
		return false;
	}elseif (preg_match("/[',:;*?~`!^<>]|\]|\[|\/|\\\|\"|\|/", $str)) {
		return false;
	}else{
		return true;
	}
}

function getico($str = false){
	$where = 'tools/disk/image/';
	$mime = array(
		'3gp'	=>	'3gp.png',
		'avi'	=>	'avi.png',
		'bmp'	=>	'bmp.png',
		'css'	=>	'css.png',
		'doc'	=>	'doc.png',
		'exe'	=>	'exe.png',
		'fla'	=>	'fla.png',
		'gif'	=>	'gif.png',
		'htaccess'	=>	'htaccess.png',
		'html'	=>	'htm.png',
		'xhtml'	=>	'htm.png',
		'wml'	=>	'wml.png',
		'htm'	=>	'htm.png',
		'jad'	=>	'jad.png',
		'jar'	=>	'jar.png',
		'jpg'	=>	'jpg.png',
		'jpeg'	=>	'jpg.png',
		'e'		=>	'log.png',
		'log'	=>	'log.png',
		'mdb'	=>	'mdb.png',
		'mid'	=>	'mid.png',
		'mp3'	=>	'mp3.png',
		'php'	=>	'php.png',
		'png'	=>	'png.png',
		'ppt'	=>	'ppt.png',
		'pdf'	=>	'pdf.png',
		'psd'	=>	'psd.png',
		'py'	=>	'py.png',
		'pyd'	=>	'py.png',
		'rar'	=>	'rar.png',
		'rtf'	=>	'rtf.png',
		'sql'	=>	'sql.png',
		'swf'	=>	'swf.png',
		'ttf'	=>	'ttf.png',
		'txt'	=>	'txt.png',
		'wav'	=>	'wav.png',
		'wmv'	=>	'wmv.png',
		'mp4'	=>	'rmvb.png',
		'xls'	=>	'xls.png',
		'aspx'	=>	'xml.png',
		'asp'	=>	'xml.png',
		'asa'	=>	'xml.png',
		'xml'	=>	'xml.png',
		'zip'	=>	'zip.png',
		'ini'	=>	'css.png',
		'inc'	=>	'css.png',
		'mrp'	=>	'zip.png',
		'torrent'=>	'torrent.png',
		'rm'	=>	'rmvb.png',
		'rmvb'	=>	'rmvb.png',
		'reg'	=>	'ini.png',
		'mf'	=>	'ini.png',
		'3gp'	=>	'3gp.png',
		'7z'	=>	'zip.png',
		'gz'	=>	'zip.png',
		'tar'	=>	'zip.png',
	);
	$sys = array('cldir.png','unkn.png','opdir.png');

	if ( $str === false ){
		return $where.$sys[0];
	}elseif ( isset($mime[$str]) ) {
		return $where.$mime[$str];
	}else{
		return $where.$sys[1];
	}
}

function urltree($id=0){
	global $browser,$disk,$h;
	$return = '';
	$dir = $browser->db->fetch_first('SELECT title,oid FROM `disk_dir` WHERE uid='.$disk['id'].' AND id='.$id);
	if ( $dir ){
		if ( $dir['oid'] ){
			$return .= urltree($dir['oid']);
		}
		$return .= '<a href="disk.php?id='.$id.$h.'">'.$dir['title'].'</a>\\';
	}
	return $return;
}

function id2password($id = 0,$code = ''){
	if ( $code == ''){
		global $b_set;
		$code = $b_set['key1'];
	}
	$cry = new funCrypt($code);
	$str = $id.','.(time_() + 600);
	return $cry->enCrypt($str);
}

function password2id($str,$code = ''){
	if ( $code == ''){
		global $b_set;
		$code = $b_set['key1'];
	}
	$cry = new funCrypt($code);
	$str = $cry->deCrypt($str);
	$c = strpos($str,',');
	if ( $c !== false ){
		$id = substr($str,0,$c);
		$time = (int)substr($str,$c+1);
		if ( $time < time_()){
			return false;
		}else{
			return $id;
		}
	}else{
		return false;
	}
}

function deldirs($id=0){
	global $browser,$disk,$b_set;
	$query = $browser->db->query('SELECT id FROM `disk_dir` WHERE oid='.$id);
	while ( $dir = $browser->db->fetch_array($query) ){
		$qu = $browser->db->query('SELECT id,mime,file,size FROM `disk_file` WHERE oid='.$dir['id']);
		$size = 0;
		while ( $file = $browser->db->fetch_array($qu) ){
            if ( $file['mime'] == 'zip' || $dir['mime'] == 'jar'){
                deldir($b_set['dftemp'].md5($file['id'].'_u'));
            }
			@unlink($b_set['dfforever'].$file['file']);
			$size += $file['size'];
		}
		if ( $size ){
			$browser->db->query('UPDATE `disk_config` SET space_use=space_use-'.$size.' WHERE id='.$disk['id']);
		}
		$browser->db->delete('disk_file','oid='.$dir['id']);
		if ( $dir['id'] <> $id){
			deldirs($dir['id']);
		}
	}
	$browser->db->delete('disk_dir','oid='.$id);

	$query = $browser->db->query('SELECT id FROM `disk_dir` WHERE id='.$id);
	while ( $dir = $browser->db->fetch_array($query) ){
		$qu = $browser->db->query('SELECT id,mime,file,size FROM `disk_file` WHERE oid='.$dir['id']);
		$size = 0;
		while ( $file = $browser->db->fetch_array($qu) ){
            if ( $file['mime'] == 'zip' || $file['mime'] == 'jar'){
                deldir($b_set['dftemp'].md5($file['id'].'_u'));
            }
			@unlink($b_set['dfforever'].$file['file']);
			$size += $file['size'];
		}
		if ( $size ){
			$browser->db->query('UPDATE `disk_config` SET space_use=space_use-'.$size.' WHERE id='.$disk['id']);
		}
		$browser->db->delete('disk_file','oid='.$dir['id']);
		$browser->db->delete('disk_dir','id='.$dir['id']);
		if ( $dir['id'] <> $id){
			deldirs($dir['id']);
		}
	}
}


function tree_select($select = false,$id = 0 ,$tree='└'){
	global $browser,$disk;
	!isset($disk) && init_disk();
	$query = $browser->db->query('SELECT id,title FROM `disk_dir` WHERE oid='.$id.' AND uid='.$disk['id']);
	$return = '';
	while ( $dir = $browser->db->fetch_array($query) ){
		if ( $select !== false && $select == $dir['id'] ) {
			$return .= '<option value="'.$dir['id'].'" selected="selected">'.$tree.$dir['title'].'</option>';
		}else{
			$return .= '<option value="'.$dir['id'].'">'.$tree.$dir['title'].'</option>';
		}
		$return .= tree_select($select,$dir['id'],'&nbsp;'.$tree);
	}
	return $return;
}

function init_disk(){
	global $browser,$disk,$b_set;
	$disk = $browser->db->fetch_first('SELECT id,space_all,space_use FROM `disk_config` WHERE username="my" AND password="'.$browser->uid.'"');
	if ( !$disk ){
		$disk = array();
		$disk['username'] = 'my';
		$disk['password'] = $browser->uid;
		$disk['space_all'] = $b_set['dinit'];
		$disk['space_use'] = 0;
		$disk['id'] = $browser->db->insert('disk_config',$disk,true);
	}

}

function get_short_file_mime($title,$mime=''){
	$a = strrpos($title,'.');
	if ( $a !== false ){
		if ( strlen($title) - $a <= 15){
			$mime = substr($title,$a+1);
		}
	}
	return strtolower($mime);
}

function mime_istxt($mime){
	$arr = array('stx','reg','py','mf','ppu','hpp','sql','rc','config','manifest','xaml','csproj','vbproj','java','cgi','pm','pl','xsl','xsd','c','js','jsp','h','cs','pm','vb','vbs','cpp','txt','php','ini','xhtml','css','html','htm','asp','asa','aspx','asax','shtml','stm','hta','dhtml','hts','hdm','hdml','jad','wml','wmlc','wmls','wmlsc','wmlscript','ws','xml');
	return in_array($mime,$arr);
}

function mime_ispic($mime){
	$arr = array('jpg','jpeg','gif','bmp','png');
	return in_array($mime,$arr);
}

function fixpicsize(&$height,&$width,$N_height,$N_width){
	if ( $height > $N_height ){
		$i = $height / $N_height;
		$width = $width / $i;
		$height = $N_height;
	}
	if ( $width > $N_width ){
		$i = $width / $N_width;
		$height = $height / $i;
		$width = $N_width;
	}
}
