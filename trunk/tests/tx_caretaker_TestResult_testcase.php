<?php

class tx_caretaker_TestResult_testcase extends tx_phpunit_testcase  {
	
	function test_comparisonOfTestResults (){
		
		$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined);

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty undefined results should be equal' );

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		$this->assertTrue( $result->isDifferent($compareResult) , 'result with other state is not equal');

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined, 0 );
		$this->assertTrue( $result->equals( $compareResult ), 'default is undefined state and value 0' );

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined, 1 );
		$this->assertTrue( $result->isDifferent( $compareResult ), 'value 1 is different from 0' );


		$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty OK results should be equal' );

		$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_warning);
		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_warning);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty WARNING results should be equal' );

				$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error);
		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty ERROR results should be equal' );


	}
	
}

?>