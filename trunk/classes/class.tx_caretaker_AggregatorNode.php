<?php 

require_once ('class.tx_caretaker_Node.php');
require_once ('class.tx_caretaker_AggregatorResultRepository.php');


abstract class tx_caretaker_AggregatorNode extends tx_caretaker_Node {

	protected $children = NULL;
	
	public function getChildren(){
		if ($this->children === NULL){
			$this->children = $this->findChildren();
		}
		return $this->children;
	}
	
	protected function findChildren(){
		return array();
	}
	
	function updateTestResult( $force_update = false){
		
		$this->log('update', 1);
		
		$children  = $this->getChildren();

		if (count($children)>0){		
			$test_results = new tx_caretaker_TestResultRange(); 
			foreach($children as $child){
				$test_result = $child->updateTestResult($force_update);
				$test_results->addResult($test_result);
			}
			$group_result = $test_results->getAggregatedTestResult();
		} else {
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_UNDEFINED, 0, 'No children were found');
		}
		
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		
			// save aggregator node state to cache
		// $last_result = $this->getTestResult();
		// if ($last_result->getState() != $group_result->getState() || $last_result->getValue() != $group_result->getValue() ){
			$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
			$result_repository->addNodeResult($this, $group_result);
		// }
		
		return $group_result;
	}
		
	function getTestResult(){
		
		$this->log( 'get', 1 );
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$group_result = $result_repository->getLatestByNode($this);
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		return $group_result;
		
		/*
		$this->log( 'get', 1 );
		
			// read aggregartor node state from cache
			
		$children  = $this->getChildren();
		$test_results = new tx_caretaker_TestResultRange(); 
		
		foreach($children as $child){ 
			$test_result = $child->getTestResult($force_update);
			$test_results->addResult($test_result);
		}
		
		$group_result = $test_results->getAggregatedTestResult();
			
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		
		return $group_result;
		*/
		
	}
	
	
	function getTestResultRange($startdate, $stopdate , $distance=FALSE){
		
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$group_results = $result_repository->getRangeByNode($this, $startdate, $stopdate);
		return $group_results;
		
	}
	
}

?>