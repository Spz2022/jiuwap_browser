<?php
define('DEFINED_JIUWAP','jiuwap.cn');
include $_SERVER['DOCUMENT_ROOT'].'/inc/common.php';
if ( isset($_GET['yes']) ){
	$get_string = $browser->user_reg($_POST['name'],$_POST['pass'],$_POST['pass']);
	if ( $get_string === false ){
		load_template('reg_success',false,'/?r='.$browser->rand,false,'utf-8',0);
	}else{
		load_template('reg_fail',false);
	}
}else{
	load_template('reg_form');
}

