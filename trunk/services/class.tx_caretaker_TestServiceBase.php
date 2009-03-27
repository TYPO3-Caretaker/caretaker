<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de>
 * 
 * $$Id: class.tx_caretaker_service_interface.php 33 2008-06-13 14:00:38Z thomas $$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Thomas Hempel <hempel@work.de>
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

require_once(PATH_t3lib.'class.t3lib_svbase.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRange.php');
require_once (t3lib_extMgm::extPath('caretaker').'/services/interface.tx_caretaker_TestService.php');

class tx_caretaker_TestServiceBase extends t3lib_svbase implements tx_caretaker_TestService{
	var $instance;
	var $configuration = false;
	var $ff_config = false;

	function setInstance($instance){
		$this->instance = $instance;
	}
	
	function setConfiguration($configuration){
		
		if (is_array( $configuration) ){
			$this->ff_config = false;	
			$this->configuration  = $configuration;
		} else if ($configuration){
			$this->ff_config = true;
			$this->configuration = t3lib_div::xml2array($configuration);
		}
	}
	
	function getConfigValue($key, $default=false, $sheet=false){
		if (!$this->configuration) return false;
		  
		$result = false;
		if ($this->ff_config){
			if (!$sheet) $sheet = 'sDEF';
			if (isset($this->configuration['data'][$sheet]['lDEF'][$key]['vDEF']) ){
				$result = $this->configuration['data'][$sheet]['lDEF'][$key]['vDEF'];
			}
		} else {
			if ($sheet==false && isset($this->configuration[$key]) ){
				$result = $this->configuration[$key];
			} else if (isset($this->configuration[$sheet][$key]) ){
				$result = $this->configuration[$sheet][$key];
			}
		} 
		if ($result){
			return $result;
		} else {
			return $default;
		}
	}
		
	/**
	 * Run the Test defined in TestConf and return a Testresult Object 
	 * 
	 * @param array $flexFormData Flexform Configuration
	 * @return tx_caretaker_TestResult
	 */
	public function runTest(){
		$result = new tx_caretaker_TestResult();
		return $result;
	}

}

?>