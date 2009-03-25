<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Test.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Instance.php');

class tx_caretaker_ping_testcase extends tx_phpunit_testcase  {
	
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
		$test     = new tx_caretaker_Test(9996, 'tx_caretaker_ping', $conf );
		$instance = new tx_caretaker_Instance(9996, $conf['host'], $conf['ip']);
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );		
		
	}
	
	function test_ping_warning(){
		$conf = $this->splitTestConf($this->ping_test_warning);
		$test     = new tx_caretaker_Test(9995, 'tx_caretaker_ping', $conf );
		$instance = new tx_caretaker_Instance(9995, $conf['host'], $conf['ip']);
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_WARNING, 'State was not OK' );		
		
	}
	
	function test_ping_error(){
		$conf = $this->splitTestConf($this->ping_test_error);
		$test     = new tx_caretaker_Test(9994, 'tx_caretaker_ping', $conf );
		$instance = new tx_caretaker_Instance(9994, $conf['host'], $conf['ip']);
		$result   = $test->runTest($instance);
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