<?php
!defined('m') && header('location: /?r='.rand(0,999));
if ( isset($_GET['yes']) ){
	@ob_end_clean();
	@ob_start();
	$filename = $b_set['dfforever'].$dir['file'];
	$arr = GetImageSize($filename);
	if ( $arr === false ){
		header('HTTP/1.0 400 Not Found');
		exit;
	}
	$width = $arr[0];
	$height = $arr[1];
	switch ($arr[2]) {
		case 1://gif
			$im = ImageCreateFromGIF($filename);
			break;
		case 2://jpg
			$im = imagecreatefromJPEG($filename);
			break;
		case 3://png
			$im = ImageCreateFromPNG($filename);
			break;
		case 6://bmp
			$im = ImageCreateFromBMP($filename);
			break;
		default://
			exit;
			break;
	}
	$arr = GetImageSize($filename);
	fixpicsize($height,$width,320,320);
	$srcW = ImageSX($im);
	$srcH = ImageSY($im);
	$ni = imagecreatetruecolor($width,$height);
	if ($arr[2]<>6){
		imagealphablending($ni,false);
	}
	if ($arr[2]==1){
		$black = ImageColorAllocate($ni, 0,0,0);
		imagecolortransparent($ni,$black);
	}elseif ($arr[2]==3){
		imagesavealpha($ni,true);
	}
	ImageCopyResized($ni,$im,0,0,0,0,$width,$height,$srcW,$srcH);
	Header('Content-type: image/jpeg');
	ImageJpeg($ni,'', 60);
	exit;
}

echo '查看图片'.hr;
if ( mime_ispic($dir['mime']) ){
	echo '<img src="disk.php?cmd=info&amp;do=picread&amp;yes=yes&amp;id='.$id.$h.'" alt="缩略图"/><br/>查看原图请下载图片';
}else{
	echo '不支持此文件';
}
echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
