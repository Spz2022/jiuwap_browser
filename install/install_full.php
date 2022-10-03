<?php
if ( !defined('DIR') ){
	die('hello world');
}

if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

$isfull = post('isfull',1);
$psw = isset($_REQUEST['psw']) ? $_REQUEST['psw'] : '';
if ( file_exists(DIR.'set_config/set_config.php') ){
	if ( $version && $version<'20110431' ){
		top('安装玖玩浏览器');
		echo '<b>重新安装玖玩浏览器</b>';
		echo hr;
		echo '请先将浏览器升级至20110431版再进行玖玩浏览器的重新(升级)安装！<a href="index.php?do=update">检测升级</a><br/>';
		foot();
		exit;
	}
	@include DIR.'set_config/set_config.php';
	@include DIR.'set_config/set_mail.php';
	$db_server = post('db_server',$b_set['db']['server']);
	$db_user = post('db_user',$b_set['db']['user']);
	$db_pass = post('db_pass',$b_set['db']['pass']);
	$db_table = post('db_table',$b_set['db']['table']);
	$icp = post('icp',$b_set['icp']);
	$title_str = post('title_str',$b_set['title_str']);
	$webtitle = post('webtitle',$b_set['webtitle']);
	$disktitle = post('disktitle',$b_set['disktitle']);
	$mail_smtp = post('mail_smtp',$b_set['mail']['smtp']);
	$mail_user = post('mail_user',$b_set['mail']['user']);
	$mail_pass = post('mail_pass',$b_set['mail']['pass']);
	$mail_from = post('mail_from',$b_set['mail']['from']);

	$tupload = post('tupload',floor($b_set['tupload']/1024/1024));
	$tdown = post('tdown',floor($b_set['tdown']/1024/1024));
	$tmail = post('tmail',floor($b_set['tmail']/1024/1024));
	$dlocal = post('dlocal',floor($b_set['dlocal']/1024/1024));
	$dhttp = post('dhttp',floor($b_set['dhttp']/1024/1024));
	$thttp = post('thttp',floor($b_set['thttp']/1024/1024));
	$dinit = post('dinit',floor($b_set['dinit']/1024/1024));


	if ( isset($b_set['rootpassword']) ){
		$rootpassword = post('rootpassword',$b_set['rootpassword']);
		if ( !isset($_REQUEST['psw']) || $_REQUEST['psw']<>$b_set['rootpassword'] ){
			top('安装玖玩浏览器');
			echo '<b>重新安装玖玩浏览器</b>';
			echo hr;
			if (  isset($_REQUEST['psw']) && $_REQUEST['psw']<>$b_set['rootpassword'] ){
				echo '密码错误！<br/>';
			}else{
				echo '需要验证超级密码！<br/>';
			}
			if ( !$iswml ){
				echo '<form action="index.php?do=install_full" method="post">';
			}
			echo '密码：<input type="text" value="" name="psw"/>';
			if ( !$iswml ){
				echo '<input type="submit" value="确认"/>';
				echo '</form>';
			}else{
				echo '<anchor>';
				echo '<go href="index.php?do=install_full" method="post">';
				echo '<postfield name="psw" value="$psw" />';
				echo '</go>确认</anchor>';
			}
			echo '<a href="index.php">返回</a><br/>';
			foot();
			exit;
		}

	}else{
		$rootpassword = post('rootpassword','jiuwap');

	}
	$是否是覆盖安装 = true;
}else{
	$db_server = post('db_server','localhost');
	$db_user = post('db_user','');
	$db_pass = post('db_pass','');
	$db_table = post('db_table','');
	$icp = post('icp','');
	$title_str = post('title_str','[压流]');
	$webtitle = post('webtitle','玖玩浏览器');
	$disktitle = post('disktitle','玖玩网盘');
	$mail_smtp = post('mail_smtp','smtp.qq.com');
	$mail_user = post('mail_user','10000@qq.com');
	$mail_pass = post('mail_pass','');
	$mail_from = post('mail_from','10000@qq.com');
	$tupload = post('tupload','10');
	$tdown = post('tdown','10');
	$tmail = post('tmail','10');
	$dlocal = post('dlocal','10');
	$dhttp = post('dhttp','10');
	$thttp = post('thttp','10');
	$dinit = post('dinit','50');
	$rootpassword = post('rootpassword','jiuwap');
	$是否是覆盖安装 = false;
}

if ( isset($_GET['yes']) ){
	$error = false;
	try{
		if ( !$db = @mysql_connect($db_server,$db_user,$db_pass)){
			throw new Exception('连接数据库失败,'.mysql_error());
		}
		@mysql_query('CREATE DATABASE `'.$db_table.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		if( !$conn2 = @mysql_select_db($db_table,$db)){
			throw new Exception('打开数据库失败,'.mysql_error());
		}
		if ( $isfull ){
			$是否是覆盖安装 = false;
			quick_connect('api/?cmd=clean_all&psw='.$psw);
			@mysql_query('DROP TABLE `browser_books`,`browser_caches`,`browser_copys`,`browser_users`,`disk_config`,`disk_dir`,`disk_file`,`browser_cookies`;');
		}
		if ( !$sql = @file_get_contents('jiuwap.sql') ){
			throw new Exception('读取jiuwap.sql时发生错误');
		}
		$sql = str_replace(array("\r","\n","\t"),'',$sql);
		$sql = explode(';',$sql);
		$n = count($sql);
		for($i=0;$i<$n ;$i++){
			!empty($sql[$i]) && @mysql_query($sql[$i]);
		}
		if ( $是否是覆盖安装 ){
			$nDir = array(
				DIR.'temp/cache_forever/',
				$b_set['dfforever'],
				$b_set['dftemp'],
				$b_set['rfile'],
				$b_set['rini'],
				$b_set['utemp'],
				$b_set['utemp'].'pics/',
			);
			$nPassKey = array(
				$b_set['key1'],
				$b_set['key2'],
				$b_set['key3'],
				$b_set['key4'],
			);
			$nDir2 = $nDir;
		}else{
			$tmp = strtolower(str_rand(7));
			$nDir = array(
				DIR.'temp/cache_forever/',
				DIR.'temp/disk_forever_'.$tmp.'/',
				DIR.'temp/disk_temp_'.$tmp.'/',
				DIR.'temp/down_file_'.$tmp.'/',
				DIR.'temp/down_ini_'.$tmp.'/',
				DIR.'temp/cache_'.$tmp.'/',
				DIR.'temp/cache_'.$tmp.'/pics/',
			);
			$nPassKey = array(
				str_rand(7),
				str_rand(7),
				str_rand(7),
				GET_LOGIN_KEY_LONG(),
			);
			$nDir2 = $nDir;
		}
		foreach( $nDir2 as $k=>$__Dir){
			$nDir2[$k] = 'DIR.\''.substr($__Dir,strlen(DIR)).'\'';
		}
		if ( !$set_config = @file_get_contents('set_config.tmp') ){
			throw new Exception('读取set_config.tmp时发生错误');
		}
		$set_config = str_replace('[server]',$db_server,$set_config);
		$set_config = str_replace('[user]',$db_user,$set_config);
		$set_config = str_replace('[pass]',$db_pass,$set_config);
		$set_config = str_replace('[table]',$db_table,$set_config);

		$set_config = str_replace('[webtitle]',$webtitle,$set_config);
		$set_config = str_replace('[disktitle]',$disktitle,$set_config);
		$set_config = str_replace('[icp]',$icp,$set_config);

		$set_config = str_replace('[tupload]',$tupload*1024*1024,$set_config);
		$set_config = str_replace('[tdown]',$tdown*1024*1024,$set_config);
		$set_config = str_replace('[tmail]',$tmail*1024*1024,$set_config);
		$set_config = str_replace('[dlocal]',$dlocal*1024*1024,$set_config);
		$set_config = str_replace('[thttp]',$thttp*1024*1024,$set_config);
		$set_config = str_replace('[dinit]',$dinit*1024*1024,$set_config);
		$set_config = str_replace('[dhttp]',$dhttp*1024*1024,$set_config);

		$set_config = str_replace('[dfforever]',$nDir2[1],$set_config);
		$set_config = str_replace('[dftemp]',$nDir2[2],$set_config);
		$set_config = str_replace('[rfile]',$nDir2[3],$set_config);
		$set_config = str_replace('[rini]',$nDir2[4],$set_config);
		$set_config = str_replace('[utemp]',$nDir2[5],$set_config);

		$set_config = str_replace('[key1]',$nPassKey[0],$set_config);
		$set_config = str_replace('[key2]',$nPassKey[1],$set_config);
		$set_config = str_replace('[key3]',$nPassKey[2],$set_config);
		$set_config = str_replace('[key4]',$nPassKey[3],$set_config);

		$set_config = str_replace('[title_str]',$title_str,$set_config);

		$set_config = str_replace('[mail_smtp]',$mail_smtp,$set_config);
		$set_config = str_replace('[mail_user]',$mail_user,$set_config);
		$set_config = str_replace('[mail_pass]',$mail_pass,$set_config);
		$set_config = str_replace('[mail_from]',$mail_from,$set_config);

		$set_config = str_replace('[rootpassword]',$rootpassword,$set_config);

		$set_config = str_replace('[tips]','安装于'.date('Y-n-j H:i:s'),$set_config);
		if ( !@file_put_contents(DIR.'set_config/set_config.php',$set_config) ){
			throw new Exception('保存set_config.php时发生错误');
		}
		@unlink(DIR.'set_config/set_mail.php');

		$ver="<?php
		//请不要修改或删除本文件!!
		//请不要干扰版本号!!
		//以免造成浏览器无法升级!!
		//删除本文件后即可重新安装！！
		\$version = '{$install_version}';";
		if ( !@file_put_contents(DIR.'set_config/version.php',$ver) ){
			throw new Exception('保存version.php时发生错误');
		}
		foreach($nDir as $dir){
			mkdirs($dir);
		}
		top('安装完成 - 安装玖玩浏览器');
		echo '<b>安装玖玩浏览器<br/>安装完成</b>';
		echo hr;
		echo '恭喜，完整安装玖玩浏览器成功！！<br/>';
		echo '当前版本：'.$install_version.'<br/>';
		echo '提示：如需重新安装，请删除set_config/version.php然后重新运行本页面！你也可以随时运行本页面检测查询是否有新版本,本程序会自动升级！！<br/>';
		echo '<a href="index.php">返回(自动升级到最新版)</a><br/>';
		foot();
		exit;
	}catch(Exception $e){
		$error = $e->getMessage();
	}

}

top('第二步,安装设置 - 安装玖玩浏览器');

echo '<b>安装玖玩浏览器<br/>第二步,安装设置(本操作将保留原数据)</b><br/>';
if ( isset($error) && $error ){
	echo '<b>错误：'.$error.'</b><br/>';
}

if ( !$iswml ){
	echo '<form action="index.php?do=install_full&amp;yes=yes&amp;psw='.$psw.'" method="post">';
}

echo hr;
echo '<b>超级密码:</b><br/>';
echo '请务必牢记本密码，当前超级密码只用于在线重装！<br/><input type="text" name="rootpassword" value="'.$rootpassword.'" /><br/>';

echo '<select name="isfull">
<option value="0">重新安装文件(保留数据)</option>
<option value="1">完全重新安装(清空数据)</option>
</select>';

echo hr;
echo '<b>MYSQL数据库:</b><br/>';
echo '地址：<input type="text" name="db_server" value="'.$db_server.'" /><br/>';
echo '账号：<input type="text" name="db_user" value="'.$db_user.'" /><br/>';
echo '密码：<input type="text" name="db_pass" value="'.$db_pass.'" /><br/>';
echo '库名：<input type="text" name="db_table" value="'.$db_table.'" /><br/>';

echo hr;
echo '<b>系统设置:</b><br/>';
echo '浏览器名称：<input type="text" name="webtitle" value="'.$webtitle.'" /><br/>';
echo '网盘名称：<input type="text" name="disktitle" value="'.$disktitle.'" /><br/>';
echo '备案信息：<input type="text" name="icp" value="'.$icp.'" /><br/>';
echo '标题前缀：<input type="text" name="title_str" value="'.$title_str.'" /><br/>';

echo hr;
echo '<b>邮箱设置:</b><br/>';
echo 'SMTP：<input type="text" name="mail_smtp" value="'.$mail_smtp.'" /><br/>';
echo '登录账号：<input type="text" name="mail_user" value="'.$mail_user.'" /><br/>';
echo '登录密码：<input type="text" name="mail_pass" value="'.$mail_pass.'" /><br/>';
echo '发信地址：<input type="text" name="mail_from" value="'.$mail_from.'" /><br/>';

echo hr;
echo '<b>最大值(单位MB):</b><br/>';
echo '直接中转上传：<input type="text" name="tupload" value="'.$tupload.'" /><br/>';
echo '直接中转下载：<input type="text" name="tdown" value="'.$tdown.'" /><br/>';
echo '邮件附件发送：<input type="text" name="tmail" value="'.$tmail.'" /><br/>';
echo '网盘本地上传：<input type="text" name="dlocal" value="'.$dlocal.'" /><br/>';
echo '网盘远程上传：<input type="text" name="dhttp" value="'.$dhttp.'" /><br/>';
echo '网盘中转上传：<input type="text" name="thttp" value="'.$thttp.'" /><br/>';
echo '网盘初始大小：<input type="text" name="dinit" value="'.$dinit.'" /><br/>';

echo hr;
echo '<b>※提示：请确保上述文本框所属内容正确无误！</b><br/>';
if ( !$iswml ){
	echo '<input type="submit" value="确认,下一步"/>';
	echo '</form>';
}else{
	echo '<anchor>';
	echo '<go href="index.php?do=install_full&amp;yes=yes&amp;psw='.$psw.'" method="post">';
	echo '<postfield name="tupload" value="$tupload" />';
	echo '<postfield name="tdown" value="$tdown" />';
	echo '<postfield name="tmail" value="$tmail" />';
	echo '<postfield name="dlocal" value="$dlocal" />';
	echo '<postfield name="dhttp" value="$dhttp" />';
	echo '<postfield name="thttp" value="$thttp" />';
	echo '<postfield name="dinit" value="$dinit" />';
	echo '<postfield name="db_server" value="$db_server" />';
	echo '<postfield name="db_user" value="$db_user" />';
	echo '<postfield name="db_pass" value="$db_pass" />';
	echo '<postfield name="db_table" value="$db_table" />';
	echo '<postfield name="webtitle" value="$webtitle" />';
	echo '<postfield name="disktitle" value="$disktitle" />';
	echo '<postfield name="mail_smtp" value="$mail_smtp" />';
	echo '<postfield name="mail_user" value="$mail_user" />';
	echo '<postfield name="mail_pass" value="$mail_pass" />';
	echo '<postfield name="mail_from" value="$mail_from" />';
	echo '<postfield name="icp" value="$icp" />';
	echo '<postfield name="rootpassword" value="$rootpassword" />';
	echo '<postfield name="isfull" value="$isfull" />';
	echo '</go>确认,下一步</anchor>';
}
foot();