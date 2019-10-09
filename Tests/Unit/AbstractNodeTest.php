<?php
namespace Caretaker\Caretaker\Tests\Unit;

use Caretaker\Caretaker\Tests\Unit\Stubs\AbstractNodeStub;
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
class AbstractNodeTest extends UnitTestCase
{
    public function test_getPropertyMethods()
    {
        $aggregator = new AbstractNodeStub(0, 'foo', null, '');

        $this->assertEquals(false, $aggregator->getProperty('foo'), 'wrong result');

        $aggregator->setDbRow(array('foo' => 'bar'));

        $this->assertEquals('bar', $aggregator->getProperty('foo'), 'wrong result');

        $this->assertEquals(false, $aggregator->getProperty('bar'), 'wrong result');
    }
}
