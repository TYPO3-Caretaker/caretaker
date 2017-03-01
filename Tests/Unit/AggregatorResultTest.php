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
class AggregatorResultTest extends UnitTestCase
{

    function test_comparisonOfAggreagtorResults()
    {

        $result = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined);

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined);
        $this->assertTrue($result->equals($compareResult), 'two empty undefined results should be equal');

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_ok);
        $this->assertTrue($result->isDifferent($compareResult), 'result with other state is not equal');

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined, 0, 0, 0, 0);
        $this->assertTrue($result->equals($compareResult),
            'result with state undefined and all errorNumbers 0 is equal to empty result');

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined, 1, 0, 0, 0);
        $this->assertFalse($result->equals($compareResult),
            'result with state undefined and but numUndefined = 1 is not equal to empty result');

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined, 0, 1, 0, 0);
        $this->assertFalse($result->equals($compareResult),
            'result with state undefined and but numOK = 1 is not equal to empty result');

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined, 0, 0, 1, 0);
        $this->assertFalse($result->equals($compareResult),
            'result with state undefined and but numWarning = 1 is not equal to empty result');

        $compareResult = \tx_caretaker_AggregatorResult::create(\tx_caretaker_Constants::state_undefined, 0, 0, 0, 1);
        $this->assertFalse($result->equals($compareResult),
            'result with state undefined and but numError = 1 is not equal to empty result');

    }

}
