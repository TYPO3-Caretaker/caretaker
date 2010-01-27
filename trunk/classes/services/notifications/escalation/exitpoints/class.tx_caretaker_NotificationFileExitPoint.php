<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class tx_caretaker_NotificationFileExitPoint extends tx_caretaker_NotificationBaseExitPoint {
	
	public function execute() {
		$fh = fopen($this->config['data']['sDEF']['lDEF']['filePath']['vDEF'], 'a');
		
		fclose($fh);
	}
}

?>
