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
class tx_caretaker_SimpleMailNotificationService implements tx_caretaker_NotificationServiceInterface  {

	/**
	 * The notification storage 
	 * @var array
	 */
	private $recipients_messages = array();

	/**
	 * The addresses for the recipients
	 * @var array 
	 */
	private $recipients_addresses = array();

	/**
	 * Notification Email from Address
	 * @var string
	 */
	private $mail_from;

	/**
	 * The mail subject Prefix
	 * @var string
	 */
	private $mail_subject;

	/**
	 * URL Pattern for links to display the frontend info for a test
	 * @var string URL with a ### marker which is replaced by the caretakerNodId
	 */
	private $mail_link;

	/**
	 * Testservice is enabled
	 * @var boolean
	 */
	private $enabled = TRUE;
	

	/**
	 * IDs of roles which are recieving mails
	 * @var array
	 */
	private $mail_roles = array();

	/**
	 * Constructor
	 * reads the service configuration
	 */
	public function __construct (){

		$contactRepository = tx_caretaker_ContactRepository::getInstance();
		
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);

		$this->mail_from      = $confArray['notifications.']['simple_mail.']['mail_from'];
		$this->mail_subject   = $confArray['notifications.']['simple_mail.']['mail_subject'];
		$this->mail_link      = $confArray['notifications.']['simple_mail.']['mail_link'];
		$this->mail_link      = $confArray['notifications.']['simple_mail.']['mail_link'];

		$this->mail_roles     = array();
		$role_ids = explode ( ',' , $confArray['notifications.']['simple_mail.']['role_ids'] );
		foreach ($role_ids as $role_id){
			$role = $contactRepository->getContactRoleById($role_id);
			if ( $role ) {
				$this->mail_roles[] = $role;
			}
		}
		
		$this->enabled        = (bool)$confArray['notifications.']['simple_mail.']['enabled'];
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
	 * Notify the service about a test status
	 *
	 * @param string $event Event Identifier
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification ($event, $node, $result = NULL, $lastResult = NULL ){


			// stop if event is not updatedTestResult of a TestNode
		if ( $event != 'updatedTestResult' || is_a( $node, 'tx_caretaker_TestNode' ) == false ){
			return;
		}

			// check that the state is not ok or undefined
		if ( $result->getState() <= tx_caretaker_Constants::state_ok ){
			return;
		}

			// Check that the result is not equal to the previous one
		if ( $lastResult && $result->getState() == $lastResult->getState() ){
			return;
		}

			// collect the recipients fron the node rootline
		$recipientIds = array();

		
		if ( count ($this->mail_roles) ){
			$contacts = array();
			foreach ( $this->mail_roles as $role){
			$contacts = array_merge($contacts, $node->getContacts( $role ) );
			}
		} else {
			$contacts = $node->getContacts();
		}

		foreach ($contacts as $contact ){
			$address = $contact->getAddress();
			if ( ! $this->recipients_addresses[ $address['uid'] ] ){
				$this->recipients_addresses[ $address['uid'] ] = $address;
			}
			$recipientIds[] = $address['uid'];
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
				case tx_caretaker_Constants::state_warning:
					$this->recipients_messages[$recipientId]['num_warning'] ++;
					break;
				case tx_caretaker_Constants::state_error:
					$this->recipients_messages[$recipientId]['num_error'] ++;
					break;
			}
			array_unshift( $this->recipients_messages[$recipientId]['messages'] ,
				'* '. ( $lastResult ? $lastResult->getLocallizedStateInfo().'->' :  '' ) . $result->getLocallizedStateInfo().' :: '.$node->getTitle().':'.$node->getInstance()->getTitle().' ['.$node->getCaretakerNodeId().'] *'.chr(10).chr(10).
				$result->getLocallizedInfotext() . chr(10).
				str_replace('###', $node->getCaretakerNodeId(),  $this->mail_link  ).chr(10)
			);
		}		
	}

	/**
	 * Send the aggregated Notifications
	 */
	public function sendNotifications(){
		
		foreach ($this->recipients_messages as $recipientID => $recipientInfo){
							
			$recipient = $this->recipients_addresses[ $recipientID ] ;

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
