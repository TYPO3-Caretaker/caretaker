<?php 

require_once('class.tx_caretaker_AggregatorNode.php');

class tx_caretaker_Instancegroup extends tx_caretaker_AggregatorNode {
	
	function __construct( $uid, $title, $parent) {
		parent::__construct($uid, $title, $parent, 'Instancegroup');
		
	}
	
	function getChildren (){
		
		if (!$this->children){
				// read subgroups
			$instancegroup_repository = tx_caretaker_InstancegroupRepository::getInstance();
			$subgroups = $instancegroup_repository->getByParentGroupUid($this->uid, $this );
				// read instances
			$instance_repository = tx_caretaker_InstanceRepository::getInstance();
			$instances = $instance_repository->getByInstancegroupUid($this->uid, $this );
				// save
			$this->children = array_merge($subgroups, $instances);
			
		}
		return $this->children;
	}
	
}

?>