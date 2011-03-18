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

class tx_caretaker_TestResult_testcase extends tx_phpunit_testcase  {

	function test_comparisonOfTestResults (){

		$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined);

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty undefined results should be equal' );

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		$this->assertTrue( $result->isDifferent($compareResult) , 'result with other state is not equal');

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined, 0 );
		$this->assertTrue( $result->equals( $compareResult ), 'default is undefined state and value 0' );

		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined, 1 );
		$this->assertTrue( $result->isDifferent( $compareResult ), 'value 1 is different from 0' );


		$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty OK results should be equal' );

		$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_warning);
		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_warning);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty WARNING results should be equal' );

				$result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error);
		$compareResult =  tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error);
		$this->assertTrue( $result->equals( $compareResult ), 'two empty ERROR results should be equal' );


	}

}

?>