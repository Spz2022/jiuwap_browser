<?php
ob_start('ob_gzip');
$iswml = isset($_SERVER['HTTP_ACCEPT']) ? '.'.strtolower($_SERVER['HTTP_ACCEPT']) : false;
if ( $iswml && (
	strpos($iswml,'text/html') ||
	strpos($iswml,'application/xhtml+xml')
	)){
	$iswml= false;
	define('hr','<hr/>');
}else{
	$iswml = isset($_SERVER['HTTP_USER_AGENT']) ? '.'.$_SERVER['HTTP_USER_AGENT'] : false;
	if (
		$iswml && (
		stripos($iswml,'MSIE') ||
		stripos($iswml,'Windows') ||
		stripos($iswml,'Mozilla') ||
		stripos($iswml,'Symbian') ||
		stripos($iswml,'iPhone') ||
		stripos($iswml,'KHTML') ||
		stripos($iswml,'Chrome') ||
		stripos($iswml,'ucweb') ||
		stripos($iswml,'smartphone') ||
		stripos($iswml,'blackberry') ||
		stripos($iswml,'opera') ||
		stripos($iswml,'AppleWebKit')
		)){
		$iswml= false;
		define('hr','<hr/>');
	}else{
		$iswml= true;
		define('hr','<br/>------------<br/>');
	}
}


Function top_wap($title,$refreshurl=''){
	global $iswml;
	if ( $iswml ){
		if ($refreshurl){
			$refreshurl = '<card id="main" title="'.$title.'" ontimer="'.$refreshurl.'"><timer value="1"/>';
		}else{
			$refreshurl = '<card id="main" title="'.$title.'">';
		}
		header('Content-Type: text/vnd.wap.wml; charset=utf-8');
		echo '<?xml version="1.0" encoding="utf-8"?>
		<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
		<wml><head><meta http-equiv="Cache-Control" content="max-age=0"/>
		<meta http-equiv="Cache-Control" content="no-cache"/></head>
		'.$refreshurl.'
		<p>';
	}else{
		$refreshurl && $refreshurl = '<meta http-equiv="refresh" content="1;url='.$refreshurl.'"/>';
		echo '<?xml version="1.0" encoding="utf-8"?>'
		?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml;charset=utf-8"/>
<meta http-equiv="Cache-Control" content="must-revalidate,no-cache"/><?=$refreshurl?>
<title><?=$title?></title><style>body{font-size:14px;color:#000;font-family:Arial,Helvetica,sans-serif;}a{color:#039;text-decoration:none;}</style>
</head><body>
<?php
	}
}

Function foot_wap($exit=true){
	global $iswml;
	if ( $iswml ){
		echo '</p></card></wml>';
	}else{
		echo '</body></html>';
	}
	exit_fix_html($exit);
}

function exit_fix_html($exit=true){
	$str = ob_get_contents();;
	ob_clean();
	$str = str_replace(array("\n","\r","\t"),'',$str);
	$str = str_replace(' />','/>',$str);
	echo $str;
	if ( $exit){
		ob_end_flush();exit;
	}
}

function ob_gzip($content){
	if( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && !headers_sent() && extension_loaded('zlib') && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') ){
		$content = gzencode($content,9);
		header("Content-Encoding: gzip");
		header("Vary: Accept-Encoding");
		header("Content-Length: ".strlen($content));
	}
	return $content;
}

