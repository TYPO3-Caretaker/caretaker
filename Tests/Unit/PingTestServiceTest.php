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
class PingTestServiceTest extends UnitTestCase
{
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

    public function testIfNoCommandIsSetAnErrorOccurs()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_pingTestService $stub */
        $stub = $this->getMock(
            '\tx_caretaker_pingTestService',
            array('getTimeError', 'getTimeWarning', 'buildPingCommand')
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 200);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'buildPingCommand', false);

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(2, $result->getState());
        $this->assertEquals(0, $result->getValue());
    }

    public function testReturnOkIfAllWentWell()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_pingTestService $stub */
        $stub = $this->getMock(
            '\tx_caretaker_pingTestService',
            array('getTimeError', 'getTimeWarning', 'buildPingCommand', 'executeSystemCommand')
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 200);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'buildPingCommand', true);
        $this->setMethodReturnValue($stub, 'executeSystemCommand', array(0, '', 5));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(0, $result->getState());
        $this->assertEquals(5, $result->getValue());
    }

    public function testReturnsWarningIfTimeoutIsReached()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_pingTestService $stub */
        $stub = $this->getMock(
            '\tx_caretaker_pingTestService',
            array('getTimeError', 'getTimeWarning', 'buildPingCommand', 'executeSystemCommand')
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 200);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'buildPingCommand', true);
        $this->setMethodReturnValue($stub, 'executeSystemCommand', array(0, '', 20));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(1, $result->getState());
        $this->assertEquals(20, $result->getValue());
    }

    public function testReturnsErrorIfTimeoutIsReached()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_pingTestService $stub */
        $stub = $this->getMock(
            '\tx_caretaker_pingTestService',
            array('getTimeError', 'getTimeWarning', 'buildPingCommand', 'executeSystemCommand')
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 200);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'buildPingCommand', true);
        $this->setMethodReturnValue($stub, 'executeSystemCommand', array(0, '', 201));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(2, $result->getState());
        $this->assertEquals(201, $result->getValue());
    }

    public function testReturnsErrorIfCommandFailes()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\tx_caretaker_pingTestService $stub */
        $stub = $this->getMock(
            '\tx_caretaker_pingTestService',
            array('getTimeError', 'getTimeWarning', 'buildPingCommand', 'executeSystemCommand')
        );

        $this->setMethodReturnValue($stub, 'getTimeError', 200);
        $this->setMethodReturnValue($stub, 'getTimeWarning', 10);
        $this->setMethodReturnValue($stub, 'buildPingCommand', true);
        $this->setMethodReturnValue($stub, 'executeSystemCommand', array(3, '', 5));

        $result = $stub->runTest();

        $this->assertInstanceOf('tx_caretaker_TestResult', $result);
        $this->assertEquals(2, $result->getState());
        $this->assertEquals(5, $result->getValue());
    }
}
