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
 * Plugin 'Overview' for the 'user_overview' extension.
 */
class tx_caretaker_pi_abstract extends tx_caretaker_pibase {

	var $prefixId = 'tx_caretaker_pi_abstract';        // Same as class name
	var $scriptRelPath = 'pi_abstract/class.tx_caretaker_pi_abstract.php';    // Path to this script relative to the extension dir.
	var $extKey = 'caretaker';    // The extension key.

	/**
	 * GetPlugin Content
	 *
	 * @return string
	 */
	function getContent() {

		$node = $this->getNode();

		if ($node) {

			// getData
			$data = $this->getNodeStatusData($node);

			// read template
			$template = $this->cObj->cObjGetSingle($this->conf['template'], $this->conf['template.']);

			$renderData = $data['nodeInfo'];

			// substitute subparts
			foreach (array('error', 'warning', 'ack') as $key) {
				$subpartMark = '###CARETAKER-CHILDREN-' . strtoupper($key) . '###';
				if (count($data['testResults'][$key]) == 0) {
					$template = $this->cObj->substituteSubpart($template, $subpartMark, '');
				} else {
					$partTemplate = $this->cObj->getSubpart($template, $subpartMark);
					$childTemplate = $this->cObj->getSubpart($partTemplate, '###CARETAKER-CHILD###');
					$renderedChildren = $this->renderNodeList($data['testResults'][$key], $childTemplate);
					$partTemplate = $this->cObj->substituteSubpart($partTemplate, '###CARETAKER-CHILD###', $renderedChildren);
					$template = $this->cObj->substituteSubpart($template, $subpartMark, $partTemplate);
				}
			}

			$lcObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
			$lcObj->start($renderData);


			// substitute markers
			if ($this->conf['markers.']) {
				$markers = arary();
				foreach (array_keys($this->conf['markers.']) as $key) {
					if (substr($key, -1) != '.') {
						$mark = $lcObj->cObjGetSingle($this->conf['markers.'][$key], $this->conf['markers.'][$key . '.']);
						$markers['###' . $key . '###'] = $mark;
					}
				}
				$template = $this->cObj->substituteMarkerArray($template, $markers);
			}

			$content = $template;

		} else {
			$content = 'no node found';
		}
		return $content;
	}

	/**
	 * Render a list of node data
	 *
	 * @param array $nodeDataList See getNodeStatusData
	 * @param string $template
	 * @return string
	 */
	function renderNodeList($nodeDataList, $template) {
		$renderedNodelist = '';
		if ($nodeDataList && is_array($nodeDataList)) {
			foreach ($nodeDataList as $nodeData) {
				$lcObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
				$lcObj->start($nodeData);
				$node_markers = array();
				if ($this->conf['childMarkers.']) {
					foreach (array_keys($this->conf['childMarkers.']) as $key) {
						if (substr($key, -1) != '.') {
							$mark = $lcObj->cObjGetSingle($this->conf['childMarkers.'][$key], $this->conf['childMarkers.'][$key . '.']);
							$node_markers['###' . $key . '###'] = $mark;
						}
					}
					$renderedNodelist .= $this->cObj->substituteMarkerArray($template, $node_markers);
				}
			}
		}

		return $renderedNodelist;
	}

	/**
	 * Get the node which is configured in the pi-flexform
	 *
	 * @return tx_caretaker_AbstractNode
	 */
	function getNode() {
		$this->pi_initPIflexForm();
		$node_id = $this->pi_getFFValue($this->cObj->data['pi_flexform'], 'node_id');
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$node = $node_repository->id2node($node_id);
		return $node;
	}

	/**
	 * Get the status of all test nodes and some extra informations
	 *
	 * @param tx_caretaker_AbstractNode $node
	 * @return array Associative Array with the keys 'nodeInfo' and 'testResults'
	 */
	function getNodeStatusData($node) {
		if (is_a($node, 'tx_caretaker_AggregatorNode')) {
			$testChildNodes = $node->getTestNodes();
		} else if (is_a($node, 'tx_caretaker_TestNode')) {
			$testChildNodes = array($node);
		} else {
			$testChildNodes = array();
		}

		$nodesErrors = array();
		$nodesWarnings = array();
		$nodesAck = array();
		$nodesDue = array();
		$nodesOk = array();
		$nodesUndefined = array();

		$worst_state = tx_caretaker_Constants::state_ok;
		$worst_state_info = '';

		$num_error = 0;
		$num_warning = 0;
		$num_ok = 0;
		$num_undefined = 0;
		$num_ack = 0;
		$num_due = 0;

		/** @var tx_caretaker_TestNode $testNode */
		foreach ($testChildNodes as $testNode) {

			$testResult = $testNode->getTestResult();
			$testNodeState = $testResult->getState();

			// worst state
			if ($testNodeState > $worst_state) {
				$worst_state = $testNodeState;
				$worst_state_info = $testResult->getStateInfo();
			}

			// aggreate infos about nodes and errors
			$instance = $testNode->getInstance();
			$nodeInfo = Array(
					'title' => $instance->getTitle() . ' ' . $testNode->getTitle(),
					'node_title' => $testNode->getTitle(),
					'instance_title' => $instance->getTitle(),
					'node_id' => $testNode->getCaretakerNodeId(),
					'link_parameters' => '&tx_caretaker_pi_singleview[id]=' . $testNode->getCaretakerNodeId(),

					'timestamp' => $testResult->getTimestamp(),
					'stateinfo' => $testResult->getStateInfo(),
					'stateinfo_ll' => $testResult->getLocallizedStateInfo(),
					'message' => $testResult->getMessage(),
					'message_ll' => $testResult->getLocallizedInfotext(),
					'state' => $testResult->getState(),
			);

			// save info
			switch ($testNodeState) {
				case tx_caretaker_Constants::state_error:
					$nodesErrors[] = $nodeInfo;
					$num_error++;
					break;
				case tx_caretaker_Constants::state_warning:
					$nodesWarnings[] = $nodeInfo;
					$num_warning++;
					break;
				case tx_caretaker_Constants::state_ack:
					$nodesAck[] = $nodeInfo;
					$num_ack++;
					break;
				case tx_caretaker_Constants::state_due:
					$nodesDue[] = $nodeInfo;
					$num_due++;
					break;
				case tx_caretaker_Constants::state_ok:
					$nodesOk[] = $nodeInfo;
					$num_ok++;
					break;
				case tx_caretaker_Constants::state_undefined:
					$nodesUndefined[] = $nodeInfo;
					$num_undefined++;
					break;
			}
		}

		$data = array(
				'nodeInfo' => array(
						'numError' => $num_error,
						'numWarning' => $num_warning,
						'numUndefined' => $num_undefined,
						'numDue' => $num_due,
						'numAck' => $num_ack,
						'numOk' => $num_ok,
						'nodeTitle' => $node->getTitle(),
						'state' => $worst_state,
						'stateInfo' => $worst_state_info
				),
				'testResults' => array(
						'error' => $nodesErrors,
						'warning' => $nodesWarnings,
						'ack' => $nodesAck,
						'due' => $nodesDue
				)
		);

		return $data;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_abstract/class.tx_caretaker_pi_abstract.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_abstract/class.tx_caretaker_pi_abstract.php']);
}

