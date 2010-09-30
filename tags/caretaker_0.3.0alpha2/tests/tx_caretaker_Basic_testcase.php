<?php 

class tx_caretaker_Base_testcase extends tx_phpunit_testcase  {

	function test_services_are_present(){
		global $T3_SERVICES;
		$this->assertNotNull(count($T3_SERVICES['caretaker_test_service']), 'No testservices are present');
	}
	
}

?>