<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRepository.php');

class tx_caretaker_Test {
	
	private $uid;
	private $test_type;
	private $test_conf = false;
	private $test_conf_mode = false;
	private $test_interval  = false; 
	
	function __construct($uid, $type, $conf, $interval = 86400){
		$this->uid = $uid;
		$this->test_type = $type;
		$this->test_conf = $conf;
		$this->test_interval = $interval;
	}
	
	function getUid(){
		return $this->uid;
	}

	function runTest($instance){
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service',$this->test_type);
		
		if (!$test_service) {
			$error = new tx_caretaker_TestResult(TX_CARETAKER_STATE_ERROR, 0, 'Test Service not found');
			return $error;
		}
			// prepare repository		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$test_id = $test_result_repository->prepareTest($instance, $this);
			
			// execute test
		$test_service->setInstance($instance);
		$test_service->setConfiguration($this->test_conf);
		$test_result = $test_service->runTest();
		
			// save result
		$test_result_repository->saveTestResult($test_id, $test_result);
		
			// return result
		return $test_result;
		
	}
	
	function updateState($instance){
		//debug('updateState Test:'.$this->uid);
		$last_result = $this->getState($instance);
		if ($last_result == false || ($last_result->getTstamp() < time()-$this->test_interval) ){
			$this->runTest($instance);
		}
	}
	
	function getState($instance){
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
		return $result;
	}
	
	function getTestResultRange($instance, $startdate, $stopdate){
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result = $test_result_repository->getRangeByInstanceAndTest($instance, $this , $startdate, $stopdate);
		return $result;
	}
		
}

?>