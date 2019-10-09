<?php
namespace Caretaker\Caretaker\Tests\Unit;

use Nimut\TestingFramework\TestCase\UnitTestCase;

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
class TestResultRepositoryTest extends UnitTestCase
{
    public function test_getLatest()
    {
        $this->markTestIncomplete('stub tx_caretaker_TestResultRepository');
        $instance = new \tx_caretaker_InstanceNode(1, 'title', false, '', '');
        $test = new \tx_caretaker_TestNode(0, 'title', $instance, '\tx_caretaker_ping', '');

        $test_result_repository = \tx_caretaker_TestResultRepository::getInstance();
        $result = $test_result_repository->getLatestByNode($test);
        $this->assertEquals(get_class($result), '\tx_caretaker_TestResult', 'a testresult was found');
    }

    public function test_getResultRange()
    {
        $this->markTestIncomplete('stub tx_caretaker_TestResultRepository');
        $instance = new \tx_caretaker_InstanceNode(1, 'title', false, '');
        $test = new \tx_caretaker_TestNode(0, 'title', $instance, '\tx_caretaker_ping', '');

        $test_result_repository = \tx_caretaker_TestResultRepository::getInstance();

        $result_range = $test_result_repository->getRangeByNode($test, time() - 10000, time());
        $this->assertNotNull(count($result_range), 'there are tests found in range');
    }
}
