<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
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
 * The Testrunner Output Notification-Service
 *
 * @author Thomas Hempel <thomas@work.de>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_NotificationService implements tx_caretaker_NotificationServiceInterface  {
	/**
	 * Service is enabled or not
	 *
	 * @var boolean
	 */
	private $enabled = false;

	/**
	 * Internal data structure to collect all notofications
	 * 
	 * @var array
	 */
	private $notifications = array();

	/**
	 * Constructor
	 * reads the service configuration
	 */
	public function __construct (){
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);

		$this->enabled = (bool)$confArray['notifications.']['escalating.']['enabled'];
	}

	/**
	 * Check weather the notificationService is enabled
	 *
	 * @return boolean
	 */
	public function isEnabled(){
		return $this->enabled;
	}

    /**
	 * This is called whenever the notfication service is called. We have to store all interesting
	 * results in an internal structure to use it later.
	 *
	 * @param string $event
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification ( $event, $node, $result = NULL, $lastResult = NULL ){
		if ($result == null) return true;
		
		$nodeType = $node->getType();
		$nodeId = $node->getCaretakerNodeId();

		switch ($nodeType) {
				// store the result for instances and instance groups
			case tx_caretaker_Constants::nodeType_Instance:
			case tx_caretaker_Constants::nodeType_Instancegroup:
				$this->notifications[$nodeType][$node->getUid()]['result'] = $result;
				$this->notifications[$nodeType][$node->getUid()]['node'] = $node;
				break;

				// add nodeId to testlist of all higher entities as long as they are instances or instancegroups
			case tx_caretaker_Constants::nodeType_Test:
				$nodeParent = $node;
				while ($nodeParent && $nodeParent = $nodeParent->getParent()) {
					$nodeParentType = $nodeParent->getType();
					if ($nodeParentType != tx_caretaker_Constants::nodeType_Instance && $nodeParentType != tx_caretaker_Constants::nodeType_Instancegroup) continue;
					$this->notifications[$nodeParentType][$nodeParent->getUid()]['tests'][$nodeId] = $nodeId;
				}
				
				break;
		}
	}

	/**
	 * This is the main method for sending the notifications.
	 * All general information has been collected at this point. We have the aggregated results for each instance and instancegroup with all
	 * tests that where executed for that specific instance.
	 *
	 * nothing happens here since all Informations are already sent to cli
	 */
	public function sendNotifications() {
		$parser = t3lib_div::makeInstance('t3lib_TSparser');

		foreach ($this->notifications as $nodeType => $nodeList) {
			foreach ($nodeList as $nodeData) {
				
				$strategyCount = intval($nodeData['node']->getProperty('notification_strategies'));
				if ($strategyCount <= 0) continue;

					// select strategies from database
				$strategies = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					's.*',
					tx_caretaker_Constants::table_Strategies.' s,'.tx_caretaker_Constants::relationTable_Node2Strategy.' rel',
					'rel.uid_node='.$nodeData['node']->getUid().' AND rel.node_table=\''.$nodeData['node']->getStorageTable().'\' AND rel.uid_strategy=s.uid');

				$contacts = $nodeData['node']->findContacts();

				foreach ($strategies as $strategyRow) {
					if (empty($strategyRow['config'])) continue;

					$parser->parse($strategyRow['config']);
					$strategy = $parser->setup['behaviors.'];


					// TODO: process strategy
				}
			}
		}
	}
}
?>
