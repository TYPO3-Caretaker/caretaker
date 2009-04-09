<?php

require_once ('interface.tx_caretaker_NotifierInterface.php');

class tx_caretaker_EmailNotifier implements tx_caretaker_NotifierInterface{
	
	private $recipients = array();
	
	public function addNotification ($recipientIds, $status, $msg){
		foreach($recipientIds as $rId){
			if (!isset($this->recipients[$rId]) ){
				$this->recipients[$rId] = array();
			}
			$this->recipients[$rId][] = $msg;
		}
	}
	
	public function sendNotifications (){
		
		foreach ($this->recipients as $recipients => $messages){
			/*
			print_r( "send notifications to ".$recipients."\n");
			print_r( $messages );
			*/
		}
	}
	
}
?>