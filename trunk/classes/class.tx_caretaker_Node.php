<?php

require_once ('interface.tx_caretaker_LoggerInterface.php');
require_once ('class.tx_caretaker_TestResultRange.php');


abstract class tx_caretaker_Node {
	
	public $uid     = false;
	public $title   = false;
	public $type    = '';
	
	public $parent    = false;
	public $logger    = false;
	public $instance  = false;
	
	public function __construct( $uid, $title, $parent, $type=''){
		$this->uid    = $uid;
		$this->title  = $title;
		$this->parent = $parent;
		$this->type   = $type;
	}

	public function getUid(){
		return $this->uid;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getInstance(){
		
		if ( is_a($this, 'tx_caretaker_Instance') ){
			return $this;
		} else if ($this->parent){
			return $this->parent->getInstance();
		} else {
			trigger_error  ( 'no instance was set'.chr(10) ) ;
			return false;
		}
	}
	
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
	
	abstract public function updateState($force_update = false);
	
	abstract public function getState();
	
}
?>