<?php
define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
if ( isset($_GET['yes']) ){
	$_POST['name'] = isset($_POST['name']) ? trim($_POST['name']) : NULL;
	$_POST['pass'] = isset($_POST['pass']) ? trim($_POST['pass']) : NULL;
	$_POST['pass1'] = isset($_POST['pass1']) ? trim($_POST['pass1']) : NULL;
	$var = $browser->_user_name_check($_POST['name'],$_POST['pass1']);
	if ( !$var){
		if ( strtolower($_POST['name']) == 'jiuwap' ){
			$var = false;
		}else{
			$var = $browser->db->fetch_first('SELECT pass,id FROM `browser_users` WHERE name="'.$_POST['name'].'"');
		}
		if ( !$var ){
			$var = '用户名不存在!';
		}else{
			if ( $var['pass'] == $_POST['pass'] ){
				$browser->db->query('UPDATE `browser_users` SET pass="'.$_POST['pass1'].'" WHERE id='. $var['id']);
				$var = false;
			}else{
				$var = '密码错误!';
			}
		}
	}
	if ( !$var ){
		$browser->template_top('修改密码','/?r='.$browser->rand);
		echo '修改密码成功！<br /><a href="/?r='.$browser->rand.'">返回登录</a>';
	}else{
		$browser->template_top('修改密码');
		echo '修改密码失败，'.$var.'<br /><a href="reg_repass.php?'.$browser->rand.'">返回修改</a>';
	}
	$browser->template_foot();
}


$browser->template_top('修改密码');
echo $b_set['webtitle'].'-修改密码'.hr;
if ( $browser->template == 0 ){
		echo '<form action="reg_repass.php?yes=yes&r='.$browser->rand.'" method="post">
		账号：<input type="text" name="name" value="" /><br />
		旧密码：<input type="text" name="pass" value="" /><br />
		新密码：<input type="text" name="pass1" value="" /><br />
		<input type="submit" value="修改"/>
		</form>';
}else{
	echo '账号：<input name="name" type="text" value=""/><br/>
	旧密码：<input type="text" name="pass" value="" /><br />
	新密码：<input type="text" name="pass1" value="" /><br />
	<anchor>
	<go href="reg_repass.php?yes=yes&amp;r='.$browser->rand.'" method="post">
	<postfield name="name" value="$name" />
	<postfield name="pass" value="$pass" />
	<postfield name="pass1" value="$pass1" />
	</go>修改</anchor><br/>';
}
echo '<br /><a href="login.php?r='.$browser->rand.'">返回登录</a>'.hr;
echo 'Powered By <a href="http://jiuwap.cn/">Jiuwap.cn</a><br/>';
echo $b_set['icp'];
$browser->template_foot();