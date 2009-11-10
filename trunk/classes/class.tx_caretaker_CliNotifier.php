<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_caretaker_CliNotifier implements tx_caretaker_NotifierInterface{
	
	/**
	 * Array of Messages  
	 * @var array
	 */
	private $recipients_messages   = array();
	
	/**
	 * From Address for Notification Mails
	 * @var string
	 */
	private $mail_from    = '';
	
	/**
	 * Subject for Notification Mails
	 * 
	 * @var unknown_type
	 */
	private $mail_subject = '';  
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->mail_from      = $confArray['notification.']['mail_from'];
		$this->mail_subject   = $confArray['notification.']['mail_subject'];
		$this->mail_link      = $confArray['notification.']['mail_link'];
	}

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_NotifierInterface#addNotification()
	 */
	public function addNotification ($recipientId, $state, $msg='', $description='', $id=false){
		if (!isset($this->recipients_messages[$recipientId]) ){
			$this->recipients_messages[$recipientId] = array();
		}
		array_unshift(
			$this->recipients_messages[$recipientId] ,
			array('msg'=>$msg, 'state'=>$state , 'description'=>$description , 'id' => $id)
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_NotifierInterface#sendNotifications()
	 */
	public function sendNotifications (){

		foreach ($this->recipients_messages as $recipientID => $items){
			if (count($items)>0){
				
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tt_address', 'uid='.(int)$recipientID.' AND deleted=0 AND hidden=0');
				$recipient = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				
				if ($recipient && $recipient['email'] ){
					
					$message     = '';
					$count       = 1;
					$num_error   = 0;
					$num_warning = 0;
					
					foreach ($items as $item){
						
						$message .= "*".($count ++).' '.$item['msg']."*\n";
						if ($item['description'])
							$message .= $item['description']."\n";
						$message .= "\n";
						
						if ($item['id'] && $this->mail_link)
							$message .= str_replace('###', $item['id'] , $this->mail_link). "\n";
							
						switch ($item['state']){
							case TX_CARETAKER_STATE_WARNING:
								$num_warning ++;
								break;
							case TX_CARETAKER_STATE_ERROR:
								$num_error ++;
								break;
						}
						
					}
					
					$subject = $this->mail_subject;
					if ($num_error > 0)
						$subject .= ' ' .$num_error.' Errors';
					if ($num_warning > 0)
						$subject .= ' ' .$num_warning.' Warnings';
					
					$this->sendMail($subject, $recipient['email'], $this->mail_from, $message  );
				}
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