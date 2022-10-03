<?php
if ( !defined('DIR') ){
	die('hello world');
}
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}
$api = 'http://api.jiuwap.cn/browser_update/?host='.urlencode($_SERVER["SERVER_NAME"]).'&version='.$version.'&cmd=';

$cmd = isset($_GET['cmd']) ? trim($_GET['cmd']) : '';

if ( $cmd == 'go'){
	//获取信息
	if ( !$net_info = @file_get_contents($api.'getinfo') ){
		$err = '获取玖玩更新信息失败#1';
	}else{
		$net_info = explode("\r\n",$net_info);
		if ( count($net_info) <> 3 ){
			$new_version = '-1';
			$new_info = 'NULL';
			$err = '解析玖玩更新信息失败#1';
		}else{
			$new_version = $net_info[0];
			$new_info = $net_info[1];
			$new_file = $net_info[2];

			if ( $new_version <> '0' ){
				mkdirs('update/'.$new_version.'/');
				if ( !@$new_file = file_get_contents($new_file)){
					$err = '获取玖玩服务器更新文件失败#1';
				}else{
					if ( !@file_put_contents('update/'.$new_version.'/index.php',$new_file) ){
						$err = '保存更新文件失败';
					}
				}
			}
		}
	}
	top('更新玖玩浏览器');
	echo '<b>更新玖玩浏览器</b>';
	echo hr;
	if ( $new_version <> '0' ){
		echo '更新：'.$version.'-&gt;'.$new_version.'<br/>';
		echo '说明：'.$new_info.'<br/>';
		echo '<a href="update/'.$new_version.'">进入更新['.$new_version.']</a><br/>';
		if ( isset($err) ){
			echo '提示：'.$err.'<br/>';
		}
		echo '如果长时间不进行升级，升级到最新版可能需要升级好几个阶段。<br/>';
	}else{
		echo '提示：您当前无需升级！<br/>';
	}
	echo '<a href="index.php?do=update">返回</a><br/>';

}else{
	//获取最新版本号
	top('更新玖玩浏览器');
	echo '<b>更新玖玩浏览器</b>';
	echo hr;

	echo '本地:'.$version.'<br/>';
	if ( $net_version = @file_get_contents($api.'getversion') ){
		echo '最新:'.$net_version.'<br/>';

		if ( $net_version > $version ){
			echo '您当前的玖玩浏览器版本低于玖玩服务器最新版,<a href="index.php?do=update&amp;cmd=go">立即升级</a><br/>';
		}else{
			echo '您当前的玖玩浏览器不需要升级哦！<br/>';
		}
	}else{
		echo '获取最新玖玩浏览器版本号失败！<br/>';
	}

	echo '<a href="index.php">返回</a><br/>';
}
foot();