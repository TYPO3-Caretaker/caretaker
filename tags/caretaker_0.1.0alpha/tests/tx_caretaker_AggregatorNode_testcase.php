<?php 

	/* create stub class to expose protected methods */
class tx_caretaker_AggregatorNode_Stub extends tx_caretaker_AggregatorNode {

	public function getCaretakerNodeId(){
		return "aggregator_node";
	}

	protected function findChildren($show_hidden=false){
		return array();
	}

	public function getAggregatedResult($results){
		return parent::getAggregatedResult($results);
	}
	
}

	/* this is the test */
class tx_caretaker_AggregatorNode_testcase extends tx_phpunit_testcase  {

	function test_aggregation_of_results(){

		$aggregator = new tx_caretaker_AggregatorNode_Stub( 0, 'foo', false );
		$instance   = new tx_caretaker_InstanceNode (0, 'bar', false );
		$node       = new tx_caretaker_TestNode (0, 'baz', $instance, 'tx_caretaker_ping' , '' );

		$results = array();
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( tx_caretaker_Constants::state_ok      ) );
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( tx_caretaker_Constants::state_ok      ) );
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( tx_caretaker_Constants::state_warning ) );
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( tx_caretaker_Constants::state_error   ) );
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( tx_caretaker_Constants::state_error   ) );
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( tx_caretaker_Constants::state_ok      ) );
		$results[] = array('node'=>$node , 'result'=> tx_caretaker_TestResult::create( ) );

		$aggregated_result = $aggregator->getAggregatedResult($results);
		$this->assertEquals( 2, $aggregated_result->getNumERROR() , "wrong error count" );
		$this->assertEquals( 1, $aggregated_result->getNumWARNING() , "wrong warning count" );
		$this->assertEquals( 3, $aggregated_result->getNumOK() , "wrong ok count" );
		$this->assertEquals( 1, $aggregated_result->getNumUNDEFINED() , "wrong undefined count" );

		$this->assertEquals( tx_caretaker_Constants::state_error, $aggregated_result->getState() , "wrong result" );
		
	}

	
	
}

?>