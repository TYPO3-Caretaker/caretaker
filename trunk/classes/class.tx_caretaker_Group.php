<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestRepository.php');


class tx_caretaker_Group {

	var $uid; 
	var $title;
	private	$data; 
	private $sub_groups;
	
	public function __construct($uid, $title, $data){
		$this->uid   = $uid; 
		$this->title = $title;
		$this->data  = $data;
	}
		
	public function getGroups($recursive = false){
		if (!$this->sub_groups){
			$group_repository = tx_caretaker_GroupRepository::getInstance();
			$this->sub_groups = $group_repository->getByParentId($this->uid, $recursive );
		}
		return $this->sub_groups;
	}
	
	function getTests(){
		if (!$this->tests){
			$test_repository = tx_caretaker_TestRepository::getInstance();
			$this->tests = $test_repository->getByGroupId($this->uid);
		}
		
		return $this->tests;
	}
		
	function updateState($instance){
		//debug('updateState Group:'.$this->title.$this->uid);
		
		$tests = $this->getTests();
		foreach($tests as $test){
			$test->updateState($instance);
		}
		
		$groups = $this->getGroups();
		foreach($groups as $group){
			$group->updateState($instance);
		}
		
	}
	
	function getState($instance){
		
		$tests  = $this->getTests();
		$groups = $this->getGroups();
		
		$state = TX_CARETAKER_UNDEFINED;
		$num_tests = count($tests)+count($groups);
		$num_errors = 0;
		$num_warnings = 0;
		
		foreach($tests as $test){
			$result = $test->getState($instance);
			$state = $result->getState();
			if ($state == TX_CARETAKER_ERROR ){
				$num_errors ++;
			} else if ($state == TX_CARETAKER_WARNING || $state == TX_CARETAKER_UNDEFINED){
				$num_warnings ++;
			}
		}
		
		foreach($groups as $group){
			$result = $group->getState($instance);
			$state  = $result->getState();
			if ($state == TX_CARETAKER_ERROR ){
				$num_errors ++;
			} else if ($state == TX_CARETAKER_WARNING || $state == TX_CARETAKER_UNDEFINED){
				$num_warnings ++;
			}
		}
		
		if  ($num_errors > 0){
			return tx_caretaker_TestResult::create(TX_CARETAKER_ERROR,$num_tests-$num_errors-$num_warnings, $num_errors.' Errors and '.$num_warnings.' Warnings in '.$num_tests.' Tests.' );
		} else if ($num_warnings > 0){
			return tx_caretaker_TestResult::create(TX_CARETAKER_WARNING,$num_tests-$num_warnings, $num_warnings.' Warnings in '.$num_tests.' Tests.');
		} else {
			return tx_caretaker_TestResult::create(TX_CARETAKER_OK,$num_tests, '');
		}
	}
		

}

?>