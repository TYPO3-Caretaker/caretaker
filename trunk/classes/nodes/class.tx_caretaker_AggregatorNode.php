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

require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_AbstractNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_AggregatorResultRepository.php');

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
	 * @return unknown_type
	 */
	abstract protected function findChildren($hidden=false);
	
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
		
		$children  = $this->getChildren();

		if (count($children)>0){		
			$test_results = array(); 
			foreach($children as $child){
				$test_result = $child->updateTestResult($force_update);
				$test_results[] = $test_result;
			}
			$group_result = $this->getAggregatedResult($test_results);
		} else {
			$group_result = tx_caretaker_AggregatorResult::create(TX_CARETAKER_STATE_UNDEFINED, 0, 'No children were found');
		}
		
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		
			// save aggregator node state to cache
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		/*	
		$last_result = $result_repository->getLatestByNode($this);
		if ($group_result->is_different($last_result) ){
			$result_repository->addNodeResult($this, $group_result);
		}*/
		$result_repository->addNodeResult($this, $group_result);
			
		if ($group_result->getState() > 0){
			$this->sendNotification($group_result->getState(), $group_result->getMsg() );
		}
		
		return $group_result;
	}

	/**
	 * Read aggregator node state from DB
	 * @return tx_caretaker_AggregatorResult
	 */
	public function getTestResult(){

		$this->log( 'get', 1 );
		$result_repository = tx_caretaker_AggregatorResultRepository::getInstance();
		$group_result = $result_repository->getLatestByNode($this);
		$this->log( ' |> '.$group_result->getStateInfo().' :: '.$group_result->getMsg(), false );
		return $group_result;
		
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
	private function getAggregatedResult($test_results){
		
		$num_tests = count($test_results);
		$num_undefined = 0;
		$num_errors	= 0;
		$num_ok	= 0;
		$num_warnings  = 0;
		
		for($i= 0 ; $i < $num_tests; $i++ ){
			switch ( $test_results[$i]->getState() ){
				case TX_CARETAKER_STATE_OK:
					$num_ok ++;
					break;
				case TX_CARETAKER_STATE_ERROR:
					$num_errors ++;
					break;
				case TX_CARETAKER_STATE_WARNING:
					$num_warnings ++;
					break;
				case TX_CARETAKER_STATE_UNDEFINED:
					$num_undefined ++;
					break;
			}
		}
		
		$undefined_info = '';
		if ($num_undefined > 0){
			$undefined_info = ' ['.$num_undefined.' results are in undefined state ]';
		} 
		
		$ts = time();
		if  ($num_errors > 0){
			$state = TX_CARETAKER_STATE_ERROR;
			$msg   = $num_errors.' errors and '.$num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info;
		} else if ($num_warnings > 0){
			$state = TX_CARETAKER_STATE_WARNING;
			$msg   = $num_warnings.' warnings in '.$num_tests.' results.'.$undefined_info;
		} else {
			$state = TX_CARETAKER_STATE_OK;
			$msg   = $num_tests.' results are OK'.$undefined_info;
		}
		$aggregated_state = tx_caretaker_AggregatorResult::create($state,$num_undefined,$num_ok,$num_warnings,$num_errors,$msg);
		
		return $aggregated_state;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AbstractNode#getValueDescription()
	 */
	public function getValueDescription(){
		return 'Number of Tests';
	}
	
}

?>