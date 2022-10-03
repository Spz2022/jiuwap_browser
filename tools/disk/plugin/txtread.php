<?php
!defined('m') && header('location: /?r='.rand(0,999));
	if ( mime_istxt($dir['mime']) ){
		$content = @file_get_contents($b_set['dfforever'].$dir['file']);
		$content = trim(nl2br(htmlspecialchars($content)));
		$content = getUTFString($content,$code);
		echo '在线查看['.$code.']'.hr;
        $page = isset($_GET['ipage']) ? (int)$_GET['ipage']:0;
        if ( strlen($content) <= 1500 || $page == '-1'){
            echo $content;
        }else{
            if ( $page == 0 ){
                echo '提示：内容长度大于1500<br/>';
            	echo '<br/><a href="disk.php?cmd=info&amp;do=txtread&amp;ipage=1&amp;id='.$id.$h.'">分页阅读</a>';
            	echo '<br/><a href="disk.php?cmd=info&amp;do=txtread&amp;ipage=-1&amp;id='.$id.$h.'">全文阅读</a>';
            }else{
                include DIR. 'inc/class/txt.cutpage.lib.php';
                $CP = new cutpage($content,1500);
                echo  $CP->get_str().hr.$CP->get_page();
            }
        }
	}else{
		echo '在线查看.'.hr.'不支持此文件';
	}
	echo hr.'<a href="disk.php?cmd=info&amp;id='.$id.$h.'">返回文件</a>';
