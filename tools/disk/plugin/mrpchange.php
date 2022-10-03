<?php
!defined('m') && header('location: /?r='.rand(0,999));
include DIR. 'inc/class/mrp.lib.php';


$file_mrp = $b_set['dfforever'].$dir['file'];
if ( isset($_GET['yes'])){
	$mrp_info['xn'] = isset($_POST['xn']) ? trim($_POST['xn']) : '';
	$mrp_info['nn'] = isset($_POST['nn']) ? trim($_POST['nn']) : '';
	$mrp_info['bb'] = isset($_POST['bb']) ? trim($_POST['bb']) : '';
	$mrp_info['zz'] = isset($_POST['zz']) ? trim($_POST['zz']) : '';
	$mrp_info['js'] = isset($_POST['js']) ? trim($_POST['js']) : '';
	$mrp_info['ch'] = isset($_POST['ch']) ? trim($_POST['ch']) : '';
	$mrp_info['id'] = isset($_POST['id']) ? trim($_POST['id']) : '';
	if ( !mrp::put($file_mrp,$mrp_info) ){
		echo '未知原因,修改失败！';
	}else{
		echo 'MRP信息修改成功！';
	}
	echo '<br/><a href="disk.php?cmd=info&amp;do=mrpchange&amp;id='.$id.$h.'">返回修改</a><br/><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';

}else{
	$mrp = mrp::get($file_mrp);
	if ( $browser->template == 1 ){
		echo '
		显示名：<input name="xn'.$browser->rand.'" type="text" value="'.$mrp['xn'].'"/><br/>
		内部名：<input name="nn'.$browser->rand.'" type="text" value="'.$mrp['nn'].'"/><br/>
		版本号：<input name="id'.$browser->rand.'" type="text" value="'.$mrp['id'].'"/><br/>
		应用ID：<input name="bb'.$browser->rand.'" type="text" value="'.$mrp['bb'].'"/><br/>
		串号：<input name="ch'.$browser->rand.'" type="text" value="'.$mrp['ch'].'"/><br/>
		作者：<input name="zz'.$browser->rand.'" type="text" value="'.$mrp['zz'].'"/><br/>
		介绍：<input name="js'.$browser->rand.'" type="text" value="'.$mrp['js'].'"/><br/>
		<anchor>
		<go href="disk.php?cmd=info&amp;do=mrpchange&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		<postfield name="xn" value="$(xn'.$browser->rand.')" />
		<postfield name="nn" value="$(nn'.$browser->rand.')" />
		<postfield name="id" value="$(id'.$browser->rand.')" />
		<postfield name="bb" value="$(bb'.$browser->rand.')" />
		<postfield name="ch" value="$(ch'.$browser->rand.')" />
		<postfield name="zz" value="$(zz'.$browser->rand.')" />
		<postfield name="js" value="$(js'.$browser->rand.')" />
		</go>修改</anchor><br/>
		<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>
		';
	}else{
		echo '<form action="disk.php?cmd=info&amp;do=mrpchange&amp;yes=yes&amp;id='.$id.$h.'" method="post">
		显示名：<input name="xn" type="text" value="'.$mrp['xn'].'"/><br/>
		内部名：<input name="nn" type="text" value="'.$mrp['nn'].'"/><br/>
		版本号：<input name="id" type="text" value="'.$mrp['id'].'"/><br/>
		应用ID：<input name="bb" type="text" value="'.$mrp['bb'].'"/><br/>
		串号：<input name="ch" type="text" value="'.$mrp['ch'].'"/><br/>
		作者：<input name="zz" type="text" value="'.$mrp['zz'].'"/><br/>
		介绍：<input name="js" type="text" value="'.$mrp['js'].'"/><br/>
		<input type="submit" value="修改"/>
		<br /><a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>
		</form>';
	}

}
