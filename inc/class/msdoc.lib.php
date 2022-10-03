<?php
/*
 *
 *	获取doc(word)文档内容[utf-8]
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

function doc_read($docfile){
	if(is_file($docfile)){
		$file_handler = fopen($docfile, 'r');

		//读取Root Directory位置
		fseek($file_handler, 0x30);
		$hexs = fread_little($file_handler, 4);
		$root_sector_id = hexdec($hexs);

		//定位到Root Entry
		$root_enrty = 0x200 * ($root_sector_id+1);

		//找出所有目录
		fseek($file_handler, $root_enrty);
		$dirs = dir_list($file_handler);

		//定位在1Table中的文字
		fseek($file_handler, 0x200*($dirs['WordDocument']['sector_id']+1)+0x01A2);
		$pos_in_1table = hexdec(fread_little($file_handler, 4));
		$len_in_1table = hexdec(fread_little($file_handler, 4));

		if ( !isset($dirs['1Table']['sector_id']) ){
			return '读取失败,暂时不支持此文档!#1';
		}
		//读取piece table
		fseek($file_handler, 0x200*($dirs['1Table']['sector_id']+1)+$pos_in_1table);
		$flag_piece = fread($file_handler, 1);
		$len_piece = hexdec(fread_little($file_handler, 4));
		while (hexdec(bin2hex($flag_piece))!=0x02){
			if (hexdec(bin2hex($flag_piece))==0xFF){
				break;
			}
			fseek($file_handler, $pos_piece, SEEK_CUR);
			$flag_piece = fread($file_handler, 1);
			$pos_piece = hexdec(fread_little($file_handler, 4));
		}
		if (hexdec(bin2hex($flag_piece))==0xFF){
			return '读取失败,暂时不支持此文档!#2';
		}

		$piece_num = ($len_piece - 4) / 12;
		$piece_arr = array();
		for($i=0;$i<$piece_num;$i++){
			$parr['pos'] = hexdec(fread_little($file_handler, 4));
			$parr['next_pos'] = hexdec(fread_little($file_handler, 4));
			$piece_arr[] = $parr;
			fseek($file_handler, -4, SEEK_CUR);
		}
		fread($file_handler, 4);
		$descr_arr = array();
		for($i=0;$i<$piece_num;$i++){
			fread($file_handler, 2);
			$descr_hex = hexdec(fread_little($file_handler, 4));
			fread($file_handler, 2);
			$darr['ucs_flag'] = ($descr_arr & 0x40000000==$descr_arr)?false:true;
			$descr_hex = $descr_hex & 0xBFFFFFFF;
			$darr['start_base'] = $darr['ucs_flag']?$descr_hex:$descr_hex/2;
			$descr_arr[] = $darr;
		}

		//获取文字
		$content = '';
		for ($pi=0;$pi<$piece_num;$pi++){
			$pos_pointer = 0x200*($dirs['WordDocument']['sector_id']+1)+$descr_arr[$pi]['start_base'];
			fseek($file_handler, $pos_pointer);
			for ($k=$piece_arr[$pi]['pos'];$k<$piece_arr[$pi]['next_pos'];$k++){
				$texts = fread_little($file_handler, $descr_arr[$pi]['ucs_flag']?2:1);
				$spec = check_spec_char($texts);
				if ($spec=='continue') continue;
				$pack = $spec == ''?mb_convert_encoding(pack("H4", $texts),"GBK","UCS-2"):$spec;
				if ( $pack == '' || $pack == '' || $pack == ''){
					$content .= '';
				}elseif ( $pack == chr(13)){
					$content .= '<br/>';
				}elseif ( $pack != ''){
					$content .= $pack;
				}else{
					$content .= ' ';
				}
			}
		}
		$content = str_ireplace(chr(10),'', $content);
		while( strpos($content,'<br/><br/>')){
		   $content = str_ireplace('<br/><br/>','<br/>', $content);
		}
		$content = str_ireplace('<br/>',"\r\n", $content);
		fclose($file_handler);
		$content = str_replace(' PAGE   \* MERGEFORM     ','',$content);
		$content = str_replace('HYPERLINK ','',$content);
		$content = trim(nl2br(htmlspecialchars($content)));
		@$content = iconv('gbk','utf-8//TRANSLIT', $content);
		return $content;
	}
}

//按照little endian倒序读取字节
function fread_little($fp, $len){
	$bytes = array();
	for ($i=0;$i<$len;$i++){
		$bytes[] = bin2hex(fread($fp, 1));
	}
	$bytes = array_reverse($bytes);
	return implode('', $bytes);
}

function dir_list($fp){
	$dirs = array();
	while (bin2hex(fread($fp, 2))>0){
		fseek($fp, -2, SEEK_CUR);
		$arr['name'] = '';
		for ($i=0;$i<32;$i++){
			$char = fread_little($fp, 2);
			if (hexdec($char)>0){
				$arr['name'] .= pack("C", hexdec($char));;
			}
		}
		$arr['name_len'] = hexdec(fread_little($fp, 2));
		fseek($fp, 50, SEEK_CUR);
		$arr['sector_id'] = hexdec(fread_little($fp, 4));
		$dirs[$arr['name']] = $arr;
		fseek($fp, 8, SEEK_CUR);
	}
	return $dirs;
}

function check_spec_char($hex){
	static $sss = true;
	if ( !$sss ){
		return '';
	}
	switch (hexdec($hex)){
		case 0x13:
			$sss = false;
			return '';
			break;
		case 0x14:
			$sss = true;
			return '';
			break;
		case 0x15:
			$sss = true;
			return '';
			break;
		case 0x01:
			if (!$sss){
				$sss = true;
				return '';
			}else{
				return '[图片]';
			}
			//插入图片
			break;
		case 0x08:
			$sss = true;
			//漂浮图片
			return '[图片]';
			break;
		default:
			return '';
	}
}

