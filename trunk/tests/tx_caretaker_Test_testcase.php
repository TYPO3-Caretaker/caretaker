<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Test.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Instance.php');

class tx_caretaker_Test_testcase extends tx_phpunit_testcase  {
	
	protected function setUp() {
		
	}
	
	protected function tearDown() {
		
	}

	function test_dummy_test_running(){
		
		$instance = new tx_caretaker_Instance(1, 'localhost', '127.0.0.1');
		$test     = new tx_caretaker_Test(0, 'tx_caretaker_dummy', array() );
		$result   = $test->runTest($instance);
		
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );
		$this->assertEquals( $result->getValue(), 9.18 , 'Value was not expected' );
		$this->assertEquals( $result->getComment(), 'foobar' , 'comment was not foobar' );

	}
	
	function test_ping_test_running(){
		
		$test     = new tx_caretaker_Test(0, 'tx_caretaker_ping', array('port'=>80, 'max_time_warning'=>1000, 'max_time_error'=>2000) );
		
		$instance = new tx_caretaker_Instance(1, 'localhost', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );
				
		$instance = new tx_caretaker_Instance(1, 'spiegel.de', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );		
		
		$instance = new tx_caretaker_Instance(1, 'dasddlkallkjlaskjdals.de', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_ERROR, 'State was not OK' );		
		
	}
	
	function test_hhtp_test_running(){
		
		$test     = new tx_caretaker_Test(0, 'tx_caretaker_http', array('request_query'=>'/', 'expected_status'=>200 ) );
		$instance = new tx_caretaker_Instance(1, 'localhost', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );

		$test     = new tx_caretaker_Test(0, 'tx_caretaker_http', array('request_query'=>'/this/path/does/not/exist', 'expected_status'=>404 ) );
		$instance = new tx_caretaker_Instance(1, 'localhost', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );
		
		$test     = new tx_caretaker_Test(0, 'tx_caretaker_http', array('request_query'=>'/this/path/does/not/exist', 'expected_status'=>300 ) );
		$instance = new tx_caretaker_Instance(1, 'localhost', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_ERROR, 'State was not OK' );
		
	}
	

	function test_ping_from_db_repository(){
		$test_repository = tx_caretaker_TestRepository::getInstance();
		$test = $test_repository->getByUid('30');
		
		$instance = new tx_caretaker_Instance(1, 'localhost', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );
				
		$instance = new tx_caretaker_Instance(1, 'spiegel.de', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_WARNING, 'State was not OK' );		
		
		$instance = new tx_caretaker_Instance(1, 'dasddlkallkjlaskjdals.de', '127.0.0.1');
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_ERROR, 'State was not OK' );
		
	}
	
}

?>