<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_GroupRepository.php');

class tx_caretaker_Instance {

	var $uid;
	var $host_name;
	var $host_ip;
	private $db_data;
	private $groups;
	
	function __construct($id, $host_name='', $host_ip='', $data=array()){
		$this->uid = $id;
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
	
	function getGroupsRecursive(){
		$groups = $this->getGroups();
		$subgroups = array();
		foreach ($groups as $group){
			$subgroups = array_merge($subgroups, $group->getSubgroups(true) );
		}
		return array_merge($groups, $subgroups);
	}
	
	function getTestsRecursive(){
		$groups = $this->getGroupsRecursive();
		$tests = array();
		foreach($groups as $group){
			$tests = array_merge($tests, $group->getTests() );	
		}
		return $tests;
	}
	
	function getPendingTests(){
		$allTests = $this->getTestsRecursive();
		$pendingTests = array();
		foreach ($allTests as $test){
			if ($test->isPending($this) ){
				$pendingTests[] = $test;
			}
		}
		return $pendingTests;
	}
	
	/*
	 * 
	 * @return tx_caretaker_TestResult
	 */
	function getState(){
		
	}
	
	
	
	
}

?>