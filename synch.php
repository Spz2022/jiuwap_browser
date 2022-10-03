<?php
/*
 *
 *	浏览器->历史
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
$browser->user_login_check();

$host = isset($_POST['host']) ? strtolower(trim($_POST['host'])) : null;
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$pass = isset($_POST['pass']) ? trim($_POST['pass']) : null;

$browser->template_top('数据同步');

echo $b_set['webtitle'].'-同步(<a href="/?r='.$browser->rand.'">返回</a>)';
echo hr;
if ( isset( $_GET['yes'] ) ){
	$cover = isset($_POST['cover']) ? $_POST['cover'] : 1;
	if ( !$host || !$name || !$pass ){
		echo '错误：表单内容不能为空！'.hr;
	}elseif( $err = $browser->_user_name_check($name,$pass) ){
		echo '错误：'.$err.hr;
	}elseif( isset($_SERVER['SERVER_NAME']) && $host == $_SERVER['SERVER_NAME'] ){
		echo '错误：无法同步本浏览器的数据'.hr;
	}elseif( !@gethostbyname($host) ){
		echo '错误：域名解析错误'.hr;
	}else{
		quick_connect('api/?cmd=synch&host='.urlencode($host).'&pass='.urlencode($pass).'&name='.urlencode($name).'&cover='.urlencode($cover).'&key='.urlencode($_COOKIE['FREE']));
		echo '创建同步任务成功！系统将在数分钟内同步完成！此期间请不要再执行同步操作！以免数据混论导致账号无法使用。';
		$browser->template_foot();
	}
}
if ( $browser->template == 0 ){
	echo '
	<form action="synch.php?yes=yes" method="post">
	域名：<input type="text" name="host" value="'.$host.'" /><br />
	账号：<input type="text" name="name" value="'.$name.'" /><br />
	密码：<input type="text" name="pass" value="'.$pass.'" /><br />
	<input type="checkbox" name="cover" value="1" checked="checked">覆盖同名或同网址书签<br />
	<input type="submit" value="同步"/><br />
	</form>
	';
}else{
	echo '
	域名：<input type="text" name="host'.$browser->rand.'" value="'.$host.'" /><br />
	账号：<input type="text" name="name'.$browser->rand.'" value="'.$name.'" /><br />
	密码：<input type="text" name="pass'.$browser->rand.'" value="'.$pass.'" /><br />
	<select name="cover'.$browser->rand.'">
		<option value="0">覆盖同名或同网址书签</option>
		<option value="1" selected="selected">不覆盖同名或同网址书签</option>
	</select><br />
	<anchor>
	<go href="synch.php?yes=yes" method="post">
	<postfield name="title" value="$(title'.$browser->rand.')" />
	<postfield name="name" value="$(name'.$browser->rand.')" />
	<postfield name="pass" value="$(pass'.$browser->rand.')" />
	<postfield name="cover" value="$(cover'.$browser->rand.')" />
	</go>同步</anchor>';
}

echo hr.'提示:(域名无需http://)本服务只是用于20110901(包括)以上版本的玖玩浏览器系统架设的网页浏览器,例如你想同步mm.jiuwap.cn的数据到本浏览器,那么你只需要输入域名“mm.jiuwap.cn”，再输入你在mm.jiuwap.cn的账号和密码，点同步即可将mm.jiuwap.cn的书签和COOKIES保存到本站。(如果数据过多可能需要等待几分钟才能完全同步，请不要多次同步，一面同步混乱导致数据丢失甚至损坏)';
$browser->template_foot();