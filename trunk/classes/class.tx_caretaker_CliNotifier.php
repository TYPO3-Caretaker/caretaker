<?php

require_once ('interface.tx_caretaker_NotifierInterface.php');

class tx_caretaker_CliNotifier implements tx_caretaker_NotifierInterface{
	
	private $recipients_messages   = array();
	
	private $mail_from    = '';
	private $mail_subject = '';  
	
	public function __construct(){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->mail_from      = $confArray['notification.']['mail_from'];
		$this->mail_subject   = $confArray['notification.']['mail_subject'];
	}
		
	public function addNotification ($recipientId, $state, $msg='', $description=''){
		if (!isset($this->recipients_messages[$recipientId]) ){
			$this->recipients_messages[$recipientId] = array();
		}
		array_unshift(
			$this->recipients_messages[$recipientId] ,
			array('msg'=>$msg, 'state'=>$state , 'description'=>$description )
		);
	}
	
	public function sendNotifications (){

		foreach ($this->recipients_messages as $recipientID => $items){
			if (count($items)>0){
				
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tt_address', 'uid='.(int)$recipientID.' AND deleted=0 AND hidden=0');
				$recipient = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				
				if ($recipient && $recipient['email'] ){ 
					$msg   = '';	
					$cnt   = 1;
					$num_e = 0;
					$num_w = 0;
					
					foreach ($items as $item){
						
						$msg .= "*".($cnt ++).' '.$item['msg']."*\n";
						if ($item['description'])
							$msg .= $item['description']."\n";
						$msg .= "\n";
						
						switch ($item['state']){
							case 1:
								$num_w ++;
								break;
							case 2:
								$num_e ++;
								break;
						}
						
					}
					
					$subject = $this->mail_subject;
					if ($num_e > 0)
						$subject .= ' ' .$num_e.' Errors';
					if ($num_w > 0)
						$subject .= ' ' .$num_w.' Warnings';	
					
					$this->sendMail($subject, $recipient['email'], $this->mail_from, $msg  );				
				}
			}
		}
	}
	
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