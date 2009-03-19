<?php

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResult.php');
require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_TestDummy extends tx_caretaker_TestServiceBase {
	
	function runTest() {
		$result = new tx_caretaker_TestResult( TX_CARETAKER_STATE_OK, 9.18 , 'foobar' );
		return $result;
	}
	
}

?>