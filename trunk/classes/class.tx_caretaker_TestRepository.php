<?php 

class tx_caretaker_TestRepository{
	private static $instance = null;

	private function __construct (){}	
	
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_TestRepository();
		}
		return self::$instance;
	}
	
	public function getByGroupId ($group_id){
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_group_test_mm', 'uid_foreign='.(int)$group_id);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$ids[] = $row['uid_local'];
		}
		$tests = array();
		foreach ($ids as $uid){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_test', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row){
				$tests[]=$this->dbrow2instance($row);
			}
		}
		return $tests;
	}
	
	public function getByUid ($uid){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_test', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2instance($row);
		}
		return false;
	}
	
	function dbrow2instance($row){
		$instance = new tx_caretaker_Test( $row['uid'], $row['test_service'], $row['test_conf'], $row['test_interval'] );
		return $instance; 
	}
}
?>