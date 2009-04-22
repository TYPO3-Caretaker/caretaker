<?php
if (TYPO3_MODE=='BE')    {
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array('EXT:'.$_EXTKEY.'/classes/class.tx_caretaker_Cli.php','_CLI_caretaker');
}

t3lib_extMgm::addPItoST43($_EXTKEY,'pi_overview/class.tx_caretaker_pi_overview.php','_pi_overview','list_type',0);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi_singleview/class.tx_caretaker_pi_singleview.php','_pi_singleview','list_type',0);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi_graphreport/class.tx_caretaker_pi_graphreport.php','_pi_graphreport','list_type',0);

	// Register dummy testservice
t3lib_extMgm::addService(
	'caretaker',
	'caretaker_test_service',
	'tx_caretaker_dummy',
	array(
		'title' => 'Dummy Test Service',
		'description' => 'a very basic test implementation',
		'subtype' => 'tx_caretaker_dummy',
		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'classFile' => t3lib_extMgm::extPath('caretaker').'services/class.tx_caretaker_TestDummy.php',
		'className' => 'tx_caretaker_TestDummy',
	)
);

	// load Service Helper
include_once(t3lib_extMgm::extPath('caretaker').'classes/class.tx_caretaker_ServiceHelper.php');

	// register Tests
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretaker_ping',  'Ping' , 'Retrieves System Informations' );
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretaker_http',  'HTTP' , 'Call an URI and check the HTTP-Status' );


?> 