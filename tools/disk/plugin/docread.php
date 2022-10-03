<?php
!defined('m') && header('location: /?r='.rand(0,999));
include DIR. 'inc/class/msdoc.lib.php';
echo '在线阅读doc文档[文字]'.hr;
if ( $dir['mime'] == 'doc'){
	echo doc2text($b_set['dfforever'].$dir['file']);
}else{
	echo '你要阅读的文档非DOC文档，系统无法读取。';
}
echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
