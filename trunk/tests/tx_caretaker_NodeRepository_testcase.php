<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id: class.tx_caretaker_NodeInfo.php 41726 2011-01-03 12:16:11Z martoro $
 */

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