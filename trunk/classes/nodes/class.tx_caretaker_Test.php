<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_Node.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_TestResultRepository.php');

class tx_caretaker_Test extends tx_caretaker_Node{
	
	private $test_type;
	private $test_conf = false;
	private $test_conf_mode = false;
	private $test_interval  = false; 
	private $start_hour = false;
	private $stop_hour  = false; 
	
	function __construct($uid, $title, $parent, $type, $conf, $interval = 86400, $start_hour=false, $stop_hour=false, $hidden=FALSE ){
		
		parent::__construct($uid, $title, $parent, 'Test', $hidden);
		
		$this->test_type     = $type;
		$this->test_conf     = $conf;
		$this->test_interval = $interval;
		$this->start_hour    = $start_hour;
		$this->stop_hour     = $stop_hour;
		
	}
	
	function getInterval(){
		return $this->test_interval;
	}
	
	function getStartHour(){
		return $this->start_hour;
	}
	
	function getStopHour(){
		return $this->stop_hour;
	}
	
	function runTest(){
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service',$this->test_type);
		
		if (!$test_service) {
			$error = new tx_caretaker_TestResult(TX_CARETAKER_STATE_ERROR, 0, 'Test Service not found');
			return $error;
		}
		
			// execute test
		$test_service->setInstance( $this->getInstance() );
		$test_service->setConfiguration($this->test_conf);
		$test_result = $test_service->runTest();
		
			// return result
		return $test_result;
		
	}
	
	/*
	 * Update TestResult and store in DB. 
	 * 
	 * If force is not set the execution time and exclude hours are taken in account.
	 * 
	 * @param boolean Force update of children
	 * @return tx_caretaker_NodeResult
	 */
	
	function updateTestResult($force_update = false){
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$instance = $this->getInstance();
		
			// check cache and return
		if (!$force_update ){
			$result = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
			if ($result && $result->getTstamp() > time()-$this->test_interval ) {
				$this->log('cacheresult '.$result->getStateInfo().' '.$result->getValue().' '.$result->getMsg() );
				return $result;
			} else if ($this->start_hour > 0 || $this->stop_hour > 0 ) {
				$local_time = localtime(time(), true);
				$local_hour = $local_time['tm_hour'];
				if ($local_hour < $this->start_hour || $local_hour >= $this->stop_hour ){
					$this->log('cacheresult '.$result->getStateInfo().' '.$result->getValue().' '.$result->getMsg() );
					return $result;	
				}
			}
		}
			
			// prepare
		$test_id = $test_result_repository->prepareTest($instance, $this);
		$result = $this->runTest($instance);
		$test_result_repository->saveTestResult($test_id, $result);
				
		if ($result->getState() > 0){
			$this->sendNotification($result->getState(), $result->getMsg() );
		} 
		
		$this->log('update '.$result->getStateInfo().' '.$result->getValue().' '.$result->getMsg() );
		
		return $result;
		
	}
	
	function getTestResult(){
		$instance  = $this->getInstance();
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result    = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
	
		$this->log('cache '.$result->getStateInfo().' '.$result->getValue().' '.$result->getMsg() );
		
		return $result;
	}
	
	function getTestResultRange($startdate, $stopdate, $distance = FALSE){
		$instance  = $this->getInstance();
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$resultRange = $test_result_repository->getRangeByInstanceAndTest($instance, $this , $startdate, $stopdate, $distance);
		return $resultRange;
	}
	
	function getValueDescription(){
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service',$this->test_type);
		if ($test_service){
			return $test_service->getValueDescription();
		} else {
			return 'unknown service '.$this->test_type;
		}
	}
		
}

?>