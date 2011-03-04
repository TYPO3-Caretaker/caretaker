<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * Repository to handle the storing and reconstruction of all
 * testResults. The whole object <-> database
 * communication happens here.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
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

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_lasttestresult', 'test_uid='.$testUID.' AND instance_uid='.$instanceUID, '', '', '1'  );
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
	 * @return tx_caretaker_TestResultRange
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
				$real_last = new tx_caretaker_TestResult( $stop_timestamp, $last->getState(), $last->getValue(), $last->getMessage()->getText(), $last->getSubMessages() );
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
		$message     = new tx_caretaker_ResultMessage( $row['result_msg'] , unserialize($row['result_values']) );
		$submessages = ($row['result_submessages']) ? unserialize( $row['result_submessages'] ) : array ();
		$instance    = new tx_caretaker_TestResult(
			$row['tstamp'],
			$row['result_status'],
			$row['result_value'],
			$message,
			$submessages
		);
		return $instance; 
	}
	
	/**
	 * Save the Testresult for the given TestNode
	 * @param tx_caretaker_TestNode $uid
	 * @param tx_caretaker_TestResult $result tx_caretaker_TestResult
	 */
	function saveTestResultForNode(tx_caretaker_TestNode $test, $testResult){

		$values = array(
				'test_uid'      => $test->getUid(),
				'instance_uid'  => $test->getInstance()->getUid(),
				'result_status' => TX_CARETAKER_UNDEFINED,
				'tstamp'        => $testResult->getTstamp(),
				'result_status' => $testResult->getState(),
				'result_value'  => $testResult->getValue(),
				'result_msg'    => $testResult->getMessage()->getText(),
				'result_values' => serialize( $testResult->getMessage()->getValues() ),
				'result_submessages'  => serialize( $testResult->getSubMessages() )
		);

			// store log of results
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_testresult', $values);

			// store last results for fast access
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'uid', 'tx_caretaker_lasttestresult', 'test_uid = '.$test->getUid(). ' AND instance_uid = '.$test->getInstance()->getUid() , '', '' , 1  );
		if ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_caretaker_lasttestresult', 'uid = '.$row['uid'], $values  );
		} else {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_lasttestresult', $values);
		}

	}
	
}

?>