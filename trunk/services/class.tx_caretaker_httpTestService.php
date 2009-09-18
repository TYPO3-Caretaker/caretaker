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
	 * Value Description
	 * @var string
	 */
	protected $valueDescription = 'Milliseconds';

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
	 */
	function runTest() {
		
		$time_warning  = $this->getTimeWarning();
		$time_error    = $this->getTimeError();
		$expected_code = $this->getExpectedReturnCode();
		$request_query = $this->getRequestQuery();
		$request_method = $this->getRequestMethod();
		$request_data = $this->getRequestData();
		
		$url           = $this->getInstanceUrl();
		
		$request_url   = $url.$request_query;
		
		if ( !($expected_code && $request_url)) { 
	    	return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNDEFINED, 0 , 'No HTTP-Code or no query was set' );
		}
		
		list ($http_status, $time, $response) = $this->executeCurlRequest($request_url, $time_error, $request_method, $request_data);
		
		if ($time_error && $time > $time_error ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'HTTP-Request took '.$time.' '.$this->valueDescription.'. :: '.$request_url );
		}
		
		if ($time_warning && $time > $time_warning ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time , 'HTTP-Request took '.$time.' '.$this->valueDescription.'. :: '.$request_url );
		}
		
		if ($http_status == $expected_code){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time , 'Status OK :: '.$request_url );
		} else {
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , 'Returned Status '.$http_status.' does not match expected state '.$expected_code.' :: '.$request_url );
		}
		
	}
	
	/**
	 * Get the maximal time befor WARNING 
	 * @return integer
	 */
	protected function getTimeWarning(){
		return $this->getConfigValue('max_time_warning');
	}
	
	/**
	 * Get the maximal time before ERROR
	 * @return integer
	 */
	protected function getTimeError(){
		return $this->getConfigValue('max_time_error');
	}
	
	/**
	 * 
	 * @return integer
	 */
	protected function getExpectedReturnCode(){
		return $this->getConfigValue('expected_status');
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getRequestQuery(){
		return $this->getConfigValue('request_query');
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getRequestMethod(){
		return $this->getConfigValue('request_method');
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getRequestData(){
		return $this->getConfigValue('request_data');
	}
			
	/**
	 * 
	 * @return unknown_type
	 */
	protected function getInstanceUrl(){
		return $this->instance->getUrl();
	}
	
	/**
	 * 
	 * @param string $request_url
	 * @param string $request_method
	 * @param string $request_data
	 * @return array http-Status and time in Seconds
	 */
	protected function executeCurlRequest($request_url , $timeout=0, $request_method="GET" , $request_data="" ){
		$starttime=microtime(true);
		
		$curl = curl_init();
		
			// url & timeout
		curl_setopt($curl, CURLOPT_URL, $request_url);
        if ( $timeout > 0 ) { 
        	curl_setopt($curl, CURLOPT_TIMEOUT, (int)ceil( $timeout / 1000) );
        }
			// handle request method
		switch ($request_method){
			case 'POST':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($$request_data) ) );
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
				break;
			case 'PUT':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($$request_data) ) );
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;				
		}
		
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		$info     = curl_getinfo($curl);
		curl_close($curl);
			
		$endtime=microtime(true);
		$time = $endtime-$starttime;
		
		return array ( $info['http_code'], $time*1000 ,$response);
	}
}

?>