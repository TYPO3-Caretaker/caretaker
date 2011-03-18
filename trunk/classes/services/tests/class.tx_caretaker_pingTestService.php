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
 * Testservice to send a system-ping command.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */class tx_caretaker_pingTestService extends tx_caretaker_TestServiceBase {

	/**
	 * Value Description
	 * @var string
	 */
	protected $valueDescription = 'Milliseconds';

	/**
	 * Service type description in human readble form.
	 * @var string
	 */
	protected $typeDescription = 'LLL:EXT:caretaker/locallang_fe.xml:ping_service_description';

	/**
	 * Template to display the test Configuration in human readable form.
	 * @var string
	 */
	protected $configurationInfoTemplate = 'LLL:EXT:caretaker/locallang_fe.xml:ping_service_configuration';

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
	 */
	public function runTest() {
		$time_warning = $this->getTimeWarning();
		$time_error = $this->getTimeError();
		$command = $this->buildPingCommand();

		if ($command) {
			list ($returnCode, $message, $time) = $this->executeSystemCommand($command);

			if ($returnCode === 0) {
				if ($time_error && $time > $time_error) {
					return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error, $time , 'LLL:EXT:caretaker/locallang_fe.xml:ping_info' );
				}
				if ($time_warning && $time > $time_warning) {
					return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_warning, $time , 'LLL:EXT:caretaker/locallang_fe.xml:ping_info' );
				}
				return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok, $time , 'LLL:EXT:caretaker/locallang_fe.xml:ping_info' ) ;
			} else {
				$message = new tx_caretaker_ResultMessage( 'LLL:EXT:caretaker/locallang_fe.xml:ping_error', array('command'=>$command, 'message'=>$message) );
				return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error, $time , $message );
			}
		} else {
			return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error, 0 , 'LLL:EXT:caretaker/locallang_fe.xml:ping_no_command_template');
		}
	}

	/**
	 * Get the maximal time befor WARNING
	 * @return unknown_type
	 */
	protected function getTimeWarning() {
		return $this->getConfigValue('max_time_warning');
	}

	/**
	 * Get the maximal time before ERROR
	 * @return integer
	 */
	protected function getTimeError() {
		return $this->getConfigValue('max_time_error');
	}

	/**
	 * Bild the Build Ping Command for this System
	 *
	 * @return string
	 */
	protected function buildPingCommand() {
		$hostname = $this->instance->getHostname();
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$commandTemplate = $confArray['ping.']['cli_command'];
		$command = str_replace('###' , $hostname, $commandTemplate);
		return $command;
	}

	/**
	 * Execute a system command
	 *
	 * @param string $command
	 * @return array Array of ReturnCode Message and $ime
	 */
	protected function executeSystemCommand($command){
		$starttime = microtime(TRUE);

		$returnCode = FALSE;
		$messages   = array();
		$message    = '';

		exec( $command , $messages , $returnCode );
		$message = implode( chr(10) , $messages );

		$endtime = microtime(TRUE);
		$time =  ($endtime - $starttime)*1000;

		return array( $returnCode , $message , $time );
	}

}

?>
