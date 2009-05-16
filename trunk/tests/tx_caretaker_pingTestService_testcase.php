<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_TestNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_InstanceNode.php');

class tx_caretaker_pingTestService_testcase extends tx_phpunit_testcase  {
	
	var $ping_test_ok;
	var $ping_test_error;
	
	function setUp(){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->ping_test_ok      = $confArray['unittest.']['ping_ok'];
		$this->ping_test_warning = $confArray['unittest.']['ping_warning'];
		$this->ping_test_error   = $confArray['unittest.']['ping_error'];
	
	}

	function test_ping_ok(){
		$conf = $this->splitTestConf($this->ping_test_ok);
		
		$instance = new tx_caretaker_InstanceNode(9996, 'instance' , false, '' , $conf['host'], $conf['ip']);
		$test     = new tx_caretaker_TestNode(9996,'ping-test', $instance, 'tx_caretaker_ping', $conf );
		$result   = $test->runTest();
		
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );		
		
	}
	
	function test_ping_warning(){
		$conf = $this->splitTestConf($this->ping_test_warning);
		
		$instance = new tx_caretaker_InstanceNode(9995,  'instance' , false, '', $conf['host'], $conf['ip']);
		$test     = new tx_caretaker_TestNode(9995,'ping-test', $instance, 'tx_caretaker_ping', $conf );
		$result   = $test->runTest();
		
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_WARNING, 'State was not OK' );		
		
	}
	
	function test_ping_error(){
		$conf = $this->splitTestConf($this->ping_test_error);
		$instance = new tx_caretaker_InstanceNode(9994,  'instance' , false,'' ,$conf['host'], $conf['ip']);
		$test     = new tx_caretaker_TestNode(9994,'ping-test',$instance, 'tx_caretaker_ping', $conf );
		$result   = $test->runTest();
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_ERROR, 'State was not OK' );		
		
	}

	function splitTestConf($conf){
		list($host, $port, $time_warning, $time_error) = explode(':',$conf);
		return ( array(
			'host'=>$host,
			'ip'=>'',
			'port'   =>$port,
			'max_time_warning' => $time_warning,
			'max_time_error'=> $time_error
		));
	}	
	
}

?>