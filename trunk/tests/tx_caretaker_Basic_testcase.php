<?php 

class tx_caretaker_Base_testcase extends tx_phpunit_testcase  {
	
	/*
	protected function setUp() {
		
	}
	
	protected function tearDown() {
		
	}
	*/

	function test_services_are_present(){
		global $T3_SERVICES;
		$this->assertNotNull(count($T3_SERVICES['caretaker_test_service']), 'No testservices are present');
	}
	
	function test_dummy_testService_is_present(){
		global $T3_SERVICES;
		$this->assertArrayHasKey('tx_caretaker_dummy', $T3_SERVICES['caretaker_test_service'], 'Dummy Testservice is present');
		
		$service_infos =  t3lib_extMgm::findService('caretaker_test_service', 'tx_caretaker_dummy');
		$this->assertNotNull($service_infos, 'Service was not found');
		
		$test_service = t3lib_div::makeInstanceService('caretaker_test_service','tx_caretaker_dummy');
		$this->assertEquals(get_class($test_service), 'tx_caretaker_TestDummy' , 'test Object was not of class tx_caretaker_TestDummy');
		
	}
	
}

?>