<?php 
/*
 * 
 */

require_once ('class.tx_caretaker_Instancegroup.php');
require_once ('class.tx_caretaker_Instance.php');
require_once ('class.tx_caretaker_Testgroup.php');
require_once ('class.tx_caretaker_Test.php');

class tx_caretaker_NodeRepository {

	private static $instance = null;
	private function __construct (){}	
	
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_NodeRepository();
		}
		return self::$instance;
	}
	
	/*
	 * Methods for Instancegroup Access
	 */
	
	function getAllInstancegroups($parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0 AND hidden=0');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instancegroup($row, $parent);
		}
		return $result;
	}
	
	public function getInstancegroupByUid($uid, $parent = false){
		$instanceId = (int)$instanceId;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2instancegroup($row, $parent);
		} else {
			return false;
		}
	}
	
	public function getInstancegroupsByParentGroupUid($parent_group_uid, $parent){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'hidden=0 AND deleted=0 AND parent_group='.(int)$parent_group_uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instancegroup($row, $parent);
		} 
		return $result;
	}
	
	public function getInstancegroupByChildGroupUid($child_group_uid){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('parent_group', 'tx_caretaker_instancegroup', 'hidden=0 AND deleted=0 AND uid='.(int)$child_group_uid);
		$result = array();
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$parent_item = $this->getInstancegroupByUid ($row['parent_group']);
			return $parent_item;
		} 
		return false;
	}

	
	private function dbrow2instancegroup($row, $parent){
		$instance = new tx_caretaker_Instancegroup($row['uid'], $row['title'], $parent);
		return $instance; 
	}
	/*
	 * Methods for Instance Access 
	 */
	public function getAllInstances($parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row, $parent);
		}
		return $result;
	}
	
	public function getInstanceByUid($id, $parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 AND hidden=0 AND uid = '.(int)$id);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ( $row){
			return $this->dbrow2instance($row, $parent);
		} else {
			return false; 
		}
	}
	
	public function getInstancesByInstancegroupUid($id, $parent = false){
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_instance_instancegroup_MM', 'uid_foreign = '.(int)$id);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->getInstanceByUid($row['uid_local'], $parent);
		}
		return $result;
	}
	
	
	private function dbrow2instance($row, $parent = false){
		$instance = new tx_caretaker_Instance($row['uid'], $row['title'], $parent, $row['url'], $row['host'] , $row['ip']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		return $instance; 
	}
	
	/*
	 * Methods Testgroup Access
	 */
	
	public function getAllTestgroups($parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 AND hidden=0');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2testgroup($row, $parent);
		}
		return $result;
	}
	
	public function getTestgroupByInstanceUid($instanceId, $parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign', 'tx_caretaker_instance_testgroup_mm', 'uid_local='.(int)$instanceId);
		$instance_group_ids = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$instance_group_ids[] = $row['uid_foreign'];
		}

		$result = array();
		foreach ($instance_group_ids as $id){
			$result[] = $this->getTestgroupByUid($id, $parent);
		}
				
		return $result;
	} 
	
	public function getTestgroupByUid($uid, $parent = false){
		$instanceId = (int)$instanceId;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2testgroup($row, $parent);
		} else {
			return false;
		}
		
	}
	
	public function getTestgroupsByParentGroupUid($parent_group_uid, $parent){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'hidden=0 AND deleted=0 AND parent_group='.(int)$parent_group_uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2testgroup($row, $parent);
		} 
		return $result;
	}

	
	private function dbrow2testgroup($row, $parent){
		$instance = new tx_caretaker_Testgroup($row['uid'], $row['title'], $parent);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		return $instance; 
	}
	
	/*
	 * Methods for Test Access 
	 */
	
	public function getTestsByGroupId ($group_id, $parent = false){
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup_test_mm', 'uid_foreign='.(int)$group_id);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$ids[] = $row['uid_local'];
		}
		$tests = array();
		foreach ($ids as $uid){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_test', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row){
				$tests[]=$this->dbrow2test($row, $parent);
			}
		}
		return $tests;
	}
	
	public function getTestByUid ($uid, $parent = false){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_test', 'hidden=0 AND deleted=0 AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2test($row, $parent);
		}
		return false;
	}
	
	private function dbrow2test($row, $parent = false){
		$instance = new tx_caretaker_Test( $row['uid'], $row['title'], $parent, $row['test_service'], $row['test_conf'], $row['test_interval'] );
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		return $instance; 
	}
}

?>