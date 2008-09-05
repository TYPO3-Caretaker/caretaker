<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de>
 * 
 * $$Id: class.tx_caretaker_befunctions.php 33 2008-06-13 14:00:38Z thomas $$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Thomas Hempel <hempel@work.de>
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
/**
 * This class provides various methods that are needed for the backend.
 *
 * @author	Thomas Hempel <hempel@work.de>
 * @package	TYPO3
 * @subpackage	caretaker
 */

class tx_caretaker_befunctions {

	/**
	 * Searches all services that are registered for the type caretaker and returns them as
	 * selectable items for a backend select input field.
	 *
	 * @param array $data: The items that are already in the items list
	 */
	public function serviceItems($data) {
		global $T3_SERVICES;

		$services = $T3_SERVICES['caretaker'];

		if (is_array($services)) {
			foreach ($services as $serviceKey => $serviceConfig) {
				$data['items'][] = array($serviceConfig['title'], $serviceKey);
			}
		}
	}
}

?>