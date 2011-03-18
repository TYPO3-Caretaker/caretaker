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
 * Baseclass for all Testservice implementations.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_TestServiceBase extends t3lib_svbase implements tx_caretaker_TestServiceInterface {

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
	 * Value Description. Can be a LLL Label.
	 * @var string
	 */
	protected $valueDescription = '';


	/**
	 * Testtype in human readable form. Can be a LLL Label.
	 * @var sring
	 */
	protected $typeDescription  = '';

	/**
	 * Template to display the test Configuration in human readable form. Can be a LLL Label.
	 * @var string
	 */
	protected $configurationInfoTemplate = '';

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
	public function setConfiguration($configuration) {
		if (is_array( $configuration) && !is_array($configuration['data']) ){
			$this->array_configuration  = $configuration;

		} else if (is_array($configuration) && is_array($configuration['data']) ){
			$this->flexform_configuration = $configuration;

		} else if (!is_array($configuration)) {
			$this->flexform_configuration = t3lib_div::xml2array($configuration);
		}
		
		// echo 'Following configuration is set:'."\n";
		// print_r($this->flexform_configuration);
	}

	/**
	 * Get a single Value from the test configuration
	 * 
	 * @param string $key
	 * @param string $default
	 * @param string $sheet
	 * @return string
	 */
	public function getConfigValue($key, $default = false, $sheet = false){
		$result = false;
		if ($this->flexform_configuration && is_array( $this->flexform_configuration ) ){
			if (!$sheet) $sheet = 'sDEF';
			if ( isset($this->flexform_configuration['data'][$sheet]['lDEF'][$key]['vDEF'] ) ){
				$result = $this->flexform_configuration['data'][$sheet]['lDEF'][$key]['vDEF'];
			}
		} else if ($this->array_configuration){
			if ($sheet==false && isset($this->array_configuration[$key]) ){
				$result = $this->array_configuration[$key];
			} else if (isset($this->array_configuration[$sheet][$key]) ){
				$result = $this->array_configuration[$sheet][$key];
			}
		}

		if ( $result !== false){
			return $result;
		} else {
			return $default;
		}
	}

	/**
	 * Return the type Description of this test Service
	 * @return string
	 */
	public function getTypeDescription() {
		return tx_caretaker_LocallizationHelper::locallizeString( $this->typeDescription );
	}

	/**
	 * Return the type ConfigurationInfoTemplate of this test Service
	 * @return string
	 */
	public function getConfigurationInfo() {
		$markers = array();
		if ($this->flexform_configuration && is_array($this->flexform_configuration['data'])){
			foreach($this->flexform_configuration['data'] as $sheetName => $sheet) {
				foreach( $this->flexform_configuration['data'][$sheetName]['lDEF'] as $key => $value ){
					$markers['###'.strtoupper($key).'###'] = $value['vDEF'];
				}
			}
		}

		$result = $this->locallizeString(  $this->configurationInfoTemplate );
		foreach ($markers as $marker => $content){
			$result = str_replace( $marker, $content , $result);
		}
		return $result;
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
	 * Execute a HTTP request for the POST values via CURL
	 *
	 * @param $requestUrl string The URL for the HTTP request
	 * @param $postValues array POST values with key / value
	 * @return array info/response
	 */
	protected function executeHttpRequest($requestUrl, $postValues = null) {
		$curl = curl_init();
        if (!$curl) {
        	return false;
        }

		curl_setopt($curl, CURLOPT_URL, $requestUrl);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		$headers = array(
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		if (is_array($postValues)) {
			foreach($postValues as $key => $value) {
				$postQuery .= urlencode($key) . '=' . urlencode($value) . '&';
			}
			rtrim($postQuery, '&');

			curl_setopt($curl, CURLOPT_POST, count($postValues));
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postQuery);
		}

		$response = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		return array(
			'response' => $response,
			'info' => $info
		);
	}
	
	/**
	 * Get the value description for the test
	 * 
	 * @return String Description what is stored in the Value field. 
	 */
	public function getValueDescription() {
		return $this->valueDescription;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see interfaces/tx_caretaker_TestService#isExecutable()
	 */
	public function isExecutable() {
		return true;
	}

	/**
	 * Translate a given string in the current language
	 *
	 * @param string $string
	 * @return string
	 */
	protected function locallizeString($locallang_string) {

		$locallang_parts = explode (':',$locallang_string);

		if( array_shift($locallang_parts) != 'LLL') {
			return $locallang_string;
		}

		switch (TYPO3_MODE){
			case 'FE':

				$lcObj  = t3lib_div::makeInstance('tslib_cObj');
				return( $lcObj->TEXT(array('data' => $locallang_string )) );

			case 'BE':

				$locallang_key   = array_pop($locallang_parts);
				$locallang_file  = implode(':',$locallang_parts);

				$language_key  = $GLOBALS['BE_USER']->uc['lang'];
				$LANG = t3lib_div::makeInstance('language');
				$LANG->init($language_key);

				return $LANG->getLLL($locallang_key, t3lib_div::readLLfile(t3lib_div::getFileAbsFileName( $locallang_file ),$LANG->lang, $LANG->charSet ));

			default :

				return $locallang_string;
		}

	}
	
}
?>