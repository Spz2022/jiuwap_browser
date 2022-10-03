<?php
/*
 *
 *
 *
 *	2011-4-3 @ jiuwap.cn
 *
 */

!defined('m') && header('location: /?r='.rand(0,999));

$up_id = isset($_GET['uid']) ? (float)$_GET['uid'] : 0;
$do = isset($_GET['do']) ? $_GET['do'] : '';

$browser->template_top('上传文件');
$the_title = '';
if ( $up_id<>0 ){
	$dir = $browser->db->fetch_first('SELECT title,oid FROM `disk_dir` WHERE uid='.$disk['id'].' AND id='.$up_id);
	if ( !$dir ){
		echo '错误：文件夹不存在！<br/>';
		echo '<a href="disk.php?id=0'.$h.'">返回根目录</a>';
		$browser->template_foot();
	}else{
		$up_title = $dir['title'];
		$the_title = urltree($dir['oid']).'<a href="disk.php?id='.$up_id.$h.'">'.$dir['title'].'</a>\\';
	}
}
echo '<a href="disk.php?id=0'.$h.'">root</a>:\\'.$the_title.hr;

if ( $do == 'local') {
	echo '本地上传(<a href="disk.php?cmd=upload&amp;uid='.$up_id.$h.'">返回</a>)<br/>';
	if ( isset($_GET['yes']) ){
		$error = false;
		if ( !$_FILES || !isset($_FILES['file']['size']) ) {
			$error = '您没有正确选择文件';
		}elseif ( isset($_FILES['file']['error']) && $_FILES['file']['error']<>0 ) {
			switch ( $_FILES['file']['error'] ){
			case 1:
				$error = '上传文件的过大';
				break;
			case 2:
				$error = '上传文件的大小超过'.bitsize($b_set['dlocal']);
				break;
			case 3:
				$error = '文件没有上传完整';
				break;
			case 4:
				$error = '没有文件被上传';
				break;
			case 6:
				$error = '临时文件夹不存在';
				break;
			default:
				$error = '上传出现未知错误';
				break;
			}
		}elseif ( $_FILES['file']['size'] > $b_set['dlocal'] ) {
			$error = '上传文件的大小超过'.bitsize($b_set['dlocal']).' #2';
		}elseif ( $_FILES['file']['size'] == 0 ) {
			$error = '没有文件被上传 #2';
		}elseif ( $disk['space_all']-$disk['space_use']-$_FILES['file']['size'] <= 0){
			$error = '网盘空间已满';
		}else{
			$strlen = (float)$_FILES['file']['size'];
			$basename = fix_localbase($_FILES['file']['name']);
			$dir = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$up_id.' AND title="'.$basename.'" AND uid='.$disk['id']);
			$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$up_id.' AND title="'.$basename.'" AND uid='.$disk['id']);
			if ( $dir || $file ){
				$basename = rand(1,999999).'_'.$basename;
			}
			$the_save_file = time_().'_'.rand(10000,99999);
			if ( ($mime = get_short_file_mime($basename) ) == '' ){
				$the_save_file .= '_'.$mime;
			}else{
				$the_save_file .= '_unknown';
			}

			$arr = array(
					'oid'	=>	$up_id,
					'uid'	=>	$disk['id'],
					'mime'	=>	$mime,
					'title'	=>	$basename,
					'size'	=>	$strlen,
					'file'	=>	$the_save_file,
					);
			if ( !@move_uploaded_file($_FILES['file']['tmp_name'],$b_set['dfforever'].$the_save_file) ){
				$error = '未知错误#1！';
			}elseif ( $id = $browser->db->insert('disk_file',$arr,true) ){
				$browser->db->query('UPDATE `disk_config` SET space_use=space_use+'.$arr['size'].' WHERE id='.$disk['id']);
				echo '上传文件成功！<br/>';
				echo '文件：'.$basename.'<br />';
				echo '大小：'.bitsize($strlen).'<br />';
				echo '类型：'.get_file_mime($mime).'<br />';
				echo '查看<a href="disk.php?cmd=info&amp;id='.$id.'&amp;uid='.$up_id.$h.'">['.$basename.']</a><br/>';
				$browser->template_foot();
			}else{
				$error = '未知错误#2！';
			}

		}
		if ( $error ){
			echo '错误:'.$error;
		}
	}
	if ( $browser->template == 1 ){
		echo '对不起，您当前的浏览界面不支持上传文件，请进设置将界面改为wap2.0。';
	}else{
		echo '<form enctype="multipart/form-data" action="disk.php?cmd=upload&amp;do=local&amp;yes=yes&amp;uid='.$up_id.$h.'" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="'.$b_set['dlocal'].'" />
		选择文件：<input name="file" type="file" /><br/>
		<input type="submit" value="上传"/><br />
		</form>';
	}

}elseif ( $do == 'url') {
	$url = isset($_POST['url']) ? fix_r_n_t($_POST['url']) : '';
	if ( !in_array(strtolower(substr($url,0,7)),array('https:/','http://')) ) {
		$url = 'http://'.$url;
	}
	$referer = isset($_POST['referer']) ? $_POST['referer'] : '';
	echo '远程URL上传(<a href="disk.php?cmd=upload&amp;uid='.$up_id.$h.'">返回</a>)<br/>';
	if ( isset($_GET['yes']) ){
		$error = false;
		if ( empty($url) || $url == 'http://'){
			$error = 'URL地址不能为空';
		}else{
			$http = new httplib();
			$referer && $http->referer(fix_r_n_t($referer));
			if ( $http->open($url,30,5,true) == false ){
				$error = $http->error();
			}else{
				$url_A = $http->parse_url();
				include DIR.'set_config/set_forbidhost.php';
				if ( in_array(strtolower($url_A['host']),$b_set['forbid']) ){
					$error = '禁止上传,目标站不合法,或嵌套浏览器#1';
				}else{
					$browser->selectBrowserUA();
				}
				if ( !$error && !$http->send() ){
					$error = $http->error();
				}elseif ( !$error ) {
					$header = $http->header();
					if ( !isset($header['STATUS']) || $header['STATUS'] != '200' ){
						$error = '连接远程文件失败';
					}elseif ( isset($header['CONTENT-LENGTH']) && $header['CONTENT-LENGTH'] > $b_set['dhttp'] ){
							$error = '远程文件不得大于'.bitsize($b_set['dhttp']).'#1';
					}elseif ( isset($header['CONTENT-LENGTH']) && $disk['space_all']-$disk['space_use']-$header['CONTENT-LENGTH'] <= 0){
						$error = '网盘空间已满#1';
					}else{
						$content = $http->response();
						$strlen = strlen($content);
						if ( $disk['space_all']-$disk['space_use']-$strlen <= 0){
							$error = '网盘空间已满#2';
						}elseif ( $strlen > $b_set['dhttp'] ){
							$error = '远程文件不得大于'.bitsize($b_set['dhttp']).'#2';
						}else{
							if ( isset($header['CONTENT-DISPOSITION']) ){
								$basename = fix_disposition($header['CONTENT-DISPOSITION']);
							}else{
								$basename = fix_basename($http->url());
							}
							$dir = $browser->db->fetch_first('SELECT id FROM `disk_dir` WHERE oid='.$up_id.' AND title="'.$basename.'" AND uid='.$disk['id']);
							$file = $browser->db->fetch_first('SELECT id FROM `disk_file` WHERE oid='.$up_id.' AND title="'.$basename.'" AND uid='.$disk['id']);
							if ( $dir || $file ){
								$basename = rand(1,999999).'_'.$basename;
							}
							$mime = get_short_file_mime($basename);

							$the_save_file = time_().'_'.rand(10000,99999);
							if ( $mime <> ''){
								$the_save_file .= '_'.$mime;
							}else{
								$the_save_file .= '_unknown';
							}

							$arr = array(
								'oid'	=>	$up_id,
								'uid'	=>	$disk['id'],
								'mime'	=>	$mime,
								'title'	=>	$basename,
								'size'	=>	$strlen,
								'file'	=>	$the_save_file,
							);
							if ( !writefile($b_set['dfforever'].$the_save_file,$content) ){
								$error = '未知错误#1';
							}elseif ( $id = $browser->db->insert('disk_file',$arr,true) ){
								$browser->db->query('UPDATE `disk_config` SET space_use=space_use+'.$arr['size'].' WHERE id='.$disk['id']);
								echo '上传文件成功！<br/>';
								echo '文件：'.$basename.'<br />';
								echo '大小：'.bitsize($strlen).'<br />';
								echo '类型：'.get_file_mime($mime).'<br />';
								echo '地址：'.htmlspecialchars($url).'<br />';

								echo '查看<a href="disk.php?cmd=info&amp;id='.$id.'&amp;uid='.$up_id.$h.'">['.$basename.']</a><br/>';
								$browser->template_foot();
							}else{
								$error = '未知错误#2';
							}

						}
					}
				}
			}
		}
		if ( $error ){
			echo '错误:<b>'.$error.'</b>';
		}
	}
	if ( $browser->template == 1 ){
		echo '
		URL地址*：<input name="url'.$browser->rand.'" type="text" value="'.$url.'"/><br/>
		引用地址：<input name="referer'.$browser->rand.'" type="text" value="'.$referer.'"/><br/>
		(如果远程文件有防盗链，请在引用地址填上远程文件所在的网页地址！)<br/>
		<anchor>
		<go href="disk.php?cmd=upload&amp;do=url&amp;yes=yes&amp;uid'.$up_id.$h.'" method="post">
		<postfield name="url" value="$(url'.$browser->rand.')" />
		<postfield name="referer" value="$(referer'.$browser->rand.')" />
		</go>上传</anchor><br/>
		';
	}else{
		echo '<form action="disk.php?cmd=upload&amp;do=url&amp;yes=yes&amp;uid='.$up_id.$h.'" method="post">
		URL地址*：<input name="url" type="text" value="'.$url.'"/><br/>
		引用地址：<input name="referer" type="text" value="'.$referer.'"/><br/>
		(如果远程文件有防盗链，请在引用地址填上远程文件所在的网页地址！)<br/>
		<input type="submit" value="上传"/><br />
		</form>';
	}
}else{
	echo $b_set['disktitle'].'-文件上传<br/>';
	echo '<a href="disk.php?cmd=upload&amp;do=local&amp;uid='.$up_id.$h.'">本地上传</a><br/>';
	echo '<a href="disk.php?cmd=upload&amp;do=url&amp;uid='.$up_id.$h.'">远程上传</a><br/>';
}


$browser->template_foot();
