<?php
define('DEFINED_JIUWAP','jiuwap.cn');

$install_version = '20110901';
require 'inc.php';

$version = 0;
@include DIR.'set_config/version.php';

$do = isset($_GET['do']) ? $_GET['do'] : '';

if ( $do == 'clear' ) {
	require 'clear.php';
}elseif ( $do == 'update' ) {
	require 'update_check.php';
}elseif ( $version < $install_version || $do == 'install_full' ){
	require 'install_full.php';
}else{
	require 'main.php';
}

