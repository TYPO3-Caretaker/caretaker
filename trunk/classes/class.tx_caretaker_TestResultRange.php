<?php 

class tx_caretaker_TestResultRange implements Iterator {
	
  	private $position = 0;
    private $array = array();
    var $min = 0;
    var $max = 0;
	var $len = 0;
	var $ts_min = 0;
	var $ts_max = 0;
	
    public function __construct() {
        $this->position = 0;
    }

    function addResult($result){
    	$this->array[]=$result;	
    	
    	$val = $result->getValue();
    	if ($val < $this->min){
    		$this->min = $val;
    	} else if ($val > $this->max){
    		$this->max = $val;
    	}
    	
    	$ts = $result->getTstamp();
    	if ($this->ts_min > 0 && $val < $this->ts_min ){
    		$this->ts_min  = $ts;
    	} else if ($val > $this->ts_max){
    		$this->ts_max = $ts;
    	}
    	 
    	$this->len ++;
    }
    
    function getMinValue(){
    	return $this->min;
    }
    
    function getMaxValue(){
    	return $this->max;
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
	
	function getAggregatedState(){
		
		$num_tests = count($this->array);
		$num_undefined = 0;
		$num_errors    = 0;
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
			$aggregated_state = tx_caretaker_TestResult::restore($this->ts_max, TX_CARETAKER_STATE_ERROR,$num_tests-$num_errors-$num_warnings, $num_errors.' errors and '.$num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info );
		} else if ($num_warnings > 0){
			$aggregated_state = tx_caretaker_TestResult::restore($this->ts_max, TX_CARETAKER_STATE_WARNING,$num_tests-$num_warnings, $num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info);
		} else {
			$aggregated_state = tx_caretaker_TestResult::restore($this->ts_max, TX_CARETAKER_STATE_OK,$num_tests, $num_tests.' results are OK'.$undefined_info);
		}
		
		return $aggregated_state;
	}
	
}

?>