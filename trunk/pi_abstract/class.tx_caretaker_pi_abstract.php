<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009  <>
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
/**
 * Plugin 'Overview' for the 'user_overview' extension.
 *
 * @author	 <>
 */

class tx_caretaker_pi_abstract extends tx_caretaker_pibase {
	var $prefixId = 'tx_caretaker_pi_abstract';		// Same as class name
	var $scriptRelPath = 'pi_abstract/class.tx_caretaker_pi_abstract.php';	// Path to this script relative to the extension dir.
	var $extKey = 'caretaker';	// The extension key.

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

				// render childnodes
			$childTemplate = $this->cObj->getSubpart($template,  '###'.$this->conf['childSubpartError'].'###' );
			$renderData['renderedErrorNodes']     = $this->renderNodeList( $data['testResults']['error']     , $childTemplate);
			
			$childTemplate = $this->cObj->getSubpart($template,  '###'.$this->conf['childSubpartWarning'].'###' );
			$renderData['renderedWarningNodes']   = $this->renderNodeList( $data['testResults']['warning']   , $childTemplate);

			$lcObj = t3lib_div::makeInstance('tslib_cObj');
			$lcObj->start($renderData);

				// substitute subparts
			if ($this->conf['subparts.']) {
				foreach (array_keys($this->conf['subparts.']) as $key){
					if (  substr($key, -1) != '.'){
						$subpart  = $lcObj->cObjGetSingle($this->conf['subparts.'][$key], $this->conf['subparts.'][$key.'.']);
						$template = $this->cObj->substituteSubpart($template, $key, $subpart);
					}
				}
			}

				// substitute markers
			if ($this->conf['markers.']) {
				foreach (array_keys($this->conf['markers.']) as $key){
					if (  substr($key, -1) != '.'){
						$mark = $lcObj->cObjGetSingle($this->conf['markers.'][$key], $this->conf['markers.'][$key.'.']);
						$markers['###'.$key.'###'] = $mark;
					}
				}
				$template = $this->cObj->substituteMarkerArray($template,$markers);
			}

			
			
			$content = 	$template;

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
	function renderNodeList($nodeDataList,  $template){
		
		$renderedNodelist = '';
		if ($nodeDataList && is_array($nodeDataList)) {
			foreach ($nodeDataList as $nodeData){
				$lcObj = t3lib_div::makeInstance('tslib_cObj');
				$lcObj->start($nodeData);
				$node_markers = array();
				if ($this->conf['childMarkers.']) {
					foreach (array_keys($this->conf['childMarkers.']) as $key){
						if (  substr($key, -1) != '.'){
							$mark = $lcObj->cObjGetSingle($this->conf['childMarkers.'][$key], $this->conf['childMarkers.'][$key.'.']);
							$node_markers['###'.$key.'###'] = $mark;
						}
					}
					$renderedNodelist .= $this->cObj->substituteMarkerArray($template,$node_markers);
				}
			}
		}

		return $renderedNodelist;
	}

	/**
	 * Get the node which is configured in the pi-flexform
	 *
	 * @return tx_caretaker_Node
	 */
	function getNode() {

		$this->pi_initPIflexForm();
		$node_id =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'node_id');
		$node_repository = tx_caretaker_NodeRepository::getInstance();

		$node = $node_repository->id2node($node_id);
		return $node;
	}

	/**
	 * Get the status of all test nodes and some extra informations
	 *
	 * @param tx_caretaker_Node $node
	 * @return array Associative Array with the keys 'nodeInfo' and 'testResults'
	 */
	function getNodeStatusData($node){
		
		$testChildNodes = $node->getTestNodes();

		$nodesErrors    = array();
		$nodesWarnings  = array();

		$worst_state       = TX_CARETAKER_STATE_OK;
		$worst_state_info  = '';

		$num_error = 0;
		$num_warning = 0;
		$num_ok = 0;
		$num_undefined = 0;

		foreach ($testChildNodes as $testNode) {

			$testResut = $testNode->getTestResult();
			$testNodeState     = $testResut->getState();

				// worst state
			if ( $testNodeState > $worst_state ){
				$worst_state      = $testNodeState;
				$worst_state_info = $testResut->getStateInfo();
			}

				// count node states info
			switch ( $testState ) {
				case TX_CARETAKER_STATE_ERROR:
					$num_error ++;
					break;
				case TX_CARETAKER_STATE_WARNING:
					$num_warning ++;
					break;
				case TX_CARETAKER_STATE_OK:
					$num_ok ++;
					break;
				case TX_CARETAKER_STATE_UNDEFINED:
					$num_undefined ++;
					break;
			}

				// aggreate infos about warnings and errors
			if ( $testNodeState > TX_CARETAKER_STATE_OK ) {
				
				$instance  = $testNode->getInstance();
				$nodeInfo = Array (
					'title'           => $instance->getTitle().' '.$testNode->getTitle() ,
					'node_title'      => $testNode->getTitle() ,
					'instance_title'  => $instance->getTitle() ,
					'node_id'         => $testNode->getCaretakerNodeId(),
					'link_parameters' => '&tx_caretaker_pi_singleview[id]='.$testNode->getCaretakerNodeId(),

					'timestamp'    => $testResut->getTimestamp(),
					'stateinfo'    => $testResut->getStateInfo(),
					'stateinfo_ll' => $testResut->getLocallizedStateInfo(),
					'message'      => $testResut->getMessage(),
					'message_ll'   => $testResut->getLocallizedMessage(),
					'state'        => $testResut->getState(),
				);

					// save info
				switch ( $testNodeState ) {
					case TX_CARETAKER_STATE_ERROR:
						$nodesErrors[] = $nodeInfo;
						$num_error ++;
						break;
					case TX_CARETAKER_STATE_WARNING:
						$nodesWarnings[] = $nodeInfo;
						$num_warning ++;
						break;
				}
			}
		}

		$data = array(
			'nodeInfo' => array(
				'numError'     => $num_error,
				'numWarning'   => $num_warning,
				'numUndefined' => $num_undefined,
				'numOk'        => $num_ok,
				'nodeTitle'    => $node->getTitle(),
				'state'        => $worst_state,
				'stateInfo'    => $worst_state_info
			),
			'testResults' => array(
				'error'     => $nodesErrors,
				'warning'   => $nodesWarnings
			)
		);
		
		return $data;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_abstract/class.tx_caretaker_pi_abstract.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_abstract/class.tx_caretaker_pi_abstract.php']);
}

?>