<?php

require_once (t3lib_extMgm::extPath('caretaker').'/interfaces/interface.tx_caretaker_LoggerInterface.php');

class tx_caretaker_CliLogger implements tx_caretaker_LoggerInterface {
	
	private $silentMode = false;
	
    function setSilentMode($silent){
    	$this->silentMode = $silent;
    }
	
    function log($msg){
    	if ($this->silentMode == false){
	    	echo($msg.chr(10));
    	}
    }

}
?>