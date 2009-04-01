<?php 

require_once ('class.tx_caretaker_Node.php');

abstract class tx_caretaker_AggregatorNode extends tx_caretaker_Node {

	protected $children;
	
	abstract function getChildren();
	
	function updateTestResult( $force_update = false){
		
		$this->log('update', 1);
		
		$children  = $this->getChildren();
		$test_results = new tx_caretaker_TestResultRange(); 
		
		foreach($children as $child){
			$test_result = $child->updateTestResult($force_update);
			$test_results->addResult($test_result);
		}
	
		$group_result = $test_results->getAggregatedTestResult();
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getComment(), false );
		
		return $group_result;
	}
		
	function getTestResult(){
		
		$this->log( 'get', 1 );
		
		$children  = $this->getChildren();
		$test_results = new tx_caretaker_TestResultRange(); 
		
		foreach($children as $child){ 
			$test_result = $child->getTestResult($force_update);
			$test_results->addResult($test_result);
		}
		
		$group_result = $test_results->getAggregatedTestResult();
			
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getComment(), false );
		
		return $group_result;
	}
	
	
	function getTestResultRange($startdate, $stopdate){
		
		$test_results = new tx_caretaker_TestResultRange(); 
		return $test_results;
		
	}
	
}

?>