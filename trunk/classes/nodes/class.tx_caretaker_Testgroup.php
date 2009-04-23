<?php
 
require_once (t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_AggregatorNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_NodeRepository.php');

class tx_caretaker_Testgroup extends tx_caretaker_AggregatorNode {
	
	public function __construct($uid, $title, $parent, $hidden=0){
		parent::__construct($uid, $title, $parent, 'Testgroup',$hidden );
	}
	
	function findChildren ($show_hidden=false){
		
			// read subgroups
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$subgroups = $node_repository->getTestgroupsByParentGroupUid($this->uid, $this , $show_hidden );
			// read instances
		$tests = $node_repository->getTestsByGroupId($this->uid, $this, $show_hidden);
			// save
		$children = array_merge($subgroups, $tests);
		return $children;
	}
	

}

?>