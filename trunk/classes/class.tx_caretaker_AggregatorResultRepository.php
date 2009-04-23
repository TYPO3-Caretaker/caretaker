<?php
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_AggregatorResultRange.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_AggregatorResult.php');

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
		$result_range = new tx_caretaker_AggregatorResultRange($start_ts, $stop_ts);
		
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
				$result_range->addResult($result);
			}
		}
		
			// add last value if needed
		$last = $result_range->getLast(); 
		if ($last && $last->getTstamp() < $stop_ts){
			$real_last = tx_caretaker_AggregatorResult::restore($stop_ts, $last->getState() , $last->getNumUNDEFINED(), $last->getNumOK() , $last->getNumWARNING(),  $last->getNumERROR(),$last->getMsg() );
			$result_range->addResult($real_last);			
		}
		
		
		return $result_range; 
	}
	
	/*
	 * Prepare a db record for storing of test results
	 * @return integer uid of created result record
	 */
	function addNodeResult(tx_caretaker_AggregatorNode $node, tx_caretaker_AggregatorResult $test_result){
		
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
		
			'result_status'        => $test_result->getState(),
			'result_num_undefined' => $test_result->getNumUNDEFINED(),
			'result_num_ok'        => $test_result->getNumOK(),
			'result_num_warnig'    => $test_result->getNumERROR(),
			'result_num_error'     => $test_result->getNumWARNING(),
			'result_msg'           => $test_result->getMsg(),
			'tstamp'               => $test_result->getTstamp()
		);
		
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_aggregatorresult', $values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	} 
	
	function dbrow2instance($row){
		$instance = tx_caretaker_AggregatorResult::restore(
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