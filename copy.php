<?php
/*
 *
 *	浏览器->剪切板
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
$browser->user_login_check();

$h = isset($_GET['h']) ? $_GET['h'] : '';
if ( $h <> ''){
	$au = '&amp;h='.$h;
}else{
	$au = '';
}

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

if ( $cmd == 'copy_title' || $cmd == 'copy_content2' || $cmd == 'copy_url'){
	$arr = $browser->history_get($h);
	if ( $arr === false ){
		error_book('复制失败','复制失败(历史缓存丢失['.$h.'])');
	}
	switch ( $cmd ){
	case 'copy_title':
		$content = $arr['title'];
		$type = '复制标题';
		$cmd = '_copy_form_';
		break;
	case 'copy_url':
		$content = $arr['url'];
		$type = '复制网址';
		$cmd = '_copy_form_';
		break;
	case 'copy_content2':
		$content = $arr['content'];
		@$content = iconv($arr['code'],'utf-8//TRANSLIT', $content);
		break;
	}
	unset($arr);
	if ( $cmd == 'copy_content2'){
		if ( $browser->copy_num() >= 20 ){
			error_book('复制失败','复制失败(剪切板最大容纳20条，请进入剪切板清理内容)');
		}
		$start = isset($_POST['start']) ? $_POST['start'] : '';
		$end = isset($_POST['end']) ? $_POST['end'] : '';
		$nnn = isset($_POST['nnn']) ? (int)$_POST['nnn'] : 0;
		if ( $nnn < 0 || $nnn > 9 ){
			$nnn = 0;
		}
		$type = '复制内容';
		$cmd = '_copy_form_';
		$content = $browser->copy_get($content,$start,$end,$nnn);
	}
}elseif ( $cmd == 'copy' ){
	$browser->template_top('复制');
	echo ''.$b_set['webtitle'].'-复制<br />';
	echo '<a href="copy.php?cmd=copy_title&amp;h='.$h.'">复制网页标题</a><br/>';
	echo '<a href="copy.php?cmd=copy_url&amp;h='.$h.'">复制网页地址</a><br/>';
	echo '<a href="copy.php?cmd=copy_content&amp;h='.$h.'">复制网页内容</a>';
	echo hr;
	echo '返回:<a href="/?h='.$h.'">网页</a>.';
	echo '<a href="/?m='.$h.'">菜单</a>.';
	echo '<a href="copy.php?h='.$h.'">剪切板</a><br/>';
	$browser->template_foot();
}elseif ( $cmd == 'myadd' ){
	$content = isset($_POST['content']) ? $_POST['content'] : '';
	if ( isset($_GET['yes']) && $content ){
		$type = '自写剪切板';
		$cmd = '_copy_form_';
	}else{
		$browser->template_top('自写剪切板内容');
		echo '返回：<a href="copy.php?h='.$h.'">剪切板</a>.';
		if ( $h<>'' ){
			echo '<a href="/?h='.$h.'">网页</a>.';
		}
		echo '<a href="/?m='.$h.'">菜单</a>';
		echo hr;
		echo '自写剪切板内容<br />';
		if ( isset($_GET['yes']) && !$content ){
			echo '提示：内容不得为空！';
			echo hr;
		}
		if ( $browser->template == 1 ){
			echo '
			内容：<input name="content'.$browser->rand.'" type="text" value=""/><br/>
			<anchor>
			<go href="copy.php?cmd=myadd&amp;yes=yes&amp;h='.$h.'" method="post">
			<postfield name="content" value="$(content'.$browser->rand.')" />
			</go>添加</anchor><br/>
			';
		}else{
			echo '<form action="copy.php?cmd=myadd&amp;yes=yes&amp;h='.$h.'" method="post">
			内容：<textarea name="content" cols="20" rows="3"></textarea><br/>
			<input type="submit" value="添加"/><br />
			</form>';
		}
		$browser->template_foot();
	}

}elseif ( $cmd == 'copy_content'){
	$browser->template_top('复制内容');
	echo '复制内容'.hr;
	if ( $browser->template == 1 ){
		echo '
		开头：<input name="start'.$browser->rand.'" type="text" value=""/><br/>
		结束：<input name="end'.$browser->rand.'" type="text" value=""/><br/>
		换行：<select name="nnncopyform">
			<option value="1">[br/]</option>
			<option value="2">[br]</option>
			<option value="3">(br)</option>
			<option value="4">///</option>
			<option value="5">//</option>
			<option value="6">\\\\</option>
			<option value="7">&lt;br&gt;</option>
			<option value="8">&lt;br/&gt;</option>
			<option value="0">无</option>
			<option value="9" selected="selected">\r\n</option>
		</select><br />
		<anchor>
		<go href="copy.php?cmd=copy_content2&amp;h='.$h.'" method="post">
		<postfield name="start" value="$(start'.$browser->rand.')" />
		<postfield name="end" value="$(end'.$browser->rand.')" />
		<postfield name="nnn" value="$nnncopyform" />
		</go>复制</anchor>
		';
	}else{
		echo '<form action="copy.php?cmd=copy_content2&amp;h='.$h.'" method="post">
		开头：<input name="start" type="text" value=""/><br/>
		结束：<input name="end" type="text" value=""/><br/>
		换行：<select name="nnn">
			<option value="1">[br/]</option>
			<option value="2">[br]</option>
			<option value="3">(br)</option>
			<option value="4">///</option>
			<option value="5">//</option>
			<option value="6">\\\\</option>
			<option value="7">&lt;br&gt;</option>
			<option value="8">&lt;br/&gt;</option>
			<option value="0">无</option>
			<option value="9" selected="selected">\r\n</option>
		</select><br />
		<input type="submit" value="复制"/><br />
		</form>';
	}
	echo hr;
	echo '说明：复制网页里的某段文字，输入要复制的内容的开头和结束的部分文字，如果开头和结束都留空则复制整页内容！<br />';
	echo '注意：只能复制您最后一次浏览网页内容，且不包括html代码，仅限文字部分。<br />';
	echo '提示：如果网页内容里含有换行，则转换为您选择的换行代码！';
	echo hr;
	echo '<a href="copy.php?cmd=copy&amp;h='.$h.'">返回</a>.';
	echo '<a href="/?h='.$h.'">网页</a>.';
	echo '<a href="/?m='.$h.'">菜单</a>.';
	echo '<a href="copy.php?h='.$h.'">剪切板</a><br/>';
	$browser->template_foot();

}elseif ( $cmd == 'delall' ) {
	$browser->template_top('清空剪切板');
	$browser->copy_del();
	echo '清理剪切板内容完毕。<br/>';
	echo '返回：<a href="copy.php?h='.$h.'">剪切板</a>.';
	if ( $h<>'' ){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>';

	$browser->template_foot();

}elseif ( $cmd == 'del' ) {
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$arr = $browser->copy_look($id);
	if ( empty($arr) ){
		error_book('提示','该剪切板内容不存在，或者已被删除。');
	}
	$browser->template_top('删除剪切板');
	echo '<a href="copy.php?h='.$h.'">剪切板</a>.';
	if ( $h<>''){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>';
	echo '<br/>';


	if ( !isset($_GET['yes']) ){
		echo '确认删除[copy='.$id.']？<br/>';
		echo '<a href="copy.php?cmd=del&amp;id='.$id.'&amp;yes=yes&amp;h='.$h.'">确认</a>';
	}else{
		$browser->copy_del($id);
		echo '删除[copy='.$id.']成功。';
	}
	$browser->template_foot();

}elseif ( $cmd == 'change' ) {
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$arr = $browser->copy_look($id);
	$content = isset($_POST['content']) ? $_POST['content'] : '';
	if ( empty($arr) ){
		error_book('提示','该剪切板内容不存在，或者已被删除。');
	}elseif ( empty($content) ){
		$str = '修改失败，内容不得为空。';
	}else{
		$browser->copy_change($id,$content);
		$str = '修改成功。';
	}

	$browser->template_top('剪切板内容');
	echo '<a href="copy.php?h='.$h.'">剪切板</a>.';
	if ( $h<>''){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>';
	echo hr;
	echo $str.'<br/>';
	echo '<a>[copy='.$id.']</a><a href="copy.php?cmd=look&amp;id='.$id.'&amp;h='.$h.'">查看</a><br/>';
	$browser->template_foot();

}elseif ( $cmd == 'look' ) {
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$arr = $browser->copy_look($id);
	if ( empty($arr) ){
		error_book('提示','该剪切板内容不存在，或者已被删除。');
	}
	$key = $arr['id'];
	$content = $arr['content'];
	$browser->template_top('剪切板内容');
	echo '<a href="copy.php?h='.$h.'">剪切板</a>.';
	if ( $h<>''){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>';
	echo hr;
	if ( isset($_GET['change'])){
		if ( $browser->template == 1 ){
			echo '
			代码：<a>[copy='.$key.']</a><br/>
			内容：<input name="content'.$browser->rand.'" type="text" value="'.$content.'"/><br/>
			<anchor>
			<go href="copy.php?cmd=change&amp;id='.$key.'&amp;h='.$h.'" method="post">
			<postfield name="content" value="$(content'.$browser->rand.')" />
			</go>修改</anchor><br/>
			';
		}else{
			echo '<form action="copy.php?cmd=change&amp;id='.$key.'&amp;h='.$h.'" method="post">
			代码：<a>[copy='.$key.']</a><br/>
			内容：<textarea name="content" cols="20" rows="3">'.$content.'</textarea><br/>
			<input type="submit" value="修改"/><br />
			</form>';

		}
		if ( isset($_GET['change'])){
			if ( strlen($content) > 250 ){
				echo '内容过长，部分手机不支持长文本编辑，修改会造成内容不全。<br />';
			}
		}
	}else{
		echo '<a href="copy.php?cmd=look&amp;change=y&amp;id='.$key.'&amp;h='.$h.'">修改</a>.
			<a href="copy.php?cmd=del&amp;id='.$id.'&amp;h='.$h.'">删除</a>.
			<a>[copy='.$key.']</a>'.hr;
		$content = str_replace("\r\n",'<br/>',$content);
		$content = htmlspecialchars($content);
		$content = str_replace('&lt;br/&gt;','<br/>',$content);
		echo $content.hr.'只需在文本框输入<a>[copy='.$key.']</a>，表单提交时即可粘贴。';
	}
	$browser->template_foot();


}else{
	$arr = $browser->copy_lists();
	$browser->template_top('剪切板');
	echo '返回:';
	if ( $h <> '' ){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>.';
	echo '<a href="/">首页</a>'.hr;
	echo '<a href="copy.php?cmd=myadd&amp;h='.$h.'">自写</a>.';
	echo '<a href="copy.php?cmd=delall&amp;h='.$h.'">清空</a>('.$arr[0].'条)';
	echo hr;
	if ( $arr[0] == 0 ){
		echo '剪切板为空';
	}else{
		foreach($arr[1] as $val){
			if ( strlen($val['content'])<=50 ){
				$val['content'] = $val['content'];
			}else{
				$val['content'] = fixchinese(substr($val['content'],0,10).'…'.substr($val['content'],strlen($val['content']) - 10));
			}
			echo '['.$val['id'].']<a href="copy.php?cmd=look&amp;h='.$h.'&amp;id='.$val['id'].'">'.$val['content'].'</a><br/>';
		}
		echo hr.'只需在文本框输入<a>[copy=号码]</a>，表单提交时即可粘贴。';
	}

	$browser->template_foot();
}

if ( $cmd == '_copy_form_'){
	$key = $browser->copy_add($content);
	$content = htmlspecialchars($content);
	$browser->template_top($type);
	if ( strlen($content)>50 ){
		$content = fixchinese(substr($content,0,10).'…'.substr($content,strlen($content) - 10));
	}
	echo $type.'成功<br />-------------<br />';
	echo '查看：<a href="copy.php?cmd=look&amp;h='.$h.'&amp;id='.$key.'">'.$content.'</a>';
	echo hr.'只需在文本框输入<a>[copy='.$key.']</a>，表单提交时即可粘贴。'.hr;
	echo '<a href="copy.php?cmd=copy&amp;h='.$h.'">返回</a>.';
	if ( $h<>''){
		echo '<a href="/?h='.$h.'">网页</a>.';
	}
	echo '<a href="/?m='.$h.'">菜单</a>.';
	echo '<a href="copy.php?h='.$h.'">剪切板</a><br/>';
	$browser->template_foot();
}