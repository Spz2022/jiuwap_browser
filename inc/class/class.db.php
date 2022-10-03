<?php
/*
 *
 *	MYSQL数据库类
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */
if ( !defined('DEFINED_TIANYIW') || DEFINED_TIANYIW <> 'jiuwap.cn' ){
	header('Content-Type: text/html; charset=utf-8');
	echo '<a href="http://jiuwap.cn">error</a>';
	exit;
}
class db{
	function __construct($dbhost, $dbuser, $dbpw, $dbname, $prefix='', $dbcharset='utf8',$pconnect = null) {
		$link = null;
		$func = empty($pconnect) ? 'mysql_pconnect' : 'mysql_pconnect';
		if(!$link = @$func($dbhost, $dbuser, $dbpw, 1)) {
			$this->halt('连接失败');
		} else {
			$this->curlink = $link;
			$dbcharset && mysql_query('SET sql_mode="",NAMES "'.$dbcharset.'",CHARACTER SET '.$dbcharset.',CHARACTER_SET_RESULTS='.$dbcharset.',COLLATION_CONNECTION="'.$dbcharset.'_general_ci"', $link);
			if ($dbname){
				if ( !@$this->select_db($dbname, $link) ){
					$this->halt('数据库丢失');
				}
			}
		}
		$this->prefix = $prefix;
		$this->querynum = 0;
	}

	function delete($table,$where='', $limit = 0, $unbuffered = true){
		$sql = 'DELETE FROM '.$this->table($table).($where ? " WHERE $where" : '').($limit ? " LIMIT $limit" : '');
		return $this->query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
	}

	function replace($table, $data , $where , $unbuffered = true){
		$session = $this->fetch_first("SELECT id FROM {$this->table($table)} WHERE $where") ;
		if ( !$session ){
			$this->insert($table, $data, $unbuffered);
		}else{
			$this->update($table, $data, $where, $unbuffered);
		}
	}

	function update($table, $data,$where='', $unbuffered = false){
		//$data=array('key'=>'value',...);
		$sql = '';
		foreach ($data as $k => $v) {
			$sql && $sql .= ',';
			$sql .= "`$k`='$v'";
		}
		$where && $sql .= " WHERE $where";
		$table = $this->table($table);
		return $this->query("UPDATE {$table} SET {$sql}",$unbuffered ? 'UNBUFFERED' : '');
	}

	function insert($table, $data, $return_insert_id = false){
		//$data=array('key'=>'value',...);
		$sql1 = $sql2 = '';
		foreach ($data as $k => $v) {
			if ( $sql1 ){
				$sql1 .= ',';
				$sql2 .= ',';
			}
			$sql1 .= "`$k`";
			$sql2 .= "'$v'";
		}
		$table = $this->table($table);
		$return = $this->query("INSERT INTO {$table} ({$sql1}) VALUES ({$sql2})");
		$return_insert_id && $return = $this->insert_id();
		return $return;
	}

	function table($str){
		return $this->prefix.$str;
	}

	static function connect($dbhost, $dbuser, $dbpw, $dbname, $prefix='', $dbcharset='utf8',$pconnect = null) {
		$DB = new db($dbhost, $dbuser, $dbpw, $dbname, $prefix, $dbcharset,$pconnect);
		return $DB;
	}


	function select_db($dbname) {
		return mysql_select_db($dbname, $this->curlink);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->curlink))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->connect();
				return $this->query($sql, 'RETRY'.$type);
			}
			if($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('语句错误', $sql);
			}
		}

		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysql_affected_rows($this->curlink);
	}

	function error() {
		return isset($this->curlink) ? mysql_error($this->curlink) : mysql_error();
	}

	function errno() {
		return isset($this->curlink) ? mysql_errno($this->curlink) : mysql_errno();
	}

	function result($query, $row = 0) {
		return @mysql_result($query, $row);
	}

	function num_rows($query) {
		return mysql_num_rows($query);
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->curlink);
		}
		return $this->version;
	}

	function close() {
		return @mysql_close($this->curlink);
	}

	function halt($message='',$sql='') {
        if ( $sql ){
            $sql = "\r\n".$sql;
        }
		$str = $message."\r\n".$this->errno().$sql."\r\n".$this->error();
        write_log(__FILE__,__line__,$str);
	}

	function __destruct(){
		$this->close();
	}

}
