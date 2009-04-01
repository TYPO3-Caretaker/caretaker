<?php 

require_once('class.tx_caretaker_Instancegroup.php');

class tx_caretaker_InstanceGroupRepository {
	
	private static $instance = null;

	private function __construct (){}	
	
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_InstanceGroupRepository();
		}
		return self::$instance;
	}
	
	function getAll($parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0 AND hidden=0');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row, $parent);
		}
		return $result;
	}
	
	public function getByUid($uid, $parent = false){
		$instanceId = (int)$instanceId;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2instance($row, $parent);
		} else {
			return false;
		}
		
	}
	
	public function getByParentGroupUid($parent_group_uid, $parent){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'hidden=0 AND deleted=0 AND parent_group='.(int)$parent_group_uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row, $parent);
		} 
		return $result;
	}

	
	function dbrow2instance($row, $parent){
		$instance = new tx_caretaker_Instancegroup($row['uid'], $row['title'], $parent);
		return $instance; 
	}
	
	
}

?>