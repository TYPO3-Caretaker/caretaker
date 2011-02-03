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
 * $Id: class.tx_caretaker_NodeInfo.php 41726 2011-01-03 12:16:11Z martoro $
 */

/**
 * Testservice for creating a timestamp by "touching" a file (e.g. for checking that caretaker
 * is executed properly). This test will only create a file that can be checked
 * by other infrastructure monitoring systems.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_TouchTestService extends tx_caretaker_TestServiceBase {

	/**
	 * Value Description
	 * @var string
	 */
	protected $valueDescription = '';

	/**
	 * Service type description in human readble form.
	 * @var string
	 */
	protected $typeDescription = 'LLL:EXT:caretaker/locallang_fe.xml:touch_service_description';

	/**
	 * Template to display the test Configuration in human readable form.
	 * @var string
	 */
	protected $configurationInfoTemplate = 'LLL:EXT:caretaker/locallang_fe.xml:touch_service_configuration';

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
	 */
	function runTest() {
		$filename = $this->getTimestampFilename();
		$time = time();
		if (file_put_contents($filename, $time) !== FALSE) {
			return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ok);
		} else {
			return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error, 0, 'Could not touch file ' . $filename);
		}
	}

	/**
	 * The configured filename for the timestamp
	 * @return string
	 */
	protected function getTimestampFilename() {
		return $this->getConfigValue('timestamp_filename');
	}
}

?>