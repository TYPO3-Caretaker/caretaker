<?php 

class tx_caretaker_TestResultRange implements Iterator {
	
	private $position = 0;
	var $array = array();
	var $min = 0;
	var $max = 0;
	var $len = 0;
	var $ts_min = 0;
	var $ts_max = 0;
	
	var $seconds_ok = 0;
	var $seconds_undefined = 0;
	var $seconds_warning = 0;
	var $seconds_error = 0;
	
	public function __construct($ts_min=0, $ts_max=0) {
		$this->position = 0;
		$this->ts_min = $ts_min;
		$this->ts_max = $ts_max;
	}

	function addResult($result){
		$this->array[]=$result;	
		$this->len ++;
		
		$val = $result->getValue();
		if ($val < $this->min){
			$this->min = $val;
		} else if ($val > $this->max){
			$this->max = $val;
		}
		
		$ts = $result->getTstamp();
		if ($this->ts_min > 0 && $ts < $this->ts_min ){
			$this->ts_min  = $ts;
		} else if ($ts > $this->ts_max){
			$this->ts_max = $ts;
		}
		
		if ( $this->len > 1 ) {
			$last_state = $this->array[$this->len-2]->getState();
			$time_range = $result->getTstamp() - $this->array[$this->len-2]->getTstamp();
			switch ($last_state) {
				case TX_CARETAKER_STATE_OK : 
					$this->seconds_ok += $time_range;
					break;
				case TX_CARETAKER_STATE_UNDEFINED : 
					$this->seconds_undefined += $time_range;
					break;
				case TX_CARETAKER_STATE_WARNING : 
					$this->seconds_warning += $time_range;
					break;
				case TX_CARETAKER_STATE_ERROR : 
					$this->seconds_error += $time_range;
					break;	
			}
		} 
		
		
	}
	
		// get time infos
	function getSecondsTotal(){
		return $this->ts_max - $this->ts_min;
	}
	
	function getSecondsOk(){
		return $this->seconds_ok;
	}
	
	function getSecondsUndefined(){
		return $this->seconds_undefined;
	}
	
	function getSecondsError(){
		return $this->seconds_error;
	}
	
	function getSecondsWarning(){
		return $this->seconds_warning;
	}
	
		// State Infos
	function getAvailability(){
		$total = $this->getSecondsTotal();
		return ($total - ($this->getSecondsUndefined()+ $this->getSecondsWarning()+$this->getSecondsError() ) ) / $total;
	}
		
	function getPercentOk(){
		return $this->getSecondsOk() / $this->getSecondsTotal();
	}
	function getPercentUndefined(){
		return $this->getSecondsUndefined() / $this->getSecondsTotal();
	}
	function getPercentWarning(){
		return $this->getSecondsWarning() / $this->getSecondsTotal();
	}
	function getPercentError(){
		return $this->getSecondsError() / $this->getSecondsTotal();
	}
	
		// general Infos
	function getLength(){
		return $this->len;
	}
	
	function getMinValue(){
		return $this->min;
	}
	
	function getMaxValue(){
		return $this->max;
	}
	
	function getMinTstamp(){
		return $this->ts_min;
	}
	
	function getMaxTstamp(){
		return $this->ts_max;
	}
	
	
	function rewind() {
		$this->position = 0;
	}

	function current() {
		return $this->array[$this->position];
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset($this->array[$this->position]);
	}
	
	function getAggregatedTestResult(){
		
		$num_tests = count($this->array);
		$num_undefined = 0;
		$num_errors	= 0;
		$num_warnings  = 0;
		
		for($i= 0 ; $i < $num_tests; $i++ ){
			switch ( $this->array[$i]->getState() ){
				case TX_CARETAKER_STATE_ERROR:
					$num_errors ++;
					break;
				case TX_CARETAKER_STATE_WARNING:
					$num_warnings ++;
					break;
				case TX_CARETAKER_STATE_UNDEFINED:
					$num_undefined ++;
					break;
			}
		}
		
		$undefined_info = '';
		if ($num_undefined > 0){
			$undefined_info = ' ['.$num_undefined.' results are in undefined state ]';
		} 
		
		if  ($num_errors > 0){
			$aggregated_state = tx_caretaker_TestResult::restore($this->ts_max, TX_CARETAKER_STATE_ERROR, $num_tests-$num_errors-$num_warnings, $num_errors.' errors and '.$num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info );
		} else if ($num_warnings > 0){
			$aggregated_state = tx_caretaker_TestResult::restore($this->ts_max, TX_CARETAKER_STATE_WARNING,$num_tests-$num_warnings, $num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info);
		} else {
			$aggregated_state = tx_caretaker_TestResult::restore($this->ts_max, TX_CARETAKER_STATE_OK,$num_tests, $num_tests.' results are OK'.$undefined_info);
		}
		
		return $aggregated_state;
	}
	
}

?>