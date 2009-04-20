<?php 

require_once('class.tx_caretaker_NodeResultRange.php');


class tx_caretaker_AggregatorResultRange extends tx_caretaker_NodeResultRange {
	
	var $min = 0;
	var $max = 0;
	
	function addResult($result ){
		
		parent::addResult($result);
		
		$val = $result->getNumUNDEFINED() + $result->getNumOK()+  $result->getNumWARNING()+  $result->getNumERROR() ;
		if ($val < $this->min){
			$this->min = $val;
		} else if ($val > $this->max){
			$this->max = $val;
		}
		
	}
	
	function getMinValue(){
		return $this->min;
	}
	
	function getMaxValue(){
		return $this->max;
	}
			
}

?>