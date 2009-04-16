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


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Helper.php');

class tx_caretaker_pi_overview extends tslib_pibase {
	var $prefixId = 'tx_caretaker_pi_overview';		// Same as class name
	var $scriptRelPath = 'pi_overview/class.user_overview_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'caretaker';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
		$node = $this->getNode();
		debug($node);
		
		$content='';
		
		return $this->pi_wrapInBaseClass($content);
	}
	
	function getNode(){
		$this->pi_initPIflexForm();
		$mode =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'mode');
		
		$node = false;
		switch ($mode){
			case 'instancegroup':
				$instancegroup =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'instancegroup');
				$node = tx_caretaker_Helper::getNode($instancegroup);
				break;
			case 'instance':
				$instance =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'instance');
				$node = tx_caretaker_Helper::getNode(false, $instance);
				break;
			case 'instance_testgroup':
				$instance =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'instance');
				$testgroup =  $this->pi_getFFValue($this->cObj->data['pi_flexform'],'testgroup');
				$node = tx_caretaker_Helper::getNode(false, $instance, $testgroup);
				break;	
		}
		return $node;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_overview/class.tx_caretaker_pi_overview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_overview/class.tx_caretaker_pi_overview.php']);
}

?>