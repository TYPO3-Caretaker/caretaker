<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


define('TX_CARETAKER_STATE_OK',          0);
define('TX_CARETAKER_STATE_WARNING',     1);
define('TX_CARETAKER_STATE_ERROR',       2);
define('TX_CARETAKER_STATE_UNDEFINED',  -1);

class tx_caretaker_NodeResult {
	
	/**
	 * Status Code of the Test Result
	 * @var integer  
	 */
	protected $state=0;
	
	/**
	 * Timestamp of the testresult 
	 * @var integer 
	 */
	protected $timestamp = NULL;
	
	/**
	 * A human readable representation of the testrsult
	 * @var string
	 */
	protected $message='';
	
	
	/**
	 * Constructor
	 * @param integer $timestamp  Timestamp of the result
	 * @param integer $state      Status of the result
	 * @param string  $message    Result message
	 */
	public function __construct ($timestamp, $state, $message=''){
		$this->timestamp = (int)$timestamp;
		$this->state     = (int)$state;
		$this->message   = $message;
	}
		
	/**
	 * Return Status Code of the Test
	 * @return integer 
	 */
	public function getState(){
		return $this->state;
	}
	
	/**
	 * Return human readable status message
	 * @return string
	 */
	public function getStateInfo (){
		switch ($this->state){
			case TX_CARETAKER_STATE_OK:
				return 'OK';
			case TX_CARETAKER_STATE_ERROR:
				return 'ERROR';
			case TX_CARETAKER_STATE_WARNING:
				return 'WARNING';
			case TX_CARETAKER_STATE_UNDEFINED:
				return 'UNDEFINED';
		}
	}
	
	/**
	 * Get Timestamp of this Testresult
	 * @return integer
	 * @depricated
	 * @todo remove this method
	 */
	public function getTstamp(){
		return $this->timestamp;
	}
	
	/**
	 * Get Timestamp of this Testresult
	 * @return integer
	 */
	function getTimestamp(){
		return $this->timestamp;
	}
	
	/**
	 * Return result message
	 * 
	 * @return string
	 * @depricated 
	 * @todo remove this method
	 */
	public function getMsg(){
		return $this->message;
	}
	
	/**
	 * Return result message
	 * 
	 * @return string
	 */
	public function getMessage(){
		return $this->message;
	}


	/**
	 * Get the locallized StateInformation
	 *
	 * @return string
	 */
	public function getLocallizedStateInfo(){
		return tx_caretaker_Helper::locallizeString( 'LLL:EXT:caretaker/locallang_fe.xml:state_' . strtolower( $this->getStateInfo() ) );
	}

	/**
	 * Get the locallized Result Message
	 *
	 * @return string
	 */
	public function getLocallizedMessage(){

		$message = $this->getMessage();

			// locallize
		$message = tx_caretaker_Helper::locallizeString($message);
		if (strpos($message,'###STATE###')!== false )$message = str_replace('###STATE###',$this->getLocallizedStateInfo(), $message);
		
		return $message;
		
	}

	
}

?>