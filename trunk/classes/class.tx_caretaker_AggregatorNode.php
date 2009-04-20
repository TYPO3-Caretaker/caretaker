<?php 

require_once ('class.tx_caretaker_Node.php');
require_once ('class.tx_caretaker_AggregatorResultRepository.php');


abstract class tx_caretaker_AggregatorNode extends tx_caretaker_Node {

	protected $children = NULL;
	
	public function getChildren($show_hidden=false){
		if ($this->children === NULL){
			$this->children = $this->findChildren($show_hidden);
		}
		return $this->children;
	}
	
	protected function findChildren($hidden=false){
		return array();
	}
	
	function updateTestResult( $force_update = false){
		
		$this->log('update', 1);
		
		$children  = $this->getChildren();

		if (count($children)>0){		
			$test_results = array(); 
			foreach($children as $child){
				$test_result = $child->updateTestResult($force_update);
				$test_results[] = $test_result;
			}
			$group_result = $this->getAggregatedResult($test_results);
		} else {
			$group_result = tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_UNDEFINED, 0, 'No children were found');
		}
		
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		
			// save aggregator node state to cache
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$result_repository->addNodeResult($this, $group_result);
		
		if ($group_result->getState() > 0){
			$this->sendNotification($group_result->getState(), $group_result->getMsg() );
		} 
		
		return $group_result;
	}
		
	function getTestResult(){

			// read aggregator node state from cache
		$this->log( 'get', 1 );
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$group_result = $result_repository->getLatestByNode($this);
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		return $group_result;
		
	}
	
	
	function getTestResultRange($startdate, $stopdate , $distance=FALSE){
		
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$group_results = $result_repository->getRangeByNode($this, $startdate, $stopdate);
		return $group_results;
		
	}
	
	function getAggregatedResult($test_results){
		
		$num_tests = count($test_results);
		$num_undefined = 0;
		$num_errors	= 0;
		$num_ok	= 0;
		$num_warnings  = 0;
		
		for($i= 0 ; $i < $num_tests; $i++ ){
			switch ( $test_results[$i]->getState() ){
				case TX_CARETAKER_STATE_OK:
					$num_ok ++;
					break;
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
		
		$ts = time();
		if  ($num_errors > 0){
			$state = TX_CARETAKER_STATE_ERROR;
			$msg   = $num_errors.' errors and '.$num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info;
		} else if ($num_warnings > 0){
			$state = TX_CARETAKER_STATE_WARNING;
			$msg   = $num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info;
		} else {
			$state = TX_CARETAKER_STATE_OK;
			$msg   = $num_tests.' results are OK'.$undefined_info;
		}
		$aggregated_state = tx_caretaker_AggregatorResult::create($state,$num_undefined,$num_ok,$num_warnings,$num_errors,$msg);
		
		return $aggregated_state;
	}
	
}

?>