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
 * A Basic Email Notification-Service
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_SimpleMailNotificationService extends tx_caretaker_NotificationServiceBase  {

	/**
	 * The notification storage 
	 * @var array
	 */
	private $recipients_messages = array();

	private $mail_from;

	private $mail_subject;

	private $mail_link;

	public function __construct (){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);

		$this->mail_from      = $confArray['notification.']['mail_from'];
		$this->mail_subject   = $confArray['notification.']['mail_subject'];
		$this->mail_link      = $confArray['notification.']['mail_link'];
		
	}

   	/**
	 * Notify the service about a test status
	 *
	 * @param tx_caretaker_TestNode $test
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification ($test, $result, $lastResult){

			// check that the state is not ok or undefined
		if ( $result->getState() <= TX_CARETAKER_STATE_OK ){
			return;
		}

			// Check that the result is not equal to the previous one
		if ( $lastResult && $result->getState() == $lastResult->getState() ){
			// return;
		}

			// collect the recipients fron the node rootline
		$recipientIds = array();
		$node = $test;
		while ($node) {
			$nodeNotificationIds = $node->getProperty('notifications');
			if ($nodeNotificationIds) {
				$recipientIds = array_merge ( $recipientIds, explode( ',', $nodeNotificationIds ) );
			}
			$node = $node->getParent();
		}
		$recipientIds = array_unique ($recipientIds);
		
			// store the notifications for the recipients
		foreach ($recipientIds as $recipientId){
			if (!isset($this->recipients_messages[$recipientId]) ){
				$this->recipients_messages[$recipientId] = array(
					'messages'=>array(),
					'num_warning'=>0,
					'num_error'=>0,
				);
			}
			switch ( $result->getState() ){
				case TX_CARETAKER_STATE_WARNING:
					$this->recipients_messages[$recipientId]['num_warning'] ++;
					break;
				case TX_CARETAKER_STATE_ERROR:
					$this->recipients_messages[$recipientId]['num_error'] ++;
					break;
			}
			array_unshift( $this->recipients_messages[$recipientId]['messages'] ,
				'* '.$result->getLocallizedStateInfo().' :: '.$test->getTitle().':'.$test->getInstance()->getTitle().' ['.$test->getCaretakerNodeId().'] *'.chr(10).chr(10).
				$result->getLocallizedInfotext().chr(10).
				str_replace('###', $test->getCaretakerNodeId(),  $this->mail_link  ).chr(10)
			);
		}		
	}

	/**
	 * Send the aggregated Notifications
	 */
	public function sendNotifications(){
				
		foreach ($this->recipients_messages as $recipientID => $recipientInfo){
				
				// read address
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tt_address', 'uid='.(int)$recipientID.' AND deleted=0 AND hidden=0');
			$recipient = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				// prepare mail
			if ($recipient && $recipient['email'] ){

				$message     = '';
				$count       = 1;
				$num_error   = 0;
				$num_warning = 0;

				$message = implode( chr(10).chr(10) , $recipientInfo['messages'] );

				$subject = $this->mail_subject;

				if ($recipientInfo['num_error'] > 0)
					$subject .= ' ' .$recipientInfo['num_error'].' Errors';
				if ($recipientInfo['num_warning'] > 0)
					$subject .= ' ' .$recipientInfo['num_warning'].' Warnings';

				$this->sendMail($subject, $recipient['email'], $this->mail_from, $message  );
			}
		}
	}


	/**
	 * Send a Single Notification Mail
	 *
	 * @param $subject
	 * @param $recipient
	 * @param $from
	 * @param $message
	 * @return unknown_type
	 */
	private function sendMail($subject, $recipient, $from, $message ){

		$mailbody_plain = '';
		$mailbody_plain .= $message."\n\n" ;

		$mailbody_html = '';
		$mailbody_html .= '<p>'.nl2br($message).'</p>' ;

		$boundary = md5(uniqid(time()));

		$headers  = 'From: '. $from . "\n";
		$headers .= 'To: ' . $recipient. "\n";
		$headers .= 'Return-Path: ' .$from. "\n";
		$headers .= 'MIME-Version: 1.0' ."\n";
		$headers .= 'Content-Type: text/plain; charset=utf-8' ."\n";
		$headers .= 'Content-Transfer-Encoding: base64'. "\n\n";
		$headers .= base64_encode($mailbody_plain). "\n";

		$subject = "=?UTF-8?B?".base64_encode($subject)."?=\n";

		$res = mail('', $subject, '', $headers);

		return ($mailbody_plain."res:".$res);
	}
	
}
?>
