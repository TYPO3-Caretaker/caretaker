<?php 
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

abstract class tx_caretaker_AggregatorNode extends tx_caretaker_AbstractNode {

	/**
	 * Child Nodes
	 * @var array
	 */
	protected $child_nodes = NULL;
	
	/**
	 * Get the Childnodes of this Node (cached)
	 * @param $show_hidden
	 * @return unknown_type
	 */
	public function getChildren($show_hidden=false){
		if ($this->child_nodes === NULL){
			$this->child_nodes = $this->findChildren($show_hidden);
		}
		return $this->child_nodes;
	}
	
	/**
	 * Find the children of this node
	 *  
	 * @param $hidden
	 * @return array
	 */
	abstract protected function findChildren($show_hidden=false);
	
	/**
	 * Update Node Result and store in DB. 
	 * 
	 * If force is set children will also be forced to update their state.
	 * 
	 * @param boolean Force update 
	 * @return tx_caretaker_AggregatorResult
	 */
	public function updateTestResult( $force_update = false){

		
		
		$this->log('update', 1);

		if ( $this->getHidden() == true ){
			$groupResult = tx_caretaker_AggregatorResult::undefined('Node is disabled');
		} else {
				// find children
			$children  = $this->getChildren();
			if (count($children)>0){
				$testResults = array();
				foreach($children as $child){
					$testResult = $child->updateTestResult($force_update);
					$testResults[] = array('node'=>$child, 'result'=>$testResult);
				}
				$groupResult = $this->getAggregatedResult($testResults);
			} else {
				$groupResult = tx_caretaker_AggregatorResult::undefined( 'No children were found');
			}

				// save to repository if the result differs from the last one
			$resultRepository = tx_caretaker_AggregatorResultRepository::getInstance();
			$lastGroupResult = $resultRepository->getLatestByNode($this);
			if ($lastGroupResult->isDifferent($groupResult) ){
				$resultRepository->addNodeResult($this,$groupResult);
			}
		}
		
		$this->log( ' |> '.$groupResult->getStateInfo().' :: '.$groupResult->getMsg(), false );
		
		return $groupResult;
	}

	/**
	 * Read aggregator node state from DB
	 * @return tx_caretaker_AggregatorResult
	 */
	public function getTestResult(){

		$this->log( 'get', 1 );
		
		if ( $this->getHidden() == true ){
			$group_result = tx_caretaker_AggregatorResult::undefined('Node is disabled');
		} else {
			$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
			$group_result = $result_repository->getLatestByNode($this);
		}
		
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		
		return $group_result;
		
	}

	/**
	 * Get the all tests wich can be found below this node
	 * @return array
	 */
	public function getTestNodes(){
		
		$children  = $this->getChildren();
		$tests     = array();
		
		if (count($children)>0){
			foreach($children as $child){
				if (is_a( $child , 'tx_caretaker_TestNode' ) ) {
					$tests[ $child->getCaretakerNodeId() ] = $child;
				} else if ( is_a( $child , 'tx_caretaker_AggregatorNode' ) ) {
					$tests = array_merge($child->getTestNodes(), $tests );
				}
			}
		}

		return $tests;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getTestResultRange()
	 */
	public function getTestResultRange($startdate, $stopdate , $distance=FALSE){
		
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$group_results = $result_repository->getRangeByNode($this, $startdate, $stopdate);
		return $group_results;
		
	}
	
	/**
	 * Aggregate Child-Testresults
	 * 
	 * @param tx_caretaker_NodeResult[] Child-Results to aggregate
	 * @return tx_caretaker_AggregatorResult Aggregated State
	 */
	protected function getAggregatedResult($test_results){
		
		$num_tests = count($test_results);
		
		$num_undefined = 0;
		$num_ok        = 0;
		$num_warnings  = 0;
		$num_errors	   = 0;

		$childnode_titles_undefined = array();
		$childnode_titles_ok        = array();
		$childnode_titles_warning   = array();
		$childnode_titles_error     = array();

		foreach($test_results as $test_result){
			switch ( $test_result['result']->getState() ){
				default:
				case TX_CARETAKER_STATE_UNDEFINED:
					$num_undefined ++;
					$childnode_titles_undefined[] = $test_result['node']->getTitle();
					break;
				case TX_CARETAKER_STATE_OK:
					$num_ok ++;
					$childnode_titles_ok[] = $test_result['node']->getTitle();
					break;
				case TX_CARETAKER_STATE_WARNING:
					$num_warnings ++;
					$childnode_titles_warning[] = $test_result['node']->getTitle();
					break;
				case TX_CARETAKER_STATE_ERROR:
					$num_errors ++;
					$childnode_titles_error[] = $test_result['node']->getTitle();
					break;				
			}
		}

		$info = $num_ok.' of '.$num_tests.' subnodes passed the tests. ';

		if ($num_errors > 0){
			$info .= chr(10).'  '.$num_errors.' errors in subnodes: '.implode(', ',$childnode_titles_error).'. ';
		}

		if ($num_warnings > 0){
			$info .= chr(10).'  '.$num_warnings.' warnings in subnodes: '.implode(', ',$childnode_titles_warning).'. ';
		}

		if ($num_undefined > 0){
			$info .= chr(10).'  '.$num_undefined.' undefined subnodes: '.implode(', ',$childnode_titles_undefined).'. ';
		}


		if  ($num_errors > 0){
			return tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_ERROR,$num_undefined,$num_ok,$num_warnings,$num_errors, $info);
		} else if ($num_warnings > 0){
			return tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_WARNING,$num_undefined,$num_ok,$num_warnings,$num_errors, $info);
		} else {
			return tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_OK,$num_undefined,$num_ok,$num_warnings,$num_errors, $info);
		}

	}

        
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getValueDescription()
	 */
	public function getValueDescription(){
		return 'Number of Tests';
	}

         /**
         * Get the number of available Test Results
         *
         * @return integer
         */
        public function getTestResultNumber(){
            $aggregator_result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
            $resultNumber = $aggregator_result_repository->getResultNumberByNode($this);
            return $resultNumber;
        }

        /**
	 * Get the TestResultRange for the Offset and Limit
         *
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getTestResultRange()
	 * @param $graph True by default. Used in the resultrange repository the specify the handling of the last result. For more information see tx_caretaker_testResultRepository.
	 */
	public function getTestResultRangeByOffset($offset=0, $limit=10){
            $aggregator_result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
            $resultRange = $aggregator_result_repository->getResultRangeByNodeAndOffset($this , $offset, $limit);
            return $resultRange;
	}
	
	/**
	 * Get the test configuration overlay (configuration overwritten in instance)
	 *
	 * @param integer $testUid UID of the test
	 */
	public function getTestConfigurationOverlayForTestUid($testUid) {
		$overlayConfig = false;
		if ($this->testConfigurationOverlay) {
			$fftools = new t3lib_flexformtools();
			$tests = $fftools->getArrayValueByPath(
				'data/sDEF/lDEF/testconfigurations/el',
				$this->testConfigurationOverlay
			);

			foreach ($tests as $key => $el) {
				if ($tests[$key]['test']['el']['test_service']['vDEF'] == $testUid) {
					$overlayConfig = $tests[$key]['test']['el']['test_conf']['vDEF'];
					$overlayConfig['hidden'] = $tests[$key]['test']['el']['test_hidden']['vDEF'];
					$overlayConfig['overwritten_in']['title'] = $this->title;
					$overlayConfig['overwritten_in']['uid'] = $this->uid;
					$overlayConfig['overwritten_in']['id'] = $this->getCaretakerNodeId();
				}
			}
		}
		if (!$overlayConfig 
		 && $this->parent
		 && method_exists($this->parent, 'getTestConfigurationOverlayForTestUid')) {
			$overlayConfig = $this->parent->getTestConfigurationOverlayForTestUid($testUid);
		}
		return $overlayConfig;
	}
	
}

?>
