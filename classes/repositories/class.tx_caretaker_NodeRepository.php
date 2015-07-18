<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * Repository to handle  the storing and reconstruction of all
 * caretaker-nodes. The whole object <-> database
 * communication happens here.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_NodeRepository {

	/**
	 * Retrieve a specific Node
	 *
	 * @param integer $instancegroupId
	 * @param integer $instanceId
	 * @param integer $testgroupId
	 * @param integer $testId
	 * @param boolean $show_hidden
	 * @return tx_caretaker_AbstractNode
	 */
	public function getNode($instancegroupId = false, $instanceId = false, $testgroupId = false, $testId = false, $show_hidden = false) {

		$instancegroupId = (int)$instancegroupId;
		$instanceId = (int)$instanceId;
		$testgroupId = (int)$testgroupId;
		$testId = (int)$testId;

		if ($instancegroupId > 0) {
			$instancegroup = $this->getInstancegroupByUid($instancegroupId, false, $show_hidden);
			if ($instancegroup) return $instancegroup;
		} else if ($instanceId > 0) {
			$instance = $this->getInstanceByUid($instanceId, false, $show_hidden);
			if ($instance) {
				if ($testgroupId > 0) {
					// find the instance testgroups
					$instance_testgroups = $this->getTestgroupsByInstanceUidRecursive($instance->getUid(), $instance, $show_hidden);
					foreach ($instance_testgroups as $instance_testgroup) {
						if ($instance_testgroup->getUid() == $testgroupId) {
							return $instance_testgroup;
						}
					}
				} else if ($testId > 0) {
					// find find directly assigned tests
					$instance_tests = $this->getTestsByInstanceUid($instance->getUid(), $instance, $show_hidden);
					foreach ($instance_tests as $instance_test) {
						if ($instance_test->getUid() == $testId) {
							return $instance_test;
						}
					}
					// find tests assigned to groups or subgroups
					$instance_testgroups = $this->getTestgroupsByInstanceUidRecursive($instance->getUid(), $instance, $show_hidden);
					foreach ($instance_testgroups as $instance_testgroup) {
						$testgroup_tests = $this->getTestsByGroupUid($instance_testgroup->getUid(), $instance_testgroup, $show_hidden);
						foreach ($testgroup_tests as $testgroup_test) {
							if ($testgroup_test->getUid() == $testId) {
								return $testgroup_test;
							}
						}
					}
				} else {
					return $instance;
				}
			}
		}
		return false;
	}

	/**
	 * Get the Identifier String for a Node
	 *
	 * @param tx_caretaker_AbstractNode $node
	 * @return string
	 */
	public function node2id($node) {
		$id = false;
		switch (get_class($node)) {
			case 'tx_caretaker_InstancegroupNode':
				$id = 'instancegroup_' . $node->getUid();
				break;
			case 'tx_caretaker_InstanceNode':
				$id = 'instance_' . $node->getUid();
				break;
			case 'tx_caretaker_TestgroupNode':
				$instance = $node->getInstance();
				$id = 'instance_' . $instance->getUid() . '_testgroup_' . $node->getUid();
				break;
			case 'tx_caretaker_TestNode':
				$instance = $node->getInstance();
				$id = 'instance_' . $instance->getUid() . '_test_' . $node->getUid();
				break;
			case 'tx_caretaker_RootNode':
				$instance = $node->getInstance();
				$id = 'root';
				break;

		}
		return $id;
	}

	/**
	 * Get the Node Object for a given Identifier String
	 *
	 * @param string $id_string
	 * @param boolean $show_hidden
	 * @return tx_caretaker_AbstractNode
	 */
	public function id2node($id_string, $show_hidden = false) {

		if ($id_string == 'root') return $this->getRootNode();

		$parts = explode('_', $id_string);
		$info = array();
		for ($i = 0; $i < count($parts); $i += 2) {
			switch ($parts[$i]) {
				case 'instancegroup':
					$info['instancegroup'] = (int)$parts[$i + 1];
					break;
				case 'instance':
					$info['instance'] = (int)$parts[$i + 1];
					break;
				case 'testgroup':
					$info['testgroup'] = (int)$parts[$i + 1];
					break;
				case 'test':
					$info['test'] = (int)$parts[$i + 1];
					break;
			}
		}
		return $this->getNode($info['instancegroup'], $info['instance'], $info['testgroup'], $info['test'], $show_hidden);
	}

	/**
	 * Singleton Instance
	 * @var tx_caretaker_NodeRepository
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
	}

	/**
	 * Get Singleton Instance
	 * @return tx_caretaker_NodeRepository
	 */
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new tx_caretaker_NodeRepository();
		}
		return self::$instance;
	}

	/**
	 * Get a Rootnode Object
	 *
	 * @param $show_hidden
	 * @return tx_caretaker_RootNode
	 */
	public function getRootNode() {
		return new tx_caretaker_RootNode();
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
	public function getAllInstancegroups($parent = false, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0' . $hidden);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$item = $this->dbrow2instancegroup($row, $parent);
			if ($item) {
				$result[] = $item;
			}
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
	public function getInstancegroupByUid($uid, $parent = false, $show_hidden = FALSE) {
		$instanceId = (int)$instanceId;
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0 ' . $hidden . ' AND uid=' . (int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row) {
			return $this->dbrow2instancegroup($row, $parent);
		} else {
			return false;
		}
	}

	/**
	 * Get all Instancegroups wich are Children of Instancegroup with UID xxx
	 *
	 * @param integer $parent_group_uid
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return array
	 */
	public function getInstancegroupsByParentGroupUid($parent_group_uid, $parent, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instancegroup', 'deleted=0 ' . $hidden . ' AND parent_group=' . (int)$parent_group_uid, 'title');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$item = $this->dbrow2instancegroup($row, $parent);
			if ($item) {
				$result[] = $item;
			}
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
	public function getInstancegroupByChildGroupUid($child_group_uid, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('parent_group', 'tx_caretaker_instancegroup', 'deleted=0 ' . $hidden . ' AND uid=' . (int)$child_group_uid);
		$result = array();
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$parent_item = $this->getInstancegroupByUid($row['parent_group']);
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
	private function dbrow2instancegroup($row, $parent) {
		// check access
		if (TYPO3_MODE == 'FE') {

			if ($GLOBALS['TSFE']->sys_page) {
				$result = $GLOBALS['TSFE']->sys_page->checkRecord('tx_caretaker_instancegroup', $row['uid']);
			} else {
				// this has to be implemented here
				$result = true;
			}

			if (!$result) {
				return false;
			}
		}

		// find parent node if it was not already handed over
		if ($parent == false) {
			if (intval($row['parent_group']) > 0) {
				$parent = $this->getInstancegroupByUid($row['parent_group'], false);
			} else {
				$parent = $this->getRootNode();
			}
		}

		// create instance
		$instance = new tx_caretaker_InstancegroupNode($row['uid'], $row['title'], $parent, $row['hidden']);
		if ($row['description']) $instance->setDescription($row['description']);
		$instance->setDbRow($row);
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
	public function getAllInstances($parent = false, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 ' . $hidden);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$item = $this->dbrow2instance($row, $parent);
			if ($item) {
				$result[] = $item;
			}
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
	public function getInstanceByUid($uid, $parent = NULL, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_instance', 'deleted=0 ' . $hidden . ' AND uid = ' . (int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row) {
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
	public function getInstancesByInstancegroupUid($uid, $parent = NULL, $show_hidden = FALSE) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_caretaker_instance', 'instancegroup = ' . (int)$uid, '', 'title');
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$item = $this->getInstanceByUid($row['uid'], $parent, $show_hidden);
			if ($item) {
				$result[] = $item;
			}
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
	private function dbrow2instance($row, $parent = NULL) {

		// check access
		if (TYPO3_MODE == 'FE') {
			if ($GLOBALS['TSFE']->sys_page) {
				$result = $GLOBALS['TSFE']->sys_page->checkRecord('tx_caretaker_instance', $row['uid']);
			} else {
				// implement check in eID mode here
				$result = true;
			}
			if (!$result) {
				return false;
			}
		}

		// find parent node if it was not already handed over
		if (!$parent) {
			if (intval($row['instancegroup']) > 0) {
				$parent = $this->getInstancegroupByUid($row['instancegroup'], false);
			} else {
				$parent = $this->getRootNode();
			}
		}
		// create Node
		$instance = new tx_caretaker_InstanceNode($row['uid'], $row['title'], $parent, $row['url'], $row['host'], $row['public_key'], $row['hidden']);
		if ($row['description']) $instance->setDescription($row['description']);
		if ($row['testconfigurations']) $instance->setTestConfigurations($row['testconfigurations']);
		$instance->setDbRow($row);
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
	public function getAllTestgroups($parent = false, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 ' . $hidden);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
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
	public function getTestgroupsByInstanceUid($instanceId, $parent = false, $show_hidden = FALSE) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign', 'tx_caretaker_instance_testgroup_mm', 'uid_local=' . (int)$instanceId, '', 'sorting');
		$instance_group_ids = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$instance_group_ids[] = $row['uid_foreign'];
		}

		$result = array();
		foreach ($instance_group_ids as $id) {
			$item = $this->getTestgroupByUid($id, $parent, $show_hidden);
			if ($item) $result[] = $item;
		}

		return $result;
	}

	/**
	 * Get all Testgroups of Instance X all subgroups are included recursively
	 *
	 * @param <type> $instanceId
	 * @param <type> $parent
	 * @param <type> $show_hidden
	 */
	public function getTestgroupsByInstanceUidRecursive($instanceId, $parent = false, $show_hidden = FALSE) {
		// direct assigned results
		$testgroups = $this->getTestgroupsByInstanceUid($instanceId, $parent, $show_hidden);
		// include subresults
		foreach ($testgroups as $testgroup) {
			$subgroups = $this->getTestgroupsByParentGroupUidRecursive($testgroup->getUid(), $testgroup, $show_hidden);
			$testgroups = array_merge($testgroups, $subgroups);
		}
		return $testgroups;
	}

	/**
	 * Get Testgroup of UID X
	 *
	 * @param integer $uid
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $show_hidden
	 * @return tx_caretaker_TestgroupNode
	 */
	public function getTestgroupByUid($uid, $parent = false, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$instanceId = (int)$instanceId;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 ' . $hidden . 'AND uid=' . (int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row) {
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
	public function getTestgroupsByParentGroupUid($parent_group_uid, $parent, $show_hidden) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_testgroup', 'deleted=0 ' . $hidden . ' AND parent_group=' . (int)$parent_group_uid);
		$result = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$result[] = $this->dbrow2testgroup($row, $parent);
		}
		return $result;
	}


	/**
	 * Get all Testgroups of Instance X all subgroups are included recursively
	 *
	 * @param <type> $groupId
	 * @param <type> $parent
	 * @param <type> $show_hidden
	 */
	public function getTestgroupsByParentGroupUidRecursive($groupId, $parent = false, $show_hidden = FALSE) {
		// direct assigned results
		$testgroups = $this->getTestgroupsByParentGroupUid($groupId, $parent, $show_hidden);
		// include subresults
		foreach ($testgroups as $testgroup) {
			$subgroups = $this->getTestgroupsByParentGroupUidRecursive($testgroup->getUid(), $testgroup, $show_hidden);
			$testgroups = array_merge($testgroups, $subgroups);
		}
		return $testgroups;
	}

	/**
	 * Convert Testgroup DB-Record to Object
	 *
	 * @param array $row
	 * @param tx_caretaker_AbstractNode $parent
	 * @return tx_caretaker_TestgroupNode
	 */
	private function dbrow2testgroup($row, $parent) {
		$instance = new tx_caretaker_TestgroupNode($row['uid'], $row['title'], $parent, $row['hidden']);
		if ($row['description']) $instance->setDescription($row['description']);
		$instance->setDbRow($row);
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
	public function getTestsByGroupUid($group_id, $parent = false, $show_hidden = FALSE) {
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_testgroup_test_mm', 'uid_foreign=' . (int)$group_id, '', 'sorting_foreign');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$ids[] = $row['uid_local'];
		}
		$tests = array();
		foreach ($ids as $uid) {
			$item = $this->getTestByUid($uid, $parent, $show_hidden);
			if ($item) {
				$tests[] = $item;
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
	public function getTestsByInstanceUid($instance_id, $parent = false, $show_hidden = FALSE) {
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_instance_test_mm', 'uid_foreign=' . (int)$instance_id, '', 'sorting_foreign');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$ids[] = $row['uid_local'];
		}
		$tests = array();
		foreach ($ids as $uid) {
			$item = $this->getTestByUid($uid, $parent, $show_hidden);
			if ($item) {
				$tests[] = $item;
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
	public function getTestByUid($uid, $parent = false, $show_hidden = FALSE) {
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_test', 'deleted=0 ' . $hidden . ' AND uid=' . (int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row) {
			$test = $this->dbrow2test($row, $parent);
			// the test may be disabled/hidden by configuration, so we need to double-check the hidden state
			if (!$show_hidden && $test->getHidden()) {
				return false;
			}
			return $test;
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
	private function dbrow2test($row, $parent = false) {

		if (!$parent) {
			return false;
		}

		$test = new tx_caretaker_TestNode($row['uid'], $row['title'], $parent, $row['test_service'], $row['test_conf'], $row['test_interval'], $row['test_retry'], $row['test_due'], $row['test_interval_start_hour'], $row['test_interval_stop_hour'], $row['hidden']);
		if ($row['description']) $test->setDescription($row['description']);
		$test->setDbRow($row);
		return $test;

	}


}

?>
