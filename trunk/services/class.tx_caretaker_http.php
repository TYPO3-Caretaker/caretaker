<?php

require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_http extends tx_caretaker_TestServiceBase {
		
	
	function runTest() {
		
		$time_warning  = $this->getConfigValue('max_time_warning');
		$time_error    = $this->getConfigValue('max_time_error');
		$expected_code = $this->getConfigValue('expected_status');
		$request_query = $this->getConfigValue('request_query');
		
	/*	if ( !=$this->instance->getHost() ){
			
		} 
	*/
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