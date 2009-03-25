<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_ping extends tx_caretaker_TestServiceBase {
		
	
	function runTest() {
		
		$port         = $this->getConfigValue('port');
		$time_warning = $this->getConfigValue('max_time_warning');
		$time_error   = $this->getConfigValue('max_time_error');
		
		if (!$port) {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNKNOWN, 0 , 'Port was not defined' );
		}
		
		$starttime=microtime();
		$socket=@fsockopen($this->instance->getHost(),$port);
		$endtime=microtime();
	 
		if ($socket!=false){
			fclose($socket);
			list($msec,$sec)=explode(" ",$starttime);
			$starttime=(float)$msec+(float)$sec;
			list($msec,$sec)=explode(" ",$endtime);
			$endtime=(float)$msec+(float)$sec;
			$time=($endtime-$starttime)*1000;
			
			if ($time_error && $time > $time_error) {
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping took '.$time.' milliseconds' );
			} 

			if ($time_warning && $time > $time_warning) {
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time , 'Ping took '.$time.' milliseconds' );
				
			} 

			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time , '' );
			
		} else {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, 0 , 'Error: Ping failed on '.$this->instance->getHost().':80' );
		}
		
	}
	
}

?>