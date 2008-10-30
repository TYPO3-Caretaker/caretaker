<?php

require_once('tx_caretaker_Request.php' ) ;

class tx_caretaker_TestConf implements Iterator {
	
	private $requestQueue;
	private $requestCount;
	
	function __construct(){
		$this->requestQueue = Array();
		$this->requestCount = 0;
	}

	private function storeRequest($request){
		$this->requestQueue[] = $request;
		return ( count($this->requestQueue) -1); 
	}
	
	/**
	 * Add a new Instance Request the queue
	 * @param string : Command Code
	 * @param array  : Parameters to pass
	 * @return int   : Result Identifier  
	 */
	
	public function addInstanceRequest($command, $params){
		$request = new tx_caretaker_Request('instance');
		$request->setCommand($command);
		$request->setParams($params);
		return $this->storeRequest($request);
	}
		
	/**
	 * Add a new Shell-Request the queue
	 * @param string : Command Code
	 * @param array  : Parameters to pass
	 * @return int   : Result Identifier  
	 */
	public function addShellRequest($command, $stdin){
		$request = new tx_caretaker_Request('instance');
		$request->setCommand($command);
		$request->setParams($params);
		return $this->storeRequest($request);
	}
	
	/**
	 * Add a new PHP-Request the queue
	 * @param object : Target PHP-Object
	 * @param string : Method Name
	 * @param array  : Parameters to pass
	 * @return int   : Result Identifier  
	 */
	public function addPhpRequest( &$targetObject, $methodName, $params){
		$request = new tx_caretaker_Request('instance');
		$request->setTarget($targetObject);
		$request->setCommand($methodName);
		$request->setParams($params);
		return $this->storeRequest($request);
	}
	
	/**
	 * Get the list of Instance Requests
	 *
	 * @return array
	 */
	public function getRequest($id){
		if ($this->requestQueue[$id]){
			return $this->requestQueue[$id];
		} else {
			return false;
		}
	}
	
	/*
	 * Iterator Methods
	 *
	 */
	
	/**
	 * Iterator rewind
	 */
    public function rewind() {
        reset($this->requestQueue);
    }
    
	/**
	 * Iterator current
	 * @return tx_caretaker_Request;
	 */
    public function current() {
        $var = current($this->requestQueue);
        return $var;
    }
    
	/**
	 * Iterator key
	 * @return int;
	 */
    public function key() {
        $var = key($this->requestQueue);
        return $var;
    }
    
	/**
	 * Iterator next
	 * @return int;
	 */
    public function next() {
        $var = next($this->requestQueue);
        return $var;
    }
    
	/**
	 * Iterator valid
	 * @return boolean;
	 */
    public function valid() {
        $var = $this->current() !== false;
        return $var;
    }
    
    /**
     * Count Test Requests
     * @return int
     */
    public function count() {
        return( count($this->requestQueue) );
    }
}
 
?>