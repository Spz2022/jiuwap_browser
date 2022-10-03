<?php
/*
 *
 *	运行时间
 *
 *	2011-1-14 @ jiuwap.cn
 *
 */

class runtime{
	private $StartTime = 0;
	private $StopTime = 0;

	private function get_microtime(){
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	function start(){
		$this->StartTime = $this->get_microtime();
	}

	function stop(){
		$this->StopTime = $this->get_microtime();
	}

	function spent(){
		return round(($this->StopTime - $this->StartTime) * 1000, 1);
	}
}
