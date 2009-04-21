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

require_once(t3lib_extMgm::extPath('caretaker').'/pi_base/class.tx_caretaker_pibase.php');
require_once(t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Helper.php');

class tx_caretaker_pi_overview extends tx_caretaker_pibase {
	var $prefixId = 'tx_caretaker_pi_overview';		// Same as class name
	var $scriptRelPath = 'pi_overview/class.tx_caretaker_pi_overview.php';	// Path to this script relative to the extension dir.
	var $extKey = 'caretaker';	// The extension key.

	
	function getContent(){
		$nodes = $this->getNodes();
		
		if (count($nodes)>0){
			$content = '';
			foreach ($nodes as $node){
				 $content.= $this->showNodeInfo($node);
			}
			return $content;
		} else {
			return 'no node ids found';
		} 
	}
	
	function getNodes(){
		$this->pi_initPIflexForm();
		$node_ids =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'node_ids');
		
		$nodes = array();
		$ids = explode (chr(10),$node_ids);
		
		foreach ($ids as $id){
			$node = tx_caretaker_Helper::id2node($id);
			if ($node) $nodes[]=$node;
		}

		return $nodes;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_overview/class.tx_caretaker_pi_overview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_overview/class.tx_caretaker_pi_overview.php']);
}

?>