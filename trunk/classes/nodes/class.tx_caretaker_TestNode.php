<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 * 
 * @todo Check proper setup of $this->testService, use $this->testService instead of
 * local variable in tx_caretaker_TestNode::updateTestResult and tx_caretaker_TestNode::runTest
 * Use of $this->testService currently executes several tests with the same setup.
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_AbstractNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_TestResultRepository.php');
require_once PATH_site.'typo3/sysext/lang/lang.php';

class tx_caretaker_TestNode extends tx_caretaker_AbstractNode{
	
	/**
	 * Test Service Type
	 * @var string
	 */
	private $test_service_type;
	
	/**
	 * Configuration of the test
	 * @var unknown_type
	 */
	private $test_service_configuration = false;
	
	/**
	 * Reference to the test service
	 * @var tx_caretaker_TestServiceBase
	 */
	private $test_service = null;
	
	/**
	 * Interval of Tests in Seconds
	 * @var integer
	 */
	private $test_interval  = false;

	/**
	 * The test shall be executed only after this hour
	 * @var integer
	 */
	private $start_hour = false;
	
	/**
	 * The test shall be executed only before this hour
	 * @var integer
	 */
	private $stop_hour  = false; 
	
	/**
	 * Constructor
	 * 
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent_node
	 * @param string $service_type
	 * @param string $service_configuration
	 * @param integer $interval
	 * @param integer $start_hour
	 * @param integer $stop_hour
	 * @param boolean $hidden
	 * @return tx_caretaker_TestNode
	 */
	public function __construct($uid, $title, $parent_node, $service_type, $service_configuration, $interval = 86400, $start_hour=false, $stop_hour=false, $hidden=FALSE ){
		
		parent::__construct($uid, $title, $parent_node, 'Test', $hidden);
		
		$this->test_service_type = $service_type;
		$this->test_service_configuration = $service_configuration;
		$this->test_interval = $interval;
		$this->start_hour    = $start_hour;
		$this->stop_hour     = $stop_hour;
		
		//unset($this->test_service);
		
		//echo $service_configuration;
		
		//$this->test_service = t3lib_div::makeInstanceService('caretaker_test_service',$service_type);
		//$this->test_service->setInstance( $this->getInstance() );
		//$this->test_service->setConfiguration($service_configuration);
	}
	
	/**
	 * Get the Test Interval 
	 * @return unknown_type
	 */
	public function getInterval(){
		return $this->test_interval;
	}
	
	/**
	 * Get the test start hour
	 * @return unknown_type
	 */
	public function getStartHour(){
		return $this->start_hour;
	}
	
	/**
	 * Get the test stop hour
	 * @return unknown_type
	 */
	public function getStopHour(){
		return $this->stop_hour;
	}
	
	/**
	 * Execute the test
	 * 
	 * @param tx_caretaker_TestServiceBase $testService
	 * @return tx_caretaker_TestResult
	 */
	public function runTest(tx_caretaker_TestServiceBase $testService){
		
		if (!$testService) {
			
			$error = new tx_caretaker_TestResult(TX_CARETAKER_STATE_ERROR, 0, 'Test Service not found');
			return $error;
		}
		
			// execute test
		
		$test_result = $testService->runTest();
		
			// return result
		return $test_result;
		
	}
	
	/**
	 * Update TestResult and store in DB. If the Test is not due the result is fetched from the cache.
	 * 
	 * If force is not set the execution time and exclude hours are taken in account.
	 * 
	 * @param boolean $force_update Force update of this test
	 * @return tx_caretaker_NodeResult
	 */
	public function updateTestResult($force_update = false){
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$instance = $this->getInstance();
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service',$this->test_service_type);
		$test_service->setInstance( $this->getInstance() );
		$test_service->setConfiguration($this->test_service_configuration);
		
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
		
		if($test_service->isExecutable()) {
			
				// prepare
			$test_id = $test_result_repository->prepareTest($instance, $this);
			$result = $this->runTest($test_service);
			$test_result_repository->saveTestResult($test_id, $result);
			
			$message = unserialize($result->getMsg());
			
			if($message) {
				
				$msg = $this->aggregateMessage($message);
				
			} else {
			
				$msg = $result->getMsg();
			}
					
			if ($result->getState() > 0){
				$this->sendNotification($result->getState(), $msg );
			} 
			
			$this->log('update '.$result->getStateInfo().' '.$result->getValue().' '.$msg );
			
		} else {
			
			$result = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
			$this->log('Service is busy... skipping test.');
		}
		
		return $result;
		
	}
	
	private function aggregateMessage($msgArray) {
		
		global $TYPO3_CONF_VARS;
		
		$message = '';
		
		foreach($msgArray as $resultMessage) {
			
			if(substr($resultMessage[0], 0, 3) == 'LLL' && !empty($resultMessage[2])) {
				
				$conf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['caretaker']);
				$langKey = $conf['testrunner.']['lang'];
				/* @var $LANG language */
				$LANG = t3lib_div::makeInstance('language');
				$LANG->init($langKey);
				preg_match('/LLL:(EXT:.*):(.*)/', $resultMessage[0], $matches);
				$msg = $LANG->getLLL($matches[2], $LANG->readLLfile(t3lib_div::getFileAbsFileName($matches[1])),true);
				$msg = str_replace('###BROWSER###', $resultMessage[2], $msg);
				$msg = str_replace('###MESSAGE###', $resultMessage[3], $msg);
				$msg = str_replace('###TIME###', round($resultMessage[4], 2), $msg);
				preg_match('/LLL:(EXT:.*):(.*)/', $resultMessage[5], $matches);
				$msg = str_replace('###TIME_UNIT###', substr($resultMessage[5], 0, 3) == 'LLL' ? $LANG->getLLL($matches[2], $LANG->readLLfile(t3lib_div::getFileAbsFileName($matches[1])), true) : $resultMessage[5], $msg);
				
				if(isset($resultMessage[6])) {
					
					$msg = str_replace('###TIME_LIMIT###', $resultMessage[6], $msg);
				}
				
			} else {
				
				$msg = $resultMessage[0];
			}
			
			for($i = 8; $i < count($resultMessage); $i++) {
				
				$msg = str_replace('###VALUE_'.$i.'###',$resultMessage[$i], $msg);
			}
			
			$message .= $msg;
		}
		
		return $message;
	}
	
	/**
	 * Get the current Test Result from Cache
	 * 
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getTestResult()
	 */
	public function getTestResult(){
		$instance  = $this->getInstance();
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result    = $test_result_repository->getLatestByInstanceAndTest($instance, $this);
	
		$this->log('cache '.$result->getStateInfo().' '.$result->getValue().' '.$result->getMsg() );
		
		return $result;
	}
	
	/**
	 * Get the TestResultRange for the given Timerange
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getTestResultRange()
	 * @param $graph True by default. Used in the resultrange repository the specify the handling of the last result. For more information see tx_caretaker_testResultRepository.
	 */
	public function getTestResultRange($start_timestamp, $stop_timestamp, $graph = true){
		$instance  = $this->getInstance();
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$resultRange = $test_result_repository->getRangeByInstanceAndTest($instance, $this , $start_timestamp, $stop_timestamp, $graph);
		return $resultRange;
	}
	
	/**
	 * Get the Value Description for this test
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getValueDescription()
	 */
	public function getValueDescription() {
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service',$this->test_service_type);
		
		if ($test_service){
			return $test_service->getValueDescription();
		} else {
			return 'unknown service '.$this->test_service_type;
		}
	}

	/**
	 * Get the number of available Test Results
	 *
	 * @return integer
	 */
	public function getTestResultNumber(){
		$instance  = $this->getInstance();
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$resultNumber = $test_result_repository->getResultNumberByInstanceAndTest($instance, $this);
		return $resultNumber;
	}

        /**
	 * Get the TestResultRange for the Offset and Limit
         *
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getTestResultRange()
	 * @param $graph True by default. Used in the resultrange repository the specify the handling of the last result. For more information see tx_caretaker_testResultRepository.
	 */
	public function getTestResultRangeByOffset($offset=0, $limit=10){
		$instance  = $this->getInstance();
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$resultRange = $test_result_repository->getResultRangeByInstanceAndTestAndOffset($instance, $this , $offset, $limit);
		return $resultRange;
	}
}
?>