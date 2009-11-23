<?php

class tx_caretaker_AggregatorResult_testcase extends tx_phpunit_testcase  {
	
	function test_comparisonOfAggreagtorResults (){
		
		$result = tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_UNDEFINED);

		$compareResult =  tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_UNDEFINED);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty undefined results should be equal' );

		$compareResult =  tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_OK);
		$this->assertTrue( $result->isDifferent($compareResult) , 'result with other state is not equal');

		$compareResult =  tx_caretaker_AggregatorResult::create( TX_CARETAKER_STATE_UNDEFINED , 0 , 0 , 0 , 0 );
		$this->assertTrue( $result->equals($compareResult) , 'result with state undefined and all errorNumbers 0 is equal to empty result');

		$compareResult =  tx_caretaker_AggregatorResult::create( TX_CARETAKER_STATE_UNDEFINED , 1 , 0 , 0 , 0 );
		$this->assertFalse( $result->equals($compareResult), 'result with state undefined and but numUndefined = 1 is not equal to empty result' );

		$compareResult =  tx_caretaker_AggregatorResult::create( TX_CARETAKER_STATE_UNDEFINED , 0 , 1 , 0 , 0 );
		$this->assertFalse( $result->equals($compareResult), 'result with state undefined and but numOK = 1 is not equal to empty result' );

		$compareResult =  tx_caretaker_AggregatorResult::create( TX_CARETAKER_STATE_UNDEFINED , 0 , 0 , 1 , 0 );
		$this->assertFalse( $result->equals($compareResult), 'result with state undefined and but numWarning = 1 is not equal to empty result' );

		$compareResult =  tx_caretaker_AggregatorResult::create( TX_CARETAKER_STATE_UNDEFINED , 0 , 0 , 0 , 1 );
		$this->assertFalse( $result->equals($compareResult), 'result with state undefined and but numError = 1 is not equal to empty result' );
		
	}
	
}

?>