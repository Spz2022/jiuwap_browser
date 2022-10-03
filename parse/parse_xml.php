<?php
/*
 *
 *	浏览器->解析XML
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

!defined('m') && header('location: /?r='.rand(0,999));

$html = ($http->response());//http_fixhtml
if ( !$html ){
	http_error($url,0,'网页内容为空白！');
}

//原始网页大小
$html_size_old = strlen($html);
$html_title = false;

$html = str_ireplace('@','&at;at;',$html);
$html = str_replace(array("\n","\r"),'',$html);
$html = preg_replace('@<!--(.*?)-->@','', $html);
$html = str_replace("\t",' ', $html);

//得到编码和网页类型
if ( stripos($html,'<wml>') ){
	$code = 'utf-8';
	$mime = 'text/vnd.wap.wml';
}

include DIR. 'parse/ad/init_ad.php';

if ( stripos($html,'<noscript')!==false  && stripos($html,'</noscript>')!==false  ){
	$html = preg_replace('@<noscript(.*?)<\/noscript>@i','', $html);
}
if ( stripos($html,'<script')!==false && stripos($html,'</script>')!==false  ){
	$html = preg_replace('@<script(.*?)<\/script>@ies',"script_check_jump('\\1')", $html);
}

if ( stripos($html,'<embed')!==false  && stripos($html,'</embed>')!==false  ){
	$html = preg_replace('@<embed(.*?)<\/embed>@i','', $html);
}

if ( stripos($html,'<button')!==false  && stripos($html,'</button>')!==false  ){
	$html = preg_replace('@<button(.*?)>(.*?)<\/button>@i','<button$1 name_value="$2">', $html);
}

if ( stripos($html,'<style')!==false  && stripos($html,'style>')!==false  ){
	$html = preg_replace('@<style(.*?)style>@ies',"'<style'.fix_css('\\1').'style>'", $html);
}

if ( !isset($code) ){
	$code = GetCode($html);
}
if ( !isset($mime) ){
	$mime = GetMime($html);
}
if ( $browser->wap2wml==2 && $mime == 'text/vnd.wap.wml' ){
	$html = str_ireplace('</head>','', $html);
	$html = str_ireplace('<head>','', $html);
}elseif ( $browser->wap2wml==3 && $mime <> 'text/vnd.wap.wml' ){
	$html = preg_replace('@<textarea(.*?)>(.*?)<\/textarea>@i','<textarea$1 value="$2">', $html);
	$html = preg_replace('@<style(.*?)<\/style>@i','', $html);
	if ( stripos($html,'<body') && stripos($html,'</body>')  ){
		$html = preg_replace('@<body(.*?)>@i','<p>', $html);
		$html = str_ireplace('</body>','</p>', $html);
	}
	$html = preg_replace('@<p(.*?)>@i','', $html);
	$html = str_ireplace('</p>','<br/>', $html);
}

$html = preg_replace('@<([!a-zA-Z]{1,9}[1-5]{0,1}) (.*?)>@ies', "check_xml('\\1','\\2')", $html);

$browser->cacheurl_set();

$html = preg_replace('@<([/a-zA-Z1-5]{1,9}[1-5]{0,1})>@ies', "check_xml('\\1','\\2')", $html);

if ( $browser->wap2wml==2 && $mime == 'text/vnd.wap.wml' ){
	//处理wml的表单转换成form
	$html = preg_replace('@<select(.*?)>(.*?)</select>@ies', "fix_wml_form_select('\\1','\\2')", $html);
	$html = preg_replace('@<(postfield|anchor|go|/postfield|/anchor|/go)(.*?)>@ies', "fix_wml_form('\\1','\\2')", $html);
	if ( isset($wml2web_card_ontimer) ){
		$html = str_replace('[#WML2WEB_JUMP_ONTIMER#]',$wml2web_card_ontimer, $html);
	}

}elseif ( $browser->wap2wml==3 || $mime == 'text/vnd.wap.wml' ){
	$html = str_replace('&nbsp;',' ', $html);
	$html = str_replace('&ldquo;','“', $html);
	$html = str_replace('&rdquo;','”', $html);
	$html = str_replace('&darr;','↓', $html);
	$html = str_replace('&uarr;','↑', $html);
	$html = str_replace('&copy;','©', $html);
	$html = str_replace('&raquo;','»', $html);
}

while( strpos($html,' >') ){
   $html = str_replace(' >','>', $html);
}
while( strpos($html,'> ') ){
   $html = str_replace('> ','>', $html);
}
while( strpos($html,'< ') ){
   $html = str_replace('< ','<', $html);
}
while( strpos($html,' <') ){
   $html = str_replace(' <','<', $html);
}
while( strpos($html,'<<') ){
   $html = str_replace('<<','<', $html);
}
while( strpos($html,'>>') ){
   $html = str_replace('>>','>', $html);
}

while( strpos($html,'  ') ){
   $html = str_replace('  ',' ', $html);
}

$html = str_ireplace('<br/></p>','</p>', $html);
$html = str_ireplace('</p><br/>','</p>', $html);

while( strpos($html,'<br/><br/>')){
   $html = str_ireplace('<br/><br/>','<br/>', $html);
}

if ( $html_title=='' ){
	$html_title = str_pos($html,'<title>','</title>');
}

$html = str_replace('</>','</a>',$html);
if ( $browser->wap2wml==3 && $mime <> 'text/vnd.wap.wml' ){
	$html = preg_replace('@<title(.*?)<\/title>@i','', $html);
	if ( isset($_html2wmp_jump)){
		$html = str_ireplace('</head>','</head><card title="'.$html_title.'" ontimer="'.$_html2wmp_jump['url'].'"><timer value="'.$_html2wmp_jump['time'].'"/>', $html);
	}else{
		$html = str_ireplace('</head>','</head><card title="'.$html_title.'">',$html);
	}
	$mime = 'text/vnd.wap.wml' ;
	if ( stripos($html,'<wml>') === false ){
		$html ='<wml>'.$html;
	}
	if ( stripos($html,'<card') === false  ){
		if ( isset($_html2wmp_jump)){
			$html = str_ireplace('<wml>','<wml><card title="" ontimer="'.$_html2wmp_jump['url'].'"><timer value="'.$_html2wmp_jump['time'].'"/>', $html);
		}else{
			$html = str_ireplace('<wml>','<wml><card title="">', $html);
		}
	}
	if ( stripos($html,'<!DOCTYPE') === false  ){
		$html ='<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">'.$html;
	}
	if ( stripos($html,'<?xml') === false  ){
		$html = '<?xml version="1.0"?>'.$html;
	}
	if ( stripos($html,'</card>') === false  ){
		$html .='</card>';
	}
	if ( stripos($html,'</wml>') === false  ){
		$html .='</wml>';
	}
	$html = str_replace('&amp;','&', $html);
	$html = str_replace('&','&amp;', $html);
	$html = str_replace('&amp;gt;','&gt;',$html);
	$html = str_replace('&amp;lt;','&lt;',$html);
	$html = str_replace('&amp;quot;','&quot;',$html);
	$html = preg_replace('/&amp;#([0-9]{1,5});/i','&#$1;', $html);
}elseif ( $browser->wap2wml==2 && $mime == 'text/vnd.wap.wml' ){
	$mime = 'application/vnd.wap.xhtml+xml';
}

$html = str_replace('<head></head>','',$html);

$html = str_ireplace('&at;at;','@',$html);

if ( $code<>'utf-8'){
	if ( $html_title<>'' ){
		@$html_title = iconv($code,'utf-8//TRANSLIT', $html_title);
	}
}elseif ( strpos($html,'&#')!==false && strpos($html,';')!==false && $code == 'utf-8'){
    $html = unicode2utf8($html);
    $html_title = unicode2utf8($html_title);
}
if ( stripos($html,'<wml>')===false && stripos($html,'<html>')===false && stripos($html,'<card>')===false && stripos($html,'<head>')===false ){
    $html = $browser->template_top('无标题','',true,$code).$html.$browser->template_foot(false,true,$code);
}

$bottom_str = '';

//最终网页大小
$html_size_new = strlen($html);

//历史记录
$the_history_key = $browser->history_add($html_title,$url,$html,$mime,$code,$html_size_old-$html_size_new);

include DIR. 'parse/parse_foot.php';