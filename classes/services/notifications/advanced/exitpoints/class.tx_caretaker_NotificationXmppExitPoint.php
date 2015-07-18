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
 * $Id: class.tx_caretaker_NotificationMailExitPoint.php 43024 2011-02-03 11:58:50Z matrikz $
 */

/**
 *
 */
class tx_caretaker_NotificationXmppExitPoint extends tx_caretaker_NotificationBaseExitPoint {

	/**
	 * @var XMPPHP_XMPP
	 */
	protected $connection;

	/**
	 * @param array $config
	 * @return void
	 */
	public function init(array $config) {
		parent::init($config);
		$this->connectXmpp();
	}

	/**
	 * @return void
	 */
	protected function connectXmpp() {
		$this->connection = new XMPPHP_XMPP($this->config['host'], $this->config['port'], $this->config['user'], $this->config['password'], $this->config['resource'], $this->config['server']);
		// TODO configurable: $this->connection->useEncryption(FALSE);
		$this->connection->connect();
		$this->connection->processUntil('session_start');
	}

	/**
	 * @param array $notification
	 * @param array $overrideConfig
	 * @return void
	 */
	public function addNotification($notification, $overrideConfig) {
		$config = $this->getConfig($overrideConfig);
		$message = $this->getMessageForNotification($notification);
		/** @var tx_caretaker_AbstractNode $node */
		$node = $notification['node'];
		$contacts = $node->getContacts($config['roles']);
		/** @var tx_caretaker_Contact $contact */
		foreach ($contacts as $contact) {
			$xmppAddress = $contact->getAddressProperty('tx_caretaker_xmpp');
			if (!empty($xmppAddress)) {
				$this->connection->message($xmppAddress, $message);
			}
		}
	}

	/**
	 * @return void
	 */
	public function execute() {
		$this->connection->disconnect();
	}
}

