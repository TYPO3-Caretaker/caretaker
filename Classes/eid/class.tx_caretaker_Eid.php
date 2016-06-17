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
 * Eid Object reading status informations in the frontend
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_Eid {

	/**
	 *
	 */
	public function __construct() {
		\TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
	}

	/**
	 * @return tx_caretaker_AggregatorNode
	 */
	private function getRequestedNode($nodeId) {
		$node = false;
		if ($nodeId) {
			$node_repository = tx_caretaker_NodeRepository::getInstance();
			$node = $node_repository->id2node($nodeId, false);
		}
		return $node;
	}

	/**
	 * @param mixed $data
	 * @param string $format
	 */
	private function sendResultData($data, $format) {
		switch ($format) {
			case 'xml':
			case 'application/xml':
				header('Cache-Control: no-cache, must-revalidate');
				header('Content-type: text/xml; charset=UTF-8');
				echo '<xml>' . $this->formatResultDataXml($data) . '</xml>';
				break;
			case 'json':
			case 'application/json':
			default:
				header('Cache-Control: no-cache, must-revalidate');
				header('Content-type: application/json; charset=UTF-8');
				echo $this->formatResultDataJson($data);
				break;
		}
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	private function formatResultDataXml($data) {
		switch (gettype($data)) {
			case 'array':
				$result = '';
				foreach ($data as $key => $value) {
					if (is_int($key)) {
						$result .= '<item index="' . $key . '">' . $this->formatResultDataXml($value) . '</item>';
					} else {
						$result .= '<' . $key . '>' . $this->formatResultDataXml($value) . '</' . $key . '>';
					}
				}
				return $result;
				break;

			case 'boolean':
				if ($data) {
					return 'true';
				} else {
					return 'false';
				}
				break;

			case 'string':
				return '<![CDATA[' . $data . ']]>';
				break;

			default:
				if ($data) {
					return $data;
				} else {
					return '';
				}
				break;
		}
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	private function formatResultDataJson($data) {
		return json_encode($data);
	}

	/**
	 * @return mixed
	 */
	public function getEidFormat() {
		$format = $_SERVER['HTTP_ACCEPT'];
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('format')) {
			$format = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('format');
		}
		return $format;
	}

	/**
	 * Check for valid API key
	 * Ugly temporary solution. Only check if provided API key exists in users table
	 *
	 * @return bool
	 */
	private function validApiKey()
	{
		$apiKey = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('apiKey');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'fe_users',
			'tx_caretaker_api_key = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($apiKey, 'fe_users'),
			'',
			'',
			1
		);

		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		try {
			if (empty($row) || empty($row['uid'])) {
				return false;
			}
		} finally {
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function getEidData() {

		if (!$this->validApiKey()) {
			return array('success' => false);
		}

		$nodeId = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
		$node = $this->getRequestedNode($nodeId);

		if (!$node) {
			return array('success' => false, 'id' => $nodeId);
		}

		$result = array(
				'success' => true,
				'id' => $nodeId
		);

		// add node infos
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('addNode') == 1) {
			$result['node'] = array(
					'id' => $node->getCaretakerNodeId(),
					'title' => $node->getTitle(),
					'type' => $node->getType(),
					'description' => $node->getDescription()
			);
		}

		// add result infos
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('addResult') == 1) {
			$nodeResult = $node->getTestResult();
			$result['result'] = array(
					'state' => $nodeResult->getState(),
					'info' => $nodeResult->getLocallizedStateInfo(),
					'message' => $nodeResult->getLocallizedInfotext(),
					'timestamp' => $nodeResult->getTimestamp()
			);
		}

		// add child infos
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('addChildren') == 1) {
			$result['children'] = false;
			$children = $node->getChildren();
			if ($children and count($children) > 0) {
				/** @var tx_caretaker_AbstractNode $child */
				foreach ($children as $child) {
					$result['children'][] = $child->getCaretakerNodeId();
				}
			}
		}

		// add statistic infos
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('addTestStatistics') == 1) {
			$result['statistics']['count'] = array(
					'error' => 0,
					'warning' => 0,
					'ok' => 0,
					'undefined' => 0,
					'ack' => 0,
					'due' => 0
			);
			$result['statistics']['ids'] = array(
					'error' => array(),
					'warning' => array(),
					'ok' => array(),
					'undefined' => array(),
					'ack' => array(),
					'due' => array(),
			);

			$tests = $node->getTestNodes();
			if ($tests && count($tests)) {
				/** @var tx_caretaker_TestNode $test */
				foreach ($tests as $test) {
					$testResult = $test->getTestResult();
					switch ($testResult->getState()) {
						case tx_caretaker_Constants::state_error:
							$result['statistics']['ids']['error'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['error']++;
							break;
						case tx_caretaker_Constants::state_warning:
							$result['statistics']['ids']['warning'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['warning']++;
							break;
						case tx_caretaker_Constants::state_ok:
							$result['statistics']['ids']['ok'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['ok']++;
							break;
						case tx_caretaker_Constants::state_undefined:
							$result['statistics']['ids']['undefined'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['undefined']++;
							break;
						case tx_caretaker_Constants::state_ack:
							$result['statistics']['ids']['ack'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['ack']++;
							break;
						case tx_caretaker_Constants::state_due:
							$result['statistics']['id']['due'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['due']++;
							break;
					}
				}
			}
		}

		return $result;
	}

	/**
	 *
	 */
	public function processEidRequest() {
		$data = $this->getEidData();
		$format = $this->getEidFormat();
		$this->sendResultData($data, $format);
	}
}

if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('eID')
		&& \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('eID') == 'tx_caretaker') {
	$SOBE = new tx_caretaker_Eid();
	$SOBE->processEidRequest();
	exit;
}