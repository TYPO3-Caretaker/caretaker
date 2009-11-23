<?php 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_TestResultRepository.php');

class tx_caretaker_TestResultRepository_testcase extends tx_phpunit_testcase  {

	function test_getLatest(){
		$instance = new tx_caretaker_InstanceNode(1, 'title' , false, '', '');
		$test     = new tx_caretaker_TestNode(0, 'title' , $instance, 'tx_caretaker_ping' , '' );
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		$result  =  $test_result_repository->getLatestByNode($test);
		$this->assertEquals( get_class($result), 'tx_caretaker_TestResult' , 'a testresult was found');
	}
	
	function test_getResultRange(){

		$instance = new tx_caretaker_InstanceNode(1, 'title', false, '');
		$test     = new tx_caretaker_TestNode(0, 'title' , $instance, 'tx_caretaker_ping', '' );
		
		$test_result_repository = tx_caretaker_TestResultRepository::getInstance();
		
		$result_range  =  $test_result_repository->getRangeByNode( $test, time()-10000, time() );
		$this->assertNotNull( count($result_range), 'there are tests found in range');
		
	}
	
}

?>