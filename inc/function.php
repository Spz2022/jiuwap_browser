<?php
/*
 *
 *
 *
 *	2011-3-12 @ jiuwap.cn
 *
 */
!defined('DIR') && define('DIR',$_SERVER['DOCUMENT_ROOT'].'/');

if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

Apache2Nginx();

$b_set['host'] = $_SERVER['HTTP_HOST'];

function Apache2Nginx(){
	if ( isset($_SERVER['SERVER_SOFTWARE']) && $_SERVER['SERVER_SOFTWARE'] == 'nginx' ){
		$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];

		$_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'];
		$_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'],1);
		$a = strpos($_SERVER['PATH_INFO'],'/');
		if ( $a === false ){
			$_SERVER['PATH_INFO'] = '';
		}else{
			$_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'],$a);
		}
	}
}

function http_error($url,$num,$str,$lite=false){
    if ( $num == '-3' ) {
        $error_str = 'Socket连接创建失败';
    }elseif ( $num == '-4' ) {
        $error_str = 'DNS定位失败';
    }elseif ( $num == '-5' ) {
        $error_str = '连接超时或被拒绝';
    }elseif ( $str == 'php_network_getaddresses: getaddrinfo failed: 不知道这样的主机。'){
        $error_str = '网址不可用';
    }else{
        $error_str = '访问失败';
    }
    if ( $lite ){
        if ( $error_str == '访问失败'){
            return $str;
        }else{
            return $error_str;
        }
    }
    $con = '提示：'.$error_str.'('.$num.')<br/>';
    $con .= '网址：'.htmlspecialchars($url).'<br/>';
    $con .= '详细：'.$str.'<br/>';
    error_show('访问失败',$con);
    exit;
}

function error_show($title,$content=false){
    if ( $content === false ){
        $content = '错误：'.$title;
    }
    global $browser;
	$browser->template_top($title);
	echo $content;
    if ($browser->template==1 ){
        echo '<br/><anchor>返回<prev/></anchor>';
    }elseif ( isset($_SERVER['HTTP_REFERER']) ){
    	echo '<br/><a href="'.$_SERVER['HTTP_REFERER'].'">返回</a>';
    }else{
    	echo '<br/><a href="/">返回</a>';
    }
    $browser->template_foot();
}

function error_book($title,$str){
	global $browser,$h;
	$browser->template_top($title);
	echo $str.hr;
	if ( $h<>''){
		echo '返回:<a href="/?h='.$h.'">网页</a>.';
	}else{
		echo '返回:';
	}
	echo '<a href="/?m='.$h.'">菜单</a>.';
	echo '<a href="copy.php?h='.$h.'">剪切板</a><br/>';
	$browser->template_foot();
}

function write_log($file,$line,$str='',$exit=true,$fff=''){
    if ( $str ){
        $str ="\r\n".$str;
    }
    $content = "line:{$line}\r\nfile:{$file}{$str}";

	$content = str_replace("\r\n","\r\n<br/>",$content);
	$str = "出错了：\r\n<br/>\r\n<br/>".$content;
	$str .= "<hr/>Powered By <a href=\"http://jiuwap.cn\">jiuwap.cn</a>";
	echo '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/><title>错误</title></head>';
	echo '<body><div>'.$str.'</div></body></html>';
	exit();
}



function get_file_mime($mime){
	$mime_array = array(
		'3gp'	=>	'video/3gpp',
		'aab'	=>	'application/x-authoware-bin',
		'aam'	=>	'application/x-authoware-map',
		'aas'	=>	'application/x-authoware-seg',
		'ai'	=>	'application/postscript',
		'aif'	=>	'audio/x-aiff',
		'aifc'	=>	'audio/x-aiff',
		'aiff'	=>	'audio/x-aiff',
		'als'	=>	'audio/X-Alpha5',
		'amc'	=>	'application/x-mpeg',
		'ani'	=>	'application/octet-stream',
		'asc'	=>	'text/plain',
		'asd'	=>	'application/astound',
		'asf'	=>	'video/x-ms-asf',
		'asn'	=>	'application/astound',
		'asp'	=>	'application/x-asap',
		'asx'	=>	'video/x-ms-asf',
		'au'	=>	'audio/basic',
		'avb'	=>	'application/octet-stream',
		'avi'	=>	'video/x-msvideo',
		'awb'	=>	'audio/amr-wb',
		'bcpio'	=>	'application/x-bcpio',
		'bin'	=>	'application/octet-stream',
		'bld'	=>	'application/bld',
		'bld2'	=>	'application/bld2',
		'bmp'	=>	'application/x-MS-bmp',
		'bpk'	=>	'application/octet-stream',
		'bz2'	=>	'application/x-bzip2',
		'cal'	=>	'image/x-cals',
		'ccn'	=>	'application/x-cnc',
		'cco'	=>	'application/x-cocoa',
		'cdf'	=>	'application/x-netcdf',
		'cgi'	=>	'magnus-internal/cgi',
		'chat'	=>	'application/x-chat',
		'class'	=>	'application/octet-stream',
		'clp'	=>	'application/x-msclip',
		'cmx'	=>	'application/x-cmx',
		'co'	=>	'application/x-cult3d-object',
		'cod'	=>	'image/cis-cod',
		'cpio'	=>	'application/x-cpio',
		'cpt'	=>	'application/mac-compactpro',
		'crd'	=>	'application/x-mscardfile',
		'csh'	=>	'application/x-csh',
		'csm'	=>	'chemical/x-csml',
		'csml'	=>	'chemical/x-csml',
		'css'	=>	'text/css',
		'cur'	=>	'application/octet-stream',
		'dcm'	=>	'x-lml/x-evm',
		'dcr'	=>	'application/x-director',
		'dcx'	=>	'image/x-dcx',
		'dhtml'	=>	'text/html',
		'dir'	=>	'application/x-director',
		'dll'	=>	'application/octet-stream',
		'dmg'	=>	'application/octet-stream',
		'dms'	=>	'application/octet-stream',
		'doc'	=>	'application/msword',
		'dot'	=>	'application/x-dot',
		'dvi'	=>	'application/x-dvi',
		'dwf'	=>	'drawing/x-dwf',
		'dwg'	=>	'application/x-autocad',
		'dxf'	=>	'application/x-autocad',
		'dxr'	=>	'application/x-director',
		'e'		=>	'application/octet-stream',
		'ebk'	=>	'application/x-expandedbook',
		'emb'	=>	'chemical/x-embl-dl-nucleotide',
		'embl'	=>	'chemical/x-embl-dl-nucleotide',
		'eps'	=>	'application/postscript',
		'eri'	=>	'image/x-eri',
		'es'	=>	'audio/echospeech',
		'esl'	=>	'audio/echospeech',
		'etc'	=>	'application/x-earthtime',
		'etx'	=>	'text/x-setext',
		'evm'	=>	'x-lml/x-evm',
		'evy'	=>	'application/x-envoy',
		'exe'	=>	'application/octet-stream',
		'fh4'	=>	'image/x-freehand',
		'fh5'	=>	'image/x-freehand',
		'fhc'	=>	'image/x-freehand',
		'fif'	=>	'image/fif',
		'fm'	=>	'application/x-maker',
		'fpx'	=>	'image/x-fpx',
		'fvi'	=>	'video/isivideo',
		'gau'	=>	'chemical/x-gaussian-input',
		'gca'	=>	'application/x-gca-compressed',
		'gdb'	=>	'x-lml/x-gdb',
		'gif'	=>	'image/gif',
		'gps'	=>	'application/x-gps',
		'gtar'	=>	'application/x-gtar',
		'gz'	=>	'application/x-gzip',
		'hdf'	=>	'application/x-hdf',
		'hdm'	=>	'text/x-hdml',
		'hdml'	=>	'text/x-hdml',
		'hlp'	=>	'application/winhlp',
		'hqx'	=>	'application/mac-binhex40',
		'htm'	=>	'text/html',
		'html'	=>	'text/html',
		'hts'	=>	'text/html',
		'ice'	=>	'x-conference/x-cooltalk',
		'ico'	=>	'application/octet-stream',
		'ief'	=>	'image/ief',
		'ifm'	=>	'image/gif',
		'ifs'	=>	'image/ifs',
		'imy'	=>	'audio/melody',
		'ins'	=>	'application/x-NET-Install',
		'ips'	=>	'application/x-ipscript',
		'ipx'	=>	'application/x-ipix',
		'it'	=>	'audio/x-mod',
		'itz'	=>	'audio/x-mod',
		'ivr'	=>	'i-world/i-vrml',
		'j2k'	=>	'image/j2k',
		'jad'	=>	'text/vnd.sun.j2me.app-descriptor',
		'jam'	=>	'application/x-jam',
		'jar'	=>	'application/java-archive',
		'jnlp'	=>	'application/x-java-jnlp-file',
		'jpe'	=>	'image/jpeg',
		'jpeg'	=>	'image/jpeg',
		'jpg'	=>	'image/jpeg',
		'jpz'	=>	'image/jpeg',
		'js'	=>	'application/x-javascript',
		'jwc'	=>	'application/jwc',
		'kjx'	=>	'application/x-kjx',
		'lak'	=>	'x-lml/x-lak',
		'latex'	=>	'application/x-latex',
		'lcc'	=>	'application/fastman',
		'lcl'	=>	'application/x-digitalloca',
		'lcr'	=>	'application/x-digitalloca',
		'lgh'	=>	'application/lgh',
		'lha'	=>	'application/octet-stream',
		'lml'	=>	'x-lml/x-lml',
		'lmlpack'	=>	'x-lml/x-lmlpack',
		'lsf'	=>	'video/x-ms-asf',
		'lsx'	=>	'video/x-ms-asf',
		'lzh'	=>	'application/x-lzh',
		'm13'	=>	'application/x-msmediaview',
		'm14'	=>	'application/x-msmediaview',
		'm15'	=>	'audio/x-mod',
		'm3u'	=>	'audio/x-mpegurl',
		'm3url'	=>	'audio/x-mpegurl',
		'ma1'	=>	'audio/ma1',
		'ma2'	=>	'audio/ma2',
		'ma3'	=>	'audio/ma3',
		'ma5'	=>	'audio/ma5',
		'man'	=>	'application/x-troff-man',
		'map'	=>	'magnus-internal/imagemap',
		'mbd'	=>	'application/mbedlet',
		'mct'	=>	'application/x-mascot',
		'mdb'	=>	'application/x-msaccess',
		'mdz'	=>	'audio/x-mod',
		'me'	=>	'application/x-troff-me',
		'mel'	=>	'text/x-vmel',
		'mi'	=>	'application/x-mif',
		'mid'	=>	'audio/midi',
		'midi'	=>	'audio/midi',
		'mif'	=>	'application/x-mif',
		'mil'	=>	'image/x-cals',
		'mio'	=>	'audio/x-mio',
		'mmf'	=>	'application/x-skt-lbs',
		'mng'	=>	'video/x-mng',
		'mny'	=>	'application/x-msmoney',
		'moc'	=>	'application/x-mocha',
		'mocha'	=>	'application/x-mocha',
		'mod'	=>	'audio/x-mod',
		'mof'	=>	'application/x-yumekara',
		'mol'	=>	'chemical/x-mdl-molfile',
		'mop'	=>	'chemical/x-mopac-input',
		'mov'	=>	'video/quicktime',
		'movie'	=>	'video/x-sgi-movie',
		'mp2'	=>	'audio/x-mpeg',
		'mp3'	=>	'audio/x-mpeg',
		'mp4'	=>	'video/mp4',
		'mpc'	=>	'application/vnd.mpohun.certificate',
		'mpe'	=>	'video/mpeg',
		'mpeg'	=>	'video/mpeg',
		'mpg'	=>	'video/mpeg',
		'mpg4'	=>	'video/mp4',
		'mpga'	=>	'audio/mpeg',
		'mpn'	=>	'application/vnd.mophun.application',
		'mpp'	=>	'application/vnd.ms-project',
		'mps'	=>	'application/x-mapserver',
		'mrl'	=>	'text/x-mrml',
		'mrp'	=>	'application/mrp',
		'mrm'	=>	'application/x-mrm',
		'ms'	=>	'application/x-troff-ms',
		'mts'	=>	'application/metastream',
		'mtx'	=>	'application/metastream',
		'mtz'	=>	'application/metastream',
		'mzv'	=>	'application/metastream',
		'nar'	=>	'application/zip',
		'nbmp'	=>	'image/nbmp',
		'nc'	=>	'application/x-netcdf',
		'ndb'	=>	'x-lml/x-ndb',
		'ndwn'	=>	'application/ndwn',
		'nif'	=>	'application/x-nif',
		'nmz'	=>	'application/x-scream',
		'npx'	=>	'application/x-netfpx',
		'nsnd'	=>	'audio/nsnd',
		'nva'	=>	'application/x-neva1',
		'oda'	=>	'application/oda',
		'oom'	=>	'application/x-AtlasMate-Plugin',
		'pac'	=>	'audio/x-pac',
		'pae'	=>	'audio/x-epac',
		'pan'	=>	'application/x-pan',
		'pbm'	=>	'image/x-portable-bitmap',
		'pcx'	=>	'image/x-pcx',
		'pda'	=>	'image/x-pda',
		'pdb'	=>	'chemical/x-pdb',
		'pdf'	=>	'application/pdf',
		'pfr'	=>	'application/font-tdpfr',
		'pgm'	=>	'image/x-portable-graymap',
		'php'	=>	'application/x-httpd-php',
		'pict'	=>	'image/x-pict',
		'pm'	=>	'application/x-perl',
		'pmd'	=>	'application/x-pmd',
		'png'	=>	'image/png',
		'pnm'	=>	'image/x-portable-anymap',
		'pnz'	=>	'image/png',
		'pot'	=>	'application/vnd.ms-powerpoint',
		'ppm'	=>	'image/x-portable-pixmap',
		'pps'	=>	'application/vnd.ms-powerpoint',
		'ppt'	=>	'application/vnd.ms-powerpoint',
		'pqf'	=>	'application/x-cprplayer',
		'pqi'	=>	'application/cprplayer',
		'prc'	=>	'application/x-prc',
		'proxy'	=>	'application/x-ns-proxy-autoconfig',
		'ps'	=>	'application/postscript',
		'ptlk'	=>	'application/listenup',
		'pub'	=>	'application/x-mspublisher',
		'pvx'	=>	'video/x-pv-pvx',
		'qcp'	=>	'audio/vnd.qcelp',
		'qt'	=>	'video/quicktime',
		'qti'	=>	'image/x-quicktime',
		'qtif'	=>	'image/x-quicktime',
		'rng'	=>	'application/vnd.nokia.ringing-tone',
		'r3t'	=>	'text/vnd.rn-realtext3d',
		'ra'	=>	'audio/x-pn-realaudio',
		'ram'	=>	'audio/x-pn-realaudio',
		'rar'	=>	'application/x-rar-compressed',
		'ras'	=>	'image/x-cmu-raster',
		'rdf'	=>	'application/rdf+xml',
		'rf'	=>	'image/vnd.rn-realflash',
		'rgb'	=>	'image/x-rgb',
		'rlf'	=>	'application/x-richlink',
		'rm'	=>	'audio/x-pn-realaudio',
		'rmf'	=>	'audio/x-rmf',
		'rmm'	=>	'audio/x-pn-realaudio',
		'rmvb'	=>	'audio/x-pn-realaudio',
		'rnx'	=>	'application/vnd.rn-realplayer',
		'roff'	=>	'application/x-troff',
		'rp'	=>	'image/vnd.rn-realpix',
		'rpm'	=>	'audio/x-pn-realaudio-plugin',
		'rt'	=>	'text/vnd.rn-realtext',
		'rte'	=>	'x-lml/x-gps',
		'rtf'	=>	'application/rtf',
		'rtg'	=>	'application/metastream',
		'rtx'	=>	'text/richtext',
		'rv'	=>	'video/vnd.rn-realvideo',
		'rwc'	=>	'application/x-rogerwilco',
		's3m'	=>	'audio/x-mod',
		's3z'	=>	'audio/x-mod',
		'sca'	=>	'application/x-supercard',
		'scd'	=>	'application/x-msschedule',
		'sdf'	=>	'application/e-score',
		'sea'	=>	'application/x-stuffit',
		'sgm'	=>	'text/x-sgml',
		'sgml'	=>	'text/x-sgml',
		'sh'	=>	'application/x-sh',
		'shar'	=>	'application/x-shar',
		'shtml'	=>	'magnus-internal/parsed-html',
		'shw'	=>	'application/presentations',
		'si6'	=>	'image/si6',
		'si7'	=>	'image/vnd.stiwap.sis',
		'si9'	=>	'image/vnd.lgtwap.sis',
		'sis'	=>	'application/vnd.symbian.install',
		'sisx'	=>	'x-epoc/x-sisx-app',
		'sit'	=>	'application/x-stuffit',
		'skd'	=>	'application/x-Koan',
		'skm'	=>	'application/x-Koan',
		'skp'	=>	'application/x-Koan',
		'skt'	=>	'application/x-Koan',
		'slc'	=>	'application/x-salsa',
		'smd'	=>	'audio/x-smd',
		'smi'	=>	'application/smil',
		'smil'	=>	'application/smil',
		'smp'	=>	'application/studiom',
		'smz'	=>	'audio/x-smd',
		'snd'	=>	'audio/basic',
		'spc'	=>	'text/x-speech',
		'spl'	=>	'application/futuresplash',
		'spr'	=>	'application/x-sprite',
		'sprite'=>	'application/x-sprite',
		'spt'	=>	'application/x-spt',
		'src'	=>	'application/x-wais-source',
		'stk'	=>	'application/hyperstudio',
		'stm'	=>	'audio/x-mod',
		'sv4cpio'=>	'application/x-sv4cpio',
		'sv4crc'=>	'application/x-sv4crc',
		'svf'	=>	'image/vnd',
		'svg'	=>	'image/svg-xml',
		'svh'	=>	'image/svh',
		'svr'	=>	'x-world/x-svr',
		'swf'	=>	'application/x-shockwave-flash',
		'swfl'	=>	'application/x-shockwave-flash',
		't'		=>	'application/x-troff',
		'tad'	=>	'application/octet-stream',
		'talk'	=>	'text/x-speech',
		'tar'	=>	'application/x-tar',
		'taz'	=>	'application/x-tar',
		'tbp'	=>	'application/x-timbuktu',
		'tbt'	=>	'application/x-timbuktu',
		'tcl'	=>	'application/x-tcl',
		'tex'	=>	'application/x-tex',
		'texi'	=>	'application/x-texinfo',
		'texinfo'=>	'application/x-texinfo',
		'tgz'	=>	'application/x-tar',
		'thm'	=>	'application/vnd.eri.thm',
		'tif'	=>	'image/tiff',
		'tiff'	=>	'image/tiff',
		'tki'	=>	'application/x-tkined',
		'tkined'=>	'application/x-tkined',
		'toc'	=>	'application/toc',
		'toy'	=>	'image/toy',
		'tr'	=>	'application/x-troff',
		'trk'	=>	'x-lml/x-gps',
		'trm'	=>	'application/x-msterminal',
		'tsi'	=>	'audio/tsplayer',
		'tsp'	=>	'application/dsptype',
		'tsv'	=>	'text/tab-separated-values',
		'tsv'	=>	'text/tab-separated-values',
		'ttf'	=>	'application/octet-stream',
		'ttz'	=>	'application/t-time',
		'txt'	=>	'text/plain',
		'ult'	=>	'audio/x-mod',
		'ustar'	=>	'application/x-ustar',
		'uu'	=>	'application/x-uuencode',
		'uue'	=>	'application/x-uuencode',
		'vcd'	=>	'application/x-cdlink',
		'vcf'	=>	'text/x-vcard',
		'vdo'	=>	'video/vdo',
		'vib'	=>	'audio/vib',
		'viv'	=>	'video/vivo',
		'vivo'	=>	'video/vivo',
		'vmd'	=>	'application/vocaltec-media-desc',
		'vmf'	=>	'application/vocaltec-media-file',
		'vmi'	=>	'application/x-dreamcast-vms-info',
		'vms'	=>	'application/x-dreamcast-vms',
		'vox'	=>	'audio/voxware',
		'vqe'	=>	'audio/x-twinvq-plugin',
		'vqf'	=>	'audio/x-twinvq',
		'vql'	=>	'audio/x-twinvq',
		'vre'	=>	'x-world/x-vream',
		'vrml'	=>	'x-world/x-vrml',
		'vrt'	=>	'x-world/x-vrt',
		'vrw'	=>	'x-world/x-vream',
		'vts'	=>	'workbook/formulaone',
		'wav'	=>	'audio/x-wav',
		'wax'	=>	'audio/x-ms-wax',
		'wbmp'	=>	'image/vnd.wap.wbmp',
		'web'	=>	'application/vnd.xara',
		'wi'	=>	'image/wavelet',
		'wis'	=>	'application/x-InstallShield',
		'wm'	=>	'video/x-ms-wm',
		'wma'	=>	'audio/x-ms-wma',
		'wmd'	=>	'application/x-ms-wmd',
		'wmf'	=>	'application/x-msmetafile',
		'wml'	=>	'text/vnd.wap.wml',
		'wmlc'	=>	'application/vnd.wap.wmlc',
		'wmls'	=>	'text/vnd.wap.wmlscript',
		'wmlsc'	=>	'application/vnd.wap.wmlscriptc',
		'wmlscript'	=>	'text/vnd.wap.wmlscript',
		'wmv'	=>	'audio/x-ms-wmv',
		'wmx'	=>	'video/x-ms-wmx',
		'wmz'	=>	'application/x-ms-wmz',
		'wpng'	=>	'image/x-up-wpng',
		'wpt'	=>	'x-lml/x-gps',
		'wri'	=>	'application/x-mswrite',
		'wrl'	=>	'x-world/x-vrml',
		'wrz'	=>	'x-world/x-vrml',
		'ws'	=>	'text/vnd.wap.wmlscript',
		'wsc'	=>	'application/vnd.wap.wmlscriptc',
		'wv'	=>	'video/wavelet',
		'wvx'	=>	'video/x-ms-wvx',
		'wxl'	=>	'application/x-wxl',
		'x-gzip'=>	'application/x-gzip',
		'xar'	=>	'application/vnd.xara',
		'xbm'	=>	'image/x-xbitmap',
		'xdm'	=>	'application/x-xdma',
		'xdma'	=>	'application/x-xdma',
		'xdw'	=>	'application/vnd.fujixerox.docuworks',
		'xht'	=>	'application/xhtml+xml',
		'xhtm'	=>	'application/xhtml+xml',
		'xhtml'	=>	'application/xhtml+xml',
		'xla'	=>	'application/vnd.ms-excel',
		'xlc'	=>	'application/vnd.ms-excel',
		'xll'	=>	'application/x-excel',
		'xlm'	=>	'application/vnd.ms-excel',
		'xls'	=>	'application/vnd.ms-excel',
		'xlt'	=>	'application/vnd.ms-excel',
		'xlw'	=>	'application/vnd.ms-excel',
		'xm'	=>	'audio/x-mod',
		'xml'	=>	'text/xml',
		'xmz'	=>	'audio/x-mod',
		'xpi'	=>	'application/x-xpinstall',
		'xpm'	=>	'image/x-xpixmap',
		'xsit'	=>	'text/xml',
		'xsl'	=>	'text/xml',
		'xul'	=>	'text/xul',
		'xwd'	=>	'image/x-xwindowdump',
		'xyz'	=>	'chemical/x-pdb',
		'yz1'	=>	'application/x-yz1',
		'z'		=>	'application/x-compress',
		'zac'	=>	'application/x-zaurus-zac',
		'zip'	=>	'application/zip',
		'nokia-op-logo'	=>	'image/vnd.nok-oplogo-color',
	);
	if ( isset($mime_array[$mime]) ){
		return $mime_array[$mime];
	}else{
		return 'application/octet-stream';
	}
}

function unicode2utf8($str,$code=''){
	$str = unescape_unicode($str);
    return $str;
}

function unescape_unicode($str) {
	$str = rawurldecode($str);
	preg_match_all('/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U', $str,$r);
	$arr = $r[0];
	foreach($arr as $k=>$v) {
		if(substr($v,0,2) == "%u"){
			$arr[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4)));
		}elseif(substr($v,0,3) == "&#x"){
			$arr[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1))) ;
		}elseif(substr($v,0,2) == "&#"){
			$arr[$k] = iconv("UCS-2","UTF-8",pack("n",substr($v,2,-1)));
		}
	}
	return join('',$arr);
}


Function GetHost($h){
	$h = strtolower('.'.$h);
	$arr = array(
			'7'	=>	array('.org.cn','.gov.cn','.net.cn','.com.cn','.com.hk'),
			'4'	=>	array('.com','.net','.org','.tel'),
			'3'	=>	array('.la','.co','.cn','.me','.cc','.hk','.tk','.in','.gp','.us','.lc'),
			'5'	=>	array('.mobi','.info','.name','.asia'),
			);
	foreach($arr as $nn => $houzhui){
		$h_len = strlen($h)-$nn;
		foreach( $houzhui as $val){
			if ( substr($h,$h_len,$nn) == $val ){
				$temp = substr($h,0,$h_len);
				return substr($temp,strrpos($temp,'.')+1,$h_len).$val;
			}
		}
	}
	return $h;
}

function qqagent_init(){
	global $HTTP_Q_UA,$HTTP_Q_AUTH,$HTTP_Q_GUID;
	$HTTP_Q_UA = $HTTP_Q_AUTH = $HTTP_Q_GUID = '' ;
	$result = @file_get_contents(DIR.'temp/cache_forever/agent.php');
	if ( $result === false ){
		$HTTP_Q = array();
	}else{
		$result = @unserialize($result);
		if ( $result === false || $result === NULL ){
			$HTTP_Q = array();
		}else{
			$HTTP_Q = $result;
		}
	}
	if ( isset($_SERVER["HTTP_Q_UA"]) && isset($_SERVER["HTTP_Q_AUTH"]) ) {
		if ( $_SERVER["HTTP_Q_UA"]<>$HTTP_Q_UA || $_SERVER["HTTP_Q_AUTH"]<>$HTTP_Q_AUTH ){
			$HTTP_Q_UA = isset($_SERVER["HTTP_Q_UA"]) ? db_safe_dropstr($_SERVER['HTTP_Q_UA']) : '' ;
			$HTTP_Q_AUTH = isset($_SERVER["HTTP_Q_AUTH"]) ? db_safe_dropstr($_SERVER['HTTP_Q_AUTH']) : '' ;
			$HTTP_Q_GUID = isset($_SERVER["HTTP_Q_GUID"]) ? db_safe_dropstr($_SERVER['HTTP_Q_GUID']) : '' ;
			$HTTP_Q = array(
				'HTTP_Q_UA'=>$HTTP_Q_UA,
				'HTTP_Q_AUTH'=>$HTTP_Q_AUTH,
				'HTTP_Q_GUID'=>$HTTP_Q_GUID,
			);
			@file_put_contents(DIR.'temp/cache_forever/agent.php',serialize($HTTP_Q));
		}
	}
	foreach($HTTP_Q as $k=>$v){
		$$k = $v;
	}
}
	function db_safe_dropstr($str){
		$str = str_replace(array('"','\''),'',$str);
		return $str;
	}

function user_ip() {
	if ( isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){
		return $_SERVER['HTTP_CLIENT_IP'] ;
	}elseif ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		return $_SERVER['HTTP_X_FORWARDED_FOR'] ;
	}elseif ( isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])){
		return $_SERVER['REMOTE_ADDR'] ;
	}else{
		return '127.0.0.1' ;
	}
}

function ubb_copy($str){
	if ( !is_string($str) ){
		return $str;
	}
	$a = strpos($str,'[copy=');
	if ( $a===false ){
		return $str;
	}
	$b = strpos($str,']');
	if ( $b===false || $a >= $b ){
		return $str;
	}
	$str = preg_replace('/\[copy=([0-9]{1,5})\]/ies', "ubb_copy_true('\\1')", $str);
	return $str;
}

function ubb_copy_true($id){
	global $browser;
	$arr = $browser->copy_look($id);
	if ( $arr ){
		return $arr['content'];
	}else{
		return '[copy='.$id.']';
	}
}

function ob_gzip($content){
	if ( !defined('no_ob_gzip') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && !headers_sent() && extension_loaded('zlib') && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false ){
		$content = gzencode($content,9);
		header('Content-Encoding: gzip');
		header('Vary: Accept-Encoding');
		header('Content-Length: '.strlen($content));
	}
	return $content;
}

function str_pos($str,$start,$end){
	$i = stripos($str,$start);
	if ( $i === false){
		return '';
	}
	$t = substr($str,$i+strlen($start));
	$x = stripos($t,$end);
	if ( $x === false){
		return '';
	}
	$t = substr($t,0,$x);
	return $t;
}


function fix_r_n_t($str){
	$str = str_replace(array("\n","\r","\t",chr(0)),'',$str);
	return $str;
}

function fixchinese($str){
	@$str = iconv('utf-8','utf-8//IGNORE', $str);
	return $str;
}

function deldir($dir,$deldir=true) {
	if ( is_dir($dir) ) {
		$dh = opendir($dir);
		while ( $file = readdir ($dh) ) {
			if( $file!='.' && $file!='..' ) {
				$fullpath = $dir.'/'.$file;
				if( !is_dir($fullpath) ) {
					@unlink($fullpath);
				}else{
					deldir($fullpath,true);
				}
			}
		}
		closedir($dh);
		if ( $deldir ){
			@rmdir($dir);
		}
	}
}

function IsWap2(){
	static $result = null;
	if ( $result !== null ){
		return $result;
	}
	$ua = isset($_SERVER['HTTP_ACCEPT']) ? '.'.strtolower($_SERVER['HTTP_ACCEPT']) : false;
	if ( $ua && (
		strpos($ua,'text/html') ||
		strpos($ua,'application/xhtml+xml')
		)){
		$result= true;
	}else{
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? '.'.$_SERVER['HTTP_USER_AGENT'] : false;
		if (
			$ua && (
			stripos($ua,'MSIE') ||
			stripos($ua,'Windows') ||
			stripos($ua,'Mozilla') ||
			stripos($ua,'Symbian') ||
			stripos($ua,'iPhone') ||
			stripos($ua,'KHTML') ||
			stripos($ua,'Chrome') ||
			stripos($ua,'ucweb') ||
			stripos($ua,'smartphone') ||
			stripos($ua,'blackberry') ||
			stripos($ua,'opera') ||
			stripos($ua,'AppleWebKit')
			)){
			$result= true;
		}else{
			$result= false;
		}
	}
	return $result;
}

function getUTFString($string,&$code=''){
	$code = mb_detect_encoding($string, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
	return @mb_convert_encoding($string, 'utf-8', $code);
}


function writefile($file,$content){
	return file_put_contents($file,$content);
}

function bitsize($num) {
	if (!preg_match("/^[0-9]+$/", $num)){
		return 0 ;
	}
	$type = array('B', 'KB', 'MB', 'GB', 'TB', 'PB') ;
	$j = 0 ;
	while ($num >= 1024) {
		if ($j >= 5){
			return $num.$type[$j] ;
		}
		$num = $num / 1024 ;
		$j++ ;
	}
	return number_format($num, 2) . $type[$j] ;
}

class funCrypt {
	private $way = 1 ;
	private $password1 = '' ;
	private $lockstream = 'lDEFABstCNOPyzghIJK6/7nopqr89LMmGHijQRSTUwVWXYZabcdexkf+013245uv=' ;
	function __construct($passwo='') {
		global $b_set;
		if ( $passwo== ''){
			$passwo = $b_set['key3'];
		}
		$this->password1 = md5($passwo) ;
		$this->lockstream = $b_set['key4'];
	}
	//加密
	function enCrypt($txtStream) {
		if ($this->way == 1) {
			$lockLen = strlen($this->lockstream) ;
			$lockCount = rand(0, $lockLen - 1) ;
			$randomLock = $this->lockstream[$lockCount] ;
			$password = md5($this->password1 . $randomLock) ;
			$txtStream = base64_encode($txtStream) ;
			for ($i = 0, $j = 0, $k = 0, $tmpStream = ''; $i < strlen($txtStream); $i++) {
				$k = $k == strlen($password) ? 0 : $k ;
				$j = (strpos($this->lockstream, $txtStream[$i]) + $lockCount + ord($password[$k])) % ($lockLen) ;
				$tmpStream .= $this->lockstream[$j] ;
				$k++ ;
			}
			return base64_encode($tmpStream . $randomLock) ;
		} else {
			srand((double)microtime() * 1000000) ;
			$encrypt_key = md5(rand(0, 32000)) ;
			$ctr = 0 ;
			$tmp = '' ;
			for ($i = 0; $i < strlen($txt); $i++) {
				$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr ;
				$tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]) ;
			}
			return base64_encode($this->passport_key($tmp, $key)) ;
		}
	}
	//解密
	function deCrypt($txtStream) {
		if ($this->way == 1) {
			$txtStream = base64_decode($txtStream) ;
			$lockLen = strlen($this->lockstream) ;
			$txtLen = strlen($txtStream) ;
			if ($txtLen < 1)
				return '' ;
			$randomLock = $txtStream[$txtLen - 1] ;
			$lockCount = strpos($this->lockstream, $randomLock) ;
			$password = md5($this->password1 . $randomLock) ;
			$txtStream = substr($txtStream, 0, $txtLen - 1) ;
			$tmpStream = '' ;
			$j = 0 ;
			$k = 0 ;
			for ($i = 0; $i < strlen($txtStream); $i++) {
				$k = $k == strlen($password) ? 0 : $k ;
				$j = strpos($this->lockstream, $txtStream[$i]) - $lockCount - ord($password[$k]) ;
				while ($j < 0) {
					$j += $lockLen ;
				}
				$tmpStream .= $this->lockstream[$j] ;
				$k++ ;
			}
			return base64_decode($tmpStream) ;
		} else {
			$txt = $this->passport_key(base64_decode($txt)) ;
			for ($i = 0, $tmp = ''; $i < strlen($txt); $i++) {
				$tmp .= $txt[++$i] ^ $txt[$i] ;
			}
			return $tmp ;
		}
	}

	function passport_key($txt) {
		$encrypt_key = $this->password1 ;
		for ($i = 0, $ctr = 0, $tmp = ''; $i < strlen($txt); $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr ;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++] ;
		}
		return $tmp ;
	}
}

function fix_disposition($str){
	$str = str_ireplace('filename*="utf8\'\'','filename="',$str);
	$str = str_ireplace('"','',$str);
	$str = str_ireplace('\'','',$str);
	$str = str_ireplace(' ','',$str);

	if ( substr($str,0,20) == 'attachment;filename=' ){
		$str = substr($str,20);
	}
	return fix_localbase($str);
}

function fix_localbase($str){
	$str = str_replace('*','_',$str);
	$str = str_replace('`','_',$str);
	$str = str_replace('!','_',$str);
	$str = str_replace('"','_',$str);
	$str = str_replace('\'','_',$str);
	$str = str_replace(':','_',$str);
	$str = str_replace('?','_',$str);
	$str = str_replace('|','_',$str);
	$str = str_replace('<','_',$str);
	$str = str_replace('>','_',$str);
	$str = str_replace('\'','_',$str);
	$str = str_replace('"','_',$str);
	return $str;
}

function fix_basename($str){
	$str = basename($str);
	$a = strpos($str,'?');
	if ( $a !== false ){
		$str = substr($str,0,$a);
	}
	return fix_localbase($str);
}

function set_cache_forever($sid,$content){
	writefile(DIR.'temp/cache_forever/'.fix_localbase(sha1($sid)),$content);
}

function get_cache_forever($sid,$content=null){
	$sid = DIR.'temp/cache_forever/'.fix_localbase(sha1($sid));
	if ( file_exists($sid) ){
		@$content = file_get_contents($sid);
	}
	return $content;
}

function str_encrypt($str,$code=''){
	if ( $code == ''){
		global $b_set;
		$code = $b_set['key2'];
	}
	$Crypt = new funCrypt($code);
	return $Crypt->enCrypt($str);
}

function str_decrypt($str,$code=''){
	if ( $code == ''){
		global $b_set;
		$code = $b_set['key2'];
	}
	$Crypt = new funCrypt($code);
	return $Crypt->deCrypt($str);
}


function num2short($num = 0){
	static $arr = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if ( $num <= 61 ){
		return $arr[$num];
	}elseif( $num <= 3905 ){
		$int = floor($num/62);
		$mod = $num - $int * 62;
		return $arr[$int - 1] . $arr[$mod];
	}else{
		$int = floor($num/3905);
		$mod = $num - $int * 3905;
		return num2short($int-1) . num2short($mod+62);
	}
}

function short2num($str){
	static $arr = array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','a'=>'10','b'=>'11','c'=>'12','d'=>'13','e'=>'14','f'=>'15','g'=>'16','h'=>'17','i'=>'18','j'=>'19','k'=>'20','l'=>'21','m'=>'22','n'=>'23','o'=>'24','p'=>'25','q'=>'26','r'=>'27','s'=>'28','t'=>'29','u'=>'30','v'=>'31','w'=>'32','x'=>'33','y'=>'34','z'=>'35','A'=>'36','B'=>'37','C'=>'38','D'=>'39','E'=>'40','F'=>'41','G'=>'42','H'=>'43','I'=>'44','J'=>'45','K'=>'46','L'=>'47','M'=>'48','N'=>'49','O'=>'50','P'=>'51','Q'=>'52','R'=>'53','S'=>'54','T'=>'55','U'=>'56','V'=>'57','W'=>'58','X'=>'59','Y'=>'60','Z'=>'61');
    $a = strlen($str);
    if ( $a <= 1 ){
        if ( isset($arr[$str]) ){
            return $arr[$str];
        }else{
            return 0;
        }
    }elseif ( $a == 2 ){
        if ( isset($arr[$str[0]]) && isset($arr[$str[1]]) ){
            $x = (($arr[$str[0]] + 1) * 62 + $str[1]);
            if ( num2short($x) != $str){
                $x = (($arr[$str[0]] + 1) * 62 + $str[1] + $arr[$str[1]] );
            }
            return $x;
        }else{
            return 0;
        }
    }else{
        exit('No.!!!short2num');
    }
}
function getshortname($id){
    return ceil(($id+1)/250);
}

function getshortname_history($id){
    return ceil(($id+1)/5);
}

function ImageCreateFromBMP($filename){
	if ( !$f1 = @fopen ($filename ,'rb')){
		return FALSE ;
	}
	$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread ($f1,14));
	if ($FILE['file_type'] != 19778 ){
		return FALSE ;
	}
	$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'. '/Vcompression/Vsize_bitmap/Vhoriz_resolution'. '/Vvert_resolution/Vcolors_used/Vcolors_important', fread ($f1 ,40 ));
	$BMP['colors'] = pow (2 ,$BMP['bits_per_pixel']);
	if ($BMP['size_bitmap'] == 0 ){
		$BMP['size_bitmap'] = $FILE ['file_size'] - $FILE ['bitmap_offset'];
	}
	unset($FILE);
	$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8 ;
	$BMP['bytes_per_pixel2'] = ceil ($BMP['bytes_per_pixel']);
	$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4 );
	$BMP['decal'] -= floor ($BMP['width']*$BMP['bytes_per_pixel']/4 );
	$BMP['decal'] = 4 -(4 *$BMP['decal']);
	if ($BMP['decal'] == 4 ){
		$BMP['decal'] = 0 ;
	}
	$PALETTE = array ();
	if ($BMP['colors'] < 16777216 ){
		$PALETTE = unpack ('V'. $BMP['colors'], fread($f1 ,$BMP['colors']*4 ));
	}
	$IMG = fread ($f1 ,$BMP['size_bitmap']);
	fclose ($f1 );
	$VIDE = chr (0);
	$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
	$P = 0 ;
	$Y = $BMP['height']-1 ;
	while ($Y >= 0 ){
		$X =0 ;
		while ($X < $BMP['width']){
			if ($BMP['bits_per_pixel'] == 24 ){
				$COLOR = unpack ("V",substr ($IMG ,$P ,3 ). $VIDE );
			}elseif ($BMP['bits_per_pixel'] == 16 ){
				$COLOR = unpack ("n",substr ($IMG ,$P ,2 ));
				$COLOR [1] = $PALETTE [$COLOR [1]+1];
			} elseif ($BMP['bits_per_pixel'] == 8 ){
				$COLOR = unpack ("n",$VIDE.substr ($IMG ,$P ,1 ));
				$COLOR [1] = $PALETTE [$COLOR [1]+1];
			} elseif ($BMP['bits_per_pixel'] == 4 ){
				$COLOR = unpack ("n",$VIDE.substr ($IMG ,floor ($P ),1 ));
				if(($P *2 )%2 == 0 ){
					$COLOR [1] = ($COLOR [1] >> 4 ) ;
				}else{
					$COLOR [1] = ($COLOR [1] & 0x0F );
				}
				$COLOR [1] = $PALETTE [$COLOR [1]+1];
			} elseif ($BMP['bits_per_pixel'] == 1 ){
				$COLOR = unpack ("n",$VIDE.substr ($IMG ,floor ($P ),1 ));
				if (($P *8 )%8 == 0 ){
					$COLOR [1] = $COLOR [1] >>7 ;
				}elseif (($P *8 )%8 == 1 ){
					$COLOR [1] = ($COLOR [1] & 0x40 )>>6 ;
				}elseif (($P *8 )%8 == 2 ){
					$COLOR [1] = ($COLOR [1] & 0x20 )>>5 ;
				}elseif (($P *8 )%8 == 3 ){
					$COLOR [1] = ($COLOR [1] & 0x10 )>>4 ;
				}elseif (($P *8 )%8 == 4 ){
					$COLOR [1] = ($COLOR [1] & 0x8 )>>3 ;
				}elseif (($P *8 )%8 == 5 ){
					$COLOR [1] = ($COLOR [1] & 0x4 )>>2 ;
				}elseif (($P *8 )%8 == 6 ){
					$COLOR [1] = ($COLOR [1] & 0x2 )>>1 ;
				}elseif (($P *8 )%8 == 7 ){
					$COLOR [1] = ($COLOR [1] & 0x1 );
				}
				$COLOR [1] = $PALETTE [$COLOR [1]+1];
			} else{
				return FALSE ;
			}
			imagesetpixel($res ,$X ,$Y ,$COLOR [1]);
			$X ++;
			$P += $BMP['bytes_per_pixel'];
		}
		$Y --;
		$P +=$BMP['decal'];
	}
	return $res ;
}

Function top_wap2($title,$refreshurl='',$return=false,$code='utf-8',$time=1){
	$refreshurl && $refreshurl = '<meta http-equiv="refresh" content="'.$time.';url='.$refreshurl.'"/>';
    $str='<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml; charset='.$code.'"/>
<meta http-equiv="Cache-Control" content="must-revalidate,no-cache"/>'.$refreshurl.'
<title>'.$title.'</title><style>'.file_get_contents(DIR.'template/wap2.css').'</style>
</head><body>';
    if ( $code <>'utf-8'){
        @$str = iconv('utf-8',$code.'//TRANSLIT', $str);
    }
    if ( $return ){
        return $str;
    }else{
        echo $str;
        return null;
    }
}

Function foot_wap2($exit=true,$return=false,$code='utf-8'){
    $str = '</body></html>';
    if ( $code <>'utf-8'){
        @$str = iconv('utf-8',$code.'//TRANSLIT', $str);
    }
    if ( $return ){
        return $str;
    }else{
    	echo $str;
    }
	exit_fix_html($exit);
    return null;
}


////////////////////////////////////////////////////////


Function top_wap1($title,$refreshurl='',$return=false,$code='utf-8',$time=1){
	header('Content-Type: text/vnd.wap.wml; charset='.$code);
	if ($refreshurl){
		$refreshurl = '<card id="main" title="'.$title.'" ontimer="'.$refreshurl.'"><timer value="'.$time.'"/>';
	}else{
		$refreshurl = '<card id="main" title="'.$title.'">';
	}
$str='<?xml version="1.0" encoding="'.$code.'"?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
<wml><head><meta http-equiv="Cache-Control" content="max-age=0"/>
<meta http-equiv="Cache-Control" content="no-cache"/></head>'.$refreshurl.'<p>';
    if ( $code <>'utf-8'){
        @$str = iconv('utf-8',$code.'//TRANSLIT', $str);
    }
    if ( $return ){
        return $str;
    }else{
    	echo $str;
        return null;
    }
}

Function foot_wap1($exit=true,$return=false,$code='utf-8'){
    $str = '</p></card></wml>';
    if ( $code <>'utf-8'){
        @$str = iconv('utf-8',$code.'//TRANSLIT', $str);
    }
    if ( $return ){
        return $str;
    }else{
    	echo $str;
    }
	exit_fix_html($exit);
    return null;
}

function exit_fix_html($exit=true){
	$str = ob_get_contents();;
	ob_clean();
	$str = str_replace(array("\n","\r","\t"),'',$str);
	$str = str_replace(' />','/>',$str);
	echo $str;
	if ( $exit){
		ob_end_flush();exit;
	}
}

function returnJson($data){
	$data = serialize($data);
	return "<jiuwap_api time=\"".time_()."\" host=\"{$_SERVER['SERVER_NAME']}\">\r\n<return>$data</return>\r\n</jiuwap_api>";
	//exit;
}

function getJson($data){
	if ( strpos($data,'<jiuwap_api') === false || strpos($data,'<return>') === false  || strpos($data,'</return>') === false || strpos($data,'</jiuwap_api>') == false ){
		return false;
	}
	$a = strpos($data,'<return>');
	$b = strrpos($data,'</return>');

	$data = substr($data,$a+8,$b-$a-8);
	return unserialize($data);
}

function quick_connect($url){
	$fp = @fsockopen('localhost',80);
	if ( !$fp ){
		break;
	}
	$out = "GET /{$url} HTTP/1.1\r\n";
	$out .= "Host: {$_SERVER['SERVER_NAME']}\r\n";
	$out .= "Connection: Close\r\n\r\n";
	@fwrite($fp, $out);
	@fclose($fp);
}
