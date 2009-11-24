<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_TestResult.php');


/**
 * 
 */
class tx_caretaker_NodeResult_testcase extends tx_phpunit_testcase  {
	
	function test_TestResult_stores_data (){
		$result = new tx_caretaker_TestResult(123, 1, 1.75 ,'This is a Message');

		$this->assertEquals($result->getTimestamp(), 123 );
		$this->assertEquals($result->getState(), 1 );
		$this->assertEquals($result->getStateInfo(), 'WARNING' );
		$this->assertEquals($result->getValue(), 1.75 );		
		$this->assertEquals($result->getMessage()->getText(), 'This is a Message' );
	}

	function test_AggregatorResult_stores_data (){
		$result = new tx_caretaker_AggregatorResult(123, 2, 2, 1, 3 , 5 ,'This is a Message');

		$this->assertEquals($result->getTimestamp(), 123 );
		$this->assertEquals($result->getState(), 2 );
		$this->assertEquals($result->getStateInfo(), 'ERROR' );
		$this->assertEquals($result->getMessage()->getText(), 'This is a Message' );
		
		$this->assertEquals($result->getNumUNDEFINED(), 2 );
		$this->assertEquals($result->getNumOK(), 1 );		
		$this->assertEquals($result->getNumWARNING(), 3 );		
		$this->assertEquals($result->getNumERROR(), 5 );		
		
		
	}
	
}

?>