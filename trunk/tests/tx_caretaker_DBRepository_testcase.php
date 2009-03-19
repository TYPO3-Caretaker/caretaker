<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_InstanceRepository.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_GroupRepository.php');

class tx_caretaker_DBRepository_testcase extends tx_phpunit_testcase  {
	
	protected function setUp() {
		
	}
	
	protected function tearDown() {
		
	}
	
	function test_instance_repository(){
		$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
		$all_instances = $instance_repoistory->getAll();
		$this->assertNotNull(count($all_instances), 'there are instances present');

		$target = $all_instances[count($all_instances)-1];
		$test   = $instance_repoistory->getByUid( $target->getUid() );
		$this->assertEquals( $target, $test, 'instance found by id');
		
		$target = $all_instances[0];
		$test   = $instance_repoistory->getByUid( $target->getUid() );
		$this->assertEquals( $target, $test, 'instance found by id');

	}
	
	function test_group_repository(){
		$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
		$all_instances = $instance_repoistory->getAll();
		$test_instance = $all_instances[0];
		
		$groups = $test_instance->getGroups();
		$this->assertGreaterThan(0 ,count($groups), 'there are no groups present');
		
		$subgroups = $groups[0]->getSubgroups();
		$this->assertGreaterThan(0 ,count($subgroups), 'there are no subgroups found');
		
		$groups_recursive = $test_instance->getGroupsRecursive();
		$this->assertGreaterThan(count($groups) ,count($groups_recursive), 'there are more groups found if the search is done recursive');

	}	

	function test_test_repository(){
		$instance_repoistory = tx_caretaker_InstanceRepository::getInstance();
		$all_instances = $instance_repoistory->getAll();
		$test_instance = $all_instances[0];
		
		$tests = $test_instance->getTestsRecursive();
		$this->assertGreaterThan(0 ,count($tests), 'there are no tests present');
		$this->assertEquals(get_class($tests[0]) , 'tx_caretaker_Test', 'the first result is not a test object');
		
		$tests = $test_instance->getPendingTests();
		
	}
	
	
}
?>