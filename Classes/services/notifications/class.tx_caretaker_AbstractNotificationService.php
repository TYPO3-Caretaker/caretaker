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
class tx_caretaker_AbstractNotificationService implements tx_caretaker_NotificationServiceInterface {

	/**
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * @var array
	 */
	protected $notificationQueue = array();

	/**
	 *
	 * @var array
	 */
	protected $extConfig = array();

	public function __construct($serviceKey = '') {
		$this->extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->setId($serviceKey);
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = str_replace(' ', '', strtolower(trim($id)));
	}

	/**
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $notification
	 * @param string $key
	 */
	public function addNotificationToQueue($notification, $key = null) {
		$this->notificationQueue[$key] = $notification;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getNotificationFromQueue($key = null) {
		if ($key != null && isset($this->notificationQueue[$key])) {
			return $this->notificationQueue[$key];
		} else {
			return array_pop($this->notificationQueue);
		}
	}

	/**
	 * @return array
	 */
	public function getNotificationQueue() {
		return $this->notificationQueue;
	}

	/**
	 * Returns a value from the extension config array inside the path of the
	 * notification id. e.g. notifications.id.enabled for key "enabled".
	 * Returns NULL if no data was found.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getConfigValue($key) {
		if (isset($this->extConfig['notifications.'][$this->getId() . '.'][$key])) {
			return $this->extConfig['notifications.'][$this->getId() . '.'][$key];
		} else {
			return NULL;
		}
	}

	/**
	 * This returns whether the service is enabled or not. By default this returns the value of
	 * the extension config for the key notifications.[id].enabled.
	 * This can be overwritten by the specific implementation of the notification service.
	 *
	 * @return boolean
	 */
	public function isEnabled() {
		$enabled = (bool)$this->getConfigValue('enabled');
		$beUsername = $GLOBALS["BE_USER"]->user['username'];
		return ($enabled === TRUE && TYPO3_MODE == 'BE' && (defined('TYPO3_cliMode') && ($beUsername == '_cli_caretaker' || $beUsername == '_cli_scheduler') || $GLOBALS['MCONF']['name'] == 'tools_txschedulerM1'));
	}

	/**
	 * @param string $event
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaker_TestResult $lastResult
	 */
	public function addNotification($event, $node, $result = NULL, $lastResult = NULL) {
	}

	public function sendNotifications() {
	}

}
