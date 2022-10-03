<?php
/*
 *
 *	浏览器->开始..
 *
 *	2011-4-3 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

!defined('m') && header('location: /?r='.rand(0,999));

include_once DIR.'parse/function.php';

if ( !in_array(strtolower(substr($url,0,7)),array('https:/','http://')) ) {
	$url = 'http://'.$url;
}

if ( isset($_POST) && $_POST != array() && !isset($_GET['r']) ){
	if ( isset($form_post2get)){
		unset($form_post2get);
		$t = strpos($url,'?');
		foreach($_POST as $a => $b){
			if ( $t ){
				$url .= '&'.$a .'=' .urlencode(ubb_copy($b));
			} else {
				$t = true;
				$url .= '?'.$a .'=' .urlencode(ubb_copy($b));
			}
		}
	}else{
		$_post = $_POST;
	}
}
unset($_POST);

$http = new httplib();

//建立FSOCKOPEN链接
$http->open($url,30,5);

if ( isset($_SERVER['HTTP_REFERER']) ){
	$a = strpos($_SERVER['HTTP_REFERER'],'/?');
	if ( $a !== false){
		$a = substr($_SERVER['HTTP_REFERER'],$a+2);
		if ( $a && strlen($a)<=2 ){
			$a = $browser->cache_get('url',$cmd);
			if ( $a ){
				$http->referer($a);
			}
		}
	}
	unset($a);
}


$url_A = $http->parse_url();

//检测是否嵌套浏览
include DIR.'set_config/set_forbidhost.php';
if ( in_array(strtolower($url_A['host']),$b_set['forbid']) && !stripos($url,$b_set['host'].'/self') ){
	$str = '错误：您访问的网站['.$url_A['host'].']无法访问。<br/>原因：嵌套网页浏览器或目标网站不合法。';
    error_show('访问失败',$str);
}

//设置代理
if( !empty($browser->ipagent) && $browser->ipagent_open == 1 ){
	$ip = explode(':',$browser->ipagent);
	$http->proxy(trim($ip[0]),trim($ip[1]));
	unset($ip);
}

//发送POST
//网盘上传？
if( isset($form_2diskup) && isset($_GET['fi']) ){
    include_once DIR.'tools/disk/inc.php';
    init_disk();
    $ups = 'file'.(float)$_GET['fi'].'_';
    $upn = strlen($ups);
    $all_size = 0;
    if ( isset($_post) ){
        foreach($_post as $a => $b){
			if ( is_array($b) ){
				foreach($b as $c => $b){
					if ( substr($a,0,$upn) == $ups ){
						if ( $b == '' || $b == '[disk=0]'){
							continue;
						}
						$upi = (float) str_pos($b,'[disk=',']');
						$upi && $dir = $browser->db->fetch_first('SELECT size,file,mime,title FROM `disk_file` WHERE uid='.$disk['id'].' AND id='.$upi);
						if ( !$upi || !$dir ){
							error_show('上传网盘文件失败','错误：上传网盘文件失败。<br/>原因：'.$b.'对应的网盘文件不存在。');
						}
						$all_size += $dir['size'];
						if ( $all_size > $b_set['tupload'] ) {
							error_show('访问失败','错误：禁止访问。<br/>原因：上传文件过大，不得大于'.bitsize($b_set['tupload']).'。');
						}
						$dir['file'] = $b_set['dfforever'].$dir['file'];
						$valcontent = @file_get_contents($dir['file']);
						$http->file(substr($a,$upn).'['.$c.']',$dir['title'],$valcontent,get_file_mime($dir['mime']));
						unset($valcontent);
					}else{
						$http->post($a,ubb_copy($b));
					}
				}
			}else{
				if ( substr($a,0,$upn) == $ups ){
					if ( $b == '' || $b == '[disk=0]'){
						continue;
					}

					$upi = (float) str_pos($b,'[disk=',']');
					$upi && $dir = $browser->db->fetch_first('SELECT size,file,mime,title FROM `disk_file` WHERE uid='.$disk['id'].' AND id='.$upi);
					if ( !$upi || !$dir ){
						error_show('上传网盘文件失败','错误：上传网盘文件失败。<br/>原因：'.$b.'对应的网盘文件不存在。');
					}
					$all_size += $dir['size'];
					if ( $all_size > $b_set['tupload'] ) {
						error_show('访问失败','错误：禁止访问。<br/>原因：上传文件过大，不得大于'.bitsize($b_set['tupload']).'。');
					}
					$dir['file'] = $b_set['dfforever'].$dir['file'];
					$valcontent = @file_get_contents($dir['file']);
					$http->file(substr($a,$upn),$dir['title'],$valcontent,get_file_mime($dir['mime']));
					unset($valcontent);
				}else{
					$http->post($a,ubb_copy($b));
				}
			}
        }
        unset($_post);
    }
    unset($all_size,$disk,$ups,$upn,$upi);

}else{
    if ( isset($_post) ){
        foreach($_post as $a => $b){
			if ( is_array($b) ){
				foreach($b as $c => $b){
		            $http->post($a.'['.$c.']',ubb_copy($b));
				}
			}else{
				$http->post($a,ubb_copy($b));
			}
        }
        unset($_post);
    }
}


//读取数据库COOKIE
$cookies = $browser->cookieGet($url_A['host'],$url_A['path']);
foreach($cookies as $cookie_key=>$cookie_value){
	$http->cookie($cookie_key,$cookie_value);
}
unset($cookies,$cookie_key,$cookie_value);


//上传文件
if ( $_FILES ){
	$all_size = 0;
	foreach ( $_FILES as $name=>$value){
        if ( is_array($value['size']) ){
            foreach ( $value['size'] as $a){
	        	$all_size += $a;
            }
        }else{
    		$all_size += $value['size'];
        }
	}

	if ( $all_size > $b_set['tupload'] ) {
        error_show('访问失败','错误：禁止访问。<br/>原因：上传文件过大，不得大于'.bitsize($b_set['tupload']).'。');
	}

	foreach ( $_FILES as $name=>$val){
        if ( is_array($val['name']) ){
            foreach ( $val['name'] as $a=>$b){
                if ( $val['content'][$a] = @file_get_contents($val['tmp_name'][$a]) ){
                    $http->file($name.'['.$a.']',$val['name'][$a],$val['content'][$a],$val['type'][$a]);
                }
            }
        }else{
            if ( $val['content'] = @file_get_contents($val['tmp_name']) ){
                $http->file($name,$val['name'],$val['content'],$val['type']);
            }
        }

	}
	unset($all_size,$_FILES,$val);

}

$browser->selectBrowserUA();
$http->header('jiuwapb',$version);

//开始
$http->send();

//获取返回头信息
$header = $http->header();
$url_A = $http->parse_url($url);
$url = $http->url();
$fix_url_base = false;

if ( isset($header['STATUS']) && $header['STATUS'] == '301' ){
	$str = '错误：您输入的域名有误或站点访问失败。(301)<br/>地址：'.$url.'	<br /><a href="'.$url.'">直接浏览</a>';
    error_show('访问失败',$str);
}

//检测是否为网页文件
if ( !isset($is_css) && (!isset($header['CONTENT-TYPE']) || !http_ishtml($header['CONTENT-TYPE'])) ){
	include DIR.'parse/parse_down.php';exit;

}else{
	//检测大小
	if ( isset($header['CONTENT-LENGTH']) && $header['CONTENT-LENGTH'] > 512000 ){
		$str = '错误：目标网页大小超过500KB，当前不允许浏览<br/>地址：'.$url.'	<br /><a href="'.$url.'">直接浏览</a>';
        error_show('访问失败',$str);
	}
}
//保存返回的COOKIE
foreach ( $header['COOKIE'] as $key => $value){
	$browser->cookieSave($url_A['host'],$value['domain'],$key,$value['value'],$value['path'],$value['expires']);
}

unset($header['COOKIE']);
if ( isset($is_css) ){
	include DIR.'parse/parse_css.php';
}else{
	include DIR.'parse/parse_xml.php';
}
