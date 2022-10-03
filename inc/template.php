<?php
@include DIR.'set_config/version.php';
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

function load_template($file,$Powered=true,$jump=false,$jump_wait=false){
	global $b_set,$browser,$version,$is_run_temp_top,$get_string;
	$is_run_temp_top = false;
	$jiuwap_template = @file_get_contents('template/'.$file.'.html');


	$jiuwap_template = str_replace(array('<hr>','<hr/>','<hr />'),hr,$jiuwap_template);

	$jiuwap_template = str_replace('[title]',$b_set['webtitle'],$jiuwap_template);
	$jiuwap_template = str_replace('[version]',$version,$jiuwap_template);

	strpos($jiuwap_template,'[main_form_url]')!==false && $jiuwap_template = str_replace('[main_form_url]',template_main_form_url(),$jiuwap_template);
	$jiuwap_template = preg_replace('/\[main_menu_exit_true=(.*?)\]/i', "<a href=\"logout.php?yes=yes&r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_exit=(.*?)\]/i', "<a href=\"logout.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_book=(.*?)\]/i', "<a href=\"book.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_disk=(.*?)\]/i', "<a href=\"disk.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;


	$jiuwap_template = preg_replace('/\[main_menu_login=(.*?)\]/i', "<a href=\"login.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_copy=(.*?)\]/i', "<a href=\"copy.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_set=(.*?)\]/i', "<a href=\"set.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_clear=(.*?)\]/i', "<a href=\"clear.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_help=(.*?)\]/i', "<a href=\"help.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_synch=(.*?)\]/i', "<a href=\"synch.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_index=(.*?)\]/i', "<a href=\"index.php?r=".$browser->rand."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_jiuwap=(.*?)\]/i', "<a href=\"http://3.bbs.jiuwap.cn\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_updatelogs=(.*?)\]/i', "<a href=\"self/new.php\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[main_menu_update=(.*?)\]/i', "<a href=\"install\">$1</a>",$jiuwap_template) ;


	strpos($jiuwap_template,'[main_map_sites=')!==false && $jiuwap_template = preg_replace('/\[main_map_sites=(.*?)\]/ies', "template_main_map_sites('\\1')", $jiuwap_template);
	strpos($jiuwap_template,'[main_wap_select=')!==false && $jiuwap_template = preg_replace('/\[main_wap_select=(.*?)\|(.*?)\]/ies', "template_wap_select('\\1','\\2')", $jiuwap_template);


	$jiuwap_template = str_replace('[username]',$browser->uname,$jiuwap_template);

	strpos($jiuwap_template,'[main_historys]')!==false && $jiuwap_template = str_replace('[main_historys]',template_main_historys(),$jiuwap_template);
	strpos($jiuwap_template,'[main_use_image]')!==false && $jiuwap_template = str_replace('[main_use_image]',bitsize($browser->num_size_pic),$jiuwap_template);
	strpos($jiuwap_template,'[main_use_html]')!==false && $jiuwap_template = str_replace('[main_use_html]',bitsize($browser->num_size_html),$jiuwap_template);
	$jiuwap_template = str_replace('[main_use_look]',$browser->num_look,$jiuwap_template);
	strpos($jiuwap_template,'[main_use_time]')!==false && $jiuwap_template = str_replace('[main_use_time]',template_main_use_time(),$jiuwap_template);

	strpos($jiuwap_template,'[login_form]')!==false && $jiuwap_template = str_replace('[login_form]',template_login_form(),$jiuwap_template);
	$jiuwap_template = preg_replace('/\[login_reg=(.*?)\]/i', "<a href=\"reg.php\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[login_repass=(.*?)\]/i', "<a href=\"reg_repass.php\">$1</a>",$jiuwap_template) ;

	$jiuwap_template = str_replace('[icp]',$b_set['icp'],$jiuwap_template);

	strpos($jiuwap_template,'[reg_form]')!==false && $jiuwap_template = str_replace('[reg_form]',template_reg_form(),$jiuwap_template);

	if ( $get_string ){
		$jiuwap_template = str_replace('[get_string]',$get_string,$jiuwap_template);
	}

	//
	global $h,$au;
	if ( !isset($h) || $h === null ){
		$h = '';
	}
	if ( !isset($au) || $au === null ){
		$au = '';
	}
	$jiuwap_template = preg_replace('/\[menu_back=(.*?)\]/i', "<a href=\"/?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_jump_out=(.*?)\]/i', "<a href=\"/?o=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_copy=(.*?)\]/i', "<a href=\"copy.php?cmd=copy".$au."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_copy_board=(.*?)\]/i', "<a href=\"copy.php?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_book_add=(.*?)\]/i', "<a href=\"book.php?cmd=new".$au."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_book=(.*?)\]/i', "<a href=\"/?n=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_history=(.*?)\]/i', "<a href=\"history.php?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_disk=(.*?)\]/i', "<a href=\"disk.php?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_set=(.*?)\]/i', "<a href=\"set.php?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_clear=(.*?)\]/i', "<a href=\"clear.php?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = preg_replace('/\[menu_help=(.*?)\]/i', "<a href=\"help.php?h=".$h."\">$1</a>",$jiuwap_template) ;
	$jiuwap_template = str_replace('[menu_form_url]',template_menu_form_url(),$jiuwap_template);




	$jiuwap_template = preg_replace('/\[title=(.*?)\]/ies', "template___top('\\1')", $jiuwap_template);

	if ( !$is_run_temp_top ){
		$browser->template_top('',$jump,$jump_wait);
	}
	echo $jiuwap_template;
	if ( $Powered ){
		echo '<br/>Powered By <a href="http://jiuwap.cn/">Jiuwap.cn</a>';
	}
	$browser->template_foot();
}

function template___top($fun_title){
	global $browser;
	global $is_run_temp_top,$jump,$jump_wait;
	$is_run_temp_top = true;
	$browser->template_top($fun_title,$jump,$jump_wait);
	return'';
}

function template_main_form_url(){
	global $browser;
	if ( $browser->template == 0 ){
		return '<form action="index.php" method="get">
		网址：<input type="text" name="url" value="" />
		<input type="submit" value="进入"/><br /></form>';
	}else{
		return '网址：<input name="url'.$browser->rand.'" type="text" value=""/>
		<anchor>
		<go href="index.php" method="get">
		<postfield name="url" value="$(url'.$browser->rand.')" />
		</go>进入</anchor>';
	}
}

function template_main_map_sites($a){
	global $browser;
	$site = $browser->site_lists();
	if ( $site == array() ){
		return '';
	}else{
		$sites = array();
		foreach($site as $id=>$val){
			$sites[] =  '<a href="/?s='.$id.'">'.$val['title'].'</a>';
		}
		return implode($a,$sites);
	}
}

function template_main_historys(){
	global $browser;
	$history = $browser->history_get();
	if ( $history == array() ){
		return '无<br/>';
	}else{
		$echo = '';$i = count($history) - 5;
		foreach($history as $id=>$val){
			if (--$i < 0){
				$echo = '<a href="/?h='.$id.'">'.urldecode($val['title']).'</a><br/>'.$echo ;
			}
		}
		return $echo;
	}
}


function template_main_use_time(){
	global $browser;
	$size_date = time_()-$browser->num_time;
	if ( $size_date >= 60 ){
		$size_date = $size_date / 60;
		if ( $size_date >= 60 ){
			$size_date = $size_date / 60;
			if ( $size_date >= 60 ){
				$size_date = (int)($size_date/24) .'天';
			}else{
				$size_date = (int)$size_date . '小时';
			}
		}else{
			$size_date = (int)$size_date . '分钟';
		}
	}else{
		$size_date = (int)$size_date . '秒';
	}
	return $size_date;
}


function template_reg_form(){
	global $browser;
	if ( $browser->template == 0 ){
		return '<form action="reg.php?yes=yes&r='.$browser->rand.'" method="post">
		账号：<input type="text" name="name" value="" /><br />
		密码：<input type="password" name="pass" value="" /><br />
		<input type="submit" value="注册"/>
		</form>';
	}else{
		return '账号：<input name="name" type="text" value=""/><br/>
		密码：<input name="pass" type="password" value=""/><br/>
		<anchor>
			<go href="reg.php?yes=yes&amp;r='.$browser->rand.'" method="post">
			<postfield name="name" value="$name" />
			<postfield name="pass" value="$pass" />
			<postfield name="pass1" value="$pass1" />
			</go>注册</anchor><br/><a href="login.php">';
	}
}
function template_login_form(){
	global $browser;
    if ( $browser->template == 0 ){
		return '<form action="login.php" method="get">
        <input type="hidden" name="r" value="'.$browser->rand.'" /><br />
        <input type="hidden" name="yes" value="yes" /><br />
        账号：<input type="text" name="name" value="" /><br />
        密码：<input type="password" name="pass" value="" /><br />
        <input type="submit" value="登录"/>
        </form>';
    }else{
		return '
        账号：<input name="name_login" type="text" value=""/><br/>
        密码：<input name="pass_login" type="password" value=""/><br/>
        <anchor>
        <go href="login.php" method="get">
        <postfield name="yes" value="yes" />
        <postfield name="r" value="'.$browser->rand.'" />
        <postfield name="name" value="$(name_login)" />
        <postfield name="pass" value="$(pass_login)" />
        </go>登录</anchor>';
   }
}


function template_wap_select($a,$b){
	global $browser;
	if ( $browser->template == 1 ){
		return $a.'|<a href="wap.php?back=login&amp;wap=0">'.$b.'</a>';
	}else{
		return '<a href="wap.php?back=login&amp;wap=1">'.$a.'</a>|'.$b;
	}
}



function template_menu_form_url(){
	global $browser;
	global $url;
	if ( $browser->template == 0 ){
		return '<form action="index.php" method="get">
		网址：<input type="text" name="url" value="'.htmlspecialchars($url).'" />
		<input type="submit" value="进入"/>
		</form>';

	}else{
		return '网址：<input name="url'.$browser->rand.'" type="text" value="'.htmlspecialchars($url).'"/>
		<anchor>
		<go href="index.php" method="get">
		<postfield name="url" value="$(url'.$browser->rand.')" />
		</go>进入</anchor>';
	}
}
