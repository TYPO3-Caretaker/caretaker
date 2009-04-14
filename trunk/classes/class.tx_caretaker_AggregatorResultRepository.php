<?php
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRange.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');

class tx_caretaker_AggregatorResultRepository {

	// @var $instance tx_caretaker_TestResultRepository
	private static $instance = null;

	private function __construct (){}	
	
	/*
	 * @return tx_caretaker_TestResultRepository
	 */
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_AggregatorResultRepository();
		}
		return self::$instance;
	}
	
	/*
	 * get the latest Testresult for the gioven 
	 */
	function getLatestByNode($node){
		
		$result_range = new tx_caretaker_TestResultRange();
	
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
			return tx_caretaker_TestResult::undefined();
		} 		
	}
	
	/*
	 * @return tx_caretaker_testResultRange
	 */
	function getRangeByNode($node, $start_ts, $stop_ts ){
		$result_range = new tx_caretaker_TestResultRange($start_ts, $stop_ts);
		
		$instance = $node->getInstance();
		if ($instance) {
			$instanceUid = $instance->getUid();
		}else {
			$instanceUid = 0;
		}
		
		$nodeType = $node->getType();
		$nodeUid  = $node->getUid();
		
		$base_condition = 'aggregator_uid='.$nodeUid.' AND aggregator_type="'.$nodeType.'" AND instance_uid='.$instanceUid;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_aggregatorresult', $base_condition.' AND tstamp >='.$start_ts.' AND tstamp <='.$stop_ts, '', 'tstamp ASC'  );
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result = $this->dbrow2instance($row);
			$result_range->addResult($result);
		}
		
			// add first value if needed
		$first = $result_range->getFirst();
		if ($first && $first->getTstamp() > $start_ts){
			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_aggregatorresult', $base_condition.' AND tstamp <'.$start_ts, '', 'tstamp DESC' , 1  );
			if ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row['tstamp'] = $start_ts;
				$result = $this->dbrow2instance($row);
				$result_range->addResult($result, 'first');
			}
		}
		
			// add last value if needed
		$last = $result_range->getLast(); 
		if ($last && $last->getTstamp() < $stop_ts){
			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_aggregatorresult', $base_condition.' AND tstamp >'.$stop_ts, '', 'tstamp ASC' , 1  );
			if ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row['tstamp'] = $stop_ts;
				$result = $this->dbrow2instance($row);
				$result_range->addResult($result, 'last');
			}
		}
		
		
		return $result_range; 
	}
	
	/*
	 * Prepare a db record for storing of test results
	 * @return integer uid of created result record
	 */
	function addNodeResult($node, $test_result){
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
		
			'result_status'   => $test_result->getState(),
			'result_value'    => $test_result->getValue(),
			'result_msg'      => $test_result->getMsg(),
			'tstamp'          => $test_result->getTstamp()
		);
		
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_aggregatorresult', $values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	} 
	
	function dbrow2instance($row){
		$instance = tx_caretaker_TestResult::restore($row['tstamp'], $row['result_status'], $row['result_value'], $row['result_msg']);
		return $instance; 
	}
	

	

	
}

?>