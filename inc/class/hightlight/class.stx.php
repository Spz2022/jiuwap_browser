<?php
/*
 *
 *	代码高亮类
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

class STX{
	private $stx;
	private $lang;
	private $len;
	private $status;
	private $stx_path = '';
	private $lang_arr = array();
	var $ext_arr = array(
				'cpp'=>'cpp',
				'cs'=>'cs',
				'css'=>'css',
				'htm'=>'html',
				'xsl'=>'xml',
				'class'=>'java',
				'asp'=>'asp',
				'js'=>'js',
				'jsp'=>'jsp',
				'pm'=>'perl',
				'php'=>'php',
				'vbs'=>'vb',
				'sql'=>'sql',
				'py'=>'py'
			);
	var $color = array(/*
				'delimiter'=>'#0000CC',
				'reserved'=>'#0000FF',
				'functions'=>'#FF0000',
				'variables1'=>'#008080',
				'variables2'=>'#808000',
				'variables3'=>'#800000',
				'comment'=>'#FF9900',
				'quotation'=>'#FF00FF',
				'words'=>'#000000',*/
 				'delimiter'=>'q',
				'reserved'=>'w',
				'functions'=>'e',
				'variables1'=>'r',
				'variables2'=>'t',
				'variables3'=>'y',
				'comment'=>'u',
				'quotation'=>'i',
				'words'=>'o',
   		);

	function STX($lang=''){
		if ($lang != '') $this->lang = strtolower($lang);
		foreach ($this->ext_arr as $val) {
			if(in_array($val,$this->lang_arr) == false) {
				$this->lang_arr[] = $val;
			}
		}
		$this->set_stx();
	}

	function set_stx(){

		if (in_array($this->lang, array('php','jsp'))) {
			$this->lang_arr = array($this->lang,'html');
		} elseif ($this->lang != '') {
			$this->lang_arr = array($this->lang);
		}

		foreach ($this->lang_arr as $lang) {
			$root_path = preg_replace("/^(.*\/)(.*)$/","\\1",__FILE__);
			$stx_file = $root_path.'/../'.$lang.'.stx';
			if (file_exists($stx_file) == false) continue;
			$line_arr = file($stx_file);
			foreach ($line_arr as $val){
				$val = trim($val);
				if (strlen($val) == 0) continue;
				if (in_array($val{0}, array(';', ''))) continue;
				if (preg_match("/^#([\w]+)=(.*)$/i", $val, $matches)) {
					if (empty($matches)) continue;
					if ($matches[1] == 'DELIMITER') {
						$len = strlen($matches[2]);
						for ($i = 0; $i < $len; $i++) $this->stx[$lang]['DELIMITER'][] = $matches[2]{$i};
					} elseif ($matches[1] == 'KEYWORD') {
						if (isset($flag)) $flag = 2;
						else $flag = 1;
					}else{
						$this->stx[$lang][$matches[1]] = $matches[2];
					}
				}else{
					if ($flag == 1) $this->stx[$lang]['RESERVED'][] = strtolower($val);
					else $this->stx[$lang]['FUNCTIONS'][] = strtolower($val);
				}
			}

			if (empty($this->stx[$lang]['SCRIPT_BEGIN'])) $this->stx[$lang]['SCRIPT_BEGIN'] = '';
			if (empty($this->stx[$lang]['SCRIPT_END'])) $this->stx[$lang]['SCRIPT_END'] = '';
			if (empty($this->stx[$lang]['DELIMITER'])) $this->stx[$lang]['DELIMITER'] = '';
			if (empty($this->stx[$lang]['COMMENTON'])) $this->stx[$lang]['COMMENTON'] = '';
			if (empty($this->stx[$lang]['COMMENTOFF'])) $this->stx[$lang]['COMMENTOFF'] = '';
			if (empty($this->stx[$lang]['LINECOMMENT'])) $this->stx[$lang]['LINECOMMENT'] = '';
			if (empty($this->stx[$lang]['LINECOMMENT2'])) $this->stx[$lang]['LINECOMMENT2'] = '';
			if (empty($this->stx[$lang]['QUOTATION1'])) $this->stx[$lang]['QUOTATION1'] = "'";
			if (empty($this->stx[$lang]['QUOTATION2'])) $this->stx[$lang]['QUOTATION2'] = '"';
			if (empty($this->stx[$lang]['ESCAPE'])) $this->stx[$lang]['ESCAPE'] = '\\';
			if (empty($this->stx[$lang]['PREFIX3'])) $this->stx[$lang]['PREFIX3'] = '';
			if (empty($this->stx[$lang]['PREFIX4'])) $this->stx[$lang]['PREFIX4'] = '';
			if (empty($this->stx[$lang]['PREFIX5'])) $this->stx[$lang]['PREFIX5'] = '';
			if (empty($this->stx[$lang]['RESERVED'])) $this->stx[$lang]['RESERVED'] = '';
			if (empty($this->stx[$lang]['FUNCTIONS'])) $this->stx[$lang]['FUNCTIONS'] = '';
			unset($flag);
		}
		return true;
	}

	function get_flag(&$str,&$i,$word,$flag){
		$lang = $this->lang;

		$char = $str{$i};
		if ($i > 1) $char0 = $str{$i-2};
		else $char0 = '';
		if ($i > 0) $char1 = $str{$i-1};
		else $char1 = '';
		if ($i < $this->len - 1) $char2 = $str{$i+1};
		else $char2 = '';
		if ($i < $this->len - 2) $char3 = $str{$i+2};
		else $char3 = '';
		if ($i < $this->len - 3) $char4 = $str{$i+3};
		else $char4 = '';
		if ($lang == 'perl') $this->stx[$lang]['LINECOMMENT2'] = $this->stx[$lang]['LINECOMMENT'];
		if ($this->status == 'SCRIPT' || $char.$char2 == $this->stx[$lang]['SCRIPT_BEGIN']){
			if ($this->status == 'SCRIPT' && $char1.$char == $this->stx[$lang]['SCRIPT_END']) {
				$this->status = 'SCRIPT_END';
			}else{
				$this->status = 'SCRIPT';
			}
		}
		if ($this->status == 'SCRIPT_END' && ($flag == 'HTML_COMMENT' || $char.$char2.$char3.$char4 == '<!--')) {
			if ($flag == 'HTML_COMMENT' && $char0.$char1.$char == '-->') {
				$flag = 'HTML_COMMENT_END';
			} elseif (in_array($flag, array('QUOTATION1','QUOTATION2')) == false) {
				$flag = 'HTML_COMMENT';
			}
		}elseif ($this->status == 'SCRIPT' && ($flag == 'MULTI_COMMENT' || $char.$char2 == $this->stx[$lang]['COMMENTON'])){
			if ($flag == 'MULTI_COMMENT' && $char1.$char == $this->stx[$lang]['COMMENTOFF']) {
				$flag = 'MULTI_COMMENT_END';
			} elseif (in_array($flag, array('LINE_COMMENT','LINE_COMMENT2','QUOTATION1','QUOTATION2')) == false) {
				$flag = 'MULTI_COMMENT';
			}
		}elseif ($this->status == 'SCRIPT' && ($flag == 'LINE_COMMENT' || $char.$char2 == $this->stx[$lang]['LINECOMMENT'])){
			if ($flag == 'LINE_COMMENT' && $char2 == "\n"){
				$flag = 'LINE_COMMENT_END';
			} elseif (in_array($flag, array('MULTI_COMMENT','LINE_COMMENT2','QUOTATION1','QUOTATION2')) == false) {
				$flag = 'LINE_COMMENT';
			}
		}elseif ($this->status == 'SCRIPT' && ($flag == 'LINE_COMMENT2' || $char == $this->stx[$lang]['LINECOMMENT2'])){
			if ($flag == 'LINE_COMMENT2' && $char2 == "\n"){
				$flag = 'LINE_COMMENT2_END';
			} elseif (in_array($flag, array('MULTI_COMMENT','LINE_COMMENT','QUOTATION1','QUOTATION2')) == false) {
				$flag = 'LINE_COMMENT2';
			}
		}elseif ($flag == 'QUOTATION1' || $char == $this->stx[$lang]['QUOTATION1']){
			$word .= $char;
			if ($flag == 'QUOTATION1' && $char == $this->stx[$lang]['QUOTATION1']) {
				if (preg_match("/([\\".$this->stx[$lang]['ESCAPE']."]+)".$this->stx[$lang]['QUOTATION1']."$/",$word,$matches)) {
					if (strlen($matches[0])%2) {
						$flag = 'QUOTATION1_END';
						$word = '';
					}
				}else{
					$flag = 'QUOTATION1_END';
					$word = '';
				}
			} elseif (in_array($flag, array('MULTI_COMMENT','LINE_COMMENT','LINE_COMMENT2','QUOTATION2')) == false) {
				$flag = 'QUOTATION1';
			}
		}elseif ($flag == 'QUOTATION2' || $char == $this->stx[$lang]['QUOTATION2']){
			$word .= $char;
			if ($flag == 'QUOTATION2' && $char == $this->stx[$lang]['QUOTATION2']) {
				if (preg_match("/([\\".$this->stx[$lang]['ESCAPE']."]+)".$this->stx[$lang]['QUOTATION2']."$/",$word,$matches)) {
					if (strlen($matches[0])%2) {
						$flag = 'QUOTATION2_END';
						$word = '';
					}
				}else{
					$flag = 'QUOTATION2_END';
					$word = '';
				}
			} elseif (in_array($flag, array('MULTI_COMMENT','LINE_COMMENT','LINE_COMMENT2','QUOTATION1')) == false) {
				$flag = 'QUOTATION2';
			}
		}elseif (in_array($char, array("\r","\n","\t"," "))){
			$flag = 'SPACE';
		}elseif (in_array($char, $this->stx[$lang]['DELIMITER'])){
			if (in_array($flag, array('WORD','VARIABLES1','VARIABLES2','VARIABLES3')) == false) $flag = 'DELIMITER';
		}elseif ($this->status == 'SCRIPT' && $char == $this->stx[$lang]['PREFIX3']){
			$flag = 'PREFIX3';
		}elseif ($this->status == 'SCRIPT' && $char == $this->stx[$lang]['PREFIX4']){
			$flag = 'PREFIX4';
		}elseif ($this->status == 'SCRIPT' && $char == $this->stx[$lang]['PREFIX5']){
			$flag = 'PREFIX5';
		}elseif (preg_match("/!|[\w]|[\x80-\xff]/", $char)){
			$word .= $char;
			if ($flag == 'PREFIX3' || $flag == 'VARIABLES1') $flag = 'VARIABLES1';
			elseif ($flag == 'PREFIX4' || $flag == 'VARIABLES2') $flag = 'VARIABLES2';
			elseif ($flag == 'PREFIX5' || $flag == 'VARIABLES3') $flag = 'VARIABLES3';
			else $flag = 'WORD';
			if (preg_match("/!|[\w]|[\x80-\xff]/", $char2) == false) {

				if ($flag == 'VARIABLES1') {
					$flag = 'VARIABLES1_END';
				} elseif ($flag == 'VARIABLES2') {
					$flag = 'VARIABLES2_END';
				} elseif ($flag == 'VARIABLES3') {
					$flag = 'VARIABLES3_END';
				} elseif ($this->status == 'SCRIPT' && in_array(strtolower($word), $this->stx[$lang]['RESERVED'])) {
					$flag = 'RESERVED';
				} elseif ($this->status == 'SCRIPT' && in_array(strtolower($word), $this->stx[$lang]['FUNCTIONS'])) {
					$flag = 'FUNCTIONS';
				} elseif ($this->status == 'SCRIPT_END' && in_array(strtolower($word), $this->stx['html']['RESERVED'])) {
					$flag = 'HTML_TAG';
				} elseif ($this->status == 'SCRIPT_END' && in_array(strtolower($word), $this->stx['html']['FUNCTIONS'])) {
					$flag = 'HTML_ATTRIBUTES';
				}else{
					$flag = 'WORD_END';
				}
				$word = '';
			}
		}
		return $flag;
	}

	function stx_string($str){
		if (in_array($this->lang, $this->lang_arr) == false) $this->lang = 'php';
		$code_begin = '<div>';
		$words_begin = '<span id="'.$this->color['words'].'">';
		$delimiter_begin = '<span id="'.$this->color['delimiter'].'">';
		$reserved_begin = '<span id="'.$this->color['reserved'].'">';
		$functions_begin = '<span id="'.$this->color['functions'].'">';
		$quotation_begin = '<span id="'.$this->color['quotation'].'">';
		$variables1_begin = '<span id="'.$this->color['variables1'].'">';
		$variables2_begin = '<span id="'.$this->color['variables2'].'">';
		$variables3_begin = '<span id="'.$this->color['variables3'].'">';
		$comment_begin = '<span id="'.$this->color['comment'].'">';
		$span_end = '</span>';
		$code_end = '</div>';

		$this->len = strlen($str);
		$rs = $code_begin.$words_begin;
		$word = '';
		$flag = 'NORMAL';
		if (in_array($this->lang, array('php','jsp','html'))) {
			$this->status = 'SCRIPT_END';
		}else{
			$this->status = 'SCRIPT';
		}

		for ($i = 0; $i < $this->len; $i++) {
			$char = $str{$i};
			$flag = $this->get_flag($str, $i, $word, $flag);
			switch ($flag) {
				case 'MULTI_COMMENT':
				case 'LINE_COMMENT':
				case 'LINE_COMMENT2':
				case 'QUOTATION1':
				case 'QUOTATION2':
				case 'VARIABLES1':
				case 'VARIABLES2':
				case 'VARIABLES3':
				case 'WORD';
				case 'HTML_COMMENT';
					$word .= $char;
				break;
				case 'MULTI_COMMENT_END':
				case 'LINE_COMMENT_END':
				case 'LINE_COMMENT2_END':
				case 'HTML_COMMENT_END';
					$word .= $char;
					$rs .= $comment_begin.htmlspecialchars($word).$span_end;
					$word = '';
				break;
				case 'QUOTATION1_END':
				case 'QUOTATION2_END':
					$word .= $char;
					$rs .= $quotation_begin.htmlspecialchars($word).$span_end;
					$word = '';
				break;
				case 'DELIMITER':
					$rs .= $delimiter_begin.htmlspecialchars($char).$span_end;
					$word = '';
				break;
				case 'PREFIX3':
				case 'PREFIX4':
				case 'PREFIX5':
					$rs .= $reserved_begin.$char.$span_end;
					$word = '';
				break;
				case 'VARIABLES1_END':
					$word .= $char;
					$rs .= $variables1_begin.$word.$span_end;
					$word = '';
				break;
				case 'VARIABLES2_END':
					$word .= $char;
					$rs .= $variables2_begin.$word.$span_end;
					$word = '';
				break;
				case 'VARIABLES3_END':
					$word .= $char;
					$rs .= $variables3_begin.$word.$span_end;
					$word = '';
				break;
				case 'RESERVED':
				case 'HTML_TAG':
					$word .= $char;
					$rs .= $reserved_begin.$word.$span_end;
					$word = '';
				break;
				case 'FUNCTIONS':
				case 'HTML_ATTRIBUTES':
					$word .= $char;
					$rs .= $functions_begin.$word.$span_end;
					$word = '';
				break;
				case 'WORD_END':
					$word .= $char;
					$rs .= $word;
					$word = '';
				break;
				case 'SPACE':
				default:
					$rs .= $char;
					$word = '';
				break;
			}
		}
		$rs .= $span_end.$code_end;
		$rs = preg_replace("/\n\r|\r\n|\r/", "\n", $rs);
		$rs = preg_replace("/\t/","&nbsp;&nbsp;&nbsp;&nbsp;",$rs);
		$rs = preg_replace_callback("/\n[ ]+/",
					create_function(
						  '$matches',
						  'return str_replace(" ","&nbsp;",$matches[0]);'
					),
					$rs);
		$rs = '<style>#q{color:#0000CC}#w{color:#0000FF}#e{color:#FF0000}#r{color:#008080}#t{color:#808000}#y{color:#800000}#u{color:#FF9900}#i{color:#FF00FF}#o{color:#000000}</style>'.nl2br($rs);
		return $rs;
	}

	function stx_file($file){
		if (file_exists($file)) {
			if (empty($this->lang)) {
				$ext = strtolower(trim(array_pop(explode(".",$file))));
				if (empty($this->ext_arr[$ext]) == false) {
					$this->lang = strtolower($this->ext_arr[$ext]);
				}
			}
			$str = file_get_contents($file);
			$str = $this->stx_string($str);
			return $str;
		}else{
			return false;
		}
	}
}

function highlight_stx($file,$mime,$return = false){
	if ( is_file($file) ){
		$content = file_get_contents($file);
		$content = str_replace("\t",' ',$content);
		$content = getUTFString($content,$code);
	}else{
		$content = '文件丢失';
	}
	$ext_arr = array(
			'py'=>'py',

			'cpp'=>'cpp',
			'cxx'=>'cpp',
			'c'=>'cpp',
			'h'=>'cpp',
			'hpp'=>'cpp',
			'rc'=>'cpp',
			'cs'=>'cs',
			'css'=>'css',
			'vb'=>'vb',
			'vbs'=>'vb',
			'pl'=>'perl',
			'pm'=>'perl',
			'cgi'=>'perl',

			'asp'=>'asp',
			'asa'=>'asp',
			'aspx'=>'asp',
			'asax'=>'asp',
			'shtml'=>'html',
			'stm'=>'html',
			'htm'=>'html',
			'html'=>'html',
			'xhtml'=>'html',
			'hta'=>'html',

			'xml'=>'xml',
			'xsd'=>'xml',
			'xsl'=>'xml',
			'config'=>'xml',
			'manifest'=>'xml',
			'xaml'=>'xml',
			'csproj'=>'xml',
			'vbproj'=>'xml',
			'java'=>'java',
			'js'=>'js',
			'jsp'=>'jsp',
			'php'=>'php',
			'php3'=>'php',
			'sql'=>'sql',
		);
	if ( isset($ext_arr[$mime]) ){
		$stx = new STX($ext_arr[$mime]);
	}else{
		$stx = new STX();
	}
	if ( $return ){
		return $stx->stx_string($content);
	}
	echo $stx->stx_string($content);
}

function xhtmlHighlightString($str,$return=false) {
	$hlt = highlight_string($str, true);
	$fon = str_replace(array('<font ', '</font>'),array('<span ', '</span>'),$hlt);
	$ret = preg_replace('#color="(.*?)"#','style="color: $1"',$fon);
	if($return){
		return $ret;
	}else{
		echo $ret;
	}
	return true;
}