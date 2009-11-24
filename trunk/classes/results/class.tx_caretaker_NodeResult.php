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


abstract class tx_caretaker_NodeResult {
	
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
	 * The Result message 
	 * @var tx_caretaker_ResultMessage
	 */
	protected $message = NULL;

	/**
	 *
	 * @var array
	 */
	protected $submessages='';

	/**
	 * Constructor
	 * @param integer $timestamp  Timestamp of the result
	 * @param integer $state      Status of the result
	 * @param mixed   $message    Result message (string or tx_caretaker_ResultMessage Object )
	 */
	public function __construct ($timestamp, $state, $message , $submessages){
		$this->timestamp = (int)$timestamp;
		$this->state     = (int)$state;
		
		if (is_a($message , 'tx_caretaker_ResultMessage') ){
			$this->message = $message;
		} else {
			$this->message = new tx_caretaker_ResultMessage ($message);
		}
		
		if ($submessages){
			$this->submessages = $submessages;
		}
		
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
	public function getTimestamp(){
		return $this->timestamp;
	}
	
	/**
	 * Return result message
	 * @return string
	 */
	public function getMessage(){
		return $this->message;
	}

	/**
	 * Return the array of submessages
	 * @return array
	 */
	public function getSubMessages(){
		return $this->submessages;
	}

	/**
	 * Get a combined and locallized Info of message and all submessages
	 * @return string
	 */
	public function getLocallizedInfotext(){
		$result = $this->message->getLocallizedInfotext();
		if ($this->submessages ) {
			foreach ($this->submessages as $submessage){
				$result .= chr(10).' - ' . $submessage->getLocallizedInfotext();
			}
		}
		
		return $result;
	}

	/**
	 * Get the locallized StateInformation
	 *
	 * @return string
	 */
	public function getLocallizedStateInfo(){
		return tx_caretaker_LocallizationHelper::locallizeString( 'LLL:EXT:caretaker/locallang_fe.xml:state_' . strtolower( $this->getStateInfo() ) );
	}

	/**
	 * Check if another tx_caretaker_AggregatorResult is equal to this one
	 * @param tx_caretaker_AggregatorResult $result
	 * @return boolean
	 */
	public function equals(tx_caretaker_NodeResult $result){
		if ($this->getResultHash() == $result->getResultHash() ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Check if another tx_caretaker_AggregatorResult is different from this one
	 * @param tx_caretaker_AggregatorResult $result
	 * @return boolean
	 */
	public function isDifferent(tx_caretaker_NodeResult $result){
		if ($this->getResultHash() != $result->getResultHash() ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get a Hash for the given Status. If two results give the same hash they
	 * are considered to be equal.
	 *
	 * @return string ResultHash
	 */
	abstract public function getResultHash();
	
}

?>