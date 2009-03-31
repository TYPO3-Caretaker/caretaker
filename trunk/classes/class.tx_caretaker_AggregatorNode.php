<?php 

require_once ('class.tx_caretaker_Node.php');

abstract class tx_caretaker_AggregatorNode extends tx_caretaker_Node {

	protected $children;
	
	abstract function getChildren();
	
	function updateState( $force_update = false){
		
		$this->log('update', 1);
		
		$children  = $this->getChildren();
		$test_results = new tx_caretaker_TestResultRange(); 
		
		foreach($children as $child){
			$test_result = $child->updateState($force_update);
			$test_results->addResult($test_result);
		}
	
		$group_result = $test_results->getAggregatedState();
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getComment(), false );
		
		return $group_result;
	}
		
	function getState(){
		
		$this->log( 'get', 1 );
		
		$children  = $this->getChildren();
		$test_results = new tx_caretaker_TestResultRange(); 
		
		foreach($children as $child){ 
			$test_result = $child->getState($force_update);
			$test_results->addResult($test_result);
		}
		
		$group_result = $test_results->getAggregatedState();
			
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getComment(), false );
		
		return $group_result;
	}
	
}

?>