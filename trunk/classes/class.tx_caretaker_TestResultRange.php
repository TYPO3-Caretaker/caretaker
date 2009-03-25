<?php 

class tx_caretaker_TestResultRange implements Iterator {
	
  	private $position = 0;
    private $array = array();
    var $min = 0;
    var $max = 0;
	var $len = 0;
	
    public function __construct() {
        $this->position = 0;
    }

    function addResult($result){
    	$this->array[]=$result;	
    	
    	if ($result->getValue() < $this->min){
    		$this->min = $result->getValue();
    	}
    	
   		if ($result->getValue() > $this->max){
    		$this->max = $result->getValue();
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
	
	
	
}

?>