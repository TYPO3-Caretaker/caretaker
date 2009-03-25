<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_GroupRepository.php');

class tx_caretaker_Instance {

	var $uid;
	var $host_name;
	var $host_ip;
	private $db_data;
	private $groups;
	
	function __construct($uid, $host_name='', $host_ip='', $data=array()){
		$this->uid = $uid;
		$this->host_name = $host_name;
		$this->host_ip   = $host_ip;
		$this->db_data   = $data;
	}
	
	
	function getUid (){
		return $this->uid;
	}
	
	function getHost (){
		return $this->host_name;
	}
	
	function getIp (){
		return $this->host_ip;
	}
	
	function getData (){
		return $this->db_data;
	}
	
	function getGroups (){
		if (!$this->groups){
			$group_repository = tx_caretaker_GroupRepository::getInstance();
			$this->groups = $group_repository->getByInstanceId($this->uid);
		}
		return $this->groups;
	}

	/*
	 * update the state of this group
	 */
	function updateState(){
		// debug('updateState Instance:'.$this->uid);
		
		$groups = $this->getGroups();
		foreach($groups as $group){
			$group->updateState($this);
		}
	}
	
	/*
	 * 
	 * @return tx_caretaker_TestResult
	 */
	function getState(){
		$groups = $this->getGroups();
		$state = TX_CARETAKER_UNDEFINED;
		$num_tests = count($tests);
		$num_error = 0;
		$num_warning = 0;
		foreach($groups as $group){
			$result = $group->getState($this);
			$state  = $result->getState();
			if ($state == TX_CARETAKER_ERROR ){
				$num_error ++;
			} else if ($state == TX_CARETAKER_WARNING || $state == TX_CARETAKER_UNDEFINED){
				$num_warning ++;
			}
		}
		if  ($num_error > 0){
			return tx_caretaker_TestResult::create(TX_CARETAKER_ERROR,$num_tests-$num_error-$num_warning, $num_error.' Errors and '.$num_warning.' Warnings in '.$num_tests.' Tests.' );
		} else if ($num_warning > 0){
			return tx_caretaker_TestResult::create(TX_CARETAKER_WARNING,$num_tests-$num_warning, $num_warning.' Warnings in '.$num_tests.' Tests.');
		} else {
			return tx_caretaker_TestResult::create(TX_CARETAKER_OK,$num_tests, '');
		}
	}
	
	
	
}

?>