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

require_once(t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResultRange.php');

class tx_caretaker_TestResultRange extends tx_caretaker_NodeResultRange {
	
	/**
	 * Minimal value of this result range
	 * @var float
	 */
	var $min_value = 0;
	
	/**
	 * Maximal value of this result range
	 * @var float
	 */
	var $max_value = 0;
	
	/**
	 * Add a TestResult to the ResultRange
	 * @see caretaker/trunk/classes/results/tx_caretaker_NodeResultRange#addResult()
	 */
	public function addResult($result ){
		
		parent::addResult($result);
		
		$value = $result->getValue();
		if ($value < $this->min_value){
			$this->min_value = $value;
		} else if ($value > $this->max_value){
			$this->max_value = $value;
		}
		
	}
	
	/**
	 * Return the minimal result value 
	 * @return float
	 */
	public function getMinValue(){
		return $this->min_value;
	}
	
	/**
	 * Return the maximal result value
	 * @return float
	 */
	public function getMaxValue(){
		return $this->max_value;
	}
			
}

?>