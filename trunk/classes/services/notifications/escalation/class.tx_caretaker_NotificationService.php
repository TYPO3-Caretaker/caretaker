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
 * The Testrunner Output Notification-Service
 *
 * @author Thomas Hempel <thomas@work.de>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_NotificationService implements tx_caretaker_NotificationServiceInterface  {
	/**
	 * Service is enabled or not
	 *
	 * @var boolean
	 */
	private $enabled = false;

	/**
	 * Constructor
	 * reads the service configuration
	 */
	public function __construct (){
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);

		$this->enabled = (bool)$confArray['notifications.']['escalating.']['enabled'];
	}

	/**
	 * Check weather the notificationService is enabled
	 *
	 * @return boolean
	 */
	public function isEnabled(){
		return $this->enabled;
	}

    /**
	 * This is called whenever the notfication service is called. We have to store all interesting
	 * results in an internal structure to use it later.
	 *
	 * @param string $event
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification ( $event, $node, $result = NULL, $lastResult = NULL ){
		
		// var_dump($node->getProperty('notification_strategy'));
		
	}

	/**
	 * Send the aggregated Notifications
	 *
	 * nothing happens here since all Informations are already sent to cli
	 */
	public function sendNotifications(){}
}
?>
