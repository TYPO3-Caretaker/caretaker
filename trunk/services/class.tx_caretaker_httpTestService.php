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

class tx_caretaker_httpTestService extends tx_caretaker_TestServiceBase {

	/**
	 * Constructor
	 */
	function __construct(){
		$this->valueDescription = "Seconds";
	}	

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
	 */
	function runTest() {
		
		$time_warning  = $this->getConfigValue('max_time_warning');
		$time_error    = $this->getConfigValue('max_time_error');
		$expected_code = $this->getConfigValue('expected_status');
		$request_query = $this->getConfigValue('request_query');
		
		$request_url =$this->instance->getUrl().$request_query;
		
		if ( !($expected_code && $request_url)) { 
			
	    	return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNDEFINED, 0 , 'No HTTP-Code or no query was set' );
		}
		
		$starttime=microtime(true);
		
		$curl = curl_init();
        if (!$curl) {
        	return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNDEFINED, 0 , 'CURL is not present, this test is skipped' );
        }
        
		curl_setopt($curl, CURLOPT_URL, $request_url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$res     = curl_exec($curl);
		$info    = curl_getinfo($curl);
		curl_close($curl);
			
		$endtime=microtime(true);
		$time = $endtime-$starttime;
		
		if ($time_error && $time > $time_error ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'HTTP-Request took '.$time.' miliseconds. :: '.$request_url );
		}
		
		if ($time_warning && $time > $time_warning ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time , 'HTTP-Request took '.$time.' miliseconds. :: '.$request_url );
		}
		
		if ($info['http_code'] == $expected_code){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time , 'Status OK :: '.$request_url );
		} else {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Returned Status '.$info['http_code'].' does not match expeted state '.$expected_code.' :: '.$request_url );
		}
		
	}
	
}

?>