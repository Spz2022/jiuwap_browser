<?php
/*
 *
 *	浏览器->书签
 *
 *	2011-4-18 @ jiuwap.cn
 *
 */

define('DEFINED_JIUWAP','jiuwap.cn');

if ( !defined('m') ){
    include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
	$browser->user_login_check();
}
$h = isset($_GET['h']) ? $_GET['h'] : '';

if ( $h <> ''){
	$au = '&amp;h='.$h;
}else{
	$au = '';
}

if ( !isset($_GET['cmd']) || $_GET['cmd'] == 'admin' || $_GET['cmd'] == 'order'){
	if ( isset($_GET['cmd']) && $_GET['cmd']=='order' && isset($_GET['id']) ){
		$_GET['id'] = (float)$_GET['id'];
		if ( isset($_GET['dos']) && $_GET['dos']=='up' ){
			$browser->book_order($_GET['id'],'up');
		}else{
			$browser->book_order($_GET['id'],'down');
		}
	}
	$browser->template_top('书签');
	if ( isset($_GET['cmd']) ){
		if ( $_GET['cmd']=='admin' ){
			echo '<a href="book.php?cmd=order&amp;h='.$h.'">书签排序</a>.';
		}else{
			echo '<a href="book.php?cmd=admin'.$au.'">返回管理</a>.';
		}
		echo '<a href="book.php?h='.$h.'">返回书签</a><br/>';
	}else{
		if ( $h <> '' ){
			echo '<a href="book.php?cmd=new'.$au.'">添加书签</a>.';
		}else{
			echo '<a href="book.php?cmd=new'.'">新建书签</a>.';
		}
		echo '<a href="book.php?cmd=admin'.$au.'">管理书签</a><br/>';
	}
	if ( $h <> '' ){
		echo '<a href="/?h='.$h.'">返回网页</a>.';
		echo '<a href="/?m='.$h.'">返回菜单</a>';
	}else{
		echo '<a href="/?r='.$browser->rand.'">返回首页</a>';
		//echo '<a href="book.php?cmd=netbook'.$au.'">书签同步</a>';
	}

	echo hr;
	$var = $browser->book_lists();
	if ($var == array()){
		echo '空<br />';
	}else{
		if ( isset($_GET['cmd']) ){
			if ( $_GET['cmd']=='admin' ){
				foreach ( $var as $val){
					echo $val['title'].'(<a href="book.php?cmd=change&amp;id='.$val['id'].$au.'">改</a>.<a href="book.php?cmd=del&amp;id='.$val['id'].$au.'">删</a>)<br/>';
				}
			}else{
				$i = 0;
				foreach ( $var as $val){
					$i++;
					echo $val['title'].'(<a href="book.php?cmd=order&amp;dos=up&amp;id='.$i.$au.'">上</a>.<a href="book.php?cmd=order&amp;dos=down&amp;id='.$i.$au.'">下</a>)<br/>';
				}
			}
		}else{
			foreach ( $var as $val){
				echo '<a href="/?b='.$val['id'].'">'.$val['title'].'</a><br/>';
			}
		}
	}
	echo hr;
	echo '将本页保存为手机书签可方便您的访问！	';
	$browser->template_foot();

}elseif( $_GET['cmd'] == 'adddb' ){
	$browser->template_top('添加书签');
	$err = $browser->book_check();
	if ( $err == '' && !@parse_url($_POST['url']) ){
		$err = '书签地址错误#2';
	}
	if ($err == ''){
		if ( !isset($_POST['cover']) || $_POST['cover'] == 0){
			$browser->book_add($_POST['title'],$_POST['url']);
		}else{
			$browser->book_add($_POST['title'],$_POST['url'],true);
		}
		echo '添加书签成功！<br/><a href="book.php?h='.$h.'">返回书签</a>	';
	}else{
		echo $err.'<br/><a href="book.php?cmd=new'.$au.'">返回添加</a>
		<br/><a href="book.php?h='.$h.'">返回书签</a>	';
	}
	$browser->template_foot();
}elseif( $_GET['cmd'] == 'new' ){
	$title = '新书签';
	$url = 'http://';
	if ( $h <> ''){
		$arr = $browser->history_get($h);
		if ( $url !== false ){
			$title = htmlspecialchars($arr['title']);
			$url = htmlspecialchars($arr['url']);
		}
	}
	if ( $browser->template == 0 ){
		$browser->template_top('添加书签');
		echo '
		<form action="book.php?cmd=adddb'.$au.'" method="post">
		添加书签<br />
		返回：<a href="/?n='.$h.'">书签</a>.';
		if ( $h<>'' ){
			echo '<a href="/?h='.$h.'">网页</a>.';
		}
		echo '<a href="/?m='.$h.'">菜单</a><br />
		标题：<input type="text" name="title" value="'.$title.'" /><br />
		网址：<input type="text" name="url" value="'.$url.'" /><br />
		<input type="checkbox" name="cover" value="1" checked="checked">覆盖同名或同网址书签<br />
		<input type="submit" value="添加"/><br />
		</form>
		';
		$browser->template_foot();

	}else{
		$browser->template_top('添加书签');
		echo '
		添加书签<br />
		返回：<a href="/?n='.$h.'">书签</a>.';
		if ( $h<>'' ){
			echo '<a href="/?h='.$h.'">网页</a>.';
		}
		echo '<a href="/?m='.$h.'">菜单</a><br />
		标题：<input type="text" name="title'.$browser->rand.'" value="'.$title.'" /><br />
		网址：<input type="text" name="url'.$browser->rand.'" value="'.$url.'" /><br />
		<select name="cover'.$browser->rand.'">
			<option value="0">覆盖同名或同网址书签</option>
			<option value="1" selected="selected">不覆盖同名或同网址书签</option>
		</select><br />
		<anchor>
		<go href="book.php?cmd=adddb'.$au.'" method="post">
		<postfield name="title" value="$(title'.$browser->rand.')" />
		<postfield name="url" value="$(url'.$browser->rand.')" />
		<postfield name="cover" value="$(cover'.$browser->rand.')" />
		</go>添加</anchor>';
		$browser->template_foot();
	}
}elseif( $_GET['cmd'] == 'admin' ){

}elseif( $_GET['cmd'] == 'del' ){
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$browser->template_top('删除书签');
	if ( isset($_GET['yes']) ){
		$browser->book_del($id);
		$error = '书签删除成功';
	}else{
		if ( $id == 0 ){
			$error = '书签不存在[#1]';
		}else{
			$book = $browser->book_get($id);
			if ( $book == false ){
				$error = '书签不存在[#2]';
			}else{
				$error = '书签：'.$book['title'].'<br />
							地址：'.htmlspecialchars($book['url']).'<br />
							确认删除？<a href="book.php?cmd=del&amp;yes=yes&amp;id='.$id.''.$au.'">删除</a>';
			}
		}
	}
	echo $error.'<br/><a href="book.php?cmd=admin'.$au.'">返回书签</a>';
	$browser->template_foot();

}elseif( $_GET['cmd'] == 'change' ){
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$browser->template_top('修改书签');
	if ( isset($_GET['yes']) ){
		$error = $browser->book_check();
		if ( $error == '' && !@parse_url($_POST['url']) ){
			$error = '书签地址错误#2';
		}
		if ( $error == '' ){
			$browser->book_change($id,$_POST['title'],$_POST['url']);
			$error = '书签修改成功';
		}else{
			$error .= '<br/><a href="book.php?cmd=change&amp;id='.$id.''.$au.'">返回修改</a>';
		}

	}else{
		if ( $id == 0 ){
			$error = '书签不存在[#1]';
		}else{
			$book = $browser->book_get($id);
			if ( $book == false ){
				$error = '书签不存在[#2]';
			}else{
				if ( $browser->template == 0 ){
					$error = '
					<form action="book.php?cmd=change&yes=yes&id='.$id.$au.'" method="post">
					修改书签<br/>
					标题：<input type="text" name="title" value="'.$book['title'].'" /><br />
					网址：<input type="text" name="url" value="'.htmlspecialchars($book['url']).'" /><br />
					<input type="submit" value="修改"/><br />
					</form>
					';
				}else{
					$error = '
					修改书签<br/>
					标题：<input type="text" name="title" value="'.$book['title'].'" /><br />
					网址：<input type="text" name="url" value="'.htmlspecialchars($book['url']).'" /><br />
					<anchor>
					<go href="book.php?cmd=change&amp;yes=yes&amp;id='.$id.''.$au.'" method="post">
					<postfield name="title" value="$title" />
					<postfield name="url" value="$url" />
					</go>修改</anchor>';
				}
			}
		}
	}
	echo $error.'<br/><a href="book.php?cmd=admin'.$au.'">返回书签</a>';
	$browser->template_foot();
/*}elseif( $_GET['cmd'] == 'netbook' ){
	$host = isset($_POST['host']) ? strtolower(trim($_POST['host'])) : null;
	$name = isset($_POST['name']) ? trim($_POST['name']) : null;
	$pass = isset($_POST['pass']) ? trim($_POST['pass']) : null;
	$browser->template_top('网络书签');
	echo '<a href="book.php?h='.$h.'">返回书签</a>'.hr;
	if ( isset( $_GET['yes'] ) ){
		if ( !$host || !$name || !$pass ){
			echo '错误：表单内容不能为空！'.hr;
		}elseif( $err = $browser->_user_name_check($name,$pass) ){
			echo '错误：'.$err.hr;
		}else{
			$host = str_replace('http://','',$host);
			$url ='http://'.$host.'/api/?cmd=book&username='.urlencode($name).'&password='.urlencode($pass);
			if ( $host == $b_set['host']){
				echo '错误：不能获取本站书签！'.hr;
			}elseif ( ($data = @file_get_contents($url)) === false ){
				echo '错误：连接'.$host.'的API失败,请检查域名是否正确！'.hr;
			}elseif( ($result = str_pos($data,'<books_result>','</books_result>')) <>'true'){
				echo '错误：获取书签失败，请检查账号密码是否正确！'.hr;
			}elseif( ($num = str_pos($data,'<books_count>','</books_count>')) == '0' ){
				echo '错误：获取书签失败，目标书签为空！'.hr;
			}else{
				$data = str_pos($data,'<books_content>','</books_content>');
				$data = explode("\r\n\r\n",$data);
				if ( count($data) <> $num ){
					echo '错误：获取书签失败，书签格式错误！'.hr;
				}else{
					if ( !isset($_POST['cover']) || $_POST['cover'] == 0){
						$_POST['cover'] = false;
					}else{
						$_POST['cover'] = true;
					}
					$i=0;
					foreach ($data as $arr){
						$arr = explode("\r\n",trim($arr));
						if ( count($arr)<>2 ){
							continue;
						}
						$i++;
						$browser->book_add($arr[0],$arr[1],$_POST['cover']);
					}
					echo '目标书签：'.$num.'个<br/>';
					echo '更新书签：'.$i.'个';
					$browser->template_foot();
				}
			}
		}
	}
	if ( $browser->template == 0 ){
		echo '
		<form action="book.php?cmd=netbook&yes=yes'.$au.'" method="post">
		域名：<input type="text" name="host" value="'.$host.'" /><br />
		账号：<input type="text" name="name" value="'.$name.'" /><br />
		密码：<input type="text" name="pass" value="'.$pass.'" /><br />
		<input type="checkbox" name="cover" value="1" checked="checked">覆盖同名或同网址书签<br />
		<input type="submit" value="获取书签"/><br />
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
		<go href="book.php?cmd=netbook&amp;yes=yes'.$au.'" method="post">
		<postfield name="title" value="$(title'.$browser->rand.')" />
		<postfield name="name" value="$(name'.$browser->rand.')" />
		<postfield name="pass" value="$(pass'.$browser->rand.')" />
		<postfield name="cover" value="$(cover'.$browser->rand.')" />
		</go>获取书签</anchor>';
	}
	echo hr.'提示:(域名无需http://)本服务只是用于20110420(包括)以上版本的玖玩浏览器系统架设的网页浏览器,例如你在mm.jiuwap.cn有书签想要存到本地,那么你只需要输入域名“mm.jiuwap.cn”，再输入你在mm.jiuwap.cn的账号和密码，点获取书签即可将mm.jiuwap.cn的书签保存到本站。';
	$browser->template_foot();*/
}else{
	$browser->template_top('书签');
	echo '参数错误！<br/><a href="book.php?h='.$h.'">返回书签</a>	';
	$browser->template_foot();
}