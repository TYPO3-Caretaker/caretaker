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
	
	public function __construct(){
		tslib_eidtools::connectDB();
		tslib_eidtools::initFeUser();
	}
	
	/**
	 * @return tx_caretaker_agregatorNode
	 */
	private function getRequestedNode( $nodeId ){
		
		$node = false;
		if ($nodeId){
			$node_repository = tx_caretaker_NodeRepository::getInstance();
	        $node = $node_repository->id2node( $nodeId , false);
		}
		
		if ($node) {
			// check credentials
		}
		
		return $node;
	}
	
	private function formatResultData( $data, $format ){
		switch ($format){
			case 'application/xml':
				return $this->formatResultDataXml($data);
				break;
			case 'application/json':
			default:
				return $this->formatResultDataJson($data);
				break;
		}
	}
	
	private function formatResultDataXml( $data ){
		
	}
	
	private function formatResultDataJson($data ){
		return json_encode($data);
	}
	
	public function getEidData(){
		
		$nodeId = t3lib_div::_GP('node');
		$node = $this->getRequestedNode($nodeId);
		
		if (!$node){ 
			return array ('success' => false, 'id' => $nodeId ) ;
		}
		
		$result =  array(
			'success' => true,
			'id' => $nodeId
		);
		
		// add node infos
		if ( t3lib_div::_GP('addNode') == 1 ){
			$result['node'] = array(
				'id' => $node->getCaretakerNodeId(),
				'title' => $node->getTitle(),
				'type' => $node->getType(),
			);
		} 
		
		// add result infos
		if (  t3lib_div::_GP('addResult') == 1 ){
			$nodeResult = $node->getTestResult();
			$result['result'] = array(
				'state'   => $nodeResult->getState(),
				'message' => $nodeResult->getMessage(),
			);
		} 
		
		// add child infos
		if (  t3lib_div::_GP('addChildren') == 1 ){
			$result['children'] = false;
			$children = $node->getChildren();
			if ($children and count($children) > 0 ){
				foreach ($children as $child){
					$result['children'][] = $child->getCaretakerNodeId();
				}
			} 
		} 
		
		// add statistic infos
		if (  t3lib_div::_GP('addTestStatistics') == 1 ){
			
			$result['statistics']['count'] = array(
				'error'     => 0,
				'warning'   => 0,
				'undefined' => 0,
				'unknown'   => 0,
				'ack'       => 0,
				'due'       => 0
			);
			$result['statistics']['ids'] = array(
				'error'     => array(),
				'warning'   => array(),
				'undefined' => array(),
				'unknown'   => array(),
				'ack'       => array(),
				'due'       => array(),
			);
			
			$tests = $node->getTestNodes();
			if ( $tests && count($tests) ){
				foreach ($tests as $test){
					$testResult = $test->getTestResult();
					switch ( $testResult->getState() ){
						case tx_caretaker_Constants::state_error:
							$result['statistics']['id']['error'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['error'] ++;
							break;
						case tx_caretaker_Constants::state_warning:
							$result['statistics']['id']['warning'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['warning'] ++;
							break;
						case tx_caretaker_Constants::state_ok:
							$result['statistics']['id']['ok'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['ok'] ++;
							break;
						case tx_caretaker_Constants::state_undefined:
							$result['statistics']['id']['unknown'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['unknown'] ++;
							break;
						case tx_caretaker_Constants::state_ack:
							$result['statistics']['id']['ack'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['ack'] ++;
							break;
						case tx_caretaker_Constants::state_due:
							$result['statistics']['id']['due'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['due'] ++;
							break;		
					}
				}
			}	
			
		}
		
		return $result;
		
	}
	
	public function processEidRequest(){
		$data = $this->getEidData();
		$format = $_SERVER['HTTP_ACCEPT'];
		echo $this->formatResultData($data,$format);
		die();
	}
	
}

if ( t3lib_div::_GP('eID') && t3lib_div::_GP('eID') == 'tx_caretaker')    {
   $SOBE = new tx_caretaker_Eid();
   $SOBE->processEidRequest();
   exit;
}
?>