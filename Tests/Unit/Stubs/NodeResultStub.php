<?php
namespace Caretaker\Caretaker\Tests\Unit\Stubs;

/**
 * Stub class to test the abstract Node Result Class
 */
class NodeResultStub extends \tx_caretaker_NodeResult {

	/**
	 * Get a Hash for the given Status. If two results give the same hash they
	 * are considered to be equal.
	 *
	 * @return string ResultHash
	 */
	public function getResultHash() {
		return 'foobar';
	}

}