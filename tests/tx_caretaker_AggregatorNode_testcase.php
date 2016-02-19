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
/* create stub class to expose protected methods */

class tx_caretaker_AggregatorNode_Stub extends tx_caretaker_AggregatorNode {

	public function getCaretakerNodeId() {
		return "aggregator_node";
	}

	protected function findChildren($show_hidden = false) {
		return array();
	}

	public function getAggregatedResult($results) {
		return parent::getAggregatedResult($results);
	}

}

/* this is the test */

class tx_caretaker_AggregatorNode_testcase extends tx_phpunit_testcase {

	function test_aggregation_of_results() {

		$aggregator = new tx_caretaker_AggregatorNode_Stub(0, 'foo', null, '');
		$instance = new tx_caretaker_InstanceNode (0, 'bar', null);
		$node = new tx_caretaker_TestNode (0, 'baz', $instance, 'tx_caretaker_ping', '');

		$results = array();
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok));
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok));
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create(tx_caretaker_Constants::state_warning));
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error));
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error));
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok));
		$results[] = array('node' => $node, 'result' => tx_caretaker_TestResult::create());

		$aggregated_result = $aggregator->getAggregatedResult($results);
		$this->assertEquals(2, $aggregated_result->getNumERROR(), "wrong error count");
		$this->assertEquals(1, $aggregated_result->getNumWARNING(), "wrong warning count");
		$this->assertEquals(3, $aggregated_result->getNumOK(), "wrong ok count");
		$this->assertEquals(1, $aggregated_result->getNumUNDEFINED(), "wrong undefined count");

		$this->assertEquals(tx_caretaker_Constants::state_error, $aggregated_result->getState(), "wrong result");

	}


}

?>