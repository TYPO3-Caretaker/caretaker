<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRepository.php');

class tx_caretaker_Test {
	
	private $uid;
	private $test_type;
	private $test_conf = false;
	private $test_conf_mode = false;
	
	function __construct($uid, $type, $conf){
		$this->uid = $uid;
		$this->test_type = $type;
		$this->test_conf_mode = $conf_mode;
		$this->test_conf = $conf;
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
	
	/*
	 * check weather this test was executed in time for this instance
	 *  
	 * @param $instance tx_caretaker_Instance
	 * @return boolean
	 */
	function isPending($instance){
		
	}
	
	function getTestResults($instance){
		
	}
	
	function getTestResultRange($startdate, $stopdate){
		
	}
		
}

?>