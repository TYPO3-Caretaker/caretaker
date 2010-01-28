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

require_once (t3lib_extMgm::extPath('caretaker').'classes/services/tests/class.tx_caretaker_httpTestService.php');

/**
 * Stub class to expose protected methods for testing
 */
class tx_caretaker_httpTestService_stub extends tx_caretaker_httpTestService {
	
	public function checkSingleHeader( $expectedHeaders,$responseHeaders ){
		return parent::checkSingleHeader( $expectedHeaders,$responseHeaders );
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
		$this->assertTrue( $stub->checkSingleHeader( '123', '=123' ) );
		$this->assertTrue( $stub->checkSingleHeader( '123', '>99'  ) );
		$this->assertTrue( $stub->checkSingleHeader( '123', '<200' ) );

			// FALSE Assertations
		$this->assertFalse( $stub->checkSingleHeader( '345', '123'   ) );
		$this->assertFalse( $stub->checkSingleHeader( '345', '= 123' ) );
		$this->assertFalse( $stub->checkSingleHeader( '345', '> 400' ) );
		$this->assertFalse( $stub->checkSingleHeader( '345', '< 300' ) );

			// Time comparison
			// Mon, 30 Nov 2009 08:42:46 GMT
		$date_timestamp = time() - 250 ;
		$date_string    = strftime('%a, %e %b %Y %H:%M:%S %Z', $date_timestamp );
		$testedHeaders  = array('value', $date_string );

		$this->assertTrue(  $stub->checkSingleHeader( $date_string, 'Age:<300'  ) );
		$this->assertFalse( $stub->checkSingleHeader( $date_string, 'Age:<100'  ) );
		$this->assertTrue(  $stub->checkSingleHeader( $date_string, 'Age:> 100' ) );
		$this->assertFalse( $stub->checkSingleHeader( $date_string, 'Age:> 400' ) );

			// Test Markers
		$header   = 'http://foo.bar.de/blubber/blah';
		$compare  = '= ###INSTANCE_PROTOCOL###://###INSTANCE_HOSTNAME###/###INSTANCE_QUERY###/###REQUEST_QUERY###';
		$this->assertTrue(  $stub->checkSingleHeader(  $header , $compare ) );
		$this->assertFalse( $stub->checkSingleHeader( 'https://foo.barz.de/blub/bŠh', $compare ) );
		$this->assertFalse( $stub->checkSingleHeader( 'https://foo.bar.de/blubber/blah', $compare ) );
		$this->assertFalse( $stub->checkSingleHeader( 'http://foo.baz.de/blubber/blah', $compare ) );
		$this->assertFalse( $stub->checkSingleHeader( 'http://foo.bar.de/blub/blah', $compare ) );
		$this->assertFalse( $stub->checkSingleHeader( 'http://foo.bar.de/blubber/blŠh', $compare ) );

	}
}

?>
