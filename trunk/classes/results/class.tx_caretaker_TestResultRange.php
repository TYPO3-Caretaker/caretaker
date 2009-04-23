<?php 

require_once(t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResultRange.php');


class tx_caretaker_TestResultRange extends tx_caretaker_NodeResultRange {
	
	var $min = 0;
	var $max = 0;
	
	function addResult($result ){
		
		parent::addResult($result);
		
		$val = $result->getValue();
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