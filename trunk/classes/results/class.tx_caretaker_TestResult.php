<?php


require_once(t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResult.php');

class tx_caretaker_TestResult extends tx_caretaker_NodeResult {
	
	var $value=0;
	var $msg='';

	function __construct ($ts, $status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $msg=''){
		parent::__construct($ts, $status);
		$this->value = $value;
		$this->msg   = $msg;
	}
	
	static public function undefined (){
		$ts = time();
		return new tx_caretaker_TestResult($ts, TX_CARETAKER_STATE_UNDEFINED, 0, 'Result is undefined');
	}
	
	static public function restore($ts, $status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $comment=''){
		return new tx_caretaker_TestResult($ts, $status, $value, $comment);
	}
	
	static public function create($status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $comment=''){
		$ts = time();
		return new tx_caretaker_TestResult($ts, $status, $value, $comment);
	}
		
	public function getValue(){
		return $this->value;
	}
	
	public function getMsg(){
		return $this->msg;
	}
	

}

?>