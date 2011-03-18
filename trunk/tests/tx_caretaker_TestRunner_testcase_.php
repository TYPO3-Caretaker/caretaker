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

class tx_caretaker_TestNodeRunner_testcase extends tx_phpunit_testcase  {


	protected function setUp() {

	}

	protected function tearDown() {

	}

	public function provider_test_foo(){
		return array(
			array( 1, 1, 'true is not true but i knew this before' ),
			array( 1, 1, 'true is not false add unit tests here'   )
		);
	}

	/**
	 * @dataProvider provider_test_foo
	 * @param unknown_type $foo
	 * @param unknown_type $bar
	 * @param unknown_type $baz
	 */
	/*
	public function test_foo( $foo, $bar, $baz ){
		$this->assertEquals( $foo, $bar, $baz );

	}
	*/
}

?>