<?php 
interface tx_caretaker_NotifierInterface {
	
	public function addNotification ($recipientId, $state, $msg='', $dsc='', $id=false);
	public function sendNotifications ();
	
}
?>
