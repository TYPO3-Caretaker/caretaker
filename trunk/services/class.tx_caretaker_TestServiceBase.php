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
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/interfaces/interface.tx_caretaker_TestService.php');

class tx_caretaker_TestServiceBase extends t3lib_svbase implements tx_caretaker_TestService{
	
	/**
	 * The instance the test is run for
	 * @var tx_caretaker_InstanceNode
	 */
	protected $instance;
	
	/**
	 * Test Array Configuration
	 * @var array
	 */
	protected $array_configuration = false;
	
	/**
	 * Test Flexform Configuration
	 * @var array
	 */
	protected $flexform_configuration = false;
	
	/**
	 * Value Description
	 * @var string
	 */
	protected $valueDescription = '';

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_TestService#setInstance($instance)
	 */
	public function setInstance($instance){
		$this->instance = $instance;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_TestService#setConfiguration($configuration)
	 */
	public function setConfiguration($configuration){
		if (is_array( $configuration) ){
			$this->array_configuration  = $configuration;
		} else if ($configuration){
			$this->flexform_configuration = t3lib_div::xml2array($configuration);
		}
		
		//echo 'Following configuration is set:'."\n";
		//print_r($this->flexform_configuration);
		
	}
	
	/**
	 * Get a single Value from the test configuration
	 * 
	 * @param string $key
	 * @param string $default
	 * @param string $sheet
	 * @return string
	 */
	public function getConfigValue($key, $default=false, $sheet=false){
		
		$result = false;
		if ($this->flexform_configuration){
			if (!$sheet) $sheet = 'sDEF';
			if (isset($this->flexform_configuration['data'][$sheet]['lDEF'][$key]['vDEF']) ){
				$result = $this->flexform_configuration['data'][$sheet]['lDEF'][$key]['vDEF'];
			}
		} else if ($this->array_configuration){
			if ($sheet==false && isset($this->array_configuration[$key]) ){
				$result = $this->array_configuration[$key];
			} else if (isset($this->array_configuration[$sheet][$key]) ){
				$result = $this->array_configuration[$sheet][$key];
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
	
	/**
	 * Get the value description for the test
	 * 
	 * @return String Description what is stored in the Value field. 
	 */
	public function getValueDescription(){
		
		return $this->valueDescription;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see interfaces/tx_caretaker_TestService#isExecutable()
	 */
	public function isExecutable() {
		
		return true;
	}
	
}
?>