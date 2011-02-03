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
 * Baseclass for Notification-Services
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
interface tx_caretaker_NotificationServiceInterface {

	public function setId($id);

	/**
	 * Check weather the notificationService is enabled
	 * 
	 * @return boolean
	 */
	public function isEnabled();

    /**
	 * Notify the service about a test status
	 *
	 * @param string $event
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification ($event, $node, $result = NULL, $lastResult = NULL);

	/**
	 * Send the aggregated Notifications
	 */
	public function sendNotifications();
	
}
?>
