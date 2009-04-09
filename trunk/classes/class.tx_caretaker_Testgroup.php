<?php
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_AggregatorNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_NodeRepository.php');

class tx_caretaker_Testgroup extends tx_caretaker_AggregatorNode {
	
	public function __construct($uid, $title, $parent){
		parent::__construct($uid, $title, $parent, 'Testgroup' );
	}
	
	function findChildren (){
		
			// read subgroups
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$subgroups = $node_repository->getTestgroupsByParentGroupUid($this->uid, $this );
			// read instances
		$tests = $node_repository->getTestsByGroupId($this->uid, $this);
			// save
		$children = array_merge($subgroups, $tests);
		return $children;
	}
	

}

?>