<?php
/*
 *
 *	MRP(国产手机mtk手机应用软件)
 *
 *	2011-4-19 @ jiuwap.cn
 *
 * 源代码由hu60.cn开发,本人(tianyiw)整合成php类,修正部分代码.
 *
 *
 *
 *	//得到MRP信息,返回数组型
 *	mrp::get($mrp_file)
 *
 *	//编辑MRP信息,返回逻辑值
 *	mrp::put($mrp_file,$info_array)
 *
 *	//解压MRP文件,返回逻辑值
 *	mrp::unpack($mrp_file,$unzip_dir)
 *
 *	//打包MRP文件,返回逻辑值
 *	mrp::pack($mrp_file,$file_array,$gzip_class,$mrp_template,要踢处的路径前缀)
 *
 *
 */


if (!defined('SYSFILECODE')){
	//设置服务器文件名编码
	define('SYSFILECODE','gb2312');
}

class mrp{

	//得到mrp信息
	//mrp::get(MRP文件)
	static function get($f){
		if ( !$f = @fopen($f,'rb') or fread($f,4) != 'MRPG'){
			@fclose($f);
			return false;
		}
		fseek($f,52,SEEK_SET);
		$ch=self::gb2u0(fread($f,16));//串号
		fseek($f,192,SEEK_SET);
		$bb=hexdec(bin2hex(fread($f,4)));//版本id-2
		fseek($f,196,SEEK_SET);
		$id=hexdec(bin2hex(fread($f,4)));//应用id-2
		fseek($f,16,SEEK_SET);
		$nn=self::gb2u0(fread($f,12));//内部文件名
		fseek($f,28,SEEK_SET);
		$xn=self::gb2u0(fread($f,24));//显示名
		fseek($f,88,SEEK_SET);
		$zz=self::gb2u0(fread($f,40));//作者
		fseek($f,68,SEEK_SET);
		$bb2=hexdec(bin2hex(strrev(fread($f,4))));//版本id
		fseek($f,72,SEEK_SET);
		$id2=hexdec(bin2hex(strrev(fread($f,4))));//应用id
		fseek($f,128,SEEK_SET);
		$js=self::gb2u0(fread($f,64));//介绍
		fclose($f);
		return array(
			'id'=>$id2.'&'.$id,
			'ch'=>$ch,
			'bb'=>$bb2.'&'.$bb,
			'nn'=>$nn,
			'xn'=>$xn,
			'zz'=>$zz,
			'js'=>$js);
	}



	//保存MRP信息
	//mrp::put(MRP文件,数组)
	static function put($fn,$v){
		$v= $v + array(
			'id'=>'1000&0',
			'ch'=>'0123456789abcdef',
			'bb'=>'1000&0',
			'nn'=>'new.mrp',
			'xn'=>'new_MRP',
			'zz'=>'Jiuwap.cn',
			'js'=>'This is a new MRP file, Created By jiuwap.cn MRP PHPLIB.'
		);

		if ( !$fp = @fopen($fn,'r+') or fread($fp,4) != 'MRPG' ){
			@fclose($f);
			return false;
		}
		$ch=self::u2gb0($v['ch'],16);//串号
		$nn=self::u2gb0($v['nn'],12);//内部文件名
		$xn=self::u2gb0($v['xn'],24);//显示名
		$zz=self::u2gb0($v['zz'],40);//作者
		$js=self::u2gb0($v['js'],64);//介绍
		$id=explode('&',$v['id']);
		$id2=pack('H*',self::binadd(dechex($id[0]),8));
		$id=pack('H*',self::binadd(dechex($id[1]),8));
		$bb=explode('&',$v['bb']);
		$bb2=pack('H*',self::binadd(dechex($bb[0]),8));
		$bb=pack('H*',self::binadd(dechex($bb[1]),8));
		fseek($fp,16);
		fwrite($fp,$nn.$xn.$ch.strrev($bb2).strrev($id2));
		fseek($fp,88);
		fwrite($fp,$zz.$js.$bb.$id);
		fclose($fp);
		return true;
	}


	//MRP解包
	//mrp::unpack(MRP文件,解压目录)
	static function unpack($fname,$dir){
		if ( !$f=@fopen($fname,'rb') or fread($f,4) != 'MRPG'){
			@fclose($f);
			return false;
		}

		$r=240;
		fseek($f,4,SEEK_SET);
		$s=hexdec(bin2hex(strrev(fread($f,4))));
		while(true){
			if($r>$s){
				break;
			}
			fseek($f,$r,SEEK_SET);
			$x=hexdec(bin2hex(fread($f,1)));
			$r+=4;
			fseek($f,$r,SEEK_SET);
			if ( $x == 0){
				return false;
			}
			$n=fread($f,$x-1);
			$r+=$x;
			fseek($f,$r,SEEK_SET);
			$v=hexdec(bin2hex(strrev(fread($f,4))));
			$r+=4;
			fseek($f,$r,SEEK_SET);
			$l=hexdec(bin2hex(strrev(fread($f,4))));
			$r+=8;
			$list[]=array('n'=>$n,'v'=>$v,'l'=>$l);
		}

		self::mkdirs($dir);
		foreach($list as $a){
			fseek($f,$a['v'],SEEK_SET);

			$gz_data = fread($f,$a['l']);

			$file = $dir.'/'.$a['n'];

			file_put_contents($file,$gz_data);

			$gz = gzopen($file,'r');
			$data = gzread($gz,10000000);
			gzclose($gz);
			file_put_contents($file,$data);

			//if ( !$data = gzdecode($gz_data) ){
			//	$data = $gz_data;
			//}
		}
		fclose($f);
		return $list;
	}


	//打包
	//mrp::pack(生成的mrp文件,打包文件列表,gzip压缩等级,mrp模板,要踢处的路径前缀)
	static function pack($mrp,$list,$gzip=3,$f=false,$XiangDuiLuJing=false){
		if($gzip<1 or $gzip>9){
			$gzip=3;
		}
		if ( !is_array($list) ){
			$list = array($list);
		}
		$nn = 0;
		$lst = array();
		if ( $XiangDuiLuJing ){
			$ii = strlen($XiangDuiLuJing);
		}
		foreach($list as &$a){
			//@$a = iconv('utf-8',SYSFILECODE,$a);
			if( !file_exists($a) ){
				continue;
			}
			if ( !$tempgz_one = @file_get_contents($a) ){
				continue;
			}

			if ( $XiangDuiLuJing ){
				$a = substr($a,$ii);
			}

			$gztemp = 'gz_'.rand(0,99999).time().'_tmp';
			$gz = gzopen($gztemp,'w'.$gzip);
			gzwrite($gz,$tempgz_one);
			gzclose($gz);
			$tempgz_one = @file_get_contents($gztemp);
			@unlink($gztemp);

			//$tempgz_one = gzencode($tempgz_one,$gzip);
			$al = strlen($tempgz_one);
			//$na = eregi_replace(chr(94).'.*[\\/]([0-9]+\.)?(.['.chr(94).'\\/]*)\.txt$','\\2',$a);
			$na = $a;
			$n =strlen($na)+1;
			$nn += $n;
			$lst[] = array($na,$tempgz_one,$al,$n);
		}
		$lstl=16*($jc=count($lst))+$nn;
		$lst[0][]=$lstl+248+$lst[0][3];
		for($a=1;$a<$jc;$a++){
			$lst[$a][]=$lst[$a-1][4]+$lst[$a-1][2]+8+$lst[$a][3];
		}

		if ( $f ){
			if ( !$f=@fopen($f,'rb') or fread($f,4) != 'MRPG'){
				@fclose($f);
				foreach($lst as $a){
					@unlink($a[1]);
				}
				return false;
			}
			fseek($f,8,SEEK_SET);
			$fa = fread($f,232);
			//echo "\r\n\r\n".bin2hex($fa)."\r\n\r\n";//exit;
			//$fa = hex2bin(bin2hex($fa));
			fclose($f);
		}else{
			$fa = self::hex2bin('8cc70000f00000006e65772e6d727000000000006e65775f4d5250000000000000000000000000000000000030313233343536373839616263646566e8030000e80300000700000012270000c0068ecf4a69757761702e636e00000000000000000000000000000000000000000000000000000000000000546869732069732061206e6577204d52502066696c652c2043726561746564204279206a69757761702e636e204d5250205048504c49422e0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000');
		}

		$x=chr(0);
		$filelist = $dat = null;
		foreach($lst as $a){
			$wz=strrev(pack('H*',self::binadd(dechex($a[4]),8)));
			$cd=strrev(pack('H*',self::binadd(dechex($a[2]),8)));
			$fl=pack('H*',self::binadd(dechex($a[3]),2));
			$filelist.=$fl.$x.$x.$x.$a[0].$x.$wz.$cd.$x.$x.$x.$x;
			$dat.=$fl.$x.$x.$x.$a[0].$x.$cd.$a[1];
			@unlink($a[1]);
		}
		$start=strrev(pack('H*',self::binadd(dechex($lst[0][4]-$lst[0][3]-16),8)));
		file_put_contents($mrp,"MRPG".$start.$fa.$filelist.$dat);
		return $lst;
	}



	static private function gb2u0($f){
		$f = str_replace(chr(0),'',$f);
		return mb_convert_encoding($f,'utf-8',SYSFILECODE);
	}


	static private function u2gb0($f,$n){
		$f = mb_convert_encoding($f,SYSFILECODE,'utf-8');
		for($i = strlen($f); $i<=$n; $i++){
			$f .= chr(0);
		}
		$f = substr($f,0,$n);
		return $f;
	}

	static private function binadd($f,$n){
		$i = strlen($f);
		while ( $i<$n ) {
			$i++;
			$f = '0'.$f;
		}
		$f = substr($f,0,$n);
		return $f;
	}

	static function mkdirs($path, $mode = 0777){
		$path .= '/';
		$dirs = str_replace('\\','/',$path);
		$dirs = str_replace('\\','/',$path);
		$dirs = str_replace('//','/',$path);
		$dirs = str_replace('//','/',$path);
		$dirs = explode('/',$path);
		$dirs_count = count($dirs);
		if ( strrpos($path, '.') === true ) {
			$dirs_count -= 1;
		}
		for ($c=0;$c < $dirs_count; $c++) {
			$thispath = '';
			for ($cc=0; $cc <= $c; $cc++) {
				$thispath .= $dirs[$cc].'/';
			}
			if (!file_exists($thispath)) {
				@mkdir($thispath,$mode);
			}
		}
	}

	static function hex2bin($h){
		if (!is_string($h)) return null;
		$r='';
		for ($a=0; $a<strlen($h); $a+=2) {
			$r.=chr(hexdec($h{$a}.$h{($a+1)}));
		}
		return $r;
	}

	static function gzdecode($data) {
		$len = strlen($data);
		if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) {
			return null;
		}
		$method = ord(substr($data,2,1));
		$flags  = ord(substr($data,3,1));
		if ($flags & 31 != $flags) {
			return null;
		}
		$mtime = unpack("V", substr($data,4,4));
		$mtime = $mtime[1];
		$headerlen = 10;
		$extralen  = 0;
		if ($flags & 4) {
			if ($len - $headerlen - 2 < 8) {
				return false;
			}
			$extralen = unpack("v",substr($data,8,2));
			$extralen = $extralen[1];
			if ($len - $headerlen - 2 - $extralen < 8) {
				return false;
			}
			$headerlen += 2 + $extralen;
		}

		$filenamelen = 0;
		if ($flags & 8) {
			if ($len - $headerlen - 1 < 8) {
				return false;
			}
			$filenamelen = strpos(substr($data,8+$extralen),chr(0));
			if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
				return false;
			}
			$headerlen += $filenamelen + 1;
		}

		$commentlen = 0;
		if ($flags & 16) {
			if ($len - $headerlen - 1 < 8) {
				return false;
			}
			$commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0));
			if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
				return false;
			}
			$headerlen += $commentlen + 1;
		}

		$headercrc = '';
		if ($flags & 1) {
			if ($len - $headerlen - 2 < 8) {
				return false;
			}
			$calccrc = crc32(substr($data,0,$headerlen)) & 0xffff;
			$headercrc = unpack("v", substr($data,$headerlen,2));
			$headercrc = $headercrc[1];
			if ($headercrc != $calccrc) {
				return false;
			}
			$headerlen += 2;
		}

		$datacrc = unpack("V",substr($data,-8,4));
		$datacrc = $datacrc[1];
		$isize = unpack("V",substr($data,-4));
		$isize = $isize[1];

		$bodylen = $len-$headerlen-8;
		if ($bodylen < 1) {
			return null;
		}
		$body = substr($data,$headerlen,$bodylen);
		$data = '';
		if ($bodylen > 0) {
			if  ($method == 8) {
				$data = @gzinflate($body);
			}else{
				return false;
			}
		}

		if ($isize != strlen($data) || crc32($data) != $datacrc) {
			return false;
		}
		return $data;
	}
}




/*

//下面是测试
$list = array('cu.mrp');
mrp::pack('xx.mrp',$list,3);


mrp::unpack("cu.mrp",'xxxxxx');


$v=	array(
		'id'=>'1000&0',
		'ch'=>'0123456789abcdef',
		'bb'=>'1000&0',
		'nn'=>'newsf.mrp',
		'xn'=>'nesfsw_MRP',
		'zz'=>'Jiuwap.cn',
		'js'=>'This is a new MRP file, Created By jiuwap.cn MRP PHPLIB.');

mrp::put("qqqp.mrp",$v);

var_dump(mrp::get("qqqp.mrp"));
*/