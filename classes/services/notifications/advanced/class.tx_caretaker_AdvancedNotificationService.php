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
	 * @var array
	 */
	protected $defaultConditions = array(
			'event' => 'updatedTestResult'
	);

	/**
	 * @var array
	 */
	protected $strategyConfig;

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
		foreach ($strategies as $strategy) {
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
		/** @var tx_caretaker_NotificationBaseExitPoint $exitpoint */
		foreach ($this->exitpoints as $exitpoint) {
			if ($exitpoint) {
				$exitpoint->execute();
			}
		}
	}

	/**
	 * @param array $strategy
	 * @param array $config
	 * @param array $notification
	 */
	protected function processStrategy($strategy, $config, $notification) {

		$conditions = $this->defaultConditions;
		\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($conditions, $config['conditions.']);
		if (count($config['rules.']) === 0 || !$this->doConditionsApply($conditions, $notification)) {
			return;
		}
		foreach ($config['rules.'] as $ruleName => $rule) {
			$this->processRule($ruleName, $rule, $notification);
		}
	}

	/**
	 * @param string $ruleName
	 * @param array $rule
	 * @param array $notification
	 * @return void
	 */
	protected function processRule($ruleName, $rule, $notification) {
		if (count($rule['exit.']) === 0 || !$this->doConditionsApply($rule['conditions.'], $notification)) {
			return;
		}

		foreach ($rule['exit.'] as $exitName => $exit) {
			$this->processExitpoint($exitName, $exit, $notification);
		}
	}

	/**
	 * @param string $exitName
	 * @param array $exit
	 * @param array $notification
	 * @return void
	 */
	protected function processExitpoint($exitName, $exit, $notification) {
		$exitName = rtrim($exitName, '.');
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
			return false;
		}
		$info = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::findService('caretaker_exitpoint', $exitpointRecord['service']);
		if (is_array($info) && !empty($info['className'])) {
			$exitpoint = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($info['className']);
			$config = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($exitpointRecord['config']);
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

		/** @var tx_caretaker_AbstractNode $node */
		$node = $notification['node'];
		/** @var tx_caretaker_TestResult $result */
		$result = $notification['result'];
		/** @var tx_caretaker_TestResult $lastResult */
		$lastResult = $notification['lastResult'];

		foreach ($conditions as $key => $configValue) {
			$conditionApply = TRUE;
			switch ($key) {
				case 'event':
					$conditionApply = $this->matchConditionValue($configValue, $notification['event']);
					break;

				case 'state':
				case 'newState':
					if (!$result instanceof tx_caretaker_TestResult) {
						break;
					}
					$conditionApply = $this->matchConditionValue($configValue, $result->getStateInfo());
					break;

				case 'previousState':
					if (!$notification['lastResult'] instanceof tx_caretaker_TestResult) {
						break;
					}
					$conditionApply = $this->matchConditionValue($configValue, $notification['lastResult']->getStateInfo());
					break;

				case 'previousDifferingState':
					$diffResult = $node->getPreviousDifferingResult($result);
					$conditionApply = $this->matchConditionValue($configValue, $diffResult->getStateInfo());
					break;

				case 'lastStateChangeOlderThen':
					$diffResult = $node->getPreviousDifferingResult($result);
					if ($result->getTimestamp() - $diffResult->getTimestamp() < $configValue) {
						$conditionApply = FALSE;
					}
					break;

				case 'lastStateChangeYoungerThen':
					$diffResult = $node->getPreviousDifferingResult($result);
					if ($result->getTimestamp() - $diffResult->getTimestamp() > $configValue) {
						$conditionApply = FALSE;
					}
					break;

				case 'testServices':
					/** @var tx_caretaker_TestNode $node */
					if ($node instanceof tx_caretaker_TestNode
							&& !$this->matchConditionValue($configValue, get_class($node->getTestService()))
					) {
						$conditionApply = FALSE;
					}
					break;

				case 'onlyIfStateChanged':
				case 'onlyIfStateChanges':
					if ((bool)$configValue && $result->getState() === $lastResult->getState()) {
						$conditionApply = FALSE;
					}
					break;

				case 'stateChanges':
					$allowedChanges = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $configValue);
					$conditionApply = FALSE;
					foreach ($allowedChanges as $allowedChange) {
						list($from, $to) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('>', $allowedChange);
						if ($this->matchConditionValue($to, $result->getStateInfo())
								&& $this->matchConditionValue($from, $lastResult->getStateInfo())
						) {
							$conditionApply = TRUE;
							break;
						}
					}
					break;

				case 'schedule.':
					$conditionApply = $this->matchConditionSchedule($conditions['schedule'], $configValue);
					break;

				case 'threshold.':
					if ((isset($configValue['min']) && $result->getValue() < $configValue['min'])
							|| (isset($configValue['max']) && $result->getValue() > $configValue['max'])
					) {
						$conditionApply = FALSE;
					}
					break;

				case 'infoRegexp':
					$conditionApply = preg_match($configValue, $result->getLocallizedInfotext());
					break;

				case 'not.':
					$conditionApply = !$this->doConditionsApply($configValue, $notification);
					break;

				case 'userFunc':
					$conditionApply = TRUE;
					$parameters = array(
							'conditionApply' => &$conditionApply,
							'event' => $notification['event'],
							'node' => $node,
							'result' => $result,
							'lastResult' => $lastResult
					);
					\TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($configValue, $parameters, $this);
			}
			if (!$conditionApply) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param array $schedule
	 * @return bool
	 */
	protected function matchConditionSchedule($schedule, $scheduleSub) {
		if (empty($schedule) && empty($scheduleSub)) {
			return TRUE;
		}

		$weekdays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		$currentHour = intval(date('H'));
		$currentDayOfWeek = date('w');

		// schedule = 8-18
		if (!empty($schedule) && strpos($schedule, '-') !== FALSE) {
			list($start, $stop) = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode('-', $schedule, FALSE, 2);
		}
		// schedule.start = 8
		// schedule.end = 18
		if (isset($scheduleSub['start'])) $start = $scheduleSub['start'];
		if (isset($scheduleSub['end'])) $stop = $scheduleSub['end'];

		// schedule.monday = 8-18
		if (!empty($scheduleSub[$weekdays[$currentDayOfWeek]]) && strpos($scheduleSub[$weekdays[$currentDayOfWeek]], '-') !== FALSE) {
			list($start, $stop) = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode('-', $scheduleSub[$weekdays[$currentDayOfWeek]], FALSE, 2);
		}

		// schedule.monday.start = 8
		// schedule.monday.end = 18
		if (isset($scheduleSub[$weekdays[$currentDayOfWeek] . '.']['start'])) $start = $scheduleSub[$weekdays[$currentDayOfWeek] . '.']['start'];
		if (isset($scheduleSub[$weekdays[$currentDayOfWeek] . '.']['end'])) $stop = $scheduleSub[$weekdays[$currentDayOfWeek] . '.']['end'];

		if (isset($start) && isset($stop)) {
			if ($currentHour >= intval($start) && $currentHour <= intval($stop)) {
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
		foreach (\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $references) as $reference) {
			$reference = strtoupper($reference);
			if ($reference === '*' || $reference === 'ALL') return TRUE;
			if ((substr($reference, 0, 1) !== '!' && $reference !== $value)
					|| (substr($reference, 0, 1) === '!' && substr($reference, 1) === $value)
			) {
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
			$parseObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser');
			$config = \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser::checkIncludeLines($strategy['config']);
			$parseObj->parse($config);
			$config = $parseObj->setup;
			$this->strategyConfig[$strategy['uid']] = $config;
		}
		return $this->strategyConfig[$strategy['uid']];
	}
}
