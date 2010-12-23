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

class tx_caretaker_pi_singleview extends tx_caretaker_pibase {
	var $prefixId = 'tx_caretaker_pi_singleview';		// Same as class name
	var $scriptRelPath = 'pi_singleview/class.tx_caretaker_pi_singleview.php';	// Path to this script relative to the extension dir.
	var $extKey = 'caretaker';	// The extension key.
	
	function getContent(){
		$node = $this->getNode();
		if ($node) {
			$content = $this->showNodeInfo($node);
		} else {
			$content = 'no node found'; 
		}
		return $content;
	}
		
	function getNode(){
			
		$id   = $this->piVars['id'];
		$node = false;
		$node_repository = tx_caretaker_NodeRepository::getInstance();

		if ($id){
			$node = $node_repository->id2node($id);
		} else {
			$this->pi_initPIflexForm();
			$node_id =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'node_id');
			$node = $node_repository->id2node($node_id);
		}	
		return $node;
	}
	
	function getNodeData($node){
		
		$data = parent::getNodeData($node);
		
		require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_ResultRangeRenderer_pChart.php');
		
		$range = 24;
		if ($this->piVars['range']) $range = (int)$this->piVars['range'];
		
		$id = $node->getCaretakerNodeID();
		$result_range = $node->getTestResultRange(time()-3600*$range , time() );
		$filename = 'typo3temp/caretaker/charts/'.$id.'_'.$range.'.png';
		$base = t3lib_div::getIndpEnv('TYPO3_SITE_URL');

		$renderer = tx_caretaker_ResultRangeRenderer_pChart::getInstance($this->LOCAL_LANG, $this->LLkey);
		
		if (is_a($node, 'tx_caretaker_TestNode' ) ){
			
			$TestResultRangeChartRenderer = new tx_caretaker_TestResultRangeChartRenderer( );
			$TestResultRangeChartRenderer->setTitle( $node->getTitle() );
			$TestResultRangeChartRenderer->setTestResultrange( $result_range );
			$result = $TestResultRangeChartRenderer->getChartImageTag( $filename , $base);

			if ($result){
				$data['chart'] = $result;
			} else {
				$data['chart'] = '<strong>Graph 2 Error</strong>';
			}

		} else  if (is_a( $node, 'tx_caretaker_AggregatorNode')){
			
			$result = $renderer->renderAggregatorResultRange(PATH_site.$filename, $result_range , $node->getTitle());

			if ($result){
				$data['chart'] = '<img src="'.$base.$filename.'" />';
			} else {
				$data['chart'] = '<strong>Graph Error</strong>';
			}

		}	
		
		

		
		
		$data['range'] = $range/24;
		
		return $data;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_singleview/class.tx_caretaker_pi_singleview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_singleview/class.tx_caretaker_pi_singleview.php']);
}

?>