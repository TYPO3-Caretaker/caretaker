<?php 
/*
 * 
 */

require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_Instancegroup.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_Instance.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_Testgroup.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_Test.php');

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
	
	function getAllInstancegroups($parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0'.$hidden);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instancegroup($row, $parent);
		}
		return $result;
	}
	
	public function getInstancegroupByUid($uid, $parent = false,  $show_hidden = FALSE){
		$instanceId = (int)$instanceId;
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0 '.$hidden.' AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2instancegroup($row, $parent);
		} else {
			return false;
		}
	}
	
	public function getInstancegroupsByParentGroupUid($parent_group_uid, $parent,  $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0 '.$hidden.' AND parent_group='.(int)$parent_group_uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instancegroup($row, $parent);
		} 
		return $result;
	}
	
	public function getInstancegroupByChildGroupUid($child_group_uid,  $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('parent_group', 'tx_caretaker_instancegroup', 'deleted=0 '.$hidden.' AND uid='.(int)$child_group_uid);
		$result = array();
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$parent_item = $this->getInstancegroupByUid ($row['parent_group']);
			return $parent_item;
		} 
		return false;
	}

	
	private function dbrow2instancegroup($row, $parent){
		$instance = new tx_caretaker_Instancegroup($row['uid'], $row['title'], $parent, $row['hidden']);
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
	/*
	 * Methods for Instance Access 
	 */
	public function getAllInstances($parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 '.$hidden);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2instance($row, $parent);
		}
		return $result;
	}
	
	public function getInstanceByUid($id, $parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 '.$hidden.' AND uid = '.(int)$id);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ( $row){
			return $this->dbrow2instance($row, $parent);
		} else {
			return false; 
		}
	}
	
	public function getInstancesByInstancegroupUid($id, $parent = false, $show_hidden = FALSE){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_instance_instancegroup_mm', 'uid_foreign = '.(int)$id);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$item = $this->getInstanceByUid($row['uid_local'], $parent, $show_hidden);
			if ($item) $result[] = $item;
		}
		return $result;
	}
	
	
	private function dbrow2instance($row, $parent = false){
		$instance = new tx_caretaker_Instance($row['uid'], $row['title'], $parent, $row['url'], $row['host'] , $row['ip'], $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
	/*
	 * Methods Testgroup Access
	 */
	
	public function getAllTestgroups($parent = false , $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 '.$hidden);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2testgroup($row, $parent);
		}
		return $result;
	}
	
	public function getTestgroupByInstanceUid($instanceId, $parent = false , $show_hidden = FALSE){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign', 'tx_caretaker_instance_testgroup_mm', 'uid_local='.(int)$instanceId);
		$instance_group_ids = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$instance_group_ids[] = $row['uid_foreign'];
		}

		$result = array();
		foreach ($instance_group_ids as $id){
			$item = $this->getTestgroupByUid($id, $parent, $show_hidden);
			if ($item) $result[] = $item;
		}
				
		return $result;
	} 
	
	public function getTestgroupByUid($uid, $parent = false , $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$instanceId = (int)$instanceId;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 '.$hidden.'AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2testgroup($row, $parent);
		} else {
			return false;
		}
		
	}
	
	public function getTestgroupsByParentGroupUid($parent_group_uid, $parent, $show_hidden){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 '.$hidden.' AND parent_group='.(int)$parent_group_uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$result[] = $this->dbrow2testgroup($row, $parent);
		} 
		return $result;
	}

	
	private function dbrow2testgroup($row, $parent){
		$instance = new tx_caretaker_Testgroup($row['uid'], $row['title'], $parent, $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
	/*
	 * Methods for Test Access 
	 */
	
	public function getTestsByGroupId ($group_id, $parent = false, $show_hidden = FALSE){
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_testgroup_test_mm', 'uid_foreign='.(int)$group_id);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$ids[] = $row['uid_local'];
		}
		$tests = array();
		foreach ($ids as $uid){
			$item = $this->getTestByUid($uid,$parent,$show_hidden);
			if ($item){
				$tests[]=$item;
			}
		}
		return $tests;
	}
	
	public function getTestByUid ($uid, $parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_test', 'deleted=0 '.$hidden.' AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2test($row, $parent);
		}
		return false;
	}
	
	private function dbrow2test($row, $parent = false){
		$instance = new tx_caretaker_Test( $row['uid'], $row['title'], $parent, $row['test_service'], $row['test_conf'], $row['test_interval'], $row['test_interval_start_hour'], $row['test_interval_stop_hour'] , $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
}

?>