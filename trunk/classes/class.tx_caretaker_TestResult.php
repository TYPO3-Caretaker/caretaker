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
	var $comment='';
	
	function __construct ($status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $comment='' ){
		$this->status = $status;
		$this->value = $value;
		$this->comment = $comment;
	}
	
	function getState(){
		return $this->status;
	}
	
	function getValue(){
		return $this->value;
	}
	
	function getComment(){
		return $this->comment;
	}
	
}

?>