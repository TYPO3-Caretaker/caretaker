<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
*
* All rights reserved
*
* This script is part of the Caretaker project. The Caretaker project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * Single result of a testNode.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
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
	public function __construct ($timestamp = 0, $state=tx_caretaker_Constants::state_undefined, $value=0, $message='', $submessages){
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
		return new tx_caretaker_TestResult($timestamp, tx_caretaker_Constants::state_undefined, 0, $message);
	}
	
	/**
	 * Create a new testresult with current timestamp
	 * 
	 * @param integer $status
	 * @param float   $value
	 * @param string  $message
	 * @return tx_caretaker_TestResult
	 */
	static public function create($status=tx_caretaker_Constants::state_undefined, $value=0, $message='' , $submessages = NULL ){
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
			'state'   => (int)$this->getState(),
			'value'   => (float)$this->getValue(),
			'message' => $this->getMessage(),
			'submessages' => $this->getSubMessages()
		);
		return md5( serialize( $state ) );
	}

}

?>