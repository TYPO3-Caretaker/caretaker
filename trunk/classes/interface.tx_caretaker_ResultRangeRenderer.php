<?php 

interface tx_caretaker_ResultRangeRenderer {

	public function getInstance();
	
	public function render ($test_result_range, $file );
	
}
?>