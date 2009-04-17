<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Tobias Liebig		<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek 	<hlubek@networkteam.com>
 * @Author	Patrick Kollodzik	<patrick@work.de>
 *  
 * $$Id: ext_tables.php 46 2008-06-19 16:09:17Z martin $$
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


// register Records

$TCA['tx_caretaker_instancegroup'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'treeParentField' => 'parent_group',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs'=> 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/instancegroup.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	)
);

$TCA['tx_caretaker_instance'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs'=> 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/instance.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	)
);



$TCA['tx_caretaker_testgroup'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'treeParentField' => 'parent_group',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs'=> 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/group.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	)
);

$TCA['tx_caretaker_test'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'dividers2tabs'=> 1,
	    'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
    	),
		'type' => 'testservice',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/test.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
);

	// register FE-Plugins
	
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi_overview']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi_overview']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi_overview', 'FILE:EXT:'.$_EXTKEY.'/pi_overview/flexform_ds.xml');
t3lib_extMgm::addPlugin(Array('LLL:EXT:caretaker/locallang_db.xml:tt_content.list_type_pi_overview', $_EXTKEY.'_pi_overview'),'list_type');
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_caretaker_pi_overview_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi_overview/class.tx_caretaker_pi_overview_wizicon.php';

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi_singleview']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:caretaker/locallang_db.xml:tt_content.list_type_pi_singleview', $_EXTKEY.'_pi_singleview'),'list_type');
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_caretaker_pi_singleview_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi_singleview/class.tx_caretaker_pi_singleview_wizicon.php';


	// register Extension TS templates
t3lib_extMgm::addStaticFile($_EXTKEY,'res/typoscript','Caretaker Frontend Template');

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
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretaker_snmp',  'SNMP' , 'Request an SNMP Status and check Result' );

	// Register Backend Modules
if (TYPO3_MODE=="BE")	{

	t3lib_extMgm::addModule("txcaretakerNav","","",t3lib_extMgm::extPath($_EXTKEY)."mod_nav/");
	t3lib_extMgm::addModule("txcaretakerNav","txcaretakerOverview","",t3lib_extMgm::extPath($_EXTKEY)."mod_overview/");
	
	if (isset($TBE_MODULES['file']) ){
		$caretaker_modconf = $TBE_MODULES['txcaretakerNav'];
		unset($TBE_MODULES['txcaretakerNav']);
	}
		// move module after 'file'
	$temp_TBE_MODULES = array();
	foreach ($TBE_MODULES as $key=>$value){
		if ($key == 'file'){ 
			$temp_TBE_MODULES[$key]=$value;
			$temp_TBE_MODULES['txcaretakerNav']=$caretaker_modconf;
		} else {
			$temp_TBE_MODULES[$key]=$value;
		}
	}
	$TBE_MODULES = $temp_TBE_MODULES;
	
	
}


?>