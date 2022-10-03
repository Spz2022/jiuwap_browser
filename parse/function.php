<?php
/*
 *
 *	浏览器->解析处理XML用的
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}


function chkCode($string){
    $code = array('UTF-8', 'GB2312','GBK' );
    foreach($code as $c){
        if( $string === @iconv('UTF-8', $c.'//TRANSLIT', @iconv($c, 'UTF-8//TRANSLIT', $string))){
            return $c;
        }
    }
    return null;
}

Function GetMime($temp2){
    if ( stripos($temp2,'-//WAPFORUM//DTD XHTML Mobile 1.0//EN') || stripos($temp2,'application/vnd.wap.xhtml+xml') ){
        $mime = 'application/vnd.wap.xhtml+xml';
    }elseif ( stripos($temp2,'application/vnd.wap.wmlc') || stripos($temp2,'-//WAPFORUM//DTD WML') || stripos($temp2,'text/vnd.wap.wmlscript') || stripos($temp2,'application/vnd.wap.wmlscriptc') ){
        $mime = 'text/vnd.wap.wml';
    }elseif ( stripos($temp2,'application/xhtml+xml') ){
        $mime = 'application/xhtml+xml';
    }elseif ( stripos($temp2,'text/html') ){
        $mime = 'text/html';
    }elseif ( stripos($temp2,'text/plain') ){
        $mime = 'text/plain';
    }elseif ( stripos($temp2,'text/xml') ){
        $mime = 'text/xml';
    }elseif ( stripos($temp2,'wml') || stripos($temp2,'wbxml') ){
        $mime = 'text/vnd.wap.wml';
    }elseif ( stripos($temp2,'text;') ){
        $mime = 'text/html';
    }else{
        $mime = 'text/html';
    }
    return $mime;
}

Function GetCode($temp2){
    $code = chkCode($temp2);
    if ( $code ){
        return $code;
    }
    if ( stripos($temp2,'encoding="utf-8"') ){
        $code = 'utf-8';
    }elseif ( stripos($temp2,'charset=gbk') || stripos($temp2,'charset=gb2312') ){
        $code = 'gb2312';
    }elseif ( stripos($temp2,'charset=big5') ){
        $code = 'big5';
    }elseif ( stripos($temp2,'charset=utf-8') ){
        $code = 'utf-8';
    }elseif ( stripos($temp2,'gb2312') || stripos($temp2,'big5')){
        $code = 'gb2312';
    }elseif ( stripos($temp2,'big5') ){
        $code = 'big5';
    }elseif ( stripos($temp2,'Windows-1252') ){
        $code = 'gb2312';
    }elseif ( stripos($temp2,'iso-8859-1') ){
        $code = 'gb2312';
    }elseif ( stripos($temp2,'utf-8') ){
        $code = 'utf-8';
    }else{
        $code = mb_detect_encoding($temp2);
    }
    return $code;
}

function script_check_jump($str2){
    $str2 = str_replace('\'','"',$str2);
    $str2 = str_replace('\"','"',$str2);
    global $browser;
    $str = '';
    $top = str_pos($str2,'top.location="','"');
    if ( $top<>'' ){
        $str .= '[<a href="'.fullurl($top).'">javascript:top.location</a>]';
    }
    $self = str_pos($str2,'self.location="','"');
    if ( $self<>'' ){
        $str .= '[<a href="'.fullurl($self).'">javascript:self.location</a>]';
    }
    $href = str_pos($str2,'window.location.href="','"');
    if ( $href<>'' ){
        $str .= '[<a href="'.fullurl($href).'">javascript:window.location.href</a>]';
    }
    $navigate = str_pos($str2,'window.navigate("','")');
    if ( $navigate<>'' ){
        $str .= '[<a href="'.fullurl($navigate).'">javascript:window.navigate</a>]';
    }
    return $str;

}
function fullurl($new_url){
    $new_url = str_ireplace('&at;at;','@',$new_url);
    $new_url = htmlspecialchars_decode($new_url);

    static $old_url = false;
    if ( $old_url === false ){
        global $url_A;
        $old_url = $url_A;
    }

    global $fix_url_base;
    if ( isset($fix_url_base) && $fix_url_base ){
        $old_url = @parse_url($fix_url_base);
        $fix_url_base = false;
    }
    if ( isset($old_url['port']) && $old_url['port']<>80 ){
        $old_url['host'] .= ':'.$old_url['port'];
        unset($old_url['port']);
    }
    if(in_array(strtolower(substr($new_url,0,7)),array('http://','https:/'))){
        return $new_url;
    }elseif ( substr($new_url,0,1) == '?' ){
        $new_url = $old_url['scheme'].'://'.$old_url['host'].$old_url["path"].$new_url;
    }elseif ( substr($new_url,0,1) == '/'){
        $new_url = $old_url['scheme'].'://'.$old_url['host'].$new_url;
    }else{
        if ( isset($old_url["path"]) ){
            $new_url = $old_url['scheme'].'://'.$old_url['host'].substr($old_url["path"],0,strrpos(substr($old_url['path'],0,strrpos($old_url['path'].'?','?')),'/')+1).$new_url;
        }else{
            $new_url = $old_url['scheme'].'://'.$old_url['host'].'/'.$new_url;
        }
    }
    return $new_url;
}

function check_xml($xml,$str){
    $str = str_replace('\"','"',$str);
    $xml = strtolower($xml);
    if ( $xml == '!doctype' ){
        $xml = '!DOCTYPE';
    }
    global $browser,$mime;
    if ( in_array($xml,array('/iframe','/img','/input','/meta','/link','/footer','footer','/section','section')) ){
        return '';
    }
    if ( $browser->wap2wml==0 && $mime <> 'text/vnd.wap.wml' && $mime <> 'application/vnd.wap.xhtml+xml'){
        if ( strpos($str,'class="')){
            $str = trim(preg_replace('@class="(.*?)"@i','', $str));
        }
        if ( strpos($str,'id="')){
            $str = trim(preg_replace('@id="(.*?)"@i','', $str));
        }
        if ( in_array($xml,array('tbody','/tbody','noframes','/noframes','embed','/embed','object','/object','param','/param','frameset','/frameset','p','bod','/bod','fieldset','/fieldset','/legend','legend','nobr','/nobr','s','/s','b','/b','map','/map','area','/area','wbr','/wbr','tr','td','em','/em','font','/font','dl','/dl','dd','/dd','dt','/dt','script','/script','link','h1','h2','h3','h4','h5','/h1','/h2','/h3','/h4','/h5','center','/center','small','/small','strong','/strong','li','ul','table','div','span','/span')) ){
            return '';
        }elseif ( in_array($xml,array('br','br/','/table','/div','/li','/ul','/tr','/p','/td')) ){
            return '<br/>';
        }
    }elseif ( $browser->wap2wml==3 && $mime <> 'text/vnd.wap.wml' ){
        if ( $xml == '!doctype' ){
            return '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
        }elseif ( $xml == 'html' ){
            return '<wml>';
        }elseif ( $xml == '/html' ){
            return '</card></wml>';
        }elseif( $xml =='select'){
            return parse_xml_wml_select($str);
        }elseif( $xml =='textarea'){
            return parse_xml_textarea2input($str);
        }elseif( $xml =='hr'){
            return '<br/>----------<br/>';
        }else{
            if ( in_array($xml,array('label','/label','tbody','/tbody','noframes','embed','/embed','object','/object','param','/param','/noframes','frameset','/frameset','bod','/bod','fieldset','/fieldset','/legend','legend','nobr','/nobr','s','/s','b','/b','map','/map','area','/area','wbr','/wbr','body','/body','tr','/tr','td','em','/em','font','/font','dl','/dl','dd','/dd','dt','/dt','script','/script','link','h1','h2','h3','h4','h5','/h1','/h2','/h3','/h4','/h5','center','/center','small','/small','strong','/strong','li','ul','table','div','span','/span','/form','/textarea')) ){
                return '';
            }elseif ( in_array($xml,array('br','br/','/td','/table','/div','/li','/ul')) ){
                return '<br/>';
            }
        }
    }elseif ( $browser->wap2wml==1 && $mime <> 'text/vnd.wap.wml' ){
        if ( $xml == '!doctype' ){
            return '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">';
        }else{
            if ( in_array($xml,array('label','/label','tbody','/tbody','noframes','/noframes','frameset','/frameset','bod','/bod','fieldset','/fieldset','/legend','legend','nobr','/nobr','map','/map','area','/area','wbr','/wbr','tr','/tr','td','em','/em','font','/font','dl','/dl','dd','/dd','dt','/dt','script','/script','link','center','/center','small','/small','li','ul','h1','h2','h3','h4','h5','/h1','/h2','/h3','/h4','/h5','table')) ){
                return '';
            }elseif ( in_array($xml,array('br','br/','/td','/table','/li','/ul')) ){
                return '<br/>';
            }
        }
    }elseif ( $browser->wap2wml==2 && $mime == 'text/vnd.wap.wml' ){
        if ( $xml == '!doctype' ){
            return '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">';
        }elseif ( $xml == 'wml' ){
            return '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml;charset=utf-8"/>';
        }elseif ( $xml == '/wml' ){
            return '</html>';
        }elseif ( $xml == '/card' ){
            return '</body>';
        }elseif ( $xml == 'card' ){
            return parse_xml_wml2web_card($str).'<style>body{font-size:15px;color:#000;font-family:Arial,Helvetica,sans-serif;}a{color:#039;text-decoration:none;}</style></head><body>';
        }elseif ( $xml == 'timer' ){
            $value = get_xml($str,'value');
            if ( $value <> ''){
                global $wml2web_card_ontimer;
                $wml2web_card_ontimer = (int)$value;
            }
            return '';
        }
    }

    if ( in_array($xml,array('link','option','a','form','img','meta','go','card','input','base','button','iframe','frame')) ){
        $xml = 'parse_xml_'.$xml;
        return $xml($str);
    }else{
        if ( $str ){
            if ( $browser->wap2wml==2 && $mime == 'text/vnd.wap.wml' && $xml == 'p'){
                return '<p>';
            }else{
                return '<'.$xml.' '.$str.'>';
            }
        }else{
            return '<'.$xml.'>';
        }
    }
}
function parse_xml_link($str){
    global $browser;
    $rel = get_xml($str,'rel');
    $href = get_xml($str,'href');
    $type = get_xml($str,'type');
    if ( $type == 'text/css' && $href<>'' && $browser->wap2wml<>0 ){
        $href = $browser->cache_add('url',fullurl($href));
        if ( $rel<>'' ){
            $rel = ' rel="'.$rel.'"';
        }
        return '<link'.$rel.' type="text/css" href="?v='.$href.'">';
    }
    return '';
}


function parse_xml_wml2web_card($str){
    $title = get_xml($str,'title');
    $ontimer = get_xml($str,'ontimer');
    $str = '';
    if ( $ontimer<>'' ){
        global $browser;
        $ontimer = $browser->cache_add('url',fullurl($ontimer));
        $str .= '<meta http-equiv="refresh" content="[#WML2WEB_JUMP_ONTIMER#]; url=?'.$ontimer.'"/>';
    }else{
        $onenterforward = get_xml($str,'onenterforward');
        if ( $onenterforward == '' ){
            $onenterbackward = get_xml($str,'onenterbackward');
        }
        if ( $onenterforward<>'' ){
            global $browser;
            $onenterforward = $browser->cache_add('url',fullurl($onenterforward));
            $str .= '<meta http-equiv="refresh" content="0; url=?'.$onenterforward.'"/>';
        }
    }
    $str .= '<title>'.$title.'</title>';
    return $str;
}

function parse_xml_frame($str){
    return parse_xml_iframe($str);
}

function parse_xml_iframe($str){
    $src = get_xml($str,'src');
    if ( $src ){
        return '<br/>'.parse_xml_a(' href="'.$src.'"').'[iframe]</a><br/>';
    }else{
        return '[iframe]';
    }
}


function parse_xml_option($str){
    $value = get_xml($str,'value');
    $onpick = get_xml($str,'onpick');
    $selected = get_xml($str,'selected');
    $xml_end = get_xml_end($str);
    if ( $onpick<>''){
        global $browser;
        $onpick = $browser->cache_add('url',fullurl($onpick));
    }
    if ( $value<>'' ){
        $value = ' value="'.$value.'"';
    }
    if ( $onpick<>'' ){
        $onpick = ' onpick="?'.$onpick.'"';
    }
    if ( $selected<>'' ){
        $selected = ' selected="'.$selected.'"';
    }
    return '<option'.$value.$onpick.$selected.$xml_end.'>';
}

function parse_xml_button($str){
    $type = get_xml($str,'type');
    $name = get_xml($str,'name');
    $value = get_xml($str,'value');
    $name_value = get_xml($str,'name_value');
    if ( $value<>'' ){
        $value = ' value="'.$value.'"';
    }
    if ( $name<>'' ){
        $name = ' name="'.$name.'"';
    }
    if ( $type<>'' ){
        $type = ' type="'.$type.'"';
    }
    if ( $type == ' type="submit"' ){
		if ( $value == ' value="'.$name_value.'"' ){
			return parse_xml_input($value.$name.$type);
		}else{
			return '<input type="hidden" '.$value.$name.'/>'.parse_xml_input(' value="'.$name_value.'"'.$type);
			//if ( $name_value<>'' ){
			//	$name_value = '['.$name_value.']';
			//}
			//return parse_xml_input($value.$name.$type).$name_value;
		}
    }
    return '<input '.$value.$name.$type.'/>';
}

//处理base
function parse_xml_base($str){
    $href = get_xml($str,'href');
    if ( $href ){
        global $fix_url_base;
        $fix_url_base = fullurl($href);
    }
    return '';
}

//处理meta
function parse_xml_meta($str){
    $http_equiv = get_xml($str,'http-equiv');
    $content = get_xml($str,'content');
    $name = get_xml($str,'name');
    if ( $content == ''){
        $charset = get_xml($str,'charset');
    }else{
        $charset= '';
    }
    $http_equiv = strtolower($http_equiv) ;
    if ( $http_equiv == 'content-type' ){
        global $code,$mime;
        if ( !isset($code) ){
            $code = GetCode($content);
            $mime = GetMime($content);
        }
    }
    if ( $name<>'' && in_array(strtolower($name),array('generato','robots','copyright','generator','author','description','keywords','mssmarttagspreventparsing','mobileoptimized','viewport','format-detection'))){
        return '';
    }
    if ( $http_equiv<>'' && in_array($http_equiv,array('msthemecompatible'))){
        return '';
    }
    global $browser;
    if ( $http_equiv<>'' ){
        //html跳转
        if ( $http_equiv == 'refresh' && !empty($content) ){
            $i = strpos($content,';');
            if ( $i === false ){
                $i = strpos($content,' ');
            }
            if ( $i !== false ){
                $time = trim(substr($content,0,$i));
                $url = trim(substr($content,$i+1));
                if ( strtolower(substr($url,0,4)) == 'url=' ){
                    $url = trim(substr($url,4));
                }
                $url = $browser->cache_add('url',fullurl($url));
                if ( $browser->wap2wml==3 ){
                    global $_html2wmp_jump;
                    if ( !$time ){
                        $time = 1;
                    }
                    $_html2wmp_jump['time'] = $time;
                    $_html2wmp_jump['url'] = '?'.$url;
                    return '';
                }
                $content = $time.';?'.$url;
            }
        }
        global $mime;
        if ( $browser->wap2wml==3 && $http_equiv=='content-type' && $mime <> 'text/vnd.wap.wml' ){
            return '';
        }

        $http_equiv = ' http-equiv="'.$http_equiv.'"';
    }
    if ( $content <> '' ){
        $content = ' content="'.$content.'"';
    }
    if ( $charset <> '' ){
        $charset = ' charset="'.$charset.'"';
    }
    if ( $name<>'' ){
        $name = ' name="'.$name.'"';
    }
    return '<meta'.$http_equiv.$content.$charset.$name.'/>';
}

//处理img标签
function parse_xml_img($str){
    global $browser;
    if( $browser->pic == 5 ){
        //只显示验证码
        static $show_pic = true;
        if ( $show_pic ){
            global $html,$code;
            if ( isset($code) && $code<>'' && $code<>'utf-8' ){
                if (!$temp = @iconv('utf-8',$code.'//TRANSLIT','验证码')){
                    $temp = '验证码';
                }
            }else{
                $temp = '验证码';
            }
            if ( strpos($html,$temp) === false && strpos($html,'&#39564;&#35777;&#30721;') === false ){
                $browser->pic = 0;
            }else{
                $browser->pic = 3;
            }
            $show_pic = false;
        }
    }

    if ( $browser->pic == 1 ){
        return '';
    }

    $src = get_xml($str,'src');
    if ( $src=='' ){
        return '';
    }


    $alt = get_xml($str,'alt');
    if ( $browser->pic == 0 ){
        if ( $alt == ''){
            $alt = 'pic';
        }
        return '['.$alt.']';
    }

    if ( $alt<>'' ) {
        $alt = ' alt="'.$alt.'"';
    }
    $src = fullurl($src);
    if ( $browser->pic == 4 ){
        return '<img src="'.htmlspecialchars($src).'"'.$alt.'/>';
    }
    global $url,$mime;
    $src = $browser->cache_add('pic',$src,$url,$mime);
    return '<img src="?p='.$src.'"'.$alt.'/>';
}


//处理WML里的CARD标签
function parse_xml_card($str){
    global $browser;
    $title = trim(get_xml($str,'title'));
    global $html_title;
    if ( $title<>'' && ( !isset($html_title) || $html_title=='' ) ){
        $html_title = $title;
    }
    $onenterforward = get_xml($str,'onenterforward');
    if ( $onenterforward <> '' ){
        $onenterforward = $browser->cache_add('url',fullurl($onenterforward));
        return '<card title="'.$title.'" onenterforward="?'.$onenterforward.'">';
    }else{
        $ontimer = get_xml($str,'ontimer');
        if ( $ontimer == '' ){
            return '<card title="'.$title.'">';
        }else{
            $ontimer = $browser->cache_add('url',fullurl($ontimer));
            return '<card title="'.$title.'" ontimer="?'.$ontimer.'">';
        }
    }
}

//处理WML表单
function parse_xml_go($str){
    global $browser;
    $url = get_xml($str,'href');
    $method = get_xml($str,'method');
    $xml_end = get_xml_end($str);
    $url = $browser->cache_add('url',fullurl($url));
    if ( $method == '' || strtolower($method) == 'get'){
        return '<go href="?po='.$url.'" method="post"'.$xml_end.'>';
    }else{
        return '<go href="?'.$url.'" method="post"'.$xml_end.'>';
    }
}

//处理HTML表单
function parse_xml_form($str){
    global $browser;
    $url = get_xml($str,'action');
    $method = get_xml($str,'method');
    $url = $browser->cache_add('url',fullurl($url));
    if ( $method == '' || strtolower($method) == 'get'){
        $url = '?po='.$url;
    }else{
        $url = '?'.$url;
    }
    $enctype = get_xml($str,'enctype');

    if ( $browser->wap2wml==3 ){
        global $_form_url;
        $_form_url = $url;
        if ( $enctype <> '' &&strtolower($enctype) == 'multipart/form-data' ){
            global $cmd,$disk_upload_var,$form_diskipload;
            $disk_upload_var = '['.$browser->rand.'-DISK-UPLOAD-MODE-'.$browser->rand.']';
            return '<form action="'.$url.'" enctype="'.$enctype.'" method="post" disksid="'.$disk_upload_var.'"/><!--'.$disk_upload_var.'-->';
        }else{
            return '';
        }
    }

    if ( $enctype <> '' ){
        IF ( strtolower($enctype) == 'multipart/form-data' ){
            global $cmd,$disk_upload_var,$form_diskipload;
            $disk_upload_var = '['.$browser->rand.'-DISK-UPLOAD-MODE-'.$browser->rand.']';
            return '<form action="'.$url.'" enctype="'.$enctype.'" method="post" disksid="'.$disk_upload_var.'"><!--'.$disk_upload_var.'-->';
        }else{
            return '<form action="'.$url.'" enctype="'.$enctype.'" method="post">';
        }
    }else{
        return '<form action="'.$url.'" method="post">';
    }
}

//处理textarea
function parse_xml_textarea2input($str){
    $name = get_xml($str,'name');
    if ( $name=='' ){
        return '';
    }
    global $browser,$mime;
    if ( $browser->wap2wml==3 && $mime <> 'text/vnd.wap.wml' ){
        global $_form_input;
        $_form_input[$name] = '$('.$name.$browser->rand.')';
        $name .= $browser->rand;
    }

    $value = get_xml($str,'value');
    return '<input type="text" name="'.$name.'" value="'.$value.'"/>';
}

//处理textarea
function parse_xml_wml_select($str){
    $name = get_xml($str,'name');
    global $_form_input,$browser;
    $_form_input[$name] = '$('.$name.$browser->rand.')';
    $name .= $browser->rand;
    return '<select name="'.$name.'">';
}

//处理INPUT
function parse_xml_input($str){
    $name = get_xml($str,'name');
    $type = strtolower(get_xml($str,'type'));
    $value = get_xml($str,'value');
    global $browser,$mime;
    if ( $browser->wap2wml==3 && $mime <> 'text/vnd.wap.wml' ){
        if ( $type == 'hidden'){
            global $_form_input;
            $_form_input[$name] = $value;
            return '';
        }elseif ( $type == 'submit'){
            global $_form_input,$_form_url;
            if ( !isset($_form_url) ){
                return '';
            }
            if ( $value=='' ){
                $value = 'submit';
            }
            $str  = '<anchor>'.$value.'<go href="'.$_form_url.'" method="post">';
            if ( $name ){
                $str .= '<postfield name="'.$name.'" value="'.$value.'"/>';
            }
            if ( isset($_form_input) && $_form_input){
                foreach($_form_input as $name => $value){
                    $str .= '<postfield name="'.$name.'" value="'.$value.'"/>';
                }
            }
            $str .= '</go></anchor>';
            return $str;
        }
    }elseif ( $browser->wap2wml==2 && $mime == 'text/vnd.wap.wml' ){
        global $wml_form;
        $wml_form['input'][$name] = $value;
        return '[input='.$name.']';
    }
    if ( $value<>'' ){
        $value = ' value="'.$value.'"';
    }
    if ( $type<>'' ){
        $type = ' type="'.$type.'"';
    }
    if ( $name<>'' ){
        global $mime;
        if ( $browser->wap2wml==3 && $mime <> 'text/vnd.wap.wml'){
            global $_form_input;
            $_form_input[$name] = '$('.$name.$browser->rand.')';
            $name .= $browser->rand;
        }
        $name = ' name="'.$name.'"';
    }
    return '<input'.$type.$name.$value.'/>';
}

//处理超链接
function parse_xml_a($str){
    global $browser;
    $id = get_xml($str,' id');
    $href = get_xml($str,'href');
    $xml_end = get_xml_end($str);
	global $b_set;
	static $httplen = false;
	if ( $httplen ===false ){
		$httplen = strlen('http://'.$b_set['host']);
	}
    if ( $href && substr($href,0,$httplen)=='http://'.$b_set['host'] ){
        return '<a href="'.$href.'"'.$xml_end.'>';
    }

    if ( $href<>'' && $id){
        return '<a id="'.$id.'"'.$xml_end.'>';
    }elseif ( $id ){
        $id = ' id="'.$id.'"';
    }
    $a = substr($href,0,4);
    if ( $a == 'sms:' ||  $a == 'tel:' || substr($href,0,1) == '#' || substr($href,0,11) == 'javascript:'){
        return '<a href="'.$href.'"'.$xml_end.'>';
    }else{
        global $mime;
        $href = fullurl($href);
        /*if ( $mime == 'text/vnd.wap.wml' ){
            //超链接里含有input提交，fuck费劲的wml,当前不支持url里$参数
            $re = str_pos($str,'$(',')');
        }*/
        $href = $browser->cache_add('url',$href);
        return '<a href="?'.$href.'"'.$xml_end.'>';
    }
}

//解析出标签里的元素
function get_xml($str,$name){
    $i = strlen($name);
    $a = stripos($str,$name.'="');
    if ( $a !== false){
        $end = substr($str,$a + $i + 2);
        $b = strpos($end,'"');
        $str = substr($str,$a + $i + 2,$b);
    }else{
        $a = stripos($str,$name.'=\'');
        if ( $a !== false){
            $end = substr($str,$a + $i + 2);
            $b = strpos($end,'\'');
            $str = substr($str,$a + $i + 2,$b);
        }else{
            $a = stripos($str,$name.'=');
            if ( $a !== false){
                $end = substr($str,$a + $i + 1);
                $b = strpos($end,' ');
                if ( $b === false ){
                    $end = str_replace(array('/>','/ >'),'>',$end);
                    $b = strlen($end);
                }
                $str = substr($str,$a + $i + 1,$b);
            }else{
                $str = '';
            }
        }
    }
    return $str;
}

function get_xml_end($str){
    $str = substr($str,strrpos($str,'"'));
    $str = substr($str,strrpos($str,'\''));
    if (substr($str,strlen($str)-1) == '/'){
        return '/';
    }else{
        return '';
    }
}

function fix_wml_form($xml,$str){
    $str = str_replace('\"','"',$str);
    $xml = strtolower($xml);
    global $wml_form;
    if ( $xml == 'anchor' ){
        $wml_form['show_select'] = array();
        $wml_form['show_input'] = array();
        $wml_form['hidden_input'] = array();
        $wml_form['url'] = '';
        return '';

    }elseif ( $xml == '/anchor' ){
        $str = '<form action="'.$wml_form['url'].'" method="post">';
        foreach($wml_form['show_select'] as $name=>$val){
            $str .= $name.'：'.$val.'<br/>';
        }
        foreach($wml_form['show_input'] as $name=>$val){
            $str .= $name.'：<input type="text" name="'.$name.'" value="'.$val.'"/><br/>';
        }
        foreach($wml_form['hidden_input'] as $name=>$val){
            $str .= '<input type="hidden" name="'.$name.'" value="'.$val.'"/>';
        }
        $str .= '<input type="submit" value="submit"/></form>';
        return $str;

    }elseif ( $xml == 'postfield' ){
        $name = get_xml($str,'name');
        $value = get_xml($str,'value');
        if ( substr($value,0,1)=='$' ){
            $value = substr($value,1);
            if ( substr($value,0,1)=='(' && substr($value,strlen($value)-1)==')' ){
                $value = substr($value,1,strlen($value)-2);
            }
            if ( isset($wml_form['select'][$value]) ){
                $wml_form['show_select'][$name] = $wml_form['select'][$value];
            }elseif ( isset($wml_form['input'][$value]) ){
                $wml_form['show_input'][$name] = $wml_form['input'][$value];
            }else{
                $wml_form['hidden_input'][$name] = '$'.$value;
            }
        }else{
            $wml_form['hidden_input'][$name] = $value;
        }
        return '';

    }elseif ( $xml == 'go' ){
        $href = get_xml($str,'href');
        $wml_form['url'] = $href;
        return '';
    }elseif( $xml == '/go' ){
        return '';
    }else{
	    return '';
	}
}

function fix_wml_form_select($xml,$str){
    $xml = str_replace('\"','"',$xml);
    $str = str_replace('\"','"',$str);
    $name = get_xml($xml,'name');
    global $wml_form;
    $wml_form['select'][$name] = '<select '.$xml.'>'.$str.'</select>';
    return '[select='.$name.']';
}

function fix_css($str,$fix=true){
    $fix && $str = str_replace('\"','"',$str);
    if ( strpos($str,'/*')!==false && strpos($str,'*/')!==false ){
        $str = preg_replace("@\/\*(.+?)\*\/@i", '', $str);
    }
    global $browser;
    if ( $browser->pic == 0 || $browser->pic == 1 || $browser->pic == 5 ){
        $str = preg_replace("@background:url\((.+?)\)@i", 'background:none', $str);
    }elseif( $browser->pic == 4 ){
        $str = preg_replace("@background:url\((.+?)\)@ies", "'background:url('.fullurl('\\1').')'", $str);
    }else{
        $str = preg_replace("@background:url\((.+?)\)@ies", "'background:url(?p='._browser_cache_add_pic('\\1').')'", $str);
    }
    return $str;
}

function _browser_cache_add_pic($scr){
    global $browser,$url,$mime;
    return $browser->cache_add('pic',fullurl($scr),$url,$mime);
}