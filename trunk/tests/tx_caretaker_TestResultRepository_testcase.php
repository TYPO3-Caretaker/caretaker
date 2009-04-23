<?php 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_TestResultRepository.php');

class tx_caretaker_TestResultRepository_testcase extends tx_phpunit_testcase  {

	function test_getLatest(){

		$test     = new tx_caretaker_Test(0, 'title' , false, '' , '' );
		$instance = new tx_caretaker_Instance(1, 'title' , false, '', ''); 
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result  =  $test_result_repository->getLatestByInstanceAndTest($instance, $test);
		$this->assertEquals( get_class($result), 'tx_caretaker_TestResult' , 'a testresult was found');
	}
	
	function test_getResultRange(){

		$test     = new tx_caretaker_Test(0, 'title' , false, '', '' );
		$instance = new tx_caretaker_Instance(1, 'title', false, ''); 
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		
		$result_range  =  $test_result_repository->getRangeByInstanceAndTest($instance, $test, time()-10000, time() );
		$this->assertNotNull( count($result_range), 'there are tests found in range');
		
	}
	
}

?>