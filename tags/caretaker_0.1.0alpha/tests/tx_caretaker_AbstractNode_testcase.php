<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * StubClass to allow the testing of the tx_caretaker_AbstractNode Class
 *
 * @author martin
 */
class tx_caretaker_AbstractNode_Stub extends tx_caretaker_AbstractNode {
	
	public function getCaretakerNodeId(){
		return "abstract_node";
	}

	public function getTestNodes(){
		return array();
	}

	public function getValueDescription(){
		return '';
	}

	public function updateTestResult($force_update = false){ }
	public function getTestResult(){}
	public function getTestResultRange($startdate, $stopdate){}
	public function getTestResultNumber(){}
	public function getTestResultRangeByOffset($offset=0, $limit=10){}
}

/**
 * Description of tx_caretaker_AbstractNode_testcase
 *
 * @author martin
 */
class tx_caretaker_AbstractNode_testcase extends tx_phpunit_testcase  {
	
   function test_getPropertyMethods(){

		$aggregator = new tx_caretaker_AbstractNode_Stub( 0, 'foo', false );

		$this->assertEquals( false, $aggregator->getProperty('foo') , "wrong result" );

		$aggregator->setDbRow(array('foo'=>'bar'));

		$this->assertEquals( 'bar', $aggregator->getProperty('foo') , "wrong result" );

		$this->assertEquals( false, $aggregator->getProperty('bar') , "wrong result" );


	}
	
}
?>
