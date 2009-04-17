<?php

require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_ping extends tx_caretaker_TestServiceBase {
		
	
	function runTest() {
		
		$port         = $this->getConfigValue('port');
		$time_warning = $this->getConfigValue('max_time_warning');
		$time_error   = $this->getConfigValue('max_time_error');
		
		if (!$port) {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNKNOWN, 0 , 'Port was not defined' );
		}
		
		$starttime=microtime(true);
		$command = "/sbin/ping -c 1 ".$this->instance->getHost().' >/dev/null' ;
		$res = false;
		$msg = system ($command, $res);
		$endtime=microtime(true);
		$time=$endtime-$starttime;

		if ($res == 0){ 
			if ($time_error && $time > $time_error) {
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping took '.$time.' seconds' );
			} 
	
			if ($time_warning && $time > $time_warning) {
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time , 'Ping took '.$time.' seconds' );
			} 
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time , 'Ping took '.$time.' seconds' );
		} else {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping failed. '.$msg );
		}
	}
	
}

?>