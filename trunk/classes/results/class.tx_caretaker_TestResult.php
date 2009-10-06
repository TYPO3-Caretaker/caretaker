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
	 * Info array [values=>[foo=>123,bar=>baz],details[[message=>foo,values=[bar=baz] ] ] ]
	 * @var Array;
	 */
	protected $info_array=false;

	/**
	 * Constructor 
	 * 
	 * @param integer $timestamp
	 * @param integer $state
	 * @param float   $value
	 * @param string  $message
	 * @param array   $info
	 */
	public function __construct ($timestamp = 0, $state=TX_CARETAKER_STATE_UNDEFINED, $value=0, $message='', $info_array=false){
		parent::__construct($timestamp, $state, $message);
		$this->value   = $value;
		$this->info_array   = $info_array;
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
	 * @param string  $message
	 * @return tx_caretaker_TestResult
	 */
	static public function create($status=TX_CARETAKER_STATE_UNDEFINED, $value=0, $message='' , $info_array=false ){
		$ts = time();
		return new tx_caretaker_TestResult($ts, $status, $value, $message, $info_array) ;
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
	 * Return the Info Array if any is found
	 *
	 * @return array
	 */
	public function getInfoArray(){
		return $this->info_array;
	}

	
	/**
	 *
	 */
	public function getLocallizedMessage(){

		$message = parent::getLocallizedMessage();

			// add value to marker ###VALUE###
		if (strpos($message,'###VALUE###')!== false )
			$message = str_replace( '###VALUE###' , $this->value , $message );

			// add values to ###VALUE_XXX### markers
		$info_array = $this->getInfoArray();
		if ($info_array && $info_array['values'] ){
			foreach ($info_array['values'] as $key=>$value){
				$marker = '###VALUE_'.strtoupper($key).'###';
				if (strpos($message,$marker)!== false ) $message = str_replace($marker, $value, $message);
			}
		}

			// add details
		if ($info_array && $info_array['details'] ){
			foreach ($info_array['details'] as $detail){
				if (is_array($detail)){
					$detail_line = $detail['message'];
					$detail_line = $this->locallizeString($detail_line);
					foreach ($detail['values'] as $key=>$value){
						$marker = '###VALUE_'.strtoupper($key).'###';
						if (strpos($detail_line,$marker)!== false ) $detail_line = str_replace($marker, $value, $detail_line);
					}
				} else {
					$detail_line = $this->locallizeString($detail);;
				}
				$detail_array[] = $detail_line;
			}
			
			if (count($detail_array)){
				$message .= chr(10).'  '.implode( chr(10).'  ' , $detail_array);
			}
		}

		
		return $message;

	}

}

?>