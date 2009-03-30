<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_AggregatorNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_GroupRepository.php');

class tx_caretaker_Instance extends tx_caretaker_AggregatorNode {

	public $host;
	private $groups;
	
	function __construct( $uid, $title, $parent, $host) {
		parent::__construct($uid, $title, $parent, 'Instance');
		$this->host = $host;
	}
		
	function getHost (){
		return $this->host;
	}
	
	function getChildren (){
		if (!$this->groups){
			$group_repository = tx_caretaker_GroupRepository::getInstance();
			$this->children = $group_repository->getByInstanceUid($this->uid, $this);
		}
		return $this->children;
	}
	
}

?>