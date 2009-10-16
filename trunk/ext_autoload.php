<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php 1190 2009-09-03 18:01:00Z francois $
 */
return array(
	'tx_caretaker_testrunnertask'							=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_testrunnertask.php'),
	'tx_caretaker_testrunnertask_additionalfieldprovider'	=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php'),
	'tx_caretaker_terupdatetask'							=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_terupdatetask.php'),
	'tx_caretaker_terupdatetask_additionalfieldprovider'	=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_terupdatetask_additionalfieldprovider.php'),

);
?>
