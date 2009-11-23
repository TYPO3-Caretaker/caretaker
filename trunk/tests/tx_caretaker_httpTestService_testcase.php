<?php 
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_httpTestService.php');

/**
 * Stub class to expose protected methods for testing
 */
class tx_caretaker_httpTestService_stub extends tx_caretaker_httpTestService {
	
	public function checkExpectedHeaders( $expectedHeaders,$responseHeaders ){
		return parent::checkExpectedHeaders( $expectedHeaders,$responseHeaders );
	}
	
}

class tx_caretaker_httpTestService_testcase extends tx_phpunit_testcase  {
	
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
	
	public function testErrorIfNoQuery(){
		
		$stub = $this->getMock(
			'tx_caretaker_httpTestService', 
			array('getTimeError','getTimeWarning','getExpectedReturnCode','getRequestQuery','getInstanceUrl')
		);
		
		$this->setMethodReturnValue($stub, 'getTimeError',    200);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'getExpectedReturnCode',  false);
		$this->setMethodReturnValue($stub, 'getRequestQuery',   '');
		$this->setMethodReturnValue($stub, 'getInstanceUrl',  '');
		
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(-1, $result->getState() );
		$this->assertEquals(0, $result->getValue() );
		
	}
	
	public function testEverythingWentFine(){
		
		$stub = $this->getMock(
			'tx_caretaker_httpTestService', 
			array('getTimeError','getTimeWarning','getExpectedReturnCode','getRequestQuery','getInstanceUrl','executeCurlRequest')
		);
		
		$this->setMethodReturnValue($stub, 'getTimeError',     20);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'getExpectedReturnCode',  200);
		$this->setMethodReturnValue($stub, 'getRequestQuery',  true);
		$this->setMethodReturnValue($stub, 'getInstanceUrl',  true);
		$this->setMethodReturnValue($stub, 'executeCurlRequest', array(5,'',array('http_code' => 200, array() ) ) );
		
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(0, $result->getState() );
		$this->assertEquals(5, $result->getValue() );
		
	}
	
	public function testWarningIfTimeoutIsReached(){
		
		$stub = $this->getMock(
			'tx_caretaker_httpTestService', 
			array('getTimeError','getTimeWarning','getExpectedReturnCode','getRequestQuery','getInstanceUrl','executeCurlRequest')
		);
		
		$this->setMethodReturnValue($stub, 'getTimeError',     20);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'getExpectedReturnCode',  200);
		$this->setMethodReturnValue($stub, 'getRequestQuery',  true);
		$this->setMethodReturnValue($stub, 'getInstanceUrl',  true);
		$this->setMethodReturnValue($stub, 'executeCurlRequest', array(12,'',array('http_code' => 200, array() ) ) );
		
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(1, $result->getState() );
		$this->assertEquals(12, $result->getValue() );
		
	}

	public function testErrorIfTimeoutIsReached(){
		
		$stub = $this->getMock(
			'tx_caretaker_httpTestService', 
			array('getTimeError','getTimeWarning','getExpectedReturnCode','getRequestQuery','getInstanceUrl','executeCurlRequest')
		);
		
		$this->setMethodReturnValue($stub, 'getTimeError',     20);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'getExpectedReturnCode',  200);
		$this->setMethodReturnValue($stub, 'getRequestQuery',  true);
		$this->setMethodReturnValue($stub, 'getInstanceUrl',  true);
		$this->setMethodReturnValue($stub, 'executeCurlRequest', array(22,'',array('http_code' => 200, array() ) ) );
		
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(2, $result->getState() );
		$this->assertEquals(22, $result->getValue() );
		
	}
	
	public function testErrorIfHttpStatusIsWrong(){
		
		$stub = $this->getMock(
			'tx_caretaker_httpTestService', 
			array('getTimeError','getTimeWarning','getExpectedReturnCode','getRequestQuery','getInstanceUrl','executeCurlRequest')
		);
		
		$this->setMethodReturnValue($stub, 'getTimeError',     20);
		$this->setMethodReturnValue($stub, 'getTimeWarning',   10);
		$this->setMethodReturnValue($stub, 'getExpectedReturnCode',  404);
		$this->setMethodReturnValue($stub, 'getRequestQuery',  true);
		$this->setMethodReturnValue($stub, 'getInstanceUrl',  true);
		$this->setMethodReturnValue($stub, 'executeCurlRequest', array(5,'',array('http_code' => 200, array() ) ) );
		
		$result = $stub->runTest();
		
		$this->assertType('tx_caretaker_TestResult', $result);
		$this->assertEquals(2, $result->getState() );
		$this->assertEquals(5, $result->getValue() );
		
	}

	public function testHttpHeaderComparison(){

		$stub = $this->getMock(
			'tx_caretaker_httpTestService_stub',
			array('getRequestQuery','getInstanceUrl' )
		);

		$this->setMethodReturnValue($stub, 'getRequestQuery',  'blah');
		$this->setMethodReturnValue($stub, 'getInstanceUrl',   'http://foo.bar.de/blubber');

			// TRUE Assertations
		$testedHeaders = array('value','123');

		$expectedHeaders = array('value','= 123');
		$this->assertTrue( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

		$expectedHeaders = array('value','> 99');
		$this->assertTrue( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

		$expectedHeaders = array('value','< 200');
		$this->assertTrue( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

			// FALSE Assertations
		$testedHeaders = array('value','345');

		$expectedHeaders = array('value','123');
		$this->assertFalse( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

		$expectedHeaders = array('value','= 123');
		$this->assertFalse( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

		$expectedHeaders = array('value','> 400');
		$this->assertFalse( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

		$expectedHeaders = array('value','< 300');
		$this->assertFalse( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

			// Test Markers
		$testedHeaders = array('value','http://foo.bar.de/blubber/blah');
		$expectedHeaders = array('value','= ###INSTANCE_PROTOCOL###://###INSTANCE_HOSTNAME###/###INSTANCE_QUERY###/###REQUEST_QUERY###');
		$this->assertTrue( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

		$testedHeaders = array('value','https://foo.barz.de/blub/bläh');
		$expectedHeaders = array('value','= ###INSTANCE_PROTOCOL###://###INSTANCE_HOSTNAME###/###INSTANCE_QUERY###/###REQUEST_QUERY###');
		$this->assertFalse( $stub->checkExpectedHeaders( $expectedHeaders, $testedHeaders ) );

	}
	
}

?>