<?php
namespace Caretaker\Caretaker\Tests\Unit;

use TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * $Id$
 */
class HttpTestServiceTest extends UnitTestCase
{
    public function testErrorIfNoQuery()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_httpTestService $stub */
        $stub = $this->getMock(
                '\tx_caretaker_httpTestService',
                array('getTimeError', 'getTimeWarning', 'getExpectedReturnCode', 'getRequestQuery', 'getInstanceUrl')
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 200);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'getExpectedReturnCode', false);
        $this->setMethodReturnValue($stub, 'getRequestQuery', '');
        $this->setMethodReturnValue($stub, 'getInstanceUrl', '');

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(-1, $result->getState());
        $this->assertEquals(0, $result->getValue());
    }

    /**
     * Set the Return Value of a Method
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $stub
     * @param $method_name
     * @param $return_value
     */
    private function setMethodReturnValue(&$stub, $method_name, $return_value)
    {
        $stub->expects($this->any())
                ->method($method_name)
                ->with()
                ->will($this->returnValue($return_value));
    }

    public function testEverythingWentFine()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_httpTestService $stub */
        $stub = $this->getMock(
                'tx_caretaker_httpTestService',
                array(
                        'getTimeError',
                        'getTimeWarning',
                        'getExpectedReturnCode',
                        'getRequestQuery',
                        'getInstanceUrl',
                        'executeCurlRequest',
                )
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 20);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'getExpectedReturnCode', array(200));
        $this->setMethodReturnValue($stub, 'getRequestQuery', true);
        $this->setMethodReturnValue($stub, 'getInstanceUrl', true);
        $this->setMethodReturnValue($stub, 'executeCurlRequest', array(5, '', array('http_code' => 200, array())));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(0, $result->getState());
        $this->assertEquals(5, $result->getValue());
    }

    public function testWarningIfTimeoutIsReached()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_httpTestService $stub */
        $stub = $this->getMock(
                'tx_caretaker_httpTestService',
                array(
                        'getTimeError',
                        'getTimeWarning',
                        'getExpectedReturnCode',
                        'getRequestQuery',
                        'getInstanceUrl',
                        'executeCurlRequest',
                )
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 20);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'getExpectedReturnCode', array(200));
        $this->setMethodReturnValue($stub, 'getRequestQuery', true);
        $this->setMethodReturnValue($stub, 'getInstanceUrl', true);
        $this->setMethodReturnValue($stub, 'executeCurlRequest', array(12, '', array('http_code' => 200, array())));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(1, $result->getState());
        $this->assertEquals(12, $result->getValue());
    }

    public function testErrorIfTimeoutIsReached()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_httpTestService $stub */
        $stub = $this->getMock(
                'tx_caretaker_httpTestService',
                array(
                        'getTimeError',
                        'getTimeWarning',
                        'getExpectedReturnCode',
                        'getRequestQuery',
                        'getInstanceUrl',
                        'executeCurlRequest',
                )
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 20);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'getExpectedReturnCode', array(200));
        $this->setMethodReturnValue($stub, 'getRequestQuery', true);
        $this->setMethodReturnValue($stub, 'getInstanceUrl', true);
        $this->setMethodReturnValue($stub, 'executeCurlRequest', array(22, '', array('http_code' => 200, array())));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(2, $result->getState());
        $this->assertEquals(22, $result->getValue());
    }

    public function testErrorIfHttpStatusIsWrong()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_httpTestService $stub */
        $stub = $this->getMock(
                'tx_caretaker_httpTestService',
                array(
                        'getTimeError',
                        'getTimeWarning',
                        'getExpectedReturnCode',
                        'getRequestQuery',
                        'getInstanceUrl',
                        'executeCurlRequest',
                )
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 20);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'getExpectedReturnCode', array(404));
        $this->setMethodReturnValue($stub, 'getRequestQuery', true);
        $this->setMethodReturnValue($stub, 'getInstanceUrl', true);
        $this->setMethodReturnValue($stub, 'executeCurlRequest', array(5, '', array('http_code' => 200, array())));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(2, $result->getState());
        $this->assertEquals(5, $result->getValue());
    }

    public function testHttpHeaderComparison()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_httpTestService|\Caretaker\Caretaker\Tests\Unit\Stubs\HttpTestServiceStub $stub */
        $stub = $this->getMock(
                '\Caretaker\Caretaker\Tests\Unit\Stubs\HttpTestServiceStub',
                array('getRequestQuery', 'getInstanceUrl')
        );

        $this->setMethodReturnValue($stub, 'getRequestQuery', 'blah');
        $this->setMethodReturnValue($stub, 'getInstanceUrl', 'http://foo.bar.de/blubber');

        // TRUE Assertations
        $this->assertTrue($stub->checkSingleHeader('123', '=123'));
        $this->assertTrue($stub->checkSingleHeader('123', '>99'));
        $this->assertTrue($stub->checkSingleHeader('123', '<200'));

        // FALSE Assertations
        $this->assertFalse($stub->checkSingleHeader('345', '123'));
        $this->assertFalse($stub->checkSingleHeader('345', '= 123'));
        $this->assertFalse($stub->checkSingleHeader('345', '> 400'));
        $this->assertFalse($stub->checkSingleHeader('345', '< 300'));

        // Time comparison
        // Mon, 30 Nov 2009 08:42:46 GMT
        $date_timestamp = time() - 250;
        $date_string = strftime('%a, %e %b %Y %H:%M:%S %Z', $date_timestamp);

        $this->assertTrue($stub->checkSingleHeader($date_string, 'Age:<300'));
        $this->assertFalse($stub->checkSingleHeader($date_string, 'Age:<100'));
        $this->assertTrue($stub->checkSingleHeader($date_string, 'Age:> 100'));
        $this->assertFalse($stub->checkSingleHeader($date_string, 'Age:> 400'));

        // Test Markers
        $header = 'http://foo.bar.de/blubber/blah';
        $compare = '= ###INSTANCE_PROTOCOL###://###INSTANCE_HOSTNAME###/###INSTANCE_QUERY###/###REQUEST_QUERY###';
        $this->assertTrue($stub->checkSingleHeader($header, $compare));
        $this->assertFalse($stub->checkSingleHeader('https://foo.barz.de/blub/b�h', $compare));
        $this->assertFalse($stub->checkSingleHeader('https://foo.bar.de/blubber/blah', $compare));
        $this->assertFalse($stub->checkSingleHeader('http://foo.baz.de/blubber/blah', $compare));
        $this->assertFalse($stub->checkSingleHeader('http://foo.bar.de/blub/blah', $compare));
        $this->assertFalse($stub->checkSingleHeader('http://foo.bar.de/blubber/bl�h', $compare));
    }

    /**
     * @return array
     */
    public function parseHeaderDateDataProvider()
    {
        $now = time();
        return array(
                array('Tue, 15 Nov 1994 08:12:31 GMT', '784887151'),
                array('Mon, 14 Jul 2014 10:48:22 UTC', '1405334902'),
                array('Tue, 15 Jul 2014 10:48:22 UTC', '1405421302'),
                array('Sat, 15 Jul 2017 10:48:22 UTC', '1500115702'),
                array('Sat, 15 Jul 2017 10:48:22 +0200', '1500108502'),
                array(date(DATE_RFC1123, $now), $now),
        );
    }

    /**
     * @dataProvider parseHeaderDateDataProvider
     * @param mixed $dateString
     * @param mixed $expectedDate
     */
    public function testParseHeaderDate($dateString, $expectedDate)
    {
        $subject = new \Caretaker\Caretaker\Tests\Unit\Stubs\HttpTestServiceStub();
        $this->assertEquals($expectedDate, $subject->parseHeaderDate($dateString));
    }
}
