<?php
 
class tx_caretaker_InstanceRepository {

	private static $instance = null;

	private function __construct (){}	
	
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_InstanceRepository();
		}
		return self::$instance;
	}
	
	function getAll(){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row);
		}
		return $result;
	}
	
	function getByUid($id){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0 AND uid = '.$id);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ( $row){
			return $this->dbrow2instance($row);
		} else {
			return false; 
		}
	}
	
	
	function dbrow2instance($row){
		$instance = new tx_caretaker_Instance($row['uid'], $row['url'], $row['ip'], $row);
		return $instance; 
	}
	
}

?>