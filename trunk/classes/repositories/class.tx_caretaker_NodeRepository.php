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

require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_InstancegroupNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_InstanceNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_TestgroupNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_TestNode.php');

class tx_caretaker_NodeRepository {

	/**
	 * Singleton Instance
	 * @var tx_caretaker_NodeRepository
	 */
	private static $instance = null;
	
	/**
	 * Constructor
	 */
	private function __construct (){}	

	/**
	 * Get Singleton Instance 
	 * @return tx_caretaker_NodeRepository
	 */
	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_NodeRepository();
		}
		return self::$instance;
	}
	
	/*
	 * Methods for Instancegroup Access
	 */
	
	/**
	 * Get all Instancegroups
	 * 
	 * @param tx_caretaker_AbstractNode $parent 
	 * @param boolean $show_hidden
	 * @return array
	 */
	public function getAllInstancegroups($parent = false, $show_hidden = FALSE){
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
	
	/**
	 * Get the instancegroup with UID
	 * 
	 * @param $uid
	 * @param $parent
	 * @param $show_hidden
	 * @return unknown_type
	 */
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
	
	/**
	 * Get all Instancegroups wich are Children of Instancegroup with UID xxx
	 * 
	 * @param integer $parent_group_uid
	 * @param tx_caretaker_Instancgroup $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
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
	
	/**
	 * Get the Instancegroup wich is the prarent of Instancegroup X
	 * 
	 * @param integer $child_group_uid
	 * @param boolean $show_hidden
	 * @return tx_caretaker_InstancegroupNode
	 */
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

	/**
	 * Convert Instancegroup DB-Row to Instancegroup-Object
	 * 
	 * @param array $row
	 * @param tx_caretaker_AbstractNode $parent
	 * @return tx_caretaker_InstancegroupNode
	 */
	private function dbrow2instancegroup($row, $parent){
		$instance = new tx_caretaker_InstancegroupNode($row['uid'], $row['title'], $parent, $row['hidden']);
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
	/*
	 * Methods for Instance Access 
	 */
	
	/**
	 * Get all Instances in Repository
	 * 
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
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
	
	/**
	 * Get Instance with UID X
	 * 
	 * @param integer $uid
	 * @param $parent
	 * @param $show_hidden
	 * @return unknown_type
	 */
	public function getInstanceByUid($uid, $parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 '.$hidden.' AND uid = '.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ( $row){
			return $this->dbrow2instance($row, $parent);
		} else {
			return false; 
		}
	}
	
	/**
	 * Get all Instances wich are part of Group X
	 * 
	 * @param integer $uid
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
	public function getInstancesByInstancegroupUid($uid, $parent = false, $show_hidden = FALSE){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_caretaker_instance', 'instancegroup = '.(int)$uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$item = $this->getInstanceByUid($row['uid'], $parent, $show_hidden);
			if ($item) $result[] = $item;
		}
		return $result;
	}
	
	/**
	 * Convert DB-Row to Instance-Object
	 * 
	 * @param array $row
	 * @param tx_caretaker_AbstractNode $parent
	 * @return tx_caretaker_InstanceNode
	 */
	private function dbrow2instance($row, $parent = false){
		$instance = new tx_caretaker_InstanceNode($row['uid'], $row['title'], $parent, $row['url'], $row['host'] , $row['ip'], $row['public_key'], $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
	/*
	 * Methods for Testgroup Access
	 */
	
	/**
	 * Get all Testgroups
	 * 
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return tx_caretaker_TestgroupNode
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
	
	/**
	 * Get all Testgroups of Instance X
	 * 
	 * @param integer $instanceId
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
	public function getTestgroupsByInstanceUid($instanceId, $parent = false , $show_hidden = FALSE){
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
	
	/**
	 * Get Testgroup of UID X
	 * 
	 * @param integer $uid
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return tx_caretaker_TestgroupNode
	 */
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
	
	/**
	 * Get all Testgroups wich are child of Testgroup X
	 * 
	 * @param integer $parent_group_uid
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
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

	/**
	 * Convert Testgroup DB-Record to Object 
	 * 
	 * @param array $row
	 * @param tx_caretaker_AbstractNode $parent
	 * @return tx_caretaker_TestgroupNode
	 */
	private function dbrow2testgroup($row, $parent){
		$instance = new tx_caretaker_TestgroupNode($row['uid'], $row['title'], $parent, $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
	/*
	 * Methods for Test Access 
	 */
	
	/**
	 * Get Tests of Group X
	 * 
	 * @param integer $group_id
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
	public function getTestsByGroupUid ($group_id, $parent = false, $show_hidden = FALSE){
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
	
	/**
	 * Get Tests of Instance X
	 * 
	 * @param integer $instance_id
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
	public function getTestsByInstanceUid ($instance_id, $parent = false, $show_hidden = FALSE){
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_instance_test_mm', 'uid_foreign='.(int)$instance_id);
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
	
	/**
	 * Get Test of UID X
	 * 
	 * @param integer $uid
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return tx_caretaker_TestNode
	 */
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
	
	/**
	 * Convert Test DB-Row to Object
	 * 
	 * @param array $row
	 * @param tx_caretaker_AbstractNode $parent
	 * @return tx_caretaker_TestNode
	 */
	private function dbrow2test($row, $parent = false){
		$instance = new tx_caretaker_TestNode( $row['uid'], $row['title'], $parent, $row['test_service'], $row['test_conf'], $row['test_interval'], $row['test_interval_start_hour'], $row['test_interval_stop_hour'] , $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
}

?>