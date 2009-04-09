<?php 

require_once('class.tx_caretaker_AggregatorNode.php');
require_once('class.tx_caretaker_NodeRepository.php');

class tx_caretaker_Instancegroup extends tx_caretaker_AggregatorNode {
	
	function __construct( $uid, $title, $parent) {
		parent::__construct($uid, $title, $parent, 'Instancegroup');
		
	}
	
	function findChildren (){
		$node_repository = tx_caretaker_NodeRepository::getInstance();
			// read subgroups
		$subgroups = $node_repository->getInstancegroupsByParentGroupUid($this->uid, $this );
			// read instances
		$instances = $node_repository->getInstancesByInstancegroupUid($this->uid, $this );
			// save
		$children = array_merge($subgroups, $instances);
		
		return $children;
		
	}
	
	function findParent (){
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$parent = $node_repository->getInstancegroupByChildGroupUid($this->uid, $this );
		return $parent;
	}
	
}

?>