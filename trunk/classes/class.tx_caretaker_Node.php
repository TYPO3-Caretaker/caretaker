<?php

require_once ('interface.tx_caretaker_LoggerInterface.php');
require_once ('interface.tx_caretaker_NotifierInterface.php');
require_once ('class.tx_caretaker_TestResultRange.php');

abstract class tx_caretaker_Node {
	
	protected $uid       = false;
	protected $title     = false;
	protected $type      = '';
	protected $parent    = NULL;
	protected $logger    = false;
	protected $notifier  = false;
	protected $notificationIds = array();
	
	public function __construct( $uid, $title, $parent, $type=''){
		$this->uid    = $uid;
		$this->title  = $title;
		$this->parent = $parent;
		$this->type   = $type;
	}
	
	public function setNotificationIds($id_array){
		$this->notificationIds = $id_array;
	}
	
	public function getUid(){
		return $this->uid;
	}
	
	public function getPid(){
		
	}
	
	public function getHidden(){
		return $this->hidden;
	}
	
		
	public function getTitle(){
		return $this->title;
	}
	
	public function getType(){
		return $this->type;
	}	
	
	public function getInstance(){
		
		if ( is_a($this, 'tx_caretaker_Instance') ){
			return $this;
		} else if ($this->parent){
			return $this->parent->getInstance();
		} else {
			return false;
		}
	}
	
	abstract public function updateTestResult($force_update = false);
	
	abstract public function getTestResult();
	
	abstract public function getTestResultRange($startdate, $stopdate, $distance = FALSE);
	
	public function getRange($start, $stop){
		return new tx_caretaker_TestResultRange();
	}
		
	
	/*
	 * Logging Methods
	 */
	
	public function setLogger (tx_caretaker_LoggerInterface $logger){
		$this->logger = $logger;
	}
	

	
	public function log($msg, $add_info=true){
		if ($add_info){
				$msg = ' +- '.$this->type.' '.$this->title.'['.$this->uid.'] '.$msg;
		}
		if ($this->logger){
			$this->logger->log($msg);
		} else if ($this->parent) {
			$this->parent->log(' | '.$msg , false);
		}
	}
	
	/*
	 * Notification Methods
	 */
	
	public function setNotifier (tx_caretaker_NotifierInterface $notifier){
		$this->notifier = $notifier;
	}
	
	public function sendNotification( $state, $msg){
		if ( count($this->notificationIds) > 0 ){ 
			foreach($this->notificationIds as $notfificationId){
				$this->notify( $notfificationId, $state, $this->type.' '.$this->title.'['.$this->uid.'] '.$msg);
			}
		}
	}
	
	private function notify( $recipients, $state, $msg){
		if ($this->notifier){
			$this->notifier->addNotification($recipients, $state, $msg);
		} else if ($this->parent) {
			$this->parent->notify($recipients, $state, $msg, false);
		}
	}
	

}
?>