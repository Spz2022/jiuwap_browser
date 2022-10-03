<?
/*
 *
 *	HTTP通讯类(fsockopen||curl||file_get_contents||get_headers)
 *
 *	2011/8/1 @ jiuwap.cn [原创]转载或使用请保留注释,谢谢
 *
 */

if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}

//curl(不支持检测文件大小)有问题,没开发完..
define('HTTPLIB_TYPE_CURL',0);

//fsockopen(推荐,但是网络处理不健全)
define('HTTPLIB_TYPE_FSOCKOPEN',1);

//file_get_contents(不支持上传,不支持先检测文件大小,但是速度快)
define('HTTPLIB_TYPE_FILE_GET_CONTENT',2);


Class httplib{
	//当前使用的网络
	private $userType	= HTTPLIB_TYPE_FSOCKOPEN;

	private $jump		= 5;
	private $outtime	= 30;

	private $errormsg	= null;

	private $proxy_host	= null;
	private $proxy_port	= null;

	private $set_referer= null;

	//设置的东西
	private $set_header	= array();
	private $set_cookie	= array();
	private $set_post	= array();
	private $set_file	= array();

	private $get_header	= array();
	private $get__body	= null;

	private $fp	= false;


	private $tmp_cookie = array();

	function __destruct(){
		if ( $this->userType == HTTPLIB_TYPE_FSOCKOPEN && $this->fp ){
			@fclose($this->fp);
		}
	}

	//设置使用的网络方式
	public function type($type=false){
		if ( $type === false ){
			return $this->userType;
		}
		$this->userType = $type;
	}

	public function private_header(){
		return $this->set_header;
	}

	public function private_cookie(){
		return $this->set_cookie;
	}

	public function private_post(){
		return $this->set_post;
	}

	//上传文件
	public function file($name,$value,$content,$type='application/octet-stream'){
		$this->set_file[] = array(
            'form'=>$name,
            'name'=>$value,
            'type'=>$type,
            'content'=>$content,
        );
	}

	//设置代理
	public function proxy($host,$part=80){
		$this->proxy_host = self::gethostIp($host);
		$this->proxy_port = $part;
	}

	//设置referer
	public function referer($set_referer=false){
		if ( $set_referer === false){
			return $this->set_referer;
		}else{
			$this->set_referer = $set_referer;
		}
	}

	//设置HEADER
	public function header($name=false,$value=false){
		if ( $name === false ){
			return $this->get_header;
		}elseif ( $value === false ) {
			if ( isset($this->get_header[$name] ) ){
				return $this->get_header[$name];
			}else{
				return null;
			}
		}else{
			$this->set_header[$name] = $value;
		}
	}

	//设置、获取cookie
	public function cookie($name=false,$value=false){
		if ( $name === false ){
			return $this->get_header['COOKIE'];
		}elseif ( $value === false ) {
			if ( isset($this->get_header['COOKIE'][$name] ) ){
				return $this->get_header['COOKIE'][$name];
			}else{
				return null;
			}
		}else{
			$this->set_cookie[$name] = $value;
		}
	}

	//设置post
	public function post($name,$value){
		$this->set_post[$name] = $value;
	}

	//返回URL
	public function url(){
		return $this->_url;
	}

	//返回当前URL数组
	public function parse_url(){
		return array('ip' => $this->_ip,'scheme' => $this->_scheme,'host' => $this->_host,'port' => $this->_port,'path' => $this->_path,'query' => $this->_query);
	}

	public function error(){
		return $this->errormsg;
	}

	//建立连接
	public function open($url,$outtime=30,$jump=5,$returnerr=false){
		$this->jump = $jump;
		$this->outtime = $outtime;
		$this->returnerr = $returnerr;
		return $this->_parseURL($url);
	}

	//发送
	public function send($onlysend=false){
		$head = array();
		if ( $this->set_cookie ){
			$this->set_header['Cookie'] = '';
			foreach ($this->set_cookie as $key => $value) {
				$this->set_header['Cookie'] .= "{$key}={$value}; ";
			}
		}
		if ( $this->set_referer ){
			$this->set_header['Referer'] = $this->set_referer;
		}
		$this->set_header['Accept-Language'] = 'zh-CN,zh;q=0.9,en;q=0.8';

		//记录一下：cURL不支持Accept-Charset，原因待调查
		//$this->set_header['Accept-Charset'] = 'utf-8, gb2312, *;q=0.1';

		$this->set_header['Accept'] = 'text/html, application/xml;q=0.9, application/xhtml+xml, application/vnd.wap.xhtml+xml, application/vnd.wap.wmlc;q=0.9, application/vnd.wap.wmlscriptc;q=0.7, text/vnd.wap.wml;q=0.7, */*;q=0.1';
		//$this->set_header['Connection'] = 'keep-alive';
		//$this->set_header['Keep-Alive'] = '300';
		//$this->set_header['Pragma'] = 'no-cache';
		//$this->set_header['Cache-Control'] = 'no-cache';
		//$this->set_header['Authorization'] = 'BASIC '.base64_encode(用户名:密码);
		//$this->set_header['Accept-Encoding'] = 'gzip, deflate, compress;q=0.9';
		foreach($this->set_header as $name => $value){
			$head[] = "{$name}: {$value}";
		}

		if ( $this->userType == HTTPLIB_TYPE_FILE_GET_CONTENT ){
			$context = array();
			$context['http']['timeout'] = $this->outtime;
			$context['http']['header'] = '';
			foreach($head as $value){
				$context['http']['header'] .= "{$value}\r\n";
			}

			if ( $this->proxy_host && $this->proxy_port){
				$context['http']['proxy'] = 'tcp://'.$this->proxy_host.':'.$this->proxy_port;
			}

			if ( $this->set_post ){
				$QueryStr = http_build_query($this->set_post);
				$context['http']['method'] = 'POST';
				$context['http']['content'] = $QueryStr;
				$context['http']['header'] .= "Content-Type: application/x-www-form-urlencoded\r\n";
			}

			$context = @stream_context_create($context);
			$body = @file_get_contents($this->_url, false,$context );
			if ( $body === false  ){
				if ( $this->returnerr ){
					$this->errormsg = http_error($this->_url, 0,'获取失败',true);
					return false;
				}else{
					http_error($this->_url, 0,'获取失败');
				}
			}
			if ($onlysend){
				return true;
			}
			$head = $http_response_header;
			$this->get__body = $body;
			$this->get_header = $this->_parseHEADER($head);
			$this->_checkJUMP();

		}elseif ( $this->userType == HTTPLIB_TYPE_CURL ){
			if ( !extension_loaded('curl') ){
				if ( $this->returnerr ){
					$this->errormsg = http_error($this->_url, 0,'服务器不支持curl',true);
					return false;
				}else{
					http_error($this->_url, $eNum,'服务器不支持curl');
				}
			}

			$this->fp = curl_init();
			curl_setopt($this->fp, CURLOPT_CRLF,true);
			curl_setopt($this->fp, CURLOPT_URL,$this->_scheme.'://'.$this->_host.$this->_path.$this->_query);
			if ( $this->_port <> '80' ){
				curl_setopt($this->fp, CURLOPT_PORT, $this->_port);
			}
			curl_setopt($this->fp, CURLOPT_HEADER,true);
			curl_setopt($this->fp, CURLOPT_RETURNTRANSFER,true);

			if ( $this->proxy_host && $this->proxy_port){
				curl_setopt($this->fp,CURLOPT_HTTPPROXYTUNNEL,true);
				curl_setopt($this->fp,CURLOPT_PROXY,$this->proxy_host.':'.$this->proxy_port);
			}

			curl_setopt($this->fp, CURLOPT_FOLLOWLOCATION,true);
			curl_setopt($this->fp, CURLOPT_MAXREDIRS,$this->jump);
			curl_setopt($this->fp, CURLOPT_AUTOREFERER,true);
			curl_setopt($this->fp, CURLOPT_CONNECTTIMEOUT,$this->outtime);
			curl_setopt($this->fp, CURLOPT_TIMEOUT, $this->outtime);
			curl_setopt($this->fp, CURLOPT_DNS_USE_GLOBAL_CACHE,true);
			curl_setopt($this->fp, CURLOPT_DNS_CACHE_TIMEOUT,'600');
			curl_setopt($this->fp, CURLOPT_FRESH_CONNECT, true);

			//CURL_HTTP_VERSION_1_0 //CURL_HTTP_VERSION_1_1 //CURL_HTTP_VERSION_NONE
			curl_setopt($this->fp, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_NONE);

			curl_setopt($this->fp, CURLOPT_NOBODY, false);
			if ( $this->set_post ) {
				$QueryStr = http_build_query($this->set_post);
				curl_setopt($this->fp,CURLOPT_POST,true);
				curl_setopt($this->fp,CURLOPT_POSTFIELDS,$QueryStr);
			}
			if ( $head ){
				curl_setopt($this->fp, CURLOPT_HTTPHEADER,$head );
			}

			//获取HEAD
			//curl_setopt($this->fp, CURLOPT_HEADERFUNCTION,array(&$this,'_curl_handler_recv_head'));
			$data = curl_exec($this->fp);
			if ( $eNum = curl_errno($this->fp) ) {
				if ( $this->returnerr ){
					$this->errormsg = http_error($this->_url, $eNum,curl_error($this->fp),true);
					curl_close($this->fp);
					return false;
				}else{
					http_error($this->_url, $eNum,curl_error($this->fp));
					curl_close($this->fp);
				}
			}

			$this->get_header = $this->_parseHEADER($data);
			$this->_checkJUMP();

		}elseif ( $this->userType == HTTPLIB_TYPE_FSOCKOPEN ){
			if ( $this->proxy_host && $this->proxy_port){
				$path = $this->_scheme.'://'.$this->_host.$this->_path.$this->_query;
				$ip = $this->proxy_host;
				$port = $this->proxy_port;
			}else{
				$path = $this->_path.$this->_query;
				$ip = $this->_ip;
				$port = $this->_port;
			}
			if (!$this->fp = @fsockopen($ip, $port, $errno, $errstr, $this->outtime)) {
				$errstr = trim(self::str2utf8($errstr));
				if ( $this->returnerr ){
					@fclose($this->fp);
					$this->errormsg = http_error($this->_url,$errno,$errstr,true);
					return false;
				}else{
					@fclose($this->fp);
					http_error($this->_url,$errno,$errstr);
				}
			}
			socket_set_timeout($this->fp, $this->outtime);
			//stream_set_blocking($this->fp, 1);

			$method = $this->set_post ? 'POST' : 'GET';
			$SendStr  = "{$method} {$path} HTTP/1.1\r\n";
			if ( $this->_port==80 ){
				$SendStr .= "Host: {$this->_host}\r\n";
			}else{
				$SendStr .= "Host: {$this->_host}:{$this->_port}\r\n";
			}
			foreach($head as $value){
				$SendStr.= "{$value}\r\n";
			}
			if ( $this->set_file ) {
				srand((double)microtime_()*1000000);
				$boundary = '---------------------------'.substr(md5(rand(0,32000)),0,10);
				$QueryStr = '';
				foreach ($this->set_post as $param => $value) {
						$QueryStr .= "--$boundary\r\n";
						$QueryStr .= "Content-Disposition: form-data; name=\"$param\"\r\n";
						$QueryStr .= "\r\n{$value}\r\n";
						$QueryStr .= "--$boundary\r\n";
				}
				foreach ( $this->set_file as $val){
					$QueryStr .= "--$boundary\r\n";
					$QueryStr .= "Content-Disposition: form-data; name=\"{$val['form']}\"; filename=\"{$val['name']}\"\r\n";
					$QueryStr .= "Content-Type: {$val['type']}\r\n";
					$QueryStr .= "\r\n{$val['content']}\r\n";
					$QueryStr .= "--$boundary--\r\n";
				}
				unset($this->set_file);
				$length   = strlen($QueryStr);
				$SendStr .= "Content-Type: multipart/form-data; boundary=$boundary\r\n";
				$SendStr .= "Content-Length: {$length}\r\n";

			}elseif ( $this->set_post ) {
				$QueryStr = http_build_query($this->set_post);
				$length   = strlen($QueryStr);
				$SendStr .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$SendStr .= "Content-Length: {$length}\r\n";

			}else{
				$QueryStr = null;
			}
			if ( !@fputs($this->fp,$SendStr."\r\n".$QueryStr) ){
				if ( $this->returnerr ){
					@fclose($this->fp);
					$this->errormsg = http_error($this->_url,0,'发送数据失败',true);
					return false;
				}else{
					@fclose($this->fp);
					http_error($this->_url,0,'发送数据失败');
				}
			}
			if ($onlysend){
				return true;
			}

			$header = null;
			while ( ($line = @fgets($this->fp)) !== false) {
				$header .= $line;
				if ( substr($header,-4) == "\r\n\r\n" || substr($header,-2) == "\n\n" ){
					break;
				}
			}
			$this->get__body = '';
			$this->get_header = $this->_parseHEADER($header);
			//var_dump($this->get_header);;exit;
			$this->_checkJUMP();
		}
		return true;
	}

	function response(&$limit_size=0){
		if ( $this->userType == HTTPLIB_TYPE_FILE_GET_CONTENT ){
			$response = $this->get__body;
			if ( $limit_size && strlen($response) > $limit_size ){
				$limit_size = false;
			}

		}elseif  ( $this->userType == HTTPLIB_TYPE_CURL ){
			//curl_setopt($this->fp, CURLOPT_WRITEFUNCTION,array(&$this,'_curl_handler_recv'));
			curl_setopt($this->fp, CURLOPT_HEADER, false);
			curl_setopt($this->fp,CURLOPT_NOBODY, false);
			$response = curl_exec($this->fp);
			$eNum = curl_errno($this->fp);
			if ( $eNum ) {
				if ( $this->returnerr ){
					$this->errormsg = http_error($this->_url, $eNum,curl_error($this->fp),true);
					curl_close($this->fp);
					return false;
				}else{
					http_error($this->_url, $eNum,curl_error($this->fp));
					curl_close($this->fp);
				}
			}
			curl_close($this->fp);
			if ( $limit_size && strlen($response) > $limit_size ){
				$limit_size = false;
			}

		}elseif ( $this->userType == HTTPLIB_TYPE_FSOCKOPEN ){
			if ( !$this->fp ){
				return null;
			}
			$response = $this->get__body;
			unset($this->get__body);
			if ( isset($this->get_header['CONTENT-LENGTH']) ){
				$limit = $this->get_header['CONTENT-LENGTH'] - strlen($response);
				if ( $limit > 0 ){
					//!feof($this->fp
					while( true ) {
						if (!$_response = @fread($this->fp, (!$limit || $limit>8192 )? 8192 : $limit)){
							break;
						}
						$response .= $_response;
						$limit = $limit - strlen($_response);
						if ( $limit_size && strlen($response) > $limit_size ){
							$limit_size = false;
							break;
						}
						if ( $limit <= 0){
							break;
						}
					}
				}
				unset($_response,$limit);
			}elseif( isset($this->get_header["TRANSFER-ENCODING"]) && strtolower($this->get_header["TRANSFER-ENCODING"]) == 'chunked'){
				while ($chunk_length = hexdec(fgets($this->fp))){
					$responseContentChunk = '';
					$read_length = 0;
					while ($read_length < $chunk_length){
						$responseContentChunk .= fread($this->fp, $chunk_length - $read_length);
						$read_length = strlen($responseContentChunk);
						if ( $limit_size && $read_length > $limit_size ){
							$limit_size = false;
							break;
						}
					}
					$response.= $responseContentChunk;
					unset($responseContentChunk,$read_length,$chunk_length);
					fgets($this->fp);
				}
			}else{
				while( !feof($this->fp) ) {
					$response .= @fread($this->fp,4096);
					if ( $limit_size && strlen($response) > $limit_size ){
						$limit_size = false;
						break;
					}
				}
			}
			fclose($this->fp);
		}
		if ( isset($this->get_header['CONTENT-ENCODING']) && stripos($this->get_header['CONTENT-ENCODING'],'gzip' )!==false ){
			$response = self::gzdecode($response);
		}
		return $response;
    }

	private function _checkJUMP(){
		if ( isset($this->get_header['LOCATION']) && $this->jump > 0 ){
			foreach($this->get_header['COOKIE'] as $key=>$val){
				if ( !$val['domain'] ){
					if ( $val['expires'] && $val['expires'] <= time_()){
						unset($this->set_cookie[$key]);
					}else{
						$this->set_cookie[$key] = $val['value'];
					}
				}elseif ( strtolower($val['domain']) == strtolower(substr($this->_host,strlen($this->_host)-strlen($val['domain']))) ){
					if ( $val['expires'] && $val['expires'] <= time_() ){
						unset($this->set_cookie[$key]);
					}else{
						$this->set_cookie[$key] = $val['value'];
					}
				}
			}
			if ( $this->userType == HTTPLIB_TYPE_FSOCKOPEN ){
				@fclose($this->fp);
			}
			$url = $this->_fixURL($this->get_header['LOCATION']);
			$this->jump--;
			$this->_parseURL($url);
			$this->set_referer = $url;
			$this->set_post = array();
			$this->send();
		}
	}

	private function _fixURL($url){
		if(in_array(strtolower(substr($url,0,7)),array('http://','https:/'))){
			return $url;
		}elseif ( substr($url,0,1) == '?' ){
			$url = $this->_scheme.'://'.$this->_host.$this->_path.$url;
		}elseif ( substr($url,0,1) == '/'){
			$url = $this->_scheme.'://'.$this->_host.$url;
		}else{
			if ( $this->_path <> '/' ){
				$url = $this->_scheme.'://'.$this->_host.substr($this->_path,0,strrpos(substr($this->_path,0,strrpos($this->_path.'?','?')),'/')+1).$url;
			}else{
				$url = $this->_scheme.'://'.$this->_host.'/'.$url;
			}
		}
		return $url;
	}
	private function _parseHEADER($array){
 		$header['COOKIE'] = $this->tmp_cookie;
		$header['STATUS'] = '0' ;
		if ( !is_array($array) ){
			$array = str_replace("\r","",$array);
			$array = explode("\n", $array) ;
		}
		foreach ($array as $value) {
			$pos = strpos($value, ':') ;
			if ($pos) {
				$key = trim(strtoupper(substr($value, 0, $pos))) ;
				$value = trim(substr($value, $pos + 1)) ;
				if ($key == 'SET-COOKIE') {
					$temp = $this->_parseCOOKIE($value) ;
					$key = $temp['name'];unset($temp['name']);
					$header['COOKIE'][$key] = $temp;
				}else{
					$header[$key] = $value ;
				}
			}elseif (preg_match("'HTTP/(.*?) (.*?) '", $value, $matches)) {
				$header['STATUS'] = trim($matches[2]) ;
			}
		}
		$this->tmp_cookie = $header['COOKIE'];
		return $header;
	}

	private function _parseCOOKIE($array){
		$name = $value = $path = $expires = $domain = '';
		$array = explode(';', $array) ;
		if ( !$array ) {
			$a = strpos($array,'=');
			$name = trim(substr($array,0,$a));
			$value =trim(substr($array,$a+1));
		} else {
			$a = strpos($array[0],'=');
			$name = trim(substr($array[0],0,$a));
			$value = trim(substr($array[0],$a+1));
			unset($array[0]);
			foreach($array as $temp){
				$a = strpos($temp,'=');
				if ( $a ){
					$nam = trim(substr($temp,0,$a));
					$valu = trim(substr($temp,$a+1));
					if ( $nam == 'expires'){
						$expires = self::strtotime_($valu);
					}elseif ( $nam == 'path'){
						$path = $valu;
					}elseif ( $nam == 'domain'){
						$domain = $valu;
					}else{
						//null
					}
				}
			}
		}
		return array('value'=>$value,'path'=>$path,'expires'=>$expires,'name'=>$name,'domain'=>$domain);
	}

	function _parseURL($url){
		$this->_url = $url;
		if ( !$aUrl = @parse_url($url) ){
            if ( $this->returnerr ){
                http_error($this->_url,0,'URL地址解析失败',true);
                return false;
            }else{
                http_error($this->_url,0,'URL地址解析失败');
            }
		}
		$aUrl['query'] = isset($aUrl['query']) ? $aUrl['query'] : null;
		$this->_scheme = (!isset($aUrl['scheme']) || $aUrl['scheme'] == 'http'  ) ? 'http' : 'https';
		$this->_host  = isset($aUrl['host']) ? $aUrl['host'] : null;
		$this->_port  = empty($aUrl['port']) ? 80 : (int)$aUrl['port'];
		$this->_path  = empty($aUrl['path']) ? '/' : $aUrl['path'];
		$this->_query = strlen($aUrl['query']) > 0 ? '?'.$aUrl['query'] : null;
		$this->_ip  = self::gethostIp($this->_host);
 		return true;
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


	static function str2utf8($string,&$code=''){
		$code = mb_detect_encoding($string, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
		return @mb_convert_encoding($string, 'utf-8', $code);
	}

	static function gethostIp($host,$default=false){
		if ( $host ){
			if ( $host1 = @gethostbyname($host)){
				$host = $host1;
			}elseif ($default!==false) {
				$host = $default;
			}
			if ( $default!==false && !is_ip($host) ){
				$host = $default;
			}
		}
		return $host ;
	}

	static function strtotime_($str,$date=false){
		if ( $str === false && $date !== false ){
			if ( !is_numeric($date) ) {
				return $date;
			}elseif( $date > 2030 ){
				return 2030;
			}elseif( $date < 1969 ){
				return 1969;
			}else{
				return $date;
			}
		}
		$time = strtotime_($str);
		if ( empty($time) ){
			$str = preg_replace('@([1-2][0-9][0-9][0-9])@ies', "self::strtotime_(false,'\\1')", $str);
			$time = strtotime_($str);
			if ( empty($time) ){
				$str = preg_replace('@([1-3][0-9]\-[a-zA-Z][a-zA-Z][a-zA-Z]\-)([1-9][0-9])@ies', "'\\1' . self::strtotime_(false,'20\\2')", $str);
				$time = strtotime_($str);
			}
		}
		return $time;
	}

}

function http_ishtml($contenttype){
	return ( stripos($contenttype,'text') || stripos($contenttype,'xhtml') || stripos($contenttype,'html') || stripos($contenttype,'wml') || stripos($contenttype,'plain') || stripos($contenttype,'xml') );
}

function http_fixhtml($content){
	$content = trim($content);
	if ( ($m = strpos($content,'<'))!==false ){
		$content = substr($content,$m);
	}
	if ( ($m = strrpos($content,'>'))!==false ){
		$content = substr($content,0,$m+1);
	}
	return $content;
}


if (!function_exists('time_')){
	function time_(){
		return time();
	}

	function microtime_(){
		return microtime();
	}

	function strtotime_($time,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return strtotime($time,$timestamp);
	}

	function date_($format,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return date($format,$timestamp);
	}

	function getdate_($timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return getdate($timestamp);
	}

	function gmdate_($format,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return gmdate($format,$timestamp);
	}

	function strftime_($format,$timestamp=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return strftime($format,$timestamp);
	}

	function localtime_($timestamp=false,$is_associative=false){
		if ( $timestamp == false ){
			$timestamp = time_();
		}
		return localtime($timestamp,$is_associative);
	}
}

if (!function_exists('http_error')){
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
		header('Content-Type: text/html; charset=utf-8');
		$con = '提示：'.$error_str.'('.$num.')<br/>';
		$con .= '网址：'.htmlspecialchars($url).'<br/>';
		$con .= '详细：'.$str.'<br/>';
		echo '访问失败<br/>'.$con ;
		exit;
	}
}