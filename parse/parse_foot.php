<?php
/*
 *
 *	浏览器->底部
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
if ( !isset($form_diskupload) && $html_size_old == 0 ){
    $form_diskupload = false;
}

if ( isset($disk_upload_var) ){
	$enterdiskupload = '进入网盘上传模式';
	if ($code<>'utf-8'){
		@$enterdiskupload = iconv('utf-8',$code.'//TRANSLIT', $enterdiskupload);
	}
    //进入网盘模式
    if ( $browser->wap2wml==3 ){
		$enterdiskfalse = '检测到上传表单,当前处于WAP1模式,无法本地上传';
		if ($code<>'utf-8'){
			@$enterdiskfalse = iconv('utf-8',$code.'//TRANSLIT', $enterdiskfalse);
		}
        $html = str_replace('<!--'.$disk_upload_var.'-->','<a href="?dl='.$the_history_key.'">['.$enterdiskupload.']</a><br/>('.$enterdiskfalse.')<br/>',$html);
    }else{
        $html = str_replace('<!--'.$disk_upload_var.'-->','<a href="?dl='.$the_history_key.'">['.$enterdiskupload.']</a><br/>',$html);
    }
}elseif( isset($form_diskupload) ){
	$enterdiskupload = '进入网盘上传模式';
	$exitdiskupload = '离开网盘上传模式';
	if ($code<>'utf-8'){
		@$exitdiskupload = iconv('utf-8',$code.'//TRANSLIT', $exitdiskupload);
		@$enterdiskupload = iconv('utf-8',$code.'//TRANSLIT', $enterdiskupload);
	}
    $disk_upload_var = str_pos($html,'<form','>');
    preg_match_all('@<form(.*?)>@i',$html,$matches);
    if ( isset($matches[1]) &&is_array($matches[1]) ){
        foreach($matches[1] as $var){
            $disk_action = str_pos($var,'action="','"');
            $disk_upload_var = str_pos($var,'disksid="','"');
            if ( $disk_upload_var<>'' ){
                if ( $form_diskupload ){
                    //离开网盘模式
                    if ( $browser->wap2wml==3 && str_pos($html,'<go','>')<>''){
                        $html = str_replace('<go href="'.$disk_action,'<go href="?fi='.$browser->rand.'&amp;dn='.substr($disk_action,1),$html);
                        preg_match_all('@<input type="file" name="(.*?)"@i',$html,$matches);
                        if ( isset($matches[1]) &&is_array($matches[1]) ){
                            $html = str_replace('<input type="file"','<input type="text" value="[disk=0]"',$html);
                            foreach($matches[1] as $var){
                                preg_match('@<postfield name="([0-9a-zA-Z]*)" value="\$\('.$var.'\)"@i',$html,$matches2);
                                if ( isset($matches[1]) ){
                                    $html = str_replace('<postfield name="'.$matches2[1].'"','<postfield name="file'.$browser->rand.'_'.$matches2[1].'"',$html);
                                }
                            }
                        }
                        unset($var2,$var,$matches2,$matches);
                    }else{
                        $html = str_replace('<form action="'.$disk_action,'<form action="?fi='.$browser->rand.'&amp;dn='.substr($disk_action,1),$html);
                        $html = str_replace('<input type="file" name="','<input type="text" value="[disk=0]" name="file'.$browser->rand.'_',$html);
                    }
                    $html = str_replace('enctype="multipart/form-data" ','',$html);
					$tipsdisk = '提示:请使用[disk=x]这样的代码代替网盘文件，该代码可以在网盘文件底部看到，每个文件输入框只允许有一个[disk=x]且其他字符无效，留空或者[disk=0]则不传文件。';
					if ($code<>'utf-8'){
						@$tipsdisk = iconv('utf-8',$code.'//TRANSLIT', $tipsdisk);
					}
                    $html = str_replace('<!--'.$disk_upload_var.'-->','<a href="?dh='.$the_history_key.'">['.$exitdiskupload.']</a><br/>('.$tipsdisk.')'.hr,$html);
                }else{
                    if ( $browser->wap2wml==3 ){
						$enterdiskfalse = '检测到上传表单,当前处于WAP1模式,无法本地上传';
						if ($code<>'utf-8'){
							@$enterdiskfalse = iconv('utf-8',$code.'//TRANSLIT', $enterdiskfalse);
						}
                        $html = str_replace('<!--'.$disk_upload_var.'-->','<a href="?dl='.$the_history_key.'">['.$enterdiskupload.']</a><br/>('.$enterdiskfalse.')<br/>',$html);
                    }else{
                        $html = str_replace('<!--'.$disk_upload_var.'-->','<a href="?dl='.$the_history_key.'">['.$enterdiskupload.']</a><br/>',$html);
                    }
                }
            }
        }
    unset($matches);
    }
}

$title_str = $b_set['title_str'];

//模板化
$bottom_str = $browser->template_foot;
if ( strpos($bottom_str,'[')!==false && strpos($bottom_str,']')!==false ){
    $bottom_str = str_replace('[book]','<a href="?n='.$the_history_key.'">书签</a>',$bottom_str);
    $bottom_str = str_replace('[add]','<a href="book.php?cmd=new&amp;h='.$the_history_key.'">存书签</a>',$bottom_str);
    $bottom_str = str_replace('[menu]','<a href="?m='.$the_history_key.'">菜单</a>',$bottom_str);
    $bottom_str = str_replace('[copy]','<a href="copy.php?cmd=copy&amp;h='.$the_history_key.'">复制</a>',$bottom_str);
    $bottom_str = str_replace('[board]','<a href="copy.php?h='.$the_history_key.'">剪切板</a>',$bottom_str);
    $bottom_str = str_replace('[old]','<a href="history.php?h='.$the_history_key.'">历史</a>',$bottom_str);
    $bottom_str = str_replace('[set]','<a href="set.php?h='.$the_history_key.'">设置</a>',$bottom_str);
    $bottom_str = str_replace('[main]','<a href="/">主页</a>',$bottom_str);
    $bottom_str = str_replace('[jump]','<a href="?o='.$the_history_key.'">跳出</a>',$bottom_str);
    $bottom_str = str_replace('[url]',htmlspecialchars($url),$bottom_str);
    $bottom_str = str_replace('[br]','<br/>',$bottom_str);
    if ( $html_size_old == -1){
        $bottom_str = str_replace('[size]',bitsize($html_size_new),$bottom_str);
    }elseif ( $html_size_old <> 0){
        $bottom_str = str_replace('[size]',bitsize($html_size_new).'('.bitsize($html_size_old).')',$bottom_str);
    }else{
        $bottom_str = str_replace('[size]',bitsize($html_size_new).'[缓存]',$bottom_str);
    }
    if ( $mime <> 'text/vnd.wap.wml' ){
        $bottom_str = str_replace('[go]','<form action="index.php" method="get"><input type="text" name="url" value="'.htmlspecialchars($url).'" /><br/><input type="submit" value="进入"/></form>',$bottom_str);
    }else{
        $bottom_str = str_replace('[go]','<input name="url'.$browser->rand.'" type="text" value="'.htmlspecialchars($url).'"/><br/><anchor><go href="index.php" method="get"><postfield name="url" value="$(url'.$browser->rand.')" /></go>进入</anchor>',$bottom_str);
    }
    if ( strpos($bottom_str,'[time=')!==false && strpos($bottom_str,']')!==false ){
        $bottom_str = preg_replace('/\[time=(.*?)\]/ies', "date_('\\1')", $bottom_str);
    }
}
$bottom_str = init_ad().$bottom_str;
if( $mime == 'text/vnd.wap.wml' ){
   $bottom_str = '<p>'.$bottom_str.'</p>';
}else{
   $bottom_str = '<div>'.$bottom_str.'</div>';
}

if ($code<>'utf-8'){
	//$title_str = mb_convert_encoding($title_str,$code, 'utf-8');
	//$bottom_str = mb_convert_encoding($bottom_str,$code, 'utf-8');
	@$title_str = iconv('utf-8',$code.'//TRANSLIT', $title_str);
	@$bottom_str = iconv('utf-8',$code.'//TRANSLIT', $bottom_str);
}

if ( stripos($html,'</body>') ){
   $html = str_ireplace('<title>','<title>'.$title_str,$html);
   $html = str_ireplace('</body>',$bottom_str.'</body>',$html);
}elseif ( stripos($html,'</card>') ){
   $html = preg_replace('/<card(.*?)title="(.*?)"/i','<card$1title="'.$title_str.'$2"', $html);
   $html = str_ireplace('</card>',$bottom_str.'</card>',$html);
}else{
	if ( $html=='' ){
		$browser->template_top('空白');
		echo '<p>空白内容<br />(本页面是经过'.$b_set['webtitle'].'引擎处理后的界面，可能引擎解析xml时过滤了部分代码，导致内容为空。)</p>';
		echo $bottom_str;
		$browser->template_foot();
	}else{
		$html .= $bottom_str;
	}
}

if ( $code <> 'utf-8' || $mime == 'text/vnd.wap.wml' ){
	if ( $mime <> 'text/vnd.wap.wml' ){
		$mime = 'text/html';
	}
	header('Content-Type: '.$mime.'; charset='.$code);
}
echo $html;
ob_end_flush();