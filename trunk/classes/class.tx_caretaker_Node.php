<?php

abstract class tx_caretaker_Node {
	
	public $uid     = false;
	public $title   = false;
	public $type    = '';
	public $parent = false;
	
	
	public function __construct($uid, $title, $parent, $type=''){
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
	
	public function log($msg, $add_info=true){
		
		if ($this->parent && method_exists($this->parent,'log') ){
			if ($add_info){
				$msg = $this->type.' '.$this->title.'['.$this->uid.'] '.$msg;
			}
			$this->parent->log('  '.$msg , false);
		} 
	}
}
?>