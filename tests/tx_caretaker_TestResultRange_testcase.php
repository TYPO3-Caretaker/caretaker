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

require_once(t3lib_extMgm::extPath('caretaker') . '/classes/results/class.tx_caretaker_TestResult.php');
require_once(t3lib_extMgm::extPath('caretaker') . '/classes/results/class.tx_caretaker_TestResultRange.php');

class tx_caretaker_TestResultRange_testcase extends tx_phpunit_testcase {

	var $test_result_range;

	function setUp() {

		$this->test_result_range = new tx_caretaker_TestResultRange(500, 1000);

		$this->test_result_range->addResult(
				new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 2, '')
		);

		$this->test_result_range->addResult(
				new tx_caretaker_TestResult(600, tx_caretaker_Constants::state_error, 2, '')
		);

		$this->test_result_range->addResult(
				new tx_caretaker_TestResult(650, tx_caretaker_Constants::state_ok, 3, '')
		);

		$this->test_result_range->addResult(
				new tx_caretaker_TestResult(800, tx_caretaker_Constants::state_warning, 10, '')
		);

		$this->test_result_range->addResult(
				new    tx_caretaker_TestResult(900, tx_caretaker_Constants::state_ok, 1, '')
		);

		$this->test_result_range->addResult(
				new tx_caretaker_TestResult(930, tx_caretaker_Constants::state_undefined, 50, '')
		);

		$this->test_result_range->addResult(
				new tx_caretaker_TestResult(950, tx_caretaker_Constants::state_ok, 1, '')
		);

	}

	function test_MinMaxTS() {
		$this->assertEquals($this->test_result_range->getMinTstamp(), 500);
		$this->assertEquals($this->test_result_range->getMaxTstamp(), 1000);
	}

	function test_get_state_infos() {

		$info = $this->test_result_range->getInfos();

		$this->assertEquals($info['SecondsTotal'], 500);
		$this->assertEquals($info['SecondsOK'], 280);
		$this->assertEquals($info['SecondsUNDEFINED'], 20);
		$this->assertEquals($info['SecondsERROR'], 50);
		$this->assertEquals($info['SecondsWARNING'], 100);

	}


	function test_get_availability_infos() {

		$info = $this->test_result_range->getInfos();

		$this->assertEquals($info['PercentAVAILABLE'], 330 / 500);
		$this->assertEquals($info['PercentOK'], 280 / 500);
		$this->assertEquals($info['PercentERROR'], 50 / 500);
		$this->assertEquals($info['PercentWARNING'], 100 / 500);
		$this->assertEquals($info['PercentUNDEFINED'], 20 / 500);

	}

	function test_get_length() {
		$this->assertEquals($this->test_result_range->getLength(), 7);
	}

	function test_getMedianValue() {

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 12, ''));
		$tr->addResult(new tx_caretaker_TestResult(600, tx_caretaker_Constants::state_ok, 5, ''));
		$tr->addResult(new tx_caretaker_TestResult(700, tx_caretaker_Constants::state_ok, 7, ''));
		$tr->addResult(new tx_caretaker_TestResult(800, tx_caretaker_Constants::state_ok, 8, ''));
		$tr->addResult(new tx_caretaker_TestResult(900, tx_caretaker_Constants::state_ok, 5, ''));

		$this->assertEquals($tr->getMedianValue(), 7, 'median fails for odd numbers of results');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 5, ''));
		$tr->addResult(new tx_caretaker_TestResult(600, tx_caretaker_Constants::state_ok, 7, ''));
		$tr->addResult(new tx_caretaker_TestResult(700, tx_caretaker_Constants::state_ok, 8, ''));
		$tr->addResult(new tx_caretaker_TestResult(800, tx_caretaker_Constants::state_ok, 12, ''));

		$this->assertEquals($tr->getMedianValue(), 7.5, 'median fails for even numbers of results');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$this->assertEquals($tr->getMedianValue(), 0, 'median fails for 0 numbers of results');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 5, ''));

		$this->assertEquals($tr->getMedianValue(), 5, 'median fails for single result');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 5, ''));
		$tr->addResult(new tx_caretaker_TestResult(600, tx_caretaker_Constants::state_ok, 7, ''));

		$this->assertEquals($tr->getMedianValue(), 6, 'median fails for to results');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 5, ''));
		$tr->addResult(new tx_caretaker_TestResult(600, tx_caretaker_Constants::state_ok, 7, ''));
		$tr->addResult(new tx_caretaker_TestResult(700, tx_caretaker_Constants::state_ok, 8, ''));

		$this->assertEquals($tr->getMedianValue(), 7, 'median fails for three results');


	}

	function test_getAverageValue() {

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 10, ''));
		$tr->addResult(new tx_caretaker_TestResult(750, tx_caretaker_Constants::state_ok, 20, ''));
		$tr->addResult(new tx_caretaker_TestResult(1000, tx_caretaker_Constants::state_ok, 20, ''));

		$this->assertEquals($tr->getAverageValue(500, 1000), 15, 'average value fails');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 10, ''));
		$tr->addResult(new tx_caretaker_TestResult(750, tx_caretaker_Constants::state_ok, 20, ''));

		$this->assertEquals($tr->getAverageValue(), 10, 'average value fails');

		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(800, tx_caretaker_Constants::state_ok, 10, ''));
		$tr->addResult(new tx_caretaker_TestResult(900, tx_caretaker_Constants::state_ok, 20, ''));
		$tr->addResult(new tx_caretaker_TestResult(1000, tx_caretaker_Constants::state_ok, 20, ''));

		$this->assertEquals($tr->getAverageValue(), 15, 'average value fails');


		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(500, tx_caretaker_Constants::state_ok, 10, ''));
		$tr->addResult(new tx_caretaker_TestResult(900, tx_caretaker_Constants::state_ok, 20, ''));
		$tr->addResult(new tx_caretaker_TestResult(1000, tx_caretaker_Constants::state_ok, 20, ''));

		$this->assertEquals($tr->getAverageValue(), 12, 'average value fails');


		$tr = new tx_caretaker_TestResultRange(500, 1000);
		$tr->addResult(new tx_caretaker_TestResult(750, tx_caretaker_Constants::state_ok, 10, ''));

		$this->assertEquals($tr->getAverageValue(), 0, 'average value fails');

	}

}

?>