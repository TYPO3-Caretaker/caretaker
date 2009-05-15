<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
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

require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_ping extends tx_caretaker_TestServiceBase {
	
	/**
	 * Constructor
	 */
	function __construct(){
		$this->valueDescription = "Micro seconds";
	}

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
	 */
	function runTest() {
		
		$port         = $this->getConfigValue('port');
		$time_warning = $this->getConfigValue('max_time_warning');
		$time_error   = $this->getConfigValue('max_time_error');
		
		if (!$port) {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNKNOWN, 0 , 'Port was not defined' );
		}
		
		
		
		$starttime=microtime(true);
		
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$command   = $confArray['ping.']['cli_command'];
		if ($command ){
			
			$command = str_replace( '###' , $this->instance->getHostname(), $command);
			$res = false;
			$msg = system ($command, $res);
			$endtime=microtime(true);
			$time=$endtime-$starttime;
			$time *= 1000; // convert to micro seconds
	
			if ($res == 0){ 
				if ($time_error && $time > $time_error) {
					return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping took '.$time.' micro seconds' );
				} 
		
				if ($time_warning && $time > $time_warning) {
					return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time , 'Ping took '.$time.' micro seconds' );
				} 
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time , 'Ping took '.$time.' micro seconds' );
			} else {
				return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Ping failed. '.$msg );
			}
		} else {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, 0 , 'CLI Ping-Command must be configured in ExtConf' );
		}
	}

}

?>