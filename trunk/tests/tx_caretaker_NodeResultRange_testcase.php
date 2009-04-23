<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResultRange.php');

class tx_caretaker_NodeResultRange_testcase extends tx_phpunit_testcase  {
	
	function test_AddingOfResults(){
		$range = new tx_caretaker_NodeResultRange();
		$this->assertEquals($range->getLength() , 0);
		
		$res_1 =  new tx_caretaker_NodeResult(123, 0);
		$range->addResult($res_1);
		$this->assertEquals($range->getLength() , 1);
		
		$res_2 =  new tx_caretaker_NodeResult(456, 1);
		$range->addResult($res_2);
		$this->assertEquals($range->getLength()  , 2);
		
		$res_3 =  new tx_caretaker_NodeResult(789, 2);
		$range->addResult($res_3);
		$this->assertEquals($range->getLength() ,  3);
		
	}
	
	function test_getFirstAndGetLastResults(){
		$range = new tx_caretaker_NodeResultRange();
		
		$res_1 =  new tx_caretaker_NodeResult(456, 1);
		$res_2 =  new tx_caretaker_NodeResult(123, 0);
		$res_3 =  new tx_caretaker_NodeResult(789, 2);
		$res_4 =  new tx_caretaker_NodeResult(678, 2);
		
		$range->addResult($res_1);
		$range->addResult($res_2);
		$range->addResult($res_3);
		$range->addResult($res_4);
		
		debug($range);
		
		$this->assertEquals( $range->getFirst()->getTstamp(), 123 );
		$this->assertEquals( $range->getLast()->getTstamp(),  789 );
		
	}
	
	function test_MinMaxTstamp(){
		
		$range = new tx_caretaker_NodeResultRange(100, 600);
		
		$this->assertEquals( $range->getMinTstamp(), 100 );
		$this->assertEquals( $range->getMaxTstamp(), 600 );
		
		$res_1 =  new tx_caretaker_NodeResult(456, 1);
		$range->addResult($res_1);
		
		$this->assertEquals( $range->getMinTstamp(), 100 );
		$this->assertEquals( $range->getMaxTstamp(), 600 );
		
		$res_2 =  new tx_caretaker_NodeResult(789, 2);
		$res_3 =  new tx_caretaker_NodeResult(50,  2);
		
		$range->addResult($res_2);
		$range->addResult($res_3);
		
		$this->assertEquals( $range->getMinTstamp(), 50 );
		$this->assertEquals( $range->getMaxTstamp(), 789 );
				
	}
	
		
}
?>