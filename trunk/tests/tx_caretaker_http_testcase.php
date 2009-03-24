<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Test.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Instance.php');

class tx_caretaker_http_testcase extends tx_phpunit_testcase  {
	var $http_test_ok;
	var $http_test_error;
	
	function setUp(){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->http_test_ok      = $confArray['unittest.']['http_ok'];
		$this->http_test_error   = $confArray['unittest.']['http_error'];
	}
	
	function test_hhtp_test_ok(){
		$conf = $this->splitTestConf($this->http_test_ok);
		$test     = new tx_caretaker_Test(0, 'tx_caretaker_http', $conf );
		$instance = new tx_caretaker_Instance(1, $conf['host'], $conf['ip']);
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );
	}
	
	function test_hhtp_test_error(){
		$conf = $this->splitTestConf($this->http_test_error);
		$test     = new tx_caretaker_Test(0, 'tx_caretaker_http', $conf );
		$instance = new tx_caretaker_Instance(1, $conf['host'], $conf['ip']);
		$result   = $test->runTest($instance);
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_ERROR, 'State was not OK' );
	}
	
	
	function splitTestConf($conf){
		list($host, $query, $status) = explode(':',$conf);
		return ( array(
			'host'=>$host,
			'ip'=>'',
			'request_query'   =>$query,
			'expected_status' => $status
		));
	}
	
}

?>