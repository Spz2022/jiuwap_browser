<?php
/**
 *
 *	SMTP邮件发送
 *	原作者未知...
 *	tianyiw修正附件文件名编码问题。
 *
 */
class smtp_class{

	/* mail header setting */
	var $Priority			= 3;
	var $CharSet			= "iso-8859-1";
	var $ContentType		= "text/plain";
	var	$Encoding			= "8bit";
	var	$FromEmail			= "root@localhost";
	var	$FromName			= "root";
	var $Subject			= "";
	var $Body				= "";
	var $WordWrap			= "";
	var $MailerDebug		= false;
	var $UseMSMailHeaders	= true;
	var $IsHTML				= false;
	var $IPAddress			= "unknown";
	var $Timezone			= "+0000";


	/* SMTP setting */
	var $Host				= "localhost";
	var $Port				= 25;
	var $Timeout			= 10;
	var $Helo				= "";


	/* private variables */
	var $Version			= "";
	var $To					= array();
	var $Cc					= array();
	var $Bcc				= array();
	var $ReplyTo			= array();
	var $Attachment			= array();
	var $CustomHeader		= array();
	var $boundary			= "";
	var $ErrorAlerts		= array();
	var $AuthLogin			= false;
	var $AuthUser			= "";
	var $AuthPass			= "";
	var $CRLF				= "\r\n";
	var $error_status		= false;
	var $current_count		= 0;

	var $error		= 0;

	function From($address,$name){
		$this->FromEmail=trim($address);
		if($name==""){
			$this->FromName=$this->FromEmail;
		}else{
			$this->FromName=$name;
		}
	}


	function AddTo($address,$name){
		$current_count=count($this->To);
		$this->To[$current_count][0]=trim($address);
		$this->To[$current_count][1]=$name;
	}


	function AddCc($address,$name){
		$current_count=count($this->Cc);
		$this->Cc[$current_count][0]=trim($address);
		$this->Cc[$current_count][1]=$name;
	}


	function AddBcc($address,$name){
		$current_count=count($this->Bcc);
		$this->Bcc[$current_count][0]=trim($address);
		$this->Bcc[$current_count][1]=$name;
	}


	function AddReplyTo($address,$name){
		$current_count=count($this->ReplyTo);
		$this->ReplyTo[$current_count][0]=trim($address);
		$this->ReplyTo[$current_count][1]=$name;
	}


	function AddAttachment($path,$name,$type="application/octet-stream"){
		if($name==""){
			$name=basename($path);
		}
		//$name = iconv("UTF-8","gbk//TRANSLIT",$name);
		$current_count=count($this->Attachment);
		$this->Attachment[$current_count][0]=$path;
		$this->Attachment[$current_count][1]=$name;
		$this->Attachment[$current_count][2]=$type;
	}


	function SMTP_open($host,$port,$timeout){
		$this->smtp_connection=@fsockopen($host,$port,$errno,$errstr,$timeout);
		if( !$this->smtp_connection || empty($this->smtp_connection)){
			return false;
		}

		$this->get_lines();
		return true;
	}


	function SMTP_close(){
		fclose($this->smtp_connection);
		return true;
	}


	function Send(){

		# open SMTP connection
		if(!$this->SMTP_open($this->Host,$this->Port,$this->Timeout)){
			$this->error_handler("SMTP连接失败");
			return false;
		}

		# if SMTP need authorization
		if($this->AuthLogin==true){

			# send the EHLO command to SMTP
			$this->send_lines("EHLO ".$this->Host);
			if(substr($this->get_lines(),0,3)!=250){
				$this->error_handler("EHLO not accepted from server.");
				return false;
			}

			# send AUTH LOGIN command
			if($this->error_status==false){
				$this->send_lines("AUTH LOGIN");
				if(substr($this->get_lines(),0,3)!=334){
					$this->error_handler("AUTH LOGIN not accepted from server.");
					return false;
				}
			}

			# username and password authorization
			if($this->error_status==false){
				$this->SMTP_Auth();
				if(substr($this->get_lines(),0,3)!=235){
					$this->error_handler("Authentication failed.");
					return false;
				}
			}

		}else{

			# send the HELO command to SMTP
			$this->send_lines("HELO ".$this->Host);
			if(substr($this->get_lines(),0,3)!=250){
				$this->error_handler("HELO not accepted from server.");
				return false;
			}

		}


		# define sender email
		if($this->error_status==false){
			$this->send_lines("MAIL FROM: ".$this->FromEmail);
			if(substr($this->get_lines(),0,3)!=250){
				$this->error_handler("MAIL not accepted from server.");
				return false;
			}
		}


		# define sending email address
		$this->reset_current_count();
		while($this->error_status==false and $this->current_count<count($this->To)){

			$this->SMTP_RCPT($this->To[$this->current_count][0]);
			$this->current_count++;

		}
		$this->reset_current_count();
		while($this->error_status==false and $this->current_count<count($this->Cc)){

			$this->SMTP_RCPT($this->Cc[$this->current_count][0]);
			$this->current_count++;

		}
		$this->reset_current_count();
		while($this->error_status==false and $this->current_count<count($this->Bcc)){

			$this->SMTP_RCPT($this->Bcc[$this->current_count][0]);
			$this->current_count++;

		}


		# send mail
		$this->SMTP_Data("");


		# close SMTP connection
		$this->SMTP_close();


		# return send email success or fail
		if($this->error_status){
			return false;
		}else{
			return true;
		}
	}


	function reset_current_count(){
		$this->current_count=0;
	}


	function SMTP_RCPT($emailto){
		# send the RCPT TO command to SMTP
		$this->send_lines("RCPT TO: ".$emailto);
		if(substr($this->get_lines(),0,3)!=250){
			$this->error_handler("RCPT TO not accepted from server.");
			return false;
		}
		return true;
	}


	function SMTP_Auth(){
		$this->send_lines(base64_encode($this->AuthUser));
		$this->get_lines();
		$this->send_lines(base64_encode($this->AuthPass));
	}


	function SMTP_Data($message){
		# send the DATA command to SMTP
		if($this->error_status==false){
			$this->send_lines("DATA");
			if(substr($this->get_lines(),0,3)!=354){
				$this->error_handler("DATA not accepted from server.");
				return false;
			}
		}

		if($this->error_status==false){
			$this->boundary="_alangor" . md5(uniqid(time_()));

			# send header
			$this->Create_Header();

			# send body
			if(count($this->Attachment)>0){
				$this->send_lines("----=".$this->boundary);
				$this->send_lines("Content-Type: ".$this->IsHTML($this->IsHTML)."; charset=\"".$this->CharSet."\";");
				$this->send_lines("");
			}
			$this->Create_Body();
			$this->send_lines("");

			# add attachment
			if(count($this->Attachment)>0){
				$this->append_attachment();
			}

			# end of message
			$this->send_lines(".");
		}


	}


	function Create_Header(){
		$this->send_lines("Received: from client ".getenv("REMOTE_ADDR")." for alangor.com development team; ".date_("D, j M Y G:i:s")." ".$this->Timezone);
		$this->send_lines("Date: ".date_("D, j M Y G:i:s")." ".$this->Timezone);
		$this->send_lines("From: \"".$this->FromName."\" <".$this->FromEmail.">");
		if(count($this->To)>0){
			$this->send_lines($this->append_email("To",$this->To));
		}
		if(count($this->Cc)>0){
			$this->send_lines($this->append_email("Cc",$this->Cc));
		}
		if(count($this->ReplyTo)>0){
			$this->send_lines($this->append_email("Reply-to",$this->ReplyTo));
		}
		$this->send_lines("Subject: ".$this->Subject);
		$this->send_lines("X-Priority: ".$this->Priority);
		$this->send_lines("X-Mailer: ".$this->Version);
		$this->send_lines("X-Original-IP: ".getenv("REMOTE_ADDR"));
		$this->send_lines("Content-Transfer-Encoding: ".$this->Encoding);
		$this->send_lines("Return-Path: ".$this->FromEmail);
		$this->send_lines("MIME-Version: 1.0");
		if(count($this->Attachment)>0){
			$this->send_lines("Content-Type: multipart/mixed; charset=\"".$this->CharSet."\";");
			$this->send_lines("\tboundary=\"--=".$this->boundary."\"");
		}else{
			$this->send_lines("Content-Type: ".$this->IsHTML($this->IsHTML)."; charset=\"".$this->CharSet."\";");
		}
		$this->send_lines("");
	}


	function Create_Body(){
		if($this->WordWrap){
			$this->Body=wordwrap($this->Body,$this->WordWrap,"\n",1);
		}
		$this->send_lines(ereg_replace("\n","\r\n",$this->Body));
	}


	function append_email($type,$email) {
		$email_string=$type.": ";
		for($i=0;$i<count($email);$i++){
			if($i>0){
				$email_string.=",\r\n\t";
			}
			if(trim($email[$i][1])!= ""){
				$email_string.="\"".$email[$i][1]."\" <".$email[$i][0].">";
			}else{
				$email_string.="\"".$email[$i][1]."\"";
			}
		}
		return($email_string);
	}


	function append_attachment(){
		for($i=0;$i<count($this->Attachment);$i++){
			$this->send_lines("----=".$this->boundary);
			$this->send_lines("Content-Type: ".$this->Attachment[$i][2]."; name=\"=?".$this->CharSet."?B?".base64_encode($this->Attachment[$i][1])."?=\"");
			$this->send_lines("Content-Transfer-Encoding: base64");
			$this->send_lines("Content-Disposition: attachment; filename=\"=?".$this->CharSet."?B?".base64_encode($this->Attachment[$i][1])."?=\"\r\n");
			$this->send_lines(chunk_split(base64_encode(file_get_contents($this->Attachment[$i][0])))."\r\n");
		}
	}

	function IsHTML($status){
		if($status==true){
			return $this->ContentType="text/html";
		}else{
			return $this->ContentType="text/plain";
		}
	}


	function error_handler($message){
		if($this->MailerDebug==true){
			$this->error = $message;
			throw new Exception($message);
		}
		if($message!=""){
			return $this->error_status=true;
		}else{
			return $this->error_status=false;
		}
	}


	function get_lines(){
		while($data=fgets($this->smtp_connection,1024)){
			if(substr($data,3,1)==" "){
				break;
			}
		}
		return $data;
	}


	function send_lines($command){
		fputs($this->smtp_connection,$command."\r\n");
	}
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
