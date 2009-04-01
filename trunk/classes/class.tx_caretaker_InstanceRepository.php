<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Instance.php');


class tx_caretaker_InstanceRepository {

	private static $instance = null;

	private function __construct (){}	
	
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_InstanceRepository();
		}
		return self::$instance;
	}
	
	function getAll($parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row, $parent);
		}
		return $result;
	}
	
	function getByUid($id, $parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0 AND uid = '.(int)$id);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ( $row){
			return $this->dbrow2instance($row, $parent);
		} else {
			return false; 
		}
	}
	
	function getByInstancegroupUid($id, $parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0 AND instancegroup = '.(int)$id);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row, $parent);
		}
		return $result;
	}
	
	
	function dbrow2instance($row, $parent = false){
		$instance = new tx_caretaker_Instance($row['uid'], $row['title'], $parent, $row['url'], $row['host'] , $row['ip']);
		return $instance; 
	}
	
}

?>