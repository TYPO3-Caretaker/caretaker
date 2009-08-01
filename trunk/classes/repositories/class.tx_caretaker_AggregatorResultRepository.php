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

require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_AggregatorResultRange.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_AggregatorResult.php');

class tx_caretaker_AggregatorResultRepository {

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
			self::$instance = new tx_caretaker_AggregatorResultRepository();
		}
		return self::$instance;
	}
	
	/**
	 * Get the latest Testresults for the given Node Object
	 * 
	 * @param tx_caretaker_AbstractNode $node  
	 * @returm tx_caretaker_AggreagtorResult
	 */
	public function getLatestByNode($node){
			
		$instance = $node->getInstance();
		if ($instance) {
			$instanceUid = $instance->getUid();
		}else {
			$instanceUid = 0;
		}
		
		$nodeType = $node->getType();
		$nodeUid  = $node->getUid();
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_aggregatorresult', 'aggregator_uid='.$nodeUid.' AND aggregator_type="'.$nodeType.'" AND instance_uid='.$instanceUid, '', 'tstamp DESC', '1'  );
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		if ($row) {
			$result = $this->dbrow2instance($row);
			return $result;
		} else {
			return new tx_caretaker_AggregatorResult();
		} 		
	}
	
	/**
	 * Get the ResultRange for the given Aggregator and the timerange 
	 * 
	 * @param tx_caretaker_AbstractNode $node
	 * @param integer $start_timestamp
	 * @param integer $stop_timestamp
	 * @return tx_caretaker_AggregatorResultRange
	 */
	public function getRangeByNode($node, $start_timestamp, $stop_timestamp ){
		$result_range = new tx_caretaker_AggregatorResultRange($start_timestamp, $stop_timestamp);
	
		$instance = $node->getInstance();
		if ($instance) {
			$instanceUid = $instance->getUid();
		}else {
			$instanceUid = 0;
		}
		
		$nodeType = $node->getType();
		$nodeUid  = $node->getUid();
		
		$base_condition = 'aggregator_uid='.$nodeUid.' AND aggregator_type="'.$nodeType.'" AND instance_uid='.$instanceUid;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_aggregatorresult', $base_condition.' AND tstamp >='.$start_timestamp.' AND tstamp <='.$stop_timestamp, '', 'tstamp ASC'  );
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result = $this->dbrow2instance($row);
			$result_range->addResult($result);
		}
		
			// add first value if needed
		$first = $result_range->getFirst();
		if ($first && $first->getTstamp() > $start_timestamp){
			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_aggregatorresult', $base_condition.' AND tstamp <'.$start_timestamp, '', 'tstamp DESC' , 1  );
			if ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row['tstamp'] = $start_timestamp;
				$result = $this->dbrow2instance($row);
				$result_range->addResult($result);
			}
		}
		
			// add last value if needed
		$last = $result_range->getLast(); 
		if ($last && $last->getTstamp() < $stop_timestamp){
			$real_last = new tx_caretaker_AggregatorResult($stop_timestamp, $last->getState() , $last->getNumUNDEFINED(), $last->getNumOK() , $last->getNumWARNING(),  $last->getNumERROR(),$last->getMsg() );
			$result_range->addResult($real_last);			
		}
		
		
		return $result_range; 
	}
	

	/**
	 * Save Aggregator Result to the DB
	 * 
	 * @param tx_caretaker_AggregatorNode $node
	 * @param tx_caretaker_AggregatorResult $aggregator_result
	 * @return integer UID of the new DB result Record
	 */
	public function addNodeResult(tx_caretaker_AggregatorNode $node, tx_caretaker_AggregatorResult $aggregator_result){
		
			//add an undefined row to the testresult column
		$instance = $node->getInstance();
		if ($instance) {
			$instanceUid = $instance->getUid();
		}else {
			$instanceUid = 0;
		}
		
		$values = array(
			'aggregator_uid'  => $node->getUid(),
			'aggregator_type' => $node->getType(),
			'instance_uid'    => $instanceUid,
		
			'result_status'        => $aggregator_result->getState(),
			'result_num_undefined' => $aggregator_result->getNumUNDEFINED(),
			'result_num_ok'        => $aggregator_result->getNumOK(),
			'result_num_warnig'    => $aggregator_result->getNumERROR(),
			'result_num_error'     => $aggregator_result->getNumWARNING(),
			'result_msg'           => $aggregator_result->getMsg(),
			'tstamp'               => $aggregator_result->getTstamp()
		);
		
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_aggregatorresult', $values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	} 
	
	/**
	 * Convert DB-Row to Aggregator Node Result
	 * 
	 * @param array $row DB Row
	 * @return tx_caretaker_AggregatorResult
	 */
	private function dbrow2instance($row){
		$instance = new tx_caretaker_AggregatorResult(
			$row['tstamp'], 
			$row['result_status'], 
			$row['result_num_undefined'], 
			$row['result_num_ok'], 
			$row['result_num_warnig'], 
			$row['result_num_error'], 
			$row['result_msg']
		);
		return $instance; 
	}

	
}

?>