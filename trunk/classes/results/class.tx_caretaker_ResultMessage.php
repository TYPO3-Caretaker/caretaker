<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
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
 * A Message which can be attached to node-result objects.
 * The object handles locallization and value insertion on dispplay time.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_ResultMessage {


	/**
	 * The Result Message string. Can be a LLL: String or can contain {LLL:parts}
	 * @var string;
	 */
	protected $text;

	/**
	 * Associative Array of values which should be inserted in the locallized message
	 * @var array;
	 */
	protected $values;
	
	/**
	 * Constructor
	 * 
	 * @param string $text
	 * @param array $values
	 */
	public function __construct ( $text='', $values=array() ){
		$this->text    = $text;
		$this->values  = $values;
	}

	/**
	 * Get the plain unlocallized text
	 * @return string
	 */
	public function getText (){
		return $this->text;
	}

	/**
	 * Get the value array which will be merged with the text
	 * @return array
	 */
	public function getValues (){
		return $this->values;
	}

	/**
	 * Get the locallized and valuemerged message to show
	 * @return sring
	 */
	public function getLocallizedInfotext(){
		
		$result = $this->text;

			// check for LLL strings
		$result = tx_caretaker_LocallizationHelper::locallizeString($result);
		
			// insert Values
		foreach ( $this->values as $key=>$value){
			$marker = '###VALUE_'.strtoupper($key).'###';
			$result = str_replace($marker, $value, $result);
		}

		return $result;
	}
}
?>
