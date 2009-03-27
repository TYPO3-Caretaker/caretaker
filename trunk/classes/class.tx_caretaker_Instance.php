<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Node.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_GroupRepository.php');

class tx_caretaker_Instance extends tx_caretaker_Node {

	public $host;
	private $groups;
	
	function __construct( $uid, $title, $parent, $host) {
		parent::__construct($uid, $title, $parent, 'Instance');
		$this->host = $host;
	}
		
	function getHost (){
		return $this->host;
	}
	
	function getGroups (){
		if (!$this->groups){
			$group_repository = tx_caretaker_GroupRepository::getInstance();
			$this->groups = $group_repository->getByInstanceId($this->uid, $this);
		}
		return $this->groups;
	}
	
	function updateState( $force = false ){
		
		$this->log( 'update' );
		
		$groups = $this->getGroups();
		$state = TX_CARETAKER_STATE_UNDEFINED;
		$num_tests = count($groups);
		$num_error   = 0;
		$num_warning = 0;
		foreach($groups as $group){
			$test_result = $group->updateState($this, $force);
			$state  = $test_result->getState();
			if ( $state == TX_CARETAKER_STATE_ERROR ){
				$num_error ++;
			} else if ($state == TX_CARETAKER_STATE_WARNING || $state == TX_CARETAKER_STATE_UNDEFINED){
				$num_warning ++;
			}
		}
		if  ($num_error > 0){
			$instance_result = tx_caretaker_TestResult::create(TX_CARETAKER_ERROR,$num_tests-$num_error-$num_warning, $num_error.' Errors and '.$num_warning.' Warnings in '.$num_tests.' Tests.' );
		} else if ($num_warning > 0){
			$instance_result = tx_caretaker_TestResult::create(TX_CARETAKER_WARNING,$num_tests-$num_warning, $num_warning.' Warnings in '.$num_tests.' Tests.');
		} else {
			$instance_result = tx_caretaker_TestResult::create(TX_CARETAKER_OK,$num_tests, $num_tests.' groups are ok');
		}
		
		$this->log( '-> '.$instance_result->getState().' :: '.$instance_result->getComment(), false );
		
		return $instance_result;
		
	}
	
	/*
	 * 
	 * @return tx_caretaker_TestResult
	 */
	function getState(  ){
		
		$this->log( 'get' );
		
		$groups = $this->getGroups();
		$state = TX_CARETAKER_STATE_UNDEFINED;
		$num_tests = count($groups);
		$num_error   = 0;
		$num_warning = 0;
		foreach($groups as $group){
			$test_result = $group->getState($this, $update);
			$state  = $test_result->getState();
			if ( $state == TX_CARETAKER_STATE_ERROR ){
				$num_error ++;
			} else if ($state == TX_CARETAKER_STATE_WARNING || $state == TX_CARETAKER_STATE_UNDEFINED){
				$num_warning ++;
			}
		}
		if  ($num_error > 0){
			$instance_result = tx_caretaker_TestResult::create(TX_CARETAKER_ERROR,$num_tests-$num_error-$num_warning, $num_error.' Errors and '.$num_warning.' Warnings in '.$num_tests.' Tests.' );
		} else if ($num_warning > 0){
			$instance_result = tx_caretaker_TestResult::create(TX_CARETAKER_WARNING,$num_tests-$num_warning, $num_warning.' Warnings in '.$num_tests.' Tests.');
		} else {
			$instance_result = tx_caretaker_TestResult::create(TX_CARETAKER_OK,$num_tests, $num_tests.' groups are ok');
		}
		
		$this->log( '-> '.$instance_result->getState().' :: '.$instance_result->getComment(), false );
		
		return $instance_result;
	}
}

?>