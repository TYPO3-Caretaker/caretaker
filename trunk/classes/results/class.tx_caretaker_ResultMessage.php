<?php

/**
 * Description of classtx_caretaker_ResultMessage
 *
 * @author martin
 */
class tx_caretaker_ResultMessage {


	/**
	 * The Result Message string. Can be a LLL: String or can contain {LLL:parts}
	 * @var string;
	 */
	protected $message;

	/**
	 * Associative Array of values which should be inserted in the locallized message
	 * @var array;
	 */
	protected $values;
	
	/**
	 * Constructor
	 * 
	 * @param string $message 
	 * @param array $values
	 */
	public function __construct ( $message='', $values=array() ){
		$this->message = $message;
		$this->values  = $values;
	}

	public function getMessage (){
		return $this->message;
	}

	public function getValues (){
		return $this->values;
	}

	public function getLocallizedMessage(){
		
	}

}
?>
