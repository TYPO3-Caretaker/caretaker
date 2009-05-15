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

interface tx_caretaker_TestService {
	
	/**
	 * Initialize the Service
	 * @return unknown_type
	 */
	public function init();
	
	/**
	 * Set the instance for the test execution
	 * @param $instance
	 */
	function setInstance($instance);

	/**
	 * Set the configuttion for this test
	 * @param $configuration
	 */
	function setConfiguration($configuration);
	
	/**
	 * Run the Test defined in TestConf and return a Testresult Object 
	 * 
	 * @param array $flexFormData Flexform Configuration
	 * @return tx_caretaker_TestResult
	 */
	public function runTest();
	
	/**
	 * Get a short description for the meaning of the value in the current test
	 *  
	 * @return String Description what is stored in the Value field. 
	 */
	public function getValueDescription();
	
}

?>