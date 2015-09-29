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
 * StubClass to allow the testing of the tx_caretaker_AbstractNode Class
 *
 * @author martin
 */
class tx_caretaker_AbstractNode_Stub extends tx_caretaker_AbstractNode {

	public function getCaretakerNodeId() {
		return "abstract_node";
	}

	public function getTestNodes() {
		return array();
	}

	public function getValueDescription() {
		return '';
	}

	public function updateTestResult($force_update = false) {
	}

	public function getTestResult() {
	}

	public function getTestResultRange($startdate, $stopdate) {
	}

	public function getTestResultNumber() {
	}

	public function getTestResultRangeByOffset($offset = 0, $limit = 10) {
	}
}

/**
 * Description of tx_caretaker_AbstractNode_testcase
 *
 * @author martin
 */
class tx_caretaker_AbstractNode_testcase extends tx_phpunit_testcase {

	function test_getPropertyMethods() {

		$aggregator = new tx_caretaker_AbstractNode_Stub(0, 'foo', false);

		$this->assertEquals(false, $aggregator->getProperty('foo'), "wrong result");

		$aggregator->setDbRow(array('foo' => 'bar'));

		$this->assertEquals('bar', $aggregator->getProperty('foo'), "wrong result");

		$this->assertEquals(false, $aggregator->getProperty('bar'), "wrong result");


	}

}

?>
