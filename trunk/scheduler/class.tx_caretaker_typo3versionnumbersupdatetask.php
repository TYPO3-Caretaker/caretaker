<?php

/***************************************************************
* Copyright notice
*
* (c) 2010 by n@work GmbH and networkteam GmbH
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
 * $Id: $
 */

/**
 * Scheduler Task to update the TYPO3 version numbers from the SVN tags
 *
 * @author Felix Oertel <oertel@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_Typo3versionnumbersupdateTask extends tx_scheduler_Task {


	public function execute() {
		$success = false;
		$content = file_get_contents('https://svn.typo3.org/TYPO3v4/Core/tags/');

		if (empty($content)) {
			return false;
		}

		preg_match_all('/TYPO3_(4-[0-9]{1,2}-[0-9]{1,2}((alpha|beta|RC)[^\/]{0,2})?)\//', $content, $matches);

		if (!is_array($matches[1]) || count($matches[1]) == 0) {
			return false;
		}
		
		$max = array();
		foreach ($matches[1] as $key => $version) {
			$versionDigits = explode('-', $version, 3);	
			if ($max[$versionDigits[0] . '.' . $versionDigits[1]][2] < $versionDigits[2]) {
				$max[$versionDigits[0] . '.' . $versionDigits[1]] = $versionDigits;
			}
		}

		foreach ($max as $key => $value) {
			$max[$key] = implode('.', $value);
		}
		t3lib_div::makeInstance('t3lib_Registry')->set('tx_caretaker', 'TYPO3versions', $max);
		
		return true;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_typo3versionnumbersupdatetask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_typo3versionnumbersupdatetask.php']);
}

?>