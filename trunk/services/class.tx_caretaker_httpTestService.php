<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
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
 * Testservice to execute a HHTP request and to check the result.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_httpTestService extends tx_caretaker_TestServiceBase {

	/**
	 * Value Description
	 * @var string
	 */
	protected $valueDescription = 'Milliseconds';

	/**
	 * Service type description in human readble form.
	 * @var string
	 */
	protected $typeDescription = 'LLL:EXT:caretaker/locallang_fe.xml:http_service_description';

	/**
	 * Template to display the test Configuration in human readable form.
	 * @var string
	 */
	protected $configurationInfoTemplate = 'LLL:EXT:caretaker/locallang_fe.xml:http_service_configuration';


	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
	 */
	function runTest() {
		
		$time_warning     = $this->getTimeWarning();
		$time_error       = $this->getTimeError();
		$expected_status  = $this->getExpectedReturnCode();
		$expected_headers = $this->getExpectedHeaders();
		$request_query    = $this->getRequestQuery();
		$request_method   = $this->getRequestMethod();
		$request_data     = $this->getRequestData();
		
		$url           = $this->getInstanceUrl();
		$parsed_url    = parse_url($url);
		if ($parsed_url['path'] == '') {
			$parsed_url['path'] = '/';
		}
		$request_url   = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'] . $request_query;

			// no query
		if ( !($expected_status && $request_url)) {
	    	return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_UNDEFINED, 0 , 'LLL:EXT:caretaker/locallang_fe.xml:http_no_query' );
		}

			// execute query
		list ($time, $response, $info, $headers) = $this->executeCurlRequest($request_url, $time_error, $request_method, $request_data);
		
		$values = array('url'=>$request_url, 'status'=>$info['http_code'], 'expected' => $expected_status );
		$submessages = array();

			// ERROR
		if ($time_error && $time > $time_error ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , new tx_caretaker_ResultMessage( 'LLL:EXT:caretaker/locallang_fe.xml:http_info', $values )  );
		}

			// WARNING
		if ($time_warning && $time > $time_warning ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING, $time ,  new tx_caretaker_ResultMessage( 'LLL:EXT:caretaker/locallang_fe.xml:http_info', $values )  );
		}

			// OK but status fails
		if ($info['http_code'] != $expected_status){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , new tx_caretaker_ResultMessage( 'LLL:EXT:caretaker/locallang_fe.xml:http_error', $values )  );
		}

			// OK but header fails
		if ( !$this->checkExpectedHeaders( $expected_headers,$headers ) ){
			return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR, $time , new tx_caretaker_ResultMessage( 'LLL:EXT:caretaker/locallang_fe.xml:header_error', $values )  );
		}

			// ERROR wrong status code
		return tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK, $time ,  new tx_caretaker_ResultMessage( 'LLL:EXT:caretaker/locallang_fe.xml:http_info', $values )  );
		
	}

	/**
	 * Compare the expected headers
	 * 
	 * @return boolean
	 */
	protected function checkExpectedHeaders( $expectedHeaders,$responseHeaders ){

		$instanceUrl      = $this->getInstanceUrl();
		$instanceUrlParts = explode( '/', str_replace( '://' , '/' , $instanceUrl ) );

		$instanceProtocol = array_shift( $instanceUrlParts );
 		$instanceHostname = array_shift( $instanceUrlParts );
		$instanceQuery    = implode( '/', $instanceUrlParts );

		$requestQuery     = $this->getRequestQuery();

		$result = TRUE;
		foreach ( $expectedHeaders as $headerName => $expectedValue ){
			
			$partialResult = TRUE;
			$headerValue   = NULL;
			
				// header was not found
			if (!$responseHeaders[$headerName]) {
				$partialResult = FALSE;
			}
				// header was found
			else {
				$headerValue = $responseHeaders[$headerName];
				
					// replace values
				$expectedValue = str_replace ('###INSTANCE_URL###',      $instanceUrl,      $expectedValue);
				$expectedValue = str_replace ('###INSTANCE_PROTOCOL###', $instanceProtocol, $expectedValue);
				$expectedValue = str_replace ('###INSTANCE_HOSTNAME###', $instanceHostname, $expectedValue);
				$expectedValue = str_replace ('###INSTANCE_QUERY###',    $instanceQuery,    $expectedValue);
				$expectedValue = str_replace ('###REQUEST_QUERY###',     $requestQuery,     $expectedValue);
				
					// find comparison mode
				if ( strpos( $expectedValue , '=') === 0 )  {
					$partialResult = ( $headerValue == trim( substr( $expectedValue , 1 ) ) );
				} else if ( strpos( $expectedValue , '<' ) === 0 ) {
					$partialResult = ( intval( $headerValue ) < intval( substr( $expectedValue , 1 ) ) );
				} else if ( strpos( $expectedValue , '>' ) === 0 ) {
					$partialResult = ( intval( $headerValue ) > intval( substr( $expectedValue , 1 ) ) );
				} else {
					$partialResult = ( $headerValue == $expectedValue );
				}
			}

			if ($partialResult == FALSE) {
				$result = FALSE;
			}
		}

		return $result;
		
	}
	
	/**
	 * Get the maximal time befor WARNING 
	 * @return integer
	 */
	protected function getTimeWarning(){
		return intval($this->getConfigValue('max_time_warning'));
	}
	
	/**
	 * Get the maximal time before ERROR
	 * @return integer
	 */
	protected function getTimeError(){
		return intval($this->getConfigValue('max_time_error'));
	}
	
	/**
	 * Get the expected http status code from the test configuration
	 * @return integer
	 */
	protected function getExpectedReturnCode(){
		return intval($this->getConfigValue('expected_status'));
	}

	/**
	 * Get the expected headers from the test configuration
	 * @return array an associative Array with headers as key and expectec values as sting value
	 */
	protected function getExpectedHeaders(){
		$expectedHeaders = array();
		$expectedHeadersConfiguration = $this->getConfigValue('expected_headers');
		if ($expectedHeadersConfiguration){
			$configurationLines = explode (chr(10), $expectedHeadersConfiguration);
			foreach ($configurationLines as $configurationLine){
				list ($headerName, $headerValue) = explode (':',$configurationLine, 2);
				$headerName  = trim($headerName);
				$headerValue = trim($headerValue);
				if ($headerName && $headerValue) {
					$expectedHeaders[$headerName]= $headerValue;
				}
			}
		}
		return $expectedHeaders;
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
	 * @return string
	 */
	protected function getInstanceUrl(){
		return $this->instance->getUrl();
	}

	/**
	 *
	 * @return string
	 */
	protected function getInstanceHostname(){
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
		$starttime=microtime(TRUE);
		
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
		
		curl_setopt($curl, CURLOPT_HEADER, TRUE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

			// execute request
		$response = curl_exec($curl);
		$info     = curl_getinfo($curl);
		curl_close($curl);

		list ($headerText, $content) = preg_split( '/\n[\s]*\n/', $response, 2);

			// split headers
		$headers = array();
		if ($headerText){
			$headerLines = explode (chr(10), $headerText);
			foreach ($headerLines as $headerLine){
				list ($headerName, $headerValue) = explode (':',$headerLine, 2);
				$headerName  = trim($headerName);
				$headerValue = trim($headerValue);
				if ($headerName && $headerValue) {
					$headers[$headerName]= $headerValue;
				}
			}
		}

		$endtime=microtime(TRUE);
		$time = ($endtime-$starttime)*1000;
		
		return array ( $time, $response, $info, $headers );
	}
}

?>
