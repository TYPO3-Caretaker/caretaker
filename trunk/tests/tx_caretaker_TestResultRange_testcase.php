<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRange.php');

class tx_caretaker_TestResultRange_testcase extends tx_phpunit_testcase  {

	var $test_result_range;
	
	function setUp(){
		
		$this->test_result_range = new tx_caretaker_TestResultRange(500, 1000);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(500, TX_CARETAKER_STATE_OK, 0, '')
		);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(600, TX_CARETAKER_STATE_ERROR, 0, '')
		);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(650, TX_CARETAKER_STATE_OK, 0, '')
		);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(800, TX_CARETAKER_STATE_WARNING, 0, '')
		);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(900, TX_CARETAKER_STATE_OK, 0, '')
		);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(930, TX_CARETAKER_STATE_UNDEFINED, 0, '')
		);
		
		$this->test_result_range->addResult(
			tx_caretaker_TestResult::restore(950, TX_CARETAKER_STATE_OK, 0, '')
		);
		
	}
	
	function test_get_state_infos(){
		
		$this->assertEquals( $this->test_result_range->getSecondsTotal(), 500 );
		$this->assertEquals( $this->test_result_range->getSecondsOK(), 280 );
		$this->assertEquals( $this->test_result_range->getSecondsUndefined(), 20 );
		$this->assertEquals( $this->test_result_range->getSecondsError(), 50 );
		$this->assertEquals( $this->test_result_range->getSecondsWarning(), 100 );
		
	}
	
	function test_get_availability_infos(){
		
		$this->assertEquals( $this->test_result_range->getPercentOK(), 280/500 );
		$this->assertEquals( $this->test_result_range->getPercentError(), 50/500 );
		$this->assertEquals( $this->test_result_range->getPercentWarning(), 100/500 );
		$this->assertEquals( $this->test_result_range->getPercentUndefined(), 20/500 );
		$this->assertEquals( $this->test_result_range->getAvailability(), 330/500 );
		
		
	}
	
	function test_get_length(){
		
		$this->assertEquals( $this->test_result_range->getLength(), 7 );
		
	}
	
	
	
}
?>