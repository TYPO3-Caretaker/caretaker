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

class tx_caretaker_NotificationMailExitPoint extends tx_caretaker_NotificationBaseExitPoint {

	protected $notificationsByRecipient = array();

	/**
	 * @param array $notification
	 * @param array $overrideConfig
	 * @return void
	 */
	public function addNotification($notification, $overrideConfig) {
		$config = $this->getConfig($overrideConfig);

		/** @var tx_caretaker_AbstractNode $node */
		$node = $notification['node'];
		/** @var tx_caretaker_TestResult $result */
		$result = $notification['result'];

		$contacts = $node->getContacts($config['roles']);
		/** @var tx_caretaker_Contact $contact */
		foreach ($contacts as $contact) {
			$address = $contact->getAddress();
			if (!$config['aggregateByRecipient']) {
				$this->sendMail($address['email'], $this->compileMail(array($notification)));
			} else {
				$this->notificationsByRecipient[$address['email']][] = $notification;
			}
		}
	}

	/**
	 * @return void
	 */
	public function execute() {
		if ($this->config['aggregateByRecipient'] && !empty($this->notificationsByRecipient)) {
			foreach ($this->notificationsByRecipient as $recipient => $notifications) {
				$this->sendMail($recipient, $this->compileMail($notifications));
			}
		}
	}

	/**
	 * @param array $notifications
	 * @return array
	 */
	protected function compileMail($notifications) {
		$mail = array(
				'subject' => $this->config['emailSubject'], // TODO compile a proper subject
				'message' => '',
		);

		foreach ($notifications as $notification) {
			$mail['message'] .= $this->getMessageForNotification($notification);
		}
		return $mail;
	}

	/**
	 * @param string $recipient
	 * @param array $mailContent
	 * @return
	 */
	protected function sendMail($recipient, $mailContent) {
		$mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
		$mail->setFrom($this->config['emailSenderAddress'], $this->config['emailSenderName']);
		$mail->setTo($recipient);
		$mail->setSubject($mailContent['subject']);
		$mail->setBody($mailContent['message']);
		return $mail->send();
	}

	/**
	 * @param int $time
	 * @return string
	 */
	protected function humanReadableTime($time) {
		$periods = array("sec", "min", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");
		for ($j = 0; $time >= $lengths[$j]; $j++) {
			$time /= $lengths[$j];
		}
		$time = round($time);
		if ($time != 1) $periods[$j] .= "s";
		return $time . ' ' . $periods[$j];
	}
}
