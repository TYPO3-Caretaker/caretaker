<?php 

interface tx_caretaker_TestResultRangeRenderer {

	public function getInstance();
	
	public function render ($test_result_range, $file );
	
}
?>