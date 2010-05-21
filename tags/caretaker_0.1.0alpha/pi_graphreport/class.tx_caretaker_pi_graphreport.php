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

require_once(t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_ResultRangeRenderer_pChart.php');

class tx_caretaker_pi_graphreport extends tx_caretaker_pibase {
	var $prefixId = 'tx_caretaker_pi_graphreport';		// Same as class name
	var $scriptRelPath = 'pi_graphreport/class.tx_caretaker_pi_graphreport.php';	// Path to this script relative to the extension dir.
	var $extKey = 'caretaker';	// The extension key.
	
	
	function getContent(){
		
		$template = $this->cObj->cObjGetSingle($this->conf['template'], $this->conf['template.']);
		
			// render Node Infos
		$data  = $this->getData();
		$lcObj = t3lib_div::makeInstance('tslib_cObj');
		$lcObj->start($data);
		$node_markers = array();
		if ($this->conf['markers.']) {
			foreach (array_keys($this->conf['markers.']) as $key){
				if (  substr($key, -1) != '.'){
					$mark = $lcObj->cObjGetSingle($this->conf['markers.'][$key], $this->conf['markers.'][$key.'.']);
					$node_markers['###'.$key.'###'] = $mark;
				}
			}
			$template = $this->cObj->substituteMarkerArray($template,$node_markers);
		}
		return $template;
	}
	
	function getData(){
		$data = $this->cObj->data;
		
		$range = $this->conf['defaultRange'] ? (int)$this->conf['defaultRange'] : 24;
		if ($this->piVars['range']) $range = (int)$this->piVars['range'];
		
		$nodes = $this->getNodes();
		$titles = array();
		if (count($nodes)>0){
			$content = '';
			$result_ranges = array();
			$id = '';
			foreach ($nodes as $node){
				if (is_a($node,'tx_caretaker_TestNode')){
					$result_ranges[] = $node->getTestResultRange(time()-(3600*$range), time());
					$titles[] = $node->getTitle();
					$id .= $node->getCaretakerNodeId();
				}
			}

			if (count($result_ranges)>0){
				
				$filename = 'typo3temp/caretaker/charts/report_'.$id.'_'.$range.'.png';
				
				$renderer = tx_caretaker_ResultRangeRenderer_pChart::getInstance($this->LOCAL_LANG, $this->LLkey);
				$result   = $renderer->renderMultipleTestResultRanges(PATH_site.$filename, $result_ranges, $titles );
				
				$base = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
				
				$data['chart'] = '<img src="'.$base.$filename.'" />';;
			} else {
				$data['chart'] = 'please select one or more test-nodes';
			}
			
		} else {
			$data['chart'] = 'no node ids found';
		} 
		
		return $data;
		 		
	}
		
	function getNodes(){
		$this->pi_initPIflexForm();
		$node_ids =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'node_ids');
		
		$nodes = array();
		$ids = explode (chr(10),$node_ids);
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		
		foreach ($ids as $id){
			$node = $node_repository->id2node($id);
			if ($node) $nodes[]=$node;
		}

		return $nodes;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_graphreport/class.tx_caretaker_pi_graphreport.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_graphreport/class.tx_caretaker_pi_graphreport.php']);
}

?>