<?php
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Node.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestRepository.php');

class tx_caretaker_Group extends tx_caretaker_Node {

	private $sub_groups;
	
	public function __construct($uid, $title, $parent){
		parent::__construct($uid, $title, $parent, 'Group' );
	}

	public function getUid(){
		return $this->uid;
	} 
	
	public function getTitle(){
		return $this->title;
	} 
	
	public function getGroups($recursive = false){
		if (!$this->sub_groups){
			$group_repository = tx_caretaker_GroupRepository::getInstance();
			$this->sub_groups = $group_repository->getByParentGroupId($this->uid, $this );
		}
		return $this->sub_groups;
	}
	
	function getTests(){
		if (!$this->tests){
			$test_repository = tx_caretaker_TestRepository::getInstance();
			$this->tests = $test_repository->getByGroupId($this->uid, $this);
		}
		
		return $this->tests;
	}
	
	
	function updateState($instance, $force = false){
		
		$this->log('update');
		
		$tests  = $this->getTests();
		$groups = $this->getGroups();
		
		$state = TX_CARETAKER_STATE_UNDEFINED;
		$num_tests    = count($tests)+count($groups);
		$num_errors   = 0;
		$num_warnings = 0;
		
		foreach($tests as $test){
			$test_result = $test->updateState($instance, $force);
			$state = $test_result->getState();
			if ($state == TX_CARETAKER_STATE_ERROR ){
				$num_errors ++;
			} else if ($state == TX_CARETAKER_STATE_WARNING || $state == TX_CARETAKER_STATE_UNDEFINED){
				$num_warnings ++;
			}
		}
		
		foreach($groups as $group){
			$test_result = $group->updateState($instance, $force);
			$state  = $test_result->getState();
			if ($state == TX_CARETAKER_STATE_ERROR ){
				$num_errors ++;
			} else if ($state == TX_CARETAKER_STATE_WARNING || $state == TX_CARETAKER_STATE_UNDEFINED){
				$num_warnings ++;
			}
		}
			
		if  ($num_errors > 0){
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR,$num_tests-$num_errors-$num_warnings, $num_errors.' errors and '.$num_warnings.' warnings in '.$num_tests.' Tests.' );
		} else if ($num_warnings > 0){
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_WARNING,$num_tests-$num_warnings, $num_warnings.' warnings in '.$num_tests.' tests.');
		} else {
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK,$num_tests, $num_tests.' tests are OK');
		}
		
		$this->log( '-> '.$group_result->getState().' :: '.$group_result->getComment(), false );
		
		return $group_result;
	}
		
	function getState($instance){
		
		$this->log( 'get' );
		
		$tests  = $this->getTests();
		$groups = $this->getGroups();
		
		$state = TX_CARETAKER_STATE_UNDEFINED;
		$num_tests    = count($tests)+count($groups);
		$num_errors   = 0;
		$num_warnings = 0;
		
		foreach($tests as $test){
			$test_result = $test->getState($instance);
			$state = $test_result->getState();
			if ($state == TX_CARETAKER_STATE_ERROR ){
				$num_errors ++;
			} else if ($state == TX_CARETAKER_STATE_WARNING || $state == TX_CARETAKER_STATE_UNDEFINED){
				$num_warnings ++;
			}
		}
		
		foreach($groups as $group){
			$test_result = $group->getState($instance);
			$state  = $test_result->getState();
			if ($state == TX_CARETAKER_STATE_ERROR ){
				$num_errors ++;
			} else if ($state == TX_CARETAKER_STATE_WARNING || $state == TX_CARETAKER_STATE_UNDEFINED){
				$num_warnings ++;
			}
		}
			
		if  ($num_errors > 0){
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR,$num_tests-$num_errors-$num_warnings, $num_errors.' errors and '.$num_warnings.' warnings in '.$num_tests.' Tests.' );
		} else if ($num_warnings > 0){
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_WARNING,$num_tests-$num_warnings, $num_warnings.' warnings in '.$num_tests.' tests.');
		} else {
			$group_result = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK,$num_tests, $num_tests.' tests are OK');
		}
		
		$this->log( '-> '.$group_result->getState().' :: '.$group_result->getComment(), false );
		
		return $group_result;
	}
		

}

?>