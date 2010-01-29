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
	 * Array with list of called exitpoint objects
	 * 
	 * @var array
	 */
	private $exitPoints = array();

	/**
	 * Internal data structure to hold role records
	 *
	 * @var array
	 */
	private $roleCache = array();

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
					$this->notifications[$nodeParentType][$nodeParent->getUid()]['tests'][$node->getInstance()->getUid()][$node->getUid()] = $nodeId;
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
		$constantReflectionClass = new ReflectionClass('tx_caretaker_Constants');

		/*
		foreach ($this->notifications as $nodeType => $nodeList) {
			foreach ($nodeList as $nodeData) {
				$strategies = $nodeData['node']->getStrategies();
				$contacts = $nodeData['node']->findContacts();

				foreach ($strategies as $strategyRow) {
					if (empty($strategyRow['config'])) continue;

					$results = array();

					$parser->parse($strategyRow['config']);
					$triggers = $parser->setup['reactOn.'];

					foreach ($triggers as $triggerKey => $triggerSetup) {
						$trigger = strtolower(substr($triggerKey, 0, -1));
						$resultCount = $nodeData['result']->getNumGENERIC($trigger);

						$triggerState = $constantReflectionClass->getConstant('state_'.$trigger);

							// TODO: take message interval into account

						foreach ($triggerSetup['ruleSets.'] as $ruleSet) {
							$this->processRuleSet($ruleSet, $resultCount, $contacts, $nodeData, $triggerState);
						}

					}

					// TODO: process strategy
					// $this->callExitPoint('log', $resultData, $contacts);
				}
			}
		}
		*/
	}

	/**
	 * Processes a given ruleSet. It tries to do as less as possible to keep it as cheap as possible.
	 * To achive that it figures out which roles a configured at all. If the roles should be contacted
	 * at this time, if they are connected to the node etc.
	 * Fetching the specific result lists for each role and state is pretty expensive and so we try to
	 * avoid that.
	 *
	 * @TODO: A LOT OF THINGS
	 *
	 * @param array		$ruleSet
	 * @param integer	$resultCount
	 * @param array		$contacts
	 * @param array		$data
	 * @param integer	$state
	 * @return boolean
	 */
	private function processRuleSet($ruleSet, $resultCount, $contacts, $data, $state) {
			// get the roles we are interested in
		$roleNames = array_keys($ruleSet['notify.']);
		$roles = array();
		foreach ($roleNames as $roleNameWithDot) {
			$roleName = substr($roleNameWithDot, 0, -1);

				// check if the role is defined in the database
			$dbRole = $this->getRole($roleName);
			if ($dbRole === false) continue;

			$roles[$roleName] = $dbRole;
		}

		// var_dump($roles, $contacts);

			// if no roles found, we have nothing to do
		if (count($roles) == 0) return true;

		$roleNames = array_keys($roles);

			// process threshold if set
		$threshold = $ruleSet['threshold.'];
		if (isset($threshold)) {

			// var_dump($data['tests']);

				// get the last max+1 results
			// var_dump($GLOBALS['TYPO3_DB']->SELECTquery('*', tx_caretaker_Constants::table_Aggregatorresults, 'aggregator_uid='.$data['node']->getUid().' AND result_status='.$state, '', 'tstamp DESC', intval($threshold['max'])+1));
			// var_dump($state);

		}

		// var_dump($ruleSet, $resultCount);

	}

	/**
	 * Searches and returns a role record for a given name.
	 * It looks up the role in an internal cache and if it doesn't find it there, it will look up
	 * in the database.
	 * If neither the database nor the cache contains any matching role, it returns false.
	 *
	 * @param sting $roleName
	 * @return mixed (array or false)
	 */
	private function getRole($roleName) {
		if (!isset($this->roleCache[$roleName])) {
			$dbRole = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', tx_caretaker_Constants::table_Roles, 'id=\''.mysql_real_escape_string($roleName).'\' AND deleted=0 AND hidden=0', '', '', 1);
			if (count($dbRole) == 0) $dbRole[0] = false;
			$this->roleCache[$roleName] = $dbRole[0];
		}
	
		return $this->roleCache[$roleName];
	}

	/**
	 * Finds and initializes an exitpoint and calls it's process method.
	 *
	 * @param string	$epName: The unique name of the exitpoint record
	 * @param array		$resultData: result set for the node
	 * @param array		$contacts: A list of all contacts
	 *
	 * @return boolean
	 */
	protected function callExitPoint($epName, $resultData, $contacts = null) {
			// search exitPoint record and intantiate it
		if (!isset($this->exitPoints[$epName])) {
			$epRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', tx_caretaker_Constants::table_Exitponts, 'id=\''.mysql_real_escape_string($epName).'\'', '', '', 1);
			if (count($epRow) == 0) return false;

			$epTempObj = t3lib_div::makeInstance($epRow[0]['service'].'ExitPoint');
			if (!$epTempObj) return false;

			$epTempObj->init(t3lib_div::xml2array($epRow[0]['config']));
			
			$this->exitPoints[$epName] = $epTempObj;
		}

		$epObject = $this->exitPoints[$epName];


		// $epObject->execute($resultData);
	}
}
?>
