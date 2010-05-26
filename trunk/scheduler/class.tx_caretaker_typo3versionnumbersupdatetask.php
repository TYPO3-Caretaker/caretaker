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
		
		$success = tx_caretaker_LatestTypo3VersionsHelper::updateLatestTypo3VersionRegistry();
		return $success;
		
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_typo3versionnumbersupdatetask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_typo3versionnumbersupdatetask.php']);
}

?>