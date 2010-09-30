<?php 

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
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
 * Baseclass for all nodes which are aggregating the status
 * of subodes like root, instancegroup, instance and testgroup.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
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

		$this->notify( 'updateAggregatorNode' );

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

		$this->notify( 'updateAggregatorNode' , $groupResult, $lastGroupResult);
		
		return $groupResult;
	}

	/**
	 * Read aggregator node state from DB
	 * @return tx_caretaker_AggregatorResult
	 */
	public function getTestResult(){

		if ( $this->getHidden() == true ){
			$group_result = tx_caretaker_AggregatorResult::undefined('Node is disabled');
		} else {
			$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
			$group_result = $result_repository->getLatestByNode($this);
		}
		
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
		$num_due	   = 0;
		$num_ack	   = 0;
		
		$childnode_titles_undefined = array();
		$childnode_titles_ok        = array();
		$childnode_titles_warning   = array();
		$childnode_titles_error     = array();
		$childnode_titles_ack       = array();
		$childnode_titles_due       = array();
		

		if ( is_array( $test_results ) ) {
			foreach( $test_results as $test_result ){
				switch ( $test_result['result']->getState() ){
					default:
					case tx_caretaker_Constants::state_undefined:
						$num_undefined ++;
						$childnode_titles_undefined[] = $test_result['node']->getTitle();
						break;
					case tx_caretaker_Constants::state_ack:
						$num_ack ++;
						$num_undefined ++;
						$childnode_titles_ack[] = $test_result['node']->getTitle();
						break;	
					case tx_caretaker_Constants::state_due:
						$num_due ++;
						$num_undefined ++;
						$childnode_titles_due[] = $test_result['node']->getTitle();
						break;
					case tx_caretaker_Constants::state_ok:
						$num_ok ++;
						$childnode_titles_ok[] = $test_result['node']->getTitle();
						break;
					case tx_caretaker_Constants::state_warning:
						$num_warnings ++;
						$childnode_titles_warning[] = $test_result['node']->getTitle();
						break;
					case tx_caretaker_Constants::state_error:
						$num_errors ++;
						$childnode_titles_error[] = $test_result['node']->getTitle();
						break;
				}
			}
		}

		$values = array(
			'num_tests'=>$num_tests,
			'num_ok'=>$num_ok,
			'num_warning'=>$num_warnings,
			'num_error'=>$num_errors,
			'num_undefined'=>$num_undefined,
			'num_ack'=>$num_undefined,
			'num_due'=>$num_undefined
		);

		$message = new tx_caretaker_ResultMessage( 
			'LLL:EXT:caretaker/locallang_fe.xml:aggregator_result_message',
			$values
		);

			// create Submessages
		$submessages = array();
		
		if ($num_errors > 0){
			foreach ($childnode_titles_error as $childTitle){
				$submessages[] = new tx_caretaker_ResultMessage(
					'LLL:EXT:caretaker/locallang_fe.xml:aggregator_result_submessage_error',
					array('title'=>$childTitle)
				);
			}
		}

		if ($num_warnings > 0){
			foreach ($childnode_titles_warning as $childTitle){
				$submessages[] = new tx_caretaker_ResultMessage(
					'LLL:EXT:caretaker/locallang_fe.xml:aggregator_result_submessage_warning',
					array('title'=>$childTitle)
				);
			}
		}

		if ($num_undefined > 0){
			foreach ($childnode_titles_undefined as $childTitle){
				$submessages[] = new tx_caretaker_ResultMessage(
					'LLL:EXT:caretaker/locallang_fe.xml:aggregator_result_submessage_undefined',
					array('title'=>$childTitle)
				);
			}
		}
		
		if ($num_ack > 0){
			foreach ($childnode_titles_ack as $childTitle){
				$submessages[] = new tx_caretaker_ResultMessage(
					'LLL:EXT:caretaker/locallang_fe.xml:aggregator_result_submessage_ack',
					array('title'=>$childTitle)
				);
			}
		}
		
		if ($num_due > 0){
			foreach ($childnode_titles_due as $childTitle){
				$submessages[] = new tx_caretaker_ResultMessage(
					'LLL:EXT:caretaker/locallang_fe.xml:aggregator_result_submessage_due',
					array('title'=>$childTitle)
				);
			}
		}

		if  ($num_errors > 0){
			return tx_caretaker_AggregatorResult::create(tx_caretaker_Constants::state_error,$num_undefined,$num_ok,$num_warnings,$num_errors, $message, $submessages);
		} else if ( $num_warnings > 0 ){
			return tx_caretaker_AggregatorResult::create(tx_caretaker_Constants::state_warning,$num_undefined,$num_ok,$num_warnings,$num_errors, $message, $submessages);
		} else if ( $num_undefined == $num_tests ) {
			return tx_caretaker_AggregatorResult::create(tx_caretaker_Constants::state_undefined,$num_undefined,$num_ok,$num_warnings,$num_errors, $message, $submessages);
		} else {
			return tx_caretaker_AggregatorResult::create(tx_caretaker_Constants::state_ok,$num_undefined,$num_ok,$num_warnings,$num_errors, $message, $submessages);
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
			if (is_array($tests)) {
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
		}
		if (!$overlayConfig 
		 && $this->parent
		 && method_exists($this->parent, 'getTestConfigurationOverlayForTestUid')) {
			$overlayConfig = $this->parent->getTestConfigurationOverlayForTestUid($testUid);
		}
		return $overlayConfig;
	}

	/**
	 * Fetches all assigned strategies and returns them in an array
	 *
	 * @return array
	 */
	public function getStrategies() {
		$strategies = array();

		$strategyCount = intval($this->getProperty('notification_strategies'));
		if ($strategyCount <= 0) return $strategies;

			// select strategies from database
		$strategies = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			's.*',
			tx_caretaker_Constants::table_Strategies.' s,'.tx_caretaker_Constants::relationTable_Node2Strategy.' rel',
			'rel.uid_node='.$this->getUid().' AND rel.node_table=\''.$this->getStorageTable().'\' AND rel.uid_strategy=s.uid');
		
		return $strategies;
	}
	
}

?>
