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
		
	public function getSubgroups($recursive = false){
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
		
	function getState($instance){
		
	}
		

}

?>