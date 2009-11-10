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

class tx_caretaker_AggregatorResult extends tx_caretaker_NodeResult {
	
	/**
	 * Number of subtests with state UNDEFINED
	 * @var integer
	 */
	protected $num_UNDEFINED=0;
	
	/**
	 * Number of subtests with state OK
	 * @var integer
	 */	
	protected $num_OK=0;

	/**
	 * Number of subtests with state ERROR
	 * @var integer
	 */
	protected $num_ERROR=0;
	
	/**
	 * Number of subtests with state WARNING
	 * @var integer
	 */
	protected $num_WARNING=0;
	

	/**
	 * Constructor 
	 * 
	 * @param integer $timestamp
	 * @param integer $state
	 * @param integer $num_undefined
	 * @param integer $num_ok
	 * @param integer $num_warning
	 * @param integer $num_error
	 * @param string  $message
	 */
	public function __construct ($timestamp = 0, $state=TX_CARETAKER_STATE_UNDEFINED, $num_undefined=0, $num_ok=0, $num_warning=0, $num_error=0, $message=''){
		parent::__construct($timestamp, $state, $message);
		$this->num_UNDEFINED = $num_undefined;
		$this->num_OK        = $num_ok; 
		$this->num_WARNING   = $num_warning;
		$this->num_ERROR     = $num_error;
		
	}
	
	/**
	 * Create an undefined result with current timestamp
	 * 
	 * @return tx_caretaker_AggregatorResult
	 */
	static public function undefined (){
		$ts = time();
		return new tx_caretaker_AggregatorResult($ts, TX_CARETAKER_STATE_UNDEFINED, $undefined=0, $ok=0, $warning=0, $error=0, 'Result is undefined');
	}
	
	/**
	 * Create a result with current timestamp
	 * 
	 * @param integer $state
	 * @param integer $num_undefined
	 * @param integer $num_ok
	 * @param integer $num_warning
	 * @param integer $num_error
	 * @param string  $message
	 * @return tx_caretaker_AggregatorResult
	 */
	static public function create($state=TX_CARETAKER_STATE_UNDEFINED, $num_undefined=0, $num_ok=0, $num_warning=0, $num_error=0, $message=''){
		$timestamp = time();
		return new tx_caretaker_AggregatorResult($timestamp, $state, $num_undefined, $num_ok, $num_warning, $num_error, $message);
	}

	/**
	 * Return number of children with state UNDEFINED
	 * @return unknown_type
	 */
	public function getNumUNDEFINED(){
		return $this->num_UNDEFINED;
	}
	
	/**
	 * Return number of children with state OK
	 * @return unknown_type
	 */
	public function getNumOK(){
		return $this->num_OK;
	}

	/**
	 * Return number of children with state WARNING
	 * @return unknown_type
	 */
	public function getNumWARNING(){
		return $this->num_WARNING;
	}
	
	/**
	 * Return number of children with state ERROR
	 * @return unknown_type
	 */	
	public function getNumERROR(){
		return $this->num_ERROR;
	}
	
	/**
	 * Check if another tx_caretaker_AggregatorResult is different from this one
	 * @param tx_caretaker_AggregatorResult $result  	
	 * @return boolean
	 */
	public function is_different(tx_caretaker_AggregatorResult $result){
		if ( 
			$this->status        != $result->getState()||
			$this->num_UNDEFINED != $result->getNumUNDEFINED() ||
			$this->num_OK        != $result->getNumOK() ||
			$this->num_WARNING   != $result->getNumWARNING() ||
			$this->num_ERROR     != $result->getNumERROR()
		) {
			return false;
		} else {
			return true;
		}
	}

}

?>