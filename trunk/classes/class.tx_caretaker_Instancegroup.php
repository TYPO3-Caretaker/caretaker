<?php 

require_once('class.tx_caretaker_AggregatorNode.php');

class tx_caretaker_Instancegroup extends tx_caretaker_AggregatorNode {
	
	function __construct( $uid, $title, $parent) {
		parent::__construct($uid, $title, $parent, 'Instancegroup');
		
	}
	
	function findChildren (){
		
			// read subgroups
		$instancegroup_repository = tx_caretaker_InstancegroupRepository::getInstance();
		$subgroups = $instancegroup_repository->getByParentGroupUid($this->uid, $this );
			// read instances
		$instance_repository = tx_caretaker_InstanceRepository::getInstance();
		$instances = $instance_repository->getByInstancegroupUid($this->uid, $this );
			// save
		$children = array_merge($subgroups, $instances);
		
		return $children;
		
	}
	
	function findParent (){
		$instancegroup_repository = tx_caretaker_InstancegroupRepository::getInstance();
		$parent = $instancegroup_repository->getByChildGroupUid($this->uid, $this );
		return $parent;
	}
	
}

?>