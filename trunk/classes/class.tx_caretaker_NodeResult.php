<?php

define("TX_CARETAKER_STATE_OK",          0);
define("TX_CARETAKER_STATE_WARNING",     1);
define("TX_CARETAKER_STATE_ERROR",       2);
define("TX_CARETAKER_STATE_UNDEFINED",  -1);

class tx_caretaker_NodeResult {
	
	protected $status=0;
	protected $ts = NULL;
	
	function __construct ($ts, $status){
		$this->ts     = (int)$ts;
		$this->status = $status;
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
	
	function getTstamp(){
		return $this->ts;
	}
	
}

?>