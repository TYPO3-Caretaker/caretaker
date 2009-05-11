<?php

require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_ping extends tx_caretaker_TestServiceBase {
	
	function __construct(){
		$this->valueDescription = "Micro seconds";
	}
	
	function runTest() {
		
		$port         = $this->getConfigValue('port');
		$time_warning = $this->getConfigValue('max_time_warning');
		$time_error   = $this->getConfigValue('max_time_error');
		
		if (!$port) {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNKNOWN, 0 , 'Port was not defined' );
		}
		
		
		
		$starttime=microtime(true);
		
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$command   = $confArray['ping.']['cli_command'];
		if ($command ){
			
			$command = str_replace( '###' , $this->instance->getHost(), $command);
			$res = false;
			$msg = system ($command, $res);
			$endtime=microtime(true);
			$time=$endtime-$starttime;
			$time *= 1000; // convert to micro seconds
	
			if ($res == 0){ 
				if ($time_error && $time > $time_error) {
					return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping took '.$time.' micro seconds' );
				} 
		
				if ($time_warning && $time > $time_warning) {
					return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time , 'Ping took '.$time.' micro seconds' );
				} 
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time , 'Ping took '.$time.' micro seconds' );
			} else {
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping failed. '.$msg );
			}
		} else {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, 0 , 'CLI Ping-Command must be configured in ExtConf' );
		}
	}

}

?>