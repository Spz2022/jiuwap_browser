<?php
!defined('m') && header('location: /?r='.rand(0,999));
if ( $_url <>'' ){
	echo '非法打包'.hr;
	echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.urlencode($my_dir).'&amp;id='.$id.$h.'">返回目录</a>';
	$browser->template_foot();
}

$arr = ListDirFiles($__dir);
$pack_size = $arr[2];
$pack_files = $arr[1];
$pack_dirs = $arr[0];
$pack_array = $arr[3];
$fsize = bitsize($pack_size);
unset($arr);

if ( isset($_GET['yes']) ){
	$err = false;
	set_time_limit(300);
	ignore_user_abort(true);
	$size_old = filesize($_file);
	include_once DIR.'inc/class/runtime.lib.php';
	$rtime = new runtime();
	$rtime->start();
	if ( $dir['mime'] == 'mrp' ){
		$gzip = isset($_POST['gzip']) ? (float)($_POST['gzip']) : 3;
		( $gzip<1 || $gzip>9 ) && $gzip=3;
		if ( $pack_dirs >0 ){
			$err =  '对不起,网盘系统(MRP)暂时不支持文件夹打包,请删除文件夹后再打包！<br/>';
		}elseif ( !$pack_array ){
			$err =  '文件列表为空,无法打包！<br/>';
		}else{
			include_once DIR.'inc/class/mrp.lib.php';
			if ( $arr = @unserialize(file_get_contents($__dir.'_list')) ){
				$new_arr = array();
				foreach( $arr as $val){
					if ( in_array($__dir.'/'.$val,$pack_array) ){
						unset($pack_array[$__dir.'/'.$val]);
						$new_arr[] = $__dir.'/'.$val;
					}
				}
				$pack_array = $new_arr+$pack_array;
			}
			mrp::pack($__file,$pack_array,$gzip,$__file,$__dir.'/');
		}
	}elseif ( $dir['mime'] == 'zip' || $dir['mime'] == 'jar' ){
		if ( !$pack_array ){
			$err =  '文件列表为空,无法打包！<br/>';
		}
		include_once DIR.'inc/class/pclzip.lib.php';
		$zip = new PclZip($__file);
		$zip->create($pack_array, PCLZIP_OPT_REMOVE_PATH, $__dir);
	}
	if ( $err ){
		echo $err;
	}else{
		$rtime->stop();
		$size_new = filesize($__file);
		$browser->db->query('UPDATE `disk_file` SET size='.($size_new).' WHERE id='.$id);
		echo '总文件：'.$fsize.'<br/>';
		echo '原文件：'.bitsize($size_old).'<br/>';
		echo '新文件：'.bitsize($size_new).'<br/>';
		echo '文件数：'.$pack_files.'个<br/>';
		echo '文件夹：'.$pack_dirs.'个<br/>';
		echo '打包耗时：'.$rtime->spent().'毫秒<br/>';
		echo '打包文件完毕！';
	}
}else{
	if ( $browser->template == 1 ){
		echo '
		文件数：'.$pack_files.'个<br/>
		文件夹：'.$pack_dirs.'个<br/>
		总文件：'.$fsize.'<br/>';
		if ( $b_set['dlocal']*2 < $pack_size ){
			echo '对不起,系统配置不允许打包超过'.bitsize($b_set['dlocal']*2).'的文件！';
		}elseif ( $dir['mime'] == 'mrp' && $pack_dirs >0 ){
			echo '对不起,网盘系统(MRP)暂时不支持文件夹打包,请删除文件夹后再打包！';
		}else{
			if ( $dir['mime'] == 'mrp' ){
				echo '压缩等级(1-9)：<input name="gzip" type="text" value="3"/><br/>';
			}
			echo '<anchor>
			<go href="disk.php?cmd=info&amp;dir='.$__url.'&amp;do=unzip&amp;act=pack&amp;yes=yes&amp;id='.$id.$h.'" method="post">
			<postfield name="gzip" value="$(gzip'.$browser->rand.')" />
			</go>确认打包</anchor>';
		}

	}else{
		echo '<form action="disk.php?cmd=info&amp;dir='.$__url.'&amp;act=pack&amp;do=unzip&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		文件：'.$pack_files.'个<br/>
		文件夹：'.$pack_dirs.'个<br/>
		总文件：'.bitsize($pack_size).'<br/>';
		if ( $b_set['dlocal']*2 < $pack_size ){
			echo '对不起,系统配置不允许打包超过'.bitsize($b_set['dlocal']*2).'的文件！';
		}elseif ( $dir['mime'] == 'mrp' && $pack_dirs >0 ){
			echo '对不起,网盘系统(MRP)暂时不支持文件夹打包,请删除文件夹后再打包！<br/>';
		}else{
			if ( $dir['mime'] == 'mrp' ){
				echo '压缩等级(1-9)：<input name="gzip" type="text" value="3"/><br/>';
			}
			echo '<input type="submit" value="确认打包"/></form>';
		}
	}
	echo '<br/>提示：如果文件过多或过大，打包时可能出现页面超时现象，其实已经再打包了，请不要再多次打包！返回即可！';
}


echo hr;
echo '<a href="disk.php?cmd=info&amp;do=unzip&amp;dir='.$__url.'&amp;id='.$id.$h.'">返回目录</a><br/>';
echo '<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
$browser->template_foot();
