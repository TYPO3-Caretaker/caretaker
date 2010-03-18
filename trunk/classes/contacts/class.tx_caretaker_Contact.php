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
 * $Id: class.tx_caretaker_AggregatorResultRepository.php 27092 2009-11-27 22:30:42Z martoro $
 */

/**
 * Contact object (exposing tt_address as an object decorated with a role)
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_Contact {

	/**
	 *
	 * @var tx_caretaker_ContactRole
	 */
	private $role;

	/**
	 * tt_address row
	 * @var array
	 */
	private $address;

	/**
	 * Constructor
	 * 
	 * @param array $address
	 * @param tx_caretaker_ContactRole $role
	 */
	public function __construct ($address, $role){
		$this->role = $role;
		$this->address = $address;
	}

	/**
	 * Get the Role
	 *
	 * @return tx_caretaker_ContactRole
	 */
	public function getRole(){
		return $this->role;
	}

	/**
	 * Get the address
	 *
	 * @return array
	 */
	public function getAddress(){
		return $this->address;
	}

	/**
	 * Get single address property
	 *
	 * @param string $propertyName
	 * @return mixed
	 */
	public function getAddressProperty( $propertyName ){
		if ( $address[$propertyName] ){
			return $address[$propertyName];
		} else {
			return '';
		}
	}

}
?>
