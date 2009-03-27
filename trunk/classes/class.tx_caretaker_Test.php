<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Node.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRepository.php');

class tx_caretaker_Test extends tx_caretaker_Node{
	
	private $test_type;
	private $test_conf = false;
	private $test_conf_mode = false;
	private $test_interval  = false; 
	private $start_hour;
	private $stop_hour; 
	
	function __construct($uid, $title, $parent, $type, $conf, $interval = 86400, $start_hour=false, $stop_hour=false ){
		
		parent::__construct($uid, $title, $parent, 'Test');
		
		$this->test_type     = $type;
		$this->test_conf     = $conf;
		$this->test_interval = $interval;
		$this->start_hour    = $start_hour;
		$this->stop_hour     = $stop_hour;
	}
	
	function runTest($instance){
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service',$this->test_type);
		
		if (!$test_service) {
			$error = new tx_caretaker_TestResult(TX_CARETAKER_STATE_ERROR, 0, 'Test Service not found');
			return $error;
		}
		
			// execute test
		$test_service->setInstance($instance);
		$test_service->setConfiguration($this->test_conf);
		$test_result = $test_service->runTest();
		
			// return result
		return $test_result;
		
	}
	
	function updateState($instance, $force_update){
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		
			// check cache and return
		if (!$force_update ){
			$result = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
			if ( $result->getTstamp() > time()-$this->test_interval ) {
				return $result;
			} else if ($this->start_hour || $this->stop_hour ) {
				$local_time = localtime(time(), true);
				$local_hour = $local_time['tm_hour'];
				if ($local_hour < $this->start_hour || $local_hour > $this->stop_hour ){
					return $result;	
				}
			} 
		}
			
			// update
		$test_id = $test_result_repository->prepareTest($instance, $this);
		$result = $this->runTest($instance);
		$test_result_repository->saveTestResult($test_id, $result);
		
		$this->log('update '.$result->getState().' '.$result->getValue().' '.$result->getComment() );
		
		return $result;
		
	}
	
	function getState($instance){
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result    = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
	
		$this->log($result->getState().' '.$result->getValue().' '.$result->getComment() );
		
		return $result;
	}
	
	function getTestResultRange($instance, $startdate, $stopdate){
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result = $test_result_repository->getRangeByInstanceAndTest($instance, $this , $startdate, $stopdate);
		return $result;
	}
		
}

?>