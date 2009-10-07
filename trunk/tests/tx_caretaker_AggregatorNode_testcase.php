<?php 


require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_AggregatorResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResult.php');
require_once(t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_NodeRepository.php');

class tx_caretaker_AggregatorNode_testcase extends tx_phpunit_testcase  {

	function test_aggregation_of_results(){
		
		$aggregator = new tx_caretaker_InstancegroupNode( 0, 'foo', false );

		$results = array();
		$results[] = tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK      );
		$results[] = tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK      );
		$results[] = tx_caretaker_TestResult::create( TX_CARETAKER_STATE_WARNING );
		$results[] = tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR   );
		$results[] = tx_caretaker_TestResult::create( TX_CARETAKER_STATE_ERROR   );
		$results[] = tx_caretaker_TestResult::create( TX_CARETAKER_STATE_OK      );
		$results[] = tx_caretaker_TestResult::create( );


		$aggregated_result = $aggregator->getAggregatedResult($results);
		$this->assertEquals( 2, $aggregated_result->getNumERROR() , "wrong error count" );
		$this->assertEquals( 1, $aggregated_result->getNumWARNING() , "wrong warning count" );
		$this->assertEquals( 3, $aggregated_result->getNumOK() , "wrong ok count" );
		$this->assertEquals( 1, $aggregated_result->getNumUNDEFINED() , "wrong undefined count" );

		$this->assertEquals( TX_CARETAKER_STATE_ERROR, $aggregated_result->getState() , "wrong result" );
		
	}
	
}

?>