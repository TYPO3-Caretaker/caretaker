<?php 
interface tx_caretaker_NotifierInterface {
	
	public function addNotification ($recipientId, $state, $msg='', $dsc='');
	public function sendNotifications ();
	
}
?>
