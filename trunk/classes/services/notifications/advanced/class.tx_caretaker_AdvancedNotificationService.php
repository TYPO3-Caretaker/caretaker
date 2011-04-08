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
 * The Testrunner Output Notification-Service
 *
 * @author Thomas Hempel <thomas@work.de>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_AdvancedNotificationService extends tx_caretaker_AbstractNotificationService {

	/**
	 * Array with list of called exitpoint objects
	 *
	 * @var array
	 */
	protected $exitpoints = array();

	/**
	 * Constructor
	 * reads the service configuration
	 */
	public function __construct() {
		parent::__construct('advanced');
	}

	/**
	 * This is called whenever the notification service is called. We have to store all interesting
	 * results in an internal structure to use it later.
	 *
	 * @param string $event
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification($event, $node, $result = NULL, $lastResult = NULL) {
		$strategies = $node->getStrategies();
		foreach($strategies as $strategy) {
			$config = $this->getStrategyConfig($strategy);
			$this->processStrategy(
				$strategy,
				$config,
				array(
				'event' => $event,
				'node' => $node,
				'result' => $result,
				'lastResult' => $lastResult,
			));

			if ($config['stop']) {
				break;
			}
		}
	}

	/**
	 * finish all used exitpoints
	 */
	public function sendNotifications() {
		foreach($this->exitpoints as $identifier => $exitpoint) {
			$exitpoint->execute();
		}
	}

	/**
	 * @param  $strategy
	 * @param  $notification
	 * @return void
	 */
	protected function processStrategy($strategy, $config, $notification) {
		// echo 'process strategy: ' . $strategy['name'] . chr(10);

		if (count($config['rules.']) === 0 || !$this->doConditionsApply($config['conditions.'], $notification)) {
			return;
		}
		foreach($config['rules.'] as $ruleName => $rule) {
			$this->processRule($ruleName, $rule, $notification);
		}

		// TODO
		// $config['includeStrategy'] to include another strategy
	}

	/**
	 * @param  $ruleName
	 * @param  $rule
	 * @param  $notification
	 * @return void
	 */
	protected function processRule($ruleName, $rule, $notification) {
		$ruleName = rtrim($ruleName, '.');
		// echo 'process rule: ' . $ruleName . chr(10);
		if (count($rule['exit.']) === 0 || !$this->doConditionsApply($rule['conditions.'], $notification)) {
			return;
		}

		foreach($rule['exit.'] as $exitName => $exit) {
			$this->processExitpoint($exitName, $exit, $notification);
		}
	}

	/**
	 * @param  $exitName
	 * @param  $exit
	 * @param  $notification
	 * @return void
	 */
	protected function processExitpoint($exitName, $exit, $notification) {
		$exitName = rtrim($exitName, '.');
		// echo 'process exitpoint: ' . $exitName . chr(10);
		if (!$this->doConditionsApply($exit['conditions.'], $notification)) {
			return;
		}
		$exitpoint = $this->getExitpointByIdentifier($exitName);
		if ($exitpoint instanceof tx_caretaker_NotificationExitPointInterface) {
			$exitpoint->addNotification($notification, $exit);
		}
	}

	/**
	 * @param string $identifier
	 * @return bool|tx_caretaker_NotificationExitPointInterface
	 */
	protected function getExitpointByIdentifier($identifier) {
		if ($this->exitpoints[$identifier] !== NULL) {
			return $this->exitpoints[$identifier];
		}
		$exitpoint = FALSE;
		list($exitpointRecord) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			tx_caretaker_Constants::table_Exitponts,
			'id = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($identifier, '') . ' AND deleted=0 AND hidden=0',
			'',
			'',
			1
		);
		if ($exitpointRecord === NULL) {
			return;
		}
		$info = t3lib_extMgm::findService($exitpointRecord['service'], '*');
		if (is_array($info) && !empty($info['classFile'])) {
			require_once($info['classFile']);
			$exitpoint = t3lib_div::makeInstance($info['className']);
			$config = t3lib_div::xml2array($exitpointRecord['config']);
			if (!is_array($config)) $config = array();
			$exitpoint->init($config);
		}
		$this->exitpoints[$identifier] = $exitpoint;
		return $exitpoint;
	}

	/**
	 * @param  $conditions
	 * @param  $notification
	 * @return bool
	 */
	protected function doConditionsApply($conditions, $notification) {
		if (empty($conditions)) return TRUE;

		foreach($conditions as $key => $configValue) {
			$conditionApply = TRUE;
			switch($key) {
				case 'event':
					$conditionApply = $this->matchConditionValue($configValue, $notification['event']);
					break;

				case 'state':
				case 'newState':
					if (!$notification['result'] instanceof tx_caretaker_TestResult) {
						break;
					}
					$conditionApply = $this->matchConditionValue($configValue, $notification['result']->getStateInfo());
					break;

				case 'previousState':
					if (!$notification['lastResult'] instanceof tx_caretaker_TestResult) {
						break;
					}
					$conditionApply = $this->matchConditionValue($configValue, $notification['lastResult']->getStateInfo());
					break;

				case 'previousDifferingState':
					$diffResult = $notification['node']->getPreviousDifferingResult($notification['result']);
					$conditionApply = $this->matchConditionValue($configValue, $diffResult->getStateInfo());
					break;

				case 'lastStateChangeOlderThen':
					$diffResult = $notification['node']->getPreviousDifferingResult($notification['result']);
					if ($notification['result']->getTimestamp() - $diffResult->getTimestamp() < $configValue) {
						$conditionApply = FALSE;
					}
					break;

				case 'lastStateChangeYoungerThen':
					$diffResult = $notification['node']->getPreviousDifferingResult($notification['result']);
					if ($notification['result']->getTimestamp() - $diffResult->getTimestamp() > $configValue) {
						$conditionApply = FALSE;
					}
					break;

				case 'testServices':
					if ($notification['node'] instanceof tx_caretaker_TestNode
							&& !$this->matchConditionValue($configValue, get_class($notification['node']->getTestService()))) {
						$conditionApply = FALSE;
					}
					break;

				case 'onlyIfStateChanged':
				case 'onlyIfStateChanges':
					if ((bool)$configValue && $notification['result']->getState() === $notification['lastResult']->getState()) {
						$conditionApply = FALSE;
					}
					break;

				case 'stateChanges':
					$allowedChanges = t3lib_div::trimExplode(',', $configValue);
					$conditionApply = FALSE;
					foreach ($allowedChanges as $allowedChange) {
						list($from, $to) = t3lib_div::trimExplode('>', $allowedChange);
						if ($this->matchConditionValue($to, $notification['result']->getStateInfo())
								&& $this->matchConditionValue($from, $notification['lastResult']->getStateInfo())) {
							$conditionApply = TRUE;
							break;
						}
					}
					break;

				case 'schedule.':
					$conditionApply = $this->matchConditionSchedule($configValue);
					break;

				case 'threshold.':
					if ((isset($configValue['min']) && $notification['result']->getValue() < $configValue['min'])
							|| (isset($configValue['max']) && $notification['result']->getValue() > $configValue['max'])) {
						$conditionApply = FALSE;
					}
					break;

				case 'userFunc':
					$conditionApply = TRUE;
					$parameters = array(
						'conditionApply' => &$conditionApply,
						'event' => $notification['event'],
						'node' => $notification['node'],
						'result' => $notification['result'],
						'lastResult' => $notification['lastResult']
					);
					t3lib_div::callUserFunction($configValue, $parameters, $this);
			}

			if (!$conditionApply) {
				// echo 'condition does not apply: ' . $key . chr(10);
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param array $schedule
	 * @return bool
	 */
	protected function matchConditionSchedule($schedule) {
		if (empty($schedule)) {
			return TRUE;
		}

		$weekdays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		$currentHour = intval(date('H'));
		$currentDayOfWeek = date('w');
		if (!empty($schedule[$weekdays[$currentDayOfWeek] . '.'])) {
			$schedule = array_merge($schedule, $schedule[$weekdays[$currentDayOfWeek] . '.']);
		}
		if (isset($schedule['start']) && isset($schedule['end'])) {
			if ($currentHour >= intval($schedule['start']) && $currentHour <= intval($schedule['end'])) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @param string $references comma separated list of allowed values ("ok,error" OR "!undefined")
	 * @param string $value the value to compare
	 * @return bool
	 */
	protected function matchConditionValue($references, $value) {
		$value = strtoupper($value);
		foreach (t3lib_div::trimExplode(',', $references) as $reference) {
			$reference = strtoupper($reference);
			if ($reference === '*' || $reference === 'ALL') return TRUE;
			if ((substr($reference, 0, 1) !== '!' && $reference !== $value)
					|| (substr($reference, 0, 1) === '!' && substr($reference, 1) === $value)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param  $strategy
	 * @return array
	 */
	protected function getStrategyConfig($strategy) {
		if ($this->strategyConfig[$strategy['uid']] === NULL) {
			$parseObj = t3lib_div::makeInstance('t3lib_TSparser');
			$config = t3lib_TSparser::checkIncludeLines($strategy['config']);
			$parseObj->parse($config);
			$config = $parseObj->setup;
			$this->strategyConfig[$strategy['uid']] = $config;
		}
		return $this->strategyConfig[$strategy['uid']];
	}

	/**
	 * @param array $params
	 * @param tx_caretaker_AdvancedNotificationService $pObj
	 * @return void
	 */
	/* * /
	public function testUserFunc($params, $pObj) {
		$params['conditionApply'] = FALSE;
	}
	// */
}

?>
