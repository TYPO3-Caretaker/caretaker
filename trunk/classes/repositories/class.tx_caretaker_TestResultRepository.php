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
 
class tx_caretaker_TestResultRepository {

	/**
	 * Reference to the current Instance
	 * 
	 * @var $instance tx_caretaker_TestResultRepository
	 */
	private static $instance = null;

	/**
	 * The time in seconds to search for the last node result
	 *
	 * @var integer
	 */
	private $lastTestResultScanRange = 0;
	
	/**
	 * Private constructor use getInstance instead
	 * 
	 * @return unknown_type
	 */
	private function __construct (){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->lastTestResultScanRange = (int)$confArray['lastTestResultScanRange'];
	}
	
	/**
	 * Get the Singleton Object
	 *  
	 * @return tx_caretaker_TestResultRepository
	 */
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_TestResultRepository();
		}
		return self::$instance;
	}

	/**
	 * Get the latest Testresult for the given Instance and Test
	 *
	 * @param tx_caretaker_TestNode $testNode
	 * @return tx_caretaker_TestResult
	 */
	public function getLatestByNode( tx_caretaker_TestNode $testNode ){
		$testUID     = $testNode->getUid();
		$instanceUID = $testNode->getInstance()->getUid();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresult', 'test_uid='.$testUID.' AND instance_uid='.$instanceUID, '', 'tstamp DESC', '1'  );
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		if ($row) {
			$result = $this->dbrow2instance($row);
			return $result;
		} else {
			return new tx_caretaker_TestResult();
		}
	}


	/**
	 * Return the Number of available TestResults
	 *
	 * @param  tx_caretaker_TestNode $testNode
	 * @return integer
	 */
	public function getResultNumberByNode( tx_caretaker_TestNode $testNode ){
		$testUID     = $testNode->getUid();
		$instanceUID = $testNode->getInstance()->getUid();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'COUNT(*) AS number', 'tx_caretaker_testresult', 'test_uid='.$testUID.' AND instance_uid='.$instanceUID, '', '', '1'  );
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			if ($row) {
				return ( (int) $row['number'] );
			} else {
				return 0;
			}
	}

	/**
	 * Get a List of Testresults defined by Offset and Limit
	 *
	 * @param tx_caretaker_TestNode $testNode
	 * @param integer $offset
	 * @param integer $limit
	 * @return tx_caretaker_TestResultRange
	 */
	public function getResultRangeByNodeAndOffset( tx_caretaker_TestNode $testNode, $offset=0, $limit=10){

		$testUID     = $testNode->getUid();
		$instanceUID = $testNode->getInstance()->getUid();
		
		$result_range = new tx_caretaker_TestResultRange(NULL, NULL);
		$base_condition = 'test_uid='.$testUID.' AND instance_uid='.$instanceUID.' ';

		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresult', $base_condition, '', 'tstamp DESC' , (int)$offset.','.(int)$limit);

		$last = 0;

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result = $this->dbrow2instance($row);
			$result_range->addResult($result);
		}

		return $result_range;

	}

	/**
	 * Get the ResultRange for the given Instance Test and the timerange 
	 * 
	 * @param tx_caretaker_TestNode $testNode
	 * @param integer $start_timestamp
	 * @param integer $stop_timestamp
	 * @param boolean $graph By default the result range is created for the graph, so the last result is added again at the end
	 * @return tx_caretaker_testResultRange
	 */
	public function getRangeByNode ( tx_caretaker_TestNode $testNode, $start_timestamp, $stop_timestamp, $graph = true){

		$testUID     = $testNode->getUid();
		$instanceUID = $testNode->getInstance()->getUid();

		$result_range = new tx_caretaker_TestResultRange($start_timestamp, $stop_timestamp);
		$base_condition = 'test_uid='.$testUID.' AND instance_uid='.$instanceUID.' ';
		
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresult', $base_condition.'AND tstamp >='.$start_timestamp.' AND tstamp <='.$stop_timestamp, '', 'tstamp ASC'  );

		$last = 0;

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result = $this->dbrow2instance($row);
			$result_range->addResult($result);
		}

			// add first value if needed
		$first = $result_range->getFirst();
		if (!$first || ($first && $first->getTstamp() > $start_timestamp) ){
			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresult', $base_condition.' AND tstamp <'.$start_timestamp, '', 'tstamp DESC' , 1  );
			if ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row['tstamp'] = $start_timestamp;
				$result = $this->dbrow2instance($row);
				$result_range->addResult($result, 'first');
			}
		}
		
			// add last value if needed
		$last = $result_range->getLast(); 
		if ($last && $last->getTstamp() < $stop_timestamp){
			if($graph) {
				$real_last = new tx_caretaker_TestResult($stop_timestamp, $last->getState() , $last->getValue(), $last->getMsg() );
				$result_range->addResult($real_last);
			}
		}

		return $result_range; 
	}
	
	/**
	 * Convert DB-Row to Test Node Result
	 * 
	 * @param array $row
	 * @return tx_caretaker_TestResult
	 */
	private function dbrow2instance($row){
		$instance = new tx_caretaker_TestResult($row['tstamp'], $row['result_status'], $row['result_value'], $row['result_msg'], unserialize( $row['result_infos'] ));
		return $instance; 
	}
	
	/**
	 * Prepare a db record for storing of test results
	 * 
	 * @param tx_caretaker_InstanceNode $instance 
	 * @param tx_caretaker_TestNode $test 
	 * @return integer uid of created result record
	 */
	function prepareTest($instance, $test){
			//add an undefined row to the testresult column
		
		
		
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	} 
	
	/**
	 * Save the Testresult for the given TestNode
	 * @param tx_caretaker_TestNode $uid
	 * @param tx_caretaker_TestResult $result tx_caretaker_TestResult
	 */
	function saveTestResultForNode(tx_caretaker_TestNode $test, $testResult){

		$instance= $test->getInstance();

		$values = array(
			'test_uid'      => $test->getUid(),
			'instance_uid'  => $instance->getUid(),
			'result_status' => TX_CARETAKER_UNDEFINED,
			'tstamp'        => $testResult->getTstamp(),
			'result_status' => $testResult->getState(),
			'result_value'  => $testResult->getValue(),
			'result_msg'    => $testResult->getMsg(),
			'result_infos'  => serialize( $testResult->getInfoArray() )

		);
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_testresult', $values);
	
	}
	
	
}

?>