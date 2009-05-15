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
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResultRange.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResult.php');

class tx_caretaker_TestResultRepository {

	/**
	 * Reference to the current Instance
	 * 
	 * @var $instance tx_caretaker_TestResultRepository
	 */
	private static $instance = null;

	/**
	 * Private constructor use getInstance instead
	 * 
	 * @return unknown_type
	 */
	private function __construct (){}	
	
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
	 * @param tx_caretaker_Instance $instance 
	 * @param tx_caretaker_Test $test
	 * @return tx_caretaker_TestResult
	 */
	public function getLatestByInstanceAndTest($instance, $test){
			
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresult', 'test_uid='.$test->getUid().' AND instance_uid='.$instance->getUid(), '', 'tstamp DESC', '1'  );
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		if ($row) {
			$result = $this->dbrow2instance($row);
			return $result;
		} else {
			return tx_caretaker_TestResult::undefined();
		} 		
	}
	
	/**
	 * Get the ResultRange for the given Instance Test and the timerange 
	 * 
	 * @param tx_caretaker_Instance $instance 
	 * @param tx_caretaker_Test $test 
	 * @param integer $start_timestamp
	 * @param integer $stop_timestamp
	 * @return tx_caretaker_testResultRange
	 */
	public function getRangeByInstanceAndTest($instance, $test, $start_timestamp, $stop_timestamp ){
		$result_range = new tx_caretaker_TestResultRange($start_timestamp, $stop_timestamp);
		$base_condition = 'test_uid='.$test->getUid().' AND instance_uid='.$instance->getUid().' ';
		
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresult', $base_condition.'AND tstamp >='.$start_timestamp.' AND tstamp <='.$stop_timestamp, '', 'tstamp ASC'  );

		$last = 0;

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result = $this->dbrow2instance($row);
			$result_range->addResult($result);
		}

			// add first value if needed
		$first = $result_range->getFirst();
		if ($first && $first->getTstamp() > $start_timestamp){
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
			$real_last = new tx_caretaker_TestResult($stop_timestamp, $last->getState() , $last->getValue(), $last->getMsg() );
			$result_range->addResult($real_last);
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
		$instance = new tx_caretaker_TestResult($row['tstamp'], $row['result_status'], $row['result_value'], $row['result_msg']);
		return $instance; 
	}
	
	/**
	 * Prepare a db record for storing of test results
	 * 
	 * @param tx_caretaker_Instance $instance 
	 * @param tx_caretaker_Test $test 
	 * @return integer uid of created result record
	 */
	function prepareTest($instance, $test){
			//add an undefined row to the testresult column
		$values = array(
			'test_uid' =>$test->getUid(),
			'instance_uid' => $instance->getUid(),
			'result_status' => TX_CARETAKER_UNDEFINED,
			'tstamp' => time()
		);		
		
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_testresult', $values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	} 
	
	/**
	 * Save the Testresult to the DB-Record with the given UID
	 * @param integer $uid  
	 * @param $result tx_caretaker_TestResult
	 */
	function saveTestResult($uid, $test_result){
		$values = array(
			'tstamp'        => $test_result->getTstamp(),
			'result_status' => $test_result->getState(),
			'result_value'  => $test_result->getValue(),
			'result_msg'    => $test_result->getMsg(),
		);
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_caretaker_testresult', 'uid='.$uid, $values);
	}
	
	
}

?>