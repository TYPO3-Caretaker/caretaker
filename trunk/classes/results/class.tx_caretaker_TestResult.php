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

require_once(t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResult.php');

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
	 */
	public function __construct ($timestamp, $state=TX_CARETAKER_STATE_UNDEFINED, $value=0, $message=''){
		parent::__construct($timestamp, $state, $message);
		$this->value   = $value;
	}
	
	/**
	 * Create a new testresult with state UNKNOWN
	 * 
	 * @return tx_caretaker_TestResult
	 */
	static public function undefined (){
		$timestamp = time();
		return new tx_caretaker_TestResult($timestamp, TX_CARETAKER_STATE_UNDEFINED, 0, 'Result is undefined');
	}
	
	/**
	 * Create a new testresult with current timestamp
	 * 
	 * @param integer $status
	 * @param float   $value
	 * @param string  $comment
	 * @return tx_caretaker_TestResult
	 */
	static public function create($status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $comment=''){
		$ts = time();
		return new tx_caretaker_TestResult($ts, $status, $value, $comment);
	}
	
	/**
	 * Return the value of the result
	 * 
	 * @return unknown_type
	 */
	public function getValue(){
		return $this->value;
	}
	


}

?>