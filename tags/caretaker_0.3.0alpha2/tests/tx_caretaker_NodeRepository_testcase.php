<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_NodeRepository.php');

class tx_caretaker_NodeRepository_testcase extends tx_phpunit_testcase  {
	private $repository;
	
	protected function setUp() {
		$this->repository    = tx_caretaker_NodeRepository::getInstance();
	}
	
	protected function tearDown() {
		
	}
	
	function test_instance_repository(){
		$all_instances = $this->repository->getAllInstances();
		$this->assertNotNull(count($all_instances), 'there are instances present');

		$target = $all_instances[count($all_instances)-1];
		$test   = $this->repository->getInstanceByUid( $target->getUid() );
		$this->assertEquals( $target, $test, 'instance found by id');

		$target = $all_instances[0];
		$test   = $this->repository->getInstanceByUid( $target->getUid() );
		$this->assertEquals( $target, $test, 'instance found by id');

	}
	
	function test_testgroup_repository(){
		$all_instances = $this->repository->getAllTestgroups();
		$test_instance = $all_instances[0];
		$groups = $test_instance->getChildren();
		$this->assertGreaterThan(0 ,count($groups), 'there are no groups present');
		
	}	
	
	function test_instancegroup_repository(){
		$all_instancegroups = $this->repository->getAllInstancegroups();
		$test_instancegroup = $all_instancegroups[0];
		$this->assertEquals( get_class($test_instancegroup) , 'tx_caretaker_InstancegroupNode', 'instance found by id');
	}	
	

}
?>