<?php

/*
 * @TODO add comments
 */

define("TX_CARETAKER_STATE_OK",          0);
define("TX_CARETAKER_STATE_WARNING",     1);
define("TX_CARETAKER_STATE_ERROR",       2);
define("TX_CARETAKER_STATE_UNDEFINED",  -1);
 
class tx_caretaker_TestResult {
	var $status=0;
	var $value=0;
	var $msg='';
	var $ts = 0;

	function __construct ($ts, $status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $msg=''){
		$this->status = $status;
		$this->value = $value;
		$this->msg = $msg;
		$this->ts = $ts;
	}
	
	static function undefined (){
		$ts = time();
		return new tx_caretaker_TestResult($ts, TX_CARETAKER_STATE_UNDEFINED, 0, 'Result is undefined');
	}
	
	static function restore ($ts, $status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $comment=''){
		return new tx_caretaker_TestResult($ts, $status, $value, $comment);
	}
	
	static function create($status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $comment=''){
		$ts = time();
		return new tx_caretaker_TestResult($ts, $status, $value, $comment);
	}
		
	function getState(){
		return $this->status;
	}
	
	function getStateInfo (){
		switch ($this->status){
			case TX_CARETAKER_STATE_OK:
				return 'OK';
			case TX_CARETAKER_STATE_ERROR:
				return 'ERROR';
			case TX_CARETAKER_STATE_WARNING:
				return 'WARNING';
			case TX_CARETAKER_STATE_UNDEFINED:
				return 'UNDEFINED';
		}
	}
	
	function getValue(){
		return $this->value;
	}
	
	function getMsg(){
		return $this->msg;
	}
	
	function getTstamp(){
		return $this->ts;
	}
	

	
}

?>