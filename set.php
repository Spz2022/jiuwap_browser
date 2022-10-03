<?php
define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';

$h = isset($_GET['h']) ? $_GET['h'] : '';
if ( $h <> ''){
	$au = '&amp;h='.$h;
}else{
	$au = '';
}

function selectit($name,$value,$str = ' checked="checked"'){
	global $browser;
	if ( $browser->$name == $value){
		return $str;
	}
}

$browser->user_login_check();

$browser->template_top('设置');

if ( isset($_GET['old']) ){
	$browser->set_default();
	echo '恢复默认设置成功。<br/><a href="set.php?'.$au.'">返回设置</a>';
	echo '<br/><a href="/?h='.$h.'">返回浏览器</a>';
	$browser->template_foot();
}elseif( isset($_GET['yes']) ){
	$_POST['template_foot'] = isset($_POST['template_foot']) ? trim($_POST['template_foot']) : '';
	$_POST['ipagent'] = isset($_POST['ipagent']) ? trim($_POST['ipagent']) : '';
	$_POST['pic'] = isset($_POST['pic']) ? (int)$_POST['pic'] : 3;
	$_POST['useragent'] = isset($_POST['useragent']) ? (int)$_POST['useragent'] : 0;
	$_POST['wap2wml'] = isset($_POST['wap2wml']) ? (int)$_POST['wap2wml'] : 0;
	$_POST['pic_wap'] = isset($_POST['pic_wap']) ? (int)$_POST['pic_wap'] : 1;
	$_POST['ipagent_open'] = isset($_POST['ipagent_open']) ? (int)$_POST['ipagent_open'] : 0;
	if ($_POST['useragent']<0 ){
		$_POST['useragent'] = 0;
	}
	if ($_POST['pic']<0 || $_POST['pic']>8){
		$_POST['pic'] = 3;
	}
	if ($_POST['wap2wml']<0 || $_POST['wap2wml']>4){
		$_POST['wap2wml'] = 0;
	}
	if ($_POST['pic_wap']<>0 && $_POST['pic_wap']<>1){
		$_POST['pic_wap'] = 1;
	}
	if ($_POST['ipagent_open']<>0 && $_POST['ipagent_open']<>1){
		$_POST['ipagent_open'] = 1;
	}
	if( !empty($_POST['ipagent']) ){
		$_POST['ipagent'] = str_replace("\t",'',$_POST['ipagent']);
		$_POST['ipagent'] = str_replace("\n",'',$_POST['ipagent']);
		$_POST['ipagent'] = str_replace("\r",'',$_POST['ipagent']);
		$_POST['ipagent'] = str_replace(array(' ','：'),':',$_POST['ipagent']);
		if ( strpos($_POST['ipagent'],':')===false ) {
			$_POST['ipagent'] .= ':80';
		}
	}
	if ( strlen($_POST['template_foot']) > 80 ){
		$error = '浏览器底部定制内容太长，不得大于80个字符！';
	}elseif ( $_POST['ipagent_open']==1 && !empty($_POST['ipagent']) && $browser->set_ipagent_check($_POST['ipagent'],true) == false ){
		$error = 'HTTP代理连接失败';
	}else{
		$error = '';
	}
	if ( $error == ''){
		$var = array(
				'config_ipagent'	=>	$_POST['ipagent'],
				'config_useragent'	=>	$_POST['useragent'],
				'config_pic'		=>	$_POST['pic'],
				'config_wap2wml'	=>	$_POST['wap2wml'],
				'config_pic_wap'	=>	$_POST['pic_wap'],
				'template_foot'		=>	$_POST['template_foot'],
				'config_ipagent_open'=>	$_POST['ipagent_open'],
		);
		$browser->cacheurl_del('pic');
		$browser->set_config($var);
		echo '保存设置成功。<br/>';
		echo '返回:<a href="set.php?'.$au.'">设置</a>.';
		if ( $h<>'' ){
			echo '<a href="/?h='.$h.'">网页</a>.';
		}
		echo '<a href="/?m='.$h.'">菜单</a>.<a href="/">首页</a><br />';
	}else{
		echo '保存设置失败。<br/>
			提示：'.$error.'<br/>';
		echo '返回:<a href="set.php?'.$au.'">设置</a>.';
		if ( $h<>'' ){
			echo '<a href="/?h='.$h.'">网页</a>.';
		}
		echo '<a href="/?m='.$h.'">菜单</a>.<a href="/">首页</a><br />';
	}
	$browser->template_foot();

}

if ( $browser->template == 0 ){

echo '<form action="set.php?yes=yes'.$au.'" method="post">';
echo $b_set['webtitle'].'-设置<br />';
echo '返回:';
if ( $h<>'' ){
	echo '<a href="/?h='.$h.'">网页</a>.';
}
echo '<a href="/?m='.$h.'">菜单</a>.<a href="/">首页</a><br />'
?>
<?=hr?>
图片浏览：
<select name="pic">
	<option value="0"<?=selectit('pic','0',' selected="selected"')?>>无图(有提示)</option>
	<option value="1"<?=selectit('pic','1',' selected="selected"')?>>无图(无提示)</option>
	<option value="2"<?=selectit('pic','2',' selected="selected"')?>>压缩(20%)</option>
	<option value="7"<?=selectit('pic','7',' selected="selected"')?>>压缩(50%)</option>
	<option value="3"<?=selectit('pic','3',' selected="selected"')?>>压缩(70%)</option>
	<option value="8"<?=selectit('pic','8',' selected="selected"')?>>压缩(80%)</option>
	<option value="6"<?=selectit('pic','6',' selected="selected"')?>>原图(中转)</option>
	<option value="4"<?=selectit('pic','4',' selected="selected"')?>>原图(直显)</option>
	<option value="5"<?=selectit('pic','5',' selected="selected"')?>>验证码(中转)</option>
</select><br />
<input type='checkbox' name='pic_wap' value="0"<?=selectit('pic_wap','0')?>/>不压缩WAP图片<br />
提示：浏览大图时推荐使用中转，小图片这样的网页建议用原图(只显)，为了节省流量，更推荐使用验证码(中转)！压缩百分比越小，压缩后的图片越小。
<?=hr?>
模拟UA：
<select name="useragent">
	<option value="0"<?=selectit('useragent','0',' selected="selected"')?>>QQ浏览器(WAP)</option>
	<option value="1"<?=selectit('useragent','1',' selected="selected"')?>>UC浏览器(WAP)</option>
	<option value="2"<?=selectit('useragent','2',' selected="selected"')?>>IE浏览器(WEB)</option>
	<option value="3"<?=selectit('useragent','3',' selected="selected"')?>>FF浏览器(WEB)</option>
	<option value="4"<?=selectit('useragent','4',' selected="selected"')?>>OP浏览器(WEB)</option>
	<option value="5"<?=selectit('useragent','5',' selected="selected"')?>>JIUWAP(WAP)</option>
	<option value="6"<?=selectit('useragent','6',' selected="selected"')?>>移动模拟(WAP)</option>
	<option value="7"<?=selectit('useragent','7',' selected="selected"')?>>Iphone4</option>
	<option value="8"<?=selectit('useragent','8',' selected="selected"')?>>QQ浏览器(S60V5-5230)</option>
	<option value="9"<?=selectit('useragent','9',' selected="selected"')?>>测试专用(JIUWAP浏览器特权)</option>
</select>
<?=hr?>
页面转换：
<select name="wap2wml">
	<option value="0"<?=selectit('wap2wml','0',' selected="selected"')?>>适应WAP浏览</option>
	<option value="1"<?=selectit('wap2wml','1',' selected="selected"')?>>WEB转WAP2</option>
	<option value="2"<?=selectit('wap2wml','2',' selected="selected"')?>>WAP转WEB</option>
	<option value="3"<?=selectit('wap2wml','3',' selected="selected"')?>>WEB/WAP2转WAP1</option>
	<option value="4"<?=selectit('wap2wml','4',' selected="selected"')?>>不处理页面</option>
</select>
<?=hr?>
HTTP代理：<input type='checkbox' name='ipagent_open' value="1"<?=selectit('ipagent_open','1')?>/>开启<br />
格式：“地址:端口”<br/>
或者：“地址 端口”<br/>

代理IP：<input type="text" name="ipagent" value="<?=$browser->ipagent?>" />
<?=hr?>
底部定制：
<input type="text" name="template_foot" value="<?=$browser->template_foot?>" /><br/>
<a href="self/code.php">(查看定制代码)</a>
<?=hr?>
<input type="submit" value="确认"/>(返回网页不会立即生效)<br />
<a href="set.php?old=true<?=$au?>">恢复默认设置</a>
<?=hr?>
界面：<a href="wap.php?back=set&wap=1<?=$au?>">WAP1.1</a>|WAP2.0
</form>

<?php
}else{
echo $b_set['webtitle'].'-设置<br />';
echo '返回:';
if ( $h<>'' ){
	echo '<a href="/?h='.$h.'">网页</a>.';
}
echo '<a href="/?m='.$h.'">菜单</a>.<a href="/">首页</a>';
echo hr;

?>图片浏览：
<select name="pic<?=$browser->rand?>">
	<option value="0"<?=selectit('pic','0',' selected="selected"')?>>无图(有提示)</option>
	<option value="1"<?=selectit('pic','1',' selected="selected"')?>>无图(无提示)</option>
	<option value="2"<?=selectit('pic','2',' selected="selected"')?>>压缩(20%)</option>
	<option value="7"<?=selectit('pic','7',' selected="selected"')?>>压缩(50%)</option>
	<option value="3"<?=selectit('pic','3',' selected="selected"')?>>压缩(70%)</option>
	<option value="8"<?=selectit('pic','8',' selected="selected"')?>>压缩(80%)</option>
	<option value="6"<?=selectit('pic','6',' selected="selected"')?>>原图(中转)</option>
	<option value="4"<?=selectit('pic','4',' selected="selected"')?>>原图(直显)</option>
	<option value="5"<?=selectit('pic','5',' selected="selected"')?>>验证码(中转)</option>
</select><br />
压缩：
<select name="pic_wap<?=$browser->rand?>">
	<option value="0"<?=selectit('pic_wap','0',' selected="selected"')?>>压缩WAP图片</option>
	<option value="1"<?=selectit('pic_wap','1',' selected="selected"')?>>不压缩WAP图片</option>
</select><br />
提示：浏览大图时推荐使用中转，小图片这样的网页建议用原图(只显)，为了节省流量，更推荐使用验证码(中转)！压缩百分比越小，压缩后的图片越小。
<?=hr?>
模拟UA：
<select name="useragent<?=$browser->rand?>">
	<option value="0"<?=selectit('useragent','0',' selected="selected"')?>>QQ浏览器(WAP)</option>
	<option value="1"<?=selectit('useragent','1',' selected="selected"')?>>UC浏览器(WAP)</option>
	<option value="2"<?=selectit('useragent','2',' selected="selected"')?>>IE浏览器(WEB)</option>
	<option value="3"<?=selectit('useragent','3',' selected="selected"')?>>FF浏览器(WEB)</option>
	<option value="4"<?=selectit('useragent','4',' selected="selected"')?>>OP浏览器(WEB)</option>
	<option value="5"<?=selectit('useragent','5',' selected="selected"')?>>JIUWAP(WAP)</option>
	<option value="6"<?=selectit('useragent','6',' selected="selected"')?>>移动模拟(WAP)</option>
</select>
<?=hr?>
页面转换：
<select name="wap2wml">
	<option value="0"<?=selectit('wap2wml','0',' selected="selected"')?>>适应WAP浏览</option>
	<option value="1"<?=selectit('wap2wml','1',' selected="selected"')?>>WEB转WAP2</option>
	<option value="2"<?=selectit('wap2wml','2',' selected="selected"')?>>WAP转WEB</option>
	<option value="3"<?=selectit('wap2wml','3',' selected="selected"')?>>WEB/WAP2转WAP1</option>
	<option value="4"<?=selectit('wap2wml','4',' selected="selected"')?>>不处理页面</option>
</select>
<?=hr?>
HTTP代理：
<select name="ipagent_open<?=$browser->rand?>">
	<option value="1"<?=selectit('ipagent_open','1',' selected="selected"')?>>开启</option>
	<option value="0"<?=selectit('ipagent_open','0',' selected="selected"')?>>关闭</option>
</select><br />
格式：“IP地址:端口”<br/>
或者：“IP地址 端口”<br/>
代理IP：<input type="text" name="ipagent<?=$browser->rand?>" value="<?=$browser->ipagent?>" />
<?=hr?>
底部定制：
<input type="text" name="template_foot<?=$browser->rand?>" value="<?=$browser->template_foot?>" /><br/>
<a href="self/code.php">(查看定制代码)</a>
<?=hr?>
<anchor>
<go href="set.php?yes=yes<?=$au?>" method="post">
<postfield name="template_foot" value="$(template_foot<?=$browser->rand?>)" />
<postfield name="ipagent" value="$(ipagent<?=$browser->rand?>)" />
<postfield name="wap2wml" value="$(wap2wml<?=$browser->rand?>)" />
<postfield name="useragent" value="$(useragent<?=$browser->rand?>)" />
<postfield name="ipagent_open" value="$(ipagent_open<?=$browser->rand?>)" />
<postfield name="pic" value="$(pic<?=$browser->rand?>)" />
</go>确认</anchor>(返回网页不会立即生效)<br/>
<a href="set.php?old=true<?=$au?>">恢复默认设置</a>
<?=hr?>
界面：WAP1.1|<a href="wap.php?back=set&amp;wap=0<?=$au?>">WAP2.0</a>
<?php
}

$browser->template_foot();