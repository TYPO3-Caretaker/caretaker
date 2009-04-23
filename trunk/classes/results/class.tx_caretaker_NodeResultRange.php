<?php

class tx_caretaker_NodeResultRange implements Iterator {
	
	private $array = array();
	private $ts_min = NULL;
	private $ts_max = NULL;
	
	private $last_ts = 0;
	
	public function __construct($ts_min = 0, $ts_max = 0){
		$this->ts_max = $ts_max;
		$this->ts_min = $ts_min;
	}
	
	/*
	 * Add Result to Range
	 * 
	 */
	function addResult($result){
		
		$ts = (int)$result->getTstamp();
		$this->array[$ts] = $result;
		
		if ($ts < $this->last_ts){
			ksort( $this->array );
		} else {
			$this->last_ts = $ts;
		}
		
		if ($this->ts_min !== NULL && $ts < $this->ts_min ){
			$this->ts_min  = $ts;
		} 
		
		if ($this->ts_max  !== NULL && $ts > $this->ts_max){
			$this->ts_max = $ts;
		}
	}
	
	function getMinTstamp(){
		return $this->ts_min;
	}
	
	function getMaxTstamp(){
		return $this->ts_max;
	}
	
	function getLength(){
		return count($this->array);	
	}
	
	function getFirst(){
		return reset($this->array);
	}
	
	function getLast(){
		return end($this->array);
	}
	

		// Info Methods
	function getInfos(){
		
		$SecondsTotal     = $this->ts_max - $this->ts_min;
		$SecondsUNDEFINED = 0;
		$SecondsOK        = 0;
		$SecondsWARNING   = 0;
		$SecondsERROR     = 0;
		
		$lastTS    = NULL;
		$lastSTATE = NULL;
		foreach( $this->array as $ts=>$result ){
			if ($lastTS){
				
				$range = $ts - $lastTS;
				
				switch ($lastSTATE){
					case -1:
						$SecondsUNDEFINED += $range;
						break;
					case 0:
						$SecondsOK += $range;
						break;
					case 1:
						$SecondsWARNING += $range;
						break;
					case 2:
						$SecondsERROR += $range;
						break;
				}
			}
			$lastTS    = $ts;
			$lastSTATE = $result->getState();
			
		}
		
		return array(
			'SecondsTotal'      =>$SecondsTotal,
			'SecondsUNDEFINED'  =>$SecondsUNDEFINED,
			'SecondsOK'         =>$SecondsOK,
			'SecondsWARNING'    =>$SecondsWARNING,
			'SecondsERROR'      =>$SecondsERROR,
		
			'PercentAVAILABLE'  => ($SecondsTotal - $SecondsERROR - $SecondsWARNING - $SecondsUNDEFINED )/$SecondsTotal,
			'PercentUNDEFINED'  => $SecondsUNDEFINED/$SecondsTotal,
			'PercentOK'         => $SecondsOK/$SecondsTotal,
			'PercentWARNING'    => $SecondsWARNING/$SecondsTotal,
			'PercentERROR'      => $SecondsERROR/$SecondsTotal,
 		);
		
	}	
	
		// Iterator methods
	function rewind() {
		return reset($this->array);
	}

	function current() {
		return current($this->array);
	}

	function key() {
		return key($this->array);
	}

	function next() {
		return next($this->array);
	}

	function valid() {
		return isset( $this->array[key($this->array)] );
	}
	
}

?>