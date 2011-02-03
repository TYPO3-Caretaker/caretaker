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
	
	public function __construct(){
		tslib_eidtools::connectDB();
		tslib_eidtools::initFeUser();
		tslib_eidtools::initLanguage();
		tslib_eidtools::initTCA();
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
	
	private function sendResultData( $data, $format ){
		switch ($format){
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
	
	private function formatResultDataXml( $data ){
		
		switch ( gettype($data) ){
			case 'array':
				$result = '';
				foreach ( $data as $key => $value ){
					if( is_int($key) ){
						$result .= '<item index="' . $key . '">' . $this->formatResultDataXml($value). '</item>';		
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
				return '<![CDATA['.$data.']]>';
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
	
	private function formatResultDataJson($data ){
		return json_encode($data);
	}
	
	public function getEidFormat(){
		 $format = $_SERVER['HTTP_ACCEPT'];
		 if (  t3lib_div::_GP('format') ) {
			$format = 	t3lib_div::_GP('format');	 	
		 } 
		 return $format;
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
				'description' => $node->getDescription()
			);
		} 
		
		// add result infos
		if (  t3lib_div::_GP('addResult') == 1 ){
			$nodeResult = $node->getTestResult();
			$result['result'] = array(
				'state'     => $nodeResult->getState(),
				'info'      => $nodeResult->getLocallizedStateInfo(),
				'message'   => $nodeResult->getLocallizedInfotext(),
				'timestamp' => $nodeResult->getTimestamp()
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
				'ok'        => 0,
				'undefined' => 0,
				'ack'       => 0,
				'due'       => 0
			);
			$result['statistics']['ids'] = array(
				'error'     => array(),
				'warning'   => array(),
				'ok'        => array(),
				'undefined' => array(),
				'ack'       => array(),
				'due'       => array(),
			);
			
			$tests = $node->getTestNodes();
			if ( $tests && count($tests) ){
				foreach ($tests as $test){
					$testResult = $test->getTestResult();
					switch ( $testResult->getState() ){
						case tx_caretaker_Constants::state_error:
							$result['statistics']['ids']['error'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['error'] ++;
							break;
						case tx_caretaker_Constants::state_warning:
							$result['statistics']['ids']['warning'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['warning'] ++;
							break;
						case tx_caretaker_Constants::state_ok:
							$result['statistics']['ids']['ok'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['ok'] ++;
							break;
						case tx_caretaker_Constants::state_undefined:
							$result['statistics']['ids']['undefined'][] = $test->getCaretakerNodeId();
							$result['statistics']['count']['undefined'] ++;
							break;
						case tx_caretaker_Constants::state_ack:
							$result['statistics']['ids']['ack'][] = $test->getCaretakerNodeId();
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
		$data   = $this->getEidData();
		$format = $this->getEidFormat();
		$this->sendResultData($data,$format);
	}
	
}

if ( t3lib_div::_GP('eID') && t3lib_div::_GP('eID') == 'tx_caretaker')    {
   $SOBE = new tx_caretaker_Eid();
   $SOBE->processEidRequest();
   exit;
}
?>