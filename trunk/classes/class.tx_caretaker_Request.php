<?php

class tx_caretaker_Request{
	
		private $type;
		private $command;
		private $input;
		private $params;
		private $target;
		
		public function __construct($type ){
			$this->type = $type;
			$this->command = false;
			$this->target  = false;
			$this->input   = false;
			$this->params  = false;
		} 
		
		/*
		 *  Setters
		 */
		public function setCommand($command){
			$this->command = $command;
		}
		
		public function setTarget($target){
			$this->target = $target;
		}

		public function setInput($input){
			$this->input = $input;
		}
		
		public function setParams($params){
			$this->params = $params;
		}
		
		/*
		 * Getters
		 */
		
		public function getType(){
			return $this->type;
		}
		
		public function getCommand(){
			return $this->command;
		}
		
		public function getInput(){
			return $this->input;
		}
		
		public function getTarget(){
			return $this->target;
		}
		public function getParams(){
			return $this->params;
		}
}

?>