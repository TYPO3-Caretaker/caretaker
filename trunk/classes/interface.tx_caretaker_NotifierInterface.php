<?php 
interface tx_caretaker_NotifierInterface {
	
	public function addNotification ($recipients, $node, $result);
	public function sendNotifications ();
	
}
?>
