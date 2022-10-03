<?php
!defined('m') && header('location: /?r='.rand(0,999));

$str = id2password($id).'/'.urlencode($dir['title']);
echo '(可以外链,链接将在十分钟后失效)<br/>';
echo '<input type="text" value="http://'.$b_set['host'].'/disk.php/'.$str.'"/><br/>';
echo '<a href="disk.php/'.$str.'">下载文件</a><br/>';
echo '<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
