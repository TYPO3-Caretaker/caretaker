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

class tx_caretaker_TestResult extends tx_caretaker_NodeResult {
	
	/**
	 * Value of the testresult
	 * @var float
	 */
	protected $value=0;
	
	/**
	 * Constructor 
	 * 
	 * @param integer $timestamp
	 * @param integer $state
	 * @param float   $value
	 * @param string  $message
	 * @param array   $info
	 */
	public function __construct ($timestamp = 0, $state=TX_CARETAKER_STATE_UNDEFINED, $value=0, $message='', $submessages){
		parent::__construct($timestamp, $state, $message, $submessages);
		$this->value   = $value;
	}
	
	/**
	 * Create a new testresult with state UNKNOWN
	 * 
	 * @return tx_caretaker_TestResult
	 */
	static public function undefined ($message = 'Result is undefined'){
		$timestamp = time();
		return new tx_caretaker_TestResult($timestamp, TX_CARETAKER_STATE_UNDEFINED, 0, $message);
	}
	
	/**
	 * Create a new testresult with current timestamp
	 * 
	 * @param integer $status
	 * @param float   $value
	 * @param string  $message
	 * @return tx_caretaker_TestResult
	 */
	static public function create($status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $message='' , $submessages = NULL ){
		$ts = time();
		return new tx_caretaker_TestResult($ts, $status, $value, $message, $submessages) ;
	}
	
	/**
	 * Return the value of the result
	 * 
	 * @return unknown_type
	 */
	public function getValue(){
		return $this->value;
	}

	/**
	 * Get a combined and locallized Info of message and all submessages
	 * @return string
	 */
	public function getLocallizedInfotext(){
		$result = parent::getLocallizedInfotext();
		$result = str_replace ( '###STATE###'  , $this->getLocallizedStateInfo() , $result );
		$result = str_replace ( '###VALUE###'  , $this->getValue() , $result );
		return $result;
	}

	/**
	 * Get a Hash for the given Status. If two results give the same hash they
	 * are considered to be equal.
	 *
	 * @return string ResultHash
	 */
	public function getResultHash (){
		$state = array (
			'state' => (int)$this->getState(),
			'value' => (float)$this->getValue()
		);
		return md5( serialize( $state ) );
	}

}

?>