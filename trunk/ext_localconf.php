<?php
if (TYPO3_MODE=='BE')    {
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array('EXT:'.$_EXTKEY.'/classes/class.tx_caretaker_Cli.php','_CLI_caretaker');
}

t3lib_extMgm::addPItoST43($_EXTKEY,'pi_overview/class.tx_caretaker_pi_overview.php','_pi_overview','list_type',0);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi_singleview/class.tx_caretaker_pi_singleview.php','_pi_singleview','list_type',0);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi_graphreport/class.tx_caretaker_pi_graphreport.php','_pi_graphreport','list_type',0);

	// load Service Helper
include_once(t3lib_extMgm::extPath('caretaker').'classes/class.tx_caretaker_ServiceHelper.php');

	// register Tests
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretaker_ping',  'Ping' , 'Retrieves System Informations' );
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretaker_http',  'HTTP' , 'Call an URI and check the HTTP-Status' );

// Add eID script for caretaker tree loader
$TYPO3_CONF_VARS['BE']['AJAX']['tx_caretaker::treeloader']  = 'EXT:caretaker/mod_nav/class.tx_caretaker_treeloader.php:tx_caretaker_treeloader->ajaxLoadTree';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_caretaker::nodeinfo']    = 'EXT:caretaker/classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeInfo';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_caretaker::noderefresh'] = 'EXT:caretaker/classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxRefreshNode';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_caretaker::nodegraph']   = 'EXT:caretaker/classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeGraph';
$TYPO3_CONF_VARS['BE']['AJAX']['tx_caretaker::nodelog']     = 'EXT:caretaker/classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeLog';

	// Register scheduler tasks for caretaker
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_caretaker_TestrunnerTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:'.$_EXTKEY.'/locallang.xml:testrunnerTask.name',
	'description'      => 'LLL:EXT:'.$_EXTKEY.'/locallang.xml:testrunnerTask.description',
	'additionalFields' => 'tx_caretaker_TestrunnerTask_AdditionalFieldProvider'
);

?> 