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
 * Sceduler Task to update the status of a given caretakerNodeId.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_TestrunnerTask extends tx_scheduler_Task {

	private $node_id;


	public function setNodeId($id){
		$this->node_id = $id;
	}

	public function getNodeId(){
		return $this->node_id;
	}

	public function execute() {

		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$node = $node_repository->id2node($this->node_id);

		if (!$node)return false;

		$notifier = new tx_caretaker_CliNotifier();
		$node->setNotifier($notifier);

		$node->updateTestResult();

			// send aggregated notifications
		$notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
		foreach ( $notificationServices as $notificationService ){
			$notificationService->sendNotifications();
		}


		$success = true;
		
		return $success;
	}

	public function getAdditionalInformation() {
		// return $GLOBALS['LANG']->sL('LLL:EXT:scheduler/mod1/locallang.xml:label.email') . ': ' . $this->email;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask.php']);
}

?>