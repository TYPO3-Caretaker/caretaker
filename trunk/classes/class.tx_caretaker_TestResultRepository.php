<?php
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRange.php');

class tx_caretaker_TestResultRepository {

	private static $instance = null;

	private function __construct (){}	
	
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_TestResultRepository();
		}
		return self::$instance;
	}
	
	/*
	 * get the latest Testresult for the gioven 
	 */
	function getLatestByInstanceAndTest($instance, $test){
		$result_range = new tx_caretaker_TestResultRange();
	
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresults', 'test_uid='.$test->getUid().' AND instance_uid='.$instance->getUid(), '', 'tstamp DESC', '1'  );
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		if ($row) {
			$result = $this->dbrow2instance($row);
			return $result;
		} else {
			return false;
		} 		
	}
	
	/*
	 * @return tx_caretaker_testResultRange
	 */
	function getRangeByInstanceAndTest($instance, $test, $start_ts, $stop_ts){
		$result_range = new tx_caretaker_TestResultRange();
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_testresults', 'test_uid='.$test->getUid().' AND instance_uid='.$instance->getUid().' AND tstamp >='.$start_ts.' AND tstamp <='.$stop_ts, '', 'tstamp ASC'  );
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result = $this->dbrow2instance($row);
			$result_range->addResult($result);
		}
		return $result_range; 
	}
	
	function dbrow2instance($row){
		$instance = new tx_caretaker_TestResult($row['result_status'], $row['result_value'], $row['result_msg']);
		return $instance; 
	}
	
	/*
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
		
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_testresults', $values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	} 
	
	/*
	 * 	
	 */
	function saveTestResult($uid, $result){
		$values = array(
			'result_status' => $result->getState(),
			'result_value'  => $result->getValue(),
			'result_msg'    => $result->getComment(),
		);
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_caretaker_testresults', 'uid='.$uid, $values);
	}
	
	
}

?>