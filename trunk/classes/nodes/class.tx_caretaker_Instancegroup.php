<?php 

require_once(t3lib_extMgm::extPath('caretaker').'/classes/nodes/class.tx_caretaker_AggregatorNode.php');
require_once(t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_NodeRepository.php');

class tx_caretaker_Instancegroup extends tx_caretaker_AggregatorNode {
	
	function __construct( $uid, $title, $parent, $hidden=0) {
		parent::__construct($uid, $title, $parent, 'Instancegroup', $hidden);
	}
	
	function findChildren ($show_hidden=false){
		$node_repository = tx_caretaker_NodeRepository::getInstance();
			// read subgroups
		$subgroups = $node_repository->getInstancegroupsByParentGroupUid($this->uid, $this, $show_hidden );
			// read instances
		$instances = $node_repository->getInstancesByInstancegroupUid($this->uid, $this, $show_hidden );
			// save
		$children = array_merge($subgroups, $instances);
			// 
		return $children;
		
	}
	
	function findParent (){
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$parent = $node_repository->getInstancegroupByChildGroupUid($this->uid, $this );
		return $parent;
	}
	
}

?>