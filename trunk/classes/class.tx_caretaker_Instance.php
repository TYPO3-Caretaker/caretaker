<?php 

require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_AggregatorNode.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestgroupRepository.php');

class tx_caretaker_Instance extends tx_caretaker_AggregatorNode {

	private $url;
	private $host;
	private $ip;
	private $groups;
	
	function __construct( $uid, $title, $parent, $url, $host='', $ip='') {
		parent::__construct($uid, $title, $parent, 'Instance');
		$this->url  = $url;
		$this->host = $host;
		$this->ip   = $ip;
	}
		
	function getUrl (){
		return $this->url;
	}
	
	
	function getHost (){
		return $this->host;
	}
	
	function getIp (){
		return $this->ip;
	}
	
	function getChildren (){
		if (!$this->groups){
			$group_repository = tx_caretaker_TestgroupRepository::getInstance();
			$this->children = $group_repository->getByInstanceUid($this->uid, $this);
		}
		return $this->children;
	}
	
}

?>