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

require_once (t3lib_extMgm::extPath('caretaker').'classes/services/tests/class.tx_caretaker_pingTestService.php');


class tx_caretaker_pingTestService_testcase extends tx_phpunit_testcase  {
	
	/**
	 * Set the Return Value of a Method
	 * 
	 * @param $stub
	 * @param $method_name
	 * @param $return_value
	 * @return unknown_type
	 */
	private function setMethodReturnValue(&$stub, $method_name, $return_value){
		$stub->expects($this->any())
			->method($method_name)
			->with()
			->will($this->returnValue($return_value)); 
	}
	
	public function testIfNoCommandIsSetAnErrorOccurs(){
		
		$stub = $this->getMock(
			'tx_caretaker_pingTestService', 
			array('getTimeError','getTimeWarning','buildPingCommand')
		);

		
		$this->setMethodReturnValue($stub, 'getTimeError',    200);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'buildPingCommand', false);
				
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(2, $result->getState() );
		$this->assertEquals(0, $result->getValue() );
		
	}
	
	public function testReturnOkIfAllWentWell(){
		
		$stub = $this->getMock(
			'tx_caretaker_pingTestService', 
			array('getTimeError','getTimeWarning','buildPingCommand','executeSystemCommand')
		);

		$this->setMethodReturnValue($stub, 'getTimeError',    200);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'buildPingCommand', true);
		$this->setMethodReturnValue($stub, 'executeSystemCommand', array(0,'',5));
				
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(0, $result->getState() );
		$this->assertEquals(5, $result->getValue() );
		
	}

	public function testReturnsWarningIfTimeoutIsReached(){
		
		$stub = $this->getMock(
			'tx_caretaker_pingTestService', 
			array('getTimeError','getTimeWarning','buildPingCommand','executeSystemCommand')
		);

		$this->setMethodReturnValue($stub, 'getTimeError',    200);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'buildPingCommand', true);
		$this->setMethodReturnValue($stub, 'executeSystemCommand', array(0,'',20));
				
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(1, $result->getState() );
		$this->assertEquals(20, $result->getValue() );
		
	}

	public function testReturnsErrorIfTimeoutIsReached(){
		
		$stub = $this->getMock(
			'tx_caretaker_pingTestService', 
			array('getTimeError','getTimeWarning','buildPingCommand','executeSystemCommand')
		);

		$this->setMethodReturnValue($stub, 'getTimeError',    200);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'buildPingCommand', true);
		$this->setMethodReturnValue($stub, 'executeSystemCommand', array(0,'',201));
				
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(2, $result->getState() );
		$this->assertEquals(201, $result->getValue() );
		
	}
	
	public function testReturnsErrorIfCommandFailes(){
		
		$stub = $this->getMock(
			'tx_caretaker_pingTestService', 
			array('getTimeError','getTimeWarning','buildPingCommand','executeSystemCommand')
		);

		$this->setMethodReturnValue($stub, 'getTimeError',    200);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'buildPingCommand', true);
		$this->setMethodReturnValue($stub, 'executeSystemCommand', array(3,'',5));
				
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(2, $result->getState() );
		$this->assertEquals(5, $result->getValue() );
		
	}

}

?>
