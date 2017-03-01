<?php
namespace Caretaker\Caretaker\Tests\Unit;

use Caretaker\Caretaker\Tests\Unit\Stubs\NodeResultRangeStub;
use Caretaker\Caretaker\Tests\Unit\Stubs\NodeResultStub;
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

/**
 *
 */
class NodeResultRange_testcase extends UnitTestCase
{

    function test_AddingOfResults()
    {

        $range = new NodeResultRangeStub(123, 789);

        $this->assertEquals($range->getLength(), 0);

        $res_1 = new NodeResultStub(123, 0, '', []);
        $range->addResult($res_1);
        $this->assertEquals($range->getLength(), 1);

        $res_2 = new NodeResultStub(456, 1, '', []);
        $range->addResult($res_2);
        $this->assertEquals($range->getLength(), 2);

        $res_3 = new NodeResultStub(789, 2, '', []);
        $range->addResult($res_3);
        $this->assertEquals($range->getLength(), 3);

    }

    function test_getFirstAndGetLastResults()
    {

        $range = new NodeResultRangeStub(123, 789);

        $res_1 = new NodeResultStub(456, 1, '', []);
        $res_2 = new NodeResultStub(123, 0, '', []);
        $res_3 = new NodeResultStub(789, 2, '', []);
        $res_4 = new NodeResultStub(678, 2, '', []);

        $range->addResult($res_1);
        $range->addResult($res_2);
        $range->addResult($res_3);
        $range->addResult($res_4);

        $this->assertEquals($range->getFirst()->getTimestamp(), 123);
        $this->assertEquals($range->getLast()->getTimestamp(), 789);

    }

    function test_MinMaxTstamp()
    {

        $range = new NodeResultRangeStub(100, 600);

        $this->assertEquals($range->getStartTimestamp(), 100);
        $this->assertEquals($range->getEndTimestamp(), 600);

        $res_1 = new NodeResultStub(456, 1, '', []);
        $range->addResult($res_1);

        $this->assertEquals($range->getStartTimestamp(), 100);
        $this->assertEquals($range->getEndTimestamp(), 600);

        $res_2 = new NodeResultStub(789, 2, '', []);
        $res_3 = new NodeResultStub(50, 2, '', []);

        $range->addResult($res_2);
        $range->addResult($res_3);

        $this->assertEquals($range->getStartTimestamp(), 50);
        $this->assertEquals($range->getEndTimestamp(), 789);

    }

}
