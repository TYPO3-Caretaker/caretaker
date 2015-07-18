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
 * $Id$
 */

/**
 *
 */
class tx_caretaker_NodeResult_testcase extends tx_phpunit_testcase {

	function test_TestResult_stores_data() {
		$result = new tx_caretaker_TestResult(123, 1, 1.75, 'This is a Message');

		$this->assertEquals($result->getTimestamp(), 123);
		$this->assertEquals($result->getState(), 1);
		$this->assertEquals($result->getStateInfo(), 'WARNING');
		$this->assertEquals($result->getValue(), 1.75);
		$this->assertEquals($result->getMessage()->getText(), 'This is a Message');
	}

	function test_AggregatorResult_stores_data() {
		$result = new tx_caretaker_AggregatorResult(123, 2, 2, 1, 3, 5, 'This is a Message');

		$this->assertEquals($result->getTimestamp(), 123);
		$this->assertEquals($result->getState(), 2);
		$this->assertEquals($result->getStateInfo(), 'ERROR');
		$this->assertEquals($result->getMessage()->getText(), 'This is a Message');

		$this->assertEquals($result->getNumUNDEFINED(), 2);
		$this->assertEquals($result->getNumOK(), 1);
		$this->assertEquals($result->getNumWARNING(), 3);
		$this->assertEquals($result->getNumERROR(), 5);


	}

}

?>