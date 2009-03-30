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
		$instance = new tx_caretaker_Instance(9999, 'instance' ,false, $conf['host'], $conf['ip']);
		$test     = new tx_caretaker_Test(9999, 'http-test', $instance, 'tx_caretaker_http', $conf );
		$result   = $test->runTest();
		$this->assertEquals( $result->getState(), TX_CARETAKER_STATE_OK, 'State was not OK' );
	}
	
	function test_hhtp_test_error(){
		$conf = $this->splitTestConf($this->http_test_error);
		$instance = new tx_caretaker_Instance(9997, 'instance' ,false, $conf['host'], $conf['ip']);
		$test     = new tx_caretaker_Test(9997, 'http-test' , $instance,'tx_caretaker_http', $conf );
		$result   = $test->runTest();
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