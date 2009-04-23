<?php

require_once(t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResult.php');

class tx_caretaker_AggregatorResult extends tx_caretaker_NodeResult {
	
	var $num_UNDEFINED=0;
	var $num_OK=0;
	var $num_ERROR=0;
	var $num_WARNING=0;
	
	var $msg='';

	function __construct ($ts, $status=TX_CARETAKER_STATE_UNDEFINED, $undefined=0, $ok=0, $warning=0, $error=0, $msg=''){
		parent::__construct($ts, $status);
		
		$this->num_UNDEFINED = $undefined;
		$this->num_OK        = $ok; 
		$this->num_WARNING   = $warning;
		$this->num_ERROR     = $error;
		
		$this->msg   = $msg;
	}
	
	static function undefined (){
		$ts = time();
		return new tx_caretaker_AggregatorResult($ts, TX_CARETAKER_STATE_UNDEFINED, $undefined=0, $ok=0, $warning=0, $error=0, 'Result is undefined');
	}
	
	static function restore($ts, $status=TX_CARETAKER_STATE_UNDEFINED, $undefined=0, $ok=0, $warning=0, $error=0, $comment=''){
		return new tx_caretaker_AggregatorResult($ts, $status, $undefined, $ok, $warning, $error, $comment);
	}
	
	static function create($status=TX_CARETAKER_STATE_UNDEFINED, $undefined=0, $ok=0, $warning=0, $error=0, $comment=''){
		$ts = time();
		return new tx_caretaker_AggregatorResult($ts, $status, $undefined, $ok, $warning, $error, $comment);
	}
	
	function getValue(){
		return '';
	}
	
	function getNumUNDEFINED(){
		return $this->num_UNDEFINED;
	}
	
	function getNumOK(){
		return $this->num_OK;
	}
	
	function getNumWARNING(){
		return $this->num_WARNING;
	}
	
	function getNumERROR(){
		return $this->num_ERROR;
	}
	
	function getMsg(){
		return $this->msg;
	}
	
	function is_different(tx_caretaker_AggregatorResult $result){
		if ( 
			$this->status        != $result->getState()||
			$this->num_UNDEFINED != $result->getNumUNDEFINED() ||
			$this->num_OK        != $result->getNumOK() ||
			$this->num_WARNING   != $result->getNumWARNING() ||
			$this->num_ERROR     != $result->getNumERROR()
		) {
			return false;
		} else {
			return true;
		}
	}
	
}

?>