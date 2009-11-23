<?php

class tx_caretaker_TestResult_testcase extends tx_phpunit_testcase  {
	
	function test_comparisonOfTestResults (){
		
		$result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_UNDEFINED);

		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_UNDEFINED);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty undefined results should be equal' );

		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK);
		$this->assertTrue( $result->isDifferent($compareResult) , 'result with other state is not equal');

		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_UNDEFINED, 0 );
		$this->assertTrue( $result->equals( $compareResult ), 'default is undefined state and value 0' );

		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_UNDEFINED, 1 );
		$this->assertTrue( $result->isDifferent( $compareResult ), 'value 1 is different from 0' );


		$result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK);
		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty OK results should be equal' );

		$result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_WARNING);
		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_WARNING);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty WARNING results should be equal' );

				$result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR);
		$compareResult =  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty ERROR results should be equal' );


	}
	
}

?>